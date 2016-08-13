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

$date2 = date("Y-m-d");
$time2 = strtotime($date2);
$time1 = $time2 - 60 * 60 * 24 * 1;
$date1 = date("Y-m-d", $time1);

//新增用户
$new_user_id = "";
$sql = "select user_id from user_info where register_date>='$date1' and register_date<'$date2' $sql1";
$res = mysql_query($sql, $conn2);
while ($val = mysql_fetch_array($res)){
	$new_user_id[] = $val['user_id'];
}
$table0 = "game_log.log".date("Ymd", $time1);

$flag = 3;
$sql = "select count(*) as total from fx_tongji1 where data='$date1' and flag=$flag";
$res = mysql_query($sql, $conn1);
$row = mysql_fetch_array($res);
if ($row['total']==0){
	$event1 = array();
	$event1[0] = array('name' => 'tableLevel1', 'meno' => '点击初级场', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
	$event1[1] = array('name' => 'tableLevel2', 'meno' => '点击中级场', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
	$event1[2] = array('name' => 'tableLevel3', 'meno' => '点击高级场', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
	$event1[3] = array('name' => 'tiger', 'meno' => '点击老虎机', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
	$event1[4] = array('name' => 'mall', 'meno' => '点击商城图标', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
	$event1[5] = array('name' => 'task', 'meno' => '点击任务图标', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
	$event1[6] = array('name' => 'rank', 'meno' => '点击排名图标', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
	$event1[7] = array('name' => 'help', 'meno' => '点击帮助图标', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
	$event1[8] = array('name' => 'fast', 'meno' => '点击快速开始', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
	$event1[9] = array('name' => 'firstcharge', 'meno' => '点击首充', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
	$event1[10] = array('name' => 'mail', 'meno' => '点击邮件图标', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
	$event1[11] = array('name' => 'setting', 'meno' => '点击设置图标', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
	$event1[12] = array('name' => 'myinfo', 'meno' => '点击个人中心', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
	$event1[13] = array('name' => 'horn', 'meno' => '大厅点击大喇叭', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
	
	//分析各种事件
	$sql = "select distinct user_id from $table0 where addtime>='$time1' and addtime<'$time2' and tname=1";
	$res = mysql_query($sql, $conn1);
	while ($val = mysql_fetch_array($res)){
		$flag1 = (in_array($val['user_id'], $new_user_id)) ? 1 : 0;
		//$flag2 = (in_array($val['user_id'], $active_user_id)) ? 1 : 0;
		$flag2 = 0;
		foreach($event1 as $key1 => $val1){
			//统计各种事件用户数
			$sql0 = "select count(user_id) as total from $table0 where addtime>='$time1' and addtime<'$time2' and tname=1 and user_id=".$val['user_id']." and ename='".$val1['name']."'";
			$res0 = mysql_query($sql0, $conn1);
			$row0 = mysql_fetch_array($res0);
			$count1 = $row0['total'];

			if ($flag1 == 1){
				if ($count1 > 0) $event1[$key1]['num_new']++;
				$event1[$key1]['clicked_new'] += $count1;
			}else{
				if ($count1 > 0) $event1[$key1]['num_old']++;
				$event1[$key1]['clicked_old'] += $count1;
			}
			if ($flag2 == 1){
				if ($count1 > 0) $event1[$key1]['num_active']++;
				$event1[$key1]['clicked_active'] += $count1;
			}
		}
	}
	$tongji = array('data' => $date1,
					'event' => $event1);
	
	//更新到数据库
	$sql = "insert into fx_tongji1 (data, flag, channel, version, tongji, addtime) values ('$date1', '$flag', 'all', 'all', '".json_encode($tongji)."', '".time()."')";
	mysql_query($sql, $conn1);
	
	echo "1";
}else{
	echo "0";
}

$flag = 4;
$sql = "select count(*) as total from fx_tongji1 where data='$date1' and flag=$flag";
$res = mysql_query($sql, $conn1);
$row = mysql_fetch_array($res);
if ($row['total']==0){
	$event2 = array();
	$event2[0] = array('name' => 'goldBtn', 'meno' => '点击金币选项', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
	$event2[1] = array('name' => 'vipBtn', 'meno' => '点击VIP选项', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
	$event2[2] = array('name' => 'goods7', 'meno' => '点击10元首充商城', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
	$event2[3] = array('name' => 'goods2', 'meno' => '点击6元商品', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
	$event2[4] = array('name' => 'goods8', 'meno' => '点击10元商品', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
	$event2[5] = array('name' => 'goods3', 'meno' => '点击30元商品', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
	$event2[6] = array('name' => 'goods9', 'meno' => '点击50元商品', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
	$event2[7] = array('name' => 'goods4', 'meno' => '点击100元商品', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
	$event2[8] = array('name' => 'goods5', 'meno' => '点击300元商品', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
	$event2[9] = array('name' => 'goods6', 'meno' => '点击500元商品', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
	
	//分析各种事件
	$sql = "select distinct user_id from $table0 where addtime>='$time1' and addtime<'$time2' and tname=2";
	$res = mysql_query($sql, $conn1);
	while ($val = mysql_fetch_array($res)){
		$flag1 = (in_array($val['user_id'], $new_user_id)) ? 1 : 0;
		//$flag2 = (in_array($val['user_id'], $active_user_id)) ? 1 : 0;
		$flag2 = 0;
		foreach($event2 as $key1 => $val1){
			//统计各种事件用户数
			$sql0 = "select count(user_id) as total from $table0 where addtime>='$time1' and addtime<'$time2' and tname=2 and user_id=".$val['user_id']." and ename='".$val1['name']."'";
			$res0 = mysql_query($sql0, $conn1);
			$row0 = mysql_fetch_array($res0);
			$count1 = $row0['total'];

			if ($flag1 == 1){
				if ($count1 > 0) $event2[$key1]['num_new']++;
				$event2[$key1]['clicked_new'] += $count1;
			}else{
				if ($count1 > 0) $event2[$key1]['num_old']++;
				$event2[$key1]['clicked_old'] += $count1;
			}
			if ($flag2 == 1){
				if ($count1 > 0) $event2[$key1]['num_active']++;
				$event2[$key1]['clicked_active'] += $count1;
			}
		}
	}
	$tongji = array('data' => $date1,
					'event' => $event2);
	
	//更新到数据库
	$sql = "insert into fx_tongji1 (data, flag, channel, version, tongji, addtime) values ('$date1', '$flag', 'all', 'all', '".json_encode($tongji)."', '".time()."')";
	mysql_query($sql, $conn1);
	
	echo "1";
}else{
	echo "0";
}

$flag = 5;
$sql = "select count(*) as total from fx_tongji1 where data='$date1' and flag=$flag";
$res = mysql_query($sql, $conn1);
$row = mysql_fetch_array($res);
if ($row['total']==0){
	$event3 = array();
	$event3[0] = array('name' => 'myhead', 'meno' => '点击自己头像', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
	$event3[1] = array('name' => 'otherhead', 'meno' => '点击他人头像', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
	$event3[2] = array('name' => 'treasure', 'meno' => '点击在线宝箱', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
	$event3[3] = array('name' => 'quickpay', 'meno' => '点击快速充值', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
	$event3[4] = array('name' => 'ready', 'meno' => '点击准备', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
	$event3[5] = array('name' => 'changetable', 'meno' => '点击换桌', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
	$event3[6] = array('name' => 'allin', 'meno' => '点击全压', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
	
	//分析各种事件
	$sql = "select distinct user_id from $table0 where addtime>='$time1' and addtime<'$time2' and tname=3";
	$res = mysql_query($sql, $conn1);
	while ($val = mysql_fetch_array($res)){
		$flag1 = (in_array($val['user_id'], $new_user_id)) ? 1 : 0;
		//$flag2 = (in_array($val['user_id'], $active_user_id)) ? 1 : 0;
		$flag2 = 0;
		foreach($event3 as $key1 => $val1){
			//统计各种事件用户数
			$sql0 = "select count(user_id) as total from $table0 where addtime>='$time1' and addtime<'$time2' and tname=3 and user_id=".$val['user_id']." and ename='".$val1['name']."'";
			$res0 = mysql_query($sql0, $conn1);
			$row0 = mysql_fetch_array($res0);
			$count1 = $row0['total'];

			if ($flag1 == 1){
				if ($count1 > 0) $event3[$key1]['num_new']++;
				$event3[$key1]['clicked_new'] += $count1;
			}else{
				if ($count1 > 0) $event3[$key1]['num_old']++;
				$event3[$key1]['clicked_old'] += $count1;
			}
			if ($flag2 == 1){
				if ($count1 > 0) $event3[$key1]['num_active']++;
				$event3[$key1]['clicked_active'] += $count1;
			}
		}
	}
	$tongji = array('data' => $date1,
					'event' => $event3);
	
	//更新到数据库
	$sql = "insert into fx_tongji1 (data, flag, channel, version, tongji, addtime) values ('$date1', '$flag', 'all', 'all', '".json_encode($tongji)."', '".time()."')";
	mysql_query($sql, $conn1);
	
	echo "1";
}else{
	echo "0";
}
?>