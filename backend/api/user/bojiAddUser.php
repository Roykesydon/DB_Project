<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

// database connection will be here
// include database and object files
define('__ROOT__', dirname(dirname(__FILE__)));

require_once __ROOT__.'/config/database.php';

require_once __ROOT__.'/objects/user.php';
// instantiate database and user object
$database = new Database();
$db = $database->getConnection();
// initialize object
$user = new User($db);
//create data array
$data = array();

$data["user_ID"] = $_GET["user_ID"];
$data["user_name"] = $_GET["user_name"];
$data["password"] = $_GET["password"];
$data["email"] = $_GET["email"];
$data["phone_number"] = $_GET["phone_number"];
$data["access_key"] = $_GET["access_key"];
$data["timestamp"] = $_GET["timestamp"];

if(
    !empty($data["user_ID"]) &&
    !empty($data["user_name"]) &&
    !empty($data["password"]) &&
    !empty($data["email"]) &&
    !empty($data["phone_number"])
)
{
    $user->user_ID = $data["user_ID"];
    $user->user_name = $data["user_name"];
    $user->password = $data["password"];
    $user->email = $data["email"];
    $user->phone_number = $data["phone_number"];
    $user->access_key = $data["access_key"];
    $user->timestamp = $data["timestamp"];

    //add the User
    if($user->addUser())
    {
        // set response code - 201 created
        http_response_code(201);
  
        // tell the user
        echo json_encode(array("message" => "User was added."));
    }
    // if unable to add the User
    else
    {
        // set response code - 503 service unavailable
        http_response_code(503);
  
        // tell the user
        echo json_encode(array("message" => "Unable to add User."));
    }
}
else
{
    // set response code - 400 bad request
    http_response_code(400);
  
    // tell the user
    echo json_encode(array("message" => "Unable to add User. Data is incomplete."));
}

?>