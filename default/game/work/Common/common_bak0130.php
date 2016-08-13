<?php
/*多维数组排序
	$multi_array:多维数组名称
	$sort_key:二维数组的键名
	$sort:排序常量	SORT_ASC || SORT_DESC
	*/
function multi_array_sort($multi_array,$sort_key,$sort=SORT_ASC){ 
	if(is_array($multi_array)){ 
		foreach ($multi_array as $row_array){ 
			if(is_array($row_array)){ 
				$key_array[] = $row_array[$sort_key]; 
			}else{ 
				return false; 
			} 
		} 
	}else{ 
		return false; 
	} 
	array_multisort($key_array,$sort,$multi_array); 
	return $multi_array; 
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

//保存图片
function photo_upload(){
	import("ORG.Net.UploadFile");
    //导入上传类
	$upload = new UploadFile();
	 //设置上传文件大小
	$upload->maxSize = 3292200;
	 //设置上传文件类型
	$upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
	 //设置附件上传目录
	$upload->savePath = './Public/Uploads/';
	 //设置需要生成缩略图，仅对图像文件有效
	//$upload->thumb = true;
	 // 设置引用图片类库包路径
	$upload->imageClassPath = 'ORG.Util.Image';
	 //设置需要生成缩略图的文件后缀
	//$upload->thumbPrefix = 'm_,s_';  //生产2张缩略图
	 //设置缩略图最大宽度
	//$upload->thumbMaxWidth = '400,100';
	 //设置缩略图最大高度
	//$upload->thumbMaxHeight = '400,100';
	 //设置上传文件规则
	$upload->saveRule = 'uniqid';
	 //删除原图
	$upload->thumbRemoveOrigin = true;
	if (!$upload->upload()) {
		//捕获上传异常
		return $this->error($upload->getErrorMsg());
	} else {
		//取得成功上传的文件信息
		$uploadList = $upload->getUploadFileInfo();
		//import("@.ORG.Image");
		//给m_缩略图添加水印, Image::water('原文件名','水印图片地址')
		//Image::water($uploadList[0]['savepath'] . 'm_' . $uploadList[0]['savename'], APP_PATH.'Tpl/Public/Images/logo.png');
		return $uploadList[0]['savename'];
	 }

}

function getIP() {
    if(!$_SESSION['userip']) {
        if (isset($_SERVER)){
            if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
                $_SESSION['userip'] = $_SERVER["HTTP_X_FORWARDED_FOR"];
            } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
                $_SESSION['userip'] = $_SERVER["HTTP_CLIENT_IP"];
            } else {
                $_SESSION['userip'] = $_SERVER["REMOTE_ADDR"];
            }
        } else {
            if (getenv("HTTP_X_FORWARDED_FOR")){
                $_SESSION['userip'] = getenv("HTTP_X_FORWARDED_FOR");
            } else if (getenv("HTTP_CLIENT_IP")) {
                $_SESSION['userip'] = getenv("HTTP_CLIENT_IP");
            } else {
                $_SESSION['userip'] = getenv("REMOTE_ADDR");
            }
        }
    }
    return $_SESSION['userip'];
}


function adminlog($logs,$remark=''){
	$table_name = M('user_logs');
    $data = array();
	$data['logs'] = $logs;
    $data['remark'] = $remark;
    $data['userip'] = get_client_ip();
    $data['username'] = $_SESSION['username'];
    $data['addtime'] = time();
	$table_name->add($data);
}

function remiderTimeArr(){
	$remider_time['0']	 	= "准时提醒";
	$remider_time['1'] 		= "提前1小时";
	$remider_time['24'] 		= "提前1天";
	$remider_time['168'] 		= "提前1周";
	$remider_time['720'] 		= "提前1月";
	return $remider_time;
}

/*
|--------------------------------------------------------------------------
| 自定义公共函数库Helper
|--------------------------------------------------------------------------
|
*/

/**
 * 格式化表单校验消息
 *
 * @param  array $messages 未格式化之前数组
 * @return string 格式化之后字符串
 */
