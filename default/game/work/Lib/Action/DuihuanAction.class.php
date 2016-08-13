<?php
// 接口管理的文件

class DuihuanAction extends InterAction {
	protected $online_status = 1;  //接口是否需要判断用户在线
	
	public function dh(){
		header("Content-type:text/html;charset=utf-8");

		$check = $this->check_sign();
		if ($check['status'] == 1){

				$lottery_id = $check['info']['id'];;
				$user_id = $check['info']['user_id'];;
				
				if (!is_numeric($lottery_id) || !is_numeric($user_id)){
					return $this->answerResquest('-1','输入异常，请联系客服');
				}
				
				//获取用户信息
				$table1 = M(MYTABLE_PRIFIX."user_info");
				$res1 = $table1->where("user_id=".$user_id)->find();
				//获取兑换商品信息
				$table2 = M(MYTABLE_PRIFIX."profile_mall_lottery");
				$res2 = $table2->where("id=".$lottery_id)->find();
				//判断用户是否足够兑换
				$table3 = M(MYTABLE_PRIFIX."log_mall_lottery_log");
				if ($res1['ticket'] < $res2['nums']){
					//奖券不足
					$meno = "您当前奖券有".$res1['ticket']."张，不足以兑换".$res2['names'];
					
					//记录兑换日志
					$arr = array();
					$arr['user_id'] = $user_id;
					$arr['lottery_id'] = $lottery_id;
					$arr['type'] = $res2['type'];
					$arr['names'] = $res2['names'];
					$arr['nums'] = $res2['nums'];
					$arr['ticket_before'] = $res1['ticket'];
					$arr['ticket_after'] = $res1['ticket'];
					$arr['gold'] = $res2['gold'];
					$arr['gold_before'] = $res1['gold'];
					$arr['gold_after'] = $res1['gold'];
					$arr['status'] = 0;
					$arr['meno'] = $meno;
					$arr['addtime'] = time();
					$result = $table3->add($arr);
					
					return $this->answerResquest('-1',$meno);
				}else{

					//扣除用户奖券
					$ticket_before = $res1['ticket'];
					$ticket_after = $res1['ticket'] - $res2['nums'];
					$data = array();
					//$data['ticket'] = $ticket_after;
					$data['ticket'] = array('exp','ticket-'.$res2['nums']);
					$sql1 = "";
					if ($res2['type'] == 1){
						//兑换金币
						$gold_before = $res1['gold'];
						$gold_after = $res1['gold'] + $res2['gold'];
						//$data['gold'] = $gold_after;
						$data['gold'] = array('exp','gold+'.$res2['gold']);
						$sql1 .= " and ".$res2['gold'].">0";
					}else{
						$gold_before = $res1['gold'];
						$gold_after = $res1['gold'];
					}
					
					
					$result = $table1->where("user_id=".$user_id." and ".$res2['nums'].">0 and ticket>=".$res2['nums'].$sql1)->limit(1)->save($data);
					//dump($table1->_sql());
					if ($result){
						if ($res2['type'] == 1){
							//插入金币日志
							$table4 = M(MYTABLE_PRIFIX."log_gold_change_log_".date("Ym"));
							$arr = array();
							$arr['user_id'] = $user_id;
							$arr['curtime'] = time();
							$arr['date'] = date("Y-m-d H:i:s");
							$arr['module'] = 27;
							$arr['beforegold'] = $gold_before;
							$arr['aftergold'] = $gold_after;
							$arr['changegold'] = $res2['gold'];
							$arr['taxgold'] = 0;
							$arr['roomid'] = 0;
							$arr['memo'] = "奖券兑换金币";
							$result = $table4->add($arr);
						}
						
						//记录兑换日志
						$meno = "您使用“".$res2['nums']."奖券”兑换了“".$res2['names']."”";
						$arr = array();
						$arr['user_id'] = $user_id;
						$arr['lottery_id'] = $lottery_id;
						$arr['type'] = $res2['type'];
						$arr['names'] = $res2['names'];
						$arr['nums'] = $res2['nums'];
						$arr['ticket_before'] = $ticket_before;
						$arr['ticket_after'] = $ticket_after;
						$arr['gold'] = $res2['gold'];
						$arr['gold_before'] = $gold_before;
						$arr['gold_after'] = $gold_after;
						$arr['status'] = 1;
						$arr['meno'] = $meno;
						$arr['addtime'] = time();
						$result = $table3->add($arr);
						//dump($table3->_sql());
						
						$result0 = array();
						$result0['ticket'] = (int)$res2['nums'];
						$result0['ticket_after'] = (int)$ticket_after;
						$result0['gold'] = (int)$res2['gold'];
						$result0['gold_after'] = (int)$gold_after;
						$result0['ts'] = date("Y-m-d H:i:s");
						$result0['desc'] = "您使用“".$res2['nums']."奖券”兑换了“".$res2['names']."”";

						return $this->answerResquest('1','',$result0);
					}else{
						return $this->answerResquest('-1', '奖券扣除失败，请联系客服');
					}
					
				}
		}
	}	
	
