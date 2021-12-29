<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
// database connection will be here
// include database and object files
define('__ROOT__', dirname(dirname(__FILE__)));

require __ROOT__.'/config/database.php';

require_once __ROOT__.'/middleware/auth.php';

require_once __ROOT__.'/objects/user.php';
//get conn
$database = new Database();
$db = $database->getConnection();
//initiate user
$user = new User($db);
$user->user_ID = $_GET['user_ID'];

function msg($success,$status,$message,$extra = []){
    return array_merge([
        'success' => $success,
        'status' => $status,
        'message' => $message
    ],$extra);
}

//instantiate authentication objects
// echo $auth->getSecret();
$returnMsg = [
    "success" => 0,
    "status" => 401,
    "message" => "Unauthorized"
];
$returnData = array();
$returnData["record"] = array();

//check request method
if ($_SERVER["REQUEST_METHOD"] != "GET") 
{
    $returnMsg = msg(0,404,"Page Not Found!");
    echo json_encode($returnMsg);
}
else{
    try{
        //get the user data
        $stmt = $user->getProfileByUserID();
        // echo var_dump($stmt->fetchAll());
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if($result !== false)
        {
            array_push($returnData["record"],$result);
            echo json_encode($returnData);
        }
        else{
            $returnMsg = msg(0,404,"The user_ID doesn't exist.".$_GET['user_ID']);
            echo json_encode($returnMsg);
        }
    }catch(PDOException $e)
    {
        $returnMsg = msg(0,400,"Error!." . $e->getMessage());
        echo json_encode($returnMsg);
    }
}


?>