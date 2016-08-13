<?php
class InterbaseAction extends Action{
	
	public $online_status = 0;  //接口是否需要判断用户在线
	public $Inter;  
	private $key = "6F9he9U*cec4kjc168"; //加密串私钥
	public $response = array();
	
	//运行流程	
	public function index() {
		
		$this->init();
		if ($this->before()) {
			$this->logic();
		}
		$this->after();
	}
	
	//初始检查
	public function init() {	
		
		//实例化模型
		//$this->Inter = D('Interbase');
		//输入校验	
		$post_str = $this->check_sign();
		//判断开关
		$switch = $this->online_switch($post_str['user_id']);
		if ($switch){
			$this->response = $post_str;
			return true;
		}
	}
	
	//条件判断
	public function before() {
		return true;
	}
	
	//运行逻辑	
	public function logic() {
		return true;	
	}
	
	//输出结果	
	public function after() {
		return true;
	}
	
	//验证输入
	public function check_sign(){
		
		//$_POST['params'] = '{"user_id":10321888,"drawtimes":1,"drawtype":2,"sign":"17e24a5b2df706b9df1c36821beb5604"}'; //Bullwheel大转轮测试数据
        //$_POST['params'] = '{"user_id":10321888,"sign":"ef86e31a4c1a99d2cec8ffae5aa77b0d"}'; //Bullwheel大转轮测试数据钻石购买
		//$_POST['params'] = '{"user_id":10321888,"sign":"1aa43d8750e4ae693a1a9ec1827dfd5a","id":"10"}'; //Duihuan兑换测试数据
		//$_POST['params'] = '{"user_id":10321888,"sign":"68a54e2c6dadf865c4d59751e917af4f","GoodsID":12}'; //Diamondbuy钻石购买测试数据
		//$_POST['params'] = '{"user_id":10321888,"sign":"ac5d53d3b0d5f685fed8a859d4992b0d","ordercode":"201512231747473977"}'; //Jinbioff钻石购买测试数据
		//$_POST['params'] = '{"user_id":10321888,"sign":"059ff6446063993fcf2a527854666bb7","ordercode":"201512281117559206"}';  //Jinbibuy钻石购买测试数据
		//$_POST['params'] = '{"user_id":10337998,"sign":"5405418b66700f966f78d59ecc5bb577","diamond":1,"gold":100000}';  //Jinbi钻石购买测试数据
		//$_POST['params'] = '{"user_id":10321888,"code":25858133,"sign":"d745f0acb6ab4cc7bb73f6f2a6d2245d"}';  //Spread钻石购买测试数据
		//$_POST['params'] = '{"sign":"495b39f0eee381b63e6cf7abb8e5883b","user_name":"zjhaaaaac"}';  //用户上线广播
		//$_POST['params'] = '{"sign":"22803f0bbb2d7938b324f69609a5974a","platform":1,"version":"2.0","channel":20000}'; //游戏开关测试数据
		if (!empty($_POST)){
			$post_str = json_decode($_POST['params'], true);
			ksort($post_str);
			$str = "";
			foreach($post_str as $key => $val){
				if ($key != "sign") $str .= (!empty($str)) ? "&".$key."=".urlencode($val) : $key."=".urlencode($val); else $sign = $val;
			}
			$str .= "&key=".$this->key;
				
			Log::write($_POST['params'],'INFO');
			//echo $sign."<br>".$str."<br>".md5($str); exit;
			if ($sign == md5($str) && !empty($sign)){
				return $post_str;
			}else{
				$this->returnError('-1','数据异常，请联系客服');
			}
		}else{
			$this->returnError('-1','输入异常，请联系客服');
		}
	}
	
	//在线开关
	public function online_switch($user_id){
		
		$ONLINE_SWITCH = $this->getDconfig('ONLINE_SWITCH');
		//判断开关状态 
		if ($ONLINE_SWITCH == 1 && $this->online_status == 1){
			//用户在房间则不开始
			$user_online = $this->getUseronline($user_id);
			if ($user_online > 0  and $user_online != 5){
				$this->returnError('-1','房间异常，请联系客服');
			}
		}
		return true;
	}
	
	//返回错误信息
	public function returnError($status, $mesage) {
		
		$msg = array('status' => (int)$status, 'desc' => $mesage);
		$this->printData($msg);
		return true;
	}
	
