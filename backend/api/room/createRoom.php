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

require_once __ROOT__.'/objects/roomPicture.php';

require_once __ROOT__.'/objects/roomQueue.php';

require_once __ROOT__.'/objects/roomService.php';

require_once __ROOT__.'/middleware/auth.php';



// instantiate database and rentRoom object
$database = new Database();
$db=$database->getConnection();
// initialize object
$rentRoom = new RentRoom($db);
$roomPicture = new RoomPicture($db);
$roomQueue = new RoomQueue($db);
$roomService = new RoomService($db);


//instantiate authentication objects
$allHeaders = getallheaders();
$auth = new Auth($db,$allHeaders);
// echo $auth->getSecret();
$returnData = [
    "success" => 0,
    "status" => 401,
    "message" => "Unauthorized"
];

$cityList = array("新北市","臺北市","桃園市","臺中市","臺南市","高雄市","新竹縣","苗栗縣","彰化縣","南投縣","雲林縣","嘉義縣","屏東縣","宜蘭縣","花蓮縣","臺東縣","澎湖縣","金門縣","連江縣","基隆市","新竹市","嘉義市");
// $tagList = array("Wi-Fi","有線網路","電視","冰箱","停車位","冷氣","洗衣機","開伙","養寵物","電梯");


// IF REQUEST METHOD IS NOT EQUAL TO POST
if ($_SERVER["REQUEST_METHOD"] != "POST") 
{
    echo array("success" => 0 , "status" => 404,"message" => "Page Not Found!");
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
        //room owner's user_ID
        // $user_ID = $data["user_ID"];
        // $user_ID = $returnData['user']['user_ID'];

        //Authentication

        # 取得上傳檔案數量
        $fileCount = count($_FILES['file1']['name']);

        // if(strcmp($thisUser,$user_ID) == 0)
        // {   

        if( //check value not null
            !empty($data["room_name"]) && 
            !empty($data["address"]) &&
            !empty($data["cost"]) &&
            !empty($data["room_latitude"]) &&
            !empty($data["room_longtitude"]) && 
            !empty($data["room_city"]) &&
            $fileCount > 0)
        {
            // echo mb_strlen($data["room_name"],'utf-8');
            // echo mb_strlen($data["address"],'utf-8');
            // echo mb_strlen($data["room_city"],'utf-8');
            if(mb_strlen($data["room_name"],'utf-8') < 1 || mb_strlen($data["room_name"],'utf-8') > 25)
                echo json_encode(array("success" => 0,"message" => "Room_name must be >= 1 and <= 25."));
            else if(mb_strlen($data["address"],'utf-8') < 1 || mb_strlen($data["address"],'utf-8') > 50)
                echo json_encode(array("success" => 0,"message" => "Address must be >= 1 and <= 50."));
            else if($data["cost"] < 0)
                echo json_encode(array("success" => 0,"message" => "The cost must be positive!"));
            else if(!in_array($data["room_city"],$cityList))
                echo json_encode(array("success" => 0,"message" => "City must be in citylist."));
            else if($data["room_latitude"] < -90.0 || $data["room_latitude"] > 90.0)
                echo json_encode(array("success" => 0,"message" => "Latitude must be > -90.0 and < 90.0"));
            else if($data["room_longtitude"] < -180.0 || $data["room_longtitude"] > 180.0)
                echo json_encode(array("success" => 0,"message" => "Longtitude must be > -180.0 and < 180.0"));
            else{
                
                //create room

                //initiate the services
                $service = array(
                    "Wi-Fi" => 0,
                    "有線網路" => 0,
                    "電視" => 0,
                    "冰箱" => 0,
                    "停車位" => 0,
                    "冷氣" => 0,
                    "洗衣機" => 0,
                    "開伙" => 0,
                    "養寵物" => 0,
                    "電梯" => 0,
                );
                // echo "tag count : " . count($data["tag"]);
                //set the services
                for($i=0;$i < count($data["tag"]);$i++)
                {
                    $service[$data["tag"][$i]] = 1;
                }

                // echo var_dump($service);

                //initiate roomPicture 
                $roomPictureURLs = array();
                //save the file name that is too long
                $nameTooLong = array();
                //set roomPictureURLs
                for($i=0;$i<count($data["files"]);$i++)
                {
                    if(mb_strlen($data["files"][$i],'utf-8') > 300)
                    {
                        echo json_encode(array("success" => 0,"message" => "Unable to create Picture" . $data["files"][$i] . ".Because the picture file name is too long.")) . "\n";
                        array_push($nameTooLong,$data["files"][$i]);
                    }
                    else
                    {
                        array_push($roomPictureURLs,$data["files"][$i]);
                    }
                }


                //set room property values
                // $rentRoom->room_ID = $room_ID;
                $rentRoom->user_ID = $thisUser;
                $rentRoom->room_name = $data["room_name"];
                $rentRoom->address = $data["address"];
                $rentRoom->cost = $data["cost"];
                $rentRoom->room_info = $data["room_info"];
                $rentRoom->room_latitude = $data["room_latitude"];
                $rentRoom->room_longitude = $data["room_longtitude"];
                $rentRoom->room_city = $data["room_city"];
                $rentRoom->post_date = $data["post_date"];
                $rentRoom->live_number = $data["live_number"];
                $rentRoom->room_area = $data["room_area"];

                // echo var_dump($rentRoom);


                //Initiates the transaction
                $db->beginTransaction();

                //put data into database
                //create the room
                try{

                    if($rentRoom->createRoom())
                    {
                        // set response code - 201 created
                        http_response_code(201);
                
                        // tell the user
                        echo json_encode(array("success" => 1,"message" => "Roominfo was created."));
                    }
                    // echo "last insert ID: " . $db->lastInsertId() . "\n";
                    $room_ID = $db->lastInsertId();

                    //create the roomPicture

                    //set roomPicture property values
                    $roomPicture->room_ID = $room_ID;
                    $roomPicture->pictureURL_one = $roomPictureURLs[0];
                    $roomPicture->pictureURL_two = $roomPictureURLs[1];
                    $roomPicture->pictureURL_three = $roomPictureURLs[2];
                    $roomPicture->pictureURL_four = $roomPictureURLs[3];
                    $roomPicture->pictureURL_five = $roomPictureURLs[4];
                    $roomPicture->pictureURL_six = $roomPictureURLs[5];
                    $roomPicture->pictureURL_seven = $roomPictureURLs[6];
                    $roomPicture->pictureURL_eight = $roomPictureURLs[7];

                    if($roomPicture->createRoomPicture())
                    {
                        // set response code - 201 created
                        http_response_code(201);
                    
                        // tell the user
                        echo json_encode(array("success" => 1,"message" => "RoomPicture was created.")) . "\n";
                    }

                    //create the roomQueue

                    $roomQueue->room_ID = $room_ID;
                    $roomQueue->user_ID_one = $data["user_ID_one"];
                    $roomQueue->user_ID_two = $data["user_ID_two"];
                    $roomQueue->user_ID_three = $data["user_ID_three"];
                    $roomQueue->user_ID_four = $data["user_ID_four"];
                    $roomQueue->user_ID_five = $data["user_ID_five"];
                    $roomQueue->user_ID_six = $data["user_ID_six"];
                    $roomQueue->user_ID_seven = $data["user_ID_seven"];
                    $roomQueue->user_ID_eight = $data["user_ID_eight"];
                    $roomQueue->user_ID_nine = $data["user_ID_nine"];
                    $roomQueue->user_ID_ten = $data["user_ID_ten"];

                    if($roomQueue->createRoomQueue())
                    {
                        // set response code - 201 created
                        http_response_code(201);
                
                        // tell the user
                        echo json_encode(array("success" => 1,"message" => "roomQueue was created.")) . "\n";
                    }

                    //create the roomService

                    //set roomService property values
                    $roomService->room_ID = $room_ID;
                    $roomService->wifi = $service["Wi-Fi"];
                    $roomService->internet = $service["有線網路"];
                    $roomService->tv = $service["電視"];
                    $roomService->refrigerator = $service["冰箱"];
                    $roomService->parking = $service["停車位"];
                    $roomService->ac = $service["冷氣"];
                    $roomService->washing_machine = $service["洗衣機"];
                    $roomService->can_cooking = $service["開伙"];
                    $roomService->can_keep_pet = $service["養寵物"];
                    $roomService->elevator = $service["電梯"];
                    if($roomService->createRoomService())
                    {
                        // set response code - 201 created
                        http_response_code(201);
        
                        // tell the user
                        echo json_encode(array("success" => 1,"message" => "roomService was created.")) . "\n";
                    }

                    //上傳圖片檔到server端
                    for ($i = 0; $i < $fileCount; $i++) {
                        # 檢查檔案是否上傳成功
                        if ($_FILES['file1']['error'][$i] === UPLOAD_ERR_OK)
                        {
                            // echo '檔案名稱: ' . $_FILES['file1']['name'][$i] . ".\n";
                            // echo '檔案類型: ' . $_FILES['file1']['type'][$i] . ".\n";
                            // echo '檔案大小: ' . ($_FILES['file1']['size'][$i] / 1024) . "KB\n";
                            // echo '暫存名稱: ' . $_FILES['file1']['tmp_name'][$i] . ".\n";
                    
                            $uploaddir = dirname(dirname(dirname(__FILE__))) . "/files/roomImages/";
                        
                            # 檢查檔案是否已經存在
                            if (file_exists($uploaddir . $_FILES['file1']['name'][$i]))
                            {
                                echo json_encode(array("success" => 0,"message" => $_FILES['file1']['name'][$i] . " has already existed.")) . "\n";
                                // $returnData = msg(0, 422, $_POST['user_ID'] . "file has already existed");
                            }else if(in_array($_FILES['file1']['name'][$i],$nameTooLong)) 
                            {
                                echo json_encode(array("success" => 0,"message" => $_FILES['file1']['name'][$i] . "'s file name is too long to be stored in database.")) . "\n";
                            }
                            else {
                                $file = $_FILES['file1']['tmp_name'][$i];
                                $dest = $uploaddir . $_FILES['file1']['name'][$i];
                        
                                # 將檔案移至指定位置
                                move_uploaded_file($file, $dest);
                                echo json_encode(array("success" => 1,"message" => $thisUser . "File successfully uploaded.")) . "\n";
                                // $returnData = msg(1, 201, $_POST['user_ID'] . "File successfully uploaded.\n");
                            }
                        } else {
                            echo json_encode(array("success" => 0,"message" => $thisUser . "failed upload.")) . "\n";
                        }

                    }

                    //commit the transaction
                    $db->commit();

                }catch(PDOException $e)
                {
                    http_response_code(503);
                    echo $e->getMessage() . "\n";
                    echo json_encode(array("success" => 0,"message" => "Unable to create whole room.")) . "\n";
                    //Rolls back the transaction
                    $db->rollBack();
                }

            }
        }
        else
        {
            // set response code - 400 bad request
            http_response_code(400);
        
            // tell the user
            if(empty($data["room_name"]))
                echo json_encode(array("success" => 0,"message" => "Unable to create room. Room_name is empty."));
            else if(empty($data["address"]))
                echo json_encode(array("success" => 0,"message" => "Unable to create room. Address is empty."));
            else if(empty($data["cost"]))
                echo json_encode(array("success" => 0,"message" => "Unable to create room. Cost is empty."));
            else if(empty($data["room_latitude"]))
                echo json_encode(array("success" => 0,"message" => "Unable to create room. Room_latitude is empty."));
            else if(empty($data["room_longtitude"]))
                echo json_encode(array("success" => 0,"message" => "Unable to create room. Room_longtitude is empty."));
            else if(empty($data["room_city"]))
                echo json_encode(array("success" => 0,"message" => "Unable to create room. Room_city is empty."));
            else if($fileCount < 0)
                echo json_encode(array("success" => 0,"message" => "Unable to create room. File is empty."));
        }
    }else{
        echo "invalid token\n";
    }
}
//close the database connection
$db = null;
