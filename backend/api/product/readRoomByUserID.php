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

// set ID property of record to read
$rentRoom->user_ID = isset($_GET['userID']) ? $_GET['userID'] : die();

// read the details of rentRoom to be edited
$rentRoom->readByUserId();

if($rentRoom->room_ID!=null){
    // create array
    $rentRoom_arr = array(
        "room_ID" =>  $rentRoom->room_ID,
        "user_ID" => $rentRoom->user_ID,
        "room_name" => $rentRoom->room_name,
        "address" => $rentRoom->address,
        "cost" => $rentRoom->cost,
        "room_info" => $rentRoom->room_info,
        "room_latitude" => $rentRoom->room_latitude,
        "room_longitude" => $rentRoom->room_longitude,
        "room_city" => $rentRoom->room_city,
        "post_date" => $rentRoom->post_date,
        "live_number" => $rentRoom->live_number
    );
  
    // set response code - 200 OK
    http_response_code(200);
  
    // make it json format
    echo json_encode($rentRoom_arr);
}
  
else{
    // set response code - 404 Not found
    http_response_code(404);
  
    // tell the user rentRoom does not exist
    echo json_encode(array("message" => "rentRoom does not exist."));
}
?>