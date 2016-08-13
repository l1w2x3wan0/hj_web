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

$date2 = !empty($_GET['data']) ? $_GET['data'] : "2016-01-03";
$time2 = strtotime($date2);
$time1 = $time2 - 60 * 60 * 24 * 1;
$date1 = date("Y-m-d", $time1);

$sql = "select count(*) as total from fx_user_base where data='$date1'";
$res = mysql_query($sql, $conn1);
$row = mysql_fetch_array($res);
if ($row['total']==0){
	
	//用户总量
	$sql = "select count(user_id) as total from user_info where 1 $sql1";
	$res = mysql_query($sql, $conn2);
	$row = mysql_fetch_array($res);
	$count0 = $row['total'];
	
	//新增玩家
	$table1 = "log_game_record_log_".date("Ym", $time1);
	$user_add_ok = 0;
	$sql = "select user_id from user_info where register_date>='$date1' and register_date<'$date2' $sql1";
	$res = mysql_query($sql, $conn2);
	$count1 = 0;
	$sql4 = "";
	while ($row = mysql_fetch_array($res)){
		$count1++;
		$sql4 .= (empty($sql4)) ? $row['user_id'] : ",".$row['user_id'];
		
		//有效新增用户
		$sql11 = "select count(id) as total from $table1 where curtime>='$time1' and curtime<'$time2' and user_id='".$row['user_id']."'";
		$res11 = mysql_query($sql11, $conn2);
		$row11 = mysql_fetch_array($res11);
		if ($row11['total'] > 3){
			$user_add_ok++;
		}
	}
	if (!empty($sql4)) $sql4 = " and user_id in ($sql4)";
	//有效率
	$user_ok_lv = (empty($count1)) ? 0 : round($user_add_ok/$count1,2);
	
	//新增付费人数
	$sql = "select count(distinct user_id) as total from zjh_order where order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') $sql4";
	$res = mysql_query($sql, $conn1);
	$row = mysql_fetch_array($res);
	$count2 = $row['total'];
	
	//新增付费金额
	$sql = "select sum(result_money) as total from zjh_order where order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') $sql4";
	$res = mysql_query($sql, $conn1);
	$row = mysql_fetch_array($res);
	$count3 = $row['total'];
	
	//新增活跃玩家
	$sql = "select count(distinct user_id) as total from login_log where login_date>='$date1' and login_date<'$date2' $sql1 $sql4";
	$res = mysql_query($sql, $conn2);
	$row = mysql_fetch_array($res);
	$count4 = $row['total'];
	
	//新增付费率
	$count5 = (empty($count4)) ? 0 : round($count2/$count4,3) * 100;
	
	//付费人数
	$sql = "select count(distinct user_id) as total from zjh_order where order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2')";
	$res = mysql_query($sql, $conn1);
	$row = mysql_fetch_array($res);
	$user_all_pay_num = $row['total'];
	
	//付费金额
	$sql = "select sum(result_money) as total from zjh_order where order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2')";
	$res = mysql_query($sql, $conn1);
	$row = mysql_fetch_array($res);
	$user_all_pay_money = $row['total'];
	
	//活跃玩家
	$sql = "select count(distinct user_id) as total from login_log where login_date>='$date1' and login_date<'$date2' $sql1";
	$res = mysql_query($sql, $conn2);
	$row = mysql_fetch_array($res);
	$count6 = $row['total'];
	
	//付费率
	$uesr_all_pay_lv = (empty($count6)) ? 0 : round($user_all_pay_num/$count6,3) * 100;
	
	//DAU
	$dau = $count4;
	
	//DAU（老用户）
	$dau_old = $count6 - $dau;
	
	//活跃arpu
	$arpu = (empty($count6)) ? 0 : round($user_all_pay_money/100/$count6,3);
	
	//日arppu
	$arppu = (empty($user_all_pay_num)) ? 0 : round($user_all_pay_money/100/$user_all_pay_num,3);
	
	//新增arpu
	$arpu_new = (empty($count4)) ? 0 : round($user_all_pay_money/100/$count4,3);
	
	//平均在线
	$table4 = "log_online_data_".date("Ym",$time1);
	$sql = "select count(id) as total from $table4 where daytime=".$time1;
	$res = mysql_query($sql, $conn2);
	$row = mysql_fetch_array($res);
	$count70 = $row['total'];
	$sql = "select sum(room1+room2+room3) as total from $table4 where daytime=".$time1;
	$res = mysql_query($sql, $conn2);
	$row = mysql_fetch_array($res);
	$count71 = $row['total'];
	$online1 = empty($count70) ? 0 : round($count71/$count70, 1);
	
	//当日峰值在线
	$sql = "select (room1+room2+room3) as sum8 from $table4 where daytime=".$time1." order by sum8 desc";
	$res = mysql_query($sql, $conn2);
	$row = mysql_fetch_array($res);
	$online2 = $row['sum8'];
	
	//平均牌局数
	$table2 = "log_game_record_log_".date("Ym", $time1);
	$sql = "select count(id) as total from $table2 where curtime>='$time1' and curtime<'$time2' and roomid in (1,2,3) $sql1";
	$res = mysql_query($sql, $conn2);
	$row = mysql_fetch_array($res);
	$count80 = $row['total'];
	$sql = "select count(distinct user_id) as total from $table2 where curtime>='$time1' and curtime<'$time2' and roomid in (1,2,3) $sql1";
	$res = mysql_query($sql, $conn2);
	$row = mysql_fetch_array($res);
	$count81 = $row['total'];
	$paiju = (empty($count81)) ? 0 : round($count80/$count81, 1);
	
	//更新到数据库
	$sql = "insert into fx_user_base (data, user_add, user_pay_num, user_pay_money, user_pay_lv, user_num, dau, dau_old, user_add_ok, user_ok_lv, online1, online2, paiju, arpu, arppu, arpu_new, user_all_pay_num, user_all_pay_money, uesr_all_pay_lv, addtime) values 
	('$date1', '$count1', '$count2', '$count3', '$count5', '$count0', '$dau', '$dau_old', '$user_add_ok', '$user_ok_lv', '$online1', '$online2', '$paiju', '$arpu', '$arppu', '$arpu_new', '$user_all_pay_num', '$user_all_pay_money', '$uesr_all_pay_lv', '".date("Y-m-d H:i:s")."')";
	mysql_query($sql, $conn1);
	
	//次日留存率
	$time31 = $time1 - 60 * 60 * 24;
	$time32 = $time31 + 60 * 60 * 24;
	$date31 = date("Y-m-d", $time31);
	$date32 = date("Y-m-d", $time32);
	$user1 = 0;
	$sql4 = "";
	$sql = "select user_id from user_info where register_date>='$date31' and register_date<'$date32' $sql1";
	$res = mysql_query($sql, $conn2);
	while ($row = mysql_fetch_array($res)){
		$sql4 .= (empty($sql4)) ? $row['user_id'] : ",".$row['user_id'];
		$user1++;
	}
	if (!empty($sql4)) $sql4 = " and user_id in ($sql4)";
	$sql11 = "select count(distinct user_id) as total from login_log where login_date>='$date1' and login_date<'$date2' $sql4";
	$res11 = mysql_query($sql11, $conn2);
	$row11 = mysql_fetch_array($res11);
	$login1 = $row11['total'];
	$liucun1 = (empty($user1)) ? 0 : round($login1/$user1,3) * 100;
	$sql = "update fx_user_base set liucun1='$liucun1' where data='$date31'";
	mysql_query($sql, $conn1);
	
	//3日留存率
	$time41 = $time1 - 60 * 60 * 24 * 3;
	$time42 = $time41 + 60 * 60 * 24;
	$date41 = date("Y-m-d", $time41);
	$date42 = date("Y-m-d", $time42);
	$user1 = 0;
	$sql4 = "";
	$sql = "select user_id from user_info where register_date>='$date41' and register_date<'$date42' $sql1";
	$res = mysql_query($sql, $conn2);
	while ($row = mysql_fetch_array($res)){
		$sql4 .= (empty($sql4)) ? $row['user_id'] : ",".$row['user_id'];
		$user1++;
	}
	if (!empty($sql4)) $sql4 = " and user_id in ($sql4)";
	$sql11 = "select count(distinct user_id) as total from login_log where login_date>='$date1' and login_date<'$date2' $sql4";
	$res11 = mysql_query($sql11, $conn2);
	$row11 = mysql_fetch_array($res11);
	$login1 = $row11['total'];
	$liucun2 = (empty($user1)) ? 0 : round($login1/$user1,3) * 100;
	$sql = "update fx_user_base set liucun2='$liucun2' where data='$date41'";
	mysql_query($sql, $conn1);

	//7日留存率
	$time51 = $time1 - 60 * 60 * 24 * 7;
	$time52 = $time51 + 60 * 60 * 24;
	$date51 = date("Y-m-d", $time51);
	$date52 = date("Y-m-d", $time52);			
	$user1 = 0;
	$sql4 = "";
	$sql = "select user_id from user_info where register_date>='$date51' and register_date<'$date52' $sql1";
	$res = mysql_query($sql, $conn2);
	while ($row = mysql_fetch_array($res)){
		$sql4 .= (empty($sql4)) ? $row['user_id'] : ",".$row['user_id'];
		$user1++;
	}
	if (!empty($sql4)) $sql4 = " and user_id in ($sql4)";
	$sql11 = "select count(distinct user_id) as total from login_log where login_date>='$date1' and login_date<'$date2' $sql4";
	$res11 = mysql_query($sql11, $conn2);
	$row11 = mysql_fetch_array($res11);
	$login1 = $row11['total'];
	$liucun3 = (empty($user1)) ? 0 : round($login1/$user1,3) * 100;
	$sql = "update fx_user_base set liucun3='$liucun3' where data='$date51'";
	mysql_query($sql, $conn1);
	
	echo "1";
}else{
	echo "0";
}

