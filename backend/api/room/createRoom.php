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


// IF REQUEST METHOD IS NOT EQUAL TO POST
if ($_SERVER["REQUEST_METHOD"] != "POST") 
{
    echo array("success" => 0 , "status" => 404,"message" => "Page Not Found!");
}
else
{

    //create data array
    $data = json_decode(file_get_contents("php://input"),true);

    // echo "Test Auth\n";

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

        //calculate the room_ID
        $userRoomCount = "SELECT COUNT(DISTINCT `room_ID`) as `roomCount` FROM `rentRoom`;";
        $userRoomCountStmt = $db->prepare($userRoomCount);
        $userRoomCountStmt->execute();

        $row = $userRoomCountStmt->fetch(PDO::FETCH_ASSOC);
        if($row)
        {
            $count = $row['roomCount'] + 1;
            $room_ID = $count;

            // echo "\ncalculate room ID\n";
            $allUserRoomID = "SELECT `room_ID` FROM `rentRoom`;";
            $allUserRoomIDStmt = $db->prepare($allUserRoomID);
            $allUserRoomIDStmt->execute();


            $allUserRoomIDResult = $allUserRoomIDStmt->fetchAll(PDO::FETCH_ASSOC);
            $allRoomIDArray = array(
                "room_ID" => array()
            );

            for($i=0 ; $i<count($allUserRoomIDResult) ; $i++)
            {
                $allRoomIDArray["room_ID"][$i] = $allUserRoomIDResult[$i]['room_ID'];
            }
            while(in_array($room_ID,$allRoomIDArray['room_ID']))
            {
                // echo "enter count while loop\n";
                $count = $count + 1;
                $room_ID = $count;
            }
        }
        else
        {
            // $room_ID = $thisUser . "_1";
            $room_ID = 1;
        }

        // echo "The room_ID is: " . $room_ID . ".\n";
        //check whether the room has created
        $checkRoom = "SELECT `room_ID` FROM `rentRoom` WHERE `room_ID` = ?;";
        $checkRoomStmt = $db->prepare($checkRoom);
        $checkRoomStmt->bindValue(1,$room_ID);
        $checkRoomStmt->execute();

        //check whether the room has created
        if($checkRoomStmt->rowCount() > 0)
        {
            echo json_encode(array("message" => "Unable to create room.Because the room has created."));
        }
        else{
            //Authentication

            # 取得上傳檔案數量
            $fileCount = count($_FILES['file1']['name']);

            // if(strcmp($thisUser,$user_ID) == 0)
            // {   

            if( //check not null
                !empty($data["room_name"]) && 
                !empty($data["address"]) &&
                !empty($data["cost"]) &&
                !empty($data["room_latitude"]) &&
                !empty($data["room_longtitude"]) && 
                !empty($data["room_city"]) &&
                $fileCount > 0)
            {
                if(strlen($data["room_name"]) < 2 || strlen($data["room_name"]) > 25)
                    echo json_encode(array("message" => "Room_name must be >= 2 and <= 25.\n"));
                else if(strlen($data["address"]) < 6 || strlen($data["address"]) > 50)
                    echo json_encode(array("message" => "Address must be >= 6 and <= 50.\n"));
                else if($data["cost"] < 0)
                    echo json_encode(array("message" => "The cost must be positive!\n"));
                else if(strlen($data["room_city"]) < 2 || strlen($data["room_city"]) > 20)
                    echo json_encode(array("message" => "City must be >= 2 and <= 20.\n"));
                else{
                    
                    //create room

                    //initiate the services
                    $service = array(
                        "wifi" => 0,
                        "internet" => 0,
                        "tv" => 0,
                        "refrigerator" => 0,
                        "parking" => 0,
                        "ac" => 0,
                        "washing_machine" => 0,
                        "can_cooking" => 0,
                        "can_keep_pet" => 0,
                        "elevator" => 0,
                    );
                    //set the services
                    // echo "tag count : " . count($data["tag"]);
                    for($i=0;$i < count($data["tag"]);$i++)
                    {
                        $service[$data["tag"][$i]] = 1;
                    }

                    // echo var_dump($service);

                    //initiate roomPicture 
                    $roomPictureURLs = array();
                    //set roomPictureURLs
                    for($i=0;$i<count($data["files"]);$i++)
                    {
                        if($data["files"][$i] > 300)
                        {
                            echo json_encode(array("message" => "Unable to create Picture" . $data["files"][$i] . ".Because the picture file name is too long.\n"));
                        }
                        else
                        {
                            array_push($roomPictureURLs,$data["files"][$i]);
                        }
                    }


                    //set room property values
                    $rentRoom->room_ID = $room_ID;
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

                    //set roomQueue property values
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

                    //set roomService property values
                    $roomService->room_ID = $room_ID;
                    $roomService->wifi = $service["wifi"];
                    $roomService->internet = $service["internet"];
                    $roomService->tv = $service["tv"];
                    $roomService->refrigerator = $service["refrigerator"];
                    $roomService->parking = $service["parking"];
                    $roomService->ac = $service["ac"];
                    $roomService->washing_machine = $service["washing_machine"];
                    $roomService->can_cooking = $service["can_cooking"];
                    $roomService->can_keep_pet = $service["can_keep_pet"];
                    $roomService->elevator = $service["elevator"];

                    //put data into database

                    //create the room
                    if($rentRoom->createRoom())
                    {
                        // set response code - 201 created
                        http_response_code(201);
                
                        // tell the user
                        echo json_encode(array("message" => "Room was created."));
                    }
                    // if unable to create the room, tell the user
                    else
                    {
                        // set response code - 503 service unavailable
                        http_response_code(503);
                
                        // tell the user
                        echo json_encode(array("message" => "Unable to create room."));
                    }
                    echo "createRoom OK!\n";

                    //create the roomPicture
                    if($roomPicture->createRoomPicture())
                    {
                        // set response code - 201 created
                        http_response_code(201);
                    
                        // tell the user
                        echo json_encode(array("message" => "RoomPicture was created."));
                    }
                    // if unable to create the Picture, tell the user
                    else
                    {
                        // set response code - 503 service unavailable
                        http_response_code(503);
            
                        // tell the user
                        echo json_encode(array("message" => "Unable to create roomPicture."));
                    }

                    //create the roomQueue
                    if($roomQueue->createRoomQueue())
                    {
                        // set response code - 201 created
                        http_response_code(201);
                
                        // tell the user
                        echo json_encode(array("message" => "roomQueue was created."));
                    }
                    // if unable to create the roomQueue
                    else
                    {
                        // set response code - 503 service unavailable
                        http_response_code(503);
                
                        // tell the user
                        echo json_encode(array("message" => "Unable to create roomQueue."));
                    }

                    //create the roomService
                    if($roomService->createRoomService())
                    {
                        // set response code - 201 created
                        http_response_code(201);
        
                        // tell the user
                        echo json_encode(array("message" => "roomService was created."));
                    }
                    // if unable to create the roomService
                    else
                    {
                        // set response code - 503 service unavailable
                        http_response_code(503);
        
                        // tell the user
                        echo json_encode(array("message" => "Unable to create roomService."));
                    }

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
                            echo json_encode(array("message" => "file has already existed."));
                            // $returnData = msg(0, 422, $_POST['user_ID'] . "file has already existed");
                        } else {
                            $file = $_FILES['file1']['tmp_name'][$i];
                            $dest = $uploaddir . $_FILES['file1']['name'][$i];
                    
                            # 將檔案移至指定位置
                            move_uploaded_file($file, $dest);
                            echo json_encode(array("message" => $thisUser . "File successfully uploaded."));
                            // $returnData = msg(1, 201, $_POST['user_ID'] . "File successfully uploaded.\n");
                        }
                        } else {
                            echo json_encode(array("message" => $thisUser . "failed upload."));
                        }

                    }
                }
            }
            else
            {
                // set response code - 400 bad request
                http_response_code(400);
            
                // tell the user
                echo json_encode(array("message" => "Unable to create room. Data is incomplete."));
            }
            // }else{
            //     echo json_encode(array("message" => "You can't create this room!!!"));
            // }
        }
    }else{
        echo "invalid token\n";
    }
}

?>