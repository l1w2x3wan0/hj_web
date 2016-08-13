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

$table_name = "log_gold_".date("Ymd");
$sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
	  `curtime` int(11) NOT NULL DEFAULT '0' COMMENT '时间戳',
	  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '日期',
	  `module` int(11) NOT NULL DEFAULT '0' COMMENT '模块:1:注册账号	2:每日登入	3:游戏	4:破产	5:充值	6:在线宝箱 7:任务赠送',
	  `beforegold` bigint(20) NOT NULL DEFAULT '0' COMMENT '变量前的金币数',
	  `aftergold` bigint(20) NOT NULL DEFAULT '0' COMMENT '变量后的金币数',
	  `changegold` bigint(20) NOT NULL DEFAULT '0' COMMENT '变动金币数',
	  `taxgold` int(11) NOT NULL DEFAULT '0' COMMENT '税收,游戏,老虎机等有效',
	  `roomid` int(11) NOT NULL DEFAULT '0' COMMENT '房间id,只有在游戏才有效',
	  `memo` varchar(64) NOT NULL DEFAULT '' COMMENT '备注',
	  PRIMARY KEY (`id`),
	  KEY `用户id` (`user_id`) USING BTREE,
	  KEY `时间` (`curtime`) USING BTREE
	) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
$result = mysql_query($sql, $conn1);
if ($result){
	//$sql = "insert into $table_name select * from zjhmysql.log_game_record_log_".date("Ym")." where curtime>='$time1' and curtime<'$time2'";
	//echo $sql; exit;
	//mysql_query($sql, $conn2);
	
	$ziduan = "id,user_id,curtime,date,module,beforegold,aftergold,changegold,taxgold,roomid,memo";
	$arr = explode(",", $ziduan);
	
	$sql1 = "";
	$sql0 = "select id from $table_name";
	$res0 = mysql_query($sql0, $conn1);
	while ($row0 = mysql_fetch_array($res0)){
		$sql1 .= (empty($sql1)) ? $row0['id'] : ",".$row0['id'];
	}
	if (!empty($sql1)) $sql1 = " and id not in ($sql1)";
	
	$sql = "select * from log_gold_change_log_".date("Ym")." where curtime>='$time1' and curtime<'$time2' $sql1 limit 0,10000";
	$res = mysql_query($sql, $conn2);
	while ($row = mysql_fetch_array($res)){
		
		$sql0 = "select count(*) as total from $table_name where id=".$row['id'];
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