<?php
$type = !empty($_POST['type']) ? $_POST['type'] : $_GET['type'];
$uid = !empty($_POST['uid']) ? $_POST['uid'] : $_GET['uid'];
if (empty($type)) $type = "1";
$HOST = "114.119.37.179";
$LOGIN = "root";
$PWD = "dj_zjh_2015";
$NAMES = "kingflower";
$conn = mysql_connect($HOST, $LOGIN, $PWD);
mysql_query("SET NAMES utf8");
mysql_select_db($NAMES);

$arr1 = array();
$sort1 = array();
$sql = "SELECT distinct user_id FROM log_gold_change_log_201512 WHERE CURTIME>1450022400 AND CURTIME<1450108800 AND module=3 AND changegold>0 and !((user_id>=10000000 and user_id<10002000) or (user_id>=10325805 and user_id<=10327804))";
$res = mysql_query($sql);
$key = 0;
while ($row = mysql_fetch_array($res)){
	$sql0 = "SELECT sum(changegold) as maxgold FROM log_gold_change_log_201512 WHERE CURTIME>1450022400 AND CURTIME<1450108800 AND module=3 AND changegold>0 and user_id=".$row['user_id'];
	$res0 = mysql_query($sql0);
	$row0 = mysql_fetch_array($res0);
	$arr1[$key]['user_id'] = $row['user_id'];
	$arr1[$key]['maxgold'] = $row0['maxgold'];
	
	$sort1[$key] = $row0['maxgold'];
	$key++;
}
array_multisort($sort1, SORT_DESC, $arr1);
print_r($arr1);
?>