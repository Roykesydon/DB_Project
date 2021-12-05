<?php
class User{
    // database connection and table name
    private $table_name = "user";
    private $conn;

    // properties
    public $user_ID;
    public $user_name;
    public $password;
    public $email;
    public $phone_number;
    public $access_key;
    public $timestamp;

    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }
}
?>