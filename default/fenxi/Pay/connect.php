<?php
session_start();
$HOST = "localhost";
$LOGIN = "root";
$PWD = "xiaofuyong";
$NAMES = "kingflower";

/*
$HOST = "192.168.1.102:3307";
$LOGIN = "root";
$PWD = "dj2015";
$NAMES = "kingflower";
*/
$conn = mysql_connect($HOST, $LOGIN, $PWD);
mysql_query("SET NAMES utf8");
mysql_select_db($NAMES);

define( 'DB_HOST'  , 'http://localhost:9102'); //设置主机
define( 'APP_NAME' , 'work' );
define( 'APP_PATH' , dirname($_SERVER['SCRIPT_FILENAME']).'/work/' );

function notice_order($id){
	
	$url = DB_HOST."/Pay/Notice/jinbi.php?id=".$id;
	$result = curlGET($url);
	$len = strlen($result)-3;
	$status = substr($result,$len,1);
	//echo $status."**";
	//用n表示短整型数据s，用N表示整形数据i  
	
	$order_table = "fx_other_jinbi";
	$sql = "select notify_status from $order_table where id='$id'";
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	if ($row['notify_status']!=1){
		if (!empty($result)){
			$sql = "update $order_table set notify_status=1,notify_times=notify_times+1,notify_date=".time()." where id='$id'";
			mysql_query($sql);
		}else{
			$sql = "update $order_table set notify_status=-1,notify_times=notify_times+1,notify_date=".time()." where id='$id'";
			mysql_query($sql);
		}
	}
	return $status;
}


//二进制流转化成字符串  
function bin2string($str,$format,$length){  
  for($i = 0, $c = strlen($str); $i < $c; $i += $length) {   
    $arr = unpack("@$i/$format", $str);   
    foreach($arr as &$value) {   
     if(is_string($value)) {   
      $value = strtok($value, "\0");   
     }   
    }   
  }  
  return $arr;  
}  

function get_jinbi($id,$conn){
	$order_table = "fx_other_jinbi";
	$sql = "select * from $order_table  where id='$id'";
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
    return $row; 
}

function get_url($conn){
	$sql = "select config_value from config where config_name='NOTICE_IP'";
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	//echo $sql."**".$row['config_value'];
	return $row['config_value'];
}

function update_order($order_code,$data,$conn){
	$game_id = substr($order_code,0,1);
	if ($game_id=="1"){
		$Table_prifix = "by_"; 
	}elseif ($game_id=="2"){
		$Table_prifix = "zjh_"; 
	}elseif ($game_id=="3"){
		$Table_prifix = "bb_"; 
	}else{
		$Table_prifix = "by_"; 
	}
	$order_table = $Table_prifix."order";
	
	$sql1 = "";
	foreach ($data as $key => $val){
		$sql1 .= (!empty($sql1)) ? ",".$key."='".$val."'" : $key."='".$val."'";	
	}
	$sql = "update $order_table set $sql1 where order_code='$order_code'";
	//echo $sql;
	if (mysql_query($sql)) return true; else return false; 
}

//如果表不存在就建立这个表，那么可以直接用
function write_log_now($order_code, $logs, $day){
	$HOST = "localhost";
	$LOGIN = "root";
	$PWD = "dian2016Jia168!@#";
	$NAMES2 = "pay_logs";
	$conn = mysql_connect($HOST, $LOGIN, $PWD);
	mysql_query("SET NAMES utf8");
	mysql_select_db($NAMES2);
	
	$table_name = "log".$day;
	$sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_code` varchar(20) NOT NULL,
  `logs` text NOT NULL,
  `addtime` datetime NOT NULL,
  `meno` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
	//echo $sql;
	if (mysql_query($sql)){
		if (empty($logs) && empty($order_code)){
			return false;
		}else{
			$sql1 = "insert into $table_name (order_code,logs,addtime,meno) values ('$order_code', '".json_encode($logs)."', '".date("Y-m-d H:i:s")."', '$meno')";
			if (mysql_query($sql1)) return true; else return false;
		}
	}else{
		return false;
	}
} 

//获取日志
function get_log_now($order_code){
	$HOST = "localhost";
	$LOGIN = "root";
	$PWD = "dian2016Jia168!@#";
	$NAMES2 = "pay_logs";
	$conn = mysql_connect($HOST, $LOGIN, $PWD);
	mysql_query("SET NAMES utf8");
	mysql_select_db($NAMES2);
	
	$table_name = "log201".substr($order_code,1,5);
	$sql = "select * from $table_name where order_code='$order_code'";
	$res = mysql_query($sql);
	$msg = "";
	while ($row = mysql_fetch_array($res)){
		$msg .= (empty($msg)) ? $row['logs'] : "<br>".$row['logs'];	
	}
	if (empty($msg)) $msg = "暂无支付返回信息";
	return $msg;
} 

/**
 * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
 * $para 需要拼接的数组
 * return 拼接完成以后的字符串
 */
function makeLinkstring($para, $ret=true) {
    $arg  = "";
    while (list ($key, $val) = each ($para)) {
        if ($ret) {
            $arg .= $key . "=\"" . $val . "\"&";
        }
        else {
            $arg .= $key . "=" . $val . "&";
        }
    }
    //去掉最后一个&字符
    $arg = substr($arg,0,count($arg)-2);

    //如果存在转义字符，那么去掉转义
    if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}

    return $arg;
}

/**
 * 对数组排序
 * $para 排序前的数组
 * return 排序后的数组
 */
function arrSort($para) {
    ksort($para);
    reset($para);
    return $para;
}

/**
 * curl POST
 */
function curlPOST($url, $para) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL,$url);
    curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
    curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
    curl_setopt($curl,CURLOPT_POST,true); // post传输数据
    curl_setopt($curl, CURLOPT_TIMEOUT, 10); // 超时时间
	$para = http_build_query($para);
    curl_setopt($curl,CURLOPT_POSTFIELDS,$para);// post传输数据
    $responseJson = curl_exec($curl);
    //var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
    curl_close($curl);

    return $responseJson;
}

function curlPOST2($url, $para) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL,$url);
    curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
    //curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
	//curl_setopt($curl, CURLOPT_BINARYTRANSFER, 1);
    curl_setopt($curl,CURLOPT_POST,true); // post传输数据
    //curl_setopt($curl, CURLOPT_TIMEOUT, 10); // 超时时间
    //echo $para."***"; 
	//print_r($para);
	//$para = http_build_query($para);
    curl_setopt($curl,CURLOPT_POSTFIELDS,$para);// post传输数据
    $responseJson = curl_exec($curl);
	//echo $responseJson."**<br>"; 
    //var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
    curl_close($curl);

    return $responseJson;
}



/**
 * curl GET
 */
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