	//显示输出
	public function returnData($data=''){
		
		$this->printData($data);
		return true;
	}
	
	//显示输出
	public function printData($data){
		
		//echo base64_encode(json_encode($this->response));
		//echo $this->xor_data(json_encode($this->response));
		echo json_encode($data, JSON_UNESCAPED_UNICODE);
		exit(0);
	}
	
	//移位输出
	public function xor_data($string){
		
		$len = strlen($string);                        
		for($i=0;$i<$len;$i++){
			$string[$i] = chr(ord($string[$i])^13);                  
		}
		return $string;
	}
	
    //给服务器发消息
	public function send_service($content, $uid, $color=0, $type=11){
		
		$url = DB_HOST."/Pay/sendmessage.php?showtype=".$type."&uid=".$uid."&color=".$color."&content=".urlencode($content);
		$result = curlGET($url);
		$result = substr($result, -3, 1);
		return $result;
	}
	
	public function changeGold($user_id, $gold, $flag=0, $cate){
		//获取用户当前信息
		$table1 = M(MYTABLE_PRIFIX."user_info");
		$user = $table1->field('gold')->where("user_id=".$user_id)->find();
		if ($flag == 1){
			$result = $table1->where("user_id=".$user_id." and $gold>0 and gold>=$gold")->limit(1)->setDec('gold', $gold);
			$gold_change  = -$gold;
			$gold_after = $user['gold'] - $gold;
		}else{
			$result = $table1->where("user_id=".$user_id." and $gold>0")->limit(1)->setInc('gold', $gold);
			$gold_change  = $gold;
			$gold_after = $user['gold'] + $gold;
		}
		//插入金币日志
		$row = M();
		$sql = " CALL ".MYTABLE_PRIFIX."SP_Log_Write_Gold_Change( $user_id, $cate, ".$user['gold'].", ".$gold_after.", ".$gold_change.", 0, 0, '转盘金币变动' );";
		$result = $row->query($sql);
		return true;
	}
	
	//user_id:用户ID; gift:礼物类型; num:数量; flag标识0增加1减少; cate礼物日志类型
	public function changeGift($user_id, $gift, $num, $flag=0, $cate){
		//获取用户当前信息
		$table1 = M(MYTABLE_PRIFIX."user_info");
		$user = $table1->field($gift)->where("user_id=".$user_id)->find();
		//加汽车
		$aftergold = $user[$gift] + $num;
						
		$change = (int)$num;
		$result = $table1->where("user_id=".$user_id." and $change>0")->limit(1)->setInc('car', $change);
						
		//插入礼物日志
		$liwu = M(MYTABLE_PRIFIX."log_gift_record_log");
		$arr = array();
		$arr['user_id'] = $user_id;
		$arr['operatortime'] = time();
		$arr['disdate'] = date("Y-m-d H:i:s");
		$arr['from_userid'] = 1;
		$arr['giftid'] = $cate;
		$arr['beforenum'] = $user[$gift];
		$arr['changenum'] = $num;
		$arr['afternum'] = $aftergold;
		$result = $liwu->add($arr);
		return true;
	}
	
	//获取用户基本信息
	public function getUserinfo($user_id, $cate=0){
		$table1 = M(MYTABLE_PRIFIX."user_info");
		if ($cate == 1){
			$where = "user_name='".$user_id."'";
		}else{
			$where = "user_id=".$user_id;
		}
		$user = $table1->where($where)->find();
		$user['nickname'] = !empty($user['nickname']) ? $user['nickname'] : $user['nick_name'];
		$table2 = M(MYTABLE_PRIFIX."log_change_user_vip");
		$user_level = $table2->where("user_id=".$user['user_id'])->find();
		$user['showlevel'] = ($user_level['viplevel'] > $user['viplevel']) ? $user_level['viplevel'] : $user['viplevel'];
		//print_r($user);
		return $user;
	}
	
	//获取用户房间信息
	public function getUseronline($user_id){
		$table1 = M(MYTABLE_PRIFIX."user_online");
		$user_online = $table1->where("user_id=".$user_id)->find();
		return $user_online['room_id'];
	}
	
	//获取常规配置信息
	public function getDconfig($key_name){
		$table = M(MYTABLE_PRIFIX."dynamic_config");
		$info = $table->where("key_name='ONLINE_SWITCH'")->find();
		return $info['key_value'];
	}
		
}

	