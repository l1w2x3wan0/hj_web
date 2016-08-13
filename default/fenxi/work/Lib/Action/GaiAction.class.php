<?php
// 概况文件

class GaiAction extends BaseAction {

	protected $By_tpl = 'Gai'; 
	
	//欢迎页面
	public function wel(){
				
		$this->assign('left_css',"36");
		$this->assign('list',$tongji_show);
		$this->assign('totalall',$totalall);
		
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/wel";
		$this->display($lib_display);
	}
	
	//运营数据表
	public function yunying(){
		
		$beginTime = I("beginTime");
		$endTime = I("endTime");
		$act = I("act");
		
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
			$date11 = date("Y-m-d", (strtotime($date12) - 60 * 60 * 24 * 7));
			$date12 = date("Y-m-d", (strtotime($date12) - 60 * 60 * 24 * 1));
		}
		//echo $day_jian;
		//$date11 .= " 00:00:00"; 
		//$date12 .= " 23:59:59"; 
		
		$this->assign('date11',$date11);
		$this->assign('date12',$date12);

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

		if ($day_jian >= 0){
			
			$table1 = "fx_user_base";
			$row1 = M($table1);
			$table2 = "fx_jinbi_base";
			$row2 = M($table2);
			$table3 = "fx_tongji1";
			$row3 = M($table3);
			
			$tongji1 = array();
			for ($i=1; $i<=$day_jian; $i++){
				$j = $i - 1;	
				$time1 = strtotime($date11) + 60 * 60 * 24 * ($i - 1);
				$date1 = date("Y-m-d", $time1);
				//echo $date1."<br>";
				$tongji = array();
				//获取常用基本统计
				$info = $row1->where("data='$date1'")->find();
				$tongji = $info;
				//获取破产统计
				$info = $row3->where("data='$date1' and flag=11 and channel='all' and channel='all'")->find();
				$tongji['pochan'] = json_decode($info['tongji'], true);
				
				$tongji1[$j] = $tongji;
			}
		} 
		
		//print_r($tongji1);
		
		if ($act == "exceldo1"){
			$xlsName  = "统计需要数据";
			
			$xlsCell  = array(
				array('data','日期'),
				array('user_add','新增用户'),
				array('user_pay_num','新增付费人数'),
				array('user_pay_money','新增付费金额'),
				array('user_pay_lv','新增付费率'),
				array('user_num','用户总量'),
				array('dau','DAU'),
				array('dau_old','DAU（老用户）'),
				array('user_add_ok','有效新增用户'),
				array('user_ok_lv','有效率'),
				array('liucun1','次日留存'),
				array('liucun2','三日留存'),
				array('liucun3','七日留存'),
				array('online1','平均在线'),
				array('online2','峰值在线'),
				array('paiju','平均牌局数'),
				array('arpu','活跃arpu'),
				array('arppu','日arppu'),
				array('arpu_new','新增arpu'),
				array('user_all_pay_num','付费人数'),
				array('user_all_pay_money','付费金额'),
				array('uesr_all_pay_lv','付费率'),
				array('sum_jb_play','玩家金币总和'),
				array('sum_jb_active','活跃玩家金币总和'),
				array('sum01','新用户领取1,2,3次破产次数'),
				array('sum02','新用户领取1,2,3次破产人数'),
				array('sum03','新用户破产率'),
				array('sum04','新用户领取2,3次破产的次数'),
				array('sum05','新用户领取2,3次破产的人数'),
				array('sum06','新用户领取3次破产的次数'),
				array('sum07','新用户领取3次破产的人数'),
				array('sum21','活跃玩家领取1,2,3次破产次数'),
				array('sum22','活跃玩家领取1,2,3次破产人数'),
				array('sum23','活跃玩家领取2,3次破产的次数'),
				array('sum24','活跃玩家领取2,3次破产的人数'),
				array('sum25','活跃玩家领取3次破产的次数'),
				array('sum26','活跃玩家领取3次破产的人数')
			);
			//print_r($xlsCell);
			$xlsData = array();
			foreach ($tongji1 as $k => $v)
			{
				$xlsData[$k]['data'] = " ".$v['data']." ";
				$xlsData[$k]['user_add'] = $v['user_add'];
				$xlsData[$k]['user_pay_num'] = $v['user_pay_num'];
				$xlsData[$k]['user_pay_money'] = $v['user_pay_money'];
				$xlsData[$k]['user_pay_lv'] = $v['user_pay_lv'];
				$xlsData[$k]['user_num'] = $v['user_num'];
				$xlsData[$k]['dau'] = $v['dau'];
				$xlsData[$k]['dau_old'] = $v['dau_old'];
				$xlsData[$k]['user_add_ok'] = $v['user_add_ok'];
				$xlsData[$k]['user_ok_lv'] = $v['user_ok_lv'];
				$xlsData[$k]['liucun1'] = $v['liucun1'];
				$xlsData[$k]['liucun2'] = $v['liucun2'];
				$xlsData[$k]['liucun3'] = $v['liucun3'];
				$xlsData[$k]['online1'] = $v['online1'];
				$xlsData[$k]['online2'] = $v['online2'];
				$xlsData[$k]['paiju'] = $v['paiju'];
				$xlsData[$k]['arpu'] = $v['arpu'];
				$xlsData[$k]['arppu'] = $v['arppu'];
				$xlsData[$k]['arpu_new'] = $v['arpu_new'];
				$xlsData[$k]['user_all_pay_num'] = $v['user_all_pay_num'];
				$xlsData[$k]['user_all_pay_money'] = $v['user_all_pay_money'];
				$xlsData[$k]['uesr_all_pay_lv'] = $v['uesr_all_pay_lv'];
				$xlsData[$k]['sum_jb_play'] = $v['pochan']['sum_jb_play'];
				$xlsData[$k]['sum_jb_active'] = $v['pochan']['sum_jb_active'];
				$xlsData[$k]['sum01'] = $v['pochan']['sum1'][0];
				$xlsData[$k]['sum02'] = $v['pochan']['sum1'][1];
				$xlsData[$k]['sum03'] = $v['pochan']['sum1'][2];
				$xlsData[$k]['sum04'] = $v['pochan']['sum1'][3];
				$xlsData[$k]['sum05'] = $v['pochan']['sum1'][4];
				$xlsData[$k]['sum06'] = $v['pochan']['sum1'][5];
				$xlsData[$k]['sum07'] = $v['pochan']['sum1'][6];
				$xlsData[$k]['sum21'] = $v['pochan']['sum2'][0];
				$xlsData[$k]['sum22'] = $v['pochan']['sum2'][1];
				$xlsData[$k]['sum23'] = $v['pochan']['sum2'][2];
				$xlsData[$k]['sum24'] = $v['pochan']['sum2'][3];
				$xlsData[$k]['sum25'] = $v['pochan']['sum2'][4];
				$xlsData[$k]['sum26'] = $v['pochan']['sum2'][5];
			}
			//print_r($xlsData);
			//exit;
			exportExcel($xlsName,$xlsCell,$xlsData);
			exit;
		}

