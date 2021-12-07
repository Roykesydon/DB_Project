<?php
class RentRoom{
    // database connection and table name
    private $table_name = "rentRoom";
    private $conn;

    // properties
    public $room_ID;
    public $user_ID;
    public $room_name;
    public $address;
    public $cost;
    public $room_info;
    public $room_latitude;
    public $room_longitude;
    public $room_city;
    public $post_date;
    public $live_number;

    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    // used when filling up the update product form
    function readByUserId(){
        // query to read single record
        $query = "SELECT * FROM {$this->table_name} WHERE user_ID='{$this->user_ID}';";
        // prepare query statement
        $stmt = $this->conn->prepare( $query );
    
        // bind id of product to be updated
        $stmt->bindParam(1, $this->user_ID);
    
        // execute query
        $stmt->execute();
    
        // get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // set values to object properties
        $this->room_ID = $row['room_ID'];
        $this->user_ID = $row['user_ID'];
        $this->room_name = $row['room_name'];
        $this->address = $row['address'];
        $this->cost = $row['cost'];
        $this->room_info = $row['room_info'];
        $this->room_latitude = $row['room_latitude'];
        $this->room_longitude = $row['room_longitude'];
        $this->room_city = $row['room_city'];
        $this->post_date = $row['post_date'];
        $this->live_number = $row['live_number'];
    }

    function readAllRoom(){
        // query to read single record
        $query = "SELECT * FROM {$this->table_name};";

        // prepare query statement
        $stmt = $this->conn->prepare( $query );
    
        // bind id of product to be updated
        //$stmt->bindParam(1, $this->user_ID);
    
        // execute query
        $stmt->execute();

        return $stmt;
    
        // get retrieved row
        //$row = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // set values to object properties
        /*$this->room_ID = $row['room_ID'];
        $this->user_ID = $row['user_ID'];
        $this->room_name = $row['room_name'];
        $this->address = $row['address'];
        $this->cost = $row['cost'];
        $this->room_info = $row['room_info'];
        $this->room_latitude = $row['room_latitude'];
        $this->room_longitude = $row['room_longitude'];
        $this->room_city = $row['room_city'];
        $this->post_date = $row['post_date'];
        $this->live_number = $row['live_number'];*/
    }
}
?>