//统计周总用户数据
$week = date("w");
if ($week == "1"){
	$date1 =  date("Y-m-d",strtotime("-7 day"));
	$date2 =  date("Y-m-d",strtotime("-1 day"));
	
	$sql = "select count(*) as total from fx_user_tongji where date1='$date1' and date2='$date2'";
	$res = mysql_query($sql, $conn1);
	$row = mysql_fetch_array($res);
	if ($row['total']==0){
		
		$userall = 0;
		$sql = "select channel from user_info where 1 $sql1 group by channel";
		$res = mysql_query($sql, $conn2);
		while ($row = mysql_fetch_array($res)){
			//echo $row['channel']."<br>";
			//渠道用户总量
			$sql20 = "select count(user_id) as total from user_info where channel='".$row['channel']."' $sql1";
			$res20 = mysql_query($sql20, $conn2);
			$row20 = mysql_fetch_array($res20);
			$count = $row20['total'];
			$userall+=$count;
			$sql21 = "select count(*) as total from fx_user_tongji where date1='$date1' and date2='$date2' and channel='".$row['channel']."'";
			$res21 = mysql_query($sql21, $conn1);
			$row21 = mysql_fetch_array($res21);
			if ($row21['total']==0){
				$sql = "insert into fx_user_tongji (date1, date2, channel, usernum, addtime) values ('$date1', '$date2', '".$row['channel']."', '$count', '".date("Y-m-d H:i:s")."')";
				mysql_query($sql, $conn1);
			}
		}
		
		$sql = "insert into fx_user_tongji (date1, date2, channel, usernum, addtime) values ('$date1', '$date2', 'ALL', '$userall', '".date("Y-m-d H:i:s")."')";
		mysql_query($sql, $conn1);
	}

} 

