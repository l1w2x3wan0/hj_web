<?php
require("connect.php");

$yzm = $_GET['yzm'];
$tel = $_GET['tel'];
$type = $_GET['type'];
//$url = get_url($conn);
$sql = "select config_value from config where config_name='NOTICE_IP'";
$res = mysql_query($sql);
$row = mysql_fetch_array($res);
$url = $row['config_value'];
//$url = "123.59.46.6:9002";
//$url = "113.91.173.211:9002";
$para = array();
$para['tel'] = $tel;
$para['password'] = $yzm;
$para['type'] = (int)$type;
  
//echo $url."<br>";
//echo json_encode($para);
//exit;
$result = curlPOST2($url, json_encode($para));
        

//echo $result;       				
?>