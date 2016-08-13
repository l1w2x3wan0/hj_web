<?php
$type = !empty($_POST['type']) ? $_POST['type'] : $_GET['type'];
if (empty($type)) $type = "1";
$HOST = "localhost";
$LOGIN = "root";
$PWD = "root";
$NAMES = "kingflower";
/*
$HOST = "192.168.1.102:3307";
$LOGIN = "root";
$PWD = "dj2015";
$NAMES = "kingflower_bak";
*/
$conn = mysql_connect($HOST, $LOGIN, $PWD);
mysql_query("SET NAMES utf8");
mysql_select_db($NAMES);
if (!empty($type)){
	//机器人参数
	$sql = "select * from fx_paihang where flag='0'";
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	$user = json_decode($row['tongji'], true);
	//财富榜参数
	$sql = "select * from fx_paihang where flag='1'";
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	$can1 = json_decode($row['tongji'], true);
	//昨日赢金榜参数
	$sql = "select * from fx_paihang where flag='2'";
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	$can2 = json_decode($row['tongji'], true);
	//昨日充值榜参数
	$sql = "select * from fx_paihang where flag='3'";
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	$can3 = json_decode($row['tongji'], true);
	
	//排行第1的金币值
	
	//print_r($user);
	//print_r($user);
	if ($type=="1"){
		$sql = "select * from fx_paihang where flag='5'";
		$res = mysql_query($sql);
		$row = mysql_fetch_array($res);
		$tongji = json_decode($row['tongji'], true);
		
	}elseif ($type=="2"){
		$sql = "select * from fx_paihang where flag='6'";
		$res = mysql_query($sql);
		$row = mysql_fetch_array($res);
		$tongji = json_decode($row['tongji'], true);
	}elseif ($type=="3"){
		$sql = "select * from fx_paihang where flag='7'";
		$res = mysql_query($sql);
		$row = mysql_fetch_array($res);
		$pai3_show = json_decode($row['tongji'], true);
		$tongji = array();
		foreach ($pai3_show as $key => $val){
			if ($key < 10) $tongji[$key] = $pai3_show[$key];
		}
	}
	$info = array('status' => 1,
				  'paihang' => $tongji,
				  'ts' => time());
	echo json_encode($info);
	//print_r($show);
}else{
	
}


function array_sort_robert($multi_array,$sort_key){
	$len = count($multi_array);
	//echo $len;
	for($i=0; $i<$len-1; $i++){
		for($j=$i+1; $j<$len; $j++){
			$temp = array();
			//echo $multi_array[$i][$sort_key]."**".$multi_array[$j][$sort_key]."<br>";
			if ($multi_array[$i][$sort_key] < $multi_array[$j][$sort_key]){
				$temp[$i]['id'] = $multi_array[$i]['id'];
				$temp[$i]['user_id'] = $multi_array[$i]['user_id'];
				$temp[$i]['nick_name'] = $multi_array[$i]['nick_name'];
				$temp[$i]['sex'] = $multi_array[$i]['sex'];
				$temp[$i]['tx'] = $multi_array[$i]['tx'];
				$temp[$i]['gold1'] = $multi_array[$i]['gold1'];
				$temp[$i]['gold2'] = $multi_array[$i]['gold2'];
				$temp[$i]['gold3'] = $multi_array[$i]['gold3'];
				
				$multi_array[$i]['id'] = $multi_array[$j]['id'];
				$multi_array[$i]['user_id'] = $multi_array[$j]['user_id'];
				$multi_array[$i]['nick_name'] = $multi_array[$j]['nick_name'];
				$multi_array[$i]['sex'] = $multi_array[$j]['sex'];
				$multi_array[$i]['tx'] = $multi_array[$j]['tx'];
				$multi_array[$i]['gold1'] = $multi_array[$j]['gold1'];
				$multi_array[$i]['gold2'] = $multi_array[$j]['gold2'];
				$multi_array[$i]['gold3'] = $multi_array[$j]['gold3'];
				
				$multi_array[$j]['id'] = $temp[$i]['id'];
				$multi_array[$j]['user_id'] = $temp[$i]['user_id'];
				$multi_array[$j]['nick_name'] = $temp[$i]['nick_name'];
				$multi_array[$j]['sex'] = $temp[$i]['sex'];
				$multi_array[$j]['tx'] = $temp[$i]['tx'];
				$multi_array[$j]['gold1'] = $temp[$i]['gold1'];
				$multi_array[$j]['gold2'] = $temp[$i]['gold2'];
				$multi_array[$j]['gold3'] = $temp[$i]['gold3'];
			}
		}
	}
	return $multi_array;
}
?>