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

function msg($success,$status,$message,$extra = []){
    return array_merge([
        'success' => $success,
        'status' => $status,
        'message' => $message
    ],$extra);
}

// echo $auth->getSecret();
$returnMsg = [
    "success" => 0,
    "status" => 401,
    "message" => "Unauthorized"
];
$result = 0;
$cityList = array("新北市","臺北市","桃園市","臺中市","臺南市","高雄市","新竹縣","苗栗縣","彰化縣","南投縣","雲林縣","嘉義縣","屏東縣","宜蘭縣","花蓮縣","臺東縣","澎湖縣","金門縣","連江縣","基隆市","新竹市","嘉義市");
//set room_city property of record to read
$rentRoom->room_city = $_GET['roomCity'];

//check REQUEST_METHOD
if ($_SERVER["REQUEST_METHOD"] != "GET") 
{
    http_response_code(404);
    $returnMsg = msg(0,404,"Page Not Found!");
    echo json_encode($returnMsg);
}
else{
    if(in_array($_GET['roomCity'],$cityList))
    {
        try{
            $stmt = $rentRoom->getCityRoomCount();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $result = $row['total_room'];
            // echo var_dump($row);
            //read the room info by user_ID

                // set response code - 200 OK
            http_response_code(200);
        
            // make it json format
            echo json_encode($result);

        }catch(PDOException $e)
        {
            //error occur
            http_response_code(400);
            $returnMsg = msg(0,400,"Error!." . $e->getMessage());
            echo json_encode($returnMsg);
        }
    }else{
        http_response_code(400);
        $returnMsg = msg(0,400,"City must be in citylist!");
        echo json_encode($returnMsg);
    }
}
?>