<?php
class RoomQueue{
    // database connection and table name
    private $table_name = "roomQueue";
    private $conn;

    // properties
    public $room_ID;
    public $user_ID_one;
    public $user_ID_two;
    public $user_ID_three;
    public $user_ID_four;
    public $user_ID_five;
    public $user_ID_six;
    public $user_ID_seven;
    public $user_ID_eight;
    public $user_ID_nine;
    public $user_ID_ten;

    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    function createRoomQueue()
    {
        // query to insert record
        $query = "INSERT INTO {$this->table_name} values(?,?,?,?,?,?,?,?,?,?,?);";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1,$this->room_ID);
        $stmt->bindParam(2,$this->user_ID_one);
        $stmt->bindParam(3,$this->user_ID_two);
        $stmt->bindParam(4,$this->user_ID_three);
        $stmt->bindParam(5,$this->user_ID_four);
        $stmt->bindParam(6,$this->user_ID_five);
        $stmt->bindParam(7,$this->user_ID_six);
        $stmt->bindParam(8,$this->user_ID_seven);
        $stmt->bindParam(9,$this->user_ID_eight);
        $stmt->bindParam(10,$this->user_ID_nine);
        $stmt->bindParam(11,$this->user_ID_ten);

        if($stmt->execute())
        {
            return true;
        }
        else
        {
            return false;
        }
    }

}
?>