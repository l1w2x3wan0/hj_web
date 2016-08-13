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

$sql = "select count(*) as total from fx_jinbi_fb where data='$date1'";
$res = mysql_query($sql, $conn1);
$row = mysql_fetch_array($res);
if ($row['total']==0){

	$tongji = array();
	for($i=1; $i<=13; $i++){
		$showi = "x".$i;
		$showj = "x".$i."_show";
		$tongji[$showi] = 0;
		switch($i){
			case 1: $tongji[$showj] = "0-1000"; break;
			case 2: $tongji[$showj] = "10001-2000"; break;
			case 3: $tongji[$showj] = "2001-3500"; break;
			case 4: $tongji[$showj] = "3501-5000"; break;
			case 5: $tongji[$showj] = "5001-9000"; break;
			case 6: $tongji[$showj] = "9001-13000"; break;
			case 7: $tongji[$showj] = "13001-20000"; break;
			case 8: $tongji[$showj] = "20001-50000"; break;
			case 9: $tongji[$showj] = "50001-100000"; break;
			case 10: $tongji[$showj] = "100001-200000"; break;
			case 11: $tongji[$showj] = "100001-200000"; break;
			case 12: $tongji[$showj] = "500001-1000000"; break;
			case 13: $tongji[$showj] = ">1000000"; break;
			default: break;
		}
	}
	for($i=1; $i<=14; $i++){
		$showi = "y".$i;
		$showj = "y".$i."_show";
		$tongji[$showi] = 0;
		if ($sumgold<=500){
			$tongji[showj] = "0-500"; 
		}elseif ($sumgold<=2000){
			$tongji[showj] = "501-2K"; 
		}elseif ($sumgold<=5000){
			$tongji[showj] = "2K-5K"; 
		}elseif ($sumgold<=10000){
			$tongji[showj] = "5K-1W"; 
		}elseif ($sumgold<=20000){
			$tongji[showj] = "1W-2W"; 
		}elseif ($sumgold<=50000){
			$tongji[showj] = "2W-5W"; 
		}elseif ($sumgold<=100000){
			$tongji[showj] = "5W-10W"; 
		}elseif ($sumgold<=200000){
			$tongji[showj] = "10W-20W"; 
		}elseif ($sumgold<=500000){
			$tongji[showj] = "20W-50W"; 
		}elseif ($sumgold<=1000000){
			$tongji[showj] = "50W-100W"; 
		}elseif ($sumgold<=2000000){
			$tongji[showj] = "100W-200W"; 
		}elseif ($sumgold<=5000000){
			$tongji[showj] = "200W-500W"; 
		}elseif ($sumgold<=10000000){
			$tongji[showj] = "500W-1000W"; 
		}else{
			$tongji[showj] = "1000W+"; 
		}
		switch($i){
			case 1: $tongji[$showj] = "0-500"; break;
			case 2: $tongji[$showj] = "501-2K"; break;
			case 3: $tongji[$showj] = "2K-5K"; break;
			case 4: $tongji[$showj] = "5K-1W"; break;
			case 5: $tongji[$showj] = "1W-2W"; break;
			case 6: $tongji[$showj] = "2W-5W"; break;
			case 7: $tongji[$showj] = "5W-10W"; break;
			case 8: $tongji[$showj] = "10W-20W"; break;
			case 9: $tongji[$showj] = "20W-50W"; break;
			case 10: $tongji[$showj] = "50W-100W"; break;
			case 11: $tongji[$showj] = "100W-200W"; break;
			case 12: $tongji[$showj] = "200W-500W"; break;
			case 13: $tongji[$showj] = "500W-1000W"; break;
			case 14: $tongji[$showj] = "1000W+"; break;
			default: break;
		}
	}
	
	//活跃玩家金币总量
	$sql = "select distinct user_id from login_log_".date("Ym")." where login_date>='$date1' and login_date<'$date2' $sql1";
	$res = mysql_query($sql, $conn2);
	$i = 0;
	while ($row = mysql_fetch_array($res)){
		
		$sql1 = "select sum(gold) as sumgold from user_info where user_id=".$row['user_id'];
		$res1 = mysql_query($sql1, $conn2);
		$row1 = mysql_fetch_array($res1);
		$sumgold = !empty($row1['sumgold']) ? $row1['sumgold'] : 0;
		if ($sumgold<=1000){
			$tongji['x1']++; 
		}elseif ($sumgold<=2000){
			$tongji['x2']++; 
		}elseif ($sumgold<=3500){
			$tongji['x3']++; 
		}elseif ($sumgold<=5000){
			$tongji['x4']++; 
		}elseif ($sumgold<=9000){
			$tongji['x5']++; 
		}elseif ($sumgold<=13000){
			$tongji['x6']++; 
		}elseif ($sumgold<=20000){
			$tongji['x7']++; 
		}elseif ($sumgold<=50000){
			$tongji['x8']++; 
		}elseif ($sumgold<=100000){
			$tongji['x9']++; 
		}elseif ($sumgold<=200000){
			$tongji['x10']++; 
		}elseif ($sumgold<=500000){
			$tongji['x11']++; 
		}elseif ($sumgold<=1000000){
			$tongji['x12']++; 
		}else{
			$tongji['x13']++; 
		}
		
		if ($sumgold<=500){
			$tongji['y1']++; 
		}elseif ($sumgold<=2000){
			$tongji['y2']++; 
		}elseif ($sumgold<=5000){
			$tongji['y3']++; 
		}elseif ($sumgold<=10000){
			$tongji['y4']++; 
		}elseif ($sumgold<=20000){
			$tongji['y5']++; 
		}elseif ($sumgold<=50000){
			$tongji['y6']++; 
		}elseif ($sumgold<=100000){
			$tongji['y7']++; 
		}elseif ($sumgold<=200000){
			$tongji['y8']++; 
		}elseif ($sumgold<=500000){
			$tongji['y9']++; 
		}elseif ($sumgold<=1000000){
			$tongji['y10']++; 
		}elseif ($sumgold<=2000000){
			$tongji['y11']++; 
		}elseif ($sumgold<=5000000){
			$tongji['y12']++; 
		}elseif ($sumgold<=10000000){
			$tongji['y13']++; 
		}else{
			$tongji['y14']++; 
		}
		$i++;
	}
	$tongji['sumall'] = $i;
	
	$sql = "insert into fx_jinbi_fb (data, tongji, addtime) values ('$date1', '".json_encode($tongji)."', '".date("Y-m-d H:i:s")."')";
	mysql_query($sql, $conn1);
	echo "1";
}else{
	echo "0";
}
?>