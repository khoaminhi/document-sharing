<?php
defined('BASEPATH') or exit('No direct script access allowed');
require 'C:\xampp\htdocs\document-sharing\vendor\autoload.php';
// require 'vendor/autoload.php';
use MongoDB\Client;

class UserController extends CI_Controller
{

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     *	- or -
     * 		http://example.com/index.php/welcome/index
     *	- or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see https://codeigniter.com/userguide3/general/urls.html
     */
    public function index()
    {
        $this->load->view('users/khoaView');
    }

    public function insertUser($param)
    {
        $formValidateConfig = array(
            array(
                'field' => 'email', 'label' => 'email', 'rules' => 'required|valid_email',
                'errors' => array(
                    'required' => 'Bạn phải nhập %s.',
                    'valid_email' => 'Bạn phải nhập phải chính xác là email'
                ),
            ),
            array(
                'field' => 'name', 'label' => 'Tên người dùng', 'rules' => 'required|min_length[1]',
                'errors' => array(
                    'required' => 'Bạn phải nhập tên của mình.',
                    'min_length' => 'Bạn phải nhập tên của mình.',
                ),
            ),
            array(
                'field' => 'age', 'label' => 'Tuổi', 'rules' => 'required|is_natural_no_zero',
                'errors' => array(
                    'required' => 'Bạn phải nhập tuổi của mình',
                    'is_natural_no_zero' => 'Tuổi của bạn phải là số nguyên và phải lớn hơn 0',
                ),
            ),
            array(
                'field' => 'gender', 'label' => 'Giới tính', 'rules' => 'required|in_list[M, F]',
                'errors' => array(
                    'required' => 'Bạn phải nhập giới của mình',
                    'in_list' => 'Giới tính của bạn phải là nam hoặc nữ (M hoặc F)'
                ),
            ),
            array(
                'field' => 'occupation', 'label' => 'Nghề nghiệp', 'rules' => 'required|min_length[1]',
                'errors' => array(
                    'required' => 'Bạn phải nhập nghề nghiệp của mình',
                    'min_length' => 'Bạn chưa nhập nghề nghiệp'
                ),
            ),
            array(
                'field' => 'address', 'label' => 'Địa chỉ', 'rules' => 'required|min_length[1]',
                'errors' => array(
                    'required' => 'Bạn phải nhập địa chỉ của mình',
                    'min_length' => 'Bạn chưa cung cấp địa chỉ'
                ),
            ),
        );

        $this->load->helper(array('form', 'url'));

        $this->load->library('form_validation');
        $this->form_validation->set_rules($formValidateConfig);

        $data = array('resultForModal' => 'Fail to validate');
        $this->load->view('commons/headHtml');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('users/signupView', $data);
        } else {
            $data['resultForModal'] = 'Successfully';
            $this->load->view('users/signupView', $data);
        }
        $this->load->view('commons/bodyHtml');

        echo $param;
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
