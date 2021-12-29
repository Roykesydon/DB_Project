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
    public $room_area;

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
        $this->room_area = $row["room_area"];
    }

    function readAllRoom($index){
        // query to read single record
        $query = "SELECT * FROM {$this->table_name} ORDER BY 'room_ID' LIMIT ?, ?;";

        // prepare query statement
        $stmt = $this->conn->prepare( $query );
    
        // bind id of product to be updated
        //$stmt->bindParam(1, $this->user_ID);
    
        // execute query
        $stmt->execute(array($index*20, 20));

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

    function createRoom(){
        try{
            // query to insert record
            $query = "INSERT INTO {$this->table_name} values(?,?,?,?,?,?,?,?,?,?,?,?);";

            // prepare query statement
            $stmt = $this->conn->prepare($query);
            //bind Param to the corresponding question mark placeholder
            $stmt->bindValue(1,0);
            $stmt->bindParam(2,$this->user_ID);
            $stmt->bindParam(3,$this->room_name);
            $stmt->bindParam(4,$this->address);
            $stmt->bindParam(5,$this->cost);
            $stmt->bindParam(6,$this->room_info);
            $stmt->bindParam(7,$this->room_latitude);
            $stmt->bindParam(8,$this->room_longitude);
            $stmt->bindParam(9,$this->room_city);
            $stmt->bindParam(10,$this->post_date);
            $stmt->bindParam(11,$this->live_number);
            $stmt->bindParam(12,$this->room_area);
            //execute the SQL instruction
            $stmt->execute();
            
            return true;
        }catch(PDOException $e)
        {
            // tell the user
            // echo json_encode(array("success" => "0","message" => "Unable to create roominfo."));

            throw $e;
        }

        //execute the SQL instruction
        // if($stmt->execute())
        // {
        //     return true;
        // }
        // else{
        //     return false;
        // }
    }
    
}
?>