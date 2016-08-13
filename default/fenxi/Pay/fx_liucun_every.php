<?php
session_start();
header("Content-Type:text/html;charset=utf-8");
$conn1 = mysql_connect("localhost", "root","root","kingflower"); 
mysql_select_db("kingflower", $conn1); 
$conn2 = mysql_connect("huangjiatest.mysql.rds.aliyuncs.com", "hj_test","doojaa2016","zjhmysql"); 
mysql_select_db("zjhmysql", $conn2); 

$sql = "select * from config";
$res = mysql_query($sql, $conn1);
while ($row = mysql_fetch_array($res)){
	define($row['config_name'], $row['config_value']);
}
$sql1 = " and !((user_id>=".ROBERT1_BEGIN." and user_id<".ROBERT1_END.") or (user_id>=".ROBERT2_BEGIN." and user_id<=".ROBERT2_END."))";

$date2 = !empty($_GET['date2']) ? $_GET['date2'] : date("Y-m-d");
$time2 = strtotime($date2);
$time1 = $time2 - 60 * 60 * 24 * 1;
$date1 = date("Y-m-d", $time1);
//留存天数数组
$arr = array(1,2,3,4,5,6,7,14,30);

$sql = "select count(*) as total from fx_tongji7 where data='$date1' and flag=2 and version='all' and channel='all'";
$res = mysql_query($sql, $conn1);
$row = mysql_fetch_array($res);
if ($row['total'] == 0){
	$tongji0 = array();
	$tongji0['data'] = $date1;
	foreach($arr as $val){
		$show = "day".$val;
		$tongji0[$show] = 0;
	}
	//初始化留存以及游戏局数留存
	$sql = "insert into fx_tongji7 (data, tongji, addtime, flag, version, channel) values ('$date1', '".json_encode($tongji0)."', '".time()."', 2, 'all', 'all')";
	mysql_query($sql, $conn1);
	
	for ($i=10; $i<20; $i++){
		$sql = "insert into fx_tongji7 (data, tongji, addtime, flag, version, channel) values ('$date1', '".json_encode($tongji0)."', '".time()."', $i, 'all', 'all')";
		mysql_query($sql, $conn1);
	}
	
}

foreach ($arr as $val){
	$time11 = $time1 - 60 * 60 * 24 * $val;
	$date11 = date("Y-m-d", $time11);
	$time12 = $time11 + 60 * 60 * 24;
	$date12 = date("Y-m-d", $time12);
	//echo $date11."**".$date12."<br>";
	//统计新增用户留存
	$sql = "select tongji from fx_tongji6 where data='$date1' and flag=7 and version='all' and channel='all'";
	$res = mysql_query($sql, $conn1);
	$row = mysql_fetch_array($res);
	if (!empty($row['tongji'])){
		$sql1 = "select count(user_id) as count3 from log_login_".date("Ymd", $time1)." where user_id in (".$row['tongji'].")";
		$res1 = mysql_query($sql1, $conn1);
		$row1 = mysql_fetch_array($res1);
		$count3 = $row1['count3'];
	}else{
		$count3 = 0;
	}
	
	$sql = "select tongji from fx_tongji7 where data='$date11' and flag=2 and version='all' and channel='all'";
	$res = mysql_query($sql, $conn1);
	$num = mysql_num_rows($res);
	if ($num > 0){
		$row = mysql_fetch_array($res);
		$tongji = json_decode($row['tongji'], true);
		print_r($tongji);
		$show = "day".$val;
		$tongji[$show] = $count3;
		print_r($tongji);
		$sql = "update fx_tongji7 set tongji='".json_encode($tongji)."' where data='$date11' and flag=2 and version='all' and channel='all'";
		mysql_query($sql, $conn1);
	}
	
	
	//统计新增用户留存
	$sql = "select tongji from fx_tongji6 where data='$date1' and flag=8 and version='all' and channel='all'";
	$res = mysql_query($sql, $conn1);
	$row = mysql_fetch_array($res);
	if (!empty($row['tongji'])){
		$game = json_decode($row['tongji'], true);
		foreach($game as $key1 => $val1){
			if (!empty($val1)){
				$sql1 = "select count(user_id) as count3 from log_login_".date("Ymd", $time1)." where user_id in (".$val1.")";
				$res1 = mysql_query($sql1, $conn1);
				$row1 = mysql_fetch_array($res1);
				$count3 = $row1['count3'];
			}else{
				$count3 = 0;
			}
			
			$flag1 = 10 + $key1;
			$sql = "select tongji from fx_tongji7 where data='$date11' and flag=$flag1 and version='all' and channel='all'";
			$res = mysql_query($sql, $conn1);
			$num = mysql_num_rows($res);
			if ($num > 0){
				$row = mysql_fetch_array($res);
				$tongji = json_decode($row['tongji'], true);
				print_r($tongji);
				$show = "day".$val;
				$tongji[$show] = $count3;
				print_r($tongji);
				$sql = "update fx_tongji7 set tongji='".json_encode($tongji)."' where data='$date11' and flag=$flag1 and version='all' and channel='all'";
				mysql_query($sql, $conn1);
			}
		}
	}
	
	
	
}

?>