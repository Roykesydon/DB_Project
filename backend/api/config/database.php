<?php
/**
 * 查詢docker container IP
 * docker inspect container_ID | grep "IPAddress"
 */

class Database{
    // specify your own database credentials
    private $mysql_address = "172.25.0.2"; // mariaDB's IP
    private $mysql_username = "root";   // 設定連接資料庫用戶帳號
    private $mysql_password = "root";   // 設定連接資料庫用戶的密碼
    private $mysql_database = "db_final";   // 設成你在 mysql 創的資料庫
    private $mysql_portnumber = "3306"; // port number
    public $conn;

    // get the database connection
    function getConnection(){
        $this->conn="null";
        try{
            $this->conn = new PDO("mysql:host={$this->mysql_address};port={$this->mysql_portnumber};dbname={$this->mysql_database}",$this->mysql_username,$this->mysql_password);
            $this->conn->exec("set names utf8mb4");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // SQL Injection
        }catch(PDOException $e){
            echo "Connection error: " . $e->getMessage();
            exit;
        }
  
        return $this->conn;
    }

    function test(){
        echo "test";
        return "testreturn";
    }
}
?>