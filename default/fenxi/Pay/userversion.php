<?php
$type = !empty($_POST['type']) ? $_POST['type'] : $_GET['type'];
$uid = !empty($_POST['uid']) ? $_POST['uid'] : $_GET['uid'];
if (empty($type)) $type = "1";
$conn1 = mysql_connect("localhost", "root","root","kingflower"); 
mysql_select_db("kingflower", $conn1); 
$conn2 = mysql_connect("huangjiatest.mysql.rds.aliyuncs.com", "hj_test","doojaa2016","zjhmysql"); 
mysql_select_db("zjhmysql", $conn2); 
	
//print_r($show);
$sql = "select user_id,version,channel from user_info where !((user_id>=10000000 and user_id<10002000) or (user_id>=10325805 and user_id<=10327804)) ";
$res = mysql_query($sql, $conn2);
$show = array();
while ($row = mysql_fetch_array($res)){
	
	$sql4 = "'".$row['version']."'";
	//判断升级记录
	$sql1 = "select version_new from fx_user_version where user_id='".$row['user_id']."'";
	$res1 = mysql_query($sql1, $conn1);
	while ($row1 = mysql_fetch_array($res1)){
		$sql4 .= ",'".$row1['version_new']."'";
	}
	
	$sql1 = "select login_date,version,gameversion from login_log where user_id='".$row['user_id']."' and gameversion not in ($sql4) order by login_date";
	//if ($row['user_id']=="10328441") echo $sql1."<br>";
	$res1 = mysql_query($sql1, $conn2);
	$row1 = mysql_fetch_array($res1);
	if (!empty($row1['gameversion']) and ($row1['gameversion']>$row['version'])){
		$sql2 = "insert into fx_user_version (user_id, channel, version_old, version_new, logindate, addtime) values ('".$row['user_id']."', '".$row['channel']."', '".$row['version']."', '".$row1['gameversion']."', '".$row1['login_date']."', '".time()."')";
		
		//echo $sql2."<br>"; 
		mysql_query($sql2, $conn1);
	}
	

}
echo "1";
?>