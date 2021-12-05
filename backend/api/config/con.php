<?php
/**
 * 查詢docker container IP
 * docker inspect container_ID | grep "IPAddress"
 */
require 'login.php';
$mysql_host = "172.19.0.2"; // mariaDB's IP
$mysql_username = "root";   // 設定連接資料庫用戶帳號
$mysql_password = "root";   // 設定連接資料庫用戶的密碼
$mysql_database = "ttt";   // 設成你在 mysql 創的資料庫
$mysql_portnumber = "3306"; // port number
$DAO = new DatabaseAccessObject($mysql_host, $mysql_username, $mysql_password, $mysql_database, $mysql_portnumber);
// 要新增資料就
$table = "Users"; // 設定你想新增資料的資料表
$data_array['name'] = "金城武";
$data_array['userID'] = 857146;
// $data_array['hero_mp'] = 80;
$DAO->insert($table, $data_array);
//$User_id = $DAO->getLastId; // 可以拿到他自動建立的 id
// 這樣就完成新增動作了

// 想要查詢的話
$table = "Users"; // 設定你想查詢資料的資料表
$condition = "name = '金城武'";
$hero = $DAO->query($table, $condition, $order_by = "1", $fields = "*", $limit = "");
// 這樣寫等同於下面直接呼叫的語法
$hero = $DAO->execute("SELECT * FROM Users");
print_r($hero); // 可以印出來看看

// 那想修改資料呢？
$table = "hero";
$data_array['hero_name'] = "凡恩ATM"; // 想改他的名字
$key_column = "hero_id"; //
$id = $hero_id; // 根據我們剛剛上面拿到的 hero ID
$DAO->update($table, $data_array, $key_column, $id);
echo $DAO->getLastSql; // 想知道會轉換成什麼語法 可以印出來看看

// 最後的刪除也不難，告訴他條件就可以了
$table = "hero";
$key_column = "hero_id";
$id = 1; // 我們假設要刪除 hero_id = 1 的英雄
$DAO->delete($table, $key_column, $id); 
// 一行搞定
?>