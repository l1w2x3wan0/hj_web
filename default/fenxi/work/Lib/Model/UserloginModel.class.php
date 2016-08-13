<?php
class UserloginModel extends Model{
    
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