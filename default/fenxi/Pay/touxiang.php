<?php
require("connect.php");

$user_id = $_GET['user_id'];
//$url = get_url($conn);
$sql = "select config_value from config where config_name='NOTICE_IP'";
$res = mysql_query($sql);
$row = mysql_fetch_array($res);
$url = $row['config_value'];
//$url = "123.59.46.6:9002";
//$url = "113.91.173.211:9002";
$para = array();
$para['userId'] = (int)$user_id;
$para['pic'] = "tx/weigui";
$para['type'] = 3;
  
//echo $url."<br>";
//echo json_encode($para);
//exit;
$result = curlPOST2($url, json_encode($para));
        

//echo $result;       				
?>