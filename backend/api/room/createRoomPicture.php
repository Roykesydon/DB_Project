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

require_once __ROOT__.'/objects/roomPicture.php';

require_once __ROOT__.'/middleware/auth.php';

// instantiate database and roomPicture object
$database = new Database();
$db=$database->getConnection();
// initialize object
$roomPicture = new RoomPicture($db);

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
    if( //check not null
        !empty($data["room_ID"])     
    )
    {
        //set room property values
        $roomPicture->room_ID = $data["room_ID"];
        $roomPicture->pictureURL_one = $data["pictureURL_one"];
        $roomPicture->pictureURL_two = $data["pictureURL_two"];
        $roomPicture->pictureURL_three = $data["pictureURL_three"];
        $roomPicture->pictureURL_four = $data["pictureURL_four"];
        $roomPicture->pictureURL_five = $data["pictureURL_five"];
        $roomPicture->pictureURL_six = $data["pictureURL_six"];
        $roomPicture->pictureURL_seven = $data["pictureURL_seven"];
        $roomPicture->pictureURL_eight = $data["pictureURL_eight"];

        //create the room
        if($roomPicture->createRoomPicture())
        {
            // set response code - 201 created
            http_response_code(201);
          
            // tell the user
            echo json_encode(array("message" => "RoomPicture was created."));
        }
        // if unable to create the product, tell the user
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
}else{
    echo "invalid token\n";
}

?>