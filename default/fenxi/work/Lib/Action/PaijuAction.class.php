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
		$table6 = "pay_now_config.zjh_order";
		$row6 = M($table6);
		
		$table5 = "user_info";
		$row5 = M($table5, '', DB_CONFIG2);
		
		$user_base_model = M("user_base", '', DB_CONFIG3);
		
		
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
		$room = array('1','2','3','8');
		
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
				$table1 = "log_game_".date("Ymd", $time1);
				//$table1 = "log_game_record_log_".date("Ym", $time1);
				$row1 = M($table1, '', DB_CONFIG3);
				//$table4 = "log_online_data_".date("Ym", $time1);
				//$row4 = M($table4, '', DB_CONFIG2);
				
				//新用户
				$sql2 = "";
				$sql3 = "";
				$data_info = $user_base_model->where("key_adddate='$date1' and key_name='user_id_reg'")->find();
				if (!empty($data_info['key_value'])){
					$sql2 = " and user_id in (".$data_info['key_value'].")"; 
					$sql3 = " and user_id in (".$data_info['key_value'].")"; 
				}
				
				//echo $sql2; exit;
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
						$count1 = $row1->where("roomid='".$room[$k]."' $sql1 ")->count('id');
						//dump($row1->_sql()); exit;
						if (empty($count1)) $count1 = 0; 
						$sumall1 += $count1;
						//总用户游戏人数
						$count2 = $row1->where("roomid='".$room[$k]."' $sql1 ")->count('distinct user_id');
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
							$count4 = $row1->where("roomid='".$room[$k]."' $sql1 $sql2")->count('id');
							//if ($date1=="2015-10-27") dump($row1->_sql());
							if (empty($count4)) $count4 = 0; 
							$sumall4 += $count4;
							//新用户游戏人数
							$count5 = $row1->where("roomid='".$room[$k]."' $sql1 $sql2")->count('distinct user_id');
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
					
					$sumall2 = $row1->where("1 $sql1 $sql4")->count('distinct user_id');
					$sumall5 = $row1->where("1 $sql1 $sql2")->count('distinct user_id');
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
									
					if ($date1<date("Y-m-d")){
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
		//print_r($tongji1);  exit;
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
		$table1 = "fx_game_every";		
		$row1 = M($table1);		
		$table2 = "fx_game_active";		
		$row2 = M($table2);		
		$table3 = "fx_game_new";		
		$row3 = M($table3);		
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
			$day_jian = 1;
			$date11 = date("Y-m-d",strtotime("-1 day"));
		}
		//echo $day_jian;
		//$date11 .= " 00:00:00"; 
		//$date12 .= " 23:59:59"; 
		
		$this->assign('date11',$date11);
		$this->assign('date12',$date12);
		$this->assign('channel',$channel);		$this->assign('version',$version);
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
	
		import('ORG.Util.Page');
		
		//echo $Page->firstRow."**".$Page->listRows."<br>";
		$yesday = date("Y-m-d",strtotime("-1 day"));
		$info = $row1->where("data='".$yesday."'")->order('gameid desc')->find();
		$maxid = $info['gameid'];
		$tablename = str_replace('-', '', $yesday);
		$table4 = "log_game_".$tablename;
		$row4 = M($table4, '', DB_CONFIG3);
		$info = $row4->order('id desc')->find();
		$maxgameid = $info['id'];
		if ($maxid < $maxgameid){
			$showtxt = "昨日数据已分析到:$maxid; 最大值为:$maxgameid, 还未分析完成";
		}else{
			$showtxt = "昨日数据已分析到:$maxid; 最大值为:$maxgameid, 已经分析完成";
		}
		$this->assign('showtxt',$showtxt);
	
		if ($day_jian >= 0){

			//$maxi = $day_jian - $Page->firstRow;
			//$mini = $day_jian - $Page->firstRow - $Page->listRows;
			if ($mini < 1) $mini = 1;
			$j = 0;
			$room_id = array();
			$k = 0;
			for ($i=1; $i<=$day_jian; $i++){

				$time1 = strtotime($date12) - 60 * 60 * 24 * ($i - 1);
				$time2 = $time1 + 60 * 60 * 24;
				$date1 = date("Y-m-d", $time1);
				$date2 = date("Y-m-d", $time2);
				//echo $date1."**".$date2."<br>";
				//当日注册用户平均游戏局数
				$flag = "9";
				$total = $row->where("data='$date1' and flag=$flag $sql0")->count();
				if ($total == 0){
					
					
					//所有用户
					$res0 = $row2->field("channel,version,room_id")->where("data='$date1'")->group("channel,version,room_id")->select();
					//dump($row2->_sql()); exit;
					foreach ($res0 as $key => $val){
	
						if (!in_array($val['room_id'], $room_id)) {$room_id[$k] = $val['room_id']; $k++;}
						$res1 = $row2->where("data='$date1' and channel=".$val['channel']." and version='".$val['version']."' and room_id=".$val['room_id'])->order("id")->select();
						//dump($row2->_sql());
						$gamenum = 0;
						$gameren = 0;
						$gameuser = array();
						foreach ($res1 as $key1 => $val1){
							//echo $val1['tongji']."<br>";
							$showdata =  json_decode($val1['tongji'], true);
							foreach($showdata['info']['user_id'] as $key2 => $val2){
								$gameuser[$key2] += $val2;
							}
							$gamenum += $showdata['gamenum'];
							//$gameren += $showdata['gameren'];
							//echo $showdata['gamenum']."**".$showdata['gameren']."<br>";
						}
						//print_r($gameuser);
						$totalnum = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
						foreach($gameuser as $num){
							if ($num < 6){
								$totalnum[0]++;
							}else if ($num < 11){
								$totalnum[1]++;
							}else if ($num < 16){
								$totalnum[2]++;
							}else if ($num < 21){
								$totalnum[3]++;
							}else if ($num < 31){
								$totalnum[4]++;
							}else if ($num < 41){
								$totalnum[5]++;
							}else if ($num < 61){
								$totalnum[6]++;
							}else if ($num < 81){
								$totalnum[7]++;
							}else if ($num < 101){
								$totalnum[8]++;
							}else{
								$totalnum[9]++;
							}
							$gameren++;
						}
						
						$totalnum[10] = $gameren;
						$totalnum[11] = (!empty($gameren)) ? round($gamenum / $gameren, 2) : 0;
						$totalnum[12] = $gamenum;
						$tongji0 = array('data' => $date1,
										 'channel' => $val['channel'],
										 'version' => $val['version'],
										 'room_id' => $val['room_id'],
										 'tongji' => $totalnum);
						
						if ($maxid >= $maxgameid){
							$data9 = array('data' => $date1,
										   'channel' => $val['channel'],
										   'version' => $val['version'],
										   'room_id' => $val['room_id'],
										   'flag' => $flag,
										   'tongji' => json_encode($totalnum),
										   'addtime' => time());
							$result = $row->add($data9);			   
						}	
						
						$tongji1[$j] = $tongji0;	
						$j++;						
					}
					//print_r($tongji0);
					//exit;
				}else{
					$info = $row->where("data='$date1' and flag=$flag $sql0")->select();
					foreach($info as $key => $val){
						
						if (!in_array($val['room_id'], $room_id)) {$room_id[$k] = $val['room_id']; $k++;}
						$totalnum = json_decode($val['tongji'], true);
						//echo $val['tongji'];
						$tongji1[$j] = array('data' => $val['data'],
											 'channel' => $val['channel'],
											 'version' => $val['version'],
											 'room_id' => $val['room_id'],
											 'tongji' => $totalnum);
						$j++;					 
					}
					
				}
				//print_r($tongji1[$j]); exit;
			}
		}
		//print_r($tongji1);
		$showdata = array();

		if (empty($channel) and empty($version)){
			//默认按房间号统计
			foreach($room_id as $key => $val){
				$showdata[$key] = array('data' => $date1,
										'channel' => '',
										'version' => '',
										'room_id' => $val,
										'tongji' => array(0,0,0,0,0,0,0,0,0,0,0,0,0));
			}
			
			foreach ($tongji1 as $key => $val){
				//if ($showdata)
				foreach ($showdata as $key1 => $val1){
					if ($val['room_id'] == $val1['room_id']){
						for ($t=0; $t<=12; $t++){
							$showdata[$key1]['tongji'][$t] += $val['tongji'][$t];
						}

					}
				}	
			}
			foreach ($showdata as $key1 => $val1){
				$showdata[$key1]['tongji'][11] = ($showdata[$key1]['tongji'][10]==0) ? 0 : round($showdata[$key1]['tongji'][12] / $showdata[$key1]['tongji'][10], 2);
			}
		}else{
			if (!empty($channel) and empty($version)){
				$k = 0;
				$tempdata = array();
				foreach ($tongji1 as $key => $val){
					if ($val['channel'] == $channel){
						$tempdata[$k] = $tongji1[$key];
						$k++;
					}
				}
			}else if (empty($channel) and !empty($version)){
				$k = 0;
				$tempdata = array();
				foreach ($tongji1 as $key => $val){
					if ($val['version'] == $version){
						$tempdata[$k] = $tongji1[$key];
						$k++;
					}
				}
			}else if (!empty($channel) and !empty($version)){
				$k = 0;
				$tempdata = array();
				foreach ($tongji1 as $key => $val){
					if ($val['version'] == $version && $val['channel'] == $channel){
						$tempdata[$k] = $tongji1[$key];
						$k++;
					}
				}
			}
			//echo $channel."***********".$version."<br>";
			//print_r($tempdata);
			$Page       = new Page($k, 20);
			$show       = $Page->show();
			$nextnum = $Page->firstRow + $Page->listRows;
			for($i=$Page->firstRow; $i<$nextnum; $i++){
				if (!empty($tempdata[$i]))	$showdata[$i] = $tempdata[$i];
			}
		}
		//print_r($tongji1); exit;
		//print_r($tongji3);
		
		$this->assign('tongji1',$showdata);				
		$this->assign('pageshow',$show);
		$this->assign('left_css',"48");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji2";
		$this->display($lib_display);
	}
	
	public function tongji8(){
		
		$table = "fx_paiju_tongji";
		$row = M($table);
		$table1 = "fx_game_every";
		$row1 = M($table1);
		$table2 = "fx_game_active";
		$row2 = M($table2);
		$table3 = "fx_game_new";
		$row3 = M($table3);
		
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
			$day_jian = 1;
			$date11 = date("Y-m-d",strtotime("-1 day"));
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
	
		import('ORG.Util.Page');
		
		//echo $Page->firstRow."**".$Page->listRows."<br>";
		$yesday = date("Y-m-d",strtotime("-1 day"));
		$info = $row1->where("data='".$yesday."'")->order('gameid desc')->find();
		$maxid = $info['gameid'];
		$tablename = str_replace('-', '', $yesday);
		$table4 = "log_game_".$tablename;
		$row4 = M($table4, '', DB_CONFIG3);
		$info = $row4->order('id desc')->find();
		$maxgameid = $info['id'];
		if ($maxid < $maxgameid){
			$showtxt = "昨日数据已分析到:$maxid; 最大值为:$maxgameid, 还未分析完成";
		}else{
			$showtxt = "昨日数据已分析到:$maxid; 最大值为:$maxgameid, 已经分析完成";
		}
		$this->assign('showtxt',$showtxt);
	
		if ($day_jian >= 0){

			//$maxi = $day_jian - $Page->firstRow;
			//$mini = $day_jian - $Page->firstRow - $Page->listRows;
			if ($mini < 1) $mini = 1;
			$j = 0;
			$room_id = array();
			$k = 0;
			for ($i=1; $i<=$day_jian; $i++){

				$time1 = strtotime($date12) - 60 * 60 * 24 * ($i - 1);
				$time2 = $time1 + 60 * 60 * 24;
				$date1 = date("Y-m-d", $time1);
				$date2 = date("Y-m-d", $time2);
				//echo $date1."**".$date2."<br>";
				//当日注册用户平均游戏局数
				$flag = "8";
				$total = $row->where("data='$date1' and flag=$flag $sql0")->count();
				if ($total == 0){
					
					
					//所有用户
					$res0 = $row3->field("channel,version")->where("data='$date1'")->group("channel,version")->select();
					//dump($row3->_sql()); exit;
					foreach ($res0 as $key => $val){
	
						$res1 = $row3->where("data='$date1' and channel=".$val['channel']." and version='".$val['version']."'")->order("id")->select();
						//dump($row2->_sql());
						$gamenum = 0;
						$gameren = 0;
						$gameuser = array();
						foreach ($res1 as $key1 => $val1){
							//echo $val1['tongji']."<br>";
							$showdata =  json_decode($val1['tongji'], true);
							foreach($showdata['info'] as $key2 => $val2){
								$gameuser[$key2] += $val2;
							}
							$gamenum += $showdata['gamenum'];
							//$gameren += $showdata['gameren'];
							//echo $showdata['gamenum']."**".$showdata['gameren']."<br>";
						}
						//print_r($gameuser);
						$totalnum = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
						foreach($gameuser as $num){
							if ($num >= 100){
								for($tt=0; $tt<=10; $tt++){
									$totalnum[$tt]++; 
								}
							}else if ($num >= 70){
								for($tt=0; $tt<=9; $tt++){
									$totalnum[$tt]++; 
								}
							}else if ($num >= 50){
								for($tt=0; $tt<=8; $tt++){
									$totalnum[$tt]++; 
								}
							}else if ($num >= 30){
								for($tt=0; $tt<=7; $tt++){
									$totalnum[$tt]++; 
								}
							}else if ($num >= 20){
								for($tt=0; $tt<=6; $tt++){
									$totalnum[$tt]++; 
								}
							}else if ($num >= 10){
								for($tt=0; $tt<=5; $tt++){
									$totalnum[$tt]++; 
								}
							}else if ($num >= 5){
								for($tt=0; $tt<=4; $tt++){
									$totalnum[$tt]++; 
								}
							}else if ($num >= 4){
								for($tt=0; $tt<=3; $tt++){
									$totalnum[$tt]++; 
								}
							}else if ($num >= 3){
								for($tt=0; $tt<=2; $tt++){
									$totalnum[$tt]++; 
								}
							}else if ($num >= 2){
								for($tt=0; $tt<=1; $tt++){
									$totalnum[$tt]++; 
								}
							}else if ($num >= 1){
								$totalnum[0]++;
							}
							$gameren++;
						}
						
						$totalnum[12] = $gameren;
						$totalnum[11] = (!empty($gameren)) ? round($gamenum / $gameren, 2) : 0;
						$totalnum[13] = $gamenum;
						$tongji0 = array('data' => $date1,
										 'channel' => $val['channel'],
										 'version' => $val['version'],
										 'tongji' => $totalnum);
						
						if ($maxid >= $maxgameid){
							$data9 = array('data' => $date1,
										   'channel' => $val['channel'],
										   'version' => $val['version'],
										   'flag' => $flag,
										   'tongji' => json_encode($totalnum),
										   'addtime' => time());
							$result = $row->add($data9);			   
						}	
						
						$tongji1[$j] = $tongji0;	
						$j++;						
					}
					//print_r($tongji0);
					//exit;
				}else{
					$info = $row->where("data='$date1' and flag=$flag $sql0")->select();
					foreach($info as $key => $val){
						
						$totalnum = json_decode($val['tongji'], true);
						//echo $val['tongji'];
						$tongji1[$j] = array('data' => $val['data'],
											 'channel' => $val['channel'],
											 'version' => $val['version'],
											 'tongji' => $totalnum);
						$j++;					 
					}
					
				}
				//print_r($tongji1[$j]); exit;
			}
		}
		//print_r($room_id);
		$showdata = array();

		if (empty($channel) and empty($version)){
			$showdata[$key] = array('data' => $date1,
									'channel' => '',
									'version' => '',
									'tongji' => array(0,0,0,0,0,0,0,0,0,0,0,0,0,0));
			
			foreach ($tongji1 as $key => $val){
				//if ($showdata)
				foreach ($showdata as $key1 => $val1){
					for ($t=0; $t<=13; $t++){
						$showdata[$key1]['tongji'][$t] += $val['tongji'][$t];
					}
				}	
			}
			foreach ($showdata as $key1 => $val1){
				$showdata[$key1]['tongji'][11] = ($showdata[$key1]['tongji'][12]==0) ? 0 : round($showdata[$key1]['tongji'][13] / $showdata[$key1]['tongji'][12], 2);
			}
		}else{
			if (!empty($channel) and empty($version)){
				$k = 0;
				$tempdata = array();
				foreach ($tongji1 as $key => $val){
					if ($val['channel'] == $channel){
						$tempdata[$k] = $tongji1[$key];
						$k++;
					}
				}
			}else if (empty($channel) and !empty($version)){
				$k = 0;
				$tempdata = array();
				foreach ($tongji1 as $key => $val){
					if ($val['version'] == $version){
						$tempdata[$k] = $tongji1[$key];
						$k++;
					}
				}
			}else if (!empty($channel) and !empty($version)){
				$k = 0;
				$tempdata = array();
				foreach ($tongji1 as $key => $val){
					if ($val['version'] == $version && $val['channel'] == $channel){
						$tempdata[$k] = $tongji1[$key];
						$k++;
					}
				}
			}
			//echo $channel."***********".$version."<br>";
			//print_r($tempdata);
			$Page       = new Page($k, 20);
			$show       = $Page->show();
			$nextnum = $Page->firstRow + $Page->listRows;
			for($i=$Page->firstRow; $i<$nextnum; $i++){
				if (!empty($tempdata[$i]))	$showdata[$i] = $tempdata[$i];
			}
		}
		//print_r($tongji1); exit;
		//print_r($tongji3);
		
		$this->assign('tongji1',$showdata);
		
		

		$this->assign('pageshow',$show);
		$this->assign('left_css',"48");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji8";
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
					if ($date1<date("Y-m-d")){
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
									
					if ($date1<date("Y-m-d")){
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
		
		$table = "fx_tongji1";
		$row = M($table);
		
		
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
			$j = 0;
			for ($i=$maxi; $i>$mini; $i--){
				
				$j = $i - 1;	
				$time1 = strtotime($date11) + 60 * 60 * 24 * ($i - 1);
				$time2 = $time1 + 60 * 60 * 24;
				$date1 = date("Y-m-d", $time1);
				$date2 = date("Y-m-d", $time2);
				//echo $date1."******".$date2."<br>";
				$tongji1[$j] = $row->where("data='$date1' and flag=10")->find();
				$tongji1[$j]['show'] = json_decode($tongji1[$j]['tongji'], true); 
				
				$tongji2[$j] = $row->where("data='$date1' and flag=11")->find();
				$tongji2[$j]['show'] = json_decode($tongji2[$j]['tongji'], true); 
			}
		} 
		//print_r($tongji1); 
		//print_r($tongji2);
		$this->assign('tongji1',$tongji1);
		$this->assign('tongji2',$tongji2);

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
		$table6 = "pay_now_config.zjh_order";
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
			$date12 = date("Y-m-d",strtotime("-1 day"));
			$day_jian = 20;
			$date11 = date("Y-m-d",strtotime("-20 day"));
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
			$Page       = new Page($day_jian,20);
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
				
				$table1 = "log_game_".date("Ymd", $time1);
				$row1 = M($table1, '', DB_CONFIG1);
				//$table4 = "log_online_data_".date("Ym", $time1);
				//$row4 = M($table4, '', DB_CONFIG2);
				
				//新用户
				$res0 = $row5->field('user_id')->where("register_date>='$date1' and register_date<'$date2' $sql1")->select();
				$sql2 = "";
				$sql3 = "";
				$sql4 = "";
				foreach ($res0 as $key => $val){
					$sql2 .= (empty($sql2)) ? $val['user_id'] : ",".$val['user_id'];
					/*
					$count0 = $row6->where("order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') and user_id=".$val['user_id'])->count();
					//if ($val['user_id']=="10327871") dump($row6->_sql()); 
					if ($count0 > 0){
						$sql3 .= (empty($sql3)) ? $val['user_id'] : ",".$val['user_id'];
					}*/
				}
				if (!empty($sql2)) $sql2 = " and user_id in ($sql2)";
				
				if (empty($sql2)){
					$sql3 = " and user_id=-1";
				}else{
					$res0 = $row6->where("order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2')".$sql2)->select();
					foreach ($res0 as $key => $val){
						$sql3 .= (empty($sql3)) ? $val['user_id'] : ",".$val['user_id'];
					}
					if (!empty($sql3)) $sql3 = " and user_id in ($sql3)";
				}
				
				
				//当日注册用户平均游戏局数
				$total = $row->where("data='$date1' and flag=3 $sql0")->count();
				//echo $total."**".$date1."<br>"; 
				if ($total == 0){
					$online = array();
					for ($k=0; $k<count($room); $k++){
						//游戏局数
						$count1 = $row1->where("roomid='".$room[$k]."' $sql1 $sql2")->count('id');
						//if ($date1=="2015-10-27") dump($row1->_sql());
						if (empty($count1)) $count1 = 0; 
						//游戏人数
						$count2 = $row1->where("roomid='".$room[$k]."' $sql1 $sql2")->count('distinct user_id');
						//if ($date1=="2015-10-27") dump($row1->_sql());
						if (empty($count2)) $count2 = 0; 
						//平均游戏
						$count3 = (empty($count2)) ? 0 : round($count1 / $count2, 1);
						
						//付费玩家游戏局数
						$count4 = $row1->where("roomid='".$room[$k]."' $sql1 $sql3")->count('id');
						//if ($date1=="2015-10-27") dump($row1->_sql());
						if (empty($count4)) $count4 = 0; 
						//付费玩家游戏人数
						$count5 = $row1->where("roomid='".$room[$k]."' $sql1 $sql3")->count('distinct user_id');
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
									
					if ($date1<date("Y-m-d")){
						$data9 = array('data' => $date1,
									   'channel' => empty($channel) ? "all" : $channel,
									   'flag' => 3,
									   'tongji' => json_encode($tongji),
									   'addtime' => time());
						$result = $row->add($data9);			   
					}
					$tongji3[$t] = $tongji;	
					
					//print_r($tongji); exit;
				}else{
					$info = $row->where("data='$date1' and flag=3 $sql0")->find();
					$tongji3[$t] = json_decode($info['tongji'], true);
				}
				
				$t++;
			}
			//exit;
			
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
	
	//百人场庄家统计
	public function brc_bank(){
		
		$table = "log_brc_bank_statistics_log_".date("Ym");
		$bank_model = M($table, '', DB_CONFIG2);
		
		//获取所有庄家
		$bank = $bank_model->field("banknickname,bankid")->group("banknickname,bankid")->select();
		$this->assign('bank',$bank);
		
		$beginTime = I("beginTime");
		$endTime = I("endTime");
		$bankall = I("bankall");
		$win = I("win");
		$bankid = I("bankid");
		
		//查询不能大于当天
		$todaytime = strtotime(date("Y-m-d"));
		if (strtotime($beginTime)>strtotime($endTime)){
			$this->error('开始日期不能大于结束日期');
			exit;
		}
		
		if (!empty($beginTime) && !empty($endTime)){
			$beginTime = date("Y-m-d", strtotime($beginTime));
			$endTime = date("Y-m-d", strtotime($endTime));
			$day_jian = (strtotime($endTime) - strtotime($beginTime)) / (60 * 60 * 24) + 1;
		}else{
			$endTime = date("Y-m-d");
			$day_jian = 7;
			$beginTime = date("Y-m-d",strtotime("-7 day"));
		}
		
		$this->assign('beginTime',$beginTime);
		$this->assign('endTime',$endTime);
		$this->assign('bankall',$bankall);
		$this->assign('win',$win);
		$this->assign('bankid',$bankid);
	
		import('ORG.Util.Page');
		$sql0 = "(operatedate>='$beginTime 00:00:00' and operatedate<='$endTime 23:59:59')";
		if (!empty($bankall)) $sql0 .= " and bankid='$bankall'";
		if (!empty($win)) $sql0 .= ($win == 1) ? " and vargold >= 0" : " and vargold < 0";
		if (!empty($bankid)) $sql0 .= " and bankid='$bankid'";
		
		$count = $bank_model->where($sql0)->count('id');
		$Page       = new Page($count,20);//实例化分页类传入总记录数和每页显示的记录数		
		$pageshow   = $Page->show();// 分页显示输出
		$banklist = $bank_model->where($sql0)->order("id desc")->limit($Page->firstRow.','.$Page->listRows)->select();
		foreach($banklist as $key => $val){
			$banklist[$key]['beforgamegold'] = number_format($val['beforgamegold']);
			$banklist[$key]['vargold'] = number_format($val['vargold']);
			$banklist[$key]['aftergamegold'] = number_format($val['aftergamegold']);
			$banklist[$key]['eastxzcount'] = number_format($val['eastxzcount']);
			$banklist[$key]['southxzcount'] = number_format($val['southxzcount']);
			$banklist[$key]['westxzcount'] = number_format($val['westxzcount']);
			$banklist[$key]['nouthxzcount'] = number_format($val['nouthxzcount']);
			$banklist[$key]['rate'] = number_format($val['rate']);
		}

		$this->assign('pageshow',$pageshow);
		$this->assign('list',$banklist);
		
		$sum = array(0,0,0,0,0,0,0,0,0,0);
		$sum[0] = $count;
		$sum[1] = $bank_model->where($sql0." and bankid=0")->count('id');
		$sum[2] = number_format($bank_model->where($sql0." and bankid=0")->sum('vargold'));
		$sum[3] = number_format($bank_model->where($sql0." and bankid=0")->sum('eastxzcount+southxzcount+westxzcount+nouthxzcount'));
		$sum[4] = $bank_model->where($sql0." and bankid=0 and jiangjin>0")->count('id');
		$sum[5] = number_format($bank_model->where($sql0)->sum('rate'));
		$sum[6] = $bank_model->where($sql0." and bankid=1")->count('id');
		$sum[7] = number_format($bank_model->where($sql0." and bankid=1")->sum('vargold'));
		$sum[8] = number_format($bank_model->where($sql0." and bankid=1")->sum('eastxzcount+southxzcount+westxzcount+nouthxzcount'));
		$sum[9] = $bank_model->where($sql0)->sum('jiangjin');
		$this->assign('sum',$sum);
		
		$this->assign('left_css',"48");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/brc_bank";
		$this->display($lib_display);
	}
	
	//百人场散家统计
	public function brc_sanjia(){
		
		$table = "log_brc_sanjia_statistics_log_".date("Ym");
		$sanjia_model = M($table, '', DB_CONFIG2);
		$table = "log_game_record_log_".date("Ym");
		$game_model = M($table, '', DB_CONFIG2);
		
		//获取所有庄家
		$sanjia = $sanjia_model->field("nickname,user_id")->group("nickname,user_id")->select();
		$this->assign('sanjia',$sanjia);
		
		$beginTime = I("beginTime");
		$endTime = I("endTime");
		$sanjiaall = I("sanjiaall");
		$user_id = I("user_id");
		
		//查询不能大于当天
		$todaytime = strtotime(date("Y-m-d"));
		if (strtotime($beginTime)>strtotime($endTime)){
			$this->error('开始日期不能大于结束日期');
			exit;
		}
		
		if (!empty($beginTime) && !empty($endTime)){
			$beginTime = date("Y-m-d", strtotime($beginTime));
			$endTime = date("Y-m-d", strtotime($endTime));
			$day_jian = (strtotime($endTime) - strtotime($beginTime)) / (60 * 60 * 24) + 1;
		}else{
			$endTime = date("Y-m-d");
			$day_jian = 7;
			$beginTime = date("Y-m-d",strtotime("-7 day"));
		}
		
		$this->assign('beginTime',$beginTime);
		$this->assign('endTime',$endTime);
		$this->assign('sanjiaall',$sanjiaall);
		$this->assign('user_id',$user_id);
	
		import('ORG.Util.Page');
		$sql0 = "(operatedate>='$beginTime 00:00:00' and operatedate<='$endTime 23:59:59')";
		if (!empty($sanjiaall)) $sql0 .= " ";
		if (!empty($user_id)) $sql0 .= " and user_id='$user_id'";
		
		$sum = array(0,0,0,0,0,0,0,0,0,0);
		$sum[0] = $sanjia_model->where($sql0." ")->count('distinct gameid');
		$sum[1] = 0;
		$sum[2] = 0;
		$sum[3] = 0;
		$sum[4] = 0;
		$sum[5] = 0;
		$sum[6] = 0;
		
		$count = $sanjia_model->where($sql0)->count('id');
		$Page       = new Page($count,20);//实例化分页类传入总记录数和每页显示的记录数		
		$pageshow   = $Page->show();// 分页显示输出
		$sanjialist = $sanjia_model->where($sql0)->order("id desc")->limit($Page->firstRow.','.$Page->listRows)->select();
		foreach($sanjialist as $key => $val){
			$sanjialist[$key]['allxzcount'] = number_format($val['eastxzcount'] + $val['southxzcount'] + $val['westxzcount'] + $val['northxzcount']);
			
			$sanjialist[$key]['eastxzcount'] = number_format($val['eastxzcount']);
			$sanjialist[$key]['southxzcount'] = number_format($val['southxzcount']);
			$sanjialist[$key]['westxzcount'] = number_format($val['westxzcount']);
			$sanjialist[$key]['northxzcount'] = number_format($val['northxzcount']);

			//获取玩家输赢
			$sanjiagold = $game_model->where("roomid=6 AND gameid=".$val['gameid']." AND user_id=".$val['user_id'])->find();
			$sanjialist[$key]['beforegold'] = number_format($sanjiagold['beforegold']);
			$sanjialist[$key]['aftergold'] = number_format($sanjiagold['aftergold']);
			$sanjialist[$key]['changegold'] = number_format($sanjiagold['changegold']);
			$sanjialist[$key]['taxgold'] = number_format($sanjiagold['taxgold']);
			
			$sum[1] += $sanjiagold['aftergold'];
			$sum[2] += ($val['eastxzcount'] + $val['southxzcount'] + $val['westxzcount'] + $val['northxzcount']);
			$sum[3] += $sanjiagold['taxgold'];
		}
		
		$sum[1] = number_format($sum[1]);
		$sum[2] = number_format($sum[2]);
		$sum[3] = number_format($sum[3]);

		$this->assign('pageshow',$pageshow);
		$this->assign('list',$sanjialist);
		
		
		$this->assign('sum',$sum);
		
		$this->assign('left_css',"48");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/brc_sanjia";
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