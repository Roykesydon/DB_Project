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
    public $elevator;

    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    function createRoomService()
    {
        try{
            // query to insert record
            $query = "INSERT INTO {$this->table_name} values(?,?,?,?,?,?,?,?,?,?,?);";

            // prepare query statement
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1,$this->room_ID);
            $stmt->bindParam(2,$this->wifi);
            $stmt->bindParam(3,$this->internet);
            $stmt->bindParam(4,$this->tv);
            $stmt->bindParam(5,$this->refrigerator);
            $stmt->bindParam(6,$this->parking);
            $stmt->bindParam(7,$this->ac);
            $stmt->bindParam(8,$this->washing_machine);
            $stmt->bindParam(9,$this->can_cooking);
            $stmt->bindParam(10,$this->can_keep_pet);
            $stmt->bindParam(11,$this->elevator);
            //execute the SQL stmt
            $stmt->execute();

            return true;
        }catch(PDOException $e)
        {
            // tell the user
            echo json_encode(array("success" => "0","message" => "Unable to create roomService.")) . "\n";
            throw $e;
        }

        // if($stmt->execute())
        // {
        //     return true;
        // }
        // else
        // {
        //     return false;
        // }
    }

}
?>