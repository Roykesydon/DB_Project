<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
// database connection will be here
// include database and object files
define('__ROOT__', dirname(dirname(__FILE__)));

require __ROOT__.'/config/database.php';

require_once __ROOT__.'/objects/rentRoom.php';

require_once __ROOT__.'/middleware/auth.php';

// instantiate database and rentRoom object
$database = new Database();
$db=$database->getConnection();
// initialize object
$rentRoom = new RentRoom($db);

//instantiate authentication objects
$allHeaders = getallheaders();
$auth = new Auth($db,$allHeaders);
// echo $auth->getSecret();
$returnData = [
    "success" => 0,
    "status" => 401,
    "message" => "Unauthorized"
];

//create data array
$data = json_decode(file_get_contents("php://input"),true);

if($auth->isAuth())
{
    // enter here if is log in
    $returnData = $auth->isAuth();
    // echo "以".$returnData['user']['user_ID']."登入";
    // write code below
    $thisUser = $returnData['user']['user_ID'];
    $user_ID = $data["user_ID"];

    $check_room = "SELECT `room_ID` FROM `rentRoom` WHERE `room_ID` = ?;";

    $check_room_stmt = $db->prepare($check_room);

    $check_room_stmt->bindValue(1,$data["room_ID"]);

    $check_room_stmt->execute();

    if($check_room_stmt->rowCount())
    {
        echo json_encode(array("message" => "Unable to create room.Because the room has created."));
    }
    else{
        //Authentication
        if(strcmp($thisUser,$user_ID) == 0)
        {
            if( //check not null
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
                $rentRoom->room_area = $data["room_area"];

                //create the room
                if($rentRoom->createRoom())
                {
                    // set response code - 201 created
                    http_response_code(201);
            
                    // tell the user
                    echo json_encode(array("message" => "Room was created."));
                }
                // if unable to create the room, tell the user
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
        }else{
            echo json_encode(array("message" => "You can't create this room!!!"));
        }
    }
}else{
    echo "invalid token\n";
}

?>