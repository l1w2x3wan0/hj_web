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
$sql00 = "  !((user_id>=".ROBERT1_BEGIN." and user_id<".ROBERT1_END.") or (user_id>=".ROBERT2_BEGIN." and user_id<=".ROBERT2_END."))";
//限制USER_ID不统计在内
$sql = "SELECT GROUP_CONCAT(user_id) AS limit_user_id FROM channel_limit_user";
$res = mysql_query($sql, $conn1);
$row = mysql_fetch_array($res);
$limit_user_id = $row['limit_user_id'];
if (!empty($limit_user_id)) $sql00 .= " and user_id not in ($limit_user_id)";

$date1= !empty($_GET['date1']) ? $_GET['date1'] : date("Y-m-d");
$time1 = strtotime($date1);
$time2 = $time1 + 60 * 60 * 24 * 1;
$date2 = date("Y-m-d", $time2);

$table_log = "log_login_".date("Ymd", $time1);
$table_pay = "pay_now_config.zjh_order";
//获取用户渠道
$sql = "select channel from zjhmysql.user_info where $sql00 and channel!=2 group by channel";
$res = mysql_query($sql, $conn2);
while ($val1 = mysql_fetch_array($res)){
	if (!empty($val1['channel'])){
		//判断该渠道数据是否保存
		$sql0 = "select count(*) as total from fx_online_tongji1 where data='$date1' and channel='".$val1['channel']."'";
		$res0 = mysql_query($sql0, $conn1);
		$row0 = mysql_fetch_array($res0);
		if ($row0['total']==0){
			//没有开始统计
			//新增用户
			$sql1 = "select count(user_id) as total from zjhmysql.user_info where $sql00 and register_date>='$date1' and register_date<'$date2' and channel=".$val1['channel'];
			$res1 = mysql_query($sql1, $conn2);
			$row1 = mysql_fetch_array($res1);
			$count1 = $row1['total'];
			//echo $sql1."***".$count1."<br>";
			//活跃用户
			$sql1 = "select count(distinct user_id) as total from $table_log where $sql00 and channel=".$val1['channel'];
			$res1 = mysql_query($sql1, $conn2);
			$row1 = mysql_fetch_array($res1);
			$count2 = $row1['total'];
			//新增有效用户
			$sql1 = "select count(user_id) as total from zjhmysql.user_info where $sql00 and register_date>='$date1' and register_date<'$date2' and today_game_counts>=3 and channel=".$val1['channel'];
			$res1 = mysql_query($sql1, $conn2);
			$row1 = mysql_fetch_array($res1);
			$count3 = $row1['total'];
			//独立付费用户
			$sql1 = "select count(distinct user_id) as total from $table_pay where order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') and package_id=".$val1['channel'];
			$res1 = mysql_query($sql1, $conn1);
			$row1 = mysql_fetch_array($res1);
			$count4 = $row1['total'];				
			//新增付费用户
			$sql1 = "select user_id from zjhmysql.user_info where $sql00 and register_date>='$date1' and register_date<'$date2' and channel=".$val1['channel'];
			$res1 = mysql_query($sql1, $conn2);
			$sql40 = "";
			while($row1 = mysql_fetch_array($res1)){
				$sql40 .= (empty($sql40)) ? $row1['user_id'] : ",".$row1['user_id'];
			}
			if (!empty($sql40)){
				$sql40 = " and user_id in ($sql40)";
				//新增付费用户
				$sql1 = "select count(distinct user_id) as total from $table_pay where order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') $sql40 and package_id=".$val1['channel'];
				$res1 = mysql_query($sql1, $conn1);
				$row1 = mysql_fetch_array($res1);
				$count9 = $row1['total'];
				//新增用户总收入
				$sql1 = "select sum(result_money) as total from $table_pay where order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') $sql40 and package_id=".$val1['channel'];
				$res1 = mysql_query($sql1, $conn1);
				$row1 = mysql_fetch_array($res1);
				$count5 = (empty($row1['total'])) ? 0 : $row1['total']/100;
			}else{
				$count9 = 0;
				$count5 = 0;
			} 
			//总收入
			$sql1 = "select sum(result_money) as total from $table_pay where order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') and package_id=".$val1['channel'];
			$res1 = mysql_query($sql1, $conn1);
			$row1 = mysql_fetch_array($res1);
			$count13 = (empty($row1['total'])) ? 0 : $row1['total']/100;
			//付费率(付费用户/活跃用户)
			$count6 = ($count2==0) ? 0 : round($count4/$count2,3) * 100;
			//付费用户ARPU(总收入/付费用户)
			$count7 = ($count3==0) ? 0 : round($count13/$count3,2);
			//活跃用户ARPU(总收入/活跃用户)
			$count8 = ($count2==0) ? 0 : round($count13/$count2,2);
			//次日留存
			$time3 = $time1 - 60 * 60 * 24;
			$date3 = date("Y-m-d", $time3);
			$sql1 = "select user_id from zjhmysql.user_info where $sql00 and register_date>='$date3' and register_date<'$date1' and channel=".$val1['channel'];
			$res1 = mysql_query($sql1, $conn2);
			$sql4 = "";
			$sum1 = 0;
			while($row1 = mysql_fetch_array($res1)){
				$sql4 .= (empty($sql4)) ? $row1['user_id'] : ",".$row1['user_id'];
				$sum1++;
			}
			if (!empty($sql4)) $sql4 = " and user_id in ($sql4)";
			if ($sum1 == 0){
				$count10 = 0;
			}else{
				$sql1 = "select count(distinct user_id) as total from $table_log where $sql00 $sql4 and channel=".$val1['channel'];
				$res1 = mysql_query($sql1, $conn2);
				$row1 = mysql_fetch_array($res1);
				$sum2 = $row1['total'];
				$count10 = ($sum1==0) ? 0 : round($sum2/$sum1, 3)*100;
			}
			//7日留存
			$time3 = $time1 - 60 * 60 * 24 * 7;
			$date3 = date("Y-m-d", $time3);
			$sql1 = "select user_id from zjhmysql.user_info where $sql00 and register_date>='$date3' and register_date<'$date1' and channel=".$val1['channel'];
			$res1 = mysql_query($sql1, $conn2);
			$sql4 = "";
			$sum1 = 0;
			while($row1 = mysql_fetch_array($res1)){
				$sql4 .= (empty($sql4)) ? $row1['user_id'] : ",".$row1['user_id'];
				$sum1++;
			}
			if (!empty($sql4)) $sql4 = " and user_id in ($sql4)";
			if ($sum1 == 0){
				$count11 = 0;
			}else{
				$sql1 = "select count(distinct user_id) as total from $table_log where $sql00 $sql4 and channel=".$val1['channel'];
				$res1 = mysql_query($sql1, $conn2);
				$row1 = mysql_fetch_array($res1);
				$sum2 = $row1['total'];
				$count11 = ($sum1==0) ? 0 : round($sum2/$sum1, 3)*100;
			}
			//15日留存
			$time3 = $time1 - 60 * 60 * 24 * 15;
			$date3 = date("Y-m-d", $time3);
			$sql1 = "select user_id from zjhmysql.user_info where $sql00 and register_date>='$date3' and register_date<'$date1' and channel=".$val1['channel'];
			$res1 = mysql_query($sql1, $conn2);
			$sql4 = "";
			$sum1 = 0;
			while($row1 = mysql_fetch_array($res1)){
				$sql4 .= (empty($sql4)) ? $row1['user_id'] : ",".$row1['user_id'];
				$sum1++;
			}
			if (!empty($sql4)) $sql4 = " and user_id in ($sql4)";
			if ($sum1 == 0){
				$count12 = 0;
			}else{
				$sql1 = "select count(distinct user_id) as total from $table_log where $sql00 $sql4 and channel=".$val1['channel'];
				$res1 = mysql_query($sql1, $conn2);
				$row1 = mysql_fetch_array($res1);
				$sum2 = $row1['total'];
				$count12 = ($sum1==0) ? 0 : round($sum2/$sum1, 3)*100;
			}
			//支付详情
			$sql1 = "select payment_id,payment_name from payment where payment_status=1 order by order_by_value";
			$res1 = mysql_query($sql1, $conn1);
			$key2 = 0;
			$payment = array();
			while($row1 = mysql_fetch_array($res1)){
				if (empty($sql40)) {
					$count = 0;
				} else{
					$sql2 = "select sum(result_money) as total from $table_pay where order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') $sql40 and payment_id=".$row1['payment_id']." and package_id=".$val1['channel'];
					$res2 = mysql_query($sql2, $conn1);
					$row2 = mysql_fetch_array($res2);
					$count = (empty($row2['total'])) ? 0 : $row2['total']/100;
				}
				$payment[$key2]['payment_id'] = $row1['payment_id'];
				$payment[$key2]['payment_name'] = $row1['payment_name'];
				$payment[$key2]['count'] = $count;
				$key2++;
			}	
			
			$tongji = array('data' => date("Ymd", $time1),
											'channel' => empty($val1['channel']) ? "all" : $val1['channel'],
											'gameid' => '102',
											'game' => '\u7687\u5bb6AAA',
											'count1' => $count1,
											'count2' => $count2,
											'count3' => $count3,
											'count4' => $count4,
											'count5' => $count5,
											'count6' => (empty($count6)) ? 0 : $count6.'%',
											'count7' => $count7,
											'count8' => $count8,
											'count9' => $count9,
											'count10' => (empty($count10)) ? 0 : $count10.'%',
											'count11' => (empty($count11)) ? 0 : $count11.'%',
											'count12' => (empty($count12)) ? 0 : $count12.'%',
											'count13' => $count13,
											'payment' => $payment);		
			
			$sql13 = "insert into fx_online_tongji1 (data, tongji, addtime, channel) values ('$date1', '".json_encode($tongji)."', '".time()."', '".$val1['channel']."')";	
			mysql_query($sql13, $conn1);
			//计算结束
		}
	}
}
//特殊限制用户记录到2号渠道
if (!empty($limit_user_id)){
	$channel = 2;
	//活跃用户
	$sql1 = "select count(distinct user_id) as total from $table_log where user_id in ($limit_user_id)";
	$res1 = mysql_query($sql1, $conn2);
	$row1 = mysql_fetch_array($res1);
	$count2 = $row1['total'];
	
	//总收入
	$sql1 = "select sum(result_money) as total from $table_pay where order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') and user_id in ($limit_user_id)";
	$res1 = mysql_query($sql1, $conn1);
	$row1 = mysql_fetch_array($res1);
	$count13 = (empty($row1['total'])) ? 0 : $row1['total']/100;
	//活跃用户ARPU(总收入/活跃用户)
	$count8 = ($count2==0) ? 0 : round($count13/$count2,2);
	
	//支付详情
	$sql1 = "select payment_id,payment_name from payment where payment_status=1 order by order_by_value";
	$res1 = mysql_query($sql1, $conn1);
	$key2 = 0;
	$payment = array();
	while($row1 = mysql_fetch_array($res1)){
		$payment[$key2]['payment_id'] = $row1['payment_id'];
		$payment[$key2]['payment_name'] = $row1['payment_name'];
		$payment[$key2]['count'] = 0;
		$key2++;
	}
	
	$tongji = array('data' => date("Ymd", $time1),
											'channel' => $channel,
											'gameid' => '102',
											'game' => '\u7687\u5bb6AAA',
											'count1' => 0,
											'count2' => $count2,
											'count3' => 0,
											'count4' => 0,
											'count5' => 0,
											'count6' => 0,
											'count7' => 0,
											'count8' => $count8,
											'count9' => 0,
											'count10' => 0,
											'count11' => 0,
											'count12' => 0,
											'count13' => $count13,
											'payment' => $payment);		
			
	$sql13 = "insert into fx_online_tongji1 (data, tongji, addtime, channel) values ('$date1', '".json_encode($tongji)."', '".time()."', '2')";	
	mysql_query($sql13, $conn1);
}

echo "ok";
?>