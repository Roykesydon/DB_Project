<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

// database connection will be here
// include database and object files
define('__ROOT__', dirname(dirname(__FILE__)));

require_once __ROOT__.'/config/database.php';

require_once __ROOT__.'/objects/rentRoom.php';

// instantiate database and rentRoom object
$database = new Database();
$db=$database->getConnection();
// initialize object
$rentRoom = new RentRoom($db);

// read the details of rentRoom to be edited
//$rentRoom->readAllRoom();

$index = $_GET['index'];

//query rentRoom
$stmt = $rentRoom->readAllRoom($index);
//get the room nums
$num = $stmt->rowCount();




//room array
$rentRoom_arr = array();
$rentRoom_arr["records"] = array();

// check if more than 0 record found
if($num > 0)
{
    while($row = $stmt->fetch(PDO::FETCH_ASSOC))
    {
        //get the room data
        $rentRoom_item = array(
            "room_ID" =>  $row['room_ID'],
            "user_ID" => $row['user_ID'],
            "room_name" => $row['room_name'],
            "address" => $row['address'],
            "cost" => $row['cost'],
            "room_info" => $row['room_info'],
            "room_latitude" => $row['room_latitude'],
            "room_longitude" => $row['room_longitude'],
            "room_city" => $row['room_city'],
            "post_date" => $row['post_date'],
            "live_number" => $row['live_number']
        );

        //push the room data into array
        array_push($rentRoom_arr["records"],$rentRoom_item);
    }
}
// set response code - 200 OK
http_response_code(200);

// show products data in json format
echo json_encode($rentRoom_arr);

?>