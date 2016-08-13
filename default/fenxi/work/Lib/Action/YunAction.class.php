<?php
// 运营分析文件

class YunAction extends BaseAction {

	protected $By_tpl = 'Yun'; 
	
	//日激活充值数据统计
	public function tongji1(){
		$table = "user_info";
		$row = M($table);
		$table2 = "user_pay_log";
		$row2 = M($table2);
		$table3 = "fx_tongji1";
		$row3 = M($table3);
		
		$beginTime = I("param.beginTime");
		$endTime = I("param.endTime");
		
		//查询不能大于当天
		$timenow = strtotime(date("Y-m-d"));
		if (strtotime($beginTime)>$timenow || strtotime($endTime)>$timenow){
			$this->error('查询日期不能大于当天');
			exit;
		}
		
		if (strtotime($beginTime)>strtotime($endTime)){
			$this->error('开始日期不能大于结束日期');
			exit;
		}
		
		if (!empty($beginTime) && !empty($endTime)){
			$datenow = date("d", strtotime($endTime));
			$dateend = date("d", strtotime($beginTime));
		}else{
			$datenow = date("d") - 1;
			$dateend = 1;
		}
		
		if ($datenow > 0){
			$totalall = array(0,0,0,0,0);
			$tongji_show = array();
			for ($i=$datenow; $i>=$dateend; $i--){
				$data1 = array();
				$date = date("Y-m-").$i;
				$time1 = strtotime($date);
				$time2 = $time1 + 60 * 60 * 24;
				$date1 = date("Y-m-d", $time1);
				$date2 = date("Y-m-d", $time2);
				
				$total = $row3->where("data='$date1'")->count();
				if ($total == 0){
				
					$data1['data'] = $date1;
					$data1['addtime'] = time();
					//日激活量
					$count1 = $row->where("register_date>='$date1' and register_date<'$date2'")->count();
					//日充值人数
					$count2 = $row2->where("pay_date>='$date1' and pay_date<'$date2'")->count('distinct userid');
					//日充值金额
					$count3 = $row2->where("pay_date>='$date1' and pay_date<'$date2'")->sum('money');
					if (empty($count3)) $count3 = 0;
					//人均ARPU
					$count4 = ($count1==0) ? 0 : round($count3/$count1,2);
					//付费用户ARPU
					$count5 = ($count2==0) ? 0 : round($count3/$count2,2);
					$tongji1 = array('count1' => $count1,
									 'count2' => $count2,
									 'count3' => $count3,
									 'count4' => $count4,
									 'count5' => $count5);
					$data1['tongji'] = json_encode($tongji1);
					$result = $row3->add($data1);
				}else{
					
					$info = $row3->where("data='$date1'")->find();
					$tongji1 = json_decode($info['tongji'], true);
				}
				
				$tongji_show[] = array('data' => $date1,
									   'count1' => $tongji1['count1'],
									   'count2' => $tongji1['count2'],
									   'count3' => $tongji1['count3'],
									   'count4' => $tongji1['count4'],
									   'count5' => $tongji1['count5']);
									   
				$totalall[0] += $tongji1['count1'];
				$totalall[1] += $tongji1['count2'];
				$totalall[2] += $tongji1['count3'];
			}
		} 
		$totalall[3] = ($totalall[0]==0) ? 0 : round($totalall[2]/$totalall[0],2); 
		$totalall[4] = ($totalall[1]==0) ? 0 : round($totalall[2]/$totalall[1],2); 
		
		$this->assign('left_css',"14");
		$this->assign('list',$tongji_show);
		$this->assign('totalall',$totalall);
		
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji1";
		$this->display($lib_display);
	}
	
	//用户登陆数据统计
	public function tongji2(){
		$table = "fx_tongji2";
		$row = M($table);
		$table2 = "login_log";
		$row2 = M($table2);
		
		$beginTime = I("param.beginTime");
		$endTime = I("param.endTime");
		
		//查询不能大于当天
		$timenow = strtotime(date("Y-m-d"));
		if (strtotime($beginTime)>$timenow || strtotime($endTime)>$timenow){
			$this->error('查询日期不能大于当天');
			exit;
		}
		
		if (strtotime($beginTime)>strtotime($endTime)){
			$this->error('开始日期不能大于结束日期');
			exit;
		}
		
		if (!empty($beginTime) && !empty($endTime)){
			$date11 = date("Y-m-d", strtotime($beginTime));
			$date12 = date("Y-m-d", strtotime($endTime));
			$day_jian = (strtotime($date12) - strtotime($date11)) / (60 * 60 * 24);
			
			//$datenow = date("d", strtotime($endTime));
			//$dateend = date("d", strtotime($beginTime));
		}else{
			$datenow = date("d") - 1;
			$dateend = 1;
			$day_jian = $datenow - 1;
			$date12 = date("Y-m-").$datenow;
			$endTime = $date12;
			$beginTime = date("Y-m-01");
		}

		//echo $day_jian."__".$date12."<br>";
		//exit;
		if ($day_jian > 0){
			$tongji_show = array();
			for ($i=1; $i<=$day_jian+1; $i++){
				$data1 = array();
				$date = $date12;
				$time1 = strtotime($date) - 60 * 60 * 24 * ($i - 1);
				$time2 = $time1 + 60 * 60 * 24;
				$date1 = date("Y-m-d", $time1);
				$date2 = date("Y-m-d", $time2);
				
				$total = $row->where("data='$date1'")->count();
				if ($total == 0){
				
					$data1['data'] = $date1;
					$data1['addtime'] = time();
					//登录端口
					$count1 = 'Andriod';
					//登录版本
					$res2 = $row2->field("version")->where("login_date>='$date1' and login_date<'$date2' and flatform_type='1'")->group('version')->select();
					$version = array();
					foreach ($res2 as $key2 => $val2){
						$count2 = $row2->where("login_date>='$date1' and login_date<'$date2' and flatform_type='1' and version='".$val2['version']."'")->count();
						$version[$key2] = array('ver' => $val2['version'],
												'count' => $count2);
						//统计渠道
						$res3 = $row2->field("channel")->where("login_date>='$date1' and login_date<'$date2' and flatform_type='1' and version='".$val2['version']."'")->group('channel')->select();
						$version[$key2]['channel'] = array();
						$version[$key2]['channel_show'] = '';
						foreach ($res3 as $key3 => $val3){
							$count3 = $row2->where("login_date>='$date1' and login_date<'$date2' and flatform_type='1' and version='".$val2['version']."' and channel='".$val3['channel']."'")->count();
							$version[$key2]['channel_show'] .= (empty($version[$key2]['channel_show'])) ? '' : '<br>';
							$version[$key2]['channel_show'] .= 'Channel:'.$val3['channel'].' => Count:'.$count3;
							$version[$key2]['channel'][] = array('channel' => $val3['channel'],
																 'count' => $count3);
						}
					}
					//登录用户数量
					$count3 = $row2->where("login_date>='$date1' and login_date<'$date2' and flatform_type='1'")->count('distinct user_id');
					if (empty($count3)) $count3 = 0;
					//登录次数
					$count4 = $row2->where("login_date>='$date1' and login_date<'$date2' and flatform_type='1'")->count();
					//平均登录次数
					$count5 = ($count3==0) ? 0 : round($count4/$count3,2);
					$tongji1 = array('count1' => $count1,
									 'version' => $version,
									 'count3' => $count3,
									 'count4' => $count4,
									 'count5' => $count5);
					$data1['tongji'] = json_encode($tongji1);
					$result = $row->add($data1);
				}else{
					
					$info = $row->where("data='$date1'")->find();
					$tongji1 = json_decode($info['tongji'], true);
				}
				/*$ver = "";
				foreach ($tongji1['version'] as $key3 => $val3){
					$ver .= ($key3==0) ? "" : "；";
					$ver .= "".$val3['ver'].' => <font color="#FF0000">'.$val3['count'].'</font>';
				}*/
				$tongji_show[] = array('data' => $date1,
									   'count1' => $tongji1['count1'],
									   'version' => $tongji1['version'],
									   'count3' => $tongji1['count3'],
									   'count4' => $tongji1['count4'],
									   'count5' => $tongji1['count5']);
			}
		} 
		//print_r($tongji_show);
		
		//所有的版本
		$login_date1 = $beginTime;
		$login_date2 = $endTime;
		//$time1 = strtotime($login_date2);
		//$time2 = $time1 + 60 * 60 * 24;
		//$login_date2 = date("Y-m-d", $time2);
		$res2 = $row2->field("version")->where("login_date>='$login_date1' and login_date<'$login_date2' and flatform_type='1'")->group('version')->select();
		$this->assign('version',$res2);
		$count2 = $row2->where("login_date>='$login_date1' and login_date<'$login_date2' and flatform_type='1'")->count('distinct version');
		$this->assign('version_count',$count2);
		
		$this->assign('left_css',"14");
		$this->assign('list',$tongji_show);
		
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji2";
		$this->display($lib_display);
	}
	
