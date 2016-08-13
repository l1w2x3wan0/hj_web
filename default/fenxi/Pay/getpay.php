<?php
session_start();
header("Content-Type:text/html;charset=utf-8");
$HOST = "localhost";
$LOGIN = "root";
$PWD = "root";
$NAMES = "kingflower";
$conn = mysql_connect($HOST, $LOGIN, $PWD);
mysql_query("SET NAMES utf8");
mysql_select_db($NAMES);

$date = date("Y-m-d");
$gettime = strtotime($date) - 60 * 60 * 24 * 1;
$getdate = date("Y-m-d", $gettime);

$url = "http://test-pay.kk520.com:9102/index.php?m=Payinter&a=get_order_zjh&date=".$getdate;
$result = curlGET($url);
if (!empty($result)){
	$order = json_decode($result, true);
	//print_r($order); exit;
	foreach ($order as $key => $val){
		$sql = "select count(*) as total from zjh_order where order_code='".$val['order_code']."'";
		$res = mysql_query($sql);
		$row = mysql_fetch_array($res);
		if ($row['total'] == 0){
			$sql1 = "";
			$sql2 = "";
			foreach ($val as $key2 => $val2){
				if ($key2 != "id"){
					$sql1 .= ($sql1=="") ? $key2 : ",".$key2;
					if (($key2 == "result_money" || $key2 == "order_pay_time" || $key2 == "notify_date") and $val2==""){
						$sql2 .= ($sql2=="") ? "'0'" : ",'0'";
					}else{
						$sql2 .= ($sql2=="") ? "'".$val2."'" : ",'".$val2."'";
					}
				}
				
			}
			$sql0 = "insert into zjh_order ($sql1) values ($sql2)";
			//echo $sql0."<br>";
			mysql_query($sql0);
		}else{
			$sql3 = "";
			foreach ($val as $key2 => $val2){
				if ($key2 != "id"){
					
					if (($key2 == "result_money" || $key2 == "order_pay_time" || $key2 == "notify_date") and $val2==""){
						$val3 = "'0'";
					}else{
						$val3 = "'".$val2."'";
					}
					$sql3 .= ($sql3=="") ? $key2."=".$val3 : ",".$key2."=".$val3;
				}
			}
			$sql0 = "update zjh_order set $sql3 where order_code='".$val['order_code']."'";
			//echo $sql0."<br>";
			mysql_query($sql0);
		}
	}
	$url = "http://test-pay.kk520.com:9102/index.php?m=Payinter&a=post_order_zjh&date=".$getdate;
	$result = curlGET($url);
	echo $result;
}

function curlGET($url) {
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
    curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
    curl_setopt($curl, CURLOPT_TIMEOUT, 10); // 超时时间
    $responseText = curl_exec($curl);
    //var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
    curl_close($curl);

    return $responseText;
}
?>