function format_message($messages)
{
    $reason = ' ';
    foreach ($messages->all('<p>:message</p>') as $message) {
        $reason .= $message.' ';
    }
    return $reason;
}

/**
 * 格式化表单校验消息，并进行json数组化预处理
 *
 * @param  array $messages 未格式化之前数组
 * @param array $json 原始json数组数据
 * @return array
 */
function format_json_message($messages, $json)
{
    $reason = format_message($messages);
    $info = ''.$reason;
    $json = array_replace($json, ['info' => $info]);
    return $json;
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
 * 获取请求IP
 * @return string
 */
function getRealIP()
{
    static $realIP;
    if($realIP) return $realIP;
    $ip = getenv('HTTP_CLIENT_IP') ? getenv('HTTP_CLIENT_IP') : getenv('HTTP_X_FORWARDED_FOR');
    preg_match("/[\d\.]{7,15}/", $ip, $match);
    if(isset($match[0])) return $realIP = $match[0];

    $ip = !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
    preg_match("/[\d\.]{7,15}/", $ip, $match);

    return $realIP = isset($match[0]) ? $match[0] : '0.0.0.0';
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

/**
 * curl POST
 */
function curlPOST2($url, $para) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL,$url);
    curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
    curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
    curl_setopt($curl,CURLOPT_POST,true); // post传输数据
    curl_setopt($curl, CURLOPT_TIMEOUT, 10); // 超时时间
    //$para = http_build_query($para);
    curl_setopt($curl,CURLOPT_POSTFIELDS,$para);// post传输数据
    $responseJson = curl_exec($curl);
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

/**
 * 获取登录用户信息，用于登录之后页面显示或验证
 *
 * @param string $ret 限定返回的字段
 * @return string|object 返回登录用户相关字段信息或其ORM对象
 */
function user($ret = 'username')
{
    if (Auth::check()) {
        switch ($ret) {
            case 'username':
                return Auth::user()->username;  //返回登录名
                break;
            case 'id':
                return Auth::user()->id;  //返回用户id
                break;
            case 'object':
                return Auth::user();  //返回User对象
                break;
            default:
                return Auth::user()->username;  //默认返回
                break;
        }
    } else {
        return false;
    }
}

/**
 * 生成对应的结果集
 * @param $list
 * @param string $option
 * @return array
 */
function makeResultKey2Row($list, $option='id')
{
    $result = [];
    foreach ($list as $row) {
        if (isset($row->$option))
            $result[$row->$option] = $row;
    }

    return $result;
}

//函数解释：
//msubstr($str, $start=0, $length, $charset=”utf-8″, $suffix=true)
//$str:要截取的字符串
// $start=0：开始位置，默认从0开始
// $length：截取长度
// $charset=”utf-8″：字符编码，默认UTF－8
// $suffix=true：是否在截取后的字符后面显示省略号，默认true显示，false为不显示
//模版使用：{$vo.title|msubstr=0,5,'utf-8',false}
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=false)  
{  
  if(function_exists("mb_substr")){  
              if($suffix)  
              return mb_substr($str, $start, $length, $charset)."...";  
              else
                   return mb_substr($str, $start, $length, $charset);  
         }  
         elseif(function_exists('iconv_substr')) {  
             if($suffix)  
                  return iconv_substr($str,$start,$length,$charset)."...";  
             else
                  return iconv_substr($str,$start,$length,$charset);  
         }  
         $re['utf-8']   = "/[x01-x7f]|[xc2-xdf][x80-xbf]|[xe0-xef]
                  [x80-xbf]{2}|[xf0-xff][x80-xbf]{3}/";  
         $re['gb2312'] = "/[x01-x7f]|[xb0-xf7][xa0-xfe]/";  
         $re['gbk']    = "/[x01-x7f]|[x81-xfe][x40-xfe]/";  
         $re['big5']   = "/[x01-x7f]|[x81-xfe]([x40-x7e]|xa1-xfe])/";  
         preg_match_all($re[$charset], $str, $match);  
         $slice = join("",array_slice($match[0], $start, $length));  
         if($suffix) return $slice."…";  
         return $slice;
}