	//机器人数据统计
	public function tongji3(){
		$table = "fx_tongji3";
		$row = M($table);
		$table2 = "robot_game_record";
		$row2 = M($table2);
		
		$beginTime = I("param.beginTime");
		$endTime = I("param.endTime");
		
		//查询不能大于当天
		$timenow = strtotime(date("Y-m-d"));
		if (strtotime($beginTime)>$timenow || strtotime($endTime)>$timenow){
			$this->error('查询日期不能大于当天');
			exit;
		}
		
		if (strtotime($beginTime)>strtotime($endTime)){
			$this->error('开始日期不能大于结束日期');
			exit;
		}
		
		if (!empty($beginTime) && !empty($endTime)){
			$datenow = date("d", strtotime($endTime));
			$dateend = date("d", strtotime($beginTime));
		}else{
			$datenow = date("d") - 1;
			$dateend = 1;
		}
		
		if ($datenow > 0){
			$tongji_show = array();
			for ($i=$datenow; $i>=$dateend; $i--){
				$data1 = array();
				$date = date("Y-m-").$i;
				$time1 = strtotime($date);
				$time2 = $time1 + 60 * 60 * 24;
				$date1 = date("Y-m-d", $time1);
				$date2 = date("Y-m-d", $time2);
				
				$total = $row->where("data='$date1'")->count();
				if ($total == 0){
				
					$data1['data'] = $date1;
					$data1['addtime'] = time();
					//房间号
					$res2 = $row2->field("room_id")->where("oper_date>='$date1' and oper_date<'$date2'")->group('room_id')->select();
					$room = array();
					foreach ($res2 as $key2 => $val2){
						//房间内机器人数量
						$count2 = $row2->where("oper_date>='$date1' and oper_date<'$date2' and room_id='".$val2['room_id']."'")->count('distinct user_id');
						//房间内机器人赢的金币
						$count3 = $row2->where("oper_date>='$date1' and oper_date<'$date2' and room_id='".$val2['room_id']."' and score_type='3'")->sum('gold');
						//房间内机器人输的金币
						$count4 = $row2->where("oper_date>='$date1' and oper_date<'$date2' and room_id='".$val2['room_id']."' and score_type='2'")->sum('gold');
						//房间内机器人税
						$count5 = $row2->where("oper_date>='$date1' and oper_date<'$date2' and room_id='".$val2['room_id']."' and score_type='3'")->sum('tax_gold');
						//计算比例
						$count6 = $count3 + abs($count4);
						$bili1 = round($count3/$count6, 2) * 100;
						$bili2 = 100 - $bili1;
						
						$room[] = array('room_id' => $val2['room_id'],
										'count2' => $count2,
										'count3' => $count3,
										'count4' => $count4,
										'count5' => $count5,
										'bili1' => $bili1,
										'bili2' => $bili2);
					}
					
					$data1['tongji'] = json_encode($room);
					$result = $row->add($data1);
				}else{
					
					$info = $row->where("data='$date1'")->find();
					$room = json_decode($info['tongji'], true);
				}
				$ver = "";
				foreach ($tongji1['version'] as $key3 => $val3){
					$ver .= ($key3==0) ? "" : "；";
					$ver .= "".$val3['ver'].' => <font color="#FF0000">'.$val3['count'].'</font>';
				}
				$tongji_show[] = array('data' => $date1,
									   'room' => $room);
			}
		} 
		
		
		$this->assign('left_css',"14");
		$this->assign('list',$tongji_show);
		
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji3";
		$this->display($lib_display);
	}
	
	//用户充值统计
	public function tongji4(){
		$table = "fx_tongji4";
		$row = M($table);
		$table2 = "pay_now_config.zjh_order";
		$row2 = M($table2);
		$table3 = "payment";
		$row3 = M($table3);
		$res2 = $row3->select();
		
		$beginTime = I("param.beginTime");
		$endTime = I("param.endTime");
		
		//查询不能大于当天
		$timenow = strtotime(date("Y-m-d"));
		if (strtotime($beginTime)>$timenow || strtotime($endTime)>$timenow){
			$this->error('查询日期不能大于当天');
			exit;
		}
		
		if (strtotime($beginTime)>strtotime($endTime)){
			$this->error('开始日期不能大于结束日期');
			exit;
		}
		
		if (!empty($beginTime) && !empty($endTime)){
			$datenow = date("d", strtotime($endTime));
			$dateend = date("d", strtotime($beginTime));
		}else{
			$datenow = date("d") - 1;
			$dateend = 1;
		}

		
		$totalall = array(0,0,0);
		if ($datenow > 0){
			$tongji_show = array();
			for ($i=$datenow; $i>=$dateend; $i--){
				$data1 = array();
				$date = date("Y-m-").$i;
				$time1 = strtotime($date);
				$time2 = $time1 + 60 * 60 * 24;
				$date1 = date("Y-m-d", $time1);
				$date2 = date("Y-m-d", $time2);
				
				$total = $row->where("data='$date1'")->count();
				if ($total == 0){
				
					$data1['data'] = $date1;
					$data1['addtime'] = time();
					//充值账号
					$count1 = $row2->where("order_create_time>='$time1' and order_create_time<'$time2'")->count('distinct user_id');
					//充值次数
					$count2 = $row2->where("order_create_time>='$time1' and order_create_time<'$time2' and payment_status in (1,-2)")->count('id');
					//充值金额
					$count3 = $row2->where("order_create_time>='$time1' and order_create_time<'$time2' and payment_status in (1,-2)")->sum('result_money');
					if (empty($count3)) $count3 = 0;
					//支付类型
					
					$payment = array();
					foreach ($res2 as $key2 => $val2){
						//充值账号
						$count41 = $row2->where("order_create_time>='$time1' and order_create_time<'$time2' and payment_id=".$val2['payment_id'])->count('distinct user_id');
						//充值次数
						$count42 = $row2->where("order_create_time>='$time1' and order_create_time<'$time2' and payment_status in (1,-2) and payment_id=".$val2['payment_id'])->count('id');
						//充值金额
						$count43 = $row2->where("order_create_time>='$time1' and order_create_time<'$time2' and payment_status in (1,-2) and payment_id=".$val2['payment_id'])->sum('result_money');
						if (empty($count43)) $count43 = 0;
						
						$payment[$key2] = array('payment_name' => $val2['payment_name'],
												'count41' => $count41,
												'count42' => $count42,
												'count43' => $count43);
						
					}

					$tongji1 = array('count1' => $count1,
									 'count2' => $count2,
									 'count3' => $count3,
									 'payment' => $payment);
					$data1['tongji'] = json_encode($tongji1);
					$result = $row->add($data1);
				}else{
					
					$info = $row->where("data='$date1'")->find();
					$tongji1 = json_decode($info['tongji'], true);
					$payment = $tongji1['payment'];
				}

				$tongji_show[] = array('data' => $date1,
									   'count1' => $tongji1['count1'],
									   'count2' => $tongji1['count2'],
									   'count3' => $tongji1['count3'],
									   'payment' => $payment);
									   
				$totalall[0] += $tongji1['count1'];	
				$totalall[1] += $tongji1['count2'];	
				$totalall[2] += $tongji1['count3'];	
				foreach ($res2 as $key3 => $val3){
					foreach ($payment as $key4 => $val4){
						if ($val3['payment_name']==$val4['payment_name']){
							//if ($val3['payment_id']=="101") echo $res2[$key3]['count41']."**".$val4['count41']."<br>";
							$res2[$key3]['count41'] += $val4['count41'];
							$res2[$key3]['count42'] += $val4['count42'];
							$res2[$key3]['count43'] += $val4['count43'];
						}
					}
				}			   
			}
		}
		
		$this->assign('left_css',"14");
		$this->assign('list',$tongji_show);
		$this->assign('payment',$res2);
		$this->assign('totalall',$totalall);
		
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji4";
		$this->display($lib_display);
	}
	
