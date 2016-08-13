<?php
require("connect.php");

$yzm = $_GET['yzm'];
$tel = $_GET['tel'];
$type = $_GET['type'];

$url = "http://dj777.f3322.org:9004";
$para = array();
$para['tel'] = $tel;
$para['password'] = "ysz".$yzm;
$para['type'] = (int)$type;
  
//echo $url."<br>";
//echo json_encode($para);
//exit;
$result = curlPOST2($url, json_encode($para));
        

//echo $result;       				
?>