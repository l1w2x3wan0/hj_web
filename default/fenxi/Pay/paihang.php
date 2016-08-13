<?php
$type = !empty($_POST['type']) ? $_POST['type'] : $_GET['type'];
$uid = !empty($_POST['uid']) ? $_POST['uid'] : $_GET['uid'];
if (empty($type)) $type = "1";
/*
$conn = mysql_connect("192.168.1.252:3307", "root","dj2015","fenxi_ben"); 
mysql_select_db("fenxi_ben", $conn); 
mysql_query("SET NAMES utf8");
*/
$conn = mysql_connect("localhost", "root","xiaofuyong","kingflower"); 
mysql_select_db("kingflower", $conn); 
mysql_query("SET NAMES utf8");

if (!empty($type)){
	$sql = "select * from fx_paihang where flag='4'";
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	$user = json_decode($row['tongji'], true);
	//print_r($user);
	//print_r($user);
	if ($type=="1"){
		$sql = "select * from fx_paihang where flag='5'";
		$res = mysql_query($sql);
		$row = mysql_fetch_array($res);
		
		$show = json_decode($row['tongji'], true);
		$h = date("H")+1;
		if ($h > 24){
			$date = date("Y-m-d",strtotime("1 day"));
		}else{
			$date = date("Y-m-d ").$h.":00:00";
		}
		$ts = strtotime($date);
	}elseif ($type=="2"){
		$sql = "select * from fx_paihang where flag='6'";
		$res = mysql_query($sql);
		$row = mysql_fetch_array($res);
		$show = json_decode($row['tongji'], true);
		$date = date("Y-m-d",strtotime("1 day"));
		$ts = strtotime($date);
	}elseif ($type=="3"){
		$sql = "select * from fx_paihang where flag='7'";
		$res = mysql_query($sql);
		$row = mysql_fetch_array($res);
		//$show = json_decode($row['tongji'], true);
		$pai3_show = json_decode($row['tongji'], true);
		
		$show = array();
		foreach ($pai3_show as $key => $val){
			if ($key < 10) $show[$key] = $pai3_show[$key];
		}
		
		$date = date("Y-m-d",strtotime("1 day"));
		$ts = strtotime($date);
	}elseif ($type=="4"){
		$sql = "select * from fx_paihang where flag='9'";
		$res = mysql_query($sql);
		$row = mysql_fetch_array($res);
		//$show = json_decode($row['tongji'], true);
		$pai3_show = json_decode($row['tongji'], true);
		
		$show = array();
		foreach ($pai3_show as $key => $val){
			$show[$key] = $pai3_show[$key];
		}
		
		$date = date("Y-m-d",strtotime("1 day"));
		$ts = strtotime($date);
	}
	
	$flag = 0;
	foreach ($show as $key => $val){
		if ($val['uid']==$uid){
			$flag = $val['place'];
			break;
		} 
	}
	//echo $date;
	$info = array('status' => 1,
				  'paihang' => $show,
				  'flag' => $flag,
				  'ts' => $ts);
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