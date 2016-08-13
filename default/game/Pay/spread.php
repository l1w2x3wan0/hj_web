<?php
require("connect.php");

$uid = !empty($_GET['spread_id']) ? $_GET['spread_id'] : "";
$gold = !empty($_GET['jiangli']) ? $_GET['jiangli'] : "";
$msg = !empty($_GET['msg']) ? $_GET['msg'] : "";
$index = !empty($_GET['index']) ? $_GET['index'] : 1;
$type = !empty($_GET['type']) ? $_GET['type'] : 8;
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
$para['uid'] = (int)$uid;
$para['gold'] = (int)$gold;
$para['type'] = (int)$type;
$para['index'] = (int)$index;
$para['msg'] = $msg;  
//echo $url."<br>";
//echo json_encode($para);
//exit;
$result = curlPOST2($url, json_encode($para));
        

//echo $result;       				
?>