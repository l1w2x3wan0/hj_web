<?php
//header("Content-Type: text/html;charset=utf-8");
set_time_limit(0);

$conn1 = mysql_connect("localhost", "root","root","kingflower"); 
mysql_select_db("kingflower", $conn1); 
$conn2 = mysql_connect("huangjiatest.mysql.rds.aliyuncs.com", "hj_test","doojaa2016","zjhmysql"); 
mysql_select_db("zjhmysql", $conn2); 

//生成某天的金币、游戏、登陆日志
$date1 = !empty($_GET['day']) ? $_GET['day'] : date("Y-m-d");
$time1 = strtotime($date1);
$time2 = $time1 + 60 * 60 * 24;
$date2 =  date("Y-m-d",$time2);

$table_name = "log_login_".date("Ymd");
$sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
	  `log_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
	  `user_id` int(11) DEFAULT NULL COMMENT '主键',
	  `login_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	  `login_ip` int(15) unsigned NOT NULL DEFAULT '0',
	  `flatform_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1：ios ; 2 : andriod , 3: pc 平台（ios/android/pc)',
	  `imsi` varchar(16) DEFAULT NULL COMMENT '手机卡号',
	  `imei` varchar(16) DEFAULT NULL COMMENT '机器号',
	  `model` varchar(32) DEFAULT NULL COMMENT '机型',
	  `version` varchar(6) DEFAULT NULL COMMENT '客户端登录的版本号',
	  `channel` smallint(6) NOT NULL DEFAULT '0' COMMENT '渠道号',
	  `address` varchar(64) DEFAULT NULL,
	  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '0:注册，1：登录',
	  `gameversion` varchar(8) DEFAULT NULL,
	  PRIMARY KEY (`log_id`),
	  KEY `idx_login_date` (`login_date`) USING BTREE,
	  KEY `idx_login_user_id` (`user_id`) USING BTREE,
	  KEY `idx_login_version` (`gameversion`) USING BTREE
	) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
$result = mysql_query($sql, $conn1);
if ($result){
	//$sql = "insert into $table_name select * from zjhmysql.log_game_record_log_".date("Ym")." where curtime>='$time1' and curtime<'$time2'";
	//echo $sql; exit;
	//mysql_query($sql, $conn2);
	
	$ziduan = "log_id,user_id,login_date,login_ip,flatform_type,imsi,imei,model,version,channel,address,type,gameversion";
	$arr = explode(",", $ziduan);
	
	$sql1 = "";
	$sql0 = "select log_id from $table_name";
	$res0 = mysql_query($sql0, $conn1);
	while ($row0 = mysql_fetch_array($res0)){
		$sql1 .= (empty($sql1)) ? $row0['log_id'] : ",".$row0['log_id'];
	}
	if (!empty($sql1)) $sql1 = " and log_id not in ($sql1)";
	
	$sql = "select * from login_log_".date("Ym")." where login_date>='$date1' and login_date<'$date2' $sql1 limit 0,10000";
	$res = mysql_query($sql, $conn2);
	while ($row = mysql_fetch_array($res)){
		
		$sql0 = "select count(*) as total from $table_name where log_id=".$row['log_id'];
		$res0 = mysql_query($sql0, $conn1);
		$row0 = mysql_fetch_array($res0);
		if ($row0['total'] == 0){
			$str1 = "";
			$str2 = "";
			$i = 0;
			foreach ($arr as $val){
				$str1 .= ($i==0) ? $val : ",".$val; 
				$str2 .= ($i==0) ? "'".$row[$val]."'" : ",'".$row[$val]."'"; 	
				$i++;
			}
			$sql0 = "INSERT INTO $table_name ($str1) VALUES ($str2)";  
			mysql_query($sql0, $conn1);
		}
	}

}

mysql_close($conn1);
mysql_close($conn2);

echo "OK";
exit;

$sql = "select * from log_gold_change_log_".date("Ym")." where curtime>='$time1' and curtime<'$time2'";
$res = mysql_query($sql, $conn2);
$row = mysql_fetch_array($res);
$mulu = "record/gold/".date("Y")."/".date("m")."/";
//echo $mulu0;
create_dir($mulu);
$filename = $mulu."log_gold_record_".$date1.".json";
file_put_contents($filename, json_encode($row, JSON_UNESCAPED_UNICODE));

$sql = "select * from login_log_".date("Ym")." where login_date>='$date1' and login_date<'$date2'";
$res = mysql_query($sql, $conn2);
$row = mysql_fetch_array($res);
$mulu = "record/login/".date("Y")."/".date("m")."/";
//echo $mulu0;
create_dir($mulu);
$filename = $mulu."log_login_record_".$date1.".json";
file_put_contents($filename, json_encode($row, JSON_UNESCAPED_UNICODE));

echo "OK";


function create_dir($dirName, $recursive = 1, $mode = 0755) {
	!is_dir($dirName) && mkdir($dirName, $mode, $recursive);
}

//-------------------------------------------------------------------------------
//插入表内容
//-------------------------------------------------------------------------------
function insert_table($table_name, $column_value, $conn) 
{   
    $str1 = "";
	$str2 = "";
	$i = 0;
	foreach ($column_value as $key => $val){
		$str1 .= ($i==0) ? $key : ",".$key; 
		$str2 .= ($i==0) ? "'".$val."'" : ",'".$val."'"; 	
		$i++;
	}
	$sql = "INSERT INTO $table_name ($str1) VALUES ($str2)";  
	echo $sql; exit; 
	if (mysql_query($sql, $conn)) return mysql_insert_id(); else return false;
}
?>