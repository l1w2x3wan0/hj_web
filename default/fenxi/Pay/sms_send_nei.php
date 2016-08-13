<?php
require_once 'ChuanglanSmsHelper/ChuanglanSmsApi.php';
require_once("connect.php");
$clapi  = new ChuanglanSmsApi();
$uid = !empty($_POST['uid']) ? $_POST['uid'] : $_GET['uid'];
$tel = !empty($_POST['tel']) ? $_POST['tel'] : $_GET['tel'];
$type = !empty($_POST['type']) ? $_POST['type'] : $_GET['type'];
$code = !empty($_POST['code']) ? $_POST['code'] : $_GET['code'];
if (empty($type)) $type = "1";

if (!empty($tel)){
	$yzm = rand(1000,9999);
	
	if ($type == "5"){
		$url = "http://dj777.f3322.org:8081/Pay/send_nei.php?yzm=".$code."&tel=".$tel."&type=2";
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
			echo json_encode($showinfo);
			exit;
		}else{
			$showinfo = array('uid' => $uid,
							  'tel' => $tel,
							  'type' => $type,
							  'status' => 1,
							  'msg' => '服务端通知成功');
			echo json_encode($showinfo);
			exit;
		}
	}
	
	if ($type == "1"){
		//判断是否绑定
		$sql = "select count(*) as total from user_info where phone_number='$tel'";
		//echo $sql;
		$res = mysql_query($sql);
		$row = mysql_fetch_array($res);
		//echo "<br>".$row['total'];
		if ($row['total']>0){
			$showinfo = array('uid' => $uid,
							  'tel' => $tel,
							  'type' => $type,
							  'status' => 0,
							  'error' => '手机号已绑定',
							  'msg' => '手机号已绑定');
			echo json_encode($showinfo);
			exit;
		}
	}else if ($type == "2"){
		$sql = "select count(*) as total from user_info where phone_number='$tel'";
		$res = mysql_query($sql);
		$row = mysql_fetch_array($res);
		if ($row['total']==0){
			$showinfo = array('uid' => $uid,
							  'tel' => $tel,
							  'type' => $type,
							  'status' => 0,
							  'error' => '手机号未绑定',
							  'msg' => '手机号未绑定');
			echo json_encode($showinfo);
			exit;
		}
	}else if ($type == "3"){
		$sql = "select count(*) as total from user_info where phone_number='$tel'";
		$res = mysql_query($sql);
		$row = mysql_fetch_array($res);
		if ($row['total']==0){
			$showinfo = array('uid' => $uid,
							  'tel' => $tel,
							  'type' => $type,
							  'status' => 0,
							  'error' => '手机号不正确',
							  'msg' => '手机号不正确');
			echo json_encode($showinfo);
			exit;
		}
	}else{
		
	}
	
	$sql = "select * from config";
	$res = mysql_query($sql);
	while ($row = mysql_fetch_array($res)){
		define($row['config_name'], $row['config_value']);
	}
	
	if ($type == "1") $msg = "绑定手机验证码是：【$1】，三分钟内有效。"; else if ($type == "2") $msg = "重置手机验证码是：【$1】，三分钟内有效。"; else $msg = "重置密码验证码是：【$1】，三分钟内有效。";
	
	
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


?>