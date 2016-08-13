<?php
class InterAction extends Action{
	
	
	//验证输入
	public function check_sign($online_status=1){
		
		if (!empty($_POST)){
			Log::write(json_encode($_POST),'INFO');
			
			//$_POST['params'] = '{"user_id":10321888,"drawtimes":1,"drawtype":2,"sign":"17e24a5b2df706b9df1c36821beb5604"}'; //Bullwheel大转轮测试数据
            //$_POST['params'] = '{"user_id":10321888,"sign":"ef86e31a4c1a99d2cec8ffae5aa77b0d"}'; //Bullwheel大转轮测试数据钻石购买
			//$_POST['params'] = '{"user_id":10321888,"sign":"1aa43d8750e4ae693a1a9ec1827dfd5a","id":"10"}'; //Duihuan兑换测试数据
			//$_POST['params'] = '{"user_id":10321888,"sign":"68a54e2c6dadf865c4d59751e917af4f","GoodsID":12}'; //Diamondbuy钻石购买测试数据
			//$_POST['params'] = '{"user_id":10321888,"sign":"ac5d53d3b0d5f685fed8a859d4992b0d","ordercode":"201512231747473977"}'; //Jinbioff钻石购买测试数据
			//$_POST['params'] = '{"user_id":10321888,"sign":"059ff6446063993fcf2a527854666bb7","ordercode":"201512281117559206"}';  //Jinbibuy钻石购买测试数据
			//$_POST['params'] = '{"user_id":10337998,"sign":"5405418b66700f966f78d59ecc5bb577","diamond":1,"gold":100000}';  //Jinbi钻石购买测试数据
			//$_POST['params'] = '{"user_id":10321888,"code":25858133,"sign":"d745f0acb6ab4cc7bb73f6f2a6d2245d"}';  //Spread钻石购买测试数据
			//$_POST['params'] = '{"user_id":10321888,"sign":"f1578109f5032420c6b8951607036bc3","paihang":"1"}'; //判断用户上线广播, 0
			//$_POST['params'] = '{"user_id":10321888,"sign":"ef86e31a4c1a99d2cec8ffae5aa77b0d"}';  //获取用户上线广播开关状态
			$post_str = json_decode($_POST['params'], true);
			ksort($post_str);
			$str = "";
			foreach($post_str as $key => $val){
				if ($key != "sign") $str .= (!empty($str)) ? "&".$key."=".urlencode($val) : $key."=".urlencode($val); else $sign = $val;
			}
			$str .= "&key=6F9he9U*cec4kjc168";
				
			
			//echo $sign."<br>".$str."<br>".md5($str); exit;
		
			if ($sign == md5($str) && !empty($sign)){
				
				//判断开关
				$switch = $this->online_switch($post_str['user_id'], $online_status);
				if ($switch){
					$message = array('status' => 1, 'info' => $post_str);
					return $message;
				}
				
			}else{
				$this->answerResquest('-1','数据异常，请联系客服');
			}
		}else{
			$this->assign('left_css',"50");
			$lib_display = "Black:duihuan";
			$this->display($lib_display);
			exit;
			$this->answerResquest('-1','输入异常，请联系客服');
		}
	}
	
	//在线开关
	public function online_switch($user_id, $online_status=1){
		
		$table7 = M(MYTABLE_PRIFIX."dynamic_config");
		$info = $table7->where("key_name='ONLINE_SWITCH'")->find();
		$ONLINE_SWITCH = $info['key_value'];
		
		//判断开关状态 
		if ($ONLINE_SWITCH == 1 && $online_status == 1){
			//用户在房间则不开始
			$table8 = M(MYTABLE_PRIFIX."user_online");
			$user_online = $table8->where("user_id=".$user_id)->find();
			if ($user_online['room_id'] > 0  and $user_online['room_id']!=5){
				$this->answerResquest('-1','房间异常，请联系客服');
			}
		}
		return true;
	}
	
	//显示输出
	public function answerResquest($status, $mesage, $data='', $type='json'){
		$msg = array('status' => (int)$status, 'desc' => $mesage);
		if (!empty($data)) $msg = array_merge($msg, $data);
		if ($type == "json"){
			echo json_encode($msg, JSON_UNESCAPED_UNICODE);
		}else{
			echo $msg;
		}
		exit;
	}

    //
		
}

	