<?php
session_start();
header("Content-Type:text/html;charset=utf-8");
//预发布

$conn1 = mysql_connect("localhost", "root","root","kingflower"); 
mysql_select_db("kingflower", $conn1); 
$conn2 = mysql_connect("huangjiatest.mysql.rds.aliyuncs.com", "hj_test","doojaa2016","zjhmysql"); 
mysql_select_db("zjhmysql", $conn2); 
/*
//本地
$conn1 = mysql_connect("192.168.1.102:3307", "root","dj2015","fenxi_ben"); 
mysql_select_db("fenxi_ben", $conn1); 
$conn2 = mysql_connect("192.168.1.102:3307", "root","dj2015","kingflower"); 
mysql_select_db("kingflower", $conn2); 
*/
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

$sql = "select count(*) as total from fx_jinbi_base where date='$date1'";
$res = mysql_query($sql, $conn1);
$row = mysql_fetch_array($res);
if ($row['total']==0 && $date1<date("Y-m-d")){

	//总产出金币
	$table2 = "log_gold_change_log_".date("Ym", $time1);
	$sum_jb_send = 0;
	$sql0 = "select * from fx_jinbi_base_config where cate=1";
	$res0 = mysql_query($sql0, $conn1);
	while ($val = mysql_fetch_array($res0)){
		$sql2 = "select sum(changegold) as sumgold from $table2 where curtime>='$time1' and curtime<'$time2' and changegold>0 and module=".$val['module'];
		$res2 = mysql_query($sql2, $conn2);
		$row2 = mysql_fetch_array($res2);
		$count1 = (!empty($row2['sumgold'])) ? $row2['sumgold'] : 0;
		
		$sql3 = "select count(*) as total from fx_jinbi_base where date='$date1' and cate=1 and module='".$val['module']."'";
		$res3 = mysql_query($sql3, $conn1);
		$row3 = mysql_fetch_array($res3);
		if ($row3['total']==0){
			$sql3 = "insert into fx_jinbi_base (date, cate, module, gold) values ('$date1', '1', '".$val['module']."', '$count1')";
			mysql_query($sql3, $conn1);
		}
		
		$sum_jb_send += $count1;
	}
	$sql3 = "insert into fx_jinbi_base (date, cate, module, gold) values ('$date1', '1', '0', '$sum_jb_send')";
	mysql_query($sql3, $conn1);
	
	//总回收金币
	$sum_jb_recall = 0;
	$sql0 = "select * from fx_jinbi_base_config where cate=2";
	$res0 = mysql_query($sql0, $conn1);
	while ($val = mysql_fetch_array($res0)){
		//flag(1金币2税)
		if ($val['flag'] == 1){
			$sql2 = "select sum(changegold) as sumgold from $table2 where curtime>='$time1' and curtime<'$time2' and changegold<0 and module=".$val['module'];
			$res2 = mysql_query($sql2, $conn2);
			$row2 = mysql_fetch_array($res2);
			$count1 = (!empty($row2['sumgold'])) ? abs($row2['sumgold']) : 0;
			$module = $val['module'];
			
			$sum_jb_recall += $count1;
		}else{
			//针对房间统计
			if (!empty($val['roomid'])){
				$sql2 = "select sum(taxgold) as sumgold from $table2 where curtime>='$time1' and curtime<'$time2' and module=".$val['module']." and roomid=".$val['roomid'];
				$res2 = mysql_query($sql2, $conn2);
				$row2 = mysql_fetch_array($res2);
				$count1 = (!empty($row2['sumgold'])) ? $row2['sumgold'] : 0;
				$module = "6".$val['roomid'];
			}else{
				$sql2 = "select sum(taxgold) as sumgold from $table2 where curtime>='$time1' and curtime<'$time2' and module=".$val['module'];
				$res2 = mysql_query($sql2, $conn2);
				$row2 = mysql_fetch_array($res2);
				$count1 = (!empty($row2['sumgold'])) ? $row2['sumgold'] : 0;
				$module = $val['module'];
				
				$sum_jb_recall += $count1;
			}
		}
		
		$sql3 = "select count(*) as total from fx_jinbi_base where date='$date1' and cate=2 and module='".$module."'";
		$res3 = mysql_query($sql3, $conn1);
		$row3 = mysql_fetch_array($res3);
		if ($row3['total']==0){
			$sql3 = "insert into fx_jinbi_base (date, cate, module, gold) values ('$date1', '2', '".$module."', '$count1')";
			mysql_query($sql3, $conn1);
		}
		
		
	}
	$sql3 = "insert into fx_jinbi_base (date, cate, module, gold) values ('$date1', '2', '0', '$sum_jb_recall')";
	mysql_query($sql3, $conn1);
	
	echo "1";
}else{
	echo "0";
}
?>