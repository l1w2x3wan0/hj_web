<?php
// 接口管理的文件
class BullAction extends InterAction {
	
	protected $online_status = 1;  //接口是否需要判断用户在线
	
	public function index(){
		
		header("Content-type:text/html;charset=utf-8");
		$check = $this->check_sign();
		if ($check['status'] == 1){

				$user_id = $check['info']['user_id'];
				$drawtype = $check['info']['drawtype'];
				$drawtimes = $check['info']['drawtimes'];
				
				if (!(in_array($drawtimes, array(1,2,5,10)))){
					return $this->answerResquest('-1','输入异常，请联系客服');
				}
				
				//实例化
				$Inter = D('Inter');
				$Bull = D('Bull');
				
				//获取用户信息
				$user = $Inter->getUserinfo($user_id);
				if (empty($user['user_name'])){
					return $this->answerResquest('-1','用户ID异常');
				}
				
				//如果是免费抽奖，判断当前免费抽奖次数
				if ($drawtype == "1"){
					//免费抽奖次数用完
					if ($user['lotterydraw_count'] < 1){
						return $this->answerResquest('-1','当天免费次数已用完');
					}
					//获取免费抽奖概率
					$prize1 = $Bull->getLotteryrate('freerate', 2);
					$rand_goodsid = get_myrand($prize1);
				}else if ($drawtype == "2"){
					//金币抽奖，判断用户身上金币是否足够
					$jinbi = 10000 * $drawtimes;
					if ($jinbi > $user['gold']){
						return $this->answerResquest('-1','金币不够');
					}
					//获取金币抽奖概率
					$prize2 = $Bull->getLotteryrate('goldrate', 2);
					$rand_goodsid = get_myrand($prize2);
				}
				
				//获取奖品信息
				$lottery = $Bull->getLottery($rand_goodsid);
				$awardtype = $lottery['awardtype'];
				$awardvalue = $lottery['awardnum'] * $drawtimes;
				
				//超过1000W异常
				if ($awardvalue > 10000000){
					return $this->answerResquest('-1','输入异常，请联系客服');
				}
				
				//插入大转盘抽奖日志
				$arr = array('user_id'=>$user_id, 'drawtype'=>$drawtype, 'drawtimes'=>$drawtimes, 'awardtype'=>$awardtype, 'awardvalue'=>$awardvalue, 'operator'=>time());
				$result = $Bull->addLotterylog($arr);
				if ($result){
					//开始处理用户抽到礼物
					if ($drawtype == "1"){
						//用户免费抽奖券-1
						$result = $Bull->decUserlottery($user_id);
						if ($result <= 0){
							return $this->answerResquest('-1','输入异常，请联系客服处理');
						}
					}else if ($drawtype == "2"){
						//减去用户花费金币
						$result = $Inter->changeGold($user_id, $jinbi, 1, 20);
						if ($result <= 0){
							return $this->answerResquest('-1','输入异常，请联系客服处理');
						}
					}
					
					$result0 = array('status'=>1, 'goodsid'=>$rand_goodsid, 'gold'=>0, 'car'=>0, 'yacht'=>0, 'ticket'=>0);
					//根据得奖类型发奖
					if ($awardtype == 1){
						//变化后加金币
						$aftergold = $user['gold'] + $awardvalue;
						//给用户增加金币
						$result = $Inter->changeGold($user_id, $awardvalue, 0, 20);
						//插入幸运榜
						$result = $Bull->addLotteryrecord($user, $awardvalue);
						$result0['gold'] = (int)$awardvalue;
					}else if ($awardtype == 3){
						//加汽车
						$result = $Inter->changeGift($user_id, 'car', $awardvalue, 0, 3);
						$result0['car'] = (int)$awardvalue;
					}else if ($awardtype == 4){
						//加飞机
						$result = $Inter->changeGift($user_id, 'yacht', $awardvalue, 0, 5);
						$result0['yacht'] = (int)$awardvalue;
					}else if ($awardtype == 5){
						//加奖券
						$result = $Bull->addUserticket($user_id, $awardvalue);
						$result0['ticket'] = (int)$awardvalue;
					}
					return $this->answerResquest('1', '', $result0);
					
				}else{
					return $this->answerResquest('-1', '数据异常，请联系客服');
				} 

		}
	}
	
