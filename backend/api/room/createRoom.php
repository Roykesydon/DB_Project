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

require_once __ROOT__.'/objects/rentRoom.php';


echo "test\n";
echo "test\n";
echo "test\n";
echo "test\n";

require_once __ROOT__ . '/middleware/auth.php';

echo "require success";


// instantiate database and rentRoom object
$database = new Database();
$db=$database->getConnection();
// initialize object
$rentRoom = new RentRoom($db);

echo "test\n";

// //instantiate authentication objects
// $allHeaders = getallheaders();
// $auth = new Auth($db,$allHeaders);
// // echo $auth->getSecret();
// $returnData = [
//     "success" => 0,
//     "status" => 401,
//     "message" => "Unauthorized"
// ];

//create data array
$data = json_decode(file_get_contents("php://input"),true);

echo "test\n";

// if($auth->isAuth())
// {
    // // enter here if is log in
    // $returnData = $auth->isAuth();
    // echo "以".$returnData['user']['user_ID']."登入";
    // // write code below
    // $thisUser = $returnData['user']['user_ID'];
    // $user_ID = $data["user_ID"];
    //if(strcmp($thisUser,$user_ID) == 0)
   // {
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
    // }else{
    //     echo json_encode(array("message" => "You can't create this room!!!"));
    // }
// }else{
//     echo "invalid token\n";
// }

?>