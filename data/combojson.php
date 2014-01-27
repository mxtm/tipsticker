<?php
require("functions.php");

header("Content-type: application/json");

$output = array();
$output['lastupdated'] = time();
$output['difficulty'] = difficulty();
$output['ltcusdprice'] = ltcusdprice();
$output['tipsltcsatprice'] = tipsltcsatprice();
$output['tipsusdprice'] = tipsusdprice();
$output['tipsusdpricestr'] = number_format(tipsusdprice(), 9);
$outputjson = json_encode($output);

echo $outputjson;
?>
