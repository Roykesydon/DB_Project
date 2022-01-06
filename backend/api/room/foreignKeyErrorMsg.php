<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
// database connection will be here
// include database and object files
define('__ROOT__', dirname(dirname(__FILE__)));

require __ROOT__.'/config/database.php';

require_once __ROOT__.'/objects/rentRoom.php';

// instantiate database and rentRoom object
$database = new Database();
$db=$database->getConnection();
// initialize object
$rentRoom = new RentRoom($db);

// IF REQUEST METHOD IS NOT EQUAL TO POST
if ($_SERVER["REQUEST_METHOD"] != "GET") 
{
    // http_response_code(200);
    echo json_encode(array("success" => 0 , "status" => 404,"message" => "Page Not Found!"));
}
else
{
    $data = $_GET;
    $rentRoom->room_ID = $data["room_ID"];
    $rentRoom->user_ID = $data["user_ID"];
    if(!empty($data["room_ID"]) &&
       !empty($data["user_ID"]))
    {
        $query = "SELECT * FROM `rentRoom` WHERE `room_ID` = ?;";
        $stmt = $db->prepare($query);
        $stmt->bindParam(1,$data["room_ID"]);
        $stmt->execute();
        $row = $stmt->fetch();
        if(!empty($row))
        {
            try{
                $rentRoom->foreignKeyError();
            }catch(PDOException $e)
            {
                echo "Error happen!\n" . $e->getMessage();
            }
        }else{
            echo json_encode("The room hasn't created!");
        }
    }else{
        echo json_encode("You should fill the data!");
    }
}
//close the database connection
$db = null;
?>
