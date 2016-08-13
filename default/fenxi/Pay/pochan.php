<?php
session_start();
header("Content-Type:text/html;charset=utf-8");
/*
$conn1 = mysql_connect("localhost", "root","root","kingflower"); 
mysql_select_db("kingflower", $conn1); 
$conn2 = mysql_connect("huangjiatest.mysql.rds.aliyuncs.com", "hj_test","doojaa2016","zjhmysql"); 
mysql_select_db("zjhmysql", $conn2); */
//本地
$conn1 = mysql_connect("localhost", "web_local","localWEBphp2016!@#","kingflower"); 
mysql_select_db("kingflower", $conn1); 
$conn2 = mysql_connect("doojaazjh.mysql.rds.aliyuncs.com", "web_php","dianjia2016","kingflower"); 
mysql_select_db("kingflower", $conn2); 

$sql = "select * from config";
$res = mysql_query($sql, $conn1);
while ($row = mysql_fetch_array($res)){
	define($row['config_name'], $row['config_value']);
}

$date2 = !empty($_GET['date2']) ? $_GET['date2'] : date("Y-m-d");
$time2 = strtotime($date2);
$time1 = $time2 - 60 * 60 * 24 * 1;
$date1 = date("Y-m-d", $time1);

$sql = "select count(*) as total from fx_tongji1 where data='$date1' and flag=10";
$res = mysql_query($sql, $conn1);
$row = mysql_fetch_array($res);
if ($row['total']==0){
	
	

	//活跃玩家UID
	$sql = "select key_value from user_base where key_adddate='$date1' and key_name='user_id_log'";
	$res = mysql_query($sql, $conn2);
	$row = mysql_fetch_array($res);
	if (empty($row['key_value'])){
		$sql4 =  "";
	}else{
		$sql4 = " and user_id in (".$row['key_value'].")";
	}
	
	//活跃玩家
	$sql = "select key_value from user_base where key_adddate='$date1' and key_name='count5'";
	$res = mysql_query($sql, $conn2);
	$row = mysql_fetch_array($res);
	if (empty($row['key_value'])){
		$count1 = 0;
	}else{
		$count1 = $row['key_value'];
	}
	
	$table1 = "log_gold_".date("Ymd", $time1);
	
	if (!empty($sql4)){
		//活跃玩家领取1,2,3次破产次数
		$sql11 = "SELECT COUNT(user_id) AS total FROM $table1 WHERE module=4 $sql4";
		$res11 = mysql_query($sql11, $conn2);
		$row11 = mysql_fetch_array($res11);
		$sum2[0] = $row11['total'];
		
		//活跃玩家领取1,2,3次破产人数
		$sql11 = "SELECT COUNT(distinct user_id) AS total FROM $table1 WHERE module=4 $sql4";
		$res11 = mysql_query($sql11, $conn2);
		$row11 = mysql_fetch_array($res11);
		$sum2[1] = $row11['total'];
		
		//新用户破产率
		$sum2[2] = ($count1==0) ? 0 : round($sum2[1]/$count1, 3);
		
		//活跃玩家领取2,3次破产的次数
		$sql11 = "SELECT sum(nums) AS total FROM (SELECT COUNT(user_id) AS nums FROM $table1 WHERE module=4 $sql4 GROUP BY user_id ) AS t1 WHERE nums>=2";
		$res11 = mysql_query($sql11, $conn2);
		$row11 = mysql_fetch_array($res11);
		$sum2[3] = $row11['total'];
		
		//活跃玩家领取2,3次破产的人数
		$sql11 = "SELECT COUNT(nums) AS total FROM (SELECT COUNT(user_id) AS nums FROM $table1 WHERE module=4 $sql4 GROUP BY user_id ) AS t1 WHERE nums>=2";
		$res11 = mysql_query($sql11, $conn2);
		$row11 = mysql_fetch_array($res11);
		$sum2[4] = $row11['total'];
		
		//活跃玩家领取3次破产的次数
		$sql11 = "SELECT sum(nums) AS total FROM (SELECT COUNT(user_id) AS nums FROM $table1 WHERE module=4 $sql4 GROUP BY user_id ) AS t1 WHERE nums=3";
		$res11 = mysql_query($sql11, $conn2);
		$row11 = mysql_fetch_array($res11);
		$sum2[5] = $row11['total'];
		
		//活跃玩家领取3次破产的人数
		$sql11 = "SELECT COUNT(nums) AS total FROM (SELECT COUNT(user_id) AS nums FROM $table1 WHERE module=4 $sql4 GROUP BY user_id ) AS t1 WHERE nums=3";
		$res11 = mysql_query($sql11, $conn2);
		$row11 = mysql_fetch_array($res11);
		$sum2[6] = $row11['total'];
	}
	
	//循环结束
	$tongji = array('date' => $date1,
					'sum2' => $sum2
					);
	//print_r($tongji);		
	//更新到数据库
	$sql13 = "insert into fx_tongji1 (data, tongji, addtime, flag, channel, version) values ('$date1', '".json_encode($tongji)."', '".time()."', '10', 'all', 'all')";	
	mysql_query($sql13, $conn1);
	
	echo "1";
}else{
	echo "0";
}
?>