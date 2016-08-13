<?php
// 在线分析文件

class PaijuAction extends BaseAction {

	protected $By_tpl = 'Paiju'; 
	
	//在线用户
	public function tongji1(){
		$table = "fx_paiju_tongji";
		$row = M($table);
		
		$table2 = "payment";
		$row2 = M($table2);
		$table3 = "zjh_goods";
		$row3 = M($table3);
		$table6 = "zjh_order";
		$row6 = M($table6);
		
		$table5 = "user_info";
		$row5 = M($table5, '', DB_CONFIG2);
		
		
		$beginTime = I("beginTime");
		$endTime = I("endTime");
		$channel = I("channel");
		
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
			$day_jian = PAGE_SHOW;
			$date11 = date("Y-m-d", (strtotime($date12) - 60 * 60 * 24 * ($day_jian - 1)));
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
			$sql9 = " and channel=$channel";
			$sql0 .= " and channel='$channel'";
			
			//渠道用户
			//$res0 = $row5->field('user_id')->where("channel=$channel $sql1")->select();
			//dump($row5->_sql());
			//$sql4 = "";
			//foreach ($res0 as $key => $val){
				//$sql4 .= (empty($sql4)) ? $val['user_id'] : ",".$val['user_id'];
			//}
			//if (!empty($sql4)) $sql4 = " and user_id in ($sql4)";
		}else{
			$sql0 .= " and channel='all'";
			$sql9 = "";
			$sql4 = "";
		}
		
		import('ORG.Util.Page');
		$Page       = new Page($day_jian,PAGE_SHOW);
		$show       = $Page->show();
		//echo $Page->firstRow."**".$Page->listRows."<br>";
		//房间号
		$room = array('1','2','3');
		
