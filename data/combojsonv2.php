<?php
require("functionsv2.php");

header("Content-type: application/json");

$output = array();
$output['difficulty'] = difficulty();
$output['ltcusdprice'] = ltcusdprice();
$output['cryptsyltcsatprice'] = cryptsyltcsatprice();
$output['coinedupltcsatprice'] = coinedupltcsatprice();
$output['avgltcsatprice'] = avgltcsatprice();
$output['tipsusdprice'] = tipsusdprice();
$output['tipsusdpricestr'] = number_format(tipsusdprice(), 9);
$outputjson = json_encode($output);

echo $outputjson;
?>