	public function tongji5(){
		$table = "fx_tongji5";
		$row = M($table);
		$table2 = "user_info";
		$row2 = M($table2, '', DB_CONFIG2);
		$table3 = "user_base";
		$row3 = M($table3, '', DB_CONFIG3);
		
		$beginTime = I("beginTime");
		$endTime = I("endTime");
		$channel = I("channel");
		$version = I("version");
		
		//查询不能大于当天
		$timenow = strtotime(date("Y-m-d"));
		if (strtotime($beginTime)>strtotime($endTime)){
			$this->error('开始日期不能大于结束日期');
			exit;
		}
		
		if (!empty($beginTime) && !empty($endTime)){
			$date11 = date("Y-m-d", strtotime($beginTime));
			$date12 = date("Y-m-d", strtotime($endTime));
			$day_jian = (strtotime($date12) - strtotime($date11)) / (60 * 60 * 24) + 1;
			
			//$datenow = date("d", strtotime($endTime));
			//$dateend = date("d", strtotime($beginTime));
		}else{
			$date12 = date("Y-m-d");
			$day_jian = 7;
			$date11 = date("Y-m-d", (strtotime($date12) - 60 * 60 * 24 * 6));
		}
		
		//$date11 .= " 00:00:00"; 
		//$date12 .= " 23:59:59"; 
		//echo $date11."***".$date12."<br>";
		$this->assign('date11',$date11);
		$this->assign('date12',$date12);
		$this->assign('channel',$channel);
		$this->assign('version',$version);
		//计算时间区段
		$seldate = array();
		$seldate[0] = array('date1' => date("Y-m-d"),
							'date2' => date("Y-m-d"));
		$seldate[1] = array('date1' => date("Y-m-d",strtotime("-1 day")),
							'date2' => date("Y-m-d",strtotime("-1 day")));
		$seldate[2] = array('date1' => date("Y-m-d",strtotime("-6 day")),
							'date2' => date("Y-m-d"));
		$seldate[3] = array('date1' => date("Y-m-d",strtotime("-29 day")),
							'date2' => date("Y-m-d"));
		$this->assign('seldate',$seldate);
		
		$sql1 = " and !((user_id>=".ROBERT1_BEGIN." and user_id<".ROBERT1_END.") or (user_id>=".ROBERT2_BEGIN." and user_id<=".ROBERT2_END."))";
		$sql0 = "";
		if (!empty($channel)){
			$sql1 .= " and channel=$channel";
			$sql0 .= " and channel='$channel'";
		}else{
			$sql0 .= " and channel='all'";
		}
		if (!empty($version)){
			$sql1 .= " and version='$version'";
			$sql0 .= " and version='$version'";
		}else{
			$sql0 .= " and version='all'";
		}
		//echo $sql1;
		if ($day_jian >= 0){
			$tongji0 = array();
			$tongji1 = array();
			$tongji2 = array();
			$tongji3 = array();
			$data0 = '';
			$data1 = '';
			$data2 = '';
			$alltotal1 = 0;
			$alltotal2 = 0;
			
			for ($i=1; $i<=$day_jian; $i++){
				$j = $i - 1;
				$time1 = strtotime($date11) + 60 * 60 * 24 * ($i - 1);
				$time2 = $time1 + 60 * 60 * 24;
				$date1 = date("Y-m-d", $time1);
				$date2 = date("Y-m-d", $time2);
				$table1 = "zjhmysql.log_game_record_log_".date("Ym", $time1);
				$row1 = M($table1, '', DB_CONFIG3);
				if ($date1 == date("Y-m-d") || !empty($channel) || !empty($version)){
					
					//新增账号
					$count1 = $row2->where("register_date>='$date1' and register_date<'$date2' $sql1")->count('user_id');
					//新增激活
					$count2 = $row2->where("register_date>='$date1' and register_date<'$date2' $sql1")->count('distinct imei');
					//新增有效
					$res3 = $row2->field('GROUP_CONCAT(user_id) as temp_user_id')->where("register_date>='$date1' and register_date<'$date2' $sql1")->find();
					$temp_user_id = $res3['temp_user_id'];
					if (!empty($temp_user_id)){
						$sql11 = "SELECT COUNT(nums) AS total FROM (SELECT COUNT(user_id) AS nums FROM $table1 WHERE user_id in ($temp_user_id) and (curtime>=$time1 and curtime<$time2) GROUP BY user_id ) AS t1 WHERE nums>=3";
						$res11 = $row1->query($sql11);
						//dump($row1->_sql());
						//print_r($res11);
						$count3 = $res11[0]['total'];
					}else{
						$count3 = 0;
					}
					$tongji = array('data' => $date1,
									'count1' => $count1,
									'count2' => $count2,
									'count3' => $count3);
					$tongji0[$j] = $tongji;	
				}else{
					$total = $row->where("data='$date1' and flag=1 $sql0")->count();
					//dump($row->_sql());
					//echo $date1."**".$total."<br>";
					if ($total == 0){
						//新增账号
						//$count1 = $row2->where("register_date>='$date1' and register_date<'$date2' $sql1")->count('user_id');
						//dump($row2->_sql());
						$info = $row3->where("key_adddate='$date1' and key_name='count2'")->find();
						$count1 = $info['key_value'];
						//新增激活
						$info = $row3->where("key_adddate='$date1' and key_name='user_id_reg'")->find();
						$new_user_id = $info['key_value'];
						if (!empty($new_user_id)){
							$count2 = $row2->where("user_id in ($new_user_id)")->count('distinct imei');
						}else{
							$count2 = 0;
						}
						
						//新增有效
						$info = $row3->where("key_adddate='$date1' and key_name='count3'")->find();
						$count3 = $info['key_value'];
						
						$tongji = array('data' => $date1,
										'count1' => $count1,
										'count2' => $count2,
										'count3' => $count3);
						if ($date1<date("Y-m-d")){
							$data9 = array('data' => $date1,
										   'channel' => empty($channel) ? "all" : $channel,
										   'version' => empty($version) ? "all" : $version,
										   'flag' => 1,
										   'tongji' => json_encode($tongji),
										   'addtime' => time());
							$result = $row->add($data9);			   
						}
						$tongji0[$j] = $tongji;	
					}else{
						$info = $row->where("data='$date1' and flag=1 $sql0")->find();
						$tongji0[$j] = json_decode($info['tongji'], true);
					}
				}
				
			}
			
			//print_r($tongji0);
			
			foreach ($tongji0 as $key => $val){
				$data0 .= ($key==0) ? "'".$val['data']."'" : ",'".$val['data']."'";
				$data1 .= ($key==0) ? $val['count1'] : ",".$val['count1'];
				$data2 .= ($key==0) ? $val['count2'] : ",".$val['count2'];
				$data3 .= ($key==0) ? $val['count3'] : ",".$val['count3'];
					//echo $key."**".$val['count1']."**".$data1."<br>";

				$alltotal1 += $val['count1'];
				$alltotal2 += $val['count2'];
			}

			$this->assign('alltotal1',$alltotal1);
			$this->assign('alltotal2',$alltotal2);
			
			if ($date11 == $date12){
				$time1 = strtotime($date11);
				$time2 = $time1 + 60 * 60 * 24;
				$date1 = date("Y-m-d", $time1);
				$date2 = date("Y-m-d", $time2);
				$date3 = $date2;
			}else{
				$date1 = $date11;
				$date2 = $date12;
				$time3 = strtotime($date2) + 60 * 60 * 24;
				$date3 = date("Y-m-d", $time3);
			}
			
			/*
			//统计版本
			$res1 = $row2->field("gameversion")->where("register_date>='$date1' and register_date<'$date3' $sql1")->group('gameversion')->select();
			//dump($row2->_sql());
			$sort1 = array();
			foreach ($res1 as $key1 => $val1){
				$count11 = $row2->where("register_date>='$date1' and register_date<'$date3' $sql1 and gameversion='".$val1['gameversion']."'")->count('user_id');
				//dump($row2->_sql());
				$count12 = $row2->where("register_date>='$date1' and register_date<'$date3' $sql1 and gameversion='".$val1['gameversion']."'")->count('distinct imei');
				//dump($row2->_sql());
				if (!empty($val1['gameversion'])){
					$sort1[] = $count11;
					$tongji1[] = array('version' => $val1['gameversion'],
								   'count1' => $count11,
								   'count2' => $count12);
				}
				
			}
			array_multisort($sort1, SORT_DESC,  $tongji1);
				
			//统计渠道
			$res2 = $row2->field("channel")->where("register_date>='$date1' and register_date<'$date3' $sql1")->group('channel')->select();
			//dump($row2->_sql());
			$total2 = 0;
			foreach ($res2 as $key2 => $val2){
				$count12 = $row2->where("register_date>='$date1' and register_date<'$date3' $sql1 and channel='".$val2['channel']."'")->count('distinct imei');
				$total2 += $count12;
				$tongji2[] = array('channel' => $val2['channel'],
								   'count1' => $count12);
			}
			if ($total2 > 0){
				foreach($tongji2 as $key => $val){
					$bl = round($val['count1'] / $total2 * 1000) / 10;
					$tongji2[$key]['bl'] = $bl."%";
				}
			}
			
			//单设备账户数量分析
			//未注册
			for($i=0; $i<4; $i++){
				if ($i==0){$xiaohao = '未注册(游客)';} elseif($i==1){$xiaohao = $i.'个(绑定账号)';} else{$xiaohao = $i.'个(该设备有'.$i.'个账号)';}
				$tongji3[$i] = array('xiaohao' => $xiaohao,
									 'count1' => 0,
									 'bl' => 0); 
			}
			
			$total30 = $row2->where("register_date>='$date1' and register_date<'$date3' $sql1 and user_pwd=''")->count('distinct imei');
			//dump($row2->_sql());
			$total3 = $total30;
			$tongji3[0]['count1'] = $total30; 
			$res3 = $row2->field("imei")->where("register_date>='$date1' and register_date<'$date3' $sql1 and user_pwd!=''")->group('imei')->select();
			foreach ($res3 as $key3 => $val3){
				$count11 = $row2->where("register_date>='$date1' and register_date<'$date3' $sql1 and imei='".$val3['imei']."' and user_pwd!=''")->count('user_id');
				if ($count11==1){
					$tongji3[1]['count1'] += 1; 
					$total3 += 1;
				}elseif ($count11==2){
					$tongji3[2]['count1'] += 1; 
					$total3 += 1;
				}elseif ($count11==3){
					$tongji3[3]['count1'] += 1; 
					$total3 += 1;
				}
			}
			if ($total3 > 0){
				foreach($tongji3 as $key => $val){
					$bl = round($val['count1'] / $total3 * 1000) / 10;
					$tongji3[$key]['bl'] = $bl."%";
				}
			}*/
		} 
		
		$pagesize = ceil($day_jian / 10);
		$this->assign('pagesize',$pagesize);
		$this->assign('tongji',json_encode($tongji0));
		
		$pagesize1 = ceil(count($tongji1) / 10);
		$this->assign('pagesize1',$pagesize1);
		$this->assign('tongji1',json_encode($tongji1));
		
		$pagesize2 = ceil(count($tongji2) / 10);
		$this->assign('pagesize2',$pagesize2);
		$this->assign('tongji2',json_encode($tongji2));
		
		$pagesize3 = ceil(count($tongji3) / 10);
		$this->assign('pagesize3',$pagesize3);
		$this->assign('tongji3',json_encode($tongji3));
		
		/*引入GoogChart类*/
		import("ORG.Util.GoogChart");
		$chart = new GoogChart();	

		//时间轴
		$this->assign('data0',$data0);	
		//新增账号数据
		$this->assign('data1',$data1);	
		//新增激活数据
		$this->assign('data2',$data2);
		$this->assign('data3',$data3);
		
		$this->assign('left_css',"14");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/showtu";
		$this->display($lib_display);
	}
	
