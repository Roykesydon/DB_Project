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

require_once __ROOT__.'/objects/roomService.php';

// instantiate database and roomService object
$database = new Database();
$db=$database->getConnection();
// initialize object
$roomService = new RoomService($db);

//create data array
$data = array();

$data["room_ID"] = $_POST["room_ID"];
$data["wifi"] = $_POST["wifi"];
$data["internet"] = $_POST["internet"];
$data["tv"] = $_POST["tv"];
$data["refrigerator"] = $_POST["refrigerator"];
$data["parking"] = $_POST["parking"];
$data["ac"] = $_POST["ac"];
$data["wasing_machine"] = $_POST["wasing_machine"];
$data["can_cooking"] = $_POST["can_cooking"];
$data["can_keep_pet"] = $_POST["can_keep_pet"];

if(
    !empty($data["room_ID"]) &&
    !empty($data["wifi"]) &&
    !empty($data["internet"]) &&
    !empty($data["tv"]) &&
    !empty($data["refrigerator"]) &&
    !empty($data["parking"]) &&
    !empty($data["ac"]) &&
    !empty($data["wasing_machine"]) &&
    !empty($data["can_cooking"]) &&
    !empty($data["can_keep_pet"])
)
{
    $roomService->room_ID = $data["room_ID"];
    $roomService->wifi = $data["wifi"];
    $roomService->internet = $data["internet"];
    $roomService->tv = $data["tv"];
    $roomService->refrigerator = $data["refrigerator"];
    $roomService->parking = $data["parking"];
    $roomService->ac = $data["ac"];
    $roomService->washing_machine = $data["wasing_machine"];
    $roomService->can_cooking = $data["can_cooking"];
    $roomService->can_keep_pet = $data["can_keep_pet"];

    //create the roomService
    if($roomService->createRoomService())
    {
        // set response code - 201 created
        http_response_code(201);
  
        // tell the user
        echo json_encode(array("message" => "roomService was created."));
    }
    // if unable to create the roomService
    else
    {
        // set response code - 503 service unavailable
        http_response_code(503);
  
        // tell the user
        echo json_encode(array("message" => "Unable to create roomService."));
    }
}
else
{
    // set response code - 400 bad request
    http_response_code(400);
  
    // tell the user
    echo json_encode(array("message" => "Unable to create roomService. Data is incomplete."));
}


?>