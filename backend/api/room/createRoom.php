<?php

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

//create data array
$data = array();

$data["room_ID"] = $_POST['room_ID'];
$data["user_ID"] = $_POST["user_ID"];
$data["room_name"] = $_POST["room_name"];
$data["address"] = $_POST["address"];
$data["cost"] = $_POST["cost"];
$data["room_info"] = $_POST["room_info"];
$data["room_latitude"] = $_POST["room_latitude"];
$data["room_longtitude"] = $_POST["room_longtitude"];
$data["room_city"] = $_POST["room_city"];
$data["post_date"] = $_POST["post_date"];
$data["live_number"] = $_POST["live_number"];


if(
    !empty($data["room_ID"]) &&
    !empty($data["user_ID"]) &&
    !empty($data["room_name"]) && 
    !empty($data["address"]) &&
    !empty($data["cost"]) &&
    !empty($data["room_latitude"]) &&
    !empty($data["room_longtitude"]) && 
    !empty($data["room_city"]))
{
    //set room property values
    $rentRoom->room_ID = $data["room_ID"];
    $rentRoom->user_ID = $data["user_ID"];
    $rentRoom->room_name = $data["room_name"];
    $rentRoom->address = $data["address"];
    $rentRoom->cost = $data["cost"];
    $rentRoom->room_info = $data["room_info"];
    $rentRoom->room_latitude = $data["room_latitude"];
    $rentRoom->room_longitude = $data["room_longtitude"];
    $rentRoom->room_city = $data["room_city"];
    $rentRoom->post_date = $data["post_date"];
    $rentRoom->live_number = $data["live_number"];

    //create the room
    if($rentRoom->createRoom())
    {
        // set response code - 201 created
        http_response_code(201);
  
        // tell the user
        echo json_encode(array("message" => "Room was created."));
    }
    // if unable to create the product, tell the user
    else
    {
        // set response code - 503 service unavailable
        http_response_code(503);
  
        // tell the user
        echo json_encode(array("message" => "Unable to create room."));
    }
}
else
{
    // set response code - 400 bad request
    http_response_code(400);
  
    // tell the user
    echo json_encode(array("message" => "Unable to create room. Data is incomplete."));
}

?>