	public function tongji51(){
		$table = "fx_tongji5";
		$row = M($table);
		$table2 = "user_info";
		$row2 = M($table2, '', DB_CONFIG2);
		
		$beginTime = I("beginTime");
		$endTime = I("endTime");
		$channel = I("channel");
		$version = I("version");
		
		//查询不能大于当天
		$timenow = strtotime(date("Y-m-d"));
		if (strtotime($beginTime)>strtotime($endTime)){
			$this->error('开始日期不能大于结束日期');
			exit;
		}
		
		if (!empty($beginTime) && !empty($endTime)){
			$date11 = date("Y-m-d", strtotime($beginTime));
			$date12 = date("Y-m-d", strtotime($endTime));
			$day_jian = (strtotime($date12) - strtotime($date11)) / (60 * 60 * 24) + 1;
			
			//$datenow = date("d", strtotime($endTime));
			//$dateend = date("d", strtotime($beginTime));
		}else{
			$date12 = date("Y-m-d");
			$day_jian = 7;
			$date11 = date("Y-m-d", (strtotime($date12) - 60 * 60 * 24 * 6));
		}
		
		//$date11 .= " 00:00:00"; 
		//$date12 .= " 23:59:59"; 
		
		$this->assign('date11',$date11);
		$this->assign('date12',$date12);
		$this->assign('channel',$channel);
		$this->assign('version',$version);
		//计算时间区段
		$seldate = array();
		$seldate[0] = array('date1' => date("Y-m-d"),
							'date2' => date("Y-m-d"));
		$seldate[1] = array('date1' => date("Y-m-d",strtotime("-1 day")),
							'date2' => date("Y-m-d",strtotime("-1 day")));
		$seldate[2] = array('date1' => date("Y-m-d",strtotime("-6 day")),
							'date2' => date("Y-m-d"));
		$seldate[3] = array('date1' => date("Y-m-d",strtotime("-29 day")),
							'date2' => date("Y-m-d"));
		$this->assign('seldate',$seldate);
		
		$sql1 = " and !((user_id>=".ROBERT1_BEGIN." and user_id<".ROBERT1_END.") or (user_id>=".ROBERT2_BEGIN." and user_id<=".ROBERT2_END."))";
		$sql0 = "";
		if (!empty($channel)){
			$sql1 .= " and channel=$channel";
			$sql0 .= " and channel='$channel'";
		}else{
			$sql0 .= " and channel='all'";
		}
		if (!empty($version)){
			$sql1 .= " and version='$version'";
			$sql0 .= " and version='$version'";
		}else{
			$sql0 .= " and version='all'";
		}
		//echo $sql1;
		if ($day_jian >= 0){
			$tongji0 = array();
			$tongji1 = array();
			$tongji2 = array();
			$tongji3 = array();
			$data0 = '';
			$data1 = '';
			$data2 = '';
			$alltotal1 = 0;
			$alltotal2 = 0;
			
			if ($date11 == $date12){
				$time1 = strtotime($date11);
				$time2 = $time1 + 60 * 60 * 24;
				$date1 = date("Y-m-d", $time1);
				$date2 = date("Y-m-d", $time2);
				$date3 = $date2;
			}else{
				$date1 = $date11;
				$date2 = $date12;
				$time3 = strtotime($date2) + 60 * 60 * 24;
				$date3 = date("Y-m-d", $time3);
			}
			
			//统计版本
			$res1 = $row2->field("gameversion")->where("register_date>='$date1' and register_date<'$date3' $sql1")->group('gameversion')->select();
			//dump($row2->_sql());
			$sort1 = array();
			foreach ($res1 as $key1 => $val1){
				$count11 = $row2->where("register_date>='$date1' and register_date<'$date3' $sql1 and gameversion='".$val1['gameversion']."'")->count('user_id');
				//dump($row2->_sql());
				$count12 = $row2->where("register_date>='$date1' and register_date<'$date3' $sql1 and gameversion='".$val1['gameversion']."'")->count('distinct imei');
				//dump($row2->_sql());
				if (!empty($val1['gameversion'])){
					$sort1[] = $count11;
					$tongji1[] = array('version' => $val1['gameversion'],
								   'count1' => $count11,
								   'count2' => $count12);
				}
				
			}
			array_multisort($sort1, SORT_DESC,  $tongji1);
		} 
		
		$pagesize1 = ceil(count($tongji1) / 10);
		$this->assign('pagesize1',$pagesize1);
		$this->assign('tongji1',json_encode($tongji1));
		
		
		/*引入GoogChart类*/
		import("ORG.Util.GoogChart");
		$chart = new GoogChart();	

		//时间轴
		$this->assign('data0',$data0);	
		//新增账号数据
		$this->assign('data1',$data1);	
		//新增激活数据
		$this->assign('data2',$data2);
		$this->assign('data3',$data3);
		
		$this->assign('left_css',"14");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji51";
		$this->display($lib_display);
	}
	
	public function tongji52(){
		$table = "fx_tongji5";
		$row = M($table);
		$table2 = "user_info";
		$row2 = M($table2, '', DB_CONFIG2);
		
		$beginTime = I("beginTime");
		$endTime = I("endTime");
		$channel = I("channel");
		$version = I("version");
		
		//查询不能大于当天
		$timenow = strtotime(date("Y-m-d"));
		if (strtotime($beginTime)>strtotime($endTime)){
			$this->error('开始日期不能大于结束日期');
			exit;
		}
		
		if (!empty($beginTime) && !empty($endTime)){
			$date11 = date("Y-m-d", strtotime($beginTime));
			$date12 = date("Y-m-d", strtotime($endTime));
			$day_jian = (strtotime($date12) - strtotime($date11)) / (60 * 60 * 24) + 1;
			
			//$datenow = date("d", strtotime($endTime));
			//$dateend = date("d", strtotime($beginTime));
		}else{
			$date12 = date("Y-m-d");
			$day_jian = 7;
			$date11 = date("Y-m-d", (strtotime($date12) - 60 * 60 * 24 * 6));
		}
		
		//$date11 .= " 00:00:00"; 
		//$date12 .= " 23:59:59"; 
		
		$this->assign('date11',$date11);
		$this->assign('date12',$date12);
		$this->assign('channel',$channel);
		$this->assign('version',$version);
		//计算时间区段
		$seldate = array();
		$seldate[0] = array('date1' => date("Y-m-d"),
							'date2' => date("Y-m-d"));
		$seldate[1] = array('date1' => date("Y-m-d",strtotime("-1 day")),
							'date2' => date("Y-m-d",strtotime("-1 day")));
		$seldate[2] = array('date1' => date("Y-m-d",strtotime("-6 day")),
							'date2' => date("Y-m-d"));
		$seldate[3] = array('date1' => date("Y-m-d",strtotime("-29 day")),
							'date2' => date("Y-m-d"));
		$this->assign('seldate',$seldate);
		
		$sql1 = " and !((user_id>=".ROBERT1_BEGIN." and user_id<".ROBERT1_END.") or (user_id>=".ROBERT2_BEGIN." and user_id<=".ROBERT2_END."))";
		$sql0 = "";
		if (!empty($channel)){
			$sql1 .= " and channel=$channel";
			$sql0 .= " and channel='$channel'";
		}else{
			$sql0 .= " and channel='all'";
		}
		if (!empty($version)){
			$sql1 .= " and version='$version'";
			$sql0 .= " and version='$version'";
		}else{
			$sql0 .= " and version='all'";
		}
		//echo $sql1;
		if ($day_jian >= 0){
			$tongji0 = array();
			$tongji1 = array();
			$tongji2 = array();
			$tongji3 = array();
			$data0 = '';
			$data1 = '';
			$data2 = '';
			$alltotal1 = 0;
			$alltotal2 = 0;
			
			if ($date11 == $date12){
				$time1 = strtotime($date11);
				$time2 = $time1 + 60 * 60 * 24;
				$date1 = date("Y-m-d", $time1);
				$date2 = date("Y-m-d", $time2);
				$date3 = $date2;
			}else{
				$date1 = $date11;
				$date2 = $date12;
				$time3 = strtotime($date2) + 60 * 60 * 24;
				$date3 = date("Y-m-d", $time3);
			}
			
				
			//统计渠道
			$res2 = $row2->field("channel")->where("register_date>='$date1' and register_date<'$date3' $sql1")->group('channel')->select();
			//dump($row2->_sql());
			$total2 = 0;
			foreach ($res2 as $key2 => $val2){
				$count12 = $row2->where("register_date>='$date1' and register_date<'$date3' $sql1 and channel='".$val2['channel']."'")->count('distinct imei');
				$total2 += $count12;
				$tongji2[] = array('channel' => $val2['channel'],
								   'count1' => $count12);
			}
			if ($total2 > 0){
				foreach($tongji2 as $key => $val){
					$bl = round($val['count1'] / $total2 * 1000) / 10;
					$tongji2[$key]['bl'] = $bl."%";
				}
			}
			
			//单设备账户数量分析
			//未注册
			for($i=0; $i<4; $i++){
				if ($i==0){$xiaohao = '未注册(游客)';} elseif($i==1){$xiaohao = $i.'个(绑定账号)';} else{$xiaohao = $i.'个(该设备有'.$i.'个账号)';}
				$tongji3[$i] = array('xiaohao' => $xiaohao,
									 'count1' => 0,
									 'bl' => 0); 
			}

		} 
		
		
		$pagesize2 = ceil(count($tongji2) / 10);
		$this->assign('pagesize2',$pagesize2);
		$this->assign('tongji2',json_encode($tongji2));

		
		/*引入GoogChart类*/
		import("ORG.Util.GoogChart");
		$chart = new GoogChart();	

		//时间轴
		$this->assign('data0',$data0);	
		//新增账号数据
		$this->assign('data1',$data1);	
		//新增激活数据
		$this->assign('data2',$data2);
		$this->assign('data3',$data3);
		
		$this->assign('left_css',"14");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji52";
		$this->display($lib_display);
	}
	
	public function tongji53(){
		$table = "fx_tongji5";
		$row = M($table);
		$table2 = "user_info";
		$row2 = M($table2, '', DB_CONFIG2);
		
		$beginTime = I("beginTime");
		$endTime = I("endTime");
		$channel = I("channel");
		$version = I("version");
		
		//查询不能大于当天
		$timenow = strtotime(date("Y-m-d"));
		if (strtotime($beginTime)>strtotime($endTime)){
			$this->error('开始日期不能大于结束日期');
			exit;
		}
		
		if (!empty($beginTime) && !empty($endTime)){
			$date11 = date("Y-m-d", strtotime($beginTime));
			$date12 = date("Y-m-d", strtotime($endTime));
			$day_jian = (strtotime($date12) - strtotime($date11)) / (60 * 60 * 24) + 1;
			
			//$datenow = date("d", strtotime($endTime));
			//$dateend = date("d", strtotime($beginTime));
		}else{
			$date12 = date("Y-m-d");
			$day_jian = 7;
			$date11 = date("Y-m-d", (strtotime($date12) - 60 * 60 * 24 * 6));
		}
		
		//$date11 .= " 00:00:00"; 
		//$date12 .= " 23:59:59"; 
		
		$this->assign('date11',$date11);
		$this->assign('date12',$date12);
		$this->assign('channel',$channel);
		$this->assign('version',$version);
		//计算时间区段
		$seldate = array();
		$seldate[0] = array('date1' => date("Y-m-d"),
							'date2' => date("Y-m-d"));
		$seldate[1] = array('date1' => date("Y-m-d",strtotime("-1 day")),
							'date2' => date("Y-m-d",strtotime("-1 day")));
		$seldate[2] = array('date1' => date("Y-m-d",strtotime("-6 day")),
							'date2' => date("Y-m-d"));
		$seldate[3] = array('date1' => date("Y-m-d",strtotime("-29 day")),
							'date2' => date("Y-m-d"));
		$this->assign('seldate',$seldate);
		
		$sql1 = " and !((user_id>=".ROBERT1_BEGIN." and user_id<".ROBERT1_END.") or (user_id>=".ROBERT2_BEGIN." and user_id<=".ROBERT2_END."))";
		$sql0 = "";
		if (!empty($channel)){
			$sql1 .= " and channel=$channel";
			$sql0 .= " and channel='$channel'";
		}else{
			$sql0 .= " and channel='all'";
		}
		if (!empty($version)){
			$sql1 .= " and version='$version'";
			$sql0 .= " and version='$version'";
		}else{
			$sql0 .= " and version='all'";
		}
		//echo $sql1;
		if ($day_jian >= 0){
			$tongji0 = array();
			$tongji1 = array();
			$tongji2 = array();
			$tongji3 = array();
			$data0 = '';
			$data1 = '';
			$data2 = '';
			$alltotal1 = 0;
			$alltotal2 = 0;
			
			if ($date11 == $date12){
				$time1 = strtotime($date11);
				$time2 = $time1 + 60 * 60 * 24;
				$date1 = date("Y-m-d", $time1);
				$date2 = date("Y-m-d", $time2);
				$date3 = $date2;
			}else{
				$date1 = $date11;
				$date2 = $date12;
				$time3 = strtotime($date2) + 60 * 60 * 24;
				$date3 = date("Y-m-d", $time3);
			}

			$total30 = $row2->where("register_date>='$date1' and register_date<'$date3' $sql1 and user_pwd=''")->count('distinct imei');
			//dump($row2->_sql());
			$total3 = $total30;
			$tongji3[0]['count1'] = $total30; 
			$res3 = $row2->field("imei")->where("register_date>='$date1' and register_date<'$date3' $sql1 and user_pwd!=''")->group('imei')->select();
			foreach ($res3 as $key3 => $val3){
				$count11 = $row2->where("register_date>='$date1' and register_date<'$date3' $sql1 and imei='".$val3['imei']."' and user_pwd!=''")->count('user_id');
				if ($count11==1){
					$tongji3[1]['count1'] += 1; 
					$total3 += 1;
				}elseif ($count11==2){
					$tongji3[2]['count1'] += 1; 
					$total3 += 1;
				}elseif ($count11==3){
					$tongji3[3]['count1'] += 1; 
					$total3 += 1;
				}
			}
			if ($total3 > 0){
				foreach($tongji3 as $key => $val){
					$bl = round($val['count1'] / $total3 * 1000) / 10;
					$tongji3[$key]['bl'] = $bl."%";
				}
			}
		} 
		
	
		$pagesize3 = ceil(count($tongji3) / 10);
		$this->assign('pagesize3',$pagesize3);
		$this->assign('tongji3',json_encode($tongji3));
		
		/*引入GoogChart类*/
		import("ORG.Util.GoogChart");
		$chart = new GoogChart();	

		//时间轴
		$this->assign('data0',$data0);	
		//新增账号数据
		$this->assign('data1',$data1);	
		//新增激活数据
		$this->assign('data2',$data2);
		$this->assign('data3',$data3);
		
		$this->assign('left_css',"14");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji53";
		$this->display($lib_display);
	}
	
