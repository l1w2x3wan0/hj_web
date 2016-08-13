<?php
require_once 'ChuanglanSmsHelper/ChuanglanSmsApi.php';
$clapi  = new ChuanglanSmsApi();
$uid = !empty($_POST['uid']) ? $_POST['uid'] : "";
$tel = !empty($_POST['tel']) ? $_POST['tel'] : "";
$type = !empty($_POST['type']) ? $_POST['type'] : "";
$code = !empty($_POST['code']) ? $_POST['code'] : "";
if (empty($type)) $type = "1";

$conn1 = mysql_connect("localhost", "root","xiaofuyong","kingflower"); 
mysql_select_db("kingflower", $conn1); 
$conn2 = mysql_connect("huangjiatest.mysql.rds.aliyuncs.com", "hj_test","doojaa2016","zjhmysql"); 
mysql_select_db("zjhmysql", $conn2); 

if (!empty($tel)){
	$yzm = rand(1000,9999);
	
	$sql = "select * from config";
	$res = mysql_query($sql, $conn1);
	while ($row = mysql_fetch_array($res)){
		define($row['config_name'], $row['config_value']);
		//echo $row['config_name']."**".$row['config_value']."<br>";
	}

	if ($type == "5"){
		$url = "http://test.ysz.kk520.com:9102/Pay/send.php?yzm=".$code."&tel=".$tel."&type=2";
		$jinbi_result = curlGET($url);
		$result = substr($jinbi_result, 10, 1);
		//$jinbi_result = json_decode($jinbi_result, true);
		//$result = $jinbi_result['status'];
		//$jinbi_result = str_replace('{"status":0}','',$jinbi_result);
		//echo $jinbi_result."**". $result."**";
		if ($type == "2") $msg = '重置失败'; else $msg = '密码重置失败';
		if ($result != 1){
			$showinfo = array('uid' => $uid,
							  'tel' => $tel,
							  'type' => $type,
							  'status' => 0,
							  'error' => $msg,
							  'msg' => $msg);
			echo json_encode($showinfo, JSON_UNESCAPED_UNICODE);
			exit;
		}else{
			$showinfo = array('uid' => $uid,
							  'tel' => $tel,
							  'type' => $type,
							  'status' => 1,
							  'msg' => '服务端通知成功');
			echo json_encode($showinfo, JSON_UNESCAPED_UNICODE);
			exit;
		}
	}
	
	if ($type == "1"){
		//判断是否绑定
		$sql = "select count(*) as total from user_info where phone_number='$tel'";
		//echo $sql;
		$res = mysql_query($sql, $conn2);
		$row = mysql_fetch_array($res);
		//echo "<br>".$row['total'];
		if ($row['total']>0){
			$showinfo = array('uid' => $uid,
							  'tel' => $tel,
							  'type' => $type,
							  'status' => 0,
							  'error' => '手机号已绑定',
							  'msg' => '手机号已绑定');
			echo json_encode($showinfo, JSON_UNESCAPED_UNICODE);
			exit;
		}
	}else if ($type == "2"){
		$sql = "select count(*) as total from user_info where phone_number='$tel'";
		$res = mysql_query($sql, $conn2);
		$row = mysql_fetch_array($res);
		if ($row['total']==0){
			$showinfo = array('uid' => $uid,
							  'tel' => $tel,
							  'type' => $type,
							  'status' => 0,
							  'error' => '手机号未绑定',
							  'msg' => '手机号未绑定');
			echo json_encode($showinfo, JSON_UNESCAPED_UNICODE);
			exit;
		}
	}else if ($type == "3"){
		$sql = "select count(*) as total from user_info where phone_number='$tel'";
		$res = mysql_query($sql, $conn2);
		$row = mysql_fetch_array($res);
		if ($row['total']==0){
			$showinfo = array('uid' => $uid,
							  'tel' => $tel,
							  'type' => $type,
							  'status' => 0,
							  'error' => '手机号不正确',
							  'msg' => '手机号不正确');
			echo json_encode($showinfo, JSON_UNESCAPED_UNICODE);
			exit;
		}
	}else{
		
	}
	
	if ($type == "1") $msg = DUANXIN_MAG1; else if ($type == "2") $msg = DUANXIN_MAG2; else if ($type == "3") $msg = DUANXIN_MAG3; else $msg = DUANXIN_MAG3;
	//echo $msg; exit;
	
	$msg = str_replace("【$1】", $yzm, $msg);
	$result = $clapi->sendSMS($tel, $msg, 'true');
	$result = $clapi->execResult($result);
	if($result[1]==0){
		$status = 1;
	}else{
		$status = 0;
		$msg .= "[$result[1]]";
	}
	//print_r($result);
	
	
	
	$sql = "insert into duanxin (user_id, tel, msg, status, type, addtime) values ('$uid', '$tel', '$msg', '$status', '$type', '".time()."')";
	//echo $sql;
	mysql_query($sql, $conn1);
	$showinfo = array('uid' => $uid,
					  'tel' => $tel,
					  'type' => $type,
					  'password' => $yzm,
					  'status' => 1,
					  'msg' => $yzm);
	echo json_encode($showinfo);

}else{
	$showinfo = array('uid' => $uid,
					  'tel' => $tel,
					  'type' => $type,
					  'status' => 0,
					  'msg' => "输入有误");
	echo json_encode($showinfo);
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