<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// database connection will be here
// include database and object files
define('__ROOT__', dirname(dirname(__FILE__)));

require_once __ROOT__.'/config/database.php';

require_once __ROOT__.'/objects/roomService.php';

require_once __ROOT__.'/middleware/auth.php';


// instantiate database and roomService object
$database = new Database();
$db=$database->getConnection();

//instantiate authentication objects
$allHeaders = getallheaders();
$auth = new Auth($db,$allHeaders);
// echo $auth->getSecret();
$returnData = [
    "success" => 0,
    "status" => 401,
    "message" => "Unauthorized"
];

// initialize object
$roomService = new RoomService($db);

//create data array
$data = json_decode(file_get_contents("php://input"),true);

if($auth->isAuth())
{
    // enter here if is log in
    $returnData = $auth->isAuth();
    // echo "以".$returnData['user']['user_ID']."登入\n";
    // write code below


    //check if created
    $check_room_ID = "SELECT `room_ID` FROM `roomService` WHERE `room_ID` = ?;";

    $check_room_ID_stmt = $db->prepare($check_room_ID);

    $check_room_ID_stmt->bindValue(1,$data["room_ID"]);

    $check_room_ID_stmt->execute();

    $check_room = "SELECT `room_ID` FROM `rentRoom` WHERE `room_ID` = ?;";

    $check_room_stmt = $db->prepare($check_room);

    $check_room_stmt->bindValue(1,$data["room_ID"]);

    $check_room_stmt->execute();

    if($check_room_ID_stmt->rowCount())
    {
        echo json_encode(array("message" => "Unable to create roomService. Because the room has created."));
    }
    else if($check_room_stmt->rowCount() == 0)
    {
        echo json_encode(array("message" => "Unable to create roomService. Because we don't have the room!"));
    }
    else{
        if( // check NOT NULL
            isset($data["room_ID"]) &&
            isset($data["wifi"]) &&
            isset($data["internet"]) &&
            isset($data["tv"]) &&
            isset($data["refrigerator"]) &&
            isset($data["parking"]) &&
            isset($data["ac"]) &&
            isset($data["washing_machine"]) &&
            isset($data["can_cooking"]) &&
            isset($data["can_keep_pet"]) &&
            isset($data["elevator"])
            )
            {
                $roomService->room_ID = $data["room_ID"];
                $roomService->wifi = $data["wifi"];
                $roomService->internet = $data["internet"];
                $roomService->tv = $data["tv"];
                $roomService->refrigerator = $data["refrigerator"];
                $roomService->parking = $data["parking"];
                $roomService->ac = $data["ac"];
                $roomService->washing_machine = $data["washing_machine"];
                $roomService->can_cooking = $data["can_cooking"];
                $roomService->can_keep_pet = $data["can_keep_pet"];
                $roomService->elevator = $data["elevator"];
    
    
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
    
            } else{
                // set response code - 400 bad request
                http_response_code(400);
    
                // tell the user
                echo json_encode(array("message" => "Unable to create roomService. Data is incomplete."));
            }
    }
} else{
    echo "invalid token\n";
}

?>