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

function dealReturn($stmt){
        //initial array
        $rentRoom_arr = array();
        $rentRoom_arr["records"] = array();
        $URLs = array();
        $services = array();
        $tagList = array("Wi-Fi","有線網路","電視","冰箱","停車位","冷氣","洗衣機","開伙","養寵物","電梯");
        // $stmt = array_intersect($keywordResult,);
        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            // $baseURL = "../../files/roomImages/" . $row['user_ID'] . "/";
            //處理URL
            array_push($URLs,$row["pictureURL_one"],$row["pictureURL_two"],$row["pictureURL_three"],$row["pictureURL_four"],$row["pictureURL_five"],$row["pictureURL_six"],$row["pictureURL_seven"],$row["pictureURL_eight"]);
            //delete null value
            $URLs = array_filter($URLs);
            // for($i = 0;$i<count($URLs);$i++)
            //     $URLs[$i] =  $baseURL . $URLs[$i];
            //處理services
            $temp = array();
            array_push($temp,$row["wifi"],$row["internet"],$row["tv"],$row["refrigerator"],$row["parking"],$row["ac"],$row["washing_machine"],$row["can_cooking"],$row["can_keep_pet"],$row["elevator"]);
            for($i = 0;$i < count($temp);$i++)
            {
                if($temp[$i])
                    array_push($services,$tagList[$i]);
            }
            //get the room data
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

            //push the room data into array
            array_push($rentRoom_arr["records"],$rentRoom_item);
            $URLs = array();
            $services = array();
        }
        return $rentRoom_arr;
}

$returnMsg = [
    "success" => 0,
    "status" => 401,
    "message" => "Unauthorized"
];

// read the details of rentRoom to be edited
//$rentRoom->readAllRoom();

$index = $_GET["index"];
$keyword = $_GET["keyword"];
$city = $_GET["city"];
$tag = $_GET["tag"];
$cost = $_GET["cost"];

$Cost = isset($cost) ? explode(',',$cost) : null;
$keyword = isset($keyword) ? $keyword : null;
$City = isset($city) ? explode(',',$city) : null;
$Tag = isset($tag) ? explode(',',$tag) : null;


//initial array
$rentRoom_arr = array();
$rentRoom_arr["records"] = array();
$URLs = array();
$services = array();
$tagList = array("Wi-Fi","有線網路","電視","冰箱","停車位","冷氣","洗衣機","開伙","養寵物","電梯");


if ($_SERVER["REQUEST_METHOD"] != "GET") 
{
    http_response_code(404);
    $returnMsg = msg(0,404,"Page Not Found!");
    echo json_encode($returnMsg);
}
else{
    try{
        $stmt = $rentRoom->getRoomByFilter($index,$keyword,$City,$Tag,$Cost[0],$Cost[1]);
        //query rentRoom
        // $keywordResult = null;
        // $cityResult = null;
        // $costResult = null;
        // if(!is_null($keyword)){
        //     $tmp = $rentRoom->getRoomByKeyword($keyword);
        //     $keywordResult = dealReturn($tmp);
        //     echo "keywordResult is ".sizeof($keywordResult["records"])."\n";
        // }
        // if(!is_null($City)){
        //     $tmp = $rentRoom->getRoomByCity($City);
        //     $cityResult = dealReturn($tmp);
        //     echo "cityResult is ".sizeof($cityResult["records"])."\n";
        // }
        // if(!is_null($Cost)){
        //     $tmp = $rentRoom->getRoomByCost($Cost[0],$Cost[1]);
        //     $costResult = dealReturn($tmp);
        //     echo "costResult is ".sizeof($costResult["records"])."\n";
        //     // echo json_encode($costResult);
        // }
        // if(!is_null($keywordResult)){
        //     $result = $keywordResult;
        //     if(!is_null($cityResult) && sizeof($cityResult) > 0){
        //         array_intersect($result,$cityResult);   
        //     }
        //     if(!is_null($costResult) && sizeof($costResult) > 0){
        //         array_intersect($result,$costResult);
        //     }
        // }elseif(!is_null($cityResult)){
        //     $result = $cityResult;
        //     if(!is_null($keywordResult) && sizeof($keywordResult) > 0){
        //         array_intersect($result,$keywordResult);   
        //     }
        //     if(!is_null($costResult) && sizeof($costResult) > 0){
        //         array_intersect($result,$costResult);
        //     }
        // }elseif(!is_null($costResult)){
        //     $result = $costResult;
        //     if(!is_null($cityResult) && sizeof($cityResult) > 0){
        //         array_intersect($result,$cityResult);   
        //     }
        //     if(!is_null($keywordResult) && sizeof($keywordResult) > 0){
        //         array_intersect($result,$keywordResult);
        //     }
        // }
        // if(sizeof($keywordResult["records"]) > 1 && sizeof($cityResult["records"]) > 1){
        //     // $result = $keywordResult;
        //     // print_r($cityResult["records"]);
        //     $result = array_intersect($keywordResult["records"],$cityResult["records"]);
        // } else {
        //     echo "no\n";
        // }
        

        // $stmt = array_intersect($keywordResult,);
        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            // echo"row";
            // print_r($row);
            // $baseURL = "../../files/roomImages/" . $row['user_ID'] . "/";
            //處理URL
            array_push($URLs,$row["pictureURL_one"],$row["pictureURL_two"],$row["pictureURL_three"],$row["pictureURL_four"],$row["pictureURL_five"],$row["pictureURL_six"],$row["pictureURL_seven"],$row["pictureURL_eight"]);
            //delete null value
            $URLs = array_filter($URLs);
            // echo "URLS";
            // print_r($URLs);
            // for($i = 0;$i<count($URLs);$i++)
            //     $URLs[$i] =  $baseURL . $URLs[$i];
            //處理services
            $temp = array();
            array_push($temp,$row["wifi"],$row["internet"],$row["tv"],$row["refrigerator"],$row["parking"],$row["ac"],$row["washing_machine"],$row["can_cooking"],$row["can_keep_pet"],$row["elevator"]);
            for($i = 0;$i < count($temp);$i++)
            {
                if($temp[$i])
                    array_push($services,$tagList[$i]);
            }
            //get the room data
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

            //push the room data into array
            array_push($rentRoom_arr["records"],$rentRoom_item);
            $URLs = array();
            $services = array();
        }
        // set response code - 200 OK
        http_response_code(200);

        // show products data in json format
        // echo $index;
        echo json_encode($rentRoom_arr);
    }catch(PDOException $e)
    {
        //error occur
        http_response_code(400);
        $returnMsg = msg(0,400,"Error!." . $e->getMessage());
        echo json_encode($returnMsg);
    }
}
// // set response code - 200 OK
// http_response_code(200);

// // show products data in json format
// echo json_encode($rentRoom_arr);

?>