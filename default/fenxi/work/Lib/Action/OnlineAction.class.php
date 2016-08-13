<?php
// 在线分析文件

class OnlineAction extends BaseAction {

	protected $By_tpl = 'Online'; 
	
	public function tongji1(){
		$table = "fx_online_tongji1";
		$row = M($table);
		
		$table2 = "login_log";
		$row2 = M($table2, '', DB_CONFIG2);
		
		$beginTime = I("beginTime");
		$endTime = I("endTime");
		$channel = I("channel");
		
		//查询不能大于昨天
		$today = date("Y-m-d");
		$yesday = date("Y-m-d",strtotime("-1 day"));
		
		
		if (empty($beginTime)){
			$beginTime = $yesday;
		}
		
		if (strtotime($beginTime)>strtotime($yesday)){
			$this->error('查询日期不能大于昨天');
			exit;
		}
		
		$this->assign('date11',$beginTime);
		$this->assign('date99',date("d",strtotime($beginTime)));
		
		//插入测试数据
		/*
		for ($i=1; $i<=10; $i++){
			$time1 = strtotime($beginTime) - 60 * 60 * 24 * ($i -1);
			$time2 = $time1 + 60 * 60 * 24;
			$date1 = date("Y-m-d", $time1);
			$date2 = date("Y-m-d", $time2);
			//$time3 = $time1 - 60 * 60 * 24;
			//$date3 = date("Y-m-d H:i:s", $time3);
			
			for ($j=$time1; $j<=$time2; $j+=300){
				$time3 = $j;
				$date3 = date("Y-m-d H:i:s", $time3);
				$data9 = array('daydate' => date("Y-m-d H:i:s"),
							   'daytime' => $time1,
							   'minitetime' => $time3,
							   'room1' => rand(1,999),
							   'room2' => rand(1,999),
							   'room3' => rand(1,999),
							   'room4' => rand(1,999));
				$result = $row1->add($data9);
			}

		}*/
		//echo date("Y-m-d H:i:s",1445529600); exit;1445558400
	
		
		//获取今日在线
		$table1 = "log_online_data_".date("Ym");
		$row1 = M($table1);
		$time1 = strtotime($today);
		$time2 = $time1 + 60 * 60 * 24;
		$date1 = date("Y-m-d", $time1);
		$date2 = date("Y-m-d", $time2);
		$res1 = $row1->where("daytime=".$time1)->order("minitetime")->select();
		//dump($row1->_sql());
		//获取昨日在线
		$time3 = strtotime($beginTime);
		$time4 = $time3 + 60 * 60 * 24;
		$date3 = date("Y-m-d", $time3);
		$date4 = date("Y-m-d", $time4);
		$table2 = "log_online_data_".date("Ym", $time3);
		$row2 = M($table2);
		$res2 = $row2->where("daytime=".$time3)->order("minitetime")->select();
		//dump($row1->_sql());
		$online1 = array();
		$data1 = array();
		$showtime = "";
		$showarr = array();
		for($i=$time1; $i<=$time2 ; $i+=900){
			$showtime .= (empty($showtime)) ? "'".date("H:i:s", $i)."'" : ",'".date("H:i:s", $i)."'";
			$showarr[] = array('time' => date("H:i:s", $i),
							   'timei' => $i);
		}
		$this->assign('showtime',$showtime);
		foreach ($showarr as $key => $val){
			if ($val['timei'] < time()){
				$flag1 = 0;
				foreach ($res1 as $key1 => $val1){
					//echo $val['time']."**".$val1['minitetime']."**". date("Y-m-d H:i:s", $val1['minitetime'])."<br>";
					if ($val['time'] == date("H:i:s", $val1['minitetime'])){
						//echo $val['time']."**".date("H:i:s", $val1['minitetime'])."**".$val1['minitetime']."<br>";
						$sum1 = $val1['room1'] + $val1['room2'] + $val1['room3'] + $val1['room7'] ;
						$data1[1] .= ($data1[1]=="") ? $sum1 : ",".$sum1;
						$data1[3] .= ($data1[3]=="") ? $val1['room1'] : ",".$val1['room1'];
						$data1[4] .= ($data1[4]=="") ? $val1['room2'] : ",".$val1['room2'];
						$data1[5] .= ($data1[5]=="") ? $val1['room3'] : ",".$val1['room3'];
						$data1[10] .= ($data1[10]=="") ? $val1['room7'] : ",".$val1['room7'];
						$flag1 = 1;
					}
				}
				if ($flag1 == 0){
					$data1[1] .= ($data1[1]=="") ? "0" : ",0";
					$data1[3] .= ($data1[3]=="") ? "0" : ",0";
					$data1[4] .= ($data1[4]=="") ? "0" : ",0";
					$data1[5] .= ($data1[5]=="") ? "0" : ",0";
					$data1[10] .= ($data1[10]=="") ? "0" : ",0";
				}
				//echo $data1[1]."<br>";
			}
			//exit;
			$flag2 = 0;
			foreach ($res2 as $key2 => $val2){
				if ($val['time'] == date("H:i:s", $val2['minitetime'])){
					$sum2 = $val2['room1'] + $val2['room2'] + $val2['room3'] + $val2['room7'] ;
					$data1[2] .= ($data1[2]=="") ? $sum2 : ",".$sum2;
					$data1[6] .= ($data1[6]=="") ? $val2['room1'] : ",".$val2['room1'];
					$data1[7] .= ($data1[7]=="") ? $val2['room2'] : ",".$val2['room2'];
					$data1[8] .= ($data1[8]=="") ? $val2['room3'] : ",".$val2['room3'];
					$data1[9] .= ($data1[9]=="") ? $val2['room7'] : ",".$val2['room7'];
					$flag2 = 1;
				}
			}
			if ($flag2 == 0){
				$data1[2] .= ($data1[2]=="") ? "0" : ",0";
				$data1[6] .= ($data1[6]=="") ? "0" : ",0";
				$data1[7] .= ($data1[7]=="") ? "0" : ",0";
				$data1[8] .= ($data1[8]=="") ? "0" : ",0";
				$data1[9] .= ($data1[9]=="") ? "0" : ",0";
			}
		}
		//print_r($data1);
		/*
		foreach ($res1 as $key1 => $val1){
			if ($key1 % 10==0){
				$sum1 = $val1['room1'] + $val1['room2'] + $val1['room3'] + $val1['room4'];
				$online1[] = array('minitetime' => $val1['minitetime'],
								   'room1' => $val1['room1'],
								   'room2' => $val1['room2'],
								   'room3' => $val1['room3'],
								   'room4' => $val1['room4'],
								   'sum1' => $sum1);
				
				$data1[0] .= ($key1==0) ? "'".date("H:i:s", $val1['minitetime'])."'" : ",'".date("H:i:s", $val1['minitetime'])."'";
				$data1[1] .= ($key1==0) ? $sum1 : ",".$sum1;				   
			}
		}
		
		//print_r($data1);
		//获取昨日在线
		$time3 = strtotime($beginTime) - 60 * 60 * 24;
		$time4 = $time3 + 60 * 60 * 24;
		$date3 = date("Y-m-d", $time3);
		$date4 = date("Y-m-d", $time4);
		$res1 = $row1->where("daytime=".$time3)->order("minitetime")->select();
		$online2 = array();
		$data2 = array();
		foreach ($res1 as $key1 => $val1){
			if ($key1 % 10==0){
				$sum1 = $val1['room1'] + $val1['room2'] + $val1['room3'] + $val1['room4'];
				$online2[] = array('minitetime' => $val1['minitetime'],
								   'room1' => $val1['room1'],
								   'room2' => $val1['room2'],
								   'room3' => $val1['room3'],
								   'room4' => $val1['room4'],
								   'sum1' => $sum1);
				$data2[0] .= ($key1==0) ? "'".date("H:i", $val1['minitetime'])."'" : ",'".date("H:i", $val1['minitetime'])."'";
				$data2[1] .= ($key1==0) ? $sum1 : ",".$sum1;				   
			}
		}*/
		//print_r($data1);
		$this->assign('data1',$data1);
		$this->assign('data2',$data2);
		
		//获取当前最新的数据
		$res1 = $row1->field("*,(room1+room2+room3+room4+room7+lobby) AS sum1")->where("daytime=".$time1)->order("minitetime desc")->find();
		$res1['lobby'] = (int)$res1['lobby'];
		$this->assign('online_now',$res1);
		
		//获取昨日最大在线
		$res1 = $row1->field("*,(room1+room2+room3+room4+room7) AS sum1")->where("daytime=".$time3)->order("sum1 desc")->find();
		$res1['day'] = date("Y-m-d", $res1['daytime']);
		$this->assign('online_yes',$res1);
		
		//获取历史最大在线
		$res1 = $row1->field("*,(room1+room2+room3+room4+room7) AS sum1")->order("sum1 desc, daytime desc")->find();
		$res1['day'] = date("Y-m-d", $res1['daytime']);
		$this->assign('online_his',$res1);
		
		/*引入GoogChart类*/
		import("ORG.Util.GoogChart");
		$chart = new GoogChart();	
		
		$this->assign('left_css',"38");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji1";
		$this->display($lib_display);
	}
	
