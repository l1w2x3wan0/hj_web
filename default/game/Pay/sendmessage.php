<?php
require("connect.php");

$showtype = !empty($_GET['showtype']) ? $_GET['showtype'] : 11;
$uid = !empty($_GET['uid']) ? $_GET['uid'] : "";
$content = !empty($_GET['content']) ? trim($_GET['content']) : "";
$color = !empty($_GET['color']) ? $_GET['color'] : "";

$sql = "select config_value from manage_config where config_name='NOTICE_IP'";
$res = mysql_query($sql);
$row = mysql_fetch_array($res);
$url = $row['config_value'];

$para = array();
$para['type'] = (int)$showtype;
$para['content'] = $content; 
$para['uid'] = (int)$uid;
$para['color'] = (int)$color;  
//echo $url."<br>";
//echo json_encode($para);
//exit;
$result = curlPOST2($url, json_encode($para));
//echo $result;       				
?>