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
$user_ID = $_GET['user_ID'];

function msg($success,$status,$message,$extra = []){
    return array_merge([
        'success' => $success,
        'status' => $status,
        'message' => $message
    ],$extra);
}


$returnData = [
    "success" => 0,
    "status" => 401,
    "message" => "Unauthorized"
];

//create data array
$data = json_decode(file_get_contents("php://input"),true);


$uploaddir = '../../files/profileImages/'.$user_ID.'/';
// // $target_file = $target_dir . basename($_FILES["unknown.png"]["name"]);
// // unknown.png

$path="../../files/profileImages/defaultProfileImage.png";
if ($user_ID != null && file_exists($uploaddir)) {
    $files = glob($uploaddir."*.*");
    $path = "../../files/profileImages/".$user_ID."/".basename($files[0]);
}


$content = file_get_contents($path);
header('Content-Type: image/gif');
echo $content;

?>