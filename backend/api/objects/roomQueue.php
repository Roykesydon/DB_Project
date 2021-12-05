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
}
?>