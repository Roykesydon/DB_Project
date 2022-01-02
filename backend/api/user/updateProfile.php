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

require_once __ROOT__.'/objects/user.php';

require_once __ROOT__.'/middleware/auth.php';

// instantiate database and rentRoom object
$database = new Database();
$db=$database->getConnection();
// initialize object
$User = new User($db);

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
$returnData = [
    "success" => 0,
    "status" => 401,
    "message" => "Unauthorized"
];
$returnMsg = [
    "success" => 0,
    "status" => 401,
    "message" => "Unauthorized"
];

// IF REQUEST METHOD IS NOT EQUAL TO POST
if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") 
{
    http_response_code(200);
    $returnMsg = msg(0,200,"options");
}
else if ($_SERVER["REQUEST_METHOD"] != "POST") 
{
    http_response_code(404);
    $returnMsg = msg(0,404,"Page Not Found!");
}
else
{
    //create data array
    // $data = json_decode(file_get_contents("php://input"),true);
    $data = $_POST;
    
    if($auth->isAuth())
    {
        // enter here if is log in
        $returnData = $auth->isAuth();
        // echo "以".$returnData['user']['user_ID']."登入\n";
        // write code below

        //now User's user_ID
        $thisUser = $returnData['user']['user_ID'];
        # 取得上傳檔案數量
        $fileCount = count($_FILES['file']['name']);

        if(!empty($data["user_name"]) &&
           !empty($data["email"]) &&
           !empty($data["phoneNumber"]))
        {
            // check valid input
            if(!filter_var($data["email"], FILTER_VALIDATE_EMAIL))
                $returnMsg = msg(0,422,'Invalid Email Address!');
            else if(mb_strlen($data["user_name"],'utf-8') < 3 || mb_strlen($data["user_name"],'utf-8') > 30)
                $returnMsg = msg(0,422,'name must be <= 30 and >= 3');
            else if(strlen($data["phoneNumber"]) < 10 || strlen($data["phoneNumber"]) > 25)
                $returnMsg = msg(0,422,'phone number must be <= 25 and >= 10');
            else{
                // write into DB
                //check repeat email
                try{
                    $check_email_query = "SELECT `email` FROM `user` WHERE `email`=:email;";
                    $check_email_stmt = $db->prepare($check_email_query);
                    $check_email_stmt->bindValue(':email', $data["email"],PDO::PARAM_STR);
                    $check_email_stmt->execute();
                    $check_email = $check_email_stmt->fetch(PDO::FETCH_ASSOC);

                    $now_email_query = "SELECT `email` FROM `user` WHERE `user_ID`= ?;";
                    $now_email_stmt = $db->prepare($now_email_query);
                    $now_email_stmt->bindParam(1,$thisUser);
                    $now_email_stmt->execute();
                    $now_email = $now_email_stmt->fetch(PDO::FETCH_ASSOC);
                // }catch(PDOException $e)
                // {
                //     echo $e->getMessage();
                // }
                    if(strcmp($check_email['email'],$now_email['email']) != 0 && $check_email_stmt->rowCount())
                        $returnMsg = msg(0,422, 'This E-mail already in use!');
                    else
                    {
                        $User->email = $data["email"];
                        $User->user_name = $data["user_name"];
                        $User->phone_number = $data["phoneNumber"];
                        $User->user_ID = $thisUser;

                        //Initiates the transaction
                        $db->beginTransaction();

                        // try{
                        if($User->updateProfile())
                        {
                            // set response code - 200 success
                            http_response_code(200);
                        }
                        //upload Picture
                        if($fileCount == 1)
                        {
                            $uploaddir = "../../files/profileImages/" . $thisUser . "/";
                            // // $target_file = $target_dir . basename($_FILES["unknown.png"]["name"]);
                            // // unknown.png
                            if (file_exists($uploaddir)) 
                                deleteDir('../../files/profileImages/'.$thisUser.'/');

                            mkdir('../../files/profileImages/'.$thisUser, 0777);
                            
                            $uploadfile = $uploaddir . $thisUser .".". end(explode('.', $_FILES["file"]['name']));
                            if (move_uploaded_file($_FILES["file"]['tmp_name'], $uploadfile)) {
                                //     // if (getimagesize($_FILES["file"]["tmp_name"])) {
                                $returnMsg = msg(1, 200, "success");
                            } else {
                                throw new PDOException("failed upload.");
                            }
                        }
                        //commit the transaction
                        $db->commit();
                        // set response code - 200 OK
                        http_response_code(200);
                        $returnMsg = msg(1,200,"Update User profile success!");
                    // }catch(PDOException $e)
                    // {
                    //     //error
                    //     http_response_code(503);
                    //     // echo $e->getMessage() . "\n";
                    //     $returnMsg = msg(0,503,"Unable to update User profile. Because " . $e->getMessage());
                    //     // echo json_encode(array("success" => 0,"message" => "Unable to create whole room. Because " . $e->getMessage()));
                    //     //Rolls back the transaction
                    //     $db->rollBack();
                    // }
                    }
                }catch(PDOException $e)
                {
                    //error
                    http_response_code(503);
                    // echo $e->getMessage() . "\n";
                    $returnMsg = msg(0,503,"Unable to update User profile. Because " . $e->getMessage());
                    // echo json_encode(array("success" => 0,"message" => "Unable to create whole room. Because " . $e->getMessage()));
                    //Rolls back the transaction
                    $db->rollBack();
                }
            }
        }else{
            // set response code - 422 bad request
            http_response_code(422);
        
            // tell the user
            if(empty($data["user_name"]))
                $returnMsg = msg(0,422,"Unable to update User profile. Because user_name is empty");
            else if(empty($data["email"]))
                $returnMsg = msg(0,422,"Unable to update User profile. Because email is empty");
            else if(empty($data["phoneNumber"]))
                $returnMsg = msg(0,422,"Unable to update User profile. Because phoneNumber is empty");
        }
        
    }else{
        http_response_code(403);
        $returnMsg = msg(0,403,"invalid token.");
    }
}
echo json_encode($returnMsg);
//close the database connection
$db = null;
?>
