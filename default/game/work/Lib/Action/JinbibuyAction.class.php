<?php
class JinbibuyAction extends InterAction {
	public function buy(){
		$check = $this->check_sign();
				$ordercode = $check['info']['ordercode'];
				//获取用户信息
				$user = $table1->where("user_id=".$user_id)->find();
				$order = $table2->where("ordercode='$ordercode'")->find();
				//判断非空
				}
				}
				if ($result > 0){
					}
					
					//插入金币日志
					//生成订单备注
					//PUSH消息及邮件
					$result0 = array();