	public function tongji6(){
		$table = "fx_tongji6";
		$row = M($table);
		$table1 = "user_info";
		$row1 = M($table1, '', DB_CONFIG2);
		$table3 = "user_base";
		$row3 = M($table3, '', DB_CONFIG3);
		
		
		$beginTime = I("beginTime");
		$endTime = I("endTime");
		$channel = I("channel");
		$version = I("version");
		
		//查询不能大于当天
		$timenow = strtotime(date("Y-m-d"));
		if (strtotime($beginTime)>strtotime($endTime)){
			$this->error('开始日期不能大于结束日期');
			exit;
		}
		
		if (!empty($beginTime) && !empty($endTime)){
			$date11 = date("Y-m-d", strtotime($beginTime));
			$date12 = date("Y-m-d", strtotime($endTime));
			$day_jian = (strtotime($date12) - strtotime($date11)) / (60 * 60 * 24) + 1;
			
			//$datenow = date("d", strtotime($endTime));
			//$dateend = date("d", strtotime($beginTime));
		}else{
			$date12 = date("Y-m-d",strtotime("-1 day"));
			$day_jian = 7;
			$date11 = date("Y-m-d",strtotime("-7 day"));
		}
		//echo $day_jian;
		//$date11 .= " 00:00:00"; 
		//$date12 .= " 23:59:59"; 
		
		$this->assign('date11',$date11);
		$this->assign('date12',$date12);
		$this->assign('channel',$channel);
		$this->assign('version',$version);
		//计算时间区段
		$seldate = array();
		$seldate[0] = array('date1' => date("Y-m-d"),
							'date2' => date("Y-m-d"));
		$seldate[1] = array('date1' => date("Y-m-d",strtotime("-1 day")),
							'date2' => date("Y-m-d",strtotime("-1 day")));
		$seldate[2] = array('date1' => date("Y-m-d",strtotime("-6 day")),
							'date2' => date("Y-m-d"));
		$seldate[3] = array('date1' => date("Y-m-d",strtotime("-29 day")),
							'date2' => date("Y-m-d"));
		$this->assign('seldate',$seldate);
		
		$sql0 = "";
		$sql1 = " !((user_id>=".ROBERT1_BEGIN." and user_id<".ROBERT1_END.") or (user_id>=".ROBERT2_BEGIN." and user_id<=".ROBERT2_END."))";
		if (!empty($channel)){
			$sql1 .= " and channel=$channel";
			$sql0 .= " and channel='$channel'";
		}else{
			$sql0 .= " and channel='all'";
		}
		if (!empty($version)){
			$sql1 .= " and gameversion='$version'";
			$sql0 .= " and version='$version'";
		}else{
			$sql0 .= " and version='all'";
		}
	
		if ($day_jian >= 0){
			$tongji_show = array();
			$tongji1 = array();
			$tongji2 = array();
			$tongji3 = array();
			$tongji4 = array();
			$data0 = '';
			$data1 = array();
			$data2 = array();
			$data3 = array();
			$data4 = array();
			$alltotal1 = 0;
			$alltotal2 = 0;
			
			for ($i=1; $i<=$day_jian; $i++){
				$j = $i - 1;	
				$time1 = strtotime($date11) + 60 * 60 * 24 * ($i - 1);
				$time2 = $time1 + 60 * 60 * 24;
				$date1 = date("Y-m-d", $time1);
				$date2 = date("Y-m-d", $time2);
				$table2 = "log_login_".date("Ymd", $time1);
				//$table2 = "login_log";
				$row2 = M($table2, '', DB_CONFIG3);
				
				$total = $row->where("data='$date1' and flag=6 $sql0")->count();
				if ($total == 0){
					//新玩家
					//$count1 = $row1->where("register_date>='$date1' and register_date<'$date2' and $sql1")->count('distinct user_id');
					//dump($row1->_sql());
					$info = $row3->where("key_adddate='$date1' and key_name='count2'")->find();
					//dump($row3->_sql());
					$count1 = $info['key_value'];
					//总计
					//$count3 = $row2->where($sql1)->count('distinct user_id');
					$info = $row3->where("key_adddate='$date1' and key_name='count1'")->find();
					$count3 = $info['key_value'];
					//老玩家
					$count2 = $count3 - $count1;
					//新增设备数
					//$count4 = $row1->where("register_date>='$date1' and register_date<'$date2' and $sql1")->count('distinct imei');
					$info = $row3->where("key_adddate='$date1' and key_name='user_id_reg'")->find();
					$new_user_id = $info['key_value'];
					if (!empty($new_user_id)){
						$count4 = $row1->where("user_id in ($new_user_id)")->count('distinct imei');
					}else{
						$count4 = 0;
					}
					
					//活跃设备数
					//$count5 = $row2->where($sql1)->count('distinct imei');
					$info = $row3->where("key_adddate='$date1' and key_name='user_id_log'")->find();
					$login_user_id = $info['key_value'];
					if (!empty($login_user_id)){
						$count5 = $row1->where("user_id in ($login_user_id)")->count('distinct imei');
						$arr_log = explode(",", $login_user_id);
					}else{
						$count5 = 0;
						$arr_log = array();
					}
					
					//7,30日内有登陆的玩家
					$time13 = $time1 - 60 * 60 * 24 * 7;
					$date13 = date("Y-m-d", $time13);
					$info = $row3->where("key_adddate='$date13' and key_name='user_id_reg'")->find();
					$user_id_reg = $info['key_value'];
					if (!empty($user_id_reg)) {$arr_reg = explode(",", $user_id_reg); $count6 = count($arr_reg);} else {$count6 = 0;}
					if (!empty($arr_reg)) {
						$user_act = array_intersect($arr_reg, $arr_log);
						$login1 = count($user_act);
						$count8 = (empty($count1)) ? 0 : round($login1/$count6,3) * 100;
					}else{
						$count8 = 0;
					}
					
					$time13 = $time1 - 60 * 60 * 24 * 30;
					$date13 = date("Y-m-d", $time13);
					$info = $row3->where("key_adddate='$date13' and key_name='user_id_reg'")->find();
					$user_id_reg = $info['key_value'];
					if (!empty($user_id_reg)) {$arr_reg = explode(",", $user_id_reg); $count7 = count($arr_reg);} else {$count7 = 0;}
					if (!empty($arr_reg)) {
						$user_act = array_intersect($arr_reg, $arr_log);
						$login1 = count($user_act);
						$count9 = (empty($count1)) ? 0 : round($login1/$count7,3) * 100;
					}else{
						$count9 = 0;
					}
					
					$tongji = array('data' => $date1,'count1' => $count1,'count2' => $count2,'count3' => $count3,'count4' => $count4,'count5' => $count5,'count6' => $count6,'count7' => $count7,'count8' => $count8,'count9' => $count9);
					
					
					if ($date1 < date("Y-m-d")){
						$data9 = array('data' => $date1,
									   'flag' => 6,
									   'channel' => empty($channel) ? "all" : $channel,
									   'version' => empty($version) ? "all" : $version,
									   'tongji' => json_encode($tongji),
									   'addtime' => time());
						$result = $row->add($data9);			   
					}
					$tongji1[$j] = $tongji;	
				}else{
					$info = $row->where("data='$date1' and flag=6 $sql0")->find();
					$tongji1[$j] = json_decode($info['tongji'], true);
				}
				
				
			}

			foreach ($tongji1 as $key => $val){
				$data1[0] .= ($key==0) ? "'".$val['data']."'" : ",'".$val['data']."'";
				$data1[1] .= ($key==0) ? $val['count1'] : ",".$val['count1'];
				$data1[2] .= ($key==0) ? $val['count2'] : ",".$val['count2'];
				$data1[3] .= ($key==0) ? $val['count3'] : ",".$val['count3'];
				
				$data2[0] .= ($key==0) ? "'".$val['data']."'" : ",'".$val['data']."'";
				$data2[1] .= ($key==0) ? $val['count6'] : ",".$val['count6'];
				
				$data3[0] .= ($key==0) ? "'".$val['data']."'" : ",'".$val['data']."'";
				$data3[1] .= ($key==0) ? $val['count7'] : ",".$val['count7'];
				
				$data4[0] .= ($key==0) ? "'".$val['data']."'" : ",'".$val['data']."'";
				$data4[1] .= ($key==0) ? $val['count8'] : ",".$val['count8'];
				
				$data5[0] .= ($key==0) ? "'".$val['data']."'" : ",'".$val['data']."'";
				$data5[1] .= ($key==0) ? $val['count9'] : ",".$val['count9'];
				
				$tongji2[$key] = array('data' => $val['data'], 'count1' => $val['count6']);
				$tongji3[$key] = array('data' => $val['data'], 'count1' => $val['count7']);
				$tongji4[$key] = array('data' => $val['data'], 'count1' => $val['count8']);
				$tongji5[$key] = array('data' => $val['data'], 'count1' => $val['count9']);
			}
		} 

		$pagesize = ceil($day_jian / 10);
		$this->assign('pagesize',$pagesize);
		$this->assign('tongji1',json_encode($tongji1));
		$this->assign('tongji2',json_encode($tongji2));
		$this->assign('tongji3',json_encode($tongji3));
		$this->assign('tongji4',json_encode($tongji4));
		$this->assign('tongji5',json_encode($tongji5));
		
		/*引入GoogChart类*/
		import("ORG.Util.GoogChart");
		$chart = new GoogChart();	

		//时间轴\新增账号数据\新增激活数据
		$this->assign('data0',$data0);	
		$this->assign('data1',$data1);	
		$this->assign('data2',$data2);
		$this->assign('data3',$data3);
		$this->assign('data4',$data4);
		$this->assign('data5',$data5);
		
		$this->assign('left_css',"14");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji6";
		$this->display($lib_display);
	}
	
	
	public function tongji7(){
		$table = "fx_tongji7";
		$row = M($table);
		$table1 = "user_info";
		$row1 = M($table1, '', DB_CONFIG2);
		$table3 = "user_base";
		$row3 = M($table3, '', DB_CONFIG3);
		
		$beginTime = I("beginTime");
		$endTime = I("endTime");
		$channel = I("channel");
		$version = I("version");
		
		//查询不能大于当天
		$timenow = strtotime(date("Y-m-d"));
		if (strtotime($beginTime)>strtotime($endTime)){
			$this->error('开始日期不能大于结束日期');
			exit;
		}
		
		if (!empty($beginTime) && !empty($endTime)){
			$date11 = date("Y-m-d", strtotime($beginTime));
			$date12 = date("Y-m-d", strtotime($endTime));
			$day_jian = (strtotime($date12) - strtotime($date11)) / (60 * 60 * 24) + 1;
			
			//$datenow = date("d", strtotime($endTime));
			//$dateend = date("d", strtotime($beginTime));
		}else{
			$date12 = date("Y-m-d",strtotime("-2 day"));
			$day_jian = 7;
			$date11 = date("Y-m-d", (strtotime($date12) - 60 * 60 * 24 * 6));
		}
		//echo $day_jian;
		//$date11 .= " 00:00:00"; 
		//$date12 .= " 23:59:59"; 
		
		$this->assign('date11',$date11);
		$this->assign('date12',$date12);
		$this->assign('channel',$channel);
		//计算时间区段
		$seldate = array();
		$seldate[0] = array('date1' => date("Y-m-d"),
							'date2' => date("Y-m-d"));
		$seldate[1] = array('date1' => date("Y-m-d",strtotime("-1 day")),
							'date2' => date("Y-m-d",strtotime("-1 day")));
		$seldate[2] = array('date1' => date("Y-m-d",strtotime("-6 day")),
							'date2' => date("Y-m-d"));
		$seldate[3] = array('date1' => date("Y-m-d",strtotime("-29 day")),
							'date2' => date("Y-m-d"));
		$this->assign('seldate',$seldate);
		
		$sql1 = " and !((user_id>=".ROBERT1_BEGIN." and user_id<".ROBERT1_END.") or (user_id>=".ROBERT2_BEGIN." and user_id<=".ROBERT2_END."))";
		$sql0 = "";
		if (!empty($channel)){
			$sql1 .= " and channel=$channel";
			$sql0 .= " and channel='$channel'";
		}else{
			$sql0 .= " and channel='all'";
		}
		if (!empty($version)){
			$sql1 .= " and version='$version'";
			$sql0 .= " and version='$version'";
		}else{
			$sql0 .= " and version='all'";
		}
	
		if ($day_jian >= 0){
			$tongji_show = array();
			$tongji1 = array();
			$tongji2 = array();
			$tongji3 = array();
			$tongji4 = array();
			$data0 = '';
			$data1 = array();
			$data2 = array();
			$data3 = array();
			$data4 = array();
			$alltotal1 = 0;
			$alltotal2 = 0;
			
			for ($i=1; $i<=$day_jian; $i++){
				$j = $i - 1;	
				$time1 = strtotime($date11) + 60 * 60 * 24 * ($i - 1);
				$time2 = $time1 + 60 * 60 * 24;
				$date1 = date("Y-m-d", $time1);
				$date2 = date("Y-m-d", $time2);
				
				$table2 = "login_log_".date("Ym", $time1);
				//$table2 = "login_log";
				$row2 = M($table2, '', DB_CONFIG2);
				
				$total = $row->where("data='$date1' $sql0")->count();
				//dump($row->_sql());
				//ECHO $total."**<br>"; 
				$total = 0;
				if ($total == 0){
					//次日留存率
					$time31 = $time1 + 60 * 60 * 24;
					$time32 = $time31 + 60 * 60 * 24;
					$date31 = date("Y-m-d", $time31);
					$date32 = date("Y-m-d", $time32);
					//7日留存率
					$time41 = $time1 + 60 * 60 * 24 * 7;
					$time42 = $time41 + 60 * 60 * 24;
					$date41 = date("Y-m-d", $time41);
					$date42 = date("Y-m-d", $time42);
					//30日留存率
					$time51 = $time1 + 60 * 60 * 24 * 30;
					$time52 = $time51 + 60 * 60 * 24;
					$date51 = date("Y-m-d", $time51);
					$date52 = date("Y-m-d", $time52);
					//$count10 = $row1->where("register_date>='$date1' and register_date<'$date2' $sql1")->count('user_id');
					
					$info = $row3->where("key_adddate='$date1' and key_name='count2'")->find();
					$count10 = $info['key_value'];
					
					$info = $row3->where("key_adddate='$date1' and key_name='user_id_reg'")->find();
					$user_id_reg = $info['key_value'];
					if (!empty($user_id_reg)) {$arr_reg = explode(",", $user_id_reg);} else {$arr_reg = array();}
					
					$info = $row3->where("key_adddate='$date31' and key_name='user_id_log'")->find();
					$user_id_log = $info['key_value'];
					if (!empty($user_id_log)) {$arr_log = explode(",", $user_id_log);} else {$arr_log = array();}
					if (!empty($arr_reg)) {
						$user_act = array_intersect($arr_reg, $arr_log);
						$count11 = count($user_act);
						$count1 = (empty($count11)) ? 0 : round($count11/$count10,3) * 100;
					}else{
						$count1 = 0;
						$count11 = 0;
					}
					
					$info = $row3->where("key_adddate='$date41' and key_name='user_id_log'")->find();
					$user_id_log = $info['key_value'];
					if (!empty($user_id_log)) {$arr_log = explode(",", $user_id_log);} else {$arr_log = array();}
					if (!empty($arr_reg)) {
						$user_act = array_intersect($arr_reg, $arr_log);
						$count12 = count($user_act);
						$count2 = (empty($count12)) ? 0 : round($count12/$count10,3) * 100;
					}else{
						$count2 = 0;
						$count12 = 0;
					}
					
					$info = $row3->where("key_adddate='$date51' and key_name='user_id_log'")->find();
					$user_id_log = $info['key_value'];
					if (!empty($user_id_log)) {$arr_log = explode(",", $user_id_log);} else {$arr_log = array();}
					if (!empty($arr_reg)) {
						$user_act = array_intersect($arr_reg, $arr_log);
						$count13 = count($user_act);
						$count3 = (empty($count13)) ? 0 : round($count13/$count10,3) * 100;
					}else{
						$count3 = 0;
						$count13 = 0;
					}
					
									
										
					$tongji = array('data' => $date1,
								    'count1' => $count1,
								    'count2' => $count2,
								    'count3' => $count3);
					if ($date1<date("Y-m-d")){
						/*
						$data9 = array('data' => $date1,
									   'tongji' => json_encode($tongji),
									   'addtime' => time());
						$result = $row->add($data9);	
						*/		
					}
					$tongji1[$j] = $tongji;	
				}else{
					$info = $row->where("data='$date1' $sql0")->find();
					$tongji1[$j] = json_decode($info['tongji'], true);
				}
				
				
				$total = $row->where("data='$date1' $sql0")->count();
				$total = 0;
				if ($total == 0){
					$total_day = array(1,2,3,4,5,6,7,15,30);
					$show_day = array();
					for($k=0; $k<count($total_day); $k++){
						$time31 = $time1 + 60 * 60 * 24 * $total_day[$k];
						$time32 = $time31 + 60 * 60 * 24;
						$date31 = date("Y-m-d", $time31);
						$date32 = date("Y-m-d", $time32);
						$show_day[] = array('day' => $total_day[$k],
											'count' => ($date1>=date("Y-m-d") || $date31>date("Y-m-d")) ? "-1" : 0,
											'bl' => ($date1>=date("Y-m-d") || $date31>date("Y-m-d")) ? "--" : 0,
											'date31' => $date31,
											'date32' => $date32);
					}
					//print_r($arr_reg);
					
					if ($date1<date("Y-m-d")){
					
						$info = $row3->where("key_adddate='$date1' and key_name='user_id_reg'")->find();
						$user_id_reg = $info['key_value'];
						if (!empty($user_id_reg)) {$arr_reg = explode(",", $user_id_reg);} else {$arr_reg = array();}
						//echo "reg"; print_r($arr_reg);
						

						foreach($show_day as $key2 =>$val2){
							//echo $val2['date31']."**".date("Y-m-d")."<br>";
							if ($val2['date31'] > date("Y-m-d")){
									$show_day[$key2]['count'] = "-1";
							}else{
									//$total1 = $row2->where("login_date>='".$val2['date31']."' and login_date<'".$val2['date32']."' and user_id=".$val1['user_id']." $sql1")->count('distinct user_id');
									$info = $row3->where("key_adddate='".$val2['date31']."' and key_name='user_id_log'")->find();
									//dump($row3->_sql());
									$user_id_log = $info['key_value'];
									$arr_log = explode(",", $user_id_log);
									//print_r($arr_log);
									$user_act = array_intersect($arr_reg, $arr_log);
									//echo "user_act"; print_r($user_act);
									$show_day[$key2]['count'] = count($user_act);

							}
						}

						
						
						//print_r($show_day);
						foreach($show_day as $key2 =>$val2){
							if ($val2['count']=='-1'){
								$show_day[$key2]['bl'] = "--";
							}else{
								if ($count10 == 0){
									$show_day[$key2]['bl'] = 0;
								}else{
									$show_day[$key2]['bl'] = round($val2['count'] / $count10, 3) * 100;
								}
							}
						}
						//print_r($show_day);
					}
					//exit;

					
										
					$tongji = array('data' => $date1,
									'count1' => $count10,
								    'show_day' => $show_day);
					if ($date1<date("Y-m-d")){
						/*
						$data9 = array('data' => $date1,
									   'tongji' => json_encode($tongji),
									   'addtime' => time());
						$result = $row->add($data9);	
						*/		
					}
					$tongji2[$j] = $tongji;	
				}else{
					$info = $row->where("data='$date1' $sql0")->find();
					$tongji2[$j] = json_decode($info['tongji'], true);
				}
			}

			foreach ($tongji1 as $key => $val){
				$data1[0] .= ($key==0) ? "'".$val['data']."'" : ",'".$val['data']."'";
				$data1[1] .= ($key==0) ? $val['count1'] : ",".$val['count1'];
				$data1[2] .= ($key==0) ? $val['count2'] : ",".$val['count2'];
				$data1[3] .= ($key==0) ? $val['count3'] : ",".$val['count3'];
			}
		} 
		//print_r($tongji1);
		//print_r($tongji2);

		$pagesize = ceil($day_jian / 10);
		$this->assign('pagesize',$pagesize);
		$this->assign('tongji1',json_encode($tongji1));
		$this->assign('tongji2',json_encode($tongji2));
		
		/*引入GoogChart类*/
		import("ORG.Util.GoogChart");
		$chart = new GoogChart();	

		//时间轴\新增账号数据\新增激活数据
		$this->assign('data0',$data0);	
		$this->assign('data1',$data1);	
		
		$this->assign('left_css',"14");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji7";
		$this->display($lib_display);
	}
	
