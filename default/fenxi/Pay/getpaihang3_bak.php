<?php
header("Content-Type: text/html;charset=utf-8");
/*
$conn1 = mysql_connect("localhost", "web_local","localWEBphp2016!@#","kingflower"); 
mysql_select_db("kingflower", $conn1); 
$conn2 = mysql_connect("rdse2o1kfrn2cfo4z618.mysql.rds.aliyuncs.com", "webhj","HJdianjiaWEB2016","zjhmysql"); 
mysql_select_db("zjhmysql", $conn2); 
*/
$conn1 = mysql_connect("192.168.1.252:3307", "root","dj2015","fenxi_ben"); 
mysql_select_db("fenxi_ben", $conn1); 
mysql_query("SET NAMES utf8");
$conn2 = mysql_connect("192.168.1.252:3307", "root","dj2015","kingflower"); 
mysql_select_db("kingflower", $conn2); 
mysql_query("SET NAMES utf8");

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
	$sql = "select * from fx_paihang where flag='8'";
	$res = mysql_query($sql, $conn1);
	$row = mysql_fetch_array($res);
	$can3 = json_decode($row['tongji'], true);
	//获取配置
	$sql = "select * from config";
	$res = mysql_query($sql, $conn1);
	while ($row = mysql_fetch_array($res)){
		define($row['config_name'], $row['config_value']);
	}
	
	//print_r($user);
	//生成财富榜
	$gold3 = array();
	$prize_arr = array('a'=>$can3['gailv11'],'b'=>$can3['gailv12'],'c'=>$can3['gailv13'],'d'=>$can3['gailv14'],'e'=>$can3['gailv15'],'f'=>$can3['gailv16']);
	for($i=0; $i<20; $i++){
		$result = get_rand($prize_arr);
		if ($result=="a") {
			$num = rand($can3['bian110'], $can3['bian111']);
		}elseif ($result=="b"){
			$num = rand($can3['bian120'], $can3['bian121']);
		}elseif ($result=="c"){
			$num = rand($can3['bian130'], $can3['bian131']);
		}elseif ($result=="d"){
			$num = rand($can3['bian140'], $can3['bian141']);
		}elseif ($result=="e"){
			$num = rand($can3['bian150'], $can3['bian151']);
		}elseif ($result=="f"){
			$num = rand($can3['bian160'], $can3['bian161']);
		} 
		if ($num % 2==1) $num = $num + 1;
		$user[$i]['gold3'] = $num;
		//echo $i."**".$user[$i]['gold1']."<br>";
		$gold3[$i] = $num;
	}
	
	//获取真实用户排行
	$day = date("Y-m-d", strtotime("-1 day"));
	$daytime = strtotime($day);
	$endtime = $daytime + 24 * 60 * 60;
	$sql = "select userid from user_pay_log where pay_date>='".date("Y-m-d H:i:s",$daytime)."' and pay_date<'".date("Y-m-d H:i:s",$endtime)."' and success=1 GROUP BY userid";
	//echo $sql; exit; 
	$res = mysql_query($sql, $conn2);
	$usergold = array();
	$gold9 = array();
	$i1 = 0;
	$i2 = 0;
	$rand_cz = rand(1000,1300);
	$gold9[$i1] = $rand_cz;
	$usergold[$i2] = array('user_id' => 10327817,
						   'maxgold' => $rand_cz);
	while ($row = mysql_fetch_array($res)){
		$i1++; $i2++;
		$sql0 = "select SUM(money) as maxgold from user_pay_log where pay_date>='".date("Y-m-d H:i:s",$daytime)."' and pay_date<'".date("Y-m-d H:i:s",$endtime)."' and success=1 and userid=".$row['userid'];
		$res0 = mysql_query($sql0, $conn2);
		$row0 = mysql_fetch_array($res0);
		$gold9[$i1] = $row0['maxgold']/100;
		$usergold[$i2] = array('user_id' => $row['userid'],
							   'maxgold' => $row0['maxgold']/100);
 	}
	//print_r($usergold); //exit;
	array_multisort($gold9, SORT_DESC, $usergold);
	//print_r($usergold); exit;
	for ($key=0; $key<10; $key++){
		$sql = "select * from user_info where user_id=".$usergold[$key]['user_id'];
		$res = mysql_query($sql, $conn2);
		$userinfo = mysql_fetch_array($res);
		
		$next = 20 + $key;
		$user[$next]['user_id'] = $userinfo['user_id'];
		$user[$next]['nick_name'] = !empty($userinfo['nickname']) ? $userinfo['nickname'] : $userinfo['nick_name'];
		$user[$next]['sex'] = $userinfo['sex'];
		$user[$next]['tx_srouce'] = $userinfo['head_picture'];
		$user[$next]['tx'] = !empty($userinfo['head_picture']) ? "http://api.pic.kk520.com:9101/work/".$userinfo['head_picture'].".jpg" : 0;
		$user[$next]['gold3'] = $usergold[$key]['maxgold'];
		$gold3[$next] = $usergold[$key]['maxgold'];
	}
	
	array_multisort($gold3, SORT_DESC, $user);
	//print_r($user); exit;
	//$user1 = array_sort_robert($user,'gold3');
	$show = array();
	$user_service = "";
	for ($key=0; $key<10; $key++){
			$show[$key]['avatar'] = $user[$key]['tx_srouce'];
			$show[$key]['tx'] = $user[$key]['tx'];
			$show[$key]['gender'] = $user[$key]['sex'];
			$show[$key]['nickname'] = $user[$key]['nick_name'];
			$show[$key]['place'] = $key+1;
			$show[$key]['total'] = $user[$key]['gold3'];
			$show[$key]['type'] = 3;
			$show[$key]['uid'] = $user[$key]['user_id'];
			
			$sql = "select IF(u.viplevel>=IFNULL(c.viplevel,0),u.viplevel,c.viplevel) as maxlevel from user_info as u left join `log_change_user_vip` as c on u.`user_id` =c.`user_id`  where u.user_id=".$user[$key]['user_id'];
			$res = mysql_query($sql, $conn2);
			$row = mysql_fetch_array($res);
			$show[$key]['vip_type'] = $row['maxlevel'];
			
			$user_service .= empty($user_service) ? $user[$key]['user_id'] : "_".$user[$key]['user_id'];
			
			//通知接口
			/*$para = array();
			$para['order'] = "25".date("ymhis").rand(1000,9999);
			$para['money'] = (int)($user[$key]['gold3']*100);
			$para['payment'] = 101;
			$para['goodsId'] = 8;
			$para['userData'] = "";
			$para['status'] = "1";
			$para['userId'] = $user[$key]['user_id'];
			$url = "http://dj777.f3322.org:9004";
			//echo $para['userId']."**".$para['money']."<br>";
			curlPOST2($url, json_encode($para));*/
			
	}
	//print_r($show);
	$flag = 7;
	$sql = "select count(*) as total from fx_paihang where flag='$flag'";
	$res = mysql_query($sql, $conn1);
	$row = mysql_fetch_array($res);
	if ($row['total']==0){
		$sql0 = "insert into fx_paihang (data, flag, tongji, addtime) values ('".date("Y-m-d")."', '$flag', '".json_encode($show, JSON_UNESCAPED_UNICODE)."', '".time()."')";
	}else{
		$sql0 = "update fx_paihang set data='".date("Y-m-d")."',tongji='".json_encode($show, JSON_UNESCAPED_UNICODE)."',addtime='".time()."' where flag='$flag'";
	}
	if (mysql_query($sql0, $conn1)) echo "1"; else echo "0";
	//print_r($show);
	//echo $sql0;
	
	$sql = "select count(*) as total from fx_paihang where flag='$flag'";
	$res = mysql_query($sql, $conn2);
	$row = mysql_fetch_array($res);
	if ($row['total']==0){
		$sql0 = "insert into fx_paihang (data, flag, tongji, addtime) values ('".date("Y-m-d")."', '$flag', '".$user_service."', '".time()."')";
	}else{
		$sql0 = "update fx_paihang set data='".date("Y-m-d")."',tongji='".$user_service."',addtime='".time()."' where flag='$flag'";
	}
	//echo $sql0;
	if (mysql_query($sql0, $conn2)) echo "1"; else echo "0";


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
				$temp[$i]['gold3'] = $multi_array[$i]['gold3'];
				
				$multi_array[$i]['id'] = $multi_array[$j]['id'];
				$multi_array[$i]['user_id'] = $multi_array[$j]['user_id'];
				$multi_array[$i]['nick_name'] = $multi_array[$j]['nick_name'];
				$multi_array[$i]['sex'] = $multi_array[$j]['sex'];
				$multi_array[$i]['tx'] = $multi_array[$j]['tx'];
				$multi_array[$i]['tx_srouce'] = $multi_array[$j]['tx_srouce'];
				$multi_array[$i]['gold3'] = $multi_array[$j]['gold3'];
				
				$multi_array[$j]['id'] = $temp[$i]['id'];
				$multi_array[$j]['user_id'] = $temp[$i]['user_id'];
				$multi_array[$j]['nick_name'] = $temp[$i]['nick_name'];
				$multi_array[$j]['sex'] = $temp[$i]['sex'];
				$multi_array[$j]['tx'] = $temp[$i]['tx'];
				$multi_array[$j]['tx_srouce'] = $temp[$i]['tx_srouce'];
				$multi_array[$j]['gold3'] = $temp[$i]['gold3'];
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
?>