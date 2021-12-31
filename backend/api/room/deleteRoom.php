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

function msg($success,$status,$message,$extra = []){
    return array_merge([
        'success' => $success,
        'status' => $status,
        'message' => $message
    ],$extra);
}

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

$fileNameArr = array();
$URLs = array();

// IF REQUEST METHOD IS NOT EQUAL TO POST
if ($_SERVER["REQUEST_METHOD"] != "POST") 
{
    echo json_encode(array("success" => 0 , "status" => 404,"message" => "Page Not Found!"));
}
else
{

    //create data array
    $data = json_decode(file_get_contents("php://input"),true);
    //Authentication
    if($auth->isAuth())
    {
        // enter here if is log in
        $returnData = $auth->isAuth();
        // echo "以".$returnData['user']['user_ID']."登入\n";
        // write code below
        $room_ID = $data['room_ID'];
        //now User's user_ID
        $thisUser = $returnData['user']['user_ID'];

        //get the room User's user_ID
        $query = "SELECT `user_ID` FROM `rentRoom` WHERE `room_ID` = ?;";
        $stmt = $db->prepare($query);

        $stmt->bindParam(1,$room_ID);
        $stmt->execute();

        $checkUserID = $stmt->fetch();
        //room owner's user_ID
        $roomUser = $checkUserID['user_ID'];
        //set the file route
        $baseURL = "../../files/roomImages/" . $roomUser . "/";
        //delete the old pictures
        $query = "SELECT * FROM `roomPicture` WHERE `room_ID` = ?;";
        $stmt = $db->prepare($query);
        $stmt->bindParam(1,$room_ID);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        array_push($URLs,$row["pictureURL_one"],$row["pictureURL_two"],$row["pictureURL_three"],$row["pictureURL_four"],$row["pictureURL_five"],$row["pictureURL_six"],$row["pictureURL_seven"],$row["pictureURL_eight"]);
        //delete null value
        $URLs = array_filter($URLs);
        for($i = 0;$i<count($URLs);$i++)
            $URLs[$i] =  $baseURL . $URLs[$i];
        for($i = 0;$i < count($URLs);$i++)
            unlink($URLs[$i]);

        # 取得上傳檔案數量
        $fileCount = count($_FILES['file1']['name']);


            
    }else{
        echo json_encode(array("success" => 0,"message" => "invalid token."));
    }
}
//close the database connection
$db = null;
?>
