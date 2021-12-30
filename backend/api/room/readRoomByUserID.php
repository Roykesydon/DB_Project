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

require_once __ROOT__.'/middleware/auth.php';
// instantiate database and rentRoom object
$database = new Database();
$db=$database->getConnection();
// initialize object
$rentRoom = new RentRoom($db);

function msg($success,$status,$message,$extra = []){
    return array_merge([
        'success' => $success,
        'status' => $status,
        'message' => $message
    ],$extra);
}

//instantiate authentication objects
$allHeaders = getallheaders();
$auth = new Auth($db,$allHeaders);
// echo $auth->getSecret();
$returnMsg = [
    "success" => 0,
    "status" => 401,
    "message" => "Unauthorized"
];
$returnData = array();
$returnData["record"] = array();

// //$rentRoom->user_ID = isset($_GET['userID']) ? $_GET['userID'] : die();

//set ID property of record to read
$rentRoom->user_ID = $_GET['user_ID'];

//// read the details of rentRoom to be edited
//// $rentRoom->readByUserId();

//initial array
$rentRoom_arr = array();
$rentRoom_arr["records"] = array();
$URLs = array();
$services = array();
$tagList = array("Wi-Fi","有線網路","電視","冰箱","停車位","冷氣","洗衣機","開伙","養寵物","電梯");

$baseURL = dirname(dirname(dirname(__FILE__))) . "/files/roomImages/";

//check REQUEST_METHOD
if ($_SERVER["REQUEST_METHOD"] != "GET") 
{
    http_response_code(404);
    $returnMsg = msg(0,404,"Page Not Found!");
    echo json_encode($returnMsg);
}
else{
    //check whether the user login
    if($auth->isAuth())
    {
        try{
            $stmt = $rentRoom->readByUserId();
            // $row = $stmt->fetch(PDO::FETCH_ASSOC);
            // echo var_dump($row);
            //read the room info by user_ID
            while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                //處理URL
                array_push($URLs,$row["pictureURL_one"],$row["pictureURL_two"],$row["pictureURL_three"],$row["pictureURL_four"],$row["pictureURL_five"],$row["pictureURL_six"],$row["pictureURL_seven"],$row["pictureURL_eight"]);
                $URLs = array_filter($URLs);
                for($i = 0;$i<count($URLs);$i++)
                    $URLs[$i] =  $baseURL . $URLs[$i];
                //處理services
                $temp = array();
                array_push($temp,$row["wifi"],$row["internet"],$row["tv"],$row["refrigerator"],$row["parking"],$row["ac"],$row["washing_machine"],$row["can_cooking"],$row["can_keep_pet"],$row["elevator"]);
                for($i = 0;$i < count($temp);$i++)
                {
                    if($temp[$i])
                        array_push($services,$tagList[$i]);
                }
                //catch data
                $rentRoom_item = array(
                    "room_ID" =>  $row["room_ID"],
                    "user_ID" => $row["user_ID"],
                    "room_name" => $row["room_name"],
                    "address" => $row["address"],
                    "cost" => $row["cost"],
                    "room_info" => $row["room_info"],
                    "room_latitude" => $row["room_latitude"],
                    "room_longitude" => $row["room_longitude"],
                    "room_city" => $row["room_city"],
                    "post_date" => $row["post_date"],
                    "live_number" => $row["live_number"],
                    "room_area" => $row["room_area"],
                    "URLs"  => $URLs,
                    "services" => $services
                );
                //put data into result
                array_push($rentRoom_arr["records"],$rentRoom_item);
                $URLs = array();
                $services = array();
            }

                // set response code - 200 OK
            http_response_code(200);
        
            // make it json format
            echo json_encode($rentRoom_arr);

        }catch(PDOException $e)
        {
            //error occur
            http_response_code(400);
            $returnMsg = msg(0,400,"Error!." . $e->getMessage());
            echo json_encode($returnMsg);
        }
    
    }else{
        //invalid token
        http_response_code(401);
        $returnMsg = msg(0,401,"invalid token");
        echo json_encode($returnMsg);
    }
}
  
// else{
//     // set response code - 404 Not found
//     http_response_code(404);
  
//     // tell the user rentRoom does not exist
//     echo json_encode(array("message" => "rentRoom does not exist."));
// }
?>