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
        try{
            // query to read single record
            $query = "SELECT * FROM `rentRoom` NATURAL JOIN `roomPicture` NATURAL JOIN `roomService` WHERE `user_ID` = ?;";
            // prepare query statement
            $stmt = $this->conn->prepare($query);
        
            // bind id of product to be updated
            $stmt->bindParam(1, $this->user_ID);
        
            // execute query
            $stmt->execute();

            return $stmt;
        
            // // get retrieved row
            // $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
            // // set values to object properties
            // $this->room_ID = $row['room_ID'];
            // $this->user_ID = $row['user_ID'];
            // $this->room_name = $row['room_name'];
            // $this->address = $row['address'];
            // $this->cost = $row['cost'];
            // $this->room_info = $row['room_info'];
            // $this->room_latitude = $row['room_latitude'];
            // $this->room_longitude = $row['room_longitude'];
            // $this->room_city = $row['room_city'];
            // $this->post_date = $row['post_date'];
            // $this->live_number = $row['live_number'];
            // $this->room_area = $row["room_area"];
        }catch(PDOException $e)
        {
            echo $e->getMessage();
            throw $e;
        }
    }

    function readRoomByRoomID(){
        try{
            // query to read single record
            $query = "SELECT * FROM `rentRoom` NATURAL JOIN `roomPicture` NATURAL JOIN `roomService` WHERE `room_ID` = ?;";
            // prepare query statement
            $stmt = $this->conn->prepare($query);
        
            // bind id of product to be updated
            $stmt->bindParam(1, $this->room_ID);
        
            // execute query
            $stmt->execute();

            return $stmt;
        }catch(PDOException $e)
        {
            echo $e->getMessage();
            throw $e;
        }
    }

    // used when filling up the update product form
    function getRoomByFilter($index,$keyword,$city,$tag,$smallCost,$largeCost){

        $tagListTmp = array("Wi-Fi","有線網路","電視","冰箱","停車位","冷氣","洗衣機","開伙","養寵物","電梯");
        for($i=0;$i<sizeof($tag);$i++){
            for($j=0;$j<sizeof($tagListTmp);$j++){
                if($tag[$i] == $tagListTmp[$j]){
                    $tagListTmp[$j] = 1;
                }
            }
        }
        for($i=0;$i<sizeof($tagListTmp);$i++){
            if($tagListTmp[$i] != 1){
                $tagListTmp[$i] = 0;
            }
        }
        // var_dump($tagListTmp);

        // echo 'keyword is '.$keyword."\n";
        // echo 'index is '.$index."\n";
        // echo 'city is '.$city."\n";
        // echo 'tag is '.$tag."\n";
        // echo 'smallCost is '.$smallCost."\n";
        // echo 'largeCost is '.$largeCost."\n";
        $keywordArr = array();
        $count = 0;
        try{
            $query = "SELECT * FROM {$this->table_name} NATURAL JOIN `roomPicture` NATURAL JOIN `roomService` ";
            if(!is_null($keyword)){
                $count += 1;
                // $query .= "WHERE `room_name` LIKE '%$keyword%' ";
                $query .= "WHERE `room_name` LIKE ? ";
                array_push($keywordArr,'%'.$keyword.'%');
            }
            if(!is_null($city)){
                if($count > 0){
                    $count += 1;
                    $query .= " INTERSECT ";
                    // $query .= "SELECT * FROM {$this->table_name} NATURAL JOIN `roomPicture` NATURAL JOIN `roomService` WHERE `room_city` LIKE '%$city[0]%'";
                    $query .= "SELECT * FROM {$this->table_name} NATURAL JOIN `roomPicture` NATURAL JOIN `roomService` WHERE `room_city` LIKE ?";
                    array_push($keywordArr,'%'.$city[0].'%');
                    for($i=1;$i<sizeof($city);$i++){
                        // $query .= " OR `room_city` LIKE '%$city[$i]%' ";
                        $query .= " OR `room_city` LIKE ? ";
                        array_push($keywordArr,'%'.$city[$i].'%');
                    }
                } else {
                    $count += 1;
                    // $query .= "WHERE `room_city` LIKE '%$city[0]%'";
                    $query .= "WHERE `room_city` LIKE ?";
                    array_push($keywordArr,'%'.$city[0].'%');
                    for($i=1;$i<sizeof($city);$i++){
                        // $query .= " OR `room_city` LIKE '%$city[$i]%' ";
                        $query .= " OR `room_city` LIKE ? ";
                        array_push($keywordArr,'%'.$city[$i].'%');
                    }
                }
            }
            if(!is_null($smallCost) || !is_null($largeCost)){
                if($count > 0){
                    $count += 1;
                    $query .= " INTERSECT ";
                    $query .= " SELECT * FROM {$this->table_name} NATURAL JOIN `roomPicture` NATURAL JOIN `roomService` WHERE `cost` > $smallCost AND `cost` < $largeCost";
                    // $query .= " SELECT * FROM {$this->table_name} NATURAL JOIN `roomPicture` NATURAL JOIN `roomService` WHERE `cost` > ? AND `cost` < ?";
                } else {
                    $count += 1;
                    $query .= " WHERE `cost` > $smallCost AND `cost` < $largeCost";
                    // $query .= " WHERE `cost` > ? AND `cost` < ?";
                }
                // array_push($keywordArr,(int)$smallCost);
                // array_push($keywordArr,(int)$largeCost);
            }
            if(!is_null($tag)){
                if($count > 0){
                    $count += 1;
                    $query .= " INTERSECT ";
                    $query .= " SELECT * FROM {$this->table_name} NATURAL JOIN `roomPicture` NATURAL JOIN `roomService` WHERE `wifi` = $tagListTmp[0] AND `internet` = $tagListTmp[1] AND `tv` = $tagListTmp[2] AND `refrigerator` = $tagListTmp[3] AND `parking` = $tagListTmp[4] AND `ac` = $tagListTmp[5]  AND `washing_machine` = $tagListTmp[6] AND `can_cooking` = $tagListTmp[7] AND `can_keep_pet` = $tagListTmp[8] AND `elevator` = $tagListTmp[9]";
                    // $query .= " SELECT * FROM {$this->table_name} NATURAL JOIN `roomPicture` NATURAL JOIN `roomService` WHERE `cost` > ? AND `cost` < ?";
                } else {
                    $count += 1;
                    $query .= " WHERE `wifi` = $tagListTmp[0] AND `internet` = $tagListTmp[1] AND `tv` = $tagListTmp[2] AND `refrigerator` = $tagListTmp[3] AND `parking` = $tagListTmp[4] AND `ac` = $tagListTmp[5]  AND `washing_machine` = $tagListTmp[6] AND `can_cooking` = $tagListTmp[7] AND `can_keep_pet` = $tagListTmp[8] AND `elevator` = $tagListTmp[9]";
                    // $query .= " WHERE `cost` > ? AND `cost` < ?";
                }
                // array_push($keywordArr,(int)$smallCost);
                // array_push($keywordArr,(int)$largeCost);
            }
            echo "\n";
            // $query .= " ORDER BY `room_ID` LIMIT ".$index*20;
            // $query .= " ,20";
            $query .= " ORDER BY `room_ID` LIMIT ?, ?;";
            array_push($keywordArr,$index*20);
            array_push($keywordArr,20);
            // echo $query."\n";
            $stmt = $this->conn->prepare($query);
            $stmt->execute($keywordArr);
            // $stmt->execute();
            return $stmt;
        
        }catch(PDOException $e)
        {
            echo $e->getMessage();
            throw $e;
        }
    }

    function getRoomByKeyword($keyword){
        try{
            // query to read single record
            $query = "SELECT * FROM {$this->table_name} NATURAL JOIN `roomPicture` NATURAL JOIN `roomService` WHERE `room_name` LIKE ?";
            // prepare query statement
            $stmt = $this->conn->prepare($query);        
            // execute query
            $stmt->execute(array('%'.$keyword.'%'));
            return $stmt;
        
        }catch(PDOException $e)
        {
            echo $e->getMessage();
            throw $e;
        }
    }

    function getRoomByCity($City){
        try{
            // query to read single record
            $cities = array();
            $query = "SELECT * FROM {$this->table_name} NATURAL JOIN `roomPicture` NATURAL JOIN `roomService` WHERE `room_city` LIKE ?";
            array_push($cities,'%'.$City[0].'%');
            for($i=1;$i<sizeof($City);$i++){
                $query .= " OR `room_city` LIKE ?";
                array_push($cities,'%'.$City[$i].'%');
            }
            // prepare query statement
            $stmt = $this->conn->prepare($query);        
            // execute query
            // echo $query;
            $stmt->execute($cities);
            return $stmt;
        
        }catch(PDOException $e)
        {
            echo $e->getMessage();
            throw $e;
        }
    }

    function getRoomByCost($lowerCost,$higherCost){
        try{
            // query to read single record
            // SELECT * FROM `rentRoom` NATURAL JOIN `roomPicture` NATURAL JOIN `roomService` WHERE `cost` BETWEEN 0 AND 100000000
            $query = "SELECT * FROM {$this->table_name} NATURAL JOIN `roomPicture` NATURAL JOIN `roomService` WHERE `cost` BETWEEN ? AND ? ";
            // prepare query statement
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1,$lowerCost,PDO::PARAM_INT);
            $stmt->bindParam(2,$higherCost,PDO::PARAM_INT);
            // execute query
            // $stmt->execute(array((int)$lowerCost,(int)$higherCost));
            $stmt->execute();
            // $stmt->debugDumpParams();
            return $stmt;
        
        }catch(PDOException $e)
        {
            echo $e->getMessage();
            throw $e;
        }
    }

    function readAllRoom($index){
        // echo $index;
        // query to read single record
        $query = "SELECT * FROM {$this->table_name} NATURAL JOIN `roomPicture` NATURAL JOIN `roomService` ORDER BY 'room_ID' LIMIT ?, ?;";

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

    function updateRoom(){
        try{
            // query to update record
            $query = "UPDATE {$this->table_name} 
                        set `room_name` = ?,
                            `address` = ?,
                            `cost` = ?,
                            `room_info` = ?,
                            `room_latitude` = ?,
                            `room_longitude` = ?,
                            `room_city` = ?,
                            `post_date` = ?,
                            `live_number` = ?,
                            `room_area` = ?
                      WHERE `room_ID` = ?;";

            // prepare query statement
            $stmt = $this->conn->prepare($query);
            //bind Param to the corresponding question mark placeholder
            $stmt->bindValue(1,$this->room_name);
            $stmt->bindParam(2,$this->address);
            $stmt->bindParam(3,$this->cost);
            $stmt->bindParam(4,$this->room_info);
            $stmt->bindParam(5,$this->room_latitude);
            $stmt->bindParam(6,$this->room_longitude);
            $stmt->bindParam(7,$this->room_city);
            $stmt->bindParam(8,$this->post_date);
            $stmt->bindParam(9,$this->live_number);
            $stmt->bindParam(10,$this->room_area);
            $stmt->bindParam(11,$this->room_ID);
            //execute the SQL instruction
            $stmt->execute();
            
            return true;
        }catch(PDOException $e)
        {
            // tell the user
            // echo json_encode(array("success" => "0","message" => "Unable to create roominfo."));

            throw $e;
        }


    }

    function deleteRoom()
    {
        try{
            // query to delete record
            $query = "DELETE FROM `rentRoom` WHERE `room_ID` = ?;";
            // prepare query statement
            $stmt = $this->conn->prepare($query);
            //bind Param to the corresponding question mark placeholder
            $stmt->bindValue(1,$this->room_ID);
            //execute the SQL instruction
            $stmt->execute();
            
            return true;
        }catch(PDOException $e)
        {
            throw $e;
        }
    }
    

    function getCityRoomCount()
    {
        try{
            // query to count room
            $query = "SELECT count(`room_ID`) as total_room FROM `rentRoom` WHERE `room_city` = ?;";
            // prepare query statement
            $stmt = $this->conn->prepare($query);
            //bind Param to the corresponding question mark placeholder
            $stmt->bindParam(1,$this->room_city);
            //execute the SQL instruction
            $stmt->execute();

            return $stmt;
        }catch(PDOException $e)
        {
            throw $e;
        }
    }
}
?>