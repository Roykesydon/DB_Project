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
    //addUser
    function addUser()
    {
        // query to insert record
        $query = "INSERT INTO {$this->table_name} values(?,?,?,?,?,?,?);";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1,$this->user_ID);
        $stmt->bindParam(2,$this->user_name);
        $stmt->bindParam(3,$this->password);
        $stmt->bindParam(4,$this->email);
        $stmt->bindParam(5,$this->phone_number);
        $stmt->bindParam(6,$this->access_key);
        $stmt->bindParam(7,$this->timestamp);

        if($stmt->execute())
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    function getProfileByUserID()
    {
        try{
            // query to insert record
            $query = "SELECT `user_ID`, `user_name`, `email`, `phone_number` FROM `user` WHERE `user_ID` = ?";

            // prepare query statement
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1,$this->user_ID);
            $stmt->execute();

            return $stmt;
        }catch(PDOException $e)
        {
            throw $e;
        }
    }
}
?>