<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//$mongo_db = new Mongo_db('training', '127.0.0.1', 27017);
// $mongodb = new Mongo_db();


class UserModel extends CI_Model {
    // function __construct()
    // {
    //     // $this->mongodb = new Mongo_db();
    // }


    public function findOneByDownloadUrl($idDownloadUrl) {
        $result = $this->mongo_db->where('share.download_url', $idDownloadUrl)->get('user');
        if (empty($result))
            return null;

        return $result[0];
    }

    public function findOneByEmail($email) {
        try {
            $result = $this->mongo_db->where('email', $email)->get('user');
            
            if (empty($result))
                return null;
                
            return $result[0];
        }
        catch (Exception $e) {
            echo 'UserModel Error - findOneByEmail. Message:  ' . $e->getMessage();
        }
    }

    public function findOne($id) {
        // $condition = array('_id' => $_id); // new MongoDB\BSON\ObjectId() or method create_document_id
        
        // $result = $this->mongo_db->where($condition)->get('user');
        try {
            // throw new ErrorException('My error', 1000);
            $result = $this->mongo_db->where('_id', new MongoDB\BSON\ObjectId($id))->get('user');
            if (empty($result))
                return null;

            return $result[0];
        }
        catch (Exception $e) {
            echo 'UserModel Error - findOne. Message:  ' . $e->getMessage();
        }
    }

    public function insertUser(array $user) {
        $insertUserResult = $this->mongo_db->insert('user', $user);
        return $insertUserResult;
    }

    public function updateFieldsByEmail($email, array $fields) {
        $updateResult = $this->mongo_db->where(['email' => $email])
            ->set($fields)
            ->update('user');
        
        // $this->mongo_db->where(['email' => $email])
        //     ->setOnInsert($fields)
        //     ->update('user');
            
        return $updateResult;
    }

    public function updateFieldsByIdDownloadUrl($idDownloadUrl, array $fields) {
        $updateResult = $this->mongo_db->where('share.download_url', $idDownloadUrl)
            ->set($fields)
            ->update('user');
        
        // $this->mongo_db->where(['email' => $email])
        //     ->setOnInsert($fields)
        //     ->update('user');
            
        return $updateResult;
    }

    public function getAllUserModel() {
        $result = $this->mongo_db->get("user");
        return $result;
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