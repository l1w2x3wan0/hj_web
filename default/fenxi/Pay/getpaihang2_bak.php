<?php
header("Content-Type: text/html;charset=utf-8");

$conn1 = mysql_connect("localhost", "root","root","kingflower"); 
mysql_select_db("kingflower", $conn1); 
$conn2 = mysql_connect("huangjiatest.mysql.rds.aliyuncs.com", "hj_test","doojaa2016","zjhmysql"); 
mysql_select_db("zjhmysql", $conn2); 

	//机器人参数
	$sql = "select * from fx_paihang where flag='0'";
	$res = mysql_query($sql, $conn1);
	$row = mysql_fetch_array($res);
	$user = json_decode($row['tongji'], true);
	//财富榜参数
	$sql = "select * from fx_paihang where flag='1'";
	$res = mysql_query($sql, $conn1);
	$row = mysql_fetch_array($res);
	$can1 = json_decode($row['tongji'], true);
	//昨日赢金榜参数
	$sql = "select * from fx_paihang where flag='2'";
	$res = mysql_query($sql, $conn1);
	$row = mysql_fetch_array($res);
	$can2 = json_decode($row['tongji'], true);
	//昨日充值榜参数
	$sql = "select * from fx_paihang where flag='3'";
	$res = mysql_query($sql, $conn1);
	$row = mysql_fetch_array($res);
	$can3 = json_decode($row['tongji'], true);
	//排行第1的金币值
	$sql = "select MAX(gold) as maxgold from user_info where channel=11 and user_id not in (10327830,10328441)";
	$res = mysql_query($sql, $conn2);
	$row = mysql_fetch_array($res);
	$maxgold = $row['maxgold']; 
	//获取配置
	$sql = "select * from config";
	$res = mysql_query($sql, $conn1);
	while ($row = mysql_fetch_array($res)){
		define($row['config_name'], $row['config_value']);
	}
	//print_r($user);
	//生成财富榜
	$gold2 = array();
	$basenum = $maxgold + $can2['jinbi1'];
	$prize_arr = array('a'=>$can2['gailv11'],'b'=>$can2['gailv12'],'c'=>$can2['gailv13'],'d'=>$can2['gailv14'],'e'=>$can2['gailv15']);
	for($i=0; $i<10; $i++){
		$result = get_rand($prize_arr);
		if ($result=="a") {
			$num = rand($can2['bian110'], $can2['bian111']);
		}elseif ($result=="b"){
			$num = rand($can2['bian120'], $can2['bian121']);
		}elseif ($result=="c"){
			$num = rand($can2['bian130'], $can2['bian131']);
		}elseif ($result=="d"){
			$num = rand($can2['bian140'], $can2['bian141']);
		}elseif ($result=="e"){
			$num = rand($can2['bian150'], $can2['bian151']);
		}
		$user[$i]['gold2'] = round($basenum + $basenum * rand(90,100) / 100 * $num / 100);
		//echo $i."**".$user[$i]['gold2']."<br>";
		$gold2[$i] = $user[$i]['gold2'];
	}
	//print_r($user); exit;
	
	//获取真实用户排行
	$day = date("Y-m-d", strtotime("-1 day"));
	$daytime = strtotime($day);
	$endtime = $daytime + 24 * 60 * 60;
	$table = "log_gold_change_log_".date("Ym",$daytime);
	$sql = "select user_id from $table where module=3 and !(user_id>=".ROBERT2_BEGIN." and user_id<=".ROBERT2_END.") and (curtime>$daytime and curtime<$endtime) and changegold>0 GROUP BY user_id";
	$res = mysql_query($sql, $conn2);
	$usergold = array();
	$gold9 = array();
	while ($row = mysql_fetch_array($res)){
		$sql0 = "select SUM(changegold) as maxgold from $table where changegold>0 and module=3 and (curtime>$daytime and curtime<$endtime)  and user_id=".$row['user_id'];
		$res0 = mysql_query($sql0, $conn2);
		$row0 = mysql_fetch_array($res0);
		$gold9[] = $row0['maxgold'];
		$usergold[] = array('user_id' => $row['user_id'],
							'maxgold' => $row0['maxgold']);
 	}
	array_multisort($gold9, SORT_DESC, $usergold);
	
	for ($key=0; $key<10; $key++){
		$sql = "select * from user_info where user_id=".$usergold[$key]['user_id'];
		$res = mysql_query($sql, $conn2);
		$userinfo = mysql_fetch_array($res);
		
		$next = 10 + $key;
		$user[$next]['user_id'] = $userinfo['user_id'];
		$user[$next]['nick_name'] = !empty($userinfo['nickname']) ? $userinfo['nickname'] : $userinfo['nick_name'];
		$user[$next]['sex'] = $userinfo['sex'];
		$user[$next]['tx_srouce'] = $userinfo['head_picture'];
		$user[$next]['tx'] = !empty($userinfo['head_picture']) ? "http://api.pic.kk520.com:9101/work/".$userinfo['head_picture'].".jpg" : 0;
		$user[$next]['gold2'] = $usergold[$key]['maxgold'];
		$gold2[$next] = $usergold[$key]['maxgold'];
	}
	
	array_multisort($gold2, SORT_DESC, $user);
	//print_r($user);
	//exit;
	
	
	//$user1 = array_sort_robert($user,'gold2');
	$show = array();
	for ($key=0; $key<10; $key++){
			$show[$key]['avatar'] = $user[$key]['tx_srouce'];
			$show[$key]['tx'] = $user[$key]['tx'];
			$show[$key]['gender'] = $user[$key]['sex'];
			$show[$key]['nickname'] = $user[$key]['nick_name'];
			$show[$key]['place'] = $key+1;
			$show[$key]['total'] = $user[$key]['gold2'];
			$show[$key]['type'] = 2;
			$show[$key]['uid'] = $user[$key]['user_id'];
			
			$sql = "select viplevel from user_info where user_id=".$show[$key]['uid'];
			$res = mysql_query($sql, $conn2);
			$row = mysql_fetch_array($res);
			$show[$key]['vip_type'] = $row['viplevel'];
	}
	
	$flag = 6;
	$sql = "select count(*) as total from fx_paihang where flag='$flag'";
	$res = mysql_query($sql, $conn1);
	$row = mysql_fetch_array($res);
	if ($row['total']==0){
		$sql0 = "insert into fx_paihang (data, flag, tongji, addtime) values ('".date("Y-m-d")."', '$flag', '".json_encode($show, JSON_UNESCAPED_UNICODE)."', '".time()."')";
	}else{
		$sql0 = "update fx_paihang set data='".date("Y-m-d")."',tongji='".json_encode($show, JSON_UNESCAPED_UNICODE)."',addtime='".time()."' where flag='$flag'";
	}
	//echo $sql0."<br>";
	if (mysql_query($sql0, $conn1)) echo "1"; else echo "0";
	//print_r($show);



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
				$temp[$i]['tx_srouce'] = $multi_array[$i]['tx_srouce'];
				$temp[$i]['gold2'] = $multi_array[$i]['gold2'];
				
				$multi_array[$i]['id'] = $multi_array[$j]['id'];
				$multi_array[$i]['user_id'] = $multi_array[$j]['user_id'];
				$multi_array[$i]['nick_name'] = $multi_array[$j]['nick_name'];
				$multi_array[$i]['sex'] = $multi_array[$j]['sex'];
				$multi_array[$i]['tx'] = $multi_array[$j]['tx'];
				$multi_array[$i]['tx_srouce'] = $multi_array[$j]['tx_srouce'];
				$multi_array[$i]['gold2'] = $multi_array[$j]['gold2'];
				
				$multi_array[$j]['id'] = $temp[$i]['id'];
				$multi_array[$j]['user_id'] = $temp[$i]['user_id'];
				$multi_array[$j]['nick_name'] = $temp[$i]['nick_name'];
				$multi_array[$j]['sex'] = $temp[$i]['sex'];
				$multi_array[$j]['tx'] = $temp[$i]['tx'];
				$multi_array[$j]['tx_srouce'] = $temp[$i]['tx_srouce'];
				$multi_array[$j]['gold2'] = $temp[$i]['gold2'];
			}
		}
	}
	return $multi_array;
}

//经典的概率算法函数
function get_rand($proArr) { 
    $result = ''; 
    //概率数组的总概率精度 
    $proSum = array_sum($proArr); 
    //概率数组循环 
    foreach ($proArr as $key => $proCur) { 
        $randNum = mt_rand(1, $proSum);             //抽取随机数
        if ($randNum <= $proCur) { 
            $result = $key;                         //得出结果
            break; 
        } else { 
            $proSum -= $proCur;                     
        } 
    } 
    unset ($proArr); 
    return $result; 
}
?>