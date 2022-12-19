<?php
use TheSeer\Tokenizer\Exception;
use function PHPUnit\Framework\throwException;
use Pheanstalk\Pheanstalk;
use GO\Scheduler;

defined('BASEPATH') or exit('No direct script access allowed');
require 'C:\xampp\htdocs\document-sharing\vendor\autoload.php';
// require 'vendor/autoload.php';
// use MongoDB\Client;
use PhpParser\Node\Stmt\TryCatch;

// require 'Predis/Autoloader.php';
use Predis\Client as RedisClient;

$redisClient = new RedisClient([
    'host' => 'localhost',
    'port' => 6379
]);

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;

define('BEANSTALKD_USER_VERIFY_REGISTER_TUBE', 'BEANSTALKD_USER_VERIFY_REGISTER_TUBE');
define('BEANSTALKD_USER_RESEND_VERIFY_TUBE', 'BEANSTALKD_USER_RESEND_VERIFY_TUBE');
define('BEANSTALKD_SEND_DOCUMENT_LINK_TUBE', 'BEANSTALKD_SEND_DOCUMENT_LINK_TUBE');


class UserController extends CI_Controller
{
    // public $redisClient = GLOBAL['redisClient'];
    function __construct()
    {
        parent::__construct();
        $this->load->model('usermodel', 'userModel');
        // load form rule for registering user form => form_rule_register_user
        $this->load->helper('form_rules');

        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');

        $this->redisClient = new RedisClient([
            'host' => 'localhost',
            'port' => 6379
        ]);

        $this->pheanstalk = new Pheanstalk('127.0.0.1');
    }
    public function index()
    {
        $this->load->view('users/khoaView');
        $this->load->library('pagination');

        $config['base_url'] = '/document-sharing/user';
        $config['total_rows'] = 200;
        $config['per_page'] = 20;

        $this->pagination->initialize($config);

        echo $this->pagination->create_links();
    }