		$this->assign('list',$tongji1);
		
		$this->assign('left_css',"36");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/yunying";
		$this->display($lib_display);
	}
	
	//日报表
	public function gaiday(){
		$table = "fx_gai_tongji1";
		$row = M($table);
		$table3 = "pay_now_config.zjh_order";
		$row3 = M($table3);
		$table1 = "user_info";
		$row1 = M($table1, '', DB_CONFIG2);
		//$table2 = "login_log";
		$table2 = "login_log_".date("Ym");
		$row2 = M($table2, '', DB_CONFIG2);
		
		
		$beginTime = I("beginTime");
		$endTime = I("endTime");
		$channel = I("channel");
		
		//查询不能大于当天
		$timenow = strtotime(date("Y-m-d"));
		$day1 = date("Y-m-d", strtotime("-1 day"));
		$day7 = date("Y-m-d", strtotime("-8 day"));
		//echo $beginTime."**".date("Y-m-d")."<br>".strtotime($beginTime)."**".$timenow; exit;
		if (strtotime($beginTime)>$timenow){
			$this->error('开始日期不能大于当日');
			exit;
		}
		
		if (empty($beginTime)){
			$beginTime = date("Y-m-d", strtotime("-1 day"));
		}
		//echo $day_jian;
		//$date11 .= " 00:00:00"; 
		//$date12 .= " 23:59:59"; 
		$tongji_day = array($beginTime);
		$this->assign('beginTime',$beginTime);
		$this->assign('date12',$date12);
		$this->assign('channel',$channel);
		
		$day_jian = 7;
		//环比上日
		$hbr_time = strtotime($beginTime) - 60 * 60 * 24;
		$hbr = date("Y-m-d", $hbr_time);
		//同比上周
		$tbz_time = strtotime($beginTime) - 60 * 60 * 24 * $day_jian;
		$tbz = date("Y-m-d", $tbz_time);
		$this->assign('hbr',$hbr);
		$this->assign('tbz',$tbz);
		$zrq = array();
		for($i=0; $i<$day_jian; $i++){
			$time_temp = strtotime($beginTime) - 60 * 60 * 24 * ($i + 1);
			$zrq[] = array('date' => date("Y-m-d", $time_temp));
			$tongji_day[$i+1] = date("Y-m-d", $time_temp);
		}
		$this->assign('zrq',$zrq);
		
		//栏目
		$showlanmu = array();
		$showlanmu[0] = array('flag'=>0, 'show'=>'', 'name'=>'用户类');
		$showlanmu[1] = array('flag'=>1, 'show'=>'count1', 'name'=>'设备激活');
		$showlanmu[2] = array('flag'=>1, 'show'=>'count2', 'name'=>'注册账户');
		$showlanmu[3] = array('flag'=>1, 'show'=>'count3', 'name'=>'次日留存率(%)');
		$showlanmu[4] = array('flag'=>1, 'show'=>'count4', 'name'=>'7日留存率(%)');
		$showlanmu[5] = array('flag'=>1, 'show'=>'count5', 'name'=>'DAU');
		$showlanmu[6] = array('flag'=>1, 'show'=>'count6', 'name'=>'DAU(老玩家)');
		$showlanmu[7] = array('flag'=>1, 'show'=>'count7', 'name'=>'平均在线');
		$showlanmu[8] = array('flag'=>1, 'show'=>'count8', 'name'=>'峰值在线');
		$showlanmu[9] = array('flag'=>0, 'show'=>'', 'name'=>'收入类');
		$showlanmu[10] = array('flag'=>1, 'show'=>'shou1', 'name'=>'日收入(¥)');
		$showlanmu[11] = array('flag'=>1, 'show'=>'shou2', 'name'=>'付费玩家数');
		$showlanmu[12] = array('flag'=>1, 'show'=>'shou3', 'name'=>'日付费率(%)');
		$showlanmu[13] = array('flag'=>1, 'show'=>'shou6', 'name'=>'新增付费玩家');
		$showlanmu[14] = array('flag'=>1, 'show'=>'shou7', 'name'=>'新增玩家付费率(%)');
		$showlanmu[15] = array('flag'=>1, 'show'=>'shou8', 'name'=>'老玩家付费人数');
		$showlanmu[16] = array('flag'=>1, 'show'=>'shou9', 'name'=>'老玩家充值收入(元)');
		$showlanmu[17] = array('flag'=>1, 'show'=>'shou4', 'name'=>'ARPU(¥)');
		$showlanmu[18] = array('flag'=>1,'show'=>'shou5', 'name'=>'ARPPU(¥)');
		$showlanmu[19] = array('flag'=>0, 'show'=>'', 'name'=>'总量类');
		$showlanmu[20] = array('flag'=>1, 'show'=>'zong1', 'name'=>'累计激活');
		$showlanmu[21] = array('flag'=>1, 'show'=>'zong2', 'name'=>'累计账户');
		$showlanmu[22] = array('flag'=>1, 'show'=>'zong3', 'name'=>'累计收入(¥)');
		$showlanmu[23] = array('flag'=>1, 'show'=>'zong4', 'name'=>'总体付费率(%)');
		$showlanmu[24] = array('flag'=>1, 'show'=>'zong5', 'name'=>'总付费用户数');
			  
		$sql1 = " and !((user_id>=".ROBERT1_BEGIN." and user_id<".ROBERT1_END.") or (user_id>=".ROBERT2_BEGIN." and user_id<=".ROBERT2_END."))";
		//运营日报
		$tongji1 = array();
		foreach ($tongji_day as $key => $val){
			$time1 = strtotime($val);
			$time2 = $time1 + 60 * 60 * 24;
			$date1 = date("Y-m-d", $time1);
			$date2 = date("Y-m-d", $time2);
			//echo $date1."<br>";
			$flag = 1;
			$total = $row->where("data='$date1' and flag=$flag")->count();
			
			if ($total == 0){
				$table_user_base = M("fx_user_base", '', DB_CONFIG1);
				$info = $table_user_base->where("data='".$date1."'")->find();
				
				//用户类
				//设备激活
				$count1 = $info['user_add'];
				//if ($date1=="2015-12-30") {dump($row1->_sql()); echo $count1;}
				//注册账户
				$count2 = $info['user_add'];
				//if ($date1=="2015-11-03") dump($row1->_sql());
				$count11 = 0;
				$count12 = 0;
				//次日留存率(%)
				$count3 = $info['liucun1'];
				//7日留存率(%)
				$count4 = $info['liucun3'];

				//当日DAU
				$count5 = $info['dau'];
				//当日DAU[老玩家]
				$count6 = $info['dau_old'];
				//当日平均在线
				$count7 = $info['online1'];
				//当日峰值在线
				$count8 = $info['online2'];
				//平均游戏局数
				//$count9 = 0;
				//平均游戏时长
				//$count10 = 0;
				
				//收入类
				//日收入 (¥)
				$shou1 = $info['user_all_pay_money'];
				//付费玩家数
				$shou2 = $info['user_all_pay_num'];
				//if ($date1=="2015-12-30") dump($row3->_sql());
				//当日活跃玩家
				$shou30 = $count5+$count6;
				//日付费率(%)
				$shou3 = ($shou30==0) ? 0 : round($shou2/$shou30,3) * 100;
				//ARPU (¥)
				$shou4 = $info['arpu'];
				//ARPPU (¥)
				$shou5 = $info['arppu'];
				//新增付费玩家
				$shou6 = $info['user_pay_num'];
				//新增玩家付费率(%)
				$shou7 = $info['user_pay_lv'];
				//echo $shou1."**".$shou2."<br>";
				//老玩家付费人数
				$shou8 = $shou2 - $info['user_pay_num'];
				//老玩家充值收入(元)
				$shou9 = $shou1 - $info['user_pay_money'];
				
				//总量类
				//累计激活
				$zong1 = $info['zong1'];
				//累计账户
				$zong2 = $info['zong2'];
				//累计收入(¥)
				$zong3 = $info['zong3'];
				//付费玩家数
				$zong5 = $info['zong5'];
				//总体付费率(%)
				$zong4 = ($zong2==0) ? 0 : round($zong5/$zong2,3) * 100;
				
				$tongji = array('data' => $date1,
								'count1' => $count1,
								'count2' => $count2,
								'count3' => $count3,
								'count4' => $count4,
								'count5' => $count5,
								'count6' => $count6,
								'count7' => $count7,
								'count8' => $count8,
								'count9' => $count9,
								'count10' => $count10,
								'shou1' => $shou1,
								'shou2' => $shou2,
								'shou3' => $shou3,
								'shou4' => $shou4,
								'shou5' => $shou5,
								'shou6' => $shou6,
								'shou7' => $shou7,
								'shou8' => $shou8,
								'shou9' => $shou9,
								'zong1' => $zong1,
								'zong2' => $zong2,
								'zong3' => $zong3,
								'zong4' => $zong4,
								'zong5' => $zong5);
				//echo $date1."**".$yesday."<br>";
				//echo $date1."**".$day1."**".$day7."<br>";
				if ($date1<$day7){
					$data9 = array('data' => $date1,
							   'channel' => empty($channel) ? "all" : $channel,
							   'flag' => $flag,
							   'tongji' => json_encode($tongji),
							   'addtime' => time());
					$result = $row->add($data9);
				}
					
				$tongji1[$key] = $tongji;
			}else{
				$info = $row->where("data='$date1' and flag=$flag")->find();
				$tongji1[$key] = json_decode($info['tongji'], true);
			}
		}
		//print_r($tongji1); //print_r($tongji3);
		//$this->assign('tongji1',$tongji1);
		
		foreach($showlanmu as $key => $val){
			$showlanmu[$key]['day'] = array();
			foreach($tongji1 as $key2 => $val2){
				if ($val2['data'] == $beginTime){
					$t1 = $val2[$val['show']];
					$showlanmu[$key]['today'] = $t1;
				}elseif ($val2['data'] == $hbr){
					$t2 = $val2[$val['show']];
				}elseif ($val2['data'] == $tbz){
					$t3 = $val2[$val['show']];
				}
				$showlanmu[$key]['day'][] = array('date' => $val2['data'],
												  'show' => $val2[$val['show']]);
			}
			
			//总量类不统计
			if ($key < 19){
				if (empty($t2)){
					if (empty($t1)){
						$showlanmu[$key]['hbr'] = 0;
						$showlanmu[$key]['hbr_flag'] = 0; //1增长2减少
					}else{
						$showlanmu[$key]['hbr'] = 100;
						$showlanmu[$key]['hbr_flag'] = 1; //1增长2减少
					}
				}else{
					$t0 = $t1 - $t2;
					$showlanmu[$key]['hbr_flag'] = ($t0 > 0) ? 1 : 2;
					$showlanmu[$key]['hbr'] = round(abs($t0) / $t2, 3) * 100;
				}
				
				if (empty($t3)){
					if (empty($t1)){
						$showlanmu[$key]['tbz'] = 0;
						$showlanmu[$key]['tbz_flag'] = 0; //1增长2减少
					}else{
						$showlanmu[$key]['tbz'] = 100;
						$showlanmu[$key]['tbz_flag'] = 1; //1增长2减少
					}
				}else{
					$t0 = $t1 - $t3;
					$showlanmu[$key]['tbz_flag'] = ($t0 > 0) ? 1 : 2;
					$showlanmu[$key]['tbz'] = round(abs($t0) / $t3, 3) * 100;
				}
			}else{
				$showlanmu[$key]['hbr'] = '';
				$showlanmu[$key]['hbr_flag'] = '9'; 
				$showlanmu[$key]['tbz'] = '';
				$showlanmu[$key]['tbz_flag'] = '9'; 
			}
			
		}
		//print_r($showlanmu);
		$this->assign('showlanmu',$showlanmu);	
		
	
		/*引入GoogChart类*/
		import("ORG.Util.GoogChart");
		$chart = new GoogChart();	
		
		$this->assign('left_css',"36");
		$this->assign('list',$tongji_show);
		$this->assign('totalall',$totalall);
		
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji1";
		$this->display($lib_display);
	}
	
	//日报表
	public function gaichannel(){
		$table = "fx_gai_tongji1";
		$row = M($table);
		$table3 = "pay_now_config.zjh_order";
		$row3 = M($table3);
		$table1 = "user_info";
		$row1 = M($table1, '', DB_CONFIG2);
		//$table2 = "login_log";
		
		
		$beginTime = I("beginTime");
		$endTime = I("endTime");
		$channel = I("channel");
		
		//查询不能大于当天
		$timenow = strtotime(date("Y-m-d"));
		$day1 = date("Y-m-d", strtotime("-1 day"));
		$day7 = date("Y-m-d", strtotime("-8 day"));
		//echo $beginTime."**".date("Y-m-d")."<br>".strtotime($beginTime)."**".$timenow; exit;
		if (strtotime($beginTime)>$timenow){
			$this->error('开始日期不能大于当日');
			exit;
		}
		
		if (empty($beginTime)){
			$beginTime = date("Y-m-d", strtotime("-1 day"));
		}
		//echo $day_jian;
		//$date11 .= " 00:00:00"; 
		//$date12 .= " 23:59:59"; 
		$tongji_day = array($beginTime);
		$this->assign('beginTime',$beginTime);
		$this->assign('date12',$date12);
		$this->assign('channel',$channel);
		
		$day_jian = 7;
		//环比上日
		$hbr_time = strtotime($beginTime) - 60 * 60 * 24;
		$hbr = date("Y-m-d", $hbr_time);
		//同比上周
		$tbz_time = strtotime($beginTime) - 60 * 60 * 24 * $day_jian;
		$tbz = date("Y-m-d", $tbz_time);
		$this->assign('hbr',$hbr);
		$this->assign('tbz',$tbz);
		$zrq = array();
		for($i=0; $i<$day_jian; $i++){
			$time_temp = strtotime($beginTime) - 60 * 60 * 24 * ($i + 1);
			$zrq[] = array('date' => date("Y-m-d", $time_temp));
			$tongji_day[$i+1] = date("Y-m-d", $time_temp);
		}
		$this->assign('zrq',$zrq);
		
  
		$sql1 = " and !((user_id>=".ROBERT1_BEGIN." and user_id<".ROBERT1_END.") or (user_id>=".ROBERT2_BEGIN." and user_id<=".ROBERT2_END."))";
		
		//渠道日报
		$tongji2 = array();
		$flag = 2;
		
		$res0 = $row->where("data='$beginTime' and flag=$flag")->select();
		$channel_now = "";
		foreach($res0 as $key0 => $val1){
			$tongji2[] = json_decode($val1['tongji'], true);
			$channel_now .= empty($channel_now) ? $val1['channel'] : ",".$val1['channel'];
		}
		if (!empty($channel_now)) $channel_now = " and channel not in ($channel_now)";
		//echo $channel_now."*********************"; exit;
		$res1 = $row1->field('channel')->group('channel')->where('channel!=0'.$channel_now)->limit(10)->select();
		foreach($res1 as $key1 => $val1){
				
			$total = $row->where("data='$beginTime' and flag=$flag and channel=".$val1['channel'])->count();
			if ($total == 0){
				
				//当日
				$time11 = strtotime($beginTime);
				$time12 = $time11 + 60 * 60 * 24;
				$date11 = date("Y-m-d", $time11);
				$date12 = date("Y-m-d", $time12);
				//环比上日
				$time21 = strtotime($hbr);
				$time22 = $time21 + 60 * 60 * 24;
				$date21 = date("Y-m-d", $time21);
				$date22 = date("Y-m-d", $time22);
				//同比上周
				$time31 = strtotime($tbz);
				$time32 = $time31 + 60 * 60 * 24;
				$date31 = date("Y-m-d", $time31);
				$date32 = date("Y-m-d", $time32);
				
				//$res11 = $row1->field('user_id')->where("register_date>='$date11' and register_date<'$date12' $sql1 and channel=".$val1['channel'])->select();
				$res11 = $row1->field('user_id')->where("register_date<'$date12' $sql1 and channel=".$val1['channel'])->select();
				//dump($row1->_sql()); exit;
				$user_channel = "";
				if (!empty($res11)){
					foreach($res11 as $key11 => $val11){
						$user_channel .= (empty($user_channel)) ? $val11['user_id'] : ",".$val11['user_id'];
					}
				}
				if (!empty($user_channel)) $user_channel = " and user_id in ($user_channel)";
				
				//设备激活
				$c11 = $row1->where("register_date>='$date11' and register_date<'$date12' $sql1 and channel=".$val1['channel'])->count('distinct imei');
				$c21 = $row1->where("register_date>='$date21' and register_date<'$date22' $sql1 and channel=".$val1['channel'])->count('distinct imei');
				$c31 = $row1->where("register_date>='$date31' and register_date<'$date32' $sql1 and channel=".$val1['channel'])->count('distinct imei');
				if (empty($c21)){
					if (empty($c11)){
						$hbr1 = 0;
						$hbr_flag1 = 0; //1增长2减少
					}else{
						$hbr1 = 100;
						$hbr_flag1 = 1; //1增长2减少
					}
				}else{
					$t0 = $c11 - $c21;
					$hbr_flag1 = ($t0 > 0) ? 1 : 2;
					$hbr1 = round(abs($t0) / $c21, 3) * 100;
				}
				if (empty($c31)){
					if (empty($c11)){
						$tbz1 = 0;
						$tbz_flag1 = 0; //1增长2减少
					}else{
						$tbz1 = 100;
						$tbz_flag1 = 1; //1增长2减少
					}
				}else{
					$t0 = $c11 - $c31;
					$tbz_flag1 = ($t0 > 0) ? 1 : 2;
					$tbz1 = round(abs($t0) / $c31, 3) * 100;
				}
				//新增用户
				$c12 = $row1->where("register_date>='$date11' and register_date<'$date12' $sql1 and channel=".$val1['channel'])->count('user_id');
				$c22 = $row1->where("register_date>='$date21' and register_date<'$date22' $sql1 and channel=".$val1['channel'])->count('user_id');
				$c32 = $row1->where("register_date>='$date31' and register_date<'$date32' $sql1 and channel=".$val1['channel'])->count('user_id');
				if (empty($c22)){
					if (empty($c12)){
						$hbr2 = 0;
						$hbr_flag2 = 0; //1增长2减少
					}else{
						$hbr2 = 100;
						$hbr_flag2 = 1; //1增长2减少
					}
				}else{
					$t0 = $c12 - $c22;
					$hbr_flag2 = ($t0 > 0) ? 1 : 2;
					$hbr2 = round(abs($t0) / $c22, 3) * 100;
				}
				if (empty($c32)){
					if (empty($c12)){
						$tbz2 = 0;
						$tbz_flag2 = 0; //1增长2减少
					}else{
						$tbz2 = 100;
						$tbz_flag2 = 1; //1增长2减少
					}
				}else{
					$t0 = $c12 - $c32;
					$tbz_flag2 = ($t0 > 0) ? 1 : 2;
					$tbz2 = round(abs($t0) / $c32, 3) * 100;
				}
				//收入
				if (empty($user_channel)){
					$s1 = 0;
					$s2 = 0;
					$s3 = 0;
				}else{
					$s1 = $row3->where("order_create_time>='$time11' and order_create_time<'$time12' and payment_status in ('1','-2') $user_channel")->sum('result_money');
					//dump($row3->_sql());
					if (empty($s1)) $s1 = 0; else $s1 = $s1/100;
					$s2 = $row3->where("order_create_time>='$time21' and order_create_time<'$time22' and payment_status in ('1','-2') $user_channel")->sum('result_money');
					if (empty($s2)) $s2 = 0; else $s2 = $s2/100;
					$s3 = $row3->where("order_create_time>='$time31' and order_create_time<'$time32' and payment_status in ('1','-2') $user_channel")->sum('result_money');
					if (empty($s3)) $s3 = 0; else $s3 = $s3/100;
				}
				
				if (empty($s2)){
					if (empty($s1)){
						$hbr3 = 0;
						$hbr_flag3 = 0; //1增长2减少
					}else{
						$hbr3 = 100;
						$hbr_flag3 = 1; //1增长2减少
					}
				}else{
					$t0 = $s1 - $s2;
					$hbr_flag3 = ($t0 > 0) ? 1 : 2;
					$hbr3 = round(abs($t0) / $s2, 3) * 100;
				}
				if (empty($s3)){
					if (empty($s1)){
						$tbz3 = 0;
						$tbz_flag3 = 0; //1增长2减少
					}else{
						$tbz3 = 100;
						$tbz_flag3 = 1; //1增长2减少
					}
				}else{
					$t0 = $s1 - $s3;
					$tbz_flag3 = ($t0 > 0) ? 1 : 2;
					$tbz3 = round(abs($t0) / $s3, 3) * 100;
				}
				
				
				
				$tongji =  array('channel' => $val1['channel'],
								   'c11' => $c11,
								   'hbr1' => $hbr1,
								   'hbr_flag1' => $hbr_flag1,
								   'tbz1' => $tbz1,
								   'tbz_flag1' => $tbz_flag1,
								   'c12' => $c12,
								   'hbr2' => $hbr2,
								   'hbr_flag2' => $hbr_flag2,
								   'tbz2' => $tbz2,
								   'tbz_flag2' => $tbz_flag2,
								   's1' => $s1,
								   'hbr3' => $hbr3,
								   'hbr_flag3' => $hbr_flag3,
								   'tbz3' => $tbz3,
								   'tbz_flag3' => $tbz_flag3);
				
				if ($date11 < date("Y-m-d")){
					$data9 = array('data' => $date11,
								   'channel' => $val1['channel'],
								   'flag' => $flag,
								   'tongji' => json_encode($tongji),
								   'addtime' => time());
					$result = $row->add($data9);
				}	
				
				$tongji2[] = $tongji;
				
			}
		}	
		
		$this->assign('tongji2',$tongji2);
		
		/*引入GoogChart类*/
		import("ORG.Util.GoogChart");
		$chart = new GoogChart();	
		
		$this->assign('left_css',"36");
		$this->assign('list',$tongji_show);
		$this->assign('totalall',$totalall);
		
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji2";
		$this->display($lib_display);
	}
	
	//变动统计页面
	public function goldchange(){
		
		$type = I("type");
		$user_id = I("user_id");
		if (empty($type)) $type = "1";
		
		$sql0 = "";
		if (!empty($user_id)){
			$sql0 .= " and user_id=$user_id";
		}
		$this->assign('user_id',$user_id);
		
		if ($type == "1"){
			$Table = $this->Table_prifix."monitor_curtime_user_gold_change";
			$lib_display = $this->By_tpl.":goldchange1";
		}else if ($type == "2"){
			$Table = $this->Table_prifix."monitor_biggold_change_log";
			$lib_display = $this->By_tpl.":goldchange2";
		}else if ($type == "3"){
			$Table = $this->Table_prifix."monitor_record_user_gold_change";
			$lib_display = $this->By_tpl.":goldchange3";
		}
		
		$rowlist = M($Table, '', DB_CONFIG2);
		import('ORG.Util.Page');
		$count = $rowlist->where("1 $sql0")->count();
		$Page       = new Page($count,20);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		
		$list = $rowlist->where("1 $sql0")->order('changetime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($rowlist->_sql());
		$sort1 = array();
		$sort2 = array();
		$sort3 = array();
		$sort4 = array();
		
		foreach($list as $key=>$val){
			if ($type == "2"){
				switch($val['module']){
					case "1": $list[$key]['moduleshow'] = "注册账号"; break; 
					case "2": $list[$key]['moduleshow'] = "每日登入"; break; 
					case "3": $list[$key]['moduleshow'] = "游戏"; break; 
					case "4": $list[$key]['moduleshow'] = "破产"; break; 
					case "5": $list[$key]['moduleshow'] = "充值"; break; 
					case "6": $list[$key]['moduleshow'] = "在线宝箱"; break; 
					case "7": $list[$key]['moduleshow'] = "任务赠送"; break; 
					case "8": $list[$key]['moduleshow'] = "AI申请"; break; 
					case "9": $list[$key]['moduleshow'] = "后台操作"; break; 
					case "10": $list[$key]['moduleshow'] = "大喇叭"; break;
					case "11": $list[$key]['moduleshow'] = "道具发送"; break; 
					case "12": $list[$key]['moduleshow'] = "机器人金币变动"; break; 
					case "13": $list[$key]['moduleshow'] = "赠送礼物"; break; 
					case "14": $list[$key]['moduleshow'] = "变卖礼物"; break; 
					case "15": $list[$key]['moduleshow'] = "老虎机买"; break; 
					case "16": $list[$key]['moduleshow'] = "绑定账号"; break; 
					case "17": $list[$key]['moduleshow'] = "T人金币数"; break; 
					case "18": $list[$key]['moduleshow'] = "时时彩"; break; 
					case "19": $list[$key]['moduleshow'] = "转账"; break; 
					case "20": $list[$key]['moduleshow'] = "抽奖金币变动日志"; break;
					case "21": $list[$key]['moduleshow'] = "存取款日志"; break; 
					case "22": $list[$key]['moduleshow'] = "创建私人房"; break; 
					case "23": $list[$key]['moduleshow'] = "钻石兑换金币"; break; 
					case "24": $list[$key]['moduleshow'] = "用户推广奖励"; break;
					default:  $list[$key]['moduleshow'] = "未知"; break;
				}
			}
			$list[$key]['changetime'] = date("Y-m-d H:i:s", $val['changetime']);
			
			if ($type == "1"){
				$sort1[$key] = $val['beforegold'];
				$sort2[$key] = $val['curgold'];
				$sort3[$key] = $val['changetime'];
			}else if ($type == "2"){
				$sort1[$key] = $val['changegold'];
				$sort3[$key] = $val['changetime'];
			}else if ($type == "3"){
				$sort1[$key] = $val['goldchange'];
				$sort3[$key] = $val['changetime'];
			}
		}
		
		$sortscate = I("sortscate");
		$sortsflag = I("sortsflag");
		if ($sortscate=="1"){
			if ($sortsflag=="1"){
				array_multisort($sort1, SORT_ASC, $list);
			}else{
				array_multisort($sort1, SORT_DESC, $list);
			}
		}else if ($sortscate=="2"){
			if ($sortsflag=="1"){
				array_multisort($sort2, SORT_ASC, $list);
			}else{
				array_multisort($sort2, SORT_DESC, $list);
			}
		}else if ($sortscate=="3"){
			if ($sortsflag=="1"){
				array_multisort($sort3, SORT_ASC, $list);
			}else{
				array_multisort($sort3, SORT_DESC, $list);
			}
		}
		
		//增加操作记录
		$logs = C('SHANG_MSG_RECORD');
		$remark = "";
		adminlog($logs, $remark);
		
		$this->assign('left_css',"7");
		$this->assign('list',$list);
		$this->assign('pageshow',$show);
		
		$this->assign('By_tpl',$this->By_tpl);
		
		$this->display($lib_display);
	}
	
	//周报表
	public function week(){
		
		//栏目
		$showlanmu = array();
		$showlanmu[0] = array('flag'=>1, 'show'=>'count0', 'name'=>'用户总量');
		$showlanmu[1] = array('flag'=>1, 'show'=>'count1', 'name'=>'周活跃用户');
		$showlanmu[2] = array('flag'=>1, 'show'=>'count2', 'name'=>'周新增');
		$showlanmu[3] = array('flag'=>1, 'show'=>'count3', 'name'=>'周有效新增');
		$showlanmu[4] = array('flag'=>1, 'show'=>'count4', 'name'=>'周有效率');
		$showlanmu[5] = array('flag'=>1, 'show'=>'count5', 'name'=>'日均新增');
		$showlanmu[6] = array('flag'=>1, 'show'=>'count6', 'name'=>'日均活跃');
		$showlanmu[7] = array('flag'=>1, 'show'=>'count7', 'name'=>'日均次日留存');
		$showlanmu[8] = array('flag'=>1, 'show'=>'count8', 'name'=>'日均三日留存');
		$showlanmu[9] = array('flag'=>1, 'show'=>'count9', 'name'=>'日均活跃');
		$showlanmu[10] = array('flag'=>1, 'show'=>'count10', 'name'=>'周充值总额');
		$showlanmu[11] = array('flag'=>1, 'show'=>'count11', 'name'=>'周付费人数');
		$showlanmu[12] = array('flag'=>1, 'show'=>'count12', 'name'=>'周付费次数');
		$showlanmu[13] = array('flag'=>1, 'show'=>'count13', 'name'=>'周付费率');
		$showlanmu[14] = array('flag'=>1, 'show'=>'count14', 'name'=>'周新增付费金额');
		$showlanmu[15] = array('flag'=>1, 'show'=>'count15', 'name'=>'周新增付费人数');
		$showlanmu[16] = array('flag'=>1, 'show'=>'count16', 'name'=>'周新增付费率');
		$showlanmu[17] = array('flag'=>1, 'show'=>'count17', 'name'=>'ARPU(¥)');
		$showlanmu[18] = array('flag'=>1, 'show'=>'count18', 'name'=>'ARPPU(¥)');
        
		$sql1 = " and !((user_id>=".ROBERT1_BEGIN." and user_id<".ROBERT1_END.") or (user_id>=".ROBERT2_BEGIN." and user_id<=".ROBERT2_END."))";
		//本周
		$week = date("w");
		if ($week == "1"){
			$jiange = 0;
			$date1 = date("Y-m-d");
			$date2 = date("Y-m-d");
			$time1 = strtotime($date1);
			$time2 = strtotime($date2);
		}else{
			$jiange = ($week=="0") ? 6 : $week - 1;
			$date1 = date("Y-m-d",strtotime("-$jiange day"));
			$date2 = date("Y-m-d");
			$time1 = strtotime($date1);
			$time2 = strtotime($date2);
		}
		//用户总量
		$row1 = M("user_info");
		$count1 = $row1->where("1 $sql1")->count('user_id');
		//周活跃用户
		$row2 = M("login_log");
		$count2 = $row2->where("login_date>='$date1' and login_date<'$date2 23:59:59' $sql1")->count('distinct user_id');
		//周新增
		$count3 = $row1->where("register_date>='$date1' and register_date<'$date2 23:59:59' $sql1")->count('user_id');
		//周有效新增
		for ($i=0; $i<=$jiange; $i++){
			
		}
		$table1 = "log_game_record_log_".date("Ym", $time1);
		$row3 = M($table1);
		$user_add_ok = 0;
		$res = $row1->where("register_date>='$date1' and register_date<'$date2 23:59:59' $sql1")->select();
		$count3 = 0;
		$sql4 = "";
		foreach($res as $key => $row){
			$count3++;
			$sql4 .= (empty($sql4)) ? $row['user_id'] : ",".$row['user_id'];
			
			//有效新增用户
			$user_add_count = $row3->where("curtime>='$time1' and curtime<'$time2' and user_id='".$row['user_id']."'")->count('id');
			if ($user_add_count > 3){
				$user_add_ok++;
			}
		}
		if (!empty($sql4)) $sql4 = " and user_id in ($sql4)";
		//有效率
		$user_ok_lv = (empty($count3)) ? 0 : round($user_add_ok/$count3,2);
		
		
		//前6周
		for($i=1; $i<=6; $i++){
			$time4 = strtotime($date1) -  24 * 60 * 60 - 24 * 60 * 60 * 7 * ($i -1);
			$time3 = strtotime($date1) -  24 * 60 * 60 * 7 - 24 * 60 * 60 * 7 * ($i -1);
			$date3 = date("Y-m-d", $time3);
			$date4 = date("Y-m-d", $time4);
			echo $date3."**".$date4."<br>";
		}
		
		$this->assign('left_css',"36");
		$this->assign('list',$tongji_show);
		$this->assign('totalall',$totalall);
		
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/week";
		$this->display($lib_display);
	}

    public function moneytotal(){
        $table = "money_stats";
        $row = M($table, '', DB_CONFIG3);

        $beginTime = I("beginTime");

        //默认查询当天
        if (empty($beginTime)){
            $beginTime = date("Y-m-d");
        }

        //查询不能大于当天
        if ($beginTime > date("Y-m-d")){
            $this->error('查询日期不能大于当天');
            exit;
        }
        $this->assign('date11',$beginTime);

        //获取数据
		$time1 = strtotime($beginTime);
		$time2 = $time1 + 60 * 60 * 24;
		$res1 = $row->where("stats_time>=".$time1." and stats_time<".$time2)->order("stats_time")->select();
         $data1 = array();
        $i = 0;
        foreach ($res1 as $key1 => $val1){
            $data1[0] .= ($data1[0]=="") ? "'".date("H:i:s", $val1['stats_time'])."'" : ",'".date("H:i:s", $val1['stats_time'])."'";
            $data1[1] .= ($data1[1]=="") ? $val1['chips_total'] : ",".$val1['chips_total'];
            $data1[2] .= ($data1[2]=="") ? $val1['bank_total'] : ",".$val1['bank_total'];
            $data1[3] .= ($data1[3]=="") ? $val1['diamonds_total'] : ",".$val1['diamonds_total'];
            $data1[4] .= ($data1[4]=="") ? $val1['tickets_total'] : ",".$val1['tickets_total'];
            $data1[5] .= ($data1[5]=="") ? $val1['gifts_villa_total'] : ",".$val1['gifts_villa_total'];
            $data1[6] .= ($data1[6]=="") ? $val1['gifts_yacht_total'] : ",".$val1['gifts_yacht_total'];
            $data1[7] .= ($data1[7]=="") ? $val1['gifts_car_total'] : ",".$val1['gifts_car_total'];

            $res1[$key1]['data'] = date("Y-m-d H:i:s", $val1['stats_time']);
            $res1[$key1]['show_gold'] = number_format($val1['chips_total']);
            $res1[$key1]['show_bank'] = number_format($val1['bank_total']);
            $i++;
        }

        $this->assign('tongji1',json_encode($res1));
        $this->assign('data1',$data1);
        $this->assign('pagesize',$i);

        $this->assign('left_css',"36");
        $this->assign('By_tpl',$this->By_tpl);
        $lib_display = $this->By_tpl."/moneytotal";
        $this->display($lib_display);
    }

    public function userincr(){

        $beginTime = I("beginTime");
        $user_id = I("user_id");
        $sortscate = I("sortscate");
        $sortsflag = I("sortsflag");

        //默认查询当天
        if (empty($beginTime)){
            $beginTime = date("Y-m-d");
        }

        //查询不能大于当天
        if ($beginTime > date("Y-m-d")){
            $this->error('查询日期不能大于当天');
            exit;
        }
        $this->assign('date11',$beginTime);
        $this->assign('user_id',$user_id);

        $tempdate = str_replace("-", "_", $beginTime);
        $table = "user_info_diff_".$tempdate;
        $row = M($table, '', DB_CONFIG3);

        $order = "";
        if ($sortscate=="1"){
            $order .= "gold_incr";
        }else if ($sortscate=="2"){
            $order .= "deposit_incr";
        }else if ($sortscate=="3"){
            $order .= "diamond_incr";
        }else if ($sortscate=="4"){
            $order .= "ticket_incr";
        }else if ($sortscate=="5"){
            $order .= "car_incr";
        }else if ($sortscate=="6"){
            $order .= "villa_incr";
        }else if ($sortscate=="7"){
            $order .= "yacht_incr";
        }else if ($sortscate=="8"){
            $order .= "paycount_incr";
        }else{
            $order .= "gold_incr";
        }
        if ($sortsflag=="1"){
            $order .= " ASC";
        }else{
            $order .= " DESC";
        }

        if (!empty($user_id)) $where = "user_id=".$user_id; else $where = "1";

        //获取数据
        import('ORG.Util.Page');
        $count = $row->where($where)->count('user_id');
        //dump($row->_sql());
        $Page       = new Page($count,20);//实例化分页类传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        $list = $row->where($where)->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach($list as $key=>$value){
            $list[$key]['id'] = $Page->firstRow + $key;
            $list[$key]['show_gold'] = number_format($value['gold_incr']);
            $list[$key]['show_bank'] = number_format($value['deposit_incr']);
        }
        $this->assign('list',$list);
        $this->assign('pageshow',$show);

        $this->assign('left_css',"36");
        $this->assign('By_tpl',$this->By_tpl);
        $lib_display = $this->By_tpl."/userincr";
        $this->display($lib_display);
    }
	
	public function errordata(){

        $beginTime = I("beginTime");
		$endTime = I("endTime");
        $user_id = I("user_id");
        
		//查某天数据
		if (!empty($beginTime)){
			$date12 = date("Y-m-d", strtotime($beginTime));
		}else{
			$date12 = date("Y-m-d");
		}
		$this->assign('date11',$date11);
		$this->assign('date12',$date12);
        $this->assign('user_id',$user_id);
		
			$time1 = strtotime($date12);
			$time2 = $time1 + 86400;
			
			
			if ($date12 < date("Y-m-d")){
				$table = "log_gold_".date("Ymd", $time1);
				$model_gold = M($table, '', DB_CONFIG3);
				//判断是否有记录
				$model_error_data = M('fx_error_data', '', DB_CONFIG1);
				$count = $model_error_data->where("data='$date12'")->count();
				if ($count > 0){
					$info = $model_error_data->where("data='$date12'")->find();
					$arr = json_decode($info['tongji'], true);
					$j = count($arr);
				}else{
					$where = "(aftergold - beforegold != changegold) and (module!=15)";
					if (!empty($user_id)) $where .= " and user_id=".$user_id; 
					$row = $model_gold->where($where)->select();
					//dump($model_gold->_sql());
					$j = 0;
					foreach($row as $key => $val){
						switch ($val['module']){
							case '1':  $module = "注册账号"; break;
							case '2':  $module = "每日登入"; break;
							case '3':  $module = "游戏"; break;
							case '4':  $module = "破产"; break;
							case '5':  $module = "充值"; break;
							case '6':  $module = "在线宝箱"; break;
							case '7':  $module = "任务赠送"; break;
							case '8':  $module = "AI申请"; break;
							case '9':  $module = "后台操作"; break;
							case '10': $module = "大喇叭"; break;
							case '11': $module = "道具发送"; break;
							case '12': $module = "机器人金币变动"; break;
							case '13': $module = "赠送礼物"; break;
							case '14': $module = "变卖礼物"; break;
							case '15': $module = "老虎机买"; break;
							case '16': $module = "绑定账号"; break;
							case '17': $module = "T人金币数"; break;
							case '18': $module = "时时彩"; break;
							case '19': $module = "转账"; break;
							case '20': $module = "抽奖金币变动日志"; break;
							case '21': $module = "存取款日志"; break;
							case '22': $module = "创建私人房"; break;
							case '23': $module = "钻石兑换金币"; break;
							case '24': $module = "用户推广奖励"; break;
							case '25': $module = "百人场下注/结算"; break;
							case '26': $module = "金币兑换钻石"; break;
							case '27': $module = "奖券兑换金币"; break;
							default  : $module = "其它"; break;
						}
						switch ($val['roomid']){
							case '1':  $room = "初级房"; break;
							case '2':  $room = "中级房"; break;
							case '3':  $room = "高级房"; break;
							case '4':  $room = "私人房"; break;
							case '5':  $room = "娱乐房"; break;
							case '6':  $room = "百人场"; break;
							case '7':  $room = "土豪房"; break;
							default  : $room = "其它"; break;
						}
						$arr[$j] = array('id' => $val['id'],
										 'user_id' => $val['user_id'],
										 'date' => $val['date'],
										 'beforegold' => $val['beforegold'],
										 'aftergold' => $val['aftergold'],
										 'changegold' => $val['changegold'],
										 'roomid' => $val['roomid'],
										 'room' => $room,
										 'module' => $module,
										 'memo' => $val['memo']);
						$j++;
					}
					
					$data9 = array('data' => $date12,
								   'tongji' => json_encode($arr),
								   'addtime' => time());
					$result = $model_error_data->add($data9);		
				}
			}else{
				
				$table = "log_gold_change_log_".date("Ym");
				$model_gold = M($table, '', DB_CONFIG2);
				
					$where = "(aftergold - beforegold != changegold) and (module!=15) and curtime>=".$time1." and curtime<".$time2;
					if (!empty($user_id)) $where .= " and user_id=".$user_id; 
					$row = $model_gold->where($where)->select();
					//dump($model_gold->_sql());
					$j = 0;
					foreach($row as $key => $val){
						switch ($val['module']){
							case '1':  $module = "注册账号"; break;
							case '2':  $module = "每日登入"; break;
							case '3':  $module = "游戏"; break;
							case '4':  $module = "破产"; break;
							case '5':  $module = "充值"; break;
							case '6':  $module = "在线宝箱"; break;
							case '7':  $module = "任务赠送"; break;
							case '8':  $module = "AI申请"; break;
							case '9':  $module = "后台操作"; break;
							case '10': $module = "大喇叭"; break;
							case '11': $module = "道具发送"; break;
							case '12': $module = "机器人金币变动"; break;
							case '13': $module = "赠送礼物"; break;
							case '14': $module = "变卖礼物"; break;
							case '15': $module = "老虎机买"; break;
							case '16': $module = "绑定账号"; break;
							case '17': $module = "T人金币数"; break;
							case '18': $module = "时时彩"; break;
							case '19': $module = "转账"; break;
							case '20': $module = "抽奖金币变动日志"; break;
							case '21': $module = "存取款日志"; break;
							case '22': $module = "创建私人房"; break;
							case '23': $module = "钻石兑换金币"; break;
							case '24': $module = "用户推广奖励"; break;
							case '25': $module = "百人场下注/结算"; break;
							case '26': $module = "金币兑换钻石"; break;
							case '27': $module = "奖券兑换金币"; break;
							default  : $module = "其它"; break;
						}
						switch ($val['roomid']){
							case '1':  $room = "初级房"; break;
							case '2':  $room = "中级房"; break;
							case '3':  $room = "高级房"; break;
							case '4':  $room = "私人房"; break;
							case '5':  $room = "娱乐房"; break;
							case '6':  $room = "百人场"; break;
							case '7':  $room = "土豪房"; break;
							default  : $room = "其它"; break;
						}
						$arr[$j] = array('id' => $val['id'],
										 'user_id' => $val['user_id'],
										 'date' => $val['date'],
										 'beforegold' => $val['beforegold'],
										 'aftergold' => $val['aftergold'],
										 'changegold' => $val['changegold'],
										 'roomid' => $val['roomid'],
										 'room' => $room,
										 'module' => $module,
										 'memo' => $val['memo']);
						$j++;
					}
			}
			
			
		
		//print_r($arr); exit;
		/*
        //默认查3天的数据
		if (!empty($beginTime) && !empty($endTime)){
			$date11 = date("Y-m-d", strtotime($beginTime));
			$date12 = date("Y-m-d", strtotime($endTime));
			$day_jian = (strtotime($date12) - strtotime($date11)) / (60 * 60 * 24) + 1;
		}else{
			$date12 = date("Y-m-d");
			$day_jian = 3;
			$date11 = date("Y-m-d", (strtotime($date12) - 60 * 60 * 24 * 3));
		}
		$this->assign('date11',$date11);
		$this->assign('date12',$date12);
        $this->assign('user_id',$user_id);
		
		$arr = array();
		$j = 0;
		for ($i=0; $i<$day_jian; $i++){
			$time1 = strtotime($date12) - 86400 * $i;
			$time2 = $time1 + 86400;
			if (date("Y-m-d", $time1) < date("Y-m-d")){
				$table = "log_gold_".date("Ymd", $time1);
				$model_gold = M($table, '', DB_CONFIG3);
			}else{
				$table = "log_gold_change_log_".date("Ym");
				$model_gold = M($table, '', DB_CONFIG2);
			}
			
			$where = "(aftergold - beforegold != changegold) and (module!=15) and curtime>=".$time1." and curtime<".$time2;
			if (!empty($user_id)) $where .= " and user_id=".$user_id; 
			$row = $model_gold->where($where)->select();
            //dump($model_gold->_sql());
			foreach($row as $key => $val){
				switch ($val['module']){
					case '1':  $module = "注册账号"; break;
					case '2':  $module = "每日登入"; break;
					case '3':  $module = "游戏"; break;
					case '4':  $module = "破产"; break;
					case '5':  $module = "充值"; break;
					case '6':  $module = "在线宝箱"; break;
					case '7':  $module = "任务赠送"; break;
					case '8':  $module = "AI申请"; break;
					case '9':  $module = "后台操作"; break;
					case '10': $module = "大喇叭"; break;
					case '11': $module = "道具发送"; break;
					case '12': $module = "机器人金币变动"; break;
					case '13': $module = "赠送礼物"; break;
					case '14': $module = "变卖礼物"; break;
					case '15': $module = "老虎机买"; break;
					case '16': $module = "绑定账号"; break;
					case '17': $module = "T人金币数"; break;
					case '18': $module = "时时彩"; break;
					case '19': $module = "转账"; break;
					case '20': $module = "抽奖金币变动日志"; break;
					case '21': $module = "存取款日志"; break;
					case '22': $module = "创建私人房"; break;
					case '23': $module = "钻石兑换金币"; break;
					case '24': $module = "用户推广奖励"; break;
					case '25': $module = "百人场下注/结算"; break;
					case '26': $module = "金币兑换钻石"; break;
					case '27': $module = "奖券兑换金币"; break;
					default  : $module = "其它"; break;
				}
				$arr[$j] = array('id' => $val['id'],
								 'user_id' => $val['user_id'],
								 'date' => $val['date'],
								 'beforegold' => $val['beforegold'],
								 'aftergold' => $val['aftergold'],
								 'changegold' => $val['changegold'],
								 'module' => $module,
								 'memo' => $val['memo']);
				$j++;
			}
		}
		*/
		import('ORG.Util.Page');
		$count = $j;
		$Page = new Page($count,20);
		$show       = $Page->show();
		$list = array();
		//echo $Page->firstRow."**";
		$end_i = $Page->firstRow + $Page->listRows;
		//echo $end_i;
		for($i=$Page->firstRow; $i<=$Page->firstRow+$Page->listRows; $i++){
			if (!empty($arr[$i])) $list[$i] = $arr[$i];
		}
		//print_r($list);
		/*
		$table = "log_gold_change_log_".date("Ym");
		$model_gold = M($table, '', DB_CONFIG2);
		$where = "(aftergold - beforegold != changegold) and (module!=15) and curtime>=".strtotime($date11)." and curtime<".strtotime($date12);
        if (!empty($user_id)) $where .= " and user_id=".$user_id; 
        //获取数据
        import('ORG.Util.Page');
        $count = $model_gold->where($where)->count('user_id');
        
        $Page       = new Page($count,20);//实例化分页类传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        $list = $model_gold->where($where)->order("curtime desc")->limit($Page->firstRow.','.$Page->listRows)->select();
		*/
		//dump($model_gold->_sql());
        $this->assign('list',$list);
        $this->assign('pageshow',$show);

        $this->assign('left_css',"36");
        $this->assign('By_tpl',$this->By_tpl);
        $lib_display = $this->By_tpl."/errordata";
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