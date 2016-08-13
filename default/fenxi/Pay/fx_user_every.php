<?php
//为了统计当天玩家局数，每天定时23：55分跑
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

$date1 = !empty($_GET['date1']) ? $_GET['date1'] : date("Y-m-d");
$time1 = strtotime($date1);
$time2 = $time1 + 60 * 60 * 24 * 1;
$date2 = date("Y-m-d", $time2);

//存入当天新增用户UID，还有玩了多少局
$ju = array(array(0,0),array(1,1),array(2,2),array(3,3),array(4,4),array(5,5),array(6,10),array(11,20),array(21,30),array(31,50),array(51,10000));

$sql = "select count(*) as total from fx_tongji6 where data='$date1' and flag=7 and version='all' and channel='all'";
$res = mysql_query($sql, $conn1);
$row = mysql_fetch_array($res);
if ($row['total']==0){
	
	//新玩家
	$sql1 = "select user_id,today_game_counts from user_info where register_date>='$date1' and register_date<'$date2'";
	$res1 = mysql_query($sql1, $conn2);
	$tongji = "";
	$tongji_ju = array();
	foreach($ju as $key => $val){
		$tongji_ju[$key] = "";
	}
	while ($row1 = mysql_fetch_array($res1)){
		$tongji .= (empty($tongji)) ? $row1['user_id'] : ",".$row1['user_id'];	 
		foreach($ju as $key => $val){
			//echo $key."**".$val[0]."**".$val[1]."**".$row1['today_game_counts']."<br>";
			if ($row1['today_game_counts']>=$val[0] and $row1['today_game_counts']<=$val[1]){
				//echo $key."**".$row1['user_id']."<br>";
				$tongji_ju[$key] .= (empty($tongji_ju[$key])) ? $row1['user_id'] : ",".$row1['user_id'];
			}
		}
	}

	if (!empty($tongji)){
		$sql = "insert into fx_tongji6 (data, tongji, addtime, flag, version, channel) values ('$date1', '$tongji', '".time()."', 7, 'all', 'all')";
		mysql_query($sql, $conn1);
	}
	if (!empty($tongji_ju)){
		$sql = "insert into fx_tongji6 (data, tongji, addtime, flag, version, channel) values ('$date1', '".json_encode($tongji_ju)."', '".time()."', 8, 'all', 'all')";
		mysql_query($sql, $conn1);
	}
}	

$sql = "select count(*) as total from fx_tongji6 where data='$date1' and flag=6 and version='all' and channel='all'";
$res = mysql_query($sql, $conn1);
$row = mysql_fetch_array($res);
if ($row['total']==0){
	
	//新玩家
	$sql1 = "select count(user_id) as count1 from user_info where register_date>='$date1' and register_date<'$date2'";
	//echo $sql1."<br>";
	$res1 = mysql_query($sql1, $conn2);
	$row1 = mysql_fetch_array($res1);
	$count1 = $row1['count1'];

	//当天登陆总计
	$sql1 = "select count(distinct user_id) as count3 from log_login_".date("Ymd", $time1);
	$res1 = mysql_query($sql1, $conn1);
	$row1 = mysql_fetch_array($res1);
	$count3 = $row1['count3'];
	
	//老玩家
	$count2 = $count3 - $count1;
	
	//新增设备数
	$sql1 = "select count(distinct imei) as count4 from user_info where register_date>='$date1' and register_date<'$date2'";
	$res1 = mysql_query($sql1, $conn2);
	$row1 = mysql_fetch_array($res1);
	$count4 = $row1['count4'];
	
	//活跃设备数
	$sql1 = "select count(distinct imei) as count5 from log_login_".date("Ymd", $time1);
	$res1 = mysql_query($sql1, $conn1);
	$row1 = mysql_fetch_array($res1);
	$count5 = $row1['count5'];
	
	//7,30日内有登陆的玩家
	$arr7 = array();
	$arr30 = array();
	for ($i=0; $i<30; $i++){
		$time11 = $time1 - 60 * 60 * 24 * $i;
		//$date11 = date("Y-m-d", $time11);
		
		$sql1 = "select distinct user_id from log_login_".date("Ymd", $time11);
		$res1 = mysql_query($sql1, $conn1);
		while ($row1 = mysql_fetch_array($res1)){
			$arr30[] = $row1['user_id'];
			if ($i < 7) $arr7[] = $row1['user_id'];
		}
	}
	
	$arr7 = array_unique($arr7); 
	$arr30 = array_unique($arr30); 
	$count6 = count($arr7);
	$count7 = count($arr30);
	//DAU/WAU
	if ($count6 == 0){
		$count8 = 0;
	}else{
		$count8 = round($count3 / $count6 , 2);
	}
	//DAU/MAU
	if ($count7 == 0){
		$count9 = 0;
	}else{
		$count9 = round($count3 / $count7 , 2);
	}
	
	$tongji = array('data' => $date1,'count1' => $count1,'count2' => $count2,'count3' => $count3,'count4' => $count4,'count5' => $count5,'count6' => $count6,'count7' => $count7,'count8' => $count8,'count9' => $count9);
	$sql = "insert into fx_tongji6 (data, tongji, addtime, flag, version, channel) values ('$date1', '".json_encode($tongji)."', '".time()."', 6, 'all', 'all')";
	mysql_query($sql, $conn1);
	echo "1";
}else{
	echo "0";
}
?>