<?php
// 老虎机分析文件

class TrigerAction extends BaseAction {

	protected $By_tpl = 'Triger'; 
	
	//老虎机局数
	public function tongji1(){
		$table = "fx_paiju_tongji";
		$row = M($table);
		//$table1 = "log_tiger_record_log";
		//$row1 = M($table1, '', DB_CONFIG2);
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
			for ($i=$maxi; $i>$mini; $i--){
				
				$j = $i - 1;	
				$time1 = strtotime($date11) + 60 * 60 * 24 * ($i - 1);
				$time2 = $time1 + 60 * 60 * 24;
				$date1 = date("Y-m-d", $time1);
				$date2 = date("Y-m-d", $time2);
				//echo $date1."***".$date2."<br>";
				
				if ($date1 < date("Y-m-d")){
					if ($date1 < "2016-01-01"){
						$table1 = "log_tiger_2015";
					}else{
						$table1 = "log_tiger_".date("Ym", $time1);
					}
					$row1 = M($table1, '', DB_CONFIG3);
				}else{
					$table1 = "log_tiger_record_log";
					$row1 = M($table1, '', DB_CONFIG2);
				}
				
				//当日玩老虎机记录
				$flag = "4";
				//echo "**";
				$total = $row->where("data='$date1' and flag=$flag $sql0")->count();
				//dump($row->_sql());
				if ($total == 0){
					$tongji = array();
					for($k=0; $k<10; $k++){
						$tongji[0]['all'][$k]['count'] = 0;
					}
					
					//参与人数
					$count5 = 0;
					//总局数
					$count4 = 0;
					//总用户游戏人数
					$count4 = $row1->where("selecttime>='$time1' and selecttime<'$time2' and type=1")->count('id');
					$count5 = $row1->where("selecttime>='$time1' and selecttime<'$time2' and type=1")->count('distinct user_id');
					
					$sql11 = "SELECT COUNT(user_id) AS nums FROM $table1 WHERE selecttime>=$time1 AND selecttime<$time2 GROUP BY user_id ";
					$row11 = $row1->query($sql11);
					foreach($row11 as $key11 => $val11){
						$count1 = $val11['nums'];
						if ($count1==0){
							$tongji[0]['all'][0]['count']++;
						}else if ($count1<6){
							$tongji[0]['all'][1]['count']++;
						}else if ($count1<11){
							$tongji[0]['all'][2]['count']++;
						}else if ($count1<16){
							$tongji[0]['all'][3]['count']++;
						}else if ($count1<21){
							$tongji[0]['all'][4]['count']++;
						}else if ($count1<31){
							$tongji[0]['all'][5]['count']++;
						}else if ($count1<41){
							$tongji[0]['all'][6]['count']++;
						}else if ($count1<51){
							$tongji[0]['all'][7]['count']++;
						}else if ($count1<101){
							$tongji[0]['all'][8]['count']++;
						}else{
							$tongji[0]['all'][9]['count']++;
						}
					}
					
					//总投注
					$count2 = $row1->where("$sql1 and selecttime>='$time1' and selecttime<'$time2' and (type=1 or type=2)")->sum('goldnum');
					$count2 = abs($count2);
					//总输赢
					$count3 = $row1->where("$sql1 and selecttime>='$time1' and selecttime<'$time2'")->sum('goldnum');
					if (empty($count3)) $count3 = 0;  else $count3 = -$count3;
					
					//总局数
					//$count4 = $row1->where("$sql1 and selecttime>='$time1' and selecttime<'$time2' and type=1")->count('id');
					//if ($date1=="2015-11-13") dump($row1->_sql());
					//净耗率
					$count6 = (!empty($count2)) ? round($count3/$count2,2) : 0;
					
					$tongji0 = array('data' => $date1,
									 'tongji' => $tongji,
									 'count2' => $count2,
									 'count3' => $count3,
									 'count4' => $count4,
									 'count5' => $count5,
									 'count6' => $count6);
									
					if ($date1<date("Y-m-d")){
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
				
			}
		} 
		//print_r($tongji1); 
		//print_r($tongji3);
		$this->assign('tongji1',$tongji1);


		$this->assign('pageshow',$show);
		$this->assign('left_css',"62");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji1";
		$this->display($lib_display);
	}
	
	//老虎机牌型
	public function tongji2(){
		$table = "fx_paiju_tongji";
		$row = M($table);
		//$table1 = "log_tiger_record_log";
		//$row1 = M($table1, '', DB_CONFIG2);
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
			for ($i=$maxi; $i>$mini; $i--){
				
				$j = $i - 1;	
				$time1 = strtotime($date11) + 60 * 60 * 24 * ($i - 1);
				$time2 = $time1 + 60 * 60 * 24;
				$date1 = date("Y-m-d", $time1);
				$date2 = date("Y-m-d", $time2);
				//echo $date1."***".$date2."<br>";
				
				//当日玩老虎机记录
				$flag = "5";
				//echo "**";
				$total = $row->where("data='$date1' and flag=$flag $sql0")->count();
				//dump($row->_sql());
				if ($total == 0){
					$tongji = array();
					/*$tongji[0] = array('type' => '1',
									   'name' => '单牌');*/
					$tongji[0] = array('type' => '2',
									   'name' => '对子');
					$tongji[1] = array('type' => '3',
									   'name' => '顺子');
					$tongji[2] = array('type' => '4',
									   'name' => '金花');
					$tongji[3] = array('type' => '5',
									   'name' => '顺金');				   
					$tongji[4] = array('type' => '6',
									   'name' => '豹子');
					$tongji[5] = array('type' => '10',
									   'name' => '地龙');
					$tongji[6] = array('type' => '11',
									   'name' => '一花');
					$tongji[7] = array('type' => '12',
									   'name' => '二花');
					$tongji[8] = array('type' => '13',
									   'name' => '王牌AAA');
					$tongji[9] = array('type' => '14',
									    'name' => '无花');						
									   
					//dump($row5->_sql());
					if ($date1 < date("Y-m-d")){
						if ($date1 < "2016-01-01"){
							$table1 = "log_tiger_2015";
						}else{
							$table1 = "log_tiger_".date("Ym", $time1);
						}
						$row1 = M($table1, '', DB_CONFIG3);
					}else{
						$table1 = "log_tiger_record_log";
						$row1 = M($table1, '', DB_CONFIG2);
					}
					
					foreach ($tongji as $key => $val){
						$count1 = $row1->where("selecttime>='$time1' and selecttime<'$time2' and cardtype='".$val['type']."' and type in (1,2)")->count('id');
						//dump($row1->_sql());
						//if ($date1=="2015-11-13") dump($row1->_sql());
						$tongji[$key]['count'] = $count1;	
							
							
					}
										
					$tongji0 = array('data' => $date1,
									 'tongji' => $tongji);
									
					if ($date1<date("Y-m-d")){
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
				
			}
		} 
		//print_r($tongji1); 
		//print_r($tongji3);
		$this->assign('tongji1',$tongji1);


		$this->assign('pageshow',$show);
		$this->assign('left_css',"62");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji2";
		$this->display($lib_display);
	}
	
	//老虎机详情
	public function tongji3(){
		$table = "fx_paiju_tongji";
		$row = M($table);
		
		$table5 = "user_info";
		$row5 = M($table5, '', DB_CONFIG2);
		
		$beginTime = I("beginTime");
		$endTime = I("endTime");
		$user_id = I("user_id");
		
		//查询不能大于当天
		$timenow = strtotime(date("Y-m-d"));
		if (strtotime($beginTime)>strtotime($endTime)){
			$this->error('开始日期不能大于结束日期');
			exit;
		}
		
		if (!empty($beginTime) && !empty($endTime)){
			$date11 = date("Y-m-d", strtotime($beginTime));
			$date12 = date("Y-m-d", strtotime($endTime));
		}else{
			$date12 = date("Y-m-d");
			$date11 = $date12;
		}
		
		$this->assign('date11',$date11);
		$this->assign('date12',$date12);
		$this->assign('user_id',$user_id);
		
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
		
		if (!empty($user_id)) $sql0 = " and user_id=$user_id"; else $sql0 = "";
		
		$time11 = strtotime($date11);
		$time12 = strtotime($date12) + 60 * 60 * 24;
		
		if ($date11 < date("Y-m-d")){
			if ($date11 < "2016-01-01"){
				$table1 = "log_tiger_2015";
			}else{
				$table1 = "log_tiger_".date("Ym", $time11);
			}
			$row1 = M($table1, '', DB_CONFIG3);
		}else{
			$table1 = "log_tiger_record_log";
			$row1 = M($table1, '', DB_CONFIG2);
		}
		
		
		$count = $row1->where("selecttime>=$time11 and selecttime<$time12 and type in (1,2) $sql0")->count('id');
		import('ORG.Util.Page');
		$Page       = new Page($count,PAGE_SHOW);	
		$show       = $Page->show();
		$info = $row1->where("selecttime>=$time11 and selecttime<$time12 and type in (1,2) $sql0")->order("id")->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($row1->_sql());
		foreach ($info as $key => $val){
				if ($val['iswin']==0) $win_count++; else $lost_count++;
				$num1 = substr($val['cards'],0,2);
				$pai1 = '<img src="'.DB_HOST.'/Public/images/'.$num1.'.png" width="30">';
				$num2 = substr($val['cards'],2,2);
				$pai2 = '<img src="'.DB_HOST.'/Public/images/'.$num2.'.png" width="30">';
				$num3 = substr($val['cards'],4,2);
				$pai3 = '<img src="'.DB_HOST.'/Public/images/'.$num3.'.png" width="30">';
				$showcards = $pai1.$pai2.$pai3;
				$info[$key]['showcards'] = $showcards;
				
				switch ($val['cardtype']){
					case 1:  $cardtype = "单牌"; break;
					case 2:  $cardtype = "对子"; break;
					case 3:  $cardtype = "顺子"; break;
					case 4:  $cardtype = "金花"; break;
					case 5:  $cardtype = "顺金"; break;
					case 6:  $cardtype = "豹子"; break;
					case 10: $cardtype = "地龙"; break;
					case 11: $cardtype = "一花"; break;
					case 12: $cardtype = "二花"; break;
					case 13: $cardtype = "王牌AAA"; break;
					case 14: $cardtype = "无花"; break;
					default: $cardtype = "未知"; break;
				}
				$info[$key]['cardtype'] = $cardtype;
				$info[$key]['type'] = ($val['type']==1) ? "买" : "换";
				
				//获取系统结算
				$nextid = $val['id'] + 1;
				$res = $row1->where("id=".$nextid)->find();
				if ($res['type'] == 3){
					$info[$key]['xtjs'] = $res['goldnum'];
				}else{
					$info[$key]['xtjs'] = 0;
				}
		}
		
		$sum = array(0,0);
		$sum[0] = $row1->where("selecttime>$time11 and selecttime<$time12 and type in (1,2) $sql0")->sum('goldnum');
		$sum[1] = $row1->where("selecttime>$time11 and selecttime<$time12 and type=3 $sql0")->sum('goldnum');
		$this->assign('sum',$sum);
		$this->assign('info',$info);
		$this->assign('pageshow',$show);
		
		$this->assign('left_css',"62");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji3";
		$this->display($lib_display);
	}
	
	//时时彩详情
	public function tongji4(){
		$table = "fx_paiju_tongji";
		$row = M($table);
		$table1 = "kingflower.lottery_log";
		$row1 = M($table1);
		$table2 = "kingflower.log_lottery_bet_log";
		$row2 = M($table2);
		$table5 = "user_info";
		$row5 = M($table5);
		
		$beginTime = I("beginTime");
		$endTime = I("endTime");
		$user_id = I("user_id");
		
		//查询不能大于当天
		$timenow = strtotime(date("Y-m-d"));
		if (strtotime($beginTime)>strtotime($endTime)){
			$this->error('开始日期不能大于结束日期');
			exit;
		}
		
		if (!empty($beginTime) && !empty($endTime)){
			$date11 = date("Y-m-d", strtotime($beginTime));
			$date12 = date("Y-m-d", strtotime($endTime));
		}else{
			$date12 = date("Y-m-d");
			$date11 = $date12;
		}
		
		$this->assign('date11',$date11);
		$this->assign('date12',$date12);
		$this->assign('user_id',$user_id);
		
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
		
		if (!empty($user_id)) $sql0 = " "; else $sql0 = "";
		
		$time11 = strtotime($date11);
		$time12 = strtotime($date12) + 60 * 60 * 24;
		$count = $row1->where("intime>'$date11' and intime<'$date12 23:59:59' $sql0")->count('id');
		//dump($row1->_sql());
		import('ORG.Util.Page');
		$Page       = new Page($count,PAGE_SHOW);	
		$show       = $Page->show();
		$info = $row1->where("intime>'$date11' and intime<'$date12 23:59:59' $sql0")->order("id desc")->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($row1->_sql());
		
		foreach ($info as $key => $val){
			$max_win = 0;
			$max_lost = 0;
			//最大赢家盈利额lotteryid
			$res = $row2->field("user_id")->group("user_id")->where("lotteryid=".$val['lotteryid'])->select();
			foreach($res as $key1 => $val1){
				$win = $row2->where("lotteryid=".$val['lotteryid']." and user_id=".$val1['user_id']." and type=2")->sum('gold');
				$lost = $row2->where("lotteryid=".$val['lotteryid']." and user_id=".$val1['user_id']." and type=1")->sum('gold');
				if ($win > $max_win) $max_win = $win;
				if ($lost < $max_lost) $max_lost = $lost;
			}
			
			$info[$key]['max_win'] = $max_win;
			$info[$key]['max_lost'] = $max_lost;
		}
		//print_r($info);
		
		$this->assign('info',$info);
		$this->assign('pageshow',$show);
		
		$this->assign('left_css',"62");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji4";
		$this->display($lib_display);
	}
	
	//时时彩详情
	public function more(){
		$table = "fx_paiju_tongji";
		$row = M($table);
		$table1 = "kingflower.lottery_log";
		$row1 = M($table1);
		$table2 = "kingflower.log_lottery_bet_log";
		$row2 = M($table2);
		$table5 = "user_info";
		$row5 = M($table5);
		
		$lotteryid = I("lotteryid");
		$endTime = I("endTime");
		$user_id = I("user_id");
		
		//查询不能大于当天
		$timenow = strtotime(date("Y-m-d"));
		if (empty($lotteryid)){
			$this->error('输入有误');
			exit;
		}
		

		$count = $row2->where("lotteryid=".$lotteryid)->count('id');
		//dump($row1->_sql());
		import('ORG.Util.Page');
		$Page       = new Page($count,PAGE_SHOW);	
		$show       = $Page->show();
		$info = $row2->where("lotteryid=".$lotteryid)->order("id ")->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($row2->_sql());
		
		foreach ($info as $key => $val){

			if ($val['type']==1){
				$info[$key]['type'] = "购买";
			}else if ($val['type']==1){
				$info[$key]['type'] = "领奖";
			}else{
				$info[$key]['type'] = "";
			}
			$info[$key]['selecttime'] = date("Y-m-d H:i:s", $val['selecttime']);
		}
		//print_r($info);
		
		$this->assign('info',$info);
		$this->assign('pageshow',$show);
		
		$this->assign('left_css',"62");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/more";
		$this->display($lib_display);
	}
	
	//EXCEL导出
	public function exceldo(){
		$table = "fx_paiju_tongji";
		$row = M($table);
		$table1 = "log_tiger_record_log";
		$row1 = M($table1, '', DB_CONFIG2);
		$table5 = "user_info";
		$row5 = M($table5, '', DB_CONFIG2);
		
		$beginTime = I("beginTime");
		$endTime = I("endTime");
		$user_id = I("user_id");
		
		//查询不能大于当天
		$timenow = strtotime(date("Y-m-d"));
		if (strtotime($beginTime)>strtotime($endTime)){
			$this->error('开始日期不能大于结束日期');
			exit;
		}
		
		if (!empty($beginTime) && !empty($endTime)){
			$date11 = date("Y-m-d", strtotime($beginTime));
			$date12 = date("Y-m-d", strtotime($endTime));
		}else{
			$date12 = date("Y-m-d");
			$date11 = $date12;
		}
		
		
		if (!empty($user_id)) $sql0 = " and user_id=$user_id"; else $sql0 = "";
		
		$time11 = strtotime($date11);
		$time12 = strtotime($date12) + 60 * 60 * 24;
		//$count = $row1->where("selecttime>$time11 and selecttime<$time12 and type in (1,2) $sql0")->count('id');
		//dump($row1->_sql());
		//import('ORG.Util.Page');
		//$Page       = new Page($count,PAGE_SHOW);	
		//$show       = $Page->show();
		$info = $row1->where("selecttime>$time11 and selecttime<$time12 and type in (1,2) $sql0")->order("id")->select();
		//dump($row1->_sql());
		foreach ($info as $key => $val){
				if ($val['iswin']==0) $win_count++; else $lost_count++;
				$num1 = substr($val['cards'],0,2);
				$pai1 = '<img src="'.DB_HOST.'/Public/images/'.$num1.'.png" width="30">';
				$num2 = substr($val['cards'],2,2);
				$pai2 = '<img src="'.DB_HOST.'/Public/images/'.$num2.'.png" width="30">';
				$num3 = substr($val['cards'],4,2);
				$pai3 = '<img src="'.DB_HOST.'/Public/images/'.$num3.'.png" width="30">';
				$showcards = $pai1.$pai2.$pai3;
				$info[$key]['showcards'] = $showcards;
				
				switch ($val['cardtype']){
					case 1:  $cardtype = "单牌"; break;
					case 2:  $cardtype = "对子"; break;
					case 3:  $cardtype = "顺子"; break;
					case 4:  $cardtype = "金花"; break;
					case 5:  $cardtype = "顺金"; break;
					case 6:  $cardtype = "豹子"; break;
					case 10: $cardtype = "地龙"; break;
					case 11: $cardtype = "一花"; break;
					case 12: $cardtype = "二花"; break;
					case 13: $cardtype = "王牌AAA"; break;
					case 14: $cardtype = "无花"; break;
					default: $cardtype = "未知"; break;
				}
				$info[$key]['cardtype'] = $cardtype;
				$info[$key]['type'] = ($val['type']==1) ? "买" : "换";
				
				//获取系统结算
				$nextid = $val['id'] + 1;
				$res = $row1->where("id=".$nextid)->find();
				if ($res['type'] == 3){
					$info[$key]['xtjs'] = $res['goldnum'];
				}else{
					$info[$key]['xtjs'] = 0;
				}
		}
		
		$sum = array(0,0);
		$sum[0] = $row1->where("selecttime>$time11 and selecttime<$time12 and type in (1,2) $sql0")->sum('goldnum');
		$sum[1] = $row1->where("selecttime>$time11 and selecttime<$time12 and type=3 $sql0")->sum('goldnum');
		$this->assign('sum',$sum);
		$this->assign('info',$info);
		$this->assign('pageshow',$show);
		
		$xlsName  = "老虎机详情";
		$xlsCell  = array(
			array('user_id','用户UID'),
			array('type','类别'),
			array('cardtype','牌型'),
			array('goldnum','投注金币数'),
			array('xtjs','系统结算'),
			array('curgold','当前金币数'),
			array('disdate','添加时间')   
		);
		$xlsData = array();
		foreach ($info as $k => $v)
		{
				$xlsData[$k]['user_id'] = $v['user_id'];
				$xlsData[$k]['type'] = $v['type'];
				$xlsData[$k]['cardtype'] = $v['cardtype'];
				$xlsData[$k]['goldnum'] = $v['goldnum'];
				$xlsData[$k]['xtjs'] = $v['xtjs'];
				$xlsData[$k]['curgold'] = $v['curgold'];
				$xlsData[$k]['disdate'] = $v['disdate'];
		}
		exportExcel($xlsName,$xlsCell,$xlsData);
		exit;

	}
	
	//大转盘抽奖记录
	public function wheel(){

		$row = M("log_lotterydraw_record_log", '', DB_CONFIG2);
		$user = M("user_info", '', DB_CONFIG2);
		
		import('ORG.Util.Page');
		
		$user_id = I("user_id");
		$awardtype = I("awardtype");
		$this->assign('awardtype',$awardtype);
		$this->assign('user_id',$user_id);
		$sql11 = "";
		if (!empty($awardtype)) $sql11 = " and awardtype=$awardtype"; 
		if (!empty($user_id)) $sql11 = " and user_id=$user_id";  		 		
		$count = $row->where("1".$sql11)->count('id');
		$Page       = new Page($count,20);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		$list = $row->where("1".$sql11)->order("id DESC")->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($row->_sql());
		//exit;
		foreach($list as $key=>$val){
			$list[$key]['operator'] =  date("Y-m-d H:i:s", $val['operator']);
			$userinfo = $user->field('nick_name,nickname')->where("user_id=".$val['user_id'])->find();
			$list[$key]['nickname'] = (!empty($userinfo['nickname'])) ? $userinfo['nickname'] : $userinfo['nick_name'];
			
			if ($val['drawtype']==1) {
				$list[$key]['type'] = '免费抽'; 
			}elseif ($val['drawtype']==2){
				$list[$key]['type'] = '金币抽'; 
			}elseif ($val['drawtype']==3){
				$list[$key]['type'] = '钻石抽'; 
			}
			
			switch($val['awardtype']){
				case 1: $list[$key]['showtype'] = "金币"; break;
				case 2: $list[$key]['showtype'] = "话费"; break;
				case 3: $list[$key]['showtype'] = "车"; break;
				case 4: $list[$key]['showtype'] = "飞机"; break;
				case 5: $list[$key]['showtype'] = "奖券"; break;
				default: $list[$key]['showtype'] = ""; break;
			}
		}
		//print_r($list);
		$this->assign('list',$list);
		$this->assign('pageshow',$show);
		
		$this->assign('left_css',"20");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/wheel";
		$this->display($lib_display);		
	}
	
	//奖券兑换记录
	public function lottery(){

		$row = M("log_mall_lottery_log", '', DB_CONFIG2);
		$user = M("user_info", '', DB_CONFIG2);
		
		import('ORG.Util.Page');
		
		$id = I("id");
		$act = I("act");
		$user_id = I("user_id");
		$type = I("type");
		$status = I("status");
		$this->assign('type',$type);
		$this->assign('status',$status);
		$this->assign('user_id',$user_id);
		
		if ($act == "edit" && !empty($id)){
			if (!empty($_POST)){
				$data = array();
				$data['status'] = $_POST['status'];
				$data['meno'] = $_POST['meno'];
				$result = $row->where("id=".$id)->save($data);
				if($result){
					//发邮件
					$table5 = M("user_email", '', DB_CON_GAME);
					$arr = array();
					$arr['user_id'] = $user_id;
					$arr['email_type'] = 7;
					$arr['is_read'] = 0;
					$arr['content'] = $_POST['meno'];
					$arr['opera_date'] = date("Y-m-d H:i;s");
					$email_id = $table5->add($arr);
					
					$this->success('修改成功',U($this->By_tpl.'/lottery'));
					exit;
				}else{

					$this->error('修改失败');
					exit;
				}
			}else{
				$info = $row->where("id=".$id)->find();
				$this->assign('info',$info);
				$this->assign('left_css',"20");
				$this->assign('By_tpl',$this->By_tpl);
				$lib_display = $this->By_tpl."/lottery_more";
				$this->display($lib_display);
				exit;
			}
		}
		
		$sql11 = "";
		if (!empty($type)) $sql11 .= " and type=$type"; 
		if (!empty($user_id)) $sql11 .= " and user_id=$user_id";  
		if ($status == "1" || $status == "3") $sql11 .= " and status=$status"; 
		if ($status == "2") $sql11 .= " and status=0"; 
	
		$count = $row->where("1".$sql11)->count('id');
		$Page       = new Page($count,20);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		$list = $row->where("1".$sql11)->order("id DESC")->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($row->_sql());
		//exit;
		foreach($list as $key=>$val){
			$list[$key]['addtime'] =  date("Y-m-d H:i:s", $val['addtime']);
			$userinfo = $user->field('nick_name,nickname')->where("user_id=".$val['user_id'])->find();
			$list[$key]['nickname'] = (!empty($userinfo['nickname'])) ? $userinfo['nickname'] : $userinfo['nick_name'];
			
			if ($val['type']==1) {
				$list[$key]['showtype'] = '兑换金币'; 
			}elseif ($val['type']==3){
				$list[$key]['showtype'] = '兑换话费'; 
			}elseif ($val['type']==4){
				$list[$key]['showtype'] = '兑换SVIP卡'; 
			}
			
			if ($val['status']==1) {
				$list[$key]['status'] = '成功'; 
			}elseif ($val['status']==3){
				$list[$key]['status'] = '已兑换'; 
			}else{
				$list[$key]['status'] = '失败'; 
			}
		}
		//print_r($list);
		$this->assign('list',$list);
		$this->assign('pageshow',$show);
		
		$this->assign('left_css',"20");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/lottery";
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
		
		$this->assign('left_css',"20");
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
		
		$this->assign('left_css',"20");
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