	public function zuan(){
		header("Content-type:text/html;charset=utf-8");

        $check = $this->check_sign();
        if ($check['status'] == 1){

				$user_id = $check['info']['user_id'];;
				//$drawtype = $post_str['drawtype'];
				//$drawtimes = $post_str['drawtimes'];
				$table1 = M(MYTABLE_PRIFIX."user_info");
				$table2 = M(MYTABLE_PRIFIX."profile_vip_level_configure");
				$table3 = M(MYTABLE_PRIFIX."log_lotterydraw_record_log");
				$table4 = M(MYTABLE_PRIFIX."profile_lottery_draw_config");
				$table5 = M(MYTABLE_PRIFIX."log_gold_change_log_".date("Ym"));
				$table6 = M(MYTABLE_PRIFIX."user_lotterydraw_record");
				$table7 = M(MYTABLE_PRIFIX."log_diamond_change_log");
				$table8 = M(MYTABLE_PRIFIX."dynamic_config");
				$table9 = M(MYTABLE_PRIFIX."profile_lottery_jiangquan_record");
				$table10 = M(MYTABLE_PRIFIX."profile_lottery_jiangquan");
				
				//print_r($gamebase); print_r($vip); exit;
				//获取用户信息
				$user = $table1->where("user_id=".$user_id)->find();
				$buyer_nick = !empty($user['nickname']) ? $user['nickname'] : $user['nick_name'];
				if (empty($user['user_name'])){
                   return $this->answerResquest('-1', '用户ID异常');
				}

				//钻石抽奖，判断用户身上钻石是否足够
				$zuan = 10;
				if ($zuan > $user['diamond']){
                    return $this->answerResquest('-1', '钻石不够');
				}

				//连抽10次
				$jiang = array(10);
				$gold = 0;
				$car = 0;
				$yacht = 0;
				$ticket = 0;
				for($i=0; $i<10; $i++){
					$return_arr = $this->choujiang($user_id);
					$jiang[$i] = $return_arr['rand_goodsid'];
					$gold += $return_arr['gold'];
					$car += $return_arr['car'];
					$yacht += $return_arr['yacht'];
					$ticket += $return_arr['ticket'];
				}
				
				$aftergold = $user['diamond'] - $zuan;

				$change = (int)$zuan;
				$result = $table1->where("user_id=".$user_id." and $change>0 and diamond>=$change")->limit(1)->setDec('diamond', $change);

				//插入钻石日志
				$arr = array();
				$arr['user_id'] = $user_id;
				$arr['curtime'] = time();
				$arr['date'] = date("Y-m-d H:i:s");
				$arr['module'] = 3;
				$arr['beforediamond'] = $user['diamond'];
				$arr['afterdiamond'] = $aftergold;
				$arr['changediamond'] = -$zuan;
				$arr['tax'] = 0;
				$arr['memo'] = "转盘钻石变动(钻石抽奖)";
				$record_id = $table7->add($arr);

				//重新获取
				$user = $table1->where("user_id=".$user_id)->find();

				$result0 = array();
				$result0['status'] = 1;
				$result0['goodsid'] = $jiang;
				$result0['diamond'] = (int)$user['diamond'];
				$result0['gold'] = (int)$user['gold'];
				$result0['car'] = (int)$user['car'];
				$result0['yacht'] = (int)$user['yacht'];
				$result0['ticket'] = (int)$user['ticket'];

				//$result0['desc'] = "购买成功,您获得了".$showgold."万金币";
                return $this->answerResquest('1','',$result0);

		}
	}	
	
