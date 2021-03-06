<?php
class InterbaseModel extends Model{
    
	//user_id:用户ID; gold:金币数量; flag标识0增加1减少; cate金币日志类型
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
		$user_level = $table2->where("user_id=".$user_id)->find();
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