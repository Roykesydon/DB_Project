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

function msg($success,$status,$message,$extra = []){
    return array_merge([
        'success' => $success,
        'status' => $status,
        'message' => $message
    ],$extra);
}

function deleteDir($dirPath) {
    $it = new RecursiveDirectoryIterator($dirPath, RecursiveDirectoryIterator::SKIP_DOTS);
    $files = new RecursiveIteratorIterator($it,
                 RecursiveIteratorIterator::CHILD_FIRST);
    foreach($files as $file) {
        if ($file->isDir()){
            rmdir($file->getRealPath());
        } else {
            unlink($file->getRealPath());
        }
    }
    rmdir($dirPath);
}

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


    $uploaddir = '../../files/profileImages/'.$thisUser.'/';
    // // $target_file = $target_dir . basename($_FILES["unknown.png"]["name"]);
    // // unknown.png
    if (file_exists($uploaddir)) 
        deleteDir('../../files/profileImages/'.$thisUser.'/');

    mkdir('../../files/profileImages/'.$thisUser, 0777);
    
    $uploadfile = $uploaddir . $thisUser .".". end(explode('.', $_FILES["file"]['name']));
    if (move_uploaded_file($_FILES["file"]['tmp_name'], $uploadfile)) {
        //     // if (getimagesize($_FILES["file"]["tmp_name"])) {
        $returnData = msg(1, 200, "success");
    } else {
        $returnData = msg(0, 200, "upload fail");
    }
    echo json_encode($returnData);
}else{
    $returnData = msg(0, 401, "token is not valid");
    echo json_encode($returnData);
}

?>