	private function choujiang($user_id){
				
		$table1 = M(MYTABLE_PRIFIX."user_info");
		$table2 = M(MYTABLE_PRIFIX."profile_vip_level_configure");
		$table3 = M(MYTABLE_PRIFIX."log_lotterydraw_record_log");
		$table4 = M(MYTABLE_PRIFIX."profile_lottery_draw_config");
		$table5 = M(MYTABLE_PRIFIX."log_gold_change_log_".date("Ym"));
		$table6 = M(MYTABLE_PRIFIX."user_lotterydraw_record");
		$table7 = M(MYTABLE_PRIFIX."log_diamond_change_log");
		$table8 = M(MYTABLE_PRIFIX."dynamic_config");
		$table9 = M(MYTABLE_PRIFIX."profile_lottery_jiangquan_record");
		$table10 = M(MYTABLE_PRIFIX."profile_lottery_jiangquan");
				
		//获取当前抽奖配置
		$prize3 = array();
		$lottery = $table4->order("version desc,goodsid")->limit(0,12)->select();
		//print_r($lottery);
		foreach($lottery as $key => $val){
			$prize3[$val['goodsid']] = $val['diamondrate'] / 100;
		}
					
		$flag_diamond = 1;
		//查询中奖记录，单周最多中3张
		$time01 = strtotime('last sunday');
		$time02 = strtotime('next sunday');
		$count00 = $table9->where("addtime>=$time01 and addtime<$time02 and user_id=$user_id")->count();
		if ($count00 > 3){
			$flag_diamond = 0;
		}
					
		if ($flag_diamond == 1){
			//查询中奖记录，当日最多中1张
			$time01 = strtotime(date("Y-m-d"));
			$time02 = $time01 + 86400;
			$count00 = $table9->where("addtime>=$time01 and addtime<$time02 and user_id=$user_id")->count();
			if ($count00 > 0){
				$flag_diamond = 0;
			}	
		}
					
		if ($flag_diamond == 1){
			//判断用户钻石的消费是否达到发话费卡标准
			$row00 = $table8->field('key_value')->where("key_name='CHOUJIANG_HUAFEI10'")->find();
			$CHOUJIANG_HUAFEI10 = $row00['key_value'];
			$row00 = $table8->field('key_value')->where("key_name='CHOUJIANG_HUAFEI50'")->find();
			$CHOUJIANG_HUAFEI50 = $row00['key_value'];
			$ku01 = $table10->where("id=1")->find();
			$res00 = $table9->field('record_id,addtime')->where("user_id=$user_id")->order("addtime desc")->find();
			$sql00 = "";
			if (!empty($res00['record_id'])){
				$sql00 .= " and id>".$res00['record_id'];
			}
			$changediamond = $table7->where("user_id=$user_id and module=3".$sql00)->sum('changediamond');
			$changediamond = abs($changediamond);
			if ($changediamond >= $CHOUJIANG_HUAFEI50){
				//判断库存是否还有
				if ($ku01['num2']>0) $diamond_goodsid = 13; else $flag_diamond = 0;
			}else if ($changediamond >= $CHOUJIANG_HUAFEI10){
				if ($ku01['num1']>0) $diamond_goodsid = 16; else $flag_diamond = 0;
			}else{
				$flag_diamond = 0;
			}
		}
					
		if ($flag_diamond == 0){
			$rand_goodsid = get_myrand($prize3);
		}else{
			$rand_goodsid = $diamond_goodsid;
		}
					
		//插入抽奖日志
		foreach($lottery as $key => $val){
			if ($val['goodsid'] == $rand_goodsid){
				$awardtype = $val['awardtype'];
				$awardvalue = $val['awardnum'];
			}
		}
				
		$arr = array();
		$arr['user_id'] = $user_id;
		$arr['drawtype'] = 3;
		$arr['drawtimes'] = 1;
		$arr['awardtype'] = $awardtype;
		$arr['awardvalue'] = $awardvalue;
		$arr['operator'] = time();
		$log_id = $table3->add($arr);
		if ($log_id){
			
			//获取用户信息
			$user = $table1->where("user_id=".$user_id)->find();
			$buyer_nick = !empty($user['nickname']) ? $user['nickname'] : $user['nick_name'];
			
			$result0 = array(4);
			//根据得奖类型发奖
			if ($awardtype == 1){
				//加金币
				$aftergold = $user['gold'] + $awardvalue;

				$change = (int)$awardvalue;
				$result = $table1->where("user_id=".$user_id." and $change>0")->limit(1)->setInc('gold', $change);
						
				//插入金币日志
				$arr = array();
				$arr['user_id'] = $user_id;
				$arr['curtime'] = time();
				$arr['date'] = date("Y-m-d H:i:s");
				$arr['module'] = 20;
				$arr['beforegold'] = $user['gold'];
				$arr['aftergold'] = $aftergold;
				$arr['changegold'] = $awardvalue;
				$arr['taxgold'] = 0;
				$arr['roomid'] = 0;
				$arr['memo'] = "转盘金币变动(金币发奖)";
				$result = $table5->add($arr);
						
				//插入幸运榜
				$count9 = $table6->where('user_id='.$user_id)->count();
				if ($count9 == 0){
					$arr = array();
					$arr['user_id'] = $user_id;
					$arr['nickname'] = $buyer_nick;
					$arr['headerpic'] = $user['head_picture'];
					$arr['wingold'] = $awardvalue;
					$result9 = $table6->add($arr);
				}else{
					$res9 = $table6->where('user_id='.$user_id)->find();
							
					$arr = array();
					$arr['wingold'] = $awardvalue + $res9['wingold'];
					$result9 = $table6->where("user_id=".$user_id)->save($arr);
				}
				
				$result0['yacht'] = 0;
				$result0['gold'] = (int)$awardvalue;
				$result0['car'] = 0;
				$result0['ticket'] = 0;
						
			}else if ($awardtype == 2){
				
				//扣库存
				$arr1 = array();
				if ($diamond_goodsid==13) {$arr1['num2'] = $ku01['num2'] - 1; $meno = "(库存num2:".$ku01['num2']."=>".$arr1['num2'].")";}
				if ($diamond_goodsid==16) {$arr1['num1'] = $ku01['num1'] - 1; $meno = "(库存num1:".$ku01['num1']."=>".$arr1['num1'].")";}
				$result = $table10->where('id=1')->save($arr1);
				
				//分配话费卡
				$arr1 = array();
				$arr1['user_id'] = $user_id;
				$arr1['awardvalue'] = $awardvalue;
				$arr1['record_id'] = $log_id;
				$arr1['addtime'] = time();
				$arr1['meno'] = $user_id."抽中".$awardvalue."元话费卡".$meno;
				$result = $table9->add($arr1);
				
			}else if ($awardtype == 3){
				//加汽车
				$aftergold = $user['car'] + $awardvalue;
				
				$change = (int)$awardvalue;
				$result = $table1->where("user_id=".$user_id." and $change>0")->limit(1)->setInc('car', $change);
				
				//插入礼物日志
				$liwu = M(MYTABLE_PRIFIX."log_gift_record_log");
				$arr = array();
				$arr['user_id'] = $user_id;
				$arr['operatortime'] = time();
				$arr['disdate'] = date("Y-m-d H:i:s");
				$arr['from_userid'] = 1;
				$arr['giftid'] = 3;
				$arr['beforenum'] = $user['car'];
				$arr['changenum'] = $awardvalue;
				$arr['afternum'] = $aftergold;
				$result = $liwu->add($arr);
				
				$result0['yacht'] = 0;
				$result0['gold'] = 0;
				$result0['car'] = (int)$awardvalue;
				$result0['ticket'] = 0;
						
			}else if ($awardtype == 4){
				//加飞机
				$aftergold = $user['yacht'] + $awardvalue;

				$change = (int)$awardvalue;
				$result = $table1->where("user_id=".$user_id." and $change>0")->limit(1)->setInc('yacht', $change);
						
				//插入礼物日志
				$liwu = M(MYTABLE_PRIFIX."log_gift_record_log");
				$arr = array();
				$arr['user_id'] = $user_id;
				$arr['operatortime'] = time();
				$arr['disdate'] = date("Y-m-d H:i:s");
				$arr['from_userid'] = 1;
				$arr['giftid'] = 5;
				$arr['beforenum'] = $user['yacht'];
				$arr['changenum'] = $awardvalue;
				$arr['afternum'] = $aftergold;
				$result = $liwu->add($arr);
						
				$result0['yacht'] = (int)$awardvalue;
				$result0['gold'] = 0;
				$result0['car'] = 0;
				$result0['ticket'] = 0;
				
			}else if ($awardtype == 5){
				//加奖券
				$aftergold = $user['ticket'] + $awardvalue;
				
				$change = (int)$awardvalue;
				$result = $table1->where("user_id=".$user_id." and $change>0")->limit(1)->setInc('ticket', $change);
						
				$result0['ticket'] = (int)$awardvalue;
				$result0['gold'] = 0;
				$result0['car'] = 0;
				$result0['yacht'] = 0;
			}
			
			$result0['rand_goodsid'] = $rand_goodsid;
			return $result0;
			exit;
		}else{
            return $this->answerResquest('-1', '数据异常，请联系客服');
		} 
				
	}
}