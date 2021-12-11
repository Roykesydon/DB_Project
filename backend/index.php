<?php
header('Content-Type: application/json');
echo "testing\n";

$data = array();

$tt = null;

echo "tt's value = " . $tt . "\n";

$data["ttt"] = array();

if(empty($data["ttt"]))
    echo "the array is empty\n";

$data["ttt"] = "whatttt";

if(empty($data["ttt"]))
    echo "the array is empty\n";
else
    echo "the array is not empty\n"
?>