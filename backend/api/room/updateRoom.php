<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// database connection will be here
// include database and object files
define('__ROOT__', dirname(dirname(__FILE__)));

require_once __ROOT__.'/config/database.php';

require_once __ROOT__.'/objects/rentRoom.php';

$database = new Database();
$db = $database->getConnection();

$rentRoom = new RentRoom($db);

?>