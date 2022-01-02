<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
define('__ROOT__', dirname(dirname(__FILE__)));

require __ROOT__.'/config/database.php';

// instantiate database 
$database = new Database();
$db = $database->getConnection();

function msg($success,$status,$message,$extra = []){
    return array_merge([
        'success' => $success,
        'status' => $status,
        'message' => $message
    ],$extra);
}
$returnMsg = [
    "success" => 0,
    "status" => 401,
    "message" => "Unauthorized"
];

$user_ID = $_GET['user_ID'];
$fileName = $_GET['URL'];
$fileCount = count($URLs);
$uploaddir = '../../files/roomImages/' . $user_ID . '/';
// // $target_file = $target_dir . basename($_FILES["unknown.png"]["name"]);
// // unknown.png

//check REQUEST_METHOD
if ($_SERVER["REQUEST_METHOD"] != "GET") 
{
    http_response_code(404);
    $returnMsg = msg(0,404,"Page Not Found!");
    echo json_encode($returnMsg);
}
else{
    // $content = array();
    if(!empty($user_ID) &&
       !empty($fileName))
    {
        //set the header
        header('Content-Type: image/gif');
        $path="../../files/roomImages/defaultRoomImage.jpg";
        if(file_exists($uploaddir . $fileName) && strcmp($uploaddir,$uploaddir . $fileName) != 0)
        {
            // $files = glob($uploaddir . "*.*");
            // $path = "../../files/roomImages/" . $user_ID . "/" . basename($files[0]);
            //the picture URL
            $path = "../../files/roomImages/" . $user_ID . "/" . $fileName;
            // $path = $URLs[$i];
            // echo $path;
            $tmpContent = file_get_contents($path);
            echo $tmpContent;
        }else{
            // echo $path;
            $tmpContent = file_get_contents($path);
            echo $tmpContent;
        }
    }else{
        http_response_code(400);
        if(empty($user_ID))
            $returnMsg = msg(0,400,"user_ID is empty!");
        else if(empty($fileName))
            $returnMsg = msg(0,400,"URL is empty!");
        echo json_encode($returnMsg);
    }

}

//close the database connection
$db = null;
?>