	public function tongji1_ajax(){
		$table = "fx_online_tongji1";
		$row = M($table);
		$table1 = "log_online_data_".date("Ym");
		$row1 = M($table1, '', DB_CONFIG2);
		$table2 = "login_log";
		$row2 = M($table2, '', DB_CONFIG2);
		
		//查询不能大于昨天
		$today = date("Y-m-d");
	
		//获取今日在线
		$time1 = strtotime($today);
		$time2 = $time1 + 60 * 60 * 24;
		$date1 = date("Y-m-d", $time1);
		$date2 = date("Y-m-d", $time2);

		/*
		$res1 = $row1->where("daytime=".$time1)->order("minitetime")->select();
		$online1 = array();
		$data1 = array();
		$showtime = "";
		$showarr = array();
		for($i=$time1; $i<=$time2 ; $i+=900){
			$showtime .= (empty($showtime)) ? "'".date("H:i:s", $i)."'" : ",'".date("H:i:s", $i)."'";
			$showarr[] = array('time' => date("H:i:s", $i),
							   'timei' => $i);
		}

		
		foreach ($showarr as $key => $val){
			if ($val['timei'] < time()){
				$flag1 = 0;
				foreach ($res1 as $key1 => $val1){
					//echo $val['time']."**".$val1['minitetime']."**". date("Y-m-d H:i:s", $val1['minitetime'])."<br>";
					if ($val['time'] == date("H:i:s", $val1['minitetime'])){
						//echo $val['time']."**".date("H:i:s", $val1['minitetime'])."**".$val1['minitetime']."<br>";
						$sum1 = $val1['room1'] + $val1['room2'] + $val1['room3'] + $val1['room4'] + $val1['lobby'];
						$data1[1] .= ($data1[1]=="") ? $sum1 : ",".$sum1;
						$flag1 = 1;
					}
				}
				if ($flag1 == 0){
					$data1[1] .= ($data1[1]=="") ? "0" : ",0";
				}
				//echo $data1[1]."<br>";
			}
		}*/

		
		//获取当前最新的数据
		$res1 = $row1->field("*,(room1+room2+room3+room4+lobby) AS sum1")->where("daytime=".$time1)->order("minitetime desc")->find();
		$res1['lobby'] = (int)$res1['lobby'];
		echo $res1['sum1']."|".$res1['lobby']."|".$res1['room1']."|".$res1['room2']."|".$res1['room3'];
	}
	
