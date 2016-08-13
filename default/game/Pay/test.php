<?php 
$yzm = $_GET['yzm'];
$tel = $_GET['tel'];

$url = "http://dj777.f3322.org:9002";
$para = array();
$para['tel'] = $tel;
$para['password'] = "ysz".$yzm;
$para['type'] = 2;
  
echo $url."<br>";
echo json_encode($para);
//exit;
//$result = curlPOST2($url, json_encode($para));
        

echo $result;   
?>