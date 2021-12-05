<?php
class RoomPicture{
    // database connection and table name
    private $table_name = "roomPicture";
    private $conn;

    // properties
    public $room_ID;
    public $pictureURL_one;
    public $pictureURL_two;
    public $pictureURL_three;
    public $pictureURL_four;
    public $pictureURL_five;
    public $pictureURL_six;
    public $pictureURL_seven;
    public $pictureURL_eight;

    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }
}
?>