	//用户获取
	public function tongji8(){
		$table = "fx_tongji7";
		$row = M($table);
		
		$table2 = "payment";
		$row2 = M($table2);
		$table3 = "zjh_goods";
		$row3 = M($table3);
		$table6 = "pay_now_config.zjh_order";
		$row6 = M($table6);
		
		$table5 = "user_info";
		$row5 = M($table5, '', DB_CONFIG2);
		
		$table6 = "game_channel";
		$row6 = M($table6, '', DB_CONFIG3);
		
		//$table7 = "login_log";
		//$row7 = M($table7, '', DB_CONFIG2);
		
		$beginTime = I("beginTime");
		$endTime = I("endTime");
		$channel = I("channel");
		
		//查询不能大于当天
		$timenow = strtotime(date("Y-m-d"));
		if (strtotime($beginTime)>$timenow){
			$this->error('查询不能大于当天');
			exit;
		}
		
		/*if (!empty($beginTime) ){
			$date11 = $beginTime;
			$day_jian = 1;
			
			//$datenow = date("d", strtotime($endTime));
			//$dateend = date("d", strtotime($beginTime));
		}else{
			$day_jian = 1;
			$date11 = date("Y-m-d");
		}*/
		if (!empty($beginTime) && !empty($endTime)){
			$date11 = date("Y-m-d", strtotime($beginTime));
			$date12 = date("Y-m-d", strtotime($endTime));
			$day_jian = (strtotime($date12) - strtotime($date11)) / (60 * 60 * 24) + 1;
			
			//$datenow = date("d", strtotime($endTime));
			//$dateend = date("d", strtotime($beginTime));
		}else{
			$date12 = date("Y-m-d",strtotime("-1 day"));
			$day_jian = 1;
			$date11 = $date12;
		}
		//echo $day_jian;
		//$date11 .= " 00:00:00"; 
		//$date12 .= " 23:59:59"; 
		
		$this->assign('date11',$date11);
		$this->assign('date12',$date12);
		$this->assign('channel',$channel);
		//计算时间区段
		$seldate = array();
		$seldate[0] = array('date1' => date("Y-m-d"),
							'date2' => date("Y-m-d"));
		$seldate[1] = array('date1' => date("Y-m-d",strtotime("-1 day")),
							'date2' => date("Y-m-d",strtotime("-1 day")));
		$seldate[2] = array('date1' => date("Y-m-d",strtotime("-6 day")),
							'date2' => date("Y-m-d"));
		$seldate[3] = array('date1' => date("Y-m-d",strtotime("-29 day")),
							'date2' => date("Y-m-d"));
		$this->assign('seldate',$seldate);
		
		$sql1 = "!((user_id>=".ROBERT1_BEGIN." and user_id<".ROBERT1_END.") or (user_id>=".ROBERT2_BEGIN." and user_id<=".ROBERT2_END."))";
		$sql0 = "";
		if (!empty($channel)){
			$sql3 = " and channel=$channel";
			$sql0 .= " and channel='$channel'";
			
			//渠道用户
			$res0 = $row5->field('user_id')->where("channel=$channel")->select();
			$sql4 = "";
			foreach ($res0 as $key => $val){
				$sql4 .= (empty($sql4)) ? $val['user_id'] : ",".$val['user_id'];
			}
			if (!empty($sql4)) $sql4 = " and user_id in ($sql4)";
			
			$page_num = PAGE_SHOW;
		}else{
			$sql0 .= " and channel='all'";
			$sql3 = "";
			$sql4 = "";
			
			$page_num = 4;
			$channel = "all";
		}
		
		import('ORG.Util.Page');
		$Page       = new Page($day_jian,$page_num);
		$show       = $Page->show();
		//echo $Page->firstRow."**".$Page->listRows."<br>";
		//房间号
		$room = array('1','2','3');
	
		if ($day_jian >= 0){
			$tongji_show = array();
			$tongji1 = array();
			$tongji2 = array();
			$tongji3 = array();
			$tongji4 = array();
			$data0 = '';
			$data1 = array();
			$data2 = array();
			$data3 = array();
			$data4 = array();
			$alltotal1 = 0;
			$alltotal2 = 0;
			$maxi = $day_jian - $Page->firstRow;
			$mini = $day_jian - $Page->firstRow - $Page->listRows;
			if ($mini < 1) $mini = 1;
			//echo $maxi."***".$mini."<br>";
			for ($i=$maxi; $i>=$mini; $i--){
				
				$j = $i - 1;	
				$time1 = strtotime($date11) + 60 * 60 * 24 * ($i - 1);
				$time2 = $time1 + 60 * 60 * 24;
				$date1 = date("Y-m-d", $time1);
				$date2 = date("Y-m-d", $time2);
				//echo $date1."***".$date2."<br>";
				$table1 = "log_game_".date("Ymd", $time1);
				$row1 = M($table1, '', DB_CONFIG3);
				
				//用户获取
				$flag = "1";
				$total = $row->where("data='$date1' and flag=$flag $sql0")->count();
				//if ($date1=="2015-11-24") dump($row->_sql());
				if ($total == 0){
					$tongji = array();

					//渠道
					
					$res0 = $row6->field('channel')->where("lastdate='$date1'")->group("channel")->select();
					//dump($row5->_sql());
					foreach ($res0 as $key0 => $val0){
						//新用户
						//$count0 = $row5->where("$sql1 and register_date>='$date1' and register_date<'$date2' and channel='".$val0['channel']."'")->count('user_id');
						$count0 = 0;
						$temp_user_id = "";
						$res1 = $row6->field('user_id')->where("lastdate='$date1' and channel='".$val0['channel']."' and user_id!=''")->select();
						foreach ($res1 as $key1 => $val1){
							$temp = explode(",", $val1['user_id']);
							$temp_user_id .= $val1['user_id'];
							$count0 += count($temp);
						}
						
						//if ($date1=="2015-11-24") dump($row5->_sql());
						$tongji[$key0]['count0'] = $count0;
						$tongji[$key0]['channel'] = $val0['channel'];
						for($k=0; $k<12; $k++){
							$tongji[$key0]['all'][$k]['count'] = 0;
						}
						
						//游戏人数
						//$sql11 = "SELECT sum(nums) AS total FROM (SELECT COUNT(user_id) AS nums FROM $table1 WHERE user_id in ($temp_user_id) GROUP BY user_id ) AS t1 WHERE nums>=3";
						$sql11 = "SELECT COUNT(user_id) AS nums FROM $table1 WHERE user_id in ($temp_user_id) GROUP BY user_id";
						//echo $sql11."<br>";
						$res11 = $row1->query($sql11);
						foreach($res11 as $key11 => $val11){
							//echo $val11['nums']."<br>";
							if ($val11['nums'] < 10){
								$tongji[$key0]['all'][$val11['nums']]['count']++;
							}else{
								$tongji[$key0]['all'][10]['count']++;
							}
							if ($val11['nums']>3){
								$tongji[$key0]['all'][11]['count']++;
							}
						}
												
						//有效率
						$tongji[$key0]['yxl'] = (!empty($count0)) ? round($tongji[$key0]['all'][11]['count']/$count0,2) : 0;
					}

					$tongji0 = array('data' => $date1,
									 'tongji' => $tongji);
									
					if ($date1<date("Y-m-d")){
						$data9 = array('data' => $date1,
									   'flag' => $flag,
									   'channel' => $channel,
									   'tongji' => json_encode($tongji0),
									   'addtime' => time());
						$result = $row->add($data9);	
						//if ($date1=="2015-11-24") dump($row->_sql());						
					}
					$tongji1[$j] = $tongji0;	
					//print_r($tongji0);
					//exit;
				}else{
					$info = $row->where("data='$date1' and flag=$flag $sql0")->find();
					$tongji1[$j] = json_decode($info['tongji'], true);
				}
				
			}
		} 
		//print_r($tongji2); 
		//print_r($tongji3);
		$this->assign('tongji1',$tongji1);


		$this->assign('pageshow',$show);
		$this->assign('left_css',"14");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji8";
		$this->display($lib_display);
	}
	
