<?php
header('Content-Type: application/json');
echo "testing\n";

$data = array();

echo PHP_EOL;
echo   substr(readfile("../../env"), strrpos(readfile("../../env"), '=') + 1).PHP_EOL.PHP_EOL;
print_r(explode('=',file_get_contents("/var/www/html/env"))[1].PHP_EOL);
print_r(gettype(explode('=',file_get_contents("/var/www/html/env"))[1]));
print_r(gettype("123456"));

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