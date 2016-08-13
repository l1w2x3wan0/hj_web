<?php
session_start();
header("Content-Type:text/html;charset=utf-8");
//预发布
/*
$conn1 = mysql_connect("localhost", "root","root","kingflower"); 
mysql_select_db("kingflower", $conn1); 
$conn2 = mysql_connect("huangjiatest.mysql.rds.aliyuncs.com", "hj_test","doojaa2016","zjhmysql"); 
mysql_select_db("zjhmysql", $conn2); 
*/
//本地
$conn1 = mysql_connect("localhost", "web_local","localWEBphp2016!@#","kingflower"); 
mysql_select_db("kingflower", $conn1); 
//$conn2 = mysql_connect("rdse2o1kfrn2cfo4z618.mysql.rds.aliyuncs.com", "webhj","HJdianjiaWEB2016","zjhmysql"); 
//mysql_select_db("zjhmysql", $conn2); 
$conn2 = mysql_connect("doojaazjh.mysql.rds.aliyuncs.com", "web_php","dianjia2016","kingflower"); 
mysql_select_db("kingflower", $conn2); 


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

$sql = "select count(*) as total from fx_user_base where data='$date1'";
$res = mysql_query($sql, $conn1);
$row = mysql_fetch_array($res);
if ($row['total']==0){
	
	//用户总量
	$sql = "select key_value from user_base where key_adddate='$date1' and key_name='count1'";
	$res = mysql_query($sql, $conn2);
	$row = mysql_fetch_array($res);
	$count0 = $row['key_value'];
	
	//新增玩家
	$sql = "select key_value from user_base where key_adddate='$date1' and key_name='count2'";
	$res = mysql_query($sql, $conn2);
	$row = mysql_fetch_array($res);
	$count1 = $row['key_value'];
	
	//有效新增用户
	$sql = "select key_value from user_base where key_adddate='$date1' and key_name='count3'";
	$res = mysql_query($sql, $conn2);
	$row = mysql_fetch_array($res);
	$user_add_ok = $row['key_value'];
	
	//有效率
	$user_ok_lv = (empty($count1)) ? 0 : round($user_add_ok/$count1,2);
	
	//新增玩家USERID
	$sql = "select key_value from user_base where key_adddate='$date1' and key_name='user_id_reg'";
	$res = mysql_query($sql, $conn2);
	$row = mysql_fetch_array($res);
	$user_id_reg = $row['key_value'];
	if (!empty($user_id_reg)){
		$table_pay = "pay_now_config.zjh_order";
		//新增付费人数
		$sql = "select count(distinct user_id) as total from $table_pay where order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') and user_id in ($user_id_reg)";
		$res = mysql_query($sql, $conn1);
		$row = mysql_fetch_array($res);
		$count2 = $row['total'];
		
		//新增付费金额
		$sql = "select sum(result_money) as total from $table_pay where order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') and user_id in ($user_id_reg)";
		$res = mysql_query($sql, $conn1);
		$row = mysql_fetch_array($res);
		$count3 = $row['total'];
	}else{
		$count2 = 0;
		$count3 = 0;
	}
	
    if (empty($count3)) $count3 = 0; else $count3 = $count3 / 100;
	
	//新增活跃玩家
	$count4 = $count1;
	
	//新增付费率
	$count5 = (empty($count4)) ? 0 : round($count2/$count4,3) * 100;
	
	//付费人数
	$sql = "select count(distinct user_id) as total from $table_pay where order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2')";
	$res = mysql_query($sql, $conn1);
	$row = mysql_fetch_array($res);
	$user_all_pay_num = $row['total'];
	
	//付费金额
	$sql = "select sum(result_money) as total from $table_pay where order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2')";
	$res = mysql_query($sql, $conn1);
	$row = mysql_fetch_array($res);
	$user_all_pay_money = $row['total'];
    if (empty($user_all_pay_money)) $user_all_pay_money = 0; else $user_all_pay_money = $user_all_pay_money / 100;
	
	//活跃玩家
	$sql = "select key_value from user_base where key_adddate='$date1' and key_name='count9'";
	$res = mysql_query($sql, $conn2);
	$row = mysql_fetch_array($res);
	$count6 = $row['key_value'];
	
	//付费率
	$uesr_all_pay_lv = (empty($count6)) ? 0 : round($user_all_pay_num/$count6,3) * 100;
	
	//DAU
	$dau = $count6;
	
	//DAU（老用户）
	$dau_old = $count6 - $dau;
	
	//活跃arpu
	$arpu = (empty($count6)) ? 0 : round($user_all_pay_money/$count6,3);
	
	//日arppu
	$arppu = (empty($user_all_pay_num)) ? 0 : round($user_all_pay_money/$user_all_pay_num,3);
	
	//新增arpu
	$arpu_new = (empty($user_add_ok)) ? 0 : round($count3/$user_add_ok,3);
	
	//平均在线
	$table4 = "zjhmysql.log_online_data_".date("Ym",$time1);
	$sql = "select count(id) as total from $table4 where daytime=".$time1;
	$res = mysql_query($sql, $conn2);
	$row = mysql_fetch_array($res);
	$count70 = $row['total'];
	$sql = "select sum(totalnum) as total from $table4 where daytime=".$time1;
	$res = mysql_query($sql, $conn2);
	$row = mysql_fetch_array($res);
	$count71 = $row['total'];
	$online1 = empty($count70) ? 0 : round($count71/$count70, 1);
	
	//当日峰值在线
	$sql = "select totalnum from $table4 where daytime=".$time1." order by totalnum desc";
	$res = mysql_query($sql, $conn2);
	$row = mysql_fetch_array($res);
	$online2 = $row['totalnum'];
	
	//平均牌局数
	$sql = "select key_value from user_base where key_adddate='$date1' and key_name='count6'";
	$res = mysql_query($sql, $conn2);
	$row = mysql_fetch_array($res);
	$count80 = $row['key_value'];
	$sql = "select key_value from user_base where key_adddate='$date1' and key_name='count7'";
	$res = mysql_query($sql, $conn2);
	$row = mysql_fetch_array($res);
	$count81 = $row['key_value'];
	$paiju = (empty($count81)) ? 0 : round($count80/$count81, 1);
	
	//累计激活
	$sql = "select key_value from user_base where key_adddate='$date1' and key_name='count4'";
	$res = mysql_query($sql, $conn2);
	$row = mysql_fetch_array($res);
	$zong1 = $row['key_value'];
	
	//累计账户
	$sql = "select key_value from user_base where key_adddate='$date1' and key_name='count1'";
	$res = mysql_query($sql, $conn2);
	$row = mysql_fetch_array($res);
	$zong2 = $row['key_value'];
	
	//累计收入(¥)
	$sql = "select sum(result_money) as total from $table_pay where order_create_time<'$time2' and payment_status in ('1','-2')";
	$res = mysql_query($sql, $conn1);
	$row = mysql_fetch_array($res);
	$zong3 = $row['total'];
	if (empty($zong3)) $zong3 = 0; else $zong3 = $zong3 / 100;
	
	//付费玩家数
	$sql = "select count(distinct user_id) as total from $table_pay where order_create_time<'$time2' and payment_status in ('1','-2')";
	$res = mysql_query($sql, $conn1);
	$row = mysql_fetch_array($res);
	$zong5 = $row['total'];

	
	//更新到数据库
	$sql = "insert into fx_user_base (data, user_add, user_pay_num, user_pay_money, user_pay_lv, user_num, dau, dau_old, user_add_ok, user_ok_lv, online1, online2, paiju, arpu, arppu, arpu_new, user_all_pay_num, user_all_pay_money, uesr_all_pay_lv, zong1, zong2, zong3, zong5, addtime) values 
	('$date1', '$count1', '$count2', '$count3', '$count5', '$count0', '$dau', '$dau_old', '$user_add_ok', '$user_ok_lv', '$online1', '$online2', '$paiju', '$arpu', '$arppu', '$arpu_new', '$user_all_pay_num', '$user_all_pay_money', '$uesr_all_pay_lv', '$zong1', '$zong2', '$zong3', '$zong5', '".date("Y-m-d H:i:s")."')";
	mysql_query($sql, $conn1);
	
	//次日留存率
	$time31 = $time1 - 60 * 60 * 24;
	$time32 = $time31 + 60 * 60 * 24;
	$date31 = date("Y-m-d", $time31);
	$date32 = date("Y-m-d", $time32);
	
	$sql = "select key_value from user_base where key_adddate='$date31' and key_name='user_id_reg'";
	$res = mysql_query($sql, $conn2);
	$row = mysql_fetch_array($res);
	$user_id_add = $row['key_value'];
	if (!empty($user_id_add)) {$arr_reg = explode(",", $user_id_add); $user1 = count($arr_reg);} else {$user1 = 0;}
	
	$sql = "select key_value from user_base where key_adddate='$date1' and key_name='user_id_login'";
	$res = mysql_query($sql, $conn2);
	$row = mysql_fetch_array($res);
	$user_id_log = $row['key_value'];
	if (!empty($user_id_log)) {
		$arr_log = explode(",", $user_id_log); 
		$user_act = array_intersect($arr_reg, $arr_log);
		$login1 = count($user_act);
	} else {
		$login1 = 0;
	}
	$liucun1 = (empty($user1)) ? 0 : round($login1/$user1,3) * 100;
	$sql = "update fx_user_base set liucun1='$liucun1' where data='$date31'";
	mysql_query($sql, $conn1);
	
	//3日留存率
	$time41 = $time1 - 60 * 60 * 24 * 3;
	$time42 = $time41 + 60 * 60 * 24;
	$date41 = date("Y-m-d", $time41);
	$date42 = date("Y-m-d", $time42);
	
	$sql = "select key_value from user_base where key_adddate='$date41' and key_name='user_id_reg'";
	$res = mysql_query($sql, $conn2);
	$row = mysql_fetch_array($res);
	$user_id_add = $row['key_value'];
	if (!empty($user_id_add)) {$arr_reg = explode(",", $user_id_add); $user1 = count($arr_reg);} else {$user1 = 0;}
	
	if ($login1 != 0) {
		$user_act = array_intersect($arr_reg, $arr_log);
		$login1 = count($user_act);
	} 
	
	$liucun2 = (empty($user1)) ? 0 : round($login1/$user1,3) * 100;
	$sql = "update fx_user_base set liucun2='$liucun2' where data='$date41'";
	mysql_query($sql, $conn1);

	//7日留存率
	$time51 = $time1 - 60 * 60 * 24 * 7;
	$time52 = $time51 + 60 * 60 * 24;
	$date51 = date("Y-m-d", $time51);
	$date52 = date("Y-m-d", $time52);	
	
	$sql = "select key_value from user_base where key_adddate='$date51' and key_name='user_id_reg'";
	$res = mysql_query($sql, $conn2);
	$row = mysql_fetch_array($res);
	$user_id_add = $row['key_value'];
	if (!empty($user_id_add)) {$arr_reg = explode(",", $user_id_add); $user1 = count($arr_reg);} else {$user1 = 0;}
	
	if ($login1 != 0) {
		$user_act = array_intersect($arr_reg, $arr_log);
		$login1 = count($user_act);
	}
	
	$liucun3 = (empty($user1)) ? 0 : round($login1/$user1,3) * 100;
	$sql = "update fx_user_base set liucun3='$liucun3' where data='$date51'";
	mysql_query($sql, $conn1);
	
	//15日留存率
	$time51 = $time1 - 60 * 60 * 24 * 15;
	$time52 = $time51 + 60 * 60 * 24;
	$date51 = date("Y-m-d", $time51);
	$date52 = date("Y-m-d", $time52);	
	
	$sql = "select key_value from user_base where key_adddate='$date51' and key_name='user_id_reg'";
	$res = mysql_query($sql, $conn2);
	$row = mysql_fetch_array($res);
	$user_id_add = $row['key_value'];
	if (!empty($user_id_add)) {$arr_reg = explode(",", $user_id_add); $user1 = count($arr_reg);} else {$user1 = 0;}
	
	if ($login1 != 0) {
		$user_act = array_intersect($arr_reg, $arr_log);
		$login1 = count($user_act);
	}
	
	$liucun4 = (empty($user1)) ? 0 : round($login1/$user1,3) * 100;
	$sql = "update fx_user_base set liucun4='$liucun4' where data='$date51'";
	mysql_query($sql, $conn1);
	
	echo "1";
}else{
	echo "0";
}


?>