	public function xinghao(){
		
		$table = "fx_tongji5";
		$row = M($table);
		$table2 = "user_info";
		$row2 = M($table2, '', DB_CONFIG2);
		$table3 = "user_base";
		$row3 = M($table3, '', DB_CONFIG3);
		

		$date11 = I("date11");
		$model = I("model");
		if (empty($date11)) $date11 = date("Y-m-d",strtotime("-1 day"));
		
		
		//$date11 .= " 00:00:00"; 
		//$date12 .= " 23:59:59"; 
		
		$this->assign('date11',$date11);
		$this->assign('model',$model);

		//计算时间区段
		$seldate = array();
		$seldate[0] = array('date1' => date("Y-m-d"),
							'date2' => date("Y-m-d"));
		$seldate[1] = array('date1' => date("Y-m-d",strtotime("-1 day")),
							'date2' => date("Y-m-d",strtotime("-1 day")));
		$seldate[2] = array('date1' => date("Y-m-d",strtotime("-6 day")),
							'date2' => date("Y-m-d"));
		$seldate[3] = array('date1' => date("Y-m-d",strtotime("-29 day")),
							'date2' => date("Y-m-d"));
		$this->assign('seldate',$seldate);
		
		$sql1 = "";
		$sql0 = "";
		if (!empty($model)){
			//$sql1 .= " and model='$model'";
		}

		//echo $sql1;
		if ($date11 < date("Y-m-d")){
			
			$flag = "2";
			$total = $row->where("data='$date11' and channel='all' and version='all' and flag=$flag")->count();
			if ($total == 0){
				
				//获取当天活跃用户
				$info = $row3->where("key_name='user_id_log' and key_adddate='$date11'")->find();
				$user_id_log = $info['key_value'];
				if (!empty($user_id_log)){
					$tongji = $row2->field("model,count(model) as num")->where("user_id in ($user_id_log)")->group('model')->select();
					
					$tongji0 = array('data' => $date11,
									 'tongji' => $tongji);
					$data9 = array('data' => $date11,
								   'flag' => $flag,
								   'channel' => 'all',
								   'version' => 'all',
								   'tongji' => json_encode($tongji0),
								   'addtime' => time());
					$result = $row->add($data9);	
				}else{
					$tongji = array();
				}
				
				//dump($row2->_sql());
			}else{
				$info = $row->where("data='$date11' and channel='all' and version='all' and flag=$flag")->find();
				//dump($row->_sql()); 
				//echo $info['tongji'];
				$tongji0 = json_decode($info['tongji'], true);
			}

		}
		
		
		
		$list = array();
		import('ORG.Util.Page');
		if (!empty($model)){
			$pagesize = 0;
			foreach($tongji0['tongji'] as $key => $val){
				if ($val['model'] == $model){
					$list[0]['data'] = $tongji0['data'];
					$list[0]['model'] = $val['model'];
					$list[0]['num'] = $val['num'];
					$list[0]['key'] = 1;
					$pagesize = 1;
				}
			}
			$Page       = new Page($pagesize,20);	
			$show       = $Page->show();
			
		}else{
			//print_r($tongji0);
			$sort1 = array();
			foreach($tongji0['tongji'] as $key => $val){
				$sort1[$key] = $val['num'];
			}
			array_multisort($sort1, SORT_DESC, $tongji0['tongji']);
			
			$pagesize = count($tongji0['tongji']);
			$Page       = new Page($pagesize,20);	
			$show       = $Page->show();
			
			$j = 0;
			for($i = $Page->firstRow; $i< $Page->firstRow + $Page->listRows; $i++){
				$list[$j]['data'] = $tongji0['data'];
				$list[$j]['model'] = $tongji0['tongji'][$i]['model'];
				$list[$j]['num'] = $tongji0['tongji'][$i]['num'];
				$list[$j]['key'] = $i + 1;
				$j++;
			}
		}		
		
		/*引入GoogChart类*/
		import("ORG.Util.GoogChart");
		$chart = new GoogChart();	

		$this->assign('pageshow',$show);
		$this->assign('list',$list);	
		
		$this->assign('left_css',"14");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/xinghao";
		$this->display($lib_display);
	}
	
	
	public function area(){
		
		$table = "fx_tongji5";
		$row = M($table);
		$table2 = "user_info";
		$row2 = M($table2, '', DB_CONFIG2);
		$table3 = "user_base";
		$row3 = M($table3, '', DB_CONFIG3);
		

		$date11 = I("date11");
		$channel = I("channel");
		if (empty($date11)) $date11 = date("Y-m-d",strtotime("-1 day"));
		
		
		//$date11 .= " 00:00:00"; 
		//$date12 .= " 23:59:59"; 
		
		$this->assign('date11',$date11);
		$this->assign('channel',$channel);

		//计算时间区段
		$seldate = array();
		$seldate[0] = array('date1' => date("Y-m-d"),
							'date2' => date("Y-m-d"));
		$seldate[1] = array('date1' => date("Y-m-d",strtotime("-1 day")),
							'date2' => date("Y-m-d",strtotime("-1 day")));
		$seldate[2] = array('date1' => date("Y-m-d",strtotime("-6 day")),
							'date2' => date("Y-m-d"));
		$seldate[3] = array('date1' => date("Y-m-d",strtotime("-29 day")),
							'date2' => date("Y-m-d"));
		$this->assign('seldate',$seldate);
		
		$sql1 = "";
		$sql0 = "";
		if (!empty($model)){
			//$sql1 .= " and model='$model'";
		}

		//echo $sql1;
		if ($date11 < date("Y-m-d")){
			
			if (!empty($channel)){
				
				//获取当天活跃用户
				$info = $row3->where("key_name='user_id_log' and key_adddate='$date11'")->find();
				$user_id_log = $info['key_value'];
				if (!empty($user_id_log)){
					$tongji = $row2->field("province,count(province) as num")->where("user_id in ($user_id_log) and channel=".$channel)->group('province')->select();
						
					$tongji0 = array('data' => $date11,
									 'tongji' => $tongji);

				}else{
					$tongji0 = array();
				}
				
			}else{
				$flag = "3";
				$total = $row->where("data='$date11' and channel='all' and version='all' and flag=$flag")->count();
				if ($total == 0){
					
					//获取当天活跃用户
					$info = $row3->where("key_name='user_id_log' and key_adddate='$date11'")->find();
					$user_id_log = $info['key_value'];
					if (!empty($user_id_log)){
						$tongji = $row2->field("province,count(province) as num")->where("user_id in ($user_id_log)")->group('province')->select();
						
						$tongji0 = array('data' => $date11,
										 'tongji' => $tongji);
						$data9 = array('data' => $date11,
									   'flag' => $flag,
									   'channel' => 'all',
									   'version' => 'all',
									   'tongji' => json_encode($tongji0),
									   'addtime' => time());
						$result = $row->add($data9);	
					}else{
						$tongji0 = array();
					}
					
					//dump($row2->_sql());
				}else{
					$info = $row->where("data='$date11' and channel='all' and version='all' and flag=$flag")->find();
					//dump($row->_sql()); 
					//echo $info['tongji'];
					$tongji0 = json_decode($info['tongji'], true);
				}
			}

		}
		
		//print_r($tongji0);
		
		$list = array();
		import('ORG.Util.Page');
		if (!empty($model)){
			
		}else{
			//print_r($tongji0);
			$sort1 = array();
			foreach($tongji0['tongji'] as $key => $val){
				$sort1[$key] = $val['num'];
			}
			array_multisort($sort1, SORT_DESC, $tongji0['tongji']);
			
			$pagesize = count($tongji0['tongji']);
			$Page       = new Page($pagesize,20);	
			$show       = $Page->show();
			
			$j = 0;
			for($i = $Page->firstRow; $i< $Page->firstRow + $Page->listRows; $i++){
				if (!empty($tongji0['tongji'][$i]['province'])){
					$list[$j]['data'] = $tongji0['data'];
					$list[$j]['province'] = $tongji0['tongji'][$i]['province'];
					$list[$j]['num'] = $tongji0['tongji'][$i]['num'];
					$list[$j]['key'] = $i + 1;
					$j++;
				}
				
			}
		}		
		
		/*引入GoogChart类*/
		import("ORG.Util.GoogChart");
		$chart = new GoogChart();	

		$this->assign('pageshow',$show);
		$this->assign('list',$list);	
		
		$this->assign('left_css',"14");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/area";
		$this->display($lib_display);
	}
	
