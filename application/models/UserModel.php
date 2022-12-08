<?php
defined('BASEPATH') or exit('No direct script access allowed');
//$mongo_db = new Mongo_db('training', '127.0.0.1', 27017);
// $mongodb = new Mongo_db();


class UserModel extends CI_Model
{
    // function __construct()
    // {
    //     // $this->mongodb = new Mongo_db();
    // }

    public function filterUser($data) {
        $arrFields = ['email' => 'email', 'name' => 'name'];
        $conditions = [];
        $result = null;

        foreach($arrFields as $queryKey => $fieldKey) {
            if ($data[$queryKey] !== '') {
                $conditions[$fieldKey] = $this->mongo_db->regex($data[$queryKey], 'i', false, false);
            }
        }

        if ($data['share'] !== '') {
            if ($data['share'] === '0') {
                $conditions['share'] = $this->mongo_db->exists(false);
            } else {
                $conditions['share'] = $this->mongo_db->exists(true);
                if ($data['openned_mail'] !== '') {
                    ($data['openned_mail'] === '0') ?
                        $conditions['share.openned_mail'] = false :
                        $conditions['share.openned_mail'] = true;
                }
                if ($data['downloaded'] !== '') {
                    ($data['downloaded'] === '0') ?
                        $conditions['share.downloaded'] = 0 :
                        $conditions['share.downloaded'] = $this->mongo_db->gt(0);
                }
            }
        }

        $result = $this->mongo_db->get_Where('user', $conditions);
        foreach ($result as &$user) {
            if (isset($user['share'])) {
                if (isset($user['share']['send_time']))
                    $user['share']['send_time'] = date('m/d/Y H:i:s', $user['share']['send_time']);
                if (isset($user['share']['openned_mail_time']))
                    $user['share']['openned_mail_time'] = date('m/d/Y H:i:s', $user['share']['openned_mail_time']);
                if (isset($user['share']['downloaded_time']))
                    $user['share']['downloaded_time'] = date('m/d/Y H:i:s', $user['share']['downloaded_time']);
            }
        }
        return $result;
    }

    public function findOneByDownloadUrl($idDownloadUrl)
    {
        $result = $this->mongo_db->where('share.download_url', $idDownloadUrl)->get('user');
        if (empty($result))
            return null;

        return $result[0];
    }

    public function findOneByEmail($email)
    {
        try {
            $result = $this->mongo_db->where('email', $email)->get('user');

            if (empty($result))
                return null;

            return $result[0];
        } catch (Exception $e) {
            echo 'UserModel Error - findOneByEmail. Message:  ' . $e->getMessage();
        }
    }

    public function findOne($id)
    {
        // $condition = array('_id' => $_id); // new MongoDB\BSON\ObjectId() or method create_document_id

        // $result = $this->mongo_db->where($condition)->get('user');
        try {
            // throw new ErrorException('My error', 1000);
            $result = $this->mongo_db->where('_id', new MongoDB\BSON\ObjectId($id))->get('user');
            if (empty($result))
                return null;

            return $result[0];
        } catch (Exception $e) {
            echo 'UserModel Error - findOne. Message:  ' . $e->getMessage();
        }
    }

    public function insertUser(array $user)
    {
        $insertUserResult = $this->mongo_db->insert('user', $user);
        return $insertUserResult;
    }

    public function updateFieldsByEmail($email, array $fields)
    {
        $updateResult = $this->mongo_db->where(['email' => $email])
            ->set($fields)
            ->update('user');

        // $this->mongo_db->where(['email' => $email])
        //     ->setOnInsert($fields)
        //     ->update('user');

        return $updateResult;
    }

    public function updateFieldsByIdDownloadUrl($idDownloadUrl, array $fields)
    {
        $updateResult = $this->mongo_db->where('share.download_url', $idDownloadUrl)
            ->set($fields)
            ->update('user');

        // $this->mongo_db->where(['email' => $email])
        //     ->setOnInsert($fields)
        //     ->update('user');

        return $updateResult;
    }

    public function getAllUser()
    {
        $result = $this->mongo_db->get("user");
        date_default_timezone_set("Asia/Ho_Chi_Minh");
        foreach ($result as &$user) {
            if (isset($user['share'])) {
                if (isset($user['share']['send_time']))
                    $user['share']['send_time'] = date('m/d/Y H:i:s', $user['share']['send_time']);
                if (isset($user['share']['openned_mail_time']))
                    $user['share']['openned_mail_time'] = date('m/d/Y H:i:s', $user['share']['openned_mail_time']);
                if (isset($user['share']['downloaded_time']))
                    $user['share']['downloaded_time'] = date('m/d/Y H:i:s', $user['share']['downloaded_time']);
            }
        }
        return $result;
    }

    public function demoUserModel()
    {
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