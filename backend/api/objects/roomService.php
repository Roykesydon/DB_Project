<?php
class RoomService{
    // database connection and table name
    private $table_name = "roomService";
    private $conn;

    // properties
    public $room_ID;
    public $wifi;
    public $internet;
    public $tv;
    public $refrigerator;
    public $parking;
    public $ac;
    public $washing_machine;
    public $can_cooking;
    public $can_keep_pet;

    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }
}
?>