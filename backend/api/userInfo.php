<?php
/**
 * !!!!!!!!NOTE!!!!!!!!
 * this is show how to use auth.php and let user send valid request to api
 */
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require './config/database.php';
require './middleware/auth.php';

$allHeaders = getallheaders();
$db_connection = new Database();
$conn = $db_connection->getConnection();
$auth = new Auth($conn,$allHeaders);
// echo $auth->getSecret();
$returnData = [
    "success" => 0,
    "status" => 401,
    "message" => "Unauthorized"
];

/*
returnData = [success] => 1
             [status] => 200
             [user] => Array
                (
                    [user_ID] => boji1234
                    [user_name] => 一二三四五六七八九
                    [password] => $2y$10$vNZ/QQd5JJ5qIb4w5b4z0.5iFq3suGpPss0w.zwOi26Cu34hC09CG
                    [email] => abc@gmail.com
                    [phone_number] => 00000000000
                    [access_key] => 
                    [timestamp] => 
                )
 */

if($auth->isAuth()){
    // enter here if is log in
    $returnData = $auth->isAuth();
    echo "以".$returnData['user']['user_ID']."登入";
    // write code below

} else {
    echo "invalid token";
}

// echo json_encode($returnData);
?>