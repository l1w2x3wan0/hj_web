<?php// 接口管理的文件class JinbioffAction extends InterAction {	protected $online_status = 1;  //接口是否需要判断用户在线	
	public function off(){		header("Content-type:text/html;charset=utf-8");
		$check = $this->check_sign();		if ($check['status'] == 1){
				$ordercode = $check['info']['ordercode'];				$user_id = $check['info']['user_id'];
				//获取配置				$gamebase = S("GAMEBASE_CONFIG_WEB");				$vip = S("USERVIP_CONFIG_WEB");
				//获取用户信息				$table1 = M(MYTABLE_PRIFIX."user_info");
				$user = $table1->where("user_id=".$user_id)->find();				//获取用户钻石购买日志				$table2 = M(MYTABLE_PRIFIX."log_mall_diamond_log");
				$order = $table2->where("ordercode='$ordercode'")->find();
				//判断非空				if (empty($ordercode) && strlen($ordercode)!=18){					return $this->answerResquest('-1','输入异常，请联系客服');				}
				//判断订单是否是该用户				$total = $table2->where("user_id=$user_id and ordercode='$ordercode'")->count('id');				if ($total < 1){					return $this->answerResquest('-1','输入异常，请联系客服');				}
				//判断订单是否已购买				$total = $table2->where("status=1 and ordercode='$ordercode'")->count('id');				if ($total < 1){					return $this->answerResquest('-1','下架失败,商品已售出');
				}
				//商品下架,更新金币订单				$data = array();				$data['offtime'] = time();				$data['status'] = 3;				$result = $table2->where("ordercode='$ordercode' and status=1")->save($data);				if ($result > 0){					$aftergold = $user['gold'] + $order['gold'];					//返回用户金币					$data = array();					//$data['gold'] = $aftergold;					$data['gold'] = array('exp','gold+'.$order['gold']);					$result = $table1->where("user_id=".$user_id." and ".$order['gold'].">0")->limit(1)->save($data);
					//插入金币日志					$table4 = M(MYTABLE_PRIFIX."log_gold_change_log_".date("Ym"));					$arr = array();					$arr['user_id'] = $user_id;					$arr['curtime'] = time();					$arr['date'] = date("Y-m-d H:i:s");					$arr['module'] = 26;					$arr['beforegold'] = $user['gold'];					$arr['aftergold'] = $aftergold;					$arr['changegold'] = $order['gold'];					$arr['taxgold'] = 0;					$arr['roomid'] = 0;					$arr['memo'] = "金币兑换钻石(下架)";					$result = $table4->add($arr);
					$showgold = $order['gold'] / 10000;					$result0 = array();					$result0['status'] = 1;					$result0['gold'] = (int)$order['gold'];					$result0['goldafter'] = (int)$aftergold;					$result0['desc'] = "成功下架,已退还您".$showgold."万金币~";										return $this->answerResquest('1','',$result0);									}else{					return $this->answerResquest('-1', '订单生成异常，请联系客服');
				}		}	}}