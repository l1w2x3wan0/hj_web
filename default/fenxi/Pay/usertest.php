<?php
$type = !empty($_POST['type']) ? $_POST['type'] : $_GET['type'];
$uid = !empty($_POST['uid']) ? $_POST['uid'] : $_GET['uid'];
if (empty($type)) $type = "1";
$HOST = "10.13.9.3";
$LOGIN = "root";
$PWD = "root";
$NAMES = "kingflower";
$conn = mysql_connect($HOST, $LOGIN, $PWD);
mysql_query("SET NAMES utf8");
mysql_select_db($NAMES);
	
//print_r($show);
$sql = "select user_id,viplevel,vip_type from user_info where user_id in (10325875,10325868,10325898,10325870,10325858,10325869,10325878,10325861,10325904,10325820,10325886,10325818,10325831,10325856,10325879,10325903,10325890,10325836,10325872,10325847)";
$res = mysql_query($sql);
$i = 1;
while ($row = mysql_fetch_array($res)){
	/*
	$sql1 = "select count(*) as total from user_info where nickname='".$row['nick_name']."'";
	$res1 = mysql_query($sql1);
	$row1 = mysql_fetch_array($res1);
	if ($row1['total']==0){
		$str = "update user_info set nickname='".$row['nick_name']."' where user_id='".$row['user_id']."'";
		mysql_query($str);
		echo $str."<br>"; 
	}
	*/
	//$vip_type = rand(1,5);
	$sql1 = "update user_info set viplevel='".$row['vip_type']."' where user_id='".$row['user_id']."';";
	//mysql_query($sql1);
	echo $sql1."<br>";
	$i++;
}
//echo "1";
?>