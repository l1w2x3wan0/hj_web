<?php
	public function off(){
		$check = $this->check_sign();
				$ordercode = $check['info']['ordercode'];
				//获取配置
				//获取用户信息
				$user = $table1->where("user_id=".$user_id)->find();
				$order = $table2->where("ordercode='$ordercode'")->find();
				//判断非空
				//判断订单是否是该用户
				//判断订单是否已购买
				}
				//商品下架,更新金币订单
					//插入金币日志
					$showgold = $order['gold'] / 10000;
				}