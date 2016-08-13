<?php
header("Content-Type: text/html;charset=utf-8");
/*
$conn1 = mysql_connect("192.168.1.252:3307", "root","dj2015","fenxi_ben"); 
mysql_select_db("fenxi_ben", $conn1); 
mysql_query("SET NAMES utf8");
$conn2 = mysql_connect("192.168.1.252:3307", "root","dj2015","kingflower"); 
mysql_select_db("kingflower", $conn2); 
mysql_query("SET NAMES utf8");
*/

$conn1 = mysql_connect("localhost", "root","xiaofuyong","kingflower"); 
mysql_select_db("kingflower", $conn1); 
mysql_query("SET NAMES utf8");
$conn2 = mysql_connect("huangjiatest.mysql.rds.aliyuncs.com", "huangjia","doojaa2016","zjhmysql"); 
mysql_select_db("zjhmysql", $conn2); 
mysql_query("SET NAMES utf8");

$sql = "select * from config";
$res = mysql_query($sql, $conn1);
while ($row = mysql_fetch_array($res)){
	define($row['config_name'], $row['config_value']);
}

$sql = "select * from dynamic_config";
$res = mysql_query($sql, $conn2);
while ($row = mysql_fetch_array($res)){
	define($row['key_name'], $row['key_value']);
}

$show = array();
$gold9 = array();
$sql1 = " !((user_id>=".ROBERT1_BEGIN." and user_id<".ROBERT1_END.") or (user_id>=".ROBERT2_BEGIN." and user_id<=".ROBERT2_END."))";
$key=0;

//财富榜限制UID
$sql = "select * from fx_paihang where flag='1'";
$res = mysql_query($sql, $conn1);
$row = mysql_fetch_array($res);
$can2 = $row['tongji'];
if (!empty($can2)){
	$sql1 .= " and user_id not in ($can2)";
}
$shownum = RANKING_ASSETS;
$shownum = !empty($shownum) ? $shownum : 50;
$sql = "select *,(deposit+gold) as caifu from user_info where $sql1 order by gold DESC LIMIT $shownum";
$res = mysql_query($sql, $conn2);
$sql1 .= " and user_id not in (";
while($row = mysql_fetch_array($res)){
	$nick = empty($row['nickname']) ? $row['nick_name'] : $row['nickname'];
	$nick = str_replace("\n", "", $nick);
	$nick = str_replace('"', "", $nick);
	
	$small_img =  "http://api.pic.kk520.com:9101/work/small/".$row['head_picture'].".jpg";
	$big_img =  "http://api.pic.kk520.com:9101/work/".$row['head_picture'].".jpg";
	//if (file_exists($small_img)) $img = $small_img; else $img = $big_img;
	$img = $small_img;
	
	$show[$key]['avatar'] = $row['head_picture'];
	$show[$key]['tx'] = !empty($row['head_picture']) ? $img : "";
	$show[$key]['gender'] = $row['sex'];
	$show[$key]['nickname'] = $nick;
	$show[$key]['place'] = $key+1;
	$show[$key]['total'] = (int)$row['gold'];
	$show[$key]['type'] = 1;
	$show[$key]['uid'] = $row['user_id'];
	//$show[$key]['vip_type'] = $row['viplevel'];
	
	$sql0 = "select IF(u.viplevel>=IFNULL(c.viplevel,0),u.viplevel,c.viplevel) as maxlevel from user_info as u left join `log_change_user_vip` as c on u.`user_id` =c.`user_id`  where u.user_id=".$row['user_id'];
	$res0 = mysql_query($sql0, $conn2);
	$row0 = mysql_fetch_array($res0);
	$show[$key]['vip_type'] = $row0['maxlevel'];
	
	//$sql1 .= ($key==0) ? $row['user_id'] : ",".$row['user_id'];
	//$gold9[] = (int)$row['gold'];
	$key++;
}
$sql1 .= ")";

$user = array();
$user_service = "";
for ($key=0; $key<$shownum; $key++){
	
	$user[$key]['avatar'] = $show[$key]['avatar'];
	$user[$key]['tx'] = $show[$key]['tx'];
	$user[$key]['gender'] = $show[$key]['gender'];
	$user[$key]['nickname'] = $show[$key]['nickname'];
	$user[$key]['place'] = $key+1;
	$user[$key]['total'] = $show[$key]['total'];
	$user[$key]['type'] = $show[$key]['type'];
	$user[$key]['uid'] = $show[$key]['uid'];
	$user[$key]['vip_type'] = $show[$key]['vip_type'];
	
	$user_service .= empty($user_service) ? $show[$key]['uid'] : "_".$show[$key]['uid'];
}

//print_r($user); //exit;
	//echo json_encode($show, JSON_UNESCAPED_UNICODE);
	
	$flag = 5;
	$sql = "select count(*) as total from fx_paihang where flag='$flag'";
	$res = mysql_query($sql, $conn1);
	$row = mysql_fetch_array($res);
	if ($row['total']==0){
		$sql0 = "insert into fx_paihang (data, flag, tongji, addtime) values ('".date("Y-m-d")."', '$flag', '".json_encode($user, JSON_UNESCAPED_UNICODE)."', '".time()."')";
	}else{
		$sql0 = "update fx_paihang set data='".date("Y-m-d")."',tongji='".json_encode($user, JSON_UNESCAPED_UNICODE)."',addtime='".time()."' where flag='$flag'";
	}
	//echo $sql0;
	if (mysql_query($sql0, $conn1)) echo "1"; else echo "0";
	//print_r($show);

	$sql = "select count(*) as total from fx_paihang where flag='$flag'";
	$res = mysql_query($sql, $conn2);
	$row = mysql_fetch_array($res);
	if ($row['total']==0){
		$sql0 = "insert into fx_paihang (data, flag, tongji, addtime) values ('".date("Y-m-d")."', '$flag', '".$user_service."', '".time()."')";
	}else{
		$sql0 = "update fx_paihang set data='".date("Y-m-d")."',tongji='".$user_service."',addtime='".time()."' where flag='$flag'";
	}
	//echo $sql0;
	if (mysql_query($sql0, $conn2)) echo "2"; else echo "3";

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
				$temp[$i]['gold1'] = $multi_array[$i]['gold1'];
				
				$multi_array[$i]['id'] = $multi_array[$j]['id'];
				$multi_array[$i]['user_id'] = $multi_array[$j]['user_id'];
				$multi_array[$i]['nick_name'] = $multi_array[$j]['nick_name'];
				$multi_array[$i]['sex'] = $multi_array[$j]['sex'];
				$multi_array[$i]['tx'] = $multi_array[$j]['tx'];
				$multi_array[$i]['tx_srouce'] = $multi_array[$j]['tx_srouce'];
				$multi_array[$i]['gold1'] = $multi_array[$j]['gold1'];
				
				$multi_array[$j]['id'] = $temp[$i]['id'];
				$multi_array[$j]['user_id'] = $temp[$i]['user_id'];
				$multi_array[$j]['nick_name'] = $temp[$i]['nick_name'];
				$multi_array[$j]['sex'] = $temp[$i]['sex'];
				$multi_array[$j]['tx'] = $temp[$i]['tx'];
				$multi_array[$j]['tx_srouce'] = $temp[$i]['tx_srouce'];
				$multi_array[$j]['gold1'] = $temp[$i]['gold1'];
			}
		}
	}
	return $multi_array;
}

function encode_json($str){  
    $code = json_encode($str);  
    return preg_replace("#\\\u([0-9a-f]+)#ie", "iconv('UCS-2', 'UTF-8', pack('H4', '\\1'))", $code);  
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