		//总用户
		//$res0 = $row5->field('user_id')->where("user_id>0 $sql1 $sql9")->select();
		//$sql4 = "";
		//$sql42 = "";
		//foreach ($res0 as $key => $val){
		//	$sql4 .= (empty($sql4)) ? $val['user_id'] : ",".$val['user_id'];
		//}
		//if (!empty($sql4)) {$sql42 = " and user_id not in ($sql4)"; $sql4 = " and user_id in ($sql4)"; }
	
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
			for ($i=$maxi; $i>=$mini; $i--){
				
				$j = $i - 1;	
				$time1 = strtotime($date11) + 60 * 60 * 24 * ($i - 1);
				$time2 = $time1 + 60 * 60 * 24;
				$date1 = date("Y-m-d", $time1);
				$date2 = date("Y-m-d", $time2);
				//echo $date1."***".$date2."<br>";
				$table1 = "log_game_record_log_".date("Ym", $time1);
				$row1 = M($table1, '', DB_CONFIG2);
				$table4 = "log_online_data_".date("Ym", $time1);
				$row4 = M($table4, '', DB_CONFIG2);
				
				//新用户
				$res0 = $row5->field('user_id')->where("register_date>='$date1' and register_date<'$date2' $sql1 $sql9")->select();
				//if ($date1=="2015-12-06") dump($row5->_sql());
				$sql2 = "";
				$sql3 = "";
				foreach ($res0 as $key => $val){
					$sql2 .= (empty($sql2)) ? $val['user_id'] : ",".$val['user_id'];
					/*$count0 = $row6->where("order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') and user_id=".$val['user_id'])->count();
					//if ($val['user_id']=="10327871") dump($row6->_sql()); 
					if ($count0 > 0){
						$sql3 .= (empty($sql3)) ? $val['user_id'] : ",".$val['user_id'];
					}*/
				}
				if (!empty($sql2)) $sql2 = " and user_id in ($sql2)"; 
				if (!empty($sql3)) $sql3 = " and user_id in ($sql3)";
				
				//当日注册用户平均游戏局数
				$flag = "1";
				$total = $row->where("data='$date1' and flag=$flag $sql0")->count();
				if ($total == 0){
					$online = array();
					$sumall1 = 0;
					$sumall2 = 0;
					$sumall3 = 0;
					$sumall4 = 0;
					$sumall5 = 0;
					$sumall6 = 0;
					$sumall7 = 0;
					$sumall8 = 0;
					$sumall9 = 0;
					for ($k=0; $k<count($room); $k++){
						//总用户游戏局数
						$count1 = $row1->where("curtime>='$time1' and curtime<'$time2' and roomid='".$room[$k]."' $sql1 ")->count('id');
						//if ($date1=="2015-12-06") dump($row1->_sql());
						if (empty($count1)) $count1 = 0; 
						$sumall1 += $count1;
						//总用户游戏人数
						$count2 = $row1->where("curtime>='$time1' and curtime<'$time2' and roomid='".$room[$k]."' $sql1 ")->count('distinct user_id');
						//if ($date1=="2015-11-29") {dump($row1->_sql()); echo $count2."<br>"; }
						
						//dump($row1->_sql());
						if (empty($count2)) $count2 = 0; 
						//$sumall2 += $count2;
						//总用户平均游戏
						$count3 = (empty($count2)) ? 0 : round($count1 / $count2, 1);
						
						if (empty($sql2)){
							$count4 = 0; 
							$count5 = 0; 
							$count6 = 0; 
						}else{
							//新用户游戏局数
							$count4 = $row1->where("curtime>='$time1' and curtime<'$time2' and roomid='".$room[$k]."' $sql1 $sql2")->count('id');
							//if ($date1=="2015-10-27") dump($row1->_sql());
							if (empty($count4)) $count4 = 0; 
							$sumall4 += $count4;
							//新用户游戏人数
							$count5 = $row1->where("curtime>='$time1' and curtime<'$time2' and roomid='".$room[$k]."' $sql1 $sql2")->count('distinct user_id');
							//if ($date1=="2015-11-29") {dump($row1->_sql()); echo $count5."<br>";}
							//if ($date1=="2015-12-06") {dump($row1->_sql()); echo $count5."<br>"; }
							//dump($row1->_sql());
							if (empty($count5)) $count5 = 0; 
							//$sumall5 += $count5;
							//新用户平均游戏
							$count6 = (empty($count5)) ? 0 : round($count4 / $count5, 1);
						}
						
						
						//老用户游戏局数
						$count7 = $count1 - $count4;
						$sumall7 += $count7;
						//老用户游戏人数
						$count8 = $count2 - $count5;
						//$sumall8 += $count8;
						//老用户平均游戏
						$count9 = (empty($count8)) ? 0 : round($count7 / $count8, 1);

						$online[] = array('room_id' => $room[$k],
										  'count1' => $count1,
									      'count2' => $count2,
									      'count3' => $count3,
										  'count4' => $count4,
									      'count5' => $count5,
									      'count6' => $count6,
										  'count7' => $count7,
									      'count8' => $count8,
									      'count9' => $count9);
					}
					
					$sumall2 = $row1->where("curtime>='$time1' and curtime<'$time2' $sql1 $sql4")->count('distinct user_id');
					$sumall5 = $row1->where("curtime>='$time1' and curtime<'$time2' $sql1 $sql2")->count('distinct user_id');
					$sumall8 = $sumall2 - $sumall5;
					
					$sumall3 = (empty($sumall2)) ? 0 : round($sumall1 / $sumall2, 1);
					$sumall6 = (empty($sumall5)) ? 0 : round($sumall4 / $sumall5, 1);
					$sumall9 = (empty($sumall8)) ? 0 : round($sumall7 / $sumall8, 1);
					$tongji = array('data' => $date1,
									'sumall1' => $sumall1,
									'sumall2' => $sumall2,
									'sumall3' => $sumall3,
									'sumall4' => $sumall4,
									'sumall5' => $sumall5,
									'sumall6' => $sumall6,
									'sumall7' => $sumall7,
									'sumall8' => $sumall8,
									'sumall9' => $sumall9,
 									'online' => $online);
									
					if ($date1!=date("Y-m-d")){
						$data9 = array('data' => $date1,
									   'channel' => empty($channel) ? "all" : $channel,
									   'flag' => $flag,
									   'tongji' => json_encode($tongji),
									   'addtime' => time());
						$result = $row->add($data9);			   
					}
					$tongji1[$j] = $tongji;	
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
		$this->assign('left_css',"48");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji1";
		$this->display($lib_display);
	}
	
	public function tongji2(){
		$table = "fx_paiju_tongji";
		$row = M($table);
		
		$table2 = "payment";
		$row2 = M($table2);
		$table3 = "zjh_goods";
		$row3 = M($table3);
		$table6 = "zjh_order";
		$row6 = M($table6);
		
		$table5 = "user_info";
		$row5 = M($table5, '', DB_CONFIG2);
		$table7 = "login_log";
		$row7 = M($table7, '', DB_CONFIG2);
		
		$beginTime = I("beginTime");
		$endTime = I("endTime");
		$channel = I("channel");
		
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
			$day_jian = PAGE_SHOW;
			$date11 = date("Y-m-d", (strtotime($date12) - 60 * 60 * 24 * ($day_jian - 1)));
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
		}else{
			$sql0 .= " and channel='all'";
			$sql3 = "";
			$sql4 = "";
		}
		
		import('ORG.Util.Page');
		$Page       = new Page($day_jian,PAGE_SHOW);
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
			for ($i=$maxi; $i>=$mini; $i--){
				
				$j = $i - 1;	
				$time1 = strtotime($date11) + 60 * 60 * 24 * ($i - 1);
				$time2 = $time1 + 60 * 60 * 24;
				$date1 = date("Y-m-d", $time1);
				$date2 = date("Y-m-d", $time2);
				echo $date1."***".$date2."<br>";
				$table1 = "log_game_record_log_".date("Ym", $time1);
				$row1 = M($table1, '', DB_CONFIG2);
				$table4 = "log_online_data_".date("Ym", $time1);
				$row4 = M($table4, '', DB_CONFIG2);
				echo $table1."***".$table4."<br>"; exit;
				//当日注册用户平均游戏局数
				$flag = "2";
				$total = $row->where("data='$date1' and flag=$flag $sql0")->count();
				if ($total == 0){
					$tongji = array();
					for($k=0; $k<13; $k++){
						$tongji[0]['all'][$k]['count'] = 0;
						$tongji[0]['new'][$k]['count'] = 0;
						$tongji[0]['old'][$k]['count'] = 0;
					}
					
					//总用户游戏人数
					$res = $row5->field('user_id,register_date')->where("$sql1 $sql3")->select();
					//dump($row5->_sql());
					foreach ($res as $key => $val){
						$count1 = $row1->where("curtime>='$time1' and curtime<'$time2' and user_id='".$val['user_id']."'")->count('id');
						//dump($row1->_sql());
						//if ($count1 > 0 and $date1 == '2015-10-30'){echo $val['user_id'].",";}
						//新用户
						$count2 = $row5->where("register_date>='$date1' and register_date<'$date2' and user_id='".$val['user_id']."'")->count('user_id');
						//登陆用户
						$count3 = $row7->where("login_date>='$date1' and login_date<'$date2' and user_id='".$val['user_id']."'")->count('user_id');
						
						if ($count3 > 0){
							if ($count1==0){
								$tongji[0]['all'][0]['count']++;
								if ($count2>0) $tongji[0]['new'][0]['count']++;
							}
						}
							if ($count1==0){
								//$tongji[0]['all'][0]['count']++;
								//if ($count2>0) $tongji[0]['new'][0]['count']++;
							}else if ($count1<6){
								$tongji[0]['all'][1]['count']++;
								if ($count2>0) $tongji[0]['new'][1]['count']++;
							}else if ($count1<11){
								$tongji[0]['all'][2]['count']++;
								if ($count2>0) $tongji[0]['new'][2]['count']++;
							}else if ($count1<16){
								$tongji[0]['all'][3]['count']++;
								if ($count2>0) $tongji[0]['new'][3]['count']++;
							}else if ($count1<21){
								$tongji[0]['all'][4]['count']++;
								if ($count2>0) $tongji[0]['new'][4]['count']++;
							}else if ($count1<26){
								$tongji[0]['all'][5]['count']++;
								if ($count2>0) $tongji[0]['new'][5]['count']++;
							}else if ($count1<31){
								$tongji[0]['all'][6]['count']++;
								if ($count2>0) $tongji[0]['new'][6]['count']++;
							}else if ($count1<36){
								$tongji[0]['all'][7]['count']++;
								if ($count2>0) $tongji[0]['new'][7]['count']++;
							}else if ($count1<41){
								$tongji[0]['all'][8]['count']++;
								if ($count2>0) $tongji[0]['new'][8]['count']++;
							}else if ($count1<46){
								$tongji[0]['all'][9]['count']++;
								if ($count2>0) $tongji[0]['new'][9]['count']++;
							}else if ($count1<51){
								$tongji[0]['all'][10]['count']++;
								if ($count2>0) $tongji[0]['new'][10]['count']++;
							}else if ($count1<101){
								$tongji[0]['all'][11]['count']++;
								if ($count2>0) $tongji[0]['new'][11]['count']++;
							}else{
								$tongji[0]['all'][12]['count']++;
								if ($count2>0) $tongji[0]['new'][12]['count']++;
							}
						

					}
				
					//老用户游戏人数\总计
					$sum = array(0,0,0);
					for($k=0; $k<13; $k++){
						$tongji[0]['old'][$k]['count'] = $tongji[0]['all'][$k]['count'] - $tongji[0]['new'][$k]['count'];
						$sum[0] += $tongji[0]['all'][$k]['count'];
						$sum[1] += $tongji[0]['new'][$k]['count'];
						$sum[2] += $tongji[0]['old'][$k]['count'];
					}
					
					for($k=0; $k<13; $k++){
						$tongji[0]['all'][$k]['bl'] = (!empty($sum[0])) ? round($tongji[0]['all'][$k]['count'] / $sum[0], 3) * 100 : 0;
						$tongji[0]['new'][$k]['bl'] = (!empty($sum[0])) ? round($tongji[0]['new'][$k]['count'] / $sum[1], 3) * 100 : 0;
						$tongji[0]['old'][$k]['bl'] = (!empty($sum[0])) ? round($tongji[0]['old'][$k]['count'] / $sum[2], 3) * 100 : 0;
					}

					$tongji0 = array('data' => $date1,
									 'tongji' => $tongji);
									
					if ($date1!=date("Y-m-d")){
						$data9 = array('data' => $date1,
									   'channel' => empty($channel) ? "all" : $channel,
									   'flag' => $flag,
									   'tongji' => json_encode($tongji0),
									   'addtime' => time());
						$result = $row->add($data9);			   
					}
					$tongji1[$j] = $tongji0;	
					//print_r($tongji0);
					//exit;
				}else{
					$info = $row->where("data='$date1' and flag=$flag $sql0")->find();
					$tongji1[$j] = json_decode($info['tongji'], true);
				}
				//print_r($tongji1[$j]); exit;
			}
		} 
		//print_r($tongji1); exit;
		//print_r($tongji3);
		$this->assign('tongji1',$tongji1);