	//在线用户
	public function tongji2(){
		$table = "fx_online_tongji2";
		$row = M($table);
		
		$table2 = "payment";
		$row2 = M($table2);
		$table3 = "zjh_goods";
		$row3 = M($table3);
		
		$table5 = "user_info";
		$row5 = M($table5, '', DB_CONFIG2);
		$table6 = "zjh_order";
		$row6 = M($table6);
		
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
			
			for ($i=1; $i<=$day_jian; $i++){
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
									
					if ($date1<date("Y-m-d")){
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
				
				/*
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
					$tongji3[$j] = $tongji;	
				}else{
					$info = $row->where("data='$date1' and flag=3 $sql0")->find();
					$tongji3[$j] = json_decode($info['tongji'], true);
				}
				*/
			}
			
			foreach ($tongji2 as $key => $val){
				$data1[0] .= ($key==0) ? "'".$val['data']."'" : ",'".$val['data']."'";
				$data1[1] .= ($key==0) ? $val['count7'] : ",".$val['count7'];
				$data1[2] .= ($key==0) ? $val['count8'] : ",".$val['count8'];
			}
		} 
		//print_r($tongji2); 
		//print_r($tongji3);
		$pagesize = ceil($day_jian / 10);
		$this->assign('pagesize',$pagesize);
		$this->assign('tongji2',json_encode($tongji2));
		//$this->assign('tongji3',json_encode($tongji3));
		
		$this->assign('data1',$data1);

		
		/*引入GoogChart类*/
		import("ORG.Util.GoogChart");
		$chart = new GoogChart();	

		
		$this->assign('left_css',"38");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji2";
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