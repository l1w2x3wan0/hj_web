<?php// 接口管理的文件
class JinbiAction extends InterAction {	protected $online_status = 1;  //接口是否需要判断用户在线	
	public function dh(){		header("Content-type:text/html;charset=utf-8");				$check = $this->check_sign();		if ($check['status'] == 1){
				$gold = $check['info']['gold'];				$diamond = $check['info']['diamond'];				$user_id = $check['info']['user_id'];
				//获取配置				$gamebase = S("GAMEBASE_CONFIG_WEB");				$vip = S("USERVIP_CONFIG_WEB");								//获取用户信息				$table1 = M(MYTABLE_PRIFIX."user_info");
				$user = $table1->where("user_id=".$user_id)->find();
				//判断非空				if (empty($gold) && empty($diamond)){					return $this->answerResquest('-1','输入异常，请联系客服');
				}
				//判断用户VIP等级				$userlevel = "maxsellgold".$user['viplevel'];				if ($vip[$userlevel] < $gold){					$needlevel = $user['viplevel'] + 1;					for ($i=$needlevel; $i<=12; $i++){						$templevel = "maxsellgold".$i;						if ($vip[$templevel] >= $gold){$needlevel = $i; break;}					}					return $this->answerResquest('-1',"需VIP".$needlevel."才能出售哦~");
				}				//判断售价范围				$fanwei = round($gold / $diamond);				if ($fanwei < $gamebase['GOLDMALL_SELL_MIN'] or $fanwei > $gamebase['GOLDMALL_SELL_MAX']){					return $this->answerResquest('-1','售价范围不对,请重输~');
				}						//判断用户金币是否足够兑换				$faxgold = round($gold * $gamebase['GOLDMALL_TAX'] / 100);				$needgold = $faxgold + $gold;				if ($needgold > $user['gold']){					return $this->answerResquest('-1','你携带的金币不足~');
				}						//判断在售商品				$table2 = M(MYTABLE_PRIFIX."log_mall_diamond_log");
				$nums = $table2->where("status=1 and user_id=".$user_id)->count('id');				$usernums = "maxsellnum".$user['viplevel'];				if ($vip[$usernums] <= $nums){					return $this->answerResquest('-1',"你已有".$nums."件商品在售,请先提升vip等级或稍后再试~");
				}							$aftergold = $user['gold'] - $needgold;				//生成金币订单				$ordercode = date("YmdHis").rand(1000,9999);				$data = array();				$nickname = !empty($user['nickname']) ? $user['nickname'] : $user['nick_name'];				$data['user_id'] = $user_id;				$data['nickname'] = $nickname;				$data['type'] = 1;				$data['ordercode'] = $ordercode;				$data['diamond'] = $diamond;				$data['gold'] = $gold;				$data['gold_before'] = $user['gold'];				$data['gold_after'] = $aftergold;				$data['taxgold'] = $faxgold;				$data['bl'] = $fanwei;				$data['addtime'] = time();				$result = $table2->add($data);				if ($result){
					//扣除用户金币					$data = array();					$data['gold'] = array('exp','gold-'.$needgold);					$result = $table1->where("user_id=".$user_id." and $needgold>0 and gold>=".$needgold)->limit(1)->save($data);										if ($result <= 0){						return $this->answerResquest('-1','输入异常，请联系客服处理');					}
					//插入金币日志					$table4 = M(MYTABLE_PRIFIX."log_gold_change_log_".date("Ym"));					$arr = array();					$arr['user_id'] = $user_id;					$arr['curtime'] = time();					$arr['date'] = date("Y-m-d H:i:s");					$arr['module'] = 26;					$arr['beforegold'] = $user['gold'];					$arr['aftergold'] = $aftergold;					$arr['changegold'] = -$needgold;					$arr['taxgold'] = $faxgold;					$arr['roomid'] = 0;					$arr['memo'] = "金币兑换钻石(售卖)";					$result = $table4->add($arr);								//PUSH大喇叭消息:XXX出售XX万金币,只要N钻石,赶快去看看!					//插入大喇叭					$showgold = round($gold / 10000);					$table5 = M(MYTABLE_PRIFIX."user_horn_record");					$arr = array();					$arr['user_id'] = $user_id;					$arr['errorcode'] = 0;					$arr['type'] = 3;					$arr['content'] = "'".$nickname."'出售".$showgold."万金币，只要".$diamond."钻石，赶快去看看！";					$arr['sendtime'] = time();					$notice_id = $table5->add($arr);								/**/					//插入大喇叭日志					//通知服务器					$url = DB_HOST."/Pay/jbmall.php?sellid=".$user_id."&type=7&index=1&email=".urlencode($arr['content']);					$server_status = curlGET($url);					$len = strlen($server_status) - 3;					$status = substr($server_status, $len, 1);					$notice_status = ($status==1) ? 1 : 0;
					$table6 = M(MYTABLE_PRIFIX."log_mall_diamond_notice");					$arr = array();					$arr['ordercode'] = $ordercode;					$arr['notice_id'] = $notice_id;					$arr['notice_url'] = $url;					$arr['notice_num'] = 1;					$arr['notice_time'] = time();					$arr['notice_status'] = $notice_status;					$result = $table6->add($arr);
					$result0 = array();					$result0['status'] = 1;					$result0['goldneed'] = (int)$needgold;					$result0['goldafter'] = (int)$aftergold;					//$result0['ordercode'] = $ordercode;					//$result0['desc'] = "售卖发布成功,扣除所卖和手续费共".$needgold."金币~";					return $this->answerResquest('1','',$result0);								}else{					return $this->answerResquest('-1', '订单生成异常，请联系客服');
				} 		}			}	
	//通知服务器开始	public function notice(){		$table = "user_record";		$row = M($table);		//通知服务器		$flag = $_GET['flag'];		$id = $_GET['id'];				if ($flag == "5"){			$url = DB_HOST."/Pay/shang.php?showindex=5&showtype=4";		}else if ($flag == "6"){			$url = DB_HOST."/Pay/shang.php?showindex=7&showtype=4";		}else if ($flag == "7"){			$url = DB_HOST."/Pay/shang.php?showindex=6&showtype=4";		}else if ($flag == "8"){			$url = DB_HOST."/Pay/shang.php?showindex=5&showtype=4";		}else if ($flag == "3"){			$url = DB_HOST."/Pay/shang.php?showindex=4&showtype=4";		}else if ($flag == "9"){			$url = DB_HOST."/Pay/shang.php?showindex=9&showtype=4";		}else if ($flag == "10"){			$url = DB_HOST."/Pay/shang.php?showindex=8&showtype=4";		}else{			$url = DB_HOST."/Pay/shang.php";		}
		if (empty($id)){			echo "0"; exit;		}
		$result = curlGET($url);		$len = strlen($result) - 3;		$status = substr($result, $len, 1);
		if ($status == "1"){			$data = array();			$data['notice'] = "1";			$data['noname'] = $_SESSION['username'];			$data['nourl'] = $url;			$result = $row->where("id=".$id)->save($data);		} 		echo $status;	}	//通知服务器结束
}