	//测试
	public function test(){
		$table5 = "user_info";
		$row5 = M($table5);
		$sql1 = "!((user_id>=".ROBERT1_BEGIN." and user_id<".ROBERT1_END.") or (user_id>=".ROBERT2_BEGIN." and user_id<=".ROBERT2_END."))";
		$res = $row5->field("lost_count,win_count,lost_count+win_count as sum")->where($sql1." AND win_count>150")->limit(0,200)->select();
		//dump($row5->_sql());
		$count = array();
		for($i=0; $i<8; $i++){
			$count[$i] = 0;
		}
		foreach ($res as $key => $val){
			$lv = (!empty($val['sum'])) ? round($val['win_count']/$val['sum'],3)*100 : 0;
			echo $lv."**";
			if ($key % 9==0) echo "<br>";
			if ($lv<20){
				$count[0]++;
			}else if ($lv<25){
				$count[1]++;
			}else if ($lv<30){
				$count[2]++;
			}else if ($lv<35){
				$count[3]++;
			}else if ($lv<40){
				$count[4]++;
			}else if ($lv<45){
				$count[5]++;
			}else if ($lv<50){
				$count[6]++;
			}else{
				$count[7]++;
			}
		}
		echo "<br>";
		print_r($count);
		exit;
	}	
	
	//判断目录是否为空
	public function is_empty_dir($fp)    
    {    
        $H = @opendir($fp); 
        $i=0;    
        while($_file=readdir($H)){    
            $i++;    
        }    
        closedir($H);    
        if($i>2){ 
            return 1; 
        }else{ 
            return 2;  //true
        } 
    } 
	
	//空方法
	 public function _empty() {  
        $this->display("Index/index");
    }
}