<?php
// 接口管理的文件

class ShowAction extends Action {
	
		
	
	public function webpay(){
		header('Content-Type: text/html; charset=UTF-8');
		$flag = (!empty($_GET['flag'])) ? $_GET['flag'] : "0";
		$result_code = ($_GET['result_code']!="") ? $_GET['result_code'] : "-46";
		$orderid = $_GET['orderid'];
		$pay_time = $_GET['pay_time'];
		$pageData = array();
		if ($result_code == "45" || $result_code == "-45"){
			
			if ($flag == "1") {
				$pageData['result_code'] = 45;
                $pageData['result_msg'] = '提交支付请求成功！稍后会为您发送支付结果';
                $pageData['result_info'] = '提交成功不代表支付成功，只是订单的相关信息已经正确的提交到支付平台';
				$pageData['orderid'] = $orderid;
				$pageData['flag'] = 1;
				$pageData['title'] = "在线支付充值结果";
			}else{
				$pageData['result_code'] = -45;
                $pageData['result_msg'] = '充值失败，请检查充值卡信息！';
                $pageData['result_info'] = '提交失败,请检查后重新测试支付';
				$pageData['title'] = '游戏充值卡充值';
				$pageData['orderid'] = 0;
				$pageData['flag'] = 0;
				$pageData['title'] = "在线支付充值结果";
			}
		}else{
			
			if ($flag == "1") {
				if (!empty($orderid)) $show1 = "订单".$orderid."于".date("Y-m-d H:i:s",$pay_time)."支付成功，";
				$pageData['title'] = "在线支付充值结果";
				$pageData['result_code'] = $result_code;
				$pageData['orderid'] = $orderid;
				$pageData['flag'] = $flag;
				$pageData['result_msg'] = '在线支付请求成功！';
				$pageData['result_info'] = $show1.'有任何疑问请及时联系在线客服!';
			}else{
				if (!empty($orderid)) $show1 = "订单".$orderid."支付失败，";
				$pageData['title'] = "在线支付充值结果";
				$pageData['result_code'] = $result_code;
				$pageData['orderid'] = 0;
				$pageData['flag'] = $flag;
				$pageData['result_msg'] = '支付失败，请重新支付！';
				$pageData['result_info'] = $show1.'有任何疑问请及时联系在线客服!';
			}
		}
		
			
		$this->assign('pageData', $pageData);
		$this->display("Pay:show_msg");
		
	}
	
	public function webpayshow(){
		$goods_price = '5';
		$goods_price = (int)$goods_price;
		$nums = array(10,20,30,50,100,300,500);
		$show_num = array();
		foreach ($nums as $val){
			$show_num[] = array('num' => $val);
		}
		if (in_array($goods_price,$nums)) $default_price = $goods_price; else $default_price = 10;
		$this->assign('show_num', $show_num);
		$this->assign('default_price', $default_price);
		$this->display("Pay:payment_string");
	}
}