	public function svip(){
		header("Content-type:text/html;charset=utf-8");
		$result0 = array();

        $check = $this->check_sign();
        if ($check['status'] == 1){
			
			//$_POST['md5'] = $str."**".md5($str);
			//$logs_file = APP_PATH."Logs/paymentapi_".time().".txt";
			//file_put_contents($logs_file, json_encode($_POST));
			//echo $sign."<br>".$str."<br>".md5($str); exit;
	

				//echo "***"; exit;
				$lottery_id = $check['info']['id'];
				$user_id = $check['info']['user_id'];
				$table1 = M(MYTABLE_PRIFIX."user_info");
				$table2 = M(MYTABLE_PRIFIX."profile_mall_lottery");
				$table3 = M(MYTABLE_PRIFIX."log_mall_lottery_log");
				$table4 = M(MYTABLE_PRIFIX."profile_vip_level_configure");
				$table5 = M(MYTABLE_PRIFIX."log_change_user_vip");
				
				if (!is_numeric($lottery_id) || !is_numeric($user_id)){
                    return $this->answerResquest('-1', '输入异常，请联系客服');
				}
				
				//获取用户信息
				$res1 = $table1->where("user_id=".$user_id)->find();
				$viplevel = $res1['viplevel'];
				//获取兑换商品信息
				$res2 = $table2->where("id=".$lottery_id)->find();
				//判断用户钻石是否足够兑换
				if ($res1['diamond'] < $res2['nums']){
					//钻石不足
					$meno = "您当前钻石有".$res1['diamond']."，不足以购买".$res2['names'];
					
					//记录兑换日志
					$arr = array();
					$arr['user_id'] = $user_id;
					$arr['lottery_id'] = $lottery_id;
					$arr['type'] = $res2['type'];
					$arr['names'] = $res2['names'];
					$arr['nums'] = $res2['nums'];
					$arr['ticket_before'] = $res1['diamond'];
					$arr['ticket_after'] = $res1['diamond'];
					$arr['gold'] = 0;
					$arr['gold_before'] = 0;
					$arr['gold_after'] = 0;
					$arr['status'] = 0;
					$arr['meno'] = $meno;
					$arr['addtime'] = time();
					$result = $table3->add($arr);
					
                    return $this->answerResquest('-1', $meno);
				}else{

					//扣除用户钻石，增加用户成长值，判断VIP等级
					$ticket_before = $res1['diamond'];
					$ticket_after = $res1['diamond'] - (int)$res2['nums'];
					//成长值
					$vippoint0 = $res1['vippoint'];
					$vippoint1 = $res1['vippoint'] + (int)$res2['gold']*100;
					$data = array();
					$data['diamond'] = $ticket_after;
					$data['vippoint'] = $vippoint1;
					//判断VIP等级是否需要修改
					$notice_service = 0;
					$res00 = $table4->field('viplevel')->where('paycount<='.$vippoint1)->order('viplevel DESC')->find();
					if ($res00['viplevel'] > $res1['viplevel']){
						$viplevel = $res00['viplevel'];
						$data['viplevel'] = $res00['viplevel'];
						$res01 = $table5->field('viplevel')->where('user_id='.$user_id)->order('viplevel DESC')->find();
						if ($res00['viplevel'] > $res01['viplevel']) $notice_service = 1; else $viplevel = $res01['viplevel'];
					}

					$result = $table1->where("user_id=".$user_id)->save($data);
					if ($result){

						//插入钻石日志
						$table7 = M(MYTABLE_PRIFIX."log_diamond_change_log");
						$arr = array();
						$arr['user_id'] = $user_id;
						$arr['curtime'] = time();
						$arr['date'] = date("Y-m-d H:i:s");
						$arr['module'] = 4;
						$arr['beforediamond'] = $res1['diamond'];
						$arr['afterdiamond'] = $ticket_after;
						$arr['changediamond'] = -$res2['nums'];
						$arr['tax'] = 0;
						$arr['memo'] = "SVIP购买";
						$record_id = $table7->add($arr);
						//dump($table7->_sql());
						
						//记录兑换日志
						$meno = "您使用“".$res2['nums']."钻石”购买了“".$res2['names']."”";
						$arr = array();
						$arr['user_id'] = $user_id;
						$arr['lottery_id'] = $lottery_id;
						$arr['type'] = $res2['type'];
						$arr['names'] = $res2['names'];
						$arr['nums'] = $res2['nums'];
						$arr['ticket_before'] = $ticket_before;
						$arr['ticket_after'] = $ticket_after;
						$arr['gold'] = 0;
						$arr['gold_before'] = 0;
						$arr['gold_after'] = 0;
						$arr['status'] = 1;
						$arr['meno'] = $meno;
						$arr['addtime'] = time();
						$result = $table3->add($arr);
						//dump($table3->_sql());
						if ($notice_service == 1){
							//VIP变动通知服务器
							$url = DB_HOST."/Pay/jinbi.php?user_id=".$user_id."&viplevel=".$res00['viplevel'];
							//echo $url; 
							$jinbi_result = curlGET($url);
						}
						
						
						$result0['status'] = 1;
						$result0['diamond'] = (int)$res2['nums'];
						$result0['diamond_after'] = (int)$ticket_after;
						$result0['vip'] = (int)$viplevel;
						$result0['s'] = (int)$res2['gold']*100;
						$result0['ts'] = date("Y-m-d H:i:s");
						$result0['desc'] = "您使用“".$res2['nums']."钻石”购买了“".$res2['names']."”";

                        return $this->answerResquest('1', '', $result0);
					}else{

                        return $this->answerResquest('-1', '钻石扣除失败，请联系客服');
					}
					
				}
				

		}
	}
}