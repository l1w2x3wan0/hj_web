<?php
require("connect.php");

//$url = get_url($conn);
$sql = "select config_value from config where config_name='NOTICE_IP'";
$res = mysql_query($sql);
$row = mysql_fetch_array($res);
$url = $row['config_value'];
//$url = "123.59.46.6:9002";
//$url = "113.91.173.211:9002";
$para = array();
$para['userId'] = (int)$_GET['user_id'];
$para['viplevel'] = (int)$_GET['viplevel'];
$para['vippoint'] = (int)$_GET['vippoint'];
$para['lottery_id'] = (int)$_GET['lottery_id'];
$para['nums'] = (int)$_GET['nums'];
$para['type'] = 10;
//print_r($para);  
//echo $url."<br>";
//echo json_encode($para);
//exit;
$result = curlPOST2($url, json_encode($para));
        

//echo $result;       				
?>