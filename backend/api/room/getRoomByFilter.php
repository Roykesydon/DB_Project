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
//get GET data
$index = $_GET["index"];
?>