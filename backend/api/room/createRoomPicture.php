<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST/GET");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

// database connection will be here
// include database and object files
define('__ROOT__', dirname(dirname(__FILE__)));

require_once __ROOT__.'/config/database.php';

require_once __ROOT__.'/objects/roomPicture.php';

// instantiate database and roomPicture object
$database = new Database();
$db=$database->getConnection();
// initialize object
$roomPicture = new RoomPicture($db);

//create data array
$data = array();

$data["room_ID"] = $_POST["room_ID"];
$data["pictureURL_one"] = $_POST["pictureURL_one"];
$data["pictureURL_two"] = $_POST["pictureURL_two"];
$data["pictureURL_three"] = $_POST["pictureURL_three"];
$data["pictureURL_four"] = $_POST["pictureURL_four"];
$data["pictureURL_five"] = $_POST["pictureURL_five"];
$data["pictureURL_six"] = $_POST["pictureURL_six"];
$data["pictureURL_seven"] = $_POST["pictureURL_seven"];
$data["pictureURL_eight"] = $_POST["pictureURL_eight"];

if(
    !empty($data["room_ID"])
)
{
    $roomPicture->room_ID = $data["room_ID"];
    $roomPicture->pictureURL_one = $data["pictureURL_one"];
    $roomPicture->pictureURL_two = $data["pictureURL_two"];
    $roomPicture->pictureURL_three = $data["pictureURL_three"];
    $roomPicture->pictureURL_four = $data["pictureURL_four"];
    $roomPicture->pictureURL_five = $data["pictureURL_five"];
    $roomPicture->pictureURL_six = $data["pictureURL_six"];
    $roomPicture->pictureURL_seven = $data["pictureURL_seven"];
    $roomPicture->pictureURL_eight = $data["pictureURL_eight"];


    //create the roomPicture
    if($roomPicture->createRoomPicture())
    {
        // set response code - 201 created
        http_response_code(201);
  
        // tell the user
        echo json_encode(array("message" => "Roompicture was created."));
    }
    // if unable to create the roomPicture, tell the user
    else
    {
        // set response code - 503 service unavailable
        http_response_code(503);
  
        // tell the user
        echo json_encode(array("message" => "Unable to create roomPicture."));
    }
}
else
{
    // set response code - 400 bad request
    http_response_code(400);
  
    // tell the user
    echo json_encode(array("message" => "Unable to create roomPicture. Data is incomplete."));
}

?>