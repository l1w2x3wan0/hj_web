<?php
// 接口管理的文件

class SpreadAction extends InterAction {
	protected $online_status = 1;  //接口是否需要判断用户在线
	
	public function index(){
		header("Content-type:text/html;charset=utf-8");

		$check = $this->check_sign();
		if ($check['status'] == 1){
				
				$user_id = $check['info']['user_id'];
				$code = $check['info']['code'];
			
				//判断推广人UID，固定为10321888
				$spread_model = M(MYTABLE_PRIFIX."user_spread_number");
				$spread_info = $spread_model->where("flag=0 and number='$code'")->find();
				$spread_id = $spread_info['user_id'];
				//$spread_id = (($code + 168168 * 9) * 3 - 20160105) / 6;
				$table1 = M(MYTABLE_PRIFIX."user_info");
				$spread = $table1->where("user_id=".$spread_id)->find();
				if (empty($spread['user_name'])){
					return $this->answerResquest('-1','推广码有误');
				}
				
				//自己不能推广自己
				if ($spread_id == $user_id){
					return $this->answerResquest('-1','自己不能推广自己');
				}
				
				//判断UID是否来自10000号渠道
				$channel = 10000;
				$user = $table1->where("user_id=".$user_id)->find();
				$nickname = !empty($user['nickname']) ? $user['nickname'] : $user['nick_name'];
				if ($user['channel']!=$channel){
					return $this->answerResquest('-1','用户不是来自推广渠道');
				}
				
				//print_r($gamebase); print_r($vip); exit;
				//该用户是否已填推广
				$table2 = M(MYTABLE_PRIFIX."user_spread");
				$count = $table2->where("user_id=".$user_id)->count();
				if ($count > 0){
					return $this->answerResquest('-1','推广金币已领取');
				}
				
				//插入推广日志
				$arr = array();
				$arr['user_id'] = $user_id;
				$arr['spread_id'] = $spread_id;
				$arr['addtime'] = time();
				$result = $table2->add($arr);
				if ($result){
					
					//发奖励，各发5W
					$jiangli = 50000;
					$aftergold = $user['gold'] + $jiangli;
					$data = array();
					//$data['gold'] = $aftergold;
					$data['gold'] = array('exp','gold+'.$jiangli);
					$result11 = $table1->where("user_id=".$user_id)->limit(1)->save($data);
					//插入金币日志
					$arr = array();
					$arr['user_id'] = $user_id;
					$arr['curtime'] = time();
					$arr['date'] = date("Y-m-d H:i:s");
					$arr['module'] = 24;
					$arr['beforegold'] = $user['gold'];
					$arr['aftergold'] = $aftergold;
					$arr['changegold'] = $jiangli;
					$arr['taxgold'] = 0;
					$arr['roomid'] = 0;
					$arr['memo'] = "用户推广(金币奖励)";
					$table5 = M(MYTABLE_PRIFIX."log_gold_change_log_".date("Ym"));
					$result12 = $table5->add($arr);
						
					//发奖励，各发5W
					$aftergold = $spread['gold'] + $jiangli;
					$data = array();
					//$data['gold'] = $aftergold;
					$data['gold'] = array('exp','gold+'.$jiangli);
					$result21 = $table1->where("user_id=".$spread_id)->limit(1)->save($data);
					//插入金币日志
					$arr = array();
					$arr['user_id'] = $spread_id;
					$arr['curtime'] = time();
					$arr['date'] = date("Y-m-d H:i:s");
					$arr['module'] = 24;
					$arr['beforegold'] = $spread['gold'];
					$arr['aftergold'] = $aftergold;
					$arr['changegold'] = $jiangli;
					$arr['taxgold'] = 0;
					$arr['roomid'] = 0;
					$arr['memo'] = "用户被推广(金币奖励)";
					$result22 = $table5->add($arr);
					
					//插入邮件
					$msg = "您已成功推荐好友".$user_id."，系统赠送您".$jiangli."金币！";
					$table5 = M(MYTABLE_PRIFIX."user_email");
					$showgold = $jiangli;
					$arr = array();
					$arr['user_id'] = $spread_id;
					$arr['email_type'] = 7;
					$arr['is_read'] = 0;
					$arr['content'] = $msg;
					$arr['opera_date'] = date("Y-m-d H:i;s");
					$email_id = $table5->add($arr);
					
					//通知服务器
					//$msg = "您收到了用户".$user_id."的推广奖励金币".$jiangli;
					$url = DB_HOST."/Pay/spread.php?spread_id=".$spread_id."&jiangli=".$jiangli."&type=8&index=1&msg=".urlencode($msg);
					$server_status = curlGET($url);
					$len = strlen($server_status) - 3;
					$status = substr($server_status, $len, 1);
					$notice_status = ($status==1) ? 1 : 0;
					
					//更新状态
					$data = array();
					$data['user_flag'] = $result11;
					$data['spread_flag'] = $result21;
					$data['notice_flag'] = $notice_status;
					$result3 = $table2->where("id=".$result)->save($data);
					
					//更新状态
					$data = array();
					$data['flag'] = 1;
					$result4 = $spread_model->where("flag=0 and number='$code'")->save($data);
					
					$result0 = array();
					$result0['status'] = 1;
					$result0['gold'] = (int)$jiangli;
					//$result0['desc'] = "购买成功,您获得了".$showgold."万金币";
					return $this->answerResquest('1','',$result0);
					
				}else{
					return $this->answerResquest('-1', '数据异常，请联系客服');
				} 

		}
	}	
}