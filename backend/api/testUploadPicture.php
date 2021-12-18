<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

function msg($success, $status, $message, $extra = [])
{
    return array_merge([
        'success' => $success,
        'status' => $status,
        'message' => $message
    ], $extra);
}

require './config/database.php';
require 'JWTHandler.php';

$database = new Database();
$conn = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));
$returnData = [];

// IF REQUEST METHOD IS NOT EQUAL TO POST
if ($_SERVER["REQUEST_METHOD"] != "POST") :
    $returnData = msg(0, 404, 'Page Not Found!');

// CHECKING EMPTY FIELDS
// elseif(!isset($data->files)
//     || empty(trim($data->files))
//     ):

//     $fields = ['fields' => ['files']];
//     $returnData = msg(0,422,'Please Fill in all Required Fields!',$fields);

// IF THERE ARE NO EMPTY FIELDS THEN-
else :
    // $files = $data->files;

    $returnData = msg(0, 422, $_POST['user_ID'] . $_FILES["file1"]["name"]);

     # 取得上傳檔案數量
     $fileCount = count($_FILES['file1']['name']);

     for ($i = 0; $i < $fileCount; $i++) {
         # 檢查檔案是否上傳成功
         if ($_FILES['file1']['error'][$i] === UPLOAD_ERR_OK)
         {
           echo '檔案名稱: ' . $_FILES['file1']['name'][$i] . ".\n";
           echo '檔案類型: ' . $_FILES['file1']['type'][$i] . ".\n";
           echo '檔案大小: ' . ($_FILES['file1']['size'][$i] / 1024) . "KB\n";
           echo '暫存名稱: ' . $_FILES['file1']['tmp_name'][$i] . ".\n";
 
           $uploaddir = "../files/roomImages/";
       
           # 檢查檔案是否已經存在
           if (file_exists($uploaddir . $_FILES['file1']['name'][$i]))
           {
             $returnData = msg(0, 422, $_POST['user_ID'] . "file has already existed");
           } else {
             $file = $_FILES['file1']['tmp_name'][$i];
             $dest = $uploaddir . $_FILES['file1']['name'][$i];
       
             # 將檔案移至指定位置
             move_uploaded_file($file, $dest);
             $returnData = msg(1, 201, $_POST['user_ID'] . "File successfully uploaded.\n");
           }
         } else {
           $returnData = msg(0, 503, $_POST['user_ID'] . $_FILES['file1']['error']);
         }

        }
    // $uploaddir = '../files/roomImages/';
    // // // $target_file = $target_dir . basename($_FILES["unknown.png"]["name"]);
    // // // unknown.png
    // $uploadfile = $uploaddir . basename($_FILES["file1"]["name"]);
    // if (move_uploaded_file($_FILES["file1"]['tmp_name'], $uploadfile)) {
    //     //     // if (getimagesize($_FILES["file"]["tmp_name"])) {
    //     $returnData = msg(0, 422, $_POST['user_ID'] . "File successfully uploaded.\n");
    // } else {
    //     $returnData = msg(0, 422, "File upload fail\n");
    // }
// // Do the work for each file

// echo $files;



// $password = trim($data->password);

// // CHECKING THE EMAIL FORMAT (IF INVALID FORMAT)
// if(!filter_var($email, FILTER_VALIDATE_EMAIL)):
//     $returnData = msg(0,422,'Invalid Email Address!');

// // IF PASSWORD IS LESS THAN 8 THE SHOW THE ERROR
// elseif(strlen($password) < 8):
//     $returnData = msg(0,422,'Your password must be at least 8 characters long!');

// // THE USER IS ABLE TO PERFORM THE LOGIN ACTION
// else:
//     try{
//         $fetch_user_by_email = "SELECT * FROM `user` WHERE `email`=:email";
//         $query_stmt = $conn->prepare($fetch_user_by_email);
//         $query_stmt->bindValue(':email', $email,PDO::PARAM_STR);
//         $query_stmt->execute();

//         // IF THE USER IS FOUNDED BY EMAIL
//         if($query_stmt->rowCount()):
//             $row = $query_stmt->fetch(PDO::FETCH_ASSOC);
//             $check_password = password_verify($password, $row['password']);

//             // VERIFYING THE PASSWORD (IS CORRECT OR NOT?)
//             // IF PASSWORD IS CORRECT THEN SEND THE LOGIN TOKEN
//             if($check_password):

//                 $jwt = new JwtHandler();
//                 $token = $jwt->_jwt_encode_data(
//                     'http://localhost/php_jwt/',
//                     array("user_ID"=> $row['user_ID'])
//                 );

//                 $returnData = [
//                     'success' => 1,
//                     'message' => 'You have successfully logged in.',
//                     'token' => $token
//                 ];

//             // IF INVALID PASSWORD
//             else:
//                 $returnData = msg(0,422,'Invalid Password!');
//             endif;

//         // IF THE USER IS NOT FOUNDED BY EMAIL THEN SHOW THE FOLLOWING ERROR
//         else:
//             $returnData = msg(0,422,'Invalid Email Address!');
//         endif;
//     }
//     catch(PDOException $e){
//         $returnData = msg(0,500,$e->getMessage());
//     }

// endif;

endif;

echo json_encode($returnData);