    public function filter()
    {
        $arrFields = [
            'send_time' => 'share',
            'email' => 'email',
            'name' => 'name',
            'openned_mail_time' => 'openned_mail',
            'downloaded_time' => 'downloaded'
        ];
        $filterDataTemp = $this->input->get('filter[data]');
        $filterData = [];

        $skip = $this->input->get('skip') ? $this->input->get('skip') : 0;
        $limit = $this->input->get('take') ? $this->input->get('take') : 3;

        // if (!is_array($filterDataTemp) || empty($filterDataTemp)) {
        //     $response = $this->userModel->getLimit($skip, $limit);
        //     $this->output
        //         ->set_status_header(200)
        //         ->set_content_type('application/json', 'utf-8')
        //         ->set_output(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        //     return;
        // }

        foreach ($arrFields as $key => $value) {
            if (isset($filterDataTemp[$key]) && $filterDataTemp[$key] !== '') {
                $filterData[$value] = $filterDataTemp[$key];
            }
        }

        if (!is_array($filterData) || empty($filterData)) {
            $response = $this->userModel->getLimit($skip, $limit);
            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            return;
        }

        $userFilterResult = $this->userModel->filter($filterData, ['skip' => $skip, 'limit' => $limit]);

        $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($userFilterResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        return;
    }

    public function manage()
    {
        $dataView = [];

        $dataView['listUser'] = $this->userModel->getLimit();
        $this->load->view('commons/headHtml');
        $this->load->view('managements/manageView', $dataView);
        $this->load->view('commons/bodyHtml');
    }
    public function registerView()
    {
        $this->load->library('form_validation');
        $this->load->view('commons/headHtml');
        $this->load->view('users/registerView');
        $this->load->view('commons/bodyHtml');
    }

    // move to myservices
    // public function dequeueRegister()
    // {
    //     $continue = $this->input->get('continue');
    //     $timeout = $this->input->get('timeout');
    //     $isContinue = ($continue === 'true');
    //     $queueTimeout = ($timeout && is_int($timeout) && $timeout >= 0) ? $timeout : 15;
    //     var_dump($continue);
    //     var_dump($isContinue);
    //     //die;
    //     while ($isContinue) {
    //         while ($job = $this->pheanstalk->reserveFromTube(BEANSTALKD_USER_VERIFY_REGISTER_TUBE, $queueTimeout)) {
    //             try {
    //                 // if (!$job) {
    //                 //     echo 'No job existed';
    //                 //     return;
    //                 //     // throw new Exception("Dequeue user registering data hasn't data", 500);
    //                 // }

    //                 $sendMailRegisterData = json_decode($job->getData(), true);
    //                 // echo $job->getData();
    //                 // echo '<br><pre>';
    //                 // print_r($sendMailRegisterData);
    //                 $this->load->helper('my_mail');
    //                 //send_mail($sendMailRegisterData['email'], $sendMailRegisterData['message']);
    //                 echo $job->getData();
    //                 $this->pheanstalk->delete($job);
    //             } catch (Exception $e) {
    //                 $jobData = $job->getData();
    //                 $this->pheanstalk->delete($job);
    //                 $this->pheanstalk->putInTube(BEANSTALKD_USER_VERIFY_REGISTER_TUBE, $jobData);
    //                 exit();
    //             }

    //             if (!$isContinue)
    //                 exit();
    //         }
    //     }

    //     echo 'oki con de';
    // }
    public function enqueueSendMail(string $email, $message, $tube)
    {
        try {
            if (!$email || !$message || !$tube)
                throw new Exception('Argument of enqueueSendMail are not null');
            $message = [
                'email' => $email,
                'message' => $message,
                //'Callback' => 'UpdateSendStatus',
            ];

            $this->pheanstalk->putInTube($tube, json_encode($message));
        } catch (Exception $e) {
            echo "Lỗi máy chủ, gửi mail thất bại. Message: $e->getMessage()";
        }
    }

    public function enqueueSendDownloadLink($data, string $tube) {
        try {
            if (!$data || !$tube)
                throw new Exception('Argument of enqueueSendMail are not empty');

            $this->pheanstalk->putInTube($tube, json_encode($data));
        } catch (Exception $e) {
            echo "Lỗi máy chủ, gửi mail thất bại. Message: $e->getMessage()";
        }
    }
    public function register()
    {
        try {
            $inputFields = array('email', 'name', 'age', 'gender', 'occupation', 'address');
            $dataView = array();
            $userRegisterInfo = [];

            $this->form_validation->set_rules(form_rule_register_user());

            // show errors
            if ($this->form_validation->run() == FALSE) {
                $dataView['resultForModal'] = 'Nhập dữ liệu đăng ký không hợp lệ';
                foreach ($inputFields as $field) {
                    if (form_error($field, '<div class="alert alert-danger">', '</div>')) {
                        $dataView['resultForModal'] .= form_error('gender', '<div class="alert alert-danger">', '</div>');
                    }
                }
                
                $this->load->view('commons/headHtml');
                $this->load->view('users/registerView', $dataView);
                $this->load->view('commons/bodyHtml');
                return;
            }

            // check existed email
            $checkExistedEmail = $this->userModel->findOneByEmail($this->input->post('email'));

            if (!empty($checkExistedEmail)) {
                $dataView['resultForModal'] = 'Email đăng ký đã tồn tại';
                $this->load->view('commons/headHtml');
                $this->load->view('users/registerView', $dataView);
                $this->load->view('commons/bodyHtml');
                return;
            }

            // generate otp
            $sixDigitRandomNumber = random_int(100000, 999999);
            $message = "<div style='display: block; text-align: center;'><p>Đây là mã otp đăng ký tài liệu của quý khách. Vui lòng không chia sẻ bất kỳ ai!</p>
            <h2>$sixDigitRandomNumber</h2></div>";

            // verify email by sending code
            // enqueue to beantalkd
            $this->enqueueSendMail($this->input->post('email'), $message, BEANSTALKD_USER_VERIFY_REGISTER_TUBE);

            // if (!$result) {
            //     $dataView['resultForModal'] = 'Gửi mã otp thất bại. Quý khách vui lòng đăng ký lại';
            //     $this->load->view('commons/headHtml');
            //     $this->load->view('users/registerView', $dataView);
            //     $this->load->view('commons/bodyHtml');
            //     return;
            // }

            // cache otp by redis
            $otpRedisKey = "email_otp:" . (string) $this->input->post('email');
            $this->redisClient->set($otpRedisKey, $sixDigitRandomNumber, 'EX', 180); // 3 minutes
            $otpRedis = $this->redisClient->get($otpRedisKey);

            if ($otpRedis != $sixDigitRandomNumber) {
                echo 'Lỗi server. Không cache được otp';
                return;
            }

            // hash, encrypt to secure and verify data
            foreach ($inputFields as $field) {
                $userRegisterInfo[$field] = $this->input->post($field);
            }
            // jwt
            $jwt = JWT::encode($userRegisterInfo, $this->config->item('jwt_key'), 'HS256');

            // encrypt
            $this->load->library('encryption');
            $userEncryptRegisterInfo = $this->encryption->encrypt($jwt);

            // viewing
            $dataView['userEncryptRegisterInfo'] = $userEncryptRegisterInfo;
            $this->load->view('commons/headHtml');
            $this->load->view('users/verifyView', $dataView);
            $this->load->view('commons/bodyHtml');
        } catch (Exception $e) {
            echo 'UserController Error: ', $e->getMessage(), "\n";
        }
    }

    public function verifyView()
    {
        $this->load->view('commons/headHtml');
        $this->load->view('users/verifyView');
        $this->load->view('commons/bodyHtml');
    }

    public function verifyAndRegister()
    {
        try {
            $dataView = array();
            $inputFields = ['code', 'data'];

            $this->form_validation->set_rules(form_rule_verify_user());

            // show errors
            if ($this->form_validation->run() == FALSE) {
                $dataView['resultForModal'] = 'Dữ liệu xác minh đăng ký không đúng. Vui lòng đăng ký lại'
                    . form_error('code') . form_error('data');
                $dataView['userEncryptRegisterInfo'] = $this->input->post('data');
                $this->load->view('commons/headHtml');
                $this->load->view('users/verifyView', $dataView);
                $this->load->view('commons/bodyHtml');
                return;
            }

            /**
             * decrypt and verify data
             * if fail to verify, it throw an error and catch them at catch
             */
            $this->load->library('encryption');
            $userDecryptRegisterInfo = $this->encryption->decrypt($this->input->post('data'));
            $userJwtRegisterInfo = (array) JWT::decode($userDecryptRegisterInfo, new Key($this->config->item('jwt_key'), 'HS256'));

            /**
             * Check otp
             */
            $otpRedisKey = "email_otp:" . (string) $userJwtRegisterInfo['email'];
            $otpRedisValue = $this->redisClient->get($otpRedisKey);
            if (empty($otpRedisValue)) {
                // resend otp code
                $dataView['resultForModal'] = 'Mã otp đã hết hạn. Vui lòng chọn gửi lại mã phía dưới';
                $dataView['userEncryptRegisterInfo'] = $this->input->post('data');
                $this->load->view('commons/headHtml');
                $this->load->view('users/verifyView', $dataView);
                $this->load->view('commons/bodyHtml');
                return;
            }

            if ($this->input->post('code') != $otpRedisValue) {
                $dataView['resultForModal'] = 'Mã otp không đúng';
                $dataView['userEncryptRegisterInfo'] = $this->input->post('data');
                $this->load->view('commons/headHtml');
                $this->load->view('users/verifyView', $dataView);
                $this->load->view('commons/bodyHtml');
                return;
            }

            // insert
            $result = $this->userModel->insertUser($userJwtRegisterInfo); // return document _id

            // check insert
            $checkInsertUserResult = $this->userModel->findOne($result);
            if (empty($checkInsertUserResult)) {
                $dataView['resultForModal'] = 'Đăng ký người dùng thất bại';
                $dataView['userEncryptRegisterInfo'] = $this->input->post('data');
                $this->load->view('commons/headHtml');
                $this->load->view('users/registerView', $dataView);
                $this->load->view('commons/bodyHtml');
                return;
            } else {
                if ($checkInsertUserResult['email'] !== $userJwtRegisterInfo['email']) {
                    $dataView['resultForModal'] = 'Lưu thông tin người đăng ký có sai sót';
                    $dataView['userEncryptRegisterInfo'] = $this->input->post('data');
                    $this->load->view('commons/headHtml');
                    $this->load->view('users/registerView', $dataView);
                    $this->load->view('commons/bodyHtml');
                    return;
                }
            }

            // // create link download
            // $this->load->helper('security');
            // $downloadUrl = do_hash($this->config->item('my_salt') . $userJwtRegisterInfo['email'], 'md5');
            // $fields = [
            //     'share' => [
            //         'download_url' => $downloadUrl,
            //         'openned_mail' => false,
            //         'downloaded' => 0,
            //         'send_time' => time()
            //     ]
            // ];

            // // update download link to db
            // $this->userModel->updateFieldsByEmail($userJwtRegisterInfo['email'], $fields);

            // // check share
            // $checkUpdateFields = $this->userModel->findOneByEmail($userJwtRegisterInfo['email']);

            // if (empty($checkUpdateFields)) {
            //     $dataView['resultForModal'] = 'Lưu liên kết đăng ký thất bại. Quý khách vui lòng thực hiện lại!';
            //     $dataView['userEncryptRegisterInfo'] = $this->input->post('data');
            //     $this->load->view('commons/headHtml');
            //     $this->load->view('users/registerView', $dataView);
            //     $this->load->view('commons/bodyHtml');
            //     return;
            // }

            // if ($checkUpdateFields['share']['download_url'] != $downloadUrl) {
            //     $dataView['resultForModal'] = 'Lưu liên kết đăng ký sai sót. Quý khách vui lòng thực hiện lại!';
            //     $dataView['userEncryptRegisterInfo'] = $this->input->post('data');
            //     $this->load->view('commons/headHtml');
            //     $this->load->view('users/registerView', $dataView);
            //     $this->load->view('commons/bodyHtml');
            //     return;
            // }

            // send download link, link check what user openned the email   
            $mailPayload = new stdClass();    
            $downloadUrlId = hash('md5', $this->config->item('my_salt') . $userJwtRegisterInfo['email']);
            $mailPayload->downloadUrlId = $downloadUrlId;
            $mailPayload->downloadLink = 'localhost/document-sharing/download/document/' . $downloadUrlId;
            $mailPayload->checkOpenMailLink = 'localhost/document-sharing/image/' . $downloadUrlId . '/logo.png';

            $mailPayload->email = $userJwtRegisterInfo['email'];
            $mailPayload->message =
                "<div style='display: block; text-align: center;'>
                    <p>Đây là mã otp đăng ký tài liệu của quý khách. Vui lòng không chia sẻ bất kỳ ai!</p>
                    <a href='$mailPayload->downloadLink' style='color:red'>Tải tài liệu tại đây</a>
                    <a href='$mailPayload->checkOpenMailLink' >check open</a>
                    <img <!--style='display: none;-->' src='$mailPayload->checkOpenMailLink'>
            </div>
            ";
            $mailPayload->Callback = 'UpdateSendStatus';

            $this->enqueueSendDownloadLink($mailPayload, BEANSTALKD_SEND_DOCUMENT_LINK_TUBE);
            
            //$this->load->helper('my_mail');
            //$resultSendDocumentLink = send_mail((string) $userJwtRegisterInfo['email'], $message);

            // if (!$resultSendDocumentLink) {
            //     $dataView['resultForModal'] = 'Gửi liên kết tải tài liệu thất bại';
            //     $dataView['userEncryptRegisterInfo'] = $this->input->post('data');
            //     $this->load->view('commons/headHtml');
            //     $this->load->view('users/registerView', $dataView);
            //     $this->load->view('commons/bodyHtml');
            //     return;
            // }

            $dataView['resultForModal'] = 'Đăng ký nhận tài liệu thành công. Hãy mở hộp thư email của bạn để tải tài liệu.
                 Lưu ý, hệ thống đang xử lý, có thể mất vài phút để hệ thống gửi mail';
            $this->load->view('commons/headHtml');
            $this->load->view('users/registerView', $dataView);
            $this->load->view('commons/bodyHtml');
        } catch (Exception $e) {
            if ($e instanceof UnexpectedValueException) {
                echo 'Bạn đã thay đổi thông tin đoạn mã';
                return;
            }

            echo 'UserController Error: ', $e->getMessage(), "\n";
        }
    }

    public function resendOtp()
    {
        try {
            $dataView = array();
            $inputFields = ['data'];

            $this->form_validation->set_rules(form_rule_resendotp());

            // show form validation errors
            if ($this->form_validation->run() == FALSE) {
                $response = array(
                    'message' => 'Dữ liệu đã bị thay đổi quý khách vui lòng đăng ký lại. Lỗi: ' . form_error('data')
                );
                $this->output
                    ->set_status_header(400)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

                return;
            }

            /**
             * decrypt and verify data
             * if fail to verify, it throw an error and catch them at catch
             */
            $this->load->library('encryption');
            $userDecryptRegisterInfo = $this->encryption->decrypt($this->input->post('data'));
            $userJwtRegisterInfo = (array) JWT::decode($userDecryptRegisterInfo, new Key($this->config->item('jwt_key'), 'HS256'));

            // generate otp
            $sixDigitRandomNumber = random_int(100000, 999999);
            $message = "<div style='display: block; text-align: center;'><p>Đây là mã otp đăng ký tài liệu của quý khách. Vui lòng không chia sẻ bất kỳ ai!</p>
                <h2>$sixDigitRandomNumber</h2></div>";

            // resend otp code by email
            // $message = "<div style='display: block; text-align: center;'>
            //         <p>Đây là mã otp đăng ký tài liệu của quý khách. Vui lòng không chia sẻ bất kỳ ai!</p>
            //         <h2>$sixDigitRandomNumber</h2>
            //     </div>
            // ";
            // $this->load->helper('my_mail');
            // $result = send_mail((string) $userJwtRegisterInfo['email'], $message);

            // if (!$result) {
            //     $response = array(
            //         'message' => 'Gửi mã otp thất bại. Quý khách vui lòng thực hiện gửi lại'
            //     );
            //     $this->output
            //         ->set_status_header(500)
            //         ->set_content_type('application/json', 'utf-8')
            //         ->set_output(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            //     return;
            // }
            $this->enqueueSendMail((string)$userJwtRegisterInfo['email'], $message, BEANSTALKD_USER_RESEND_VERIFY_TUBE);
                
            // cache otp by redis
            $otpRedisKey = "email_otp:" . (string) $userJwtRegisterInfo['email'];
            $this->redisClient->set($otpRedisKey, $sixDigitRandomNumber, 'EX', 180); // 3 minutes
            $otpRedis = $this->redisClient->get($otpRedisKey);

            if ($otpRedis != $sixDigitRandomNumber) {
                $response = array(
                    'message' => 'Lỗi máy chủ. Ghi bộ nhớ cache không thành công'
                );
                $this->output
                    ->set_status_header(400)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                return;
            }

            $response = array(
                'message' => 'Đã gửi mã otp. Quý khách vui lòng kiểm tra hộp thư.'

            );
            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        } catch (Exception $e) {
            echo $e;
            if ($e instanceof UnexpectedValueException) {
                $response = array(
                    'message' => 'Dữ liệu đã bị thay đổi quý khách vui lòng đăng ký lại. Error: ' . $e->getMessage()
                );
                $this->output
                    ->set_status_header(400)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                return;
            }

            $response = array(
                'message' => 'Lỗi máy chủ. Error: ' . $e->getMessage()
            );
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            return;
        }
    }

    public function downloadFile($idDownloadUrl)
    {
        // date_default_timezone_set("Asia/Ho_Chi_Minh");
        // echo new MongoDB\BSON\UTCDateTime((new DateTime())->getTimestamp());
        // echo '<br>';
        // echo date("Y-m-d H:i:s", time());
        // echo '<br>';
        // print gmdate("Y-m-d\TH:i:s\Z");
        // die;
        $userDocumentResult = $this->userModel->findOneByDownloadUrl($idDownloadUrl);
        if (empty($userDocumentResult)) {
            echo 'Liên kết không tồn tại. Vui lòng đăng ký lại!';
            return;
        }

        if ($userDocumentResult['share']['downloaded'] > 1) {
            echo 'Bạn đã quá số lần cho phép tải tài liệu';
            return;
        }

        $downloadAmount = $userDocumentResult['share']['downloaded'] + 1;

        $fields = [
            'share.downloaded' => $downloadAmount,
        ];

        if ($userDocumentResult['share']['downloaded'] === 0) {

            $fields['share.downloaded_time'] = time(); //new MongoDB\BSON\UTCDateTime((new DateTime())->getTimestamp());
        }

        // update db
        $this->userModel->updateFieldsByIdDownloadUrl($idDownloadUrl, $fields);

        // check update
        $checkUpdateOpennedMail = $this->userModel->findOneByDownloadUrl($idDownloadUrl);
        print_r($checkUpdateOpennedMail);
        if (empty($checkUpdateOpennedMail))
            throw new Exception('UserController Error: Fail to update user downloading amount');

        if ($checkUpdateOpennedMail['share']['downloaded'] !== $downloadAmount)
            throw new Exception('UserController Error: Wrong to update user downloading amount value' . (string) $downloadAmount);

        // send file
        $this->load->helper('file');
        $this->load->helper('download');
        echo dirname(__FILE__);
        force_download(dirname(__FILE__) . '/../../data/test.txt', NULL);
    }

    public function checkOpennedEmail($idDownloadUrl)
    {
        $userDocumentResult = $this->userModel->findOneByDownloadUrl($idDownloadUrl);
        if (empty($userDocumentResult)) {
            throw new Exception('UserController Error: Tracking user openned email link not found');
        }

        if ($userDocumentResult['share']['openned_mail'] === true) {
            echo 'đã xem mail';
            return;
        }

        $fields = [
            'share.openned_mail' => true,
            'share.openned_mail_time' => time()
        ];

        // update db
        $this->userModel->updateFieldsByIdDownloadUrl($idDownloadUrl, $fields);

        // check update
        $checkUpdateOpennedMail = $this->userModel->findOneByDownloadUrl($idDownloadUrl);
        if (empty($checkUpdateOpennedMail))
            throw new Exception('UserController Error: Fail to update user opening email');

        if ($checkUpdateOpennedMail['share']['openned_mail'] !== true)
            throw new Exception('UserController Error: Wrong to update user opening email value to true');

    }

    public function findOneById(string $id)
    {
        try {
            if (empty($id)) {
                echo '_id không được rỗng';
                return;
            }

            $result = $this->userModel->findOne($id);
            print_r($result);
        } catch (Exception $e) {
            echo 'UserController Error: ', $e->getMessage(), "\n";
        }
    }

    public function demoDownloadFile()
    {
        $this->load->helper('file');
        $this->load->helper('download');
        //$data = file_get_contents('/data/test.txt');
        //$name = 'test.txt';

        // force_download($name, $data);
        echo dirname(__FILE__);
        force_download(dirname(__FILE__) . '/../../data/test.txt', NULL);
    }
    public function demoSendMail()
    {
        $this->load->helper('my_mail');
        $result = send_mail('khoa.pham@southtelecom.vn', 'abc', 'abc');
        echo gettype(($result)); //boolean
        echo $result;
        print_r($result);
    }
    public function demoJwt()
    {
        $payload = [
            'iss' => 'http://example.org',
            'aud' => 'http://example.com',
        ];

        $jwt = JWT::encode($payload, $this->config->item('jwt_key'), 'HS256');
        echo $jwt;
        echo '
        
        ';
        $decoded = "";
        try {
            $decoded = JWT::decode($jwt . " f", new Key($this->config->item('jwt_key'), 'HS256'));
        } catch (Exception $e) {
            echo "helo";
            if ($e instanceof UnexpectedValueException) {
                echo "Decoded Jwt Error" . $e->getMessage();
                return;
            }

            echo "Internal server error";
            return;
        }

        print_r($decoded);
    }

    public function demoEncrypt()
    {
        $this->load->library('encryption');
        $plain_text = 'This is a plain-text message!';
        $ciphertext = $this->encryption->encrypt($plain_text);
        echo $ciphertext;
        $ciphertext = "fawjopjfwapojfi";
        // Outputs: This is a plain-text message!
        echo $this->encryption->decrypt($ciphertext);
    }

    public function getAll()
    {
        $this->load->model('userModel');
        $result = $this->userModel->getAllUser();
        print_r($result);
    }

    public function demoUserController()
    {
        // $client = new Client("mongodb://localhost:27017");
        // $userCollection = $client->training->user;

        // $result = $userCollection->insertOne(['name' => 'Hinterland']);
        // $this->load->view('users/khoaView', $result);
    }

    public function demoUserModelController()
    {
        $this->load->model('usermodel', 'userModel');
        print_r($this->userModel->demoUserModel());
    }

    public function demoGettingUserController()
    {
        $manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');
        $query = new MongoDB\Driver\Query(array('name' => 'khoa'));
        $cursor = $manager->executeQuery('training.user', $query);
        // Convert cursor to Array and print result
        print_r($cursor->toArray());
    }

    public function demoUpdateFields()
    {
        $fields = [
            'share' => [
                'download_url' => 'abc3.com'
            ]
        ];

        $this->load->model('usermodel', 'userModel');
        $update = $this->userModel->updateFieldsByEmail('minhkhoa031099@gmail.com', $fields);

        echo $update;
    }

    public function demoBeanstalkd()
    {

        // Hopefully you're using Composer autoloading.

        $pheanstalk = new Pheanstalk('127.0.0.1');

        // ----------------------------------------
// producer (queues jobs)

        $pheanstalk
            ->useTube('testtube')
            ->put("job payload goes here\n");

        // ----------------------------------------
// worker (performs jobs)

        $job = $pheanstalk
            ->watch('testtube')
            ->ignore('default')
            ->reserve();

        echo $job->getData();

        $pheanstalk->delete($job);

        // ----------------------------------------
// check server availability
        echo '<pre>';
        echo $pheanstalk->getConnection()->isServiceListening(); // true or false
        echo '<br>';
        print_r($pheanstalk->listTubes());
        echo '<br>';
        print_r($pheanstalk->listTubesWatched());
        echo '<br>';
        print_r($pheanstalk->listTubeUsed());
        echo '<br>';
        print_r($pheanstalk->statsTube('smailer'));
    }

    public function demoProducerBeanstalkd()
    {
        // put to default tube
        // echo $this->pheanstalk->put(json_encode(['name' => 'name', 1 => 1]));

        //put to specific tube
        // echo $this->pheanstalk->useTube('tube-name')->put(json_encode(['specific-tube' => 2]));

        // put to priority-tube
        // echo $this->pheanstalk->useTube('priority-tube')->put(json_encode(['priority-tube' => 1]), 1);
        // echo $this->pheanstalk->useTube('priority-tube')->put(json_encode(['priority-tube' => 100]), 100);
        // echo $this->pheanstalk->useTube('priority-tube')->put(json_encode(['priority-tube' => 2000]), 2000);
        // // priority 0
        // echo $this->pheanstalk->useTube('priority-tube')->put(json_encode(['priority-tube' => 0]), 0);

        // put with delay, that would be delay and then pass to ready state to wait for execute
        // echo 'delay, job id: ';
        // echo $this->pheanstalk->putInTube('delay-tube', json_encode(['delay' => 5]), null, 5);

        // put with ttr
        echo $this->pheanstalk->putInTube('ttr-tube', json_encode(['ttr' => 10]), null, 15, 0);

    }

    public function demoWorkerBeanstalkd()
    {
        //watch, default get the deffault tube, the job will be exist (not use delete command)
        // if you pause it and call => Fatal error: Maximum execution time of 120 seconds exceeded in C:\xampp\htdocs\document-sharing\vendor\pda\pheanstalk\src\Socket\StreamFunctions.php
        // print_r( $this->pheanstalk->reserve()->getData());

        // delay
        // if hasn't job and call again => Fatal error: Maximum execution time of 120 seconds exceeded in C:\xampp\htdocs\document-sharing\vendor\pda\pheanstalk\src\Socket\StreamFunctions.php on line 58
        // $delayJob = $this->pheanstalk->reserveFromTube('delay-tube');
        // print_r($delayJob->getData());
        // $this->pheanstalk->delete($delayJob);


        // call with timeout
        // $delayJob = $this->pheanstalk->reserveFromTube('delay-tube', 2);
        // if (!$delayJob)
        //     echo 'timeout 2s';

        //ttr
        $ttrJob = $this->pheanstalk->reserveFromTube('ttr-tube');
        echo 'ttr 1: ';
        print_r($ttrJob->getData());
        $this->pheanstalk->bury($ttrJob);
        echo 'ttr bury: ';
        print_r($ttrJob->getData());
        $this->pheanstalk->delete($ttrJob);

        // no tube existed before, will be create
        // $ttrJob = $this->pheanstalk->reserveFromTube('no-tube');
    }
}


// // Create a new scheduler
// $scheduler = new Scheduler();

// $scheduler->call(
//     function ($args) {
//         return $args['user'];
//     },
//     [
//         ['user' => $user],
//     ],
//     'myCustomIdentifier'
// );

// $scheduler->php('script.php')->everyMinute();

// // Let the scheduler execute jobs which are due.
// $scheduler->run();

// $schedulerWorker = new Scheduler();
// $schedulerWorker->php('scheduler.php');
// $schedulerWorker->work();