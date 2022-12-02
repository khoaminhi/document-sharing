<?php
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


class UserController extends CI_Controller
{
    // public $redisClient = GLOBAL['redisClient'];
    function __construct() {
        parent::__construct();
        $this->redisClient = new RedisClient([
            'host' => 'localhost',
            'port' => 6379
        ]);
    }
    public function index()
    {
        $this->load->view('users/khoaView');
    }

    public function registerView()
    {
        $this->load->library('form_validation');
        $this->load->view('commons/headHtml');
        $this->load->view('users/registerView');
        $this->load->view('commons/bodyHtml');
    }

    public function register()
    {
        try {
            $inputFields = array('email', 'name', 'age', 'gender', 'occupation', 'address');
            $userDocument = [];
            $urlVerify = "/user/verifyregister?";
            $dataView = array();
            $userRegisterInfo = [];

            // load form rule for registering user form => form_rule_register_user
            $this->load->helper('form_rules');

            $this->load->helper(array('form', 'url'));
            $this->load->library('form_validation');

            $this->form_validation->set_rules(form_rule_register_user());

            // Load view: modal, head html

            // show errors
            if ($this->form_validation->run() == FALSE) {
                $dataView['resultForModal'] = 'Nhập dữ liệu đăng ký không hợp lệ';
                $this->load->view('commons/headHtml');
                $this->load->view('users/registerView', $dataView);
                $this->load->view('commons/bodyHtml');
                return;
            }

            // check existed email
            $this->load->model('usermodel', 'userModel');
            $checkExistedEmail = $this->userModel->findOneByEmail($this->input->post('email'));

            if (!empty($checkExistedEmail)) {
                $dataView['resultForModal'] = 'Email đăng ký đã tồn tại';
                $this->load->view('commons/headHtml');
                $this->load->view('users/registerView', $dataView);
                $this->load->view('commons/bodyHtml');
            }

            // cache otp by redis
            $sixDigitRandomNumber = random_int(100000, 999999);
            $otpRedisKey = "email:" . (string) $this->input->post('email');
            $this->redisClient->set($otpRedisKey, $sixDigitRandomNumber, 'EX', 180); // 3 minutes
            $otpRedis = $this->redisClient->get($otpRedisKey);

            if ($otpRedis != $sixDigitRandomNumber) {
                echo 'Lỗi server. Không cache được otp';
                return;
            }
            die;
            // verify email by sending code


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
            // create verifying url that contains user infomation
            // foreach ($inputFields as $field) {
            //     $urlVerify .= $field . "=" . urlencode($this->input->post($field)) . "&";
            // }

            // redirect($urlVerify);
            // print_r($checkInsertUserResult);
            // sleep(5);
            // print_r($result);
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

            // load form rule for registering user form => form_rule_register_user
            $this->load->helper('form_rules');

            $this->load->helper(array('form', 'url'));
            $this->load->library('form_validation');

            $this->form_validation->set_rules(form_rule_verify_user());

            // show errors
            if ($this->form_validation->run() == FALSE) {
                $dataView['resultForModal'] = 'Dữ liệu xác minh đăng ký không đúng. Vui lòng đăng ký lại';
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


            foreach ($inputFields as $field) {
                $userDocument[$field] = $this->input->get($field);
            }


            // insert
            $this->load->model('userModel');
            $result = $this->userModel->insertUser($userDocument); // return document _id

            // check insert
            $checkInsertUserResult = $this->userModel->findOne($result);
            if (empty($checkInsertUserResult)) {
                $dataView['resultForModal'] = 'Đăng ký người dùng thất bại';
                $this->load->view('commons/headHtml');
                $this->load->view('users/registerView', $dataView);
                $this->load->view('commons/bodyHtml');
                return;
            } else {
                if ($checkInsertUserResult['email'] !== $this->input->get('email')) {
                    $dataView['resultForModal'] = 'Lưu thông tin người đăng ký sai sót';
                    $this->load->view('users/registerView', $dataView);
                    $this->load->view('commons/bodyHtml');
                    return;
                }
            }



            $dataView['resultForModal'] = 'Đăng ký nhận tài liệu thành công. Hãy mở hộp thư email của bạn để tải tài liệu';
            $this->load->view('users/registerView', $dataView);
            $this->load->view('commons/bodyHtml');
            // print_r($checkInsertUserResult);
            // sleep(5);
            // print_r($result);
        } catch (Exception $e) {
            if ($e instanceof UnexpectedValueException) {
                echo 'Bạn đã thay đổi thông tin đoạn mã';
                return;
            }

            echo 'UserController Error: ', $e->getMessage(), "\n";
        }
    }

    public function findOneById(string $id)
    {
        try {
            if (empty($id)) {
                echo '_id không được rỗng';
                return;
            }

            $this->load->model('userModel');
            $result = $this->userModel->findOne($id);
            print_r($result);
        } catch (Exception $e) {
            echo 'UserController Error: ', $e->getMessage(), "\n";
        }
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
        $decoded = JWT::decode($jwt, new Key($this->config->item('jwt_key'), 'HS256'));

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

    public function getAllUserController()
    {
        $this->load->model('userModel');
        $result = $this->userModel->getAllUserModel();
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
}