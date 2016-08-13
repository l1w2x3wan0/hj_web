<?php
class BullModel extends Model{
    
	//获取当前抽奖配置:cate概率类型:freerate免费概率，goldrate金币概率，diamondrate钻石概率;version配置版本
	public function getLotteryrate($cate, $version){
		
		$table = M(MYTABLE_PRIFIX."profile_lottery_draw_config");
		$lottery = $table->order("version desc,goodsid")->where('version='.$version)->limit(0,12)->select();
		$prize = array();
		foreach($lottery as $key => $val){
			$prize[$val['goodsid']] = $val[$cate] / 100;
		}
		return $prize;
	}
	
	//获取当前抽奖商品
	public function getLottery($goodsid){
		
		$table = M(MYTABLE_PRIFIX."profile_lottery_draw_config");
		$lottery = $table->order("version desc,goodsid")->where('goodsid='.$goodsid)->find();
		return $lottery;
	}
	
	//插入大转盘抽奖日志
	public function addLotterylog($data){
		
		$table = M(MYTABLE_PRIFIX."log_lotterydraw_record_log");
		return $table->add($data);
	}
	
	//给用户免费抽奖券减1
    public function decUserlottery($user_id){
		
		$table = M(MYTABLE_PRIFIX."user_info");
		$result = $table->where("user_id=".$user_id." and lotterydraw_count>0")->limit(1)->setDec('lotterydraw_count', 1);
		return $result;
	}
	
	//插入幸运榜
	public function addLotteryrecord($user, $awardvalue){
		
		$table6 = M(MYTABLE_PRIFIX."user_lotterydraw_record");
		$count9 = $table6->where('user_id='.$user['user_id'])->count();
		if ($count9 == 0){
			$arr = array();
			$arr['user_id'] = $user['user_id'];
			$arr['nickname'] = !empty($user['nickname']) ? $user['nickname'] : $user['nick_name'];
			$arr['headerpic'] = $user['head_picture'];
			$arr['wingold'] = $awardvalue;
			$result = $table6->add($arr);
		}else{
			$result = $table6->where("user_id=".$user_id." and $awardvalue>0")->limit(1)->setInc('wingold', $awardvalue);
		} 
	}
	
	//给用户加奖券
	public function addUserticket($user_id, $ticket){
		
		$table1 = M(MYTABLE_PRIFIX."user_info");
		$change = (int)$ticket;
		$result = $table1->where("user_id=".$user_id." and $change>0")->limit(1)->setInc('ticket', $change);
		return $result;
	}
	
	//获取用户最新一次抽奖时间
	public function getLotterylog($user_id){
		
		$table1 = M(MYTABLE_PRIFIX."log_lotterydraw_record_log");
		$info = $table1->where('user_id='.$user_id)->order('operator desc')->limit(1)->find();
		return $info['operator'];
	}
}