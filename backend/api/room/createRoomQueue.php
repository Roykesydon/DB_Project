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

require_once __ROOT__.'/objects/roomQueue.php';

require './config/database.php';
require './middleware/auth.php';

// instantiate database and roomQueue object
$database = new Database();
$db=$database->getConnection();
// initialize object
$roomQueue = new RoomQueue($db);

//instantiate authentication objects
$allHeaders = getallheaders();
$auth = new Auth($db,$allHeaders);
// echo $auth->getSecret();
$returnData = [
    "success" => 0,
    "status" => 401,
    "message" => "Unauthorized"
];

$data = json_decode(file_get_contents("php://input"),true);

/*$data["room_ID"] = $_POST["room_ID"];
$data["user_ID_one"] = $_POST["user_ID_one"];
$data["user_ID_two"] = $_POST["user_ID_two"];
$data["user_ID_three"] = $_POST["user_ID_three"];
$data["user_ID_four"] = $_POST["user_ID_four"];
$data["user_ID_five"] = $_POST["user_ID_five"];
$data["user_ID_six"] = $_POST["user_ID_six"];
$data["user_ID_seven"] = $_POST["user_ID_seven"];
$data["user_ID_eight"] = $_POST["user_ID_eight"];
$data["user_ID_nine"] = $_POST["user_ID_nine"];
$data["user_ID_ten"] = $_POST["user_ID_ten"];*/

if($auth->isAuth())
{
    // enter here if is log in
    $returnData = $auth->isAuth();
    echo "以".$returnData['user']['user_ID']."登入";
    // write code below
    if(//check not null
        !empty($data["room_ID"])
    )
    {
        $roomQueue->room_ID = $data["room_ID"];
        $roomQueue->user_ID_one = $data["user_ID_one"];
        $roomQueue->user_ID_two = $data["user_ID_two"];
        $roomQueue->user_ID_three = $data["user_ID_three"];
        $roomQueue->user_ID_four = $data["user_ID_four"];
        $roomQueue->user_ID_five = $data["user_ID_five"];
        $roomQueue->user_ID_six = $data["user_ID_six"];
        $roomQueue->user_ID_seven = $data["user_ID_seven"];
        $roomQueue->user_ID_eight = $data["user_ID_eight"];
        $roomQueue->user_ID_nine = $data["user_ID_nine"];
        $roomQueue->user_ID_ten = $data["user_ID_ten"];

        //create the roomQueue
        if($roomQueue->createRoomQueue())
        {
            // set response code - 201 created
            http_response_code(201);
    
            // tell the user
            echo json_encode(array("message" => "roomQueue was created."));
        }
        // if unable to create the roomQueue
        else
        {
            // set response code - 503 service unavailable
            http_response_code(503);
    
            // tell the user
            echo json_encode(array("message" => "Unable to create roomQueue."));
        }
    }
    else
    {
        // set response code - 400 bad request
        http_response_code(400);
    
        // tell the user
        echo json_encode(array("message" => "Unable to create roomQueue. Data is incomplete."));
    }
}else{
    echo "invalid token\n";
}


?>