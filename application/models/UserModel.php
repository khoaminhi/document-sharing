<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//$mongo_db = new Mongo_db('training', '127.0.0.1', 27017);
// $mongodb = new Mongo_db();


class UserModel extends CI_Model {
    function __construct()
    {
        $this->mongodb = new Mongo_db();
    }

    public function insertUser(array $user) {
        $insertUserResult = $this->mongo_db->insert('user', $user);
        return $insertUserResult;
    }
    public function getAllUserModel() {
        $result = $this->mongodb->get("user");
        print_r($result);
    }

    public function demoUserModel() {
        $users = array(
            'user1' => array(
                'name' => 'khoa',
                'age' => 23
            ),
            'user2' => array(
                'name' => 'minhi',
                'age' => 23
            ),
        );

        return $users;
    }
}