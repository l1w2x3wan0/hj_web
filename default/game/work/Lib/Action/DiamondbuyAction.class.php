<?php
// 接口管理的文件

class DiamondbuyAction extends InterAction {
	protected $online_status = 0;  //接口是否需要判断用户在线
	
	public function buy(){
		header("Content-type:text/html;charset=utf-8");
		
		$check = $this->check_sign($this->online_status);
		if ($check['status'] == 1){

				$GoodsID = $check['info']['GoodsID'];
				$user_id = $check['info']['user_id'];

				//判断非空
				if (empty($user_id) or empty($GoodsID)){
					return $this->answerResquest('-1','输入异常，请联系客服');
				}

				//获取用户信息
				$table1 = M(MYTABLE_PRIFIX."user_info");
				$user = $table1->where("user_id=".$user_id)->find();
				//获取商品信息
				$table2 = M(MYTABLE_PRIFIX."profile_mall_goods_data");
				$goods = $table2->where("GoodsID=".$GoodsID)->find();
				
				//判断用户钻石是否足够兑换
				//echo $needgold."**".$user['gold']; exit;
				if ($user['diamond'] < $goods['GoodsValue']){
					return $this->answerResquest('-1','您钻石不足,请充值后再试~');
				}
				
				//给买家增加金币,减钻石
				$buyer_diamond = $user['diamond'] - $goods['GoodsValue'];
				$changegold = $goods['GoldNum'] + $goods['GiveGoldNum'];
				$buyer_gold = $user['gold'] + $changegold;
				$data = array();
				$data['gold'] = array('exp','gold+'.$changegold);
				$data['diamond'] = array('exp','diamond-'.$goods['GoodsValue']);
				 
				$result = $table1->where("user_id=".$user_id." and $changegold>0 and ".$goods['GoodsValue'].">0 and diamond>=".$goods['GoodsValue'])->limit(1)->save($data);
				if ($result){
					
					//插入金币日志
					$table4 = M(MYTABLE_PRIFIX."log_gold_change_log_".date("Ym"));
					$arr = array();
					$arr['user_id'] = $user_id;
					$arr['curtime'] = time();
					$arr['date'] = date("Y-m-d H:i:s");
					$arr['module'] = 23;
					$arr['beforegold'] = $user['gold'];
					$arr['aftergold'] = $buyer_gold;
					$arr['changegold'] = $changegold;
					$arr['taxgold'] = 0;
					$arr['roomid'] = 0;
					$arr['memo'] = "钻石兑换金币(".$user['diamond']."->".$buyer_diamond.")";
					$result = $table4->add($arr);
					
					//插入钻石日志
					$table4 = M(MYTABLE_PRIFIX."log_diamond_change_log");
					$arr = array();
					$arr['user_id'] = $user_id;
					$arr['curtime'] = time();
					$arr['date'] = date("Y-m-d H:i:s");
					$arr['module'] = 5;
					$arr['beforediamond'] = $user['diamond'];
					$arr['afterdiamond'] = $buyer_diamond;
					$arr['changediamond'] = $goods['GoodsValue'];
					$arr['tax'] = 0;
					$arr['memo'] = "钻石兑换金币(".$user['diamond']."->".$buyer_diamond.")";
					$result = $table4->add($arr);
					
					$result0 = array();
					$result0['status'] = 1;                                        //1兑换成功-1失败
					$result0['gold'] = (int)$changegold;                           //变动金币
					$result0['goldafter'] = (int)$buyer_gold;                      //变化后金币
					$result0['diamond'] = (int)$goods['GoodsValue'];               //变动钻石
					$result0['diamondafter'] = (int)$buyer_diamond;                //变化后钻石
					return $this->answerResquest('1','',$result0);
					
				}else{
					return $this->answerResquest('-1', '订单生成异常，请联系客服');
				} 

		}
		
		
	}	
}