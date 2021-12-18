<?php
header('Content-Type: application/json');
echo "testing\n";

$data = array();

echo "index 3 value:" . $data[3];

if(!isset($data[3]))
echo "\nthe value is null";

$test = $data[3];

if(!isset($test))
echo "\nthe test value is null";

$tt = null;

echo "\ntt's value = " . $tt . "\n";

$data["ttt"] = array();

if(empty($data["ttt"]))
    echo "the array is empty\n";

$data["ttt"] = "whatttt";

if(empty($data["ttt"]))
    echo "the array is empty\n";
else
    echo "the array is not empty\n"
?>