//统计月用户数据,每个月1号执行
$day = date("d");
if ($day == "01"){
	$date1 =  date("Y-m-d",strtotime("-1 month"));
	$date2 =  date("Y-m-d",strtotime("-1 day"));
	
	$sql = "select count(*) as total from fx_user_tongji where date1='$date1' and date2='$date2'";
	$res = mysql_query($sql, $conn1);
	$row = mysql_fetch_array($res);
	if ($row['total']==0){
		
		$userall = 0;
		$sql = "select channel from user_info where 1 $sql1 group by channel";
		$res = mysql_query($sql, $conn2);
		while ($row = mysql_fetch_array($res)){
			//echo $row['channel']."<br>";
			//渠道用户总量
			$sql20 = "select count(user_id) as total from user_info where channel='".$row['channel']."' $sql1";
			$res20 = mysql_query($sql20, $conn2);
			$row20 = mysql_fetch_array($res20);
			$count = $row20['total'];
			$userall+=$count;
			$sql21 = "select count(*) as total from fx_user_tongji where date1='$date1' and date2='$date2' and channel='".$row['channel']."'";
			$res21 = mysql_query($sql21, $conn1);
			$row21 = mysql_fetch_array($res21);
			if ($row21['total']==0){
				$sql = "insert into fx_user_tongji (date1, date2, channel, usernum, addtime) values ('$date1', '$date2', '".$row['channel']."', '$count', '".date("Y-m-d H:i:s")."')";
				mysql_query($sql, $conn1);
			}
		}
		
		$sql = "insert into fx_user_tongji (date1, date2, channel, usernum, addtime) values ('$date1', '$date2', 'ALL', '$userall', '".date("Y-m-d H:i:s")."')";
		mysql_query($sql, $conn1);
	}

} 

?>