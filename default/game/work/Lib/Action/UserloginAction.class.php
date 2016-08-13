<?php
// 接口管理的文件
class UserloginAction extends InterbaseAction {
	
	private $userlogin;
	private $user;
	
	//条件判断
	public function before() {
		
		//获取用户信息
		$this->user = $this->getUserinfo($this->response['user_name'], 1);
		if (empty($this->user['user_name'])){
			return $this->returnError('-1','用户ID异常');
		}
		//实例化
		//$this->userlogin = D('Userlogin');
		return true;
	}
	
	//运行逻辑
	public function logic() {
		
		//用户登陆
		$user_id = $this->user['user_id'];
		//echo $user_id;
		//获取系统上线开关
		$game_switch = $this->getGameswitch();
		$color = $game_switch[1];
		$color_num = hexdec($color);
		//获取用户上线开关
		$user_switch = $this->getUserswitch($user_id);
		//同时开启，判断用户是否需要发上线广播
		$show = '';
		if ($game_switch[0] == 1 and $user_switch == 1){
			
			//判断用户等级是否大于等于18级
			if ($this->user['showlevel'] >= 18){
				$msg = str_replace("%s", $this->user['nickname'], $game_switch[2]);
				//给服务器发消息
				$result = $this->send_service($msg, $user_id, $color_num);
				if ($result == '1') $show = $msg.'|'.$user_id.'|'.$color_num.'; ';
				//echo $result;
			}
			
			//判断用户是否上财富榜
			$rank1 = $this->getUserrank(5);
			if (in_array($user_id, $rank1)){
				//获取位置
				$position = array_search($user_id, $rank1) + 1;
				//获取消息模板
				$msg1 = $this->getRankingmsg(1, $position);
				$msg1 = str_replace("%s", $this->user['nickname'], $msg1);
				//排第一的给服务器发消息
				if ($position == 1){
					$result = $this->send_service($msg1, $user_id, $color_num);
					if ($result == '1') $show .= $msg1.'|'.$user_id.'|'.$color_num.'; ';
				}
				
			}
			
			//判断用户是否上魅力榜
			/*$rank2 = $this->userlogin->getUserrank(6);
			if (in_array($user_id, $rank2)){
				//获取位置
				$position = array_search($user_id, $rank2) + 1; 
				//获取消息模板
				$msg2 = $this->userlogin->getRankingmsg(2, $position);
				$msg2 = str_replace("%s", $this->user['nickname'], $msg2);
				//给服务器发消息
				$result = $this->send_service($msg2);
				if ($result == '1') $show .= '在魅力榜已发上线广播；';
			}*/
			
			//判断用户是否上昨日充值榜
			$rank3 = $this->getUserrank(7);
			if (in_array($user_id, $rank3)){
				//获取位置
				$position = array_search($user_id, $rank3) + 1;
				//获取消息模板
				$msg3 = $this->getRankingmsg(3, $position);
				$msg3 = str_replace("%s", $this->user['nickname'], $msg3);
				//给服务器发消息
				if ($position == 1){
					$result = $this->send_service($msg3, $user_id, $color_num);
					if ($result == '1') $show .= $msg3.'|'.$user_id.'|'.$color_num.'; ';
				}
			}
		}
		//返回结果数组		
		$result0 = array('status'=>1, 'msg'=>$show);
		return $this->returnData($result0);
	}
	
	//获取系统上线开关，默认开启
	public function getGameswitch(){
		
		$table = M(MYTABLE_PRIFIX."dynamic_config");
		$info = $table->where("key_name='USER_BROADCAST_NEW'")->find();
		$switch = $info['key_value'];
		$switch = explode("_", $switch);
		return $switch;
	}
	
	//获取用户自己开关，默认开启
	public function getUserswitch($user_id){
		
		$table1 = M(MYTABLE_PRIFIX."user_info_config");
		//判断是否有记录
		$table1 = M(MYTABLE_PRIFIX."user_info_config");
		$count = $table1->where("user_id=".$user_id)->count();
		if ($count == 0){
			$paihang = 1;
		}else{
			$info = $table1->where("user_id=".$user_id)->find();
			$paihang = $info['paihang'];
		}
		return $paihang;
	}
	
	//获取用户排行榜USER_ID
	public function getUserrank($flag=5){
		
		$table1 = M(MYTABLE_PRIFIX."fx_paihang");
		$info = $table1->where("flag=".$flag)->find();
		$user_rank = empty($info['tongji']) ? "" :  explode("_", $info['tongji']);
		return $user_rank;
	}
	
	//获取排行榜消息,$cate:1财富榜, 2魅力榜, 3昨日充值榜, $position位置
	public function getRankingmsg($cate, $position=4){
		
		switch ($position){
			case 1:  $names = "冠军"; break;	
			case 2:  $names = "亚军"; break;
			case 3:  $names = "季军"; break;
			default: $names = ($cate == 2) ? "红人" : "名人"; break;
		}
		
		if ($cate == 1){
			if ($position < 4){
				$msg = "财富榜".$names."【%s】隆重登场，我就是土豪，绝不低调！";
			}else{
				$msg = "财富榜".$names."【%s】隆重登场，想要和他做朋友的过来排队啦！";
			}
		}else if ($cate == 2){
			if ($position < 4){
				$msg = "魅力榜".$names."【%s】隆重登场，万千粉丝前来膜拜吧！";
			}else{
				$msg = "魅力榜".$names."【%s】隆重登场，万千粉丝尖叫起来吧！";
			}
		}else if ($cate == 3){
			if ($position < 4){
				$msg = "昨日钻石榜".$names."【%s】隆重登场，这就是高富帅的代言人！";
			}else{
				$msg = "昨日钻石榜".$names."【%s】隆重登场，赶快去和他同桌玩耍吧！";
			}
		}
		return $msg;
	}
	
}