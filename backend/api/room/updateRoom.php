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
$returnData = [
    "success" => 0,
    "status" => 401,
    "message" => "Unauthorized"
];

$cityList = array("新北市","臺北市","桃園市","臺中市","臺南市","高雄市","新竹縣","苗栗縣","彰化縣","南投縣","雲林縣","嘉義縣","屏東縣","宜蘭縣","花蓮縣","臺東縣","澎湖縣","金門縣","連江縣","基隆市","新竹市","嘉義市");
// $tagList = array("Wi-Fi","有線網路","電視","冰箱","停車位","冷氣","洗衣機","開伙","養寵物","電梯");

function random_filename($length, $directory = '', $extension = '')
{
    // default to this files directory if empty...
    $dir = !empty($directory) && is_dir($directory) ? $directory : dirname(__FILE__);

    do {
        $key = '';
        $keys = array_merge(range(0, 9), range('a', 'z'));

        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }
    } while (file_exists($dir . '/' . $key . (!empty($extension) ? '.' . $extension : '')));

    return $key . (!empty($extension) ? '.' . $extension : '');
}

$fileNameArr = array();
//old URLs
$URLs = array();

// IF REQUEST METHOD IS NOT EQUAL TO POST
if ($_SERVER["REQUEST_METHOD"] != "POST") 
{
    // http_response_code(200);
    echo json_encode(array("success" => 0 , "status" => 404,"message" => "Page Not Found!"));
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
        $room_ID = $_POST['room_ID'];

        //get the room User's user_ID
        $query = "SELECT `user_ID` FROM `rentRoom` WHERE `room_ID` = ?;";
        $stmt = $db->prepare($query);

        $stmt->bindParam(1,$room_ID);
        $stmt->execute();

        $checkUserID = $stmt->fetch();
        $roomUser = $checkUserID['user_ID'];
        //Authentication
        //確定目前的房間是不是這個User的
        if(!strcmp($thisUser,$roomUser))
        {
            # 取得上傳檔案數量
            $fileCount = count($_FILES['file1']['name']);
            // echo "fileCount: " . $fileCount;
            // echo var_dump($_FILES);

            // if(strcmp($thisUser,$user_ID) == 0)
            // {   

            if( //check value not null
                !empty($data["room_name"]) && 
                !empty($data["address"]) &&
                !empty($data["cost"]) &&
                !empty($data["room_latitude"]) &&
                !empty($data["room_longitude"]) && 
                !empty($data["room_city"]) &&
                $fileCount < 9)
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
                else if($data["room_longitude"] < -180.0 || $data["room_longitude"] > 180.0)
                    echo json_encode(array("success" => 0,"message" => "Longitude must be > -180.0 and < 180.0"));
                else{
                    
                    //update room
                    //initiate the update services
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
                    //set the update services.
                    $data["tag"]=explode(",",$data["tag"]);
                    for($i=0;$i < count($data["tag"]);$i++)
                    {
                        $service[$data["tag"][$i]] = 1;
                    }

                    // echo var_dump($service);

                    //initiate update roomPictureURL array 
                    $roomPictureURLs = array();
                    //set update roomPictureURLs
                    for($i=0;$i<$fileCount;$i++)
                    {
                        $fileType = end(explode('.', $_FILES['file1']['name'][$i]));
                        $tempFileName = random_filename(150,'',$fileType);
                        array_push($fileNameArr,$tempFileName);
                        array_push($roomPictureURLs,$tempFileName);
                    }


                    //set room property values
                    // $rentRoom->room_ID = $room_ID;
                    $rentRoom->room_ID = $room_ID;
                    $rentRoom->room_name = $data["room_name"];
                    $rentRoom->address = $data["address"];
                    $rentRoom->cost = $data["cost"];
                    $rentRoom->room_info = $data["room_info"];
                    $rentRoom->room_latitude = $data["room_latitude"];
                    $rentRoom->room_longitude = $data["room_longitude"];
                    $rentRoom->room_city = $data["room_city"];
                    $rentRoom->post_date = $data["post_date"];
                    $rentRoom->live_number = $data["live_number"];
                    $rentRoom->room_area = $data["room_area"];

                    // echo var_dump($rentRoom);


                    //Initiates the transaction
                    $db->beginTransaction();

                    //put data into database
                    //update the room
                    try{

                        if($rentRoom->updateRoom())
                        {
                            // set response code - 201 created
                            http_response_code(201);
                        }
                        // echo "updateRoom succ\n";

                        //update the roomPicture
                        if($fileCount != 0)
                        {
                            $baseURL = "../../files/roomImages/" . $roomUser . "/";
                            //delete the old pictures
                            $query = "SELECT * FROM `roomPicture` WHERE `room_ID` = ?;";
                            $stmt = $db->prepare($query);
                            $stmt->bindParam(1,$room_ID);
                            $stmt->execute();
                            $row = $stmt->fetch(PDO::FETCH_ASSOC);

                            array_push($URLs,$row["pictureURL_one"],$row["pictureURL_two"],$row["pictureURL_three"],$row["pictureURL_four"],$row["pictureURL_five"],$row["pictureURL_six"],$row["pictureURL_seven"],$row["pictureURL_eight"]);
                            //delete null value
                            $URLs = array_filter($URLs);
                            for($i = 0;$i<count($URLs);$i++)
                                $URLs[$i] =  $baseURL . $URLs[$i];
                            for($i = 0;$i < count($URLs);$i++)
                                unlink($URLs[$i]);
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

                            if($roomPicture->updateRoomPicture())
                            {
                                // set response code - 201 created
                                http_response_code(201);
                            }
                        }
                        // echo "updatePic succ\n";
                        //update the roomService

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
                        if($roomService->updateRoomService())
                        {
                            // set response code - 201 created
                            http_response_code(201);
                        }
                        // echo "updateService succ\n";
                        if($fileCount != 0)
                        {
                            //上傳圖片檔到server端
                            for ($i = 0; $i < $fileCount; $i++) {
                                # 檢查檔案是否上傳成功
                                if ($_FILES['file1']['error'][$i] === UPLOAD_ERR_OK)
                                {
                                    $uploaddir = "../../files/roomImages/" . $roomUser;
                        
                                    move_uploaded_file($_FILES['file1']['tmp_name'][$i], $uploaddir . "/" . $fileNameArr[$i]);
                                
                                } else {
                                    // echo json_encode(array("success" => 0,"message" => $thisUser . "failed upload."));
                                    throw new PDOException("failed upload.");
                                }
                            }
                        }
                        // echo "upload files succ\n";
                        //commit the transaction
                        $db->commit();
                        // tell the user
                        echo json_encode(array("success" => 1,"message" => "Room is successfully updated."));
                    }catch(PDOException $e)
                    {
                        //error
                        // http_response_code(503);
                        // echo $e->getMessage() . "\n";
                        echo json_encode(array("success" => 0,"message" => "Unable to update whole room. Because " . $e->getMessage()));
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
                    echo json_encode(array("success" => 0,"message" => "Unable to update room. Room_name is empty."));
                else if(empty($data["address"]))
                    echo json_encode(array("success" => 0,"message" => "Unable to update room. Address is empty."));
                else if(empty($data["cost"]))
                    echo json_encode(array("success" => 0,"message" => "Unable to update room. Cost is empty or zero."));
                else if(empty($data["room_latitude"]))
                    echo json_encode(array("success" => 0,"message" => "Unable to update room. Room_latitude is empty."));
                else if(empty($data["room_longitude"]))
                    echo json_encode(array("success" => 0,"message" => "Unable to update room. Room_longitude is empty."));
                else if(empty($data["room_city"]))
                    echo json_encode(array("success" => 0,"message" => "Unable to update room. Room_city is empty."));
                else if($fileCount >= 9)
                    echo json_encode(array("success" => 0,"message" => "Unable to update room. fileCount must < 9."));
            }
        }else{
            http_response_code(403);
            echo json_encode(array("success" => 0,"message" => "This room isn't yours or room_ID is empty."));
        }
    }else{
        http_response_code(403);
        echo json_encode(array("success" => 0,"message" => "invalid token."));
    }
}
//close the database connection
$db = null;
?>
