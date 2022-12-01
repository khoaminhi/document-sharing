<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//$mongo_db = new Mongo_db('training', '127.0.0.1', 27017);
$mongo_db = new Mongo_db();


class BaseModel extends CI_Model {
    protected $mongodb;
    function __construct($mongo_db)
    {
        $this->mongodb = $mongo_db;
    }
}