//生成MD5，json函数
function md5_json($version){
	//date_default_timezone_set("Asia/Shanghai");
	define("DS", DIRECTORY_SEPARATOR);

	$app_dir = dirname(__FILE__);
	//echo $app_dir."<br>";
	//去掉最后一个缓存目录
	$temp = explode(DS, $app_dir);
	$length = count($temp);
	$temp_str = "";
	for($i=0; $i<$length-2; $i++){
		$temp_str .= ($i==0) ? $temp[$i] : "/".$temp[$i];
	}
	$source = $temp_str . DS . "apk" . DS . $version . DS;
	
	//echo $source."<br>"; 
	//$source = "apk" . DS . $version . DS;
    //echo $source."<br>";
	//echo $temp_str."<br>";	exit;
	$res_list_file = $temp_str."/apk/md5/".$version.".json";

	$excludes = '.DS_Store,main.lua';
	$excludes = explode(",", $excludes);
	$excludes[] = $res_list_file;

	$list = array();

	$list["timestamp"] = time();

	$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

	foreach($objects as $filepath => $info){
		$filename = $info->getFileName();
		$filepath = $info->getPathName();
		if($info->isDir() || in_array($filename, $excludes) ){
			continue;
		}
		# 去除基本路径
		$file = str_replace($source , "" , $filepath);
		$file = str_replace('\\' , "/" , $file);
		//printf("$file====> ",$file);
		$crc32 = crc32(file_get_contents($filepath));
		//printf("$crc32====> ",$crc32);
		$size  = filesize($filepath);
		$list[$file] = array( $crc32, $size);
		# $list[] = sprintf("%s,%s,%s", $file, $crc32, $size);
	}


	$str = json_encode($list);


	# $str = implode("\n", $list);
	# $str = sprintf("-- code:%s,create at:%s\n" , crc32($str), date("Y-m-d H:i:s")) . $str;

	# file_put_contents($app_dir . DS . "__release/release" . DS . $res_list_file, $str);
	if (file_put_contents($res_list_file, $str)) return $res_list_file; else return false;

	//printf("\e[0;32m file: %s create success!\n\n", $res_list_file);
}

//生成2个版本之间的差异包
function version_chayi($ver1, $ver2){
	$file1 = "apk/md5/".$ver1.".json";
	$file2 = "apk/md5/".$ver2.".json";
	//echo file_get_contents($file1); exit;

	$result1 = json_decode(file_get_contents($file1), true);
	$result2 = json_decode(file_get_contents($file2), true);
	//print_r($result2);
	//exit;
	$update = "apk/update/".$ver1;
	if (!file_exists($update)) mkdir($update);
	$update_now = $update."/".$ver2;
	if (!file_exists($update_now)) mkdir($update_now);
	//echo $update."**".$update_now; exit;
	foreach ($result2 as $key2 => $val2){
		if ($key2 != "timestamp"){
			if (!empty($result1[$key2])){
				if (array_diff($val2, $result1[$key2])){
					$flag = 1;
				}else{
					$flag = 0;
				}
			}else{
				$flag = 1;
			}
			if ($flag == 1){
				//echo "不同的MD5：".$key2."=>";
				//print_r($val2);
				
				$dir = $update_now;
				$mulu = explode("/", $key2);
				$length = count($mulu);
				
				for($i=0; $i<$length-1; $i++){
					if ($i == 0){
						$dir = $update_now."/".$mulu[$i];
					}else{
						$dir .= "/".$mulu[$i];
					}
					if (!file_exists($dir)) mkdir($dir);
				}
				$source = "apk/".$ver2."/".$key2;
				$target = $dir."/".$mulu[$length-1];
				//echo $source."=>".$target."<br>"; 
				copy($source,$target);
			}else{
				//echo "相同的MD5：".$key2."=>";
				//print_r($val2);
			}
		}
		//echo $key."=>".$val."<br>";
	}
	return $update_now;
}