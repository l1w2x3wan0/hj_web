<?php
require("connect.php");

$id = $_GET['id'];
$jinbi = get_jinbi($id,$conn);
$sql = "select config_value from config where config_name='NOTICE_IP'";
$res = mysql_query($sql);
$row = mysql_query($res);
$url = $row['config_value'];
$url = "http://dj777.f3322.org:9002";
//$url = "113.91.173.211:9002";
$para = array();
$para['order'] = $id;
$para['userId'] = (int)$jinbi['user_id'];
$para['goldnum'] = (int)$jinbi['gold'];
$para['type'] = 1;
  

$result = curlPOST2($url, '{"order":"3","userId":840000,"goldnum":200,"type":1}');
        
echo json_encode($para);
echo $result;       				
?>