		$this->assign('pageshow',$show);
		$this->assign('left_css',"48");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji2";
		$this->display($lib_display);
	}
	
	//AI输赢
	public function tongji3(){
		$table = "fx_jinbi_tongji2";
		$row = M($table, '', DB_CONFIG1);
		
		$table2 = "normal_game_record";
		$row2 = M($table2, '', DB_CONFIG2);
		
		$beginTime = I("beginTime");
		$endTime = I("endTime");
		$channel = I("channel");
		
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
			$day_jian = PAGE_SHOW;
			$date11 = date("Y-m-d", (strtotime($date12) - 60 * 60 * 24 * ($day_jian - 1)));
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
		
		$sql1 = "";
		if (!empty($channel)){
			$sql1 .= " and channel=$channel";
		}
		
		//金币产出类型
		$module = array();
		$module[0] = array('module'=>1,'module_name'=>'注册账号');
		$module[1] = array('module'=>2,'module_name'=>'每日登录');
		$module[2] = array('module'=>4,'module_name'=>'破产保护');
		$module[3] = array('module'=>5,'module_name'=>'充值');
		$module[4] = array('module'=>6,'module_name'=>'在线宝箱');
		$module[5] = array('module'=>7,'module_name'=>'任务赠送');
		$module[6] = array('module'=>8,'module_name'=>'AI申请');
		$module[7] = array('module'=>9,'module_name'=>'后台操作');
		$module[8] = array('module'=>10,'module_name'=>'大喇叭');
		$module[9] = array('module'=>11,'module_name'=>'道具发送');
		$this->assign('module',json_encode($module));
		
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
			
			for ($i=1; $i<=$day_jian; $i++){
				$j = $i - 1;	
				$time1 = strtotime($date11) + 60 * 60 * 24 * ($i - 1);
				$time2 = $time1 + 60 * 60 * 24;
				$date1 = date("Y-m-d", $time1);
				$date2 = date("Y-m-d", $time2);
				
				$table1 = "log_gold_change_log_".date("Ym", $time1);
				$row1 = M($table1, '', DB_CONFIG2);
				
				//AI输赢
				$flag = 3;
				$total = $row->where("data='$date1' and flag=$flag")->count();
				//dump($row->_sql());
				if ($total == 0){
					//房间号
					$shui = array();
					$sumall = 0;
					$tongji3[$j]['data'] = $date1;
					for ($k=0; $k<count($room); $k++){
						//房间内机器人金币统计
						$count10 = $row1->where("curtime>='$time1' and curtime<'$time2' and module=12 and roomid='".$room[$k]."'")->sum('changegold');
						$count11 = $row1->where("curtime>='$time1' and curtime<'$time2' and module=12 and roomid='".$room[$k]."'")->sum('taxgold');
						$count1 = $count10 + $count11;
						if (empty($count1)) $count1 = 0;
						$sumall += $count1;
						if ($room[$k] == "1"){
							$tongji3[$j]['count1'] = number_format($count1);
						}else if ($room[$k] == "2"){
							$tongji3[$j]['count2'] = number_format($count1);
						}else if ($room[$k] == "3"){
							$tongji3[$j]['count3'] = number_format($count1);
						}
					}
					$tongji3[$j]['sumall'] = number_format($sumall);
					if ($date1!=date("Y-m-d")){
						$data9 = array('data' => $date1,
									   'flag' => $flag,
									   'tongji' => json_encode($tongji3[$j]),
									   'addtime' => time());
						$result = $row->add($data9);
						//dump($row->_sql());
					}
				}else{
					$info = $row->where("data='$date1' and flag=$flag")->find();
					$tongji3[$j] = json_decode($info['tongji'], true);
				}
				
				//其它金币回收
				$flag = 4;
				$total = $row->where("data='$date1' and flag=$flag")->count();
				if ($total == 0){
					
					$sumall = 0;
					//大喇叭回收
					$count1 = $row1->where("curtime>='$time1' and curtime<'$time2' and module=10")->sum('changegold');
					$count1 = abs($count1);
					if (empty($count1)) $count1 = 0; 
						
					//互动表情回收
					$count2 = $row1->where("curtime>='$time1' and curtime<'$time2' and module=11")->sum('changegold');
					$count2 = abs($count2);
					if (empty($count2)) $count2 = 0;
					
					//礼物变卖回收
					$count3 = $row1->where("curtime>='$time1' and curtime<'$time2' and module=14")->sum('taxgold');
					if (empty($count3)) $count3 = 0;
					
					$sumall = $count1 + $count2 + $count3;
					//echo $count1."**".$count2."**".$count3."**".$sumall."<br>";	
					$tongji = array('data' => $date1,
									'count1' => number_format($count1),
									'count2' => number_format($count2),
									'count3' => number_format($count3),
									'sumall' => number_format($sumall));
									
					if ($date1!=date("Y-m-d")){
						$data9 = array('data' => $date1,
									   'flag' => $flag,
									   'tongji' => json_encode($tongji),
									   'addtime' => time());
						$result = $row->add($data9);	   
					}
					$tongji4[$j] = $tongji;
				}else{
					$info = $row->where("data='$date1' and flag=$flag")->find();
					$tongji4[$j] = json_decode($info['tongji'], true);
				}
				
			}

			foreach ($tongji1 as $key => $val){
				$data1[0] .= ($key==0) ? "'".$val['data']."'" : ",'".$val['data']."'";
				$data1[1] .= ($key==0) ? $val['sumall_t'] : ",".$val['sumall_t'];
			}
		} 
		//print_r($tongji2);

		$pagesize = ceil($day_jian / PAGE_SHOW);
		$this->assign('pagesize',$pagesize);
		$this->assign('tongji1',json_encode($tongji1));
		$this->assign('tongji2',json_encode($tongji2));
		$this->assign('tongji3',json_encode($tongji3));
		$this->assign('tongji4',json_encode($tongji4));
		
		/*引入GoogChart类*/
		import("ORG.Util.GoogChart");
		$chart = new GoogChart();	

		//时间轴\新增账号数据\新增激活数据
		$this->assign('data0',$data0);	
		$this->assign('data1',$data1);	
		$this->assign('data2',$data2);
		$this->assign('data3',$data3);
		$this->assign('data4',$data4);
		
		$this->assign('left_css',"48");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji3";
		$this->display($lib_display);
	}
	
	//破产次数
	public function tongji4(){
		$table = "fx_paiju_tongji";
		$row = M($table);
		$table5 = "user_info";
		$row5 = M($table5, '', DB_CONFIG2);
		
		$beginTime = I("beginTime");
		$endTime = I("endTime");
		$channel = I("channel");
		
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
			$day_jian = PAGE_SHOW;
			$date11 = date("Y-m-d", (strtotime($date12) - 60 * 60 * 24 * ($day_jian - 1)));
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
			$sql0 .= " and channel=$channel";
			
		}else{
			$sql0 .= " and channel='all'";
			$sql3 = "";
			$sql4 = "";
		}
		
		import('ORG.Util.Page');
		$Page       = new Page($day_jian,20);
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
			for ($i=$maxi; $i>=$mini; $i--){
				
				$j = $i - 1;	
				$time1 = strtotime($date11) + 60 * 60 * 24 * ($i - 1);
				$time2 = $time1 + 60 * 60 * 24;
				$date1 = date("Y-m-d", $time1);
				$date2 = date("Y-m-d", $time2);
				//echo $date1."***".$date2."<br>";
				$table1 = "log_game_record_log_".date("Ym", $time1);
				$row1 = M($table1, '', DB_CONFIG2);
				$table2 = "log_gold_change_log_".date("Ym", $time1);
				$row2 = M($table2, '', DB_CONFIG2);
				
				//破产保护赠送问题
				$flag = 3;
				$total = $row->where("data='$date1' and flag=$flag")->count();
				if ($total == 0){
					//发放量
					$count11 = $row2->where("$sql1 and curtime>='$time1' and curtime<'$time2' and changegold>0 and module=4")->sum('changegold');
					if (empty($count11)) $count11 = 0;
					//总数(账号)
					$count12 = $row2->where("$sql1 and curtime>='$time1' and curtime<'$time2' and changegold>0 and module=4")->count('distinct user_id');
					//if ($date1=="2015-11-05") echo $count12."<br>";
					//dump($row2->_sql());
					//登陆游戏UID
					$count16 = $row1->where("$sql1 and curtime>='$time1' and curtime<'$time2'")->count('distinct user_id');
					if ($date1=="2015-10-30"){
						/*dump($row1->_sql());
						$res00 = $row1->field('distinct user_id')->where("$sql1 and curtime>='$time1' and curtime<'$time2'")->select();
						dump($row1->_sql());
						foreach($res00 as $key00 => $val00){
							echo $val00['user_id'].",";
						}
						echo "<br>".$key00."**".$count16;*/
					}  
					//dump($row1->_sql());
					//if ($date1=="2015-11-05") echo $count16."<br>";
					//破产率
					$count17 = (!empty($count16)) ? round($count12/$count16,3)*100 : 0;
					//dump($row1->_sql());
					//发放次数
					$count13 = 0;
					$count14 = 0;
					$count15 = 0;
					$res3 = $row2->field("user_id")->where("$sql1 and curtime>='$time1' and curtime<'$time2' and changegold>0 and module=4")->group('user_id')->select();
					foreach($res3 as $key3=>$val3){
						$count0 = $row2->where("curtime>='$time1' and curtime<'$time2' and changegold>0 and module=4 and user_id=".$val3['user_id'])->count();
						if ($count0==1) $count13++; elseif ($count0==2) $count14++; elseif ($count0==3) $count15++;
					}
					
					//新用户
					$res0 = $row5->field('user_id')->where("$sql1 and register_date>='$date1' and register_date<'$date2'")->select();
					$sql4 = "";
					foreach ($res0 as $key => $val){
						$sql4 .= (empty($sql4)) ? $val['user_id'] : ",".$val['user_id'];
					}
					if (!empty($sql4)) $sql4 = " and user_id in ($sql4)";
					//发放量
					$count21 = $row2->where("curtime>='$time1' and curtime<'$time2' and changegold>0 and module=4 $sql4")->sum('changegold');
					if (empty($count21)) $count21 = 0;
					//总数(账号)
					$count22 = $row2->where("curtime>='$time1' and curtime<'$time2' and changegold>0 and module=4 $sql4")->count('distinct user_id');
					//dump($row2->_sql());
					//登陆游戏UID
					$count26 = $row1->where("curtime>='$time1' and curtime<'$time2' $sql4")->count('distinct user_id');
					//破产率
					$count27 = (!empty($count26)) ? round($count22/$count26,3)*100 : 0;
					//dump($row1->_sql());
					//发放次数
					$count23 = 0;
					$count24 = 0;
					$count25 = 0;
					$res3 = $row2->field("user_id")->where("curtime>='$time1' and curtime<'$time2' and changegold>0 and module=4 $sql4")->group('user_id')->select();
					foreach($res3 as $key3=>$val3){
						$count0 = $row2->where("curtime>='$time1' and curtime<'$time2' and changegold>0 and module=4 and user_id=".$val3['user_id'])->count();
						if ($count0==1) $count23++; elseif ($count0==2) $count24++; elseif ($count0==3) $count25++;
					}
					
					
					$tongji = array('data' => $date1,
									'count11' => number_format($count11),
								    'count12' => $count12,
									'count13' => $count13,
									'count14' => $count14,
									'count15' => $count15,
									'count16' => $count16,
									'count17' => $count17.'%',
									'count21' => number_format($count21),
								    'count22' => $count22,
									'count23' => $count23,
									'count24' => $count24,
									'count25' => $count25,
									'count26' => $count26,
									'count27' => $count27.'%');
					
					if ($date1!=date("Y-m-d")){
						$data9 = array('data' => $date1,
									   'flag' => $flag,
									   'tongji' => json_encode($tongji),
									   'addtime' => time());
						$result = $row->add($data9);	   
					}
					$tongji3[$j] = $tongji;
				}else{
					$info = $row->where("data='$date1' and flag=$flag")->find();
					$tongji3[$j] = json_decode($info['tongji'], true);
				}
				
			}
		} 
		//print_r($tongji2); 
		//print_r($tongji3);
		$this->assign('tongji3',$tongji3);


		$this->assign('pageshow',$show);
		$this->assign('left_css',"48");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji4";
		$this->display($lib_display);
	}
	
	//破产分析
	public function tongji5(){
		$table = "fx_tongji1";
		$row = M($table);
		$table1 = "fx_user_base";
		$row1 = M($table1);
		$table5 = "user_info";
		$row5 = M($table5, '', DB_CONFIG2);
		
		$beginTime = I("beginTime");
		$endTime = I("endTime");
		$channel = I("channel");
		
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
			$day_jian = PAGE_SHOW;
			$date11 = date("Y-m-d", (strtotime($date12) - 60 * 60 * 24 * ($day_jian - 1)));
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
			$sql0 .= " and channel=$channel";
			
		}else{
			$sql0 .= " and channel='all'";
			$sql3 = "";
			$sql4 = "";
		}
		
		import('ORG.Util.Page');
		$Page       = new Page($day_jian,20);
		$show       = $Page->show();
		//echo $Page->firstRow."**".$Page->listRows."<br>";
	
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
			for ($i=$maxi; $i>$mini; $i--){
				
				$j = $i - 1;	
				$time1 = strtotime($date11) + 60 * 60 * 24 * ($i - 1);
				$time2 = $time1 + 60 * 60 * 24;
				$date1 = date("Y-m-d", $time1);
				$date2 = date("Y-m-d", $time2);
				//echo $date1."***".$date2."<br>";
				//$table1 = "log_game_record_log_".date("Ym", $time1);
				//$row1 = M($table1);
				//$table2 = "log_gold_change_log_".date("Ym", $time1);
				//$row2 = M($table2);
				
				//破产分析问题
				$flag = 1;
				$total = $row->where("data='$date1' and flag=$flag")->count();
				if ($total == 0){
					$tongji1[$j] = array('date' => $date1,
										'count11' => 0,
										'count12' => 0,
										'count13' => 0,
										'count14' => 0,
										'count15' => 0,
										'count16' => 0,
										'count17' => 0,
										'count18' => 0,
										'count21' => 0,
										'count22' => 0,
										'count23' => 0,
										'count24' => 0,
										'count25' => 0,
										'count26' => 0,
										'count27' => 0,
										'count28' => 0,
										'user_add' => 0,
										'dau' => 0);
				}else{
					$info = $row->where("data='$date1' and flag=$flag")->find();
					$tongji1[$j] = json_decode($info['tongji'], true);
					
					$info_more = $row1->where("data='$date1'")->find();
					//dump($row1->_sql());
					$tongji1[$j]['user_add'] = $info_more['user_add'];
					$tongji1[$j]['dau'] = $info_more['dau'];
					$tongji1[$j]['dau_all'] = $info_more['dau'] + $info_more['dau_old'];
				}
				
			}
		} 
		//print_r($tongji2); 
		//print_r($tongji3);
		$this->assign('tongji1',$tongji1);


		$this->assign('pageshow',$show);
		$this->assign('left_css',"48");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji5";
		$this->display($lib_display);
	}
	
	//破产分布
	public function tongji6(){
		$table = "fx_tongji1";
		$row = M($table);
		$table1 = "fx_user_base";
		$row1 = M($table1);
		$table5 = "user_info";
		$row5 = M($table5, '', DB_CONFIG2);
		
		$beginTime = I("beginTime");
		$endTime = I("endTime");
		$channel = I("channel");
		
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
			$day_jian = PAGE_SHOW;
			$date11 = date("Y-m-d", (strtotime($date12) - 60 * 60 * 24 * ($day_jian - 1)));
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
			$sql0 .= " and channel=$channel";
			
		}else{
			$sql0 .= " and channel='all'";
			$sql3 = "";
			$sql4 = "";
		}
		
		import('ORG.Util.Page');
		$Page       = new Page($day_jian,20);
		$show       = $Page->show();
		//echo $Page->firstRow."**".$Page->listRows."<br>";
	
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
			for ($i=$maxi; $i>$mini; $i--){
				
				$j = $i - 1;	
				$time1 = strtotime($date11) + 60 * 60 * 24 * ($i - 1);
				$time2 = $time1 + 60 * 60 * 24;
				$date1 = date("Y-m-d", $time1);
				$date2 = date("Y-m-d", $time2);
				//echo $date1."***".$date2."<br>";
				//$table1 = "log_game_record_log_".date("Ym", $time1);
				//$row1 = M($table1);
				//$table2 = "log_gold_change_log_".date("Ym", $time1);
				//$row2 = M($table2);
				
				//破产分析问题
				$flag = 2;
				$total = $row->where("data='$date1' and flag=$flag")->count();
				if ($total == 0){
					$tongji1[$j] = array('date' => $date1,
										'count11' => array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
										'count12' => array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
										'count13' => array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
										'count21' => array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
										'count22' => array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
										'count23' => array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
										'count31' => array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
										'count32' => array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
										'count33' => array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0));
				}else{
					$info = $row->where("data='$date1' and flag=$flag")->find();
					$tongji1[$j] = json_decode($info['tongji'], true);
					
					for($t=0; $t<20; $t++){
						$tongji1[$j]['count31'][$t] = $tongji1[$j]['count11'][$t] - $tongji1[$j]['count21'][$t];
						$tongji1[$j]['count32'][$t] = $tongji1[$j]['count12'][$t] - $tongji1[$j]['count22'][$t];
						$tongji1[$j]['count33'][$t] = $tongji1[$j]['count13'][$t] - $tongji1[$j]['count23'][$t];
					}

				}
				
			}
		} 
		//print_r($tongji1); 
		//print_r($tongji3);
		$this->assign('tongji1',$tongji1);


		$this->assign('pageshow',$show);
		$this->assign('left_css',"48");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji6";
		$this->display($lib_display);
	}
	
	//在线用户
	public function tongji7(){
		$table = "fx_online_tongji2";
		$row = M($table);
		
		$table2 = "payment";
		$row2 = M($table2);
		$table3 = "zjh_goods";
		$row3 = M($table3);
		$table6 = "zjh_order";
		$row6 = M($table6);
		
		$table5 = "user_info";
		$row5 = M($table5, '', DB_CONFIG2);
		
		
		$beginTime = I("beginTime");
		$endTime = I("endTime");
		$channel = I("channel");
		
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
			$day_jian = 20;
			$date11 = date("Y-m-d", (strtotime($date12) - 60 * 60 * 24 * 19));
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
			$sql1 .= " and package_id=$channel";
			$sql0 .= " and channel=$channel";
		}else{
			$sql0 .= " and channel='all'";
		}
		
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
			
			import('ORG.Util.Page');
			$Page       = new Page($day_jian,PAGE_SHOW);
			$show       = $Page->show();
			
			$maxi = $day_jian - $Page->firstRow;
			$mini = $day_jian - $Page->firstRow - $Page->listRows;
			if ($mini < 1) $mini = 1;
			$t = 0;
			for ($i=$maxi; $i>=$mini; $i--){
				
				$j = $i - 1;	
				
				$time1 = strtotime($date11) + 60 * 60 * 24 * ($i - 1);
				$time2 = $time1 + 60 * 60 * 24;
				$date1 = date("Y-m-d", $time1);
				$date2 = date("Y-m-d", $time2);
				
				$table1 = "log_game_record_log_".date("Ym", $time1);
				$row1 = M($table1, '', DB_CONFIG2);
				$table4 = "log_online_data_".date("Ym", $time1);
				$row4 = M($table4, '', DB_CONFIG2);
				
				//在线用户
				/*
				$total = $row->where("data='$date1' and flag=1 $sql0")->count();
				if ($total == 0){
					$online = array();
					for ($k=0; $k<count($room); $k++){
						//游戏局数
						$count1 = $row1->where("curtime>='$time1' and curtime<'$time2' and roomid='".$room[$k]."' $sql1")->count('id');
						//dump($row1->_sql());
						if (empty($count1)) $count1 = 0; 
						//游戏人数
						
						$count2 = $row1->where("curtime>='$time1' and curtime<'$time2' and roomid='".$room[$k]."' $sql1")->count('distinct user_id');
						//dump($row1->_sql());
						if (empty($count2)) $count2 = 0; 
						//平均游戏
						$count3 = (empty($count2)) ? 0 : round($count1 / $count2, 1);

						$online[] = array('room_id' => $room[$k],
										  'count1' => $count1,
									      'count2' => $count2,
									      'count3' => $count3);
					}
					
					$tongji = array('data' => $date1,
									'online' => $online);
									
					if ($date1!=date("Y-m-d")){
						$data9 = array('data' => $date1,
									   'channel' => empty($channel) ? "all" : $channel,
									   'flag' => 1,
									   'tongji' => json_encode($tongji),
									   'addtime' => time());
						$result = $row->add($data9);			   
					}
					$tongji1[$j] = $tongji;	
				}else{
					$info = $row->where("data='$date1' and flag=1 $sql0")->find();
					$tongji1[$j] = json_decode($info['tongji'], true);
				}*/
				
				/*
				//当日平均在线ACU,当日峰值在线PCU
				$total = $row->where("data='$date1' and flag=2 $sql0")->count();
				if ($total == 0){
					//当日平均在线
					$count70 = $row4->where("daytime=".$time1)->count('id');
					$count71 = $row4->where("daytime=".$time1)->sum('room1+room2+room3');
					$count7 = empty($count70) ? 0 : round($count71 / $count70, 1);
					//当日峰值在线
					$res8 = $row4->field('room1+room2+room3 as sum8')->where("daytime=".$time1)->order('sum8 desc')->find();
					//dump($row4->_sql());
					$count8 = empty($res8['sum8']) ? 0 : $res8['sum8'];
					
					$tongji = array('data' => $date1,
									'count7' => $count7,
									'count8' => $count8);
									
					if ($date1!=date("Y-m-d")){
						$data9 = array('data' => $date1,
									   'channel' => empty($channel) ? "all" : $channel,
									   'flag' => 2,
									   'tongji' => json_encode($tongji),
									   'addtime' => time());
						$result = $row->add($data9);			   
					}
					$tongji2[$j] = $tongji;	
				}else{
					$info = $row->where("data='$date1' and flag=2 $sql0")->find();
					$tongji2[$j] = json_decode($info['tongji'], true);
				}
				*/
				
				//新用户
				$res0 = $row5->field('user_id')->where("register_date>='$date1' and register_date<'$date2' $sql1")->select();
				$sql2 = "";
				$sql3 = "";
				$sql4 = "";
				foreach ($res0 as $key => $val){
					$sql2 .= (empty($sql2)) ? $val['user_id'] : ",".$val['user_id'];
					$count0 = $row6->where("order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') and user_id=".$val['user_id'])->count();
					//if ($val['user_id']=="10327871") dump($row6->_sql()); 
					if ($count0 > 0){
						$sql3 .= (empty($sql3)) ? $val['user_id'] : ",".$val['user_id'];
					}
				}
				if (!empty($sql2)) $sql2 = " and user_id in ($sql2)";
				if (!empty($sql3)) $sql3 = " and user_id in ($sql3)";
				
				//当日注册用户平均游戏局数
				$total = $row->where("data='$date1' and flag=3 $sql0")->count();
				if ($total == 0){
					$online = array();
					for ($k=0; $k<count($room); $k++){
						//游戏局数
						$count1 = $row1->where("curtime>='$time1' and curtime<'$time2' and roomid='".$room[$k]."' $sql1 $sql2")->count('id');
						//if ($date1=="2015-10-27") dump($row1->_sql());
						if (empty($count1)) $count1 = 0; 
						//游戏人数
						$count2 = $row1->where("curtime>='$time1' and curtime<'$time2' and roomid='".$room[$k]."' $sql1 $sql2")->count('distinct user_id');
						//if ($date1=="2015-10-27") dump($row1->_sql());
						if (empty($count2)) $count2 = 0; 
						//平均游戏
						$count3 = (empty($count2)) ? 0 : round($count1 / $count2, 1);
						
						//付费玩家游戏局数
						$count4 = $row1->where("curtime>='$time1' and curtime<'$time2' and roomid='".$room[$k]."' $sql1 $sql3")->count('id');
						//if ($date1=="2015-10-27") dump($row1->_sql());
						if (empty($count4)) $count4 = 0; 
						//付费玩家游戏人数
						$count5 = $row1->where("curtime>='$time1' and curtime<'$time2' and roomid='".$room[$k]."' $sql1 $sql3")->count('distinct user_id');
						//if ($date1=="2015-10-27") dump($row1->_sql());
						if (empty($count5)) $count5 = 0; 
						//付费玩家平均游戏
						$count6 = (empty($count5)) ? 0 : round($count4 / $count5, 1);
						
						//未付费玩家游戏局数
						$count7 = $count1 - $count4;
						//未付费玩家游戏人数
						$count8 = $count2 - $count5;
						//未付费玩家平均游戏
						$count9 = (empty($count8)) ? 0 : round($count7 / $count8, 1);

						$online[] = array('room_id' => $room[$k],
										  'count1' => $count1,
									      'count2' => $count2,
									      'count3' => $count3,
										  'count4' => $count4,
									      'count5' => $count5,
									      'count6' => $count6,
										  'count7' => $count7,
									      'count8' => $count8,
									      'count9' => $count9);
					}
					
					$tongji = array('data' => $date1,
									'online' => $online);
									
					if ($date1!=date("Y-m-d")){
						$data9 = array('data' => $date1,
									   'channel' => empty($channel) ? "all" : $channel,
									   'flag' => 3,
									   'tongji' => json_encode($tongji),
									   'addtime' => time());
						$result = $row->add($data9);			   
					}
					$tongji3[$t] = $tongji;	
				}else{
					$info = $row->where("data='$date1' and flag=3 $sql0")->find();
					$tongji3[$t] = json_decode($info['tongji'], true);
				}
				
				$t++;
			}
			
			
		} 
		//print_r($tongji2); 
		//print_r($tongji3);
		$this->assign('pageshow',$show);
		$this->assign('list',$tongji3);
		//$this->assign('tongji3',json_encode($tongji3));
		$this->assign('left_css',"48");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji7";
		$this->display($lib_display);
	}
	
	public function test(){
		
		$this->assign('left_css',"38");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/test";
		$this->display($lib_display);
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