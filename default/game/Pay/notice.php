<?php
require("connect.php");

$id = !empty($_GET['id']) ? $_GET['id'] : 189;
$sql = "select logs from user_record where id='$id'";
$res = mysql_query($sql);
$row = mysql_fetch_array($res);
$notice = json_decode($row['logs'], true);
 
//$jinbi = get_jinbi($id,$conn);
//$url = get_url($conn);
$sql = "select config_value from manage_config where config_name='NOTICE_IP'";
$res = mysql_query($sql);
$row = mysql_fetch_array($res);
$url = $row['config_value'];
//$url = "http://dj777.f3322.org:9002";
//$url = "113.91.173.211:9002";
//$url = "http://192.168.1.102:9602";
$para = array();
$para['type'] = 5;
$para['starttime'] = (int)strtotime($notice['startime']);
$para['endtime'] = (int)strtotime($notice['endtime']);
$para['message'] = $notice['message'];
  
//echo $url."<br>";
//echo json_encode($para);
//exit;
$result = curlPOST2($url, json_encode($para));
        

//echo $result;       				
?>