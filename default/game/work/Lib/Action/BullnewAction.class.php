<?php
// 接口管理的文件
class BullnewAction extends InterbaseAction {
	
	private $Bull;
	private $user;
	
	//条件判断
	public function before() {
		
		//传入的抽奖次数不对
		if (!(in_array($this->response['drawtimes'], array(1,2,5,10)))){
			return $this->returnError('-1','输入异常，请联系客服');
		}
		
		//获取用户信息
		$this->user = $this->Inter->getUserinfo($this->response['user_id']);
		if (empty($this->user['user_name'])){
			return $this->returnError('-1','用户ID异常');
		}
		
		//实例化
		$this->Bull = D('Bull');
		
		//抽奖间隔不能小于10秒
		$operator = $this->Bull->getLotterylog($this->response['user_id']);
		if (time() - $operator < 10){
			return $this->returnError('-1','时间间隔太密，请稍等再试');
		}
		return true;
	}
	
	//运行逻辑
	public function logic() {
		
		$user_id = $this->response['user_id'];
		$drawtype = $this->response['drawtype'];
		$drawtimes = $this->response['drawtimes'];
		
		//如果是免费抽奖，判断当前免费抽奖次数
		if ($drawtype == "1"){
			//免费抽奖次数用完
			if ($this->user['lotterydraw_count'] < 1){
				return $this->returnError('-1','当天免费次数已用完');
			}
			//获取免费抽奖概率
			$prize = $this->Bull->getLotteryrate('freerate', 2);
		}else if ($drawtype == "2"){
			//金币抽奖，判断用户身上金币是否足够
			$jinbi = 10000 * $drawtimes;
			if ($jinbi > $this->user['gold']){
				return $this->returnError('-1','金币不够');
			}
			//获取金币抽奖概率
			$prize = $this->Bull->getLotteryrate('goldrate', 2);
		}else{
			//非免费、金币抽奖异常
			return $this->returnError('-1','类型异常');
		}
		$rand_goodsid = get_myrand($prize);
		//获取奖品信息
		$lottery = $this->Bull->getLottery($rand_goodsid);
		$awardtype = $lottery['awardtype'];
		$awardvalue = $lottery['awardnum'] * $drawtimes;
				
		//超过1000W异常
		if ($awardvalue > 10000000){
			return $this->returnError('-1','输入异常，请联系客服');
		}
		
		//插入大转盘抽奖日志
		$arr = array('user_id'=>$user_id, 'drawtype'=>$drawtype, 'drawtimes'=>$drawtimes, 'awardtype'=>$awardtype, 'awardvalue'=>$awardvalue, 'operator'=>time());
		$result = $this->Bull->addLotterylog($arr);
		if ($result){
			//开始处理用户抽到礼物
			if ($drawtype == "1"){
				//用户免费抽奖券-1
				$result_lottery = $this->Bull->decUserlottery($user_id);
			}else if ($drawtype == "2"){
				//减去用户花费金币
				$result_lottery = $this->Inter->changeGold($user_id, $jinbi, 1, 20);
			}
			//奖券、金币未扣成功，返回异常
			if ($result_lottery <= 0){
				return $this->returnError('-1','输入异常，请联系客服处理');
			}
			//返回结果数组		
			$result0 = array('status'=>1, 'goodsid'=>$rand_goodsid, 'gold'=>0, 'car'=>0, 'yacht'=>0, 'ticket'=>0);
			//根据得奖类型发奖
			if ($awardtype == 1){
				//给用户增加金币
				$result = $this->Inter->changeGold($user_id, $awardvalue, 0, 20);
				//插入幸运榜
				$result = $this->Bull->addLotteryrecord($this->user, $awardvalue);
				$result0['gold'] = (int)$awardvalue;
			}else if ($awardtype == 3){
				//加汽车
				$result = $this->Inter->changeGift($user_id, 'car', $awardvalue, 0, 3);
				$result0['car'] = (int)$awardvalue;
			}else if ($awardtype == 4){
				//加飞机
				$result = $this->Inter->changeGift($user_id, 'yacht', $awardvalue, 0, 5);
				$result0['yacht'] = (int)$awardvalue;
			}else if ($awardtype == 5){
				//加奖券
				$result = $this->Bull->addUserticket($user_id, $awardvalue);
				$result0['ticket'] = (int)$awardvalue;
			}
			return $this->returnData($result0);
		}else{
			return $this->returnError('-1', '数据异常，请联系客服');
		} 
	}
	
}