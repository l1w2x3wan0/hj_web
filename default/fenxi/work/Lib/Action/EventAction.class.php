<?php
// 运营分析文件

class EventAction extends BaseAction {

	protected $By_tpl = 'Event'; 
	
	public function tongji1(){
		$table = "fx_tongji1";
		$row = M($table);
		
		
		$beginTime = I("beginTime");
		$endTime = I("endTime");
		$channel = I("channel");
		$version = I("version");
		$showflag = I("flag");
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
			$day_jian = PAGE_SHOW;
			$date11 = date("Y-m-d", (strtotime($date12) - 60 * 60 * 24 * ($day_jian - 1)));
		}
		//echo $day_jian;
		//$date11 .= " 00:00:00"; 
		//$date12 .= " 23:59:59"; 
		import('ORG.Util.Page');
		$Page       = new Page($day_jian,PAGE_SHOW);
		$show       = $Page->show();
		
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
		$sql1 = " and !((user_id>=".ROBERT1_BEGIN." and user_id<".ROBERT1_END.") or (user_id>=".ROBERT2_BEGIN." and user_id<=".ROBERT2_END."))";
		if (!empty($channel)){
			$sql1 .= " and channel=$channel";
			$sql0 .= " and channel=$channel";
		}else{
			$sql0 .= " and channel='all'";
		}
		if (!empty($version)){
			$sql0 .= " and version='$version'";
		}else{
			$sql0 .= " and version='all'";
		}
		
		$event1 = array();
		$event1[0] = array('name' => 'tableLevel1', 'meno' => '点击初级场', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event1[1] = array('name' => 'tableLevel2', 'meno' => '点击中级场', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event1[2] = array('name' => 'tableLevel3', 'meno' => '点击高级场', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event1[3] = array('name' => 'tiger', 'meno' => '点击老虎机', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event1[4] = array('name' => 'mall', 'meno' => '点击商城图标', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event1[5] = array('name' => 'task', 'meno' => '点击任务图标', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event1[6] = array('name' => 'rank', 'meno' => '点击排名图标', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event1[7] = array('name' => 'help', 'meno' => '点击帮助图标', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event1[8] = array('name' => 'fast', 'meno' => '点击快速开始', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event1[9] = array('name' => 'firstcharge', 'meno' => '点击首充', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event1[10] = array('name' => 'mail', 'meno' => '点击邮件图标', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event1[11] = array('name' => 'setting', 'meno' => '点击设置图标', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event1[12] = array('name' => 'myinfo', 'meno' => '点击个人中心', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event1[13] = array('name' => 'horn', 'meno' => '大厅点击大喇叭', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		
		$event2 = array();
		$event2[0] = array('name' => 'goldBtn', 'meno' => '点击金币选项', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event2[1] = array('name' => 'vipBtn', 'meno' => '点击VIP选项', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event2[2] = array('name' => 'goods7', 'meno' => '点击10元首充商城', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event2[3] = array('name' => 'goods2', 'meno' => '点击6元商品', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event2[4] = array('name' => 'goods8', 'meno' => '点击10元商品', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event2[5] = array('name' => 'goods3', 'meno' => '点击30元商品', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event2[6] = array('name' => 'goods9', 'meno' => '点击50元商品', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event2[7] = array('name' => 'goods4', 'meno' => '点击100元商品', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event2[8] = array('name' => 'goods5', 'meno' => '点击300元商品', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event2[9] = array('name' => 'goods6', 'meno' => '点击500元商品', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		
		$event3 = array();
		$event3[0] = array('name' => 'myhead', 'meno' => '点击自己头像', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event3[1] = array('name' => 'otherhead', 'meno' => '点击他人头像', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event3[2] = array('name' => 'treasure', 'meno' => '点击在线宝箱', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event3[3] = array('name' => 'quickpay', 'meno' => '点击快速充值', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event3[4] = array('name' => 'ready', 'meno' => '点击准备', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event3[5] = array('name' => 'changetable', 'meno' => '点击换桌', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event3[6] = array('name' => 'allin', 'meno' => '点击全压', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);

		if ($day_jian >= 0){
			$tongji1 = array();
			$maxi = $day_jian - $Page->firstRow;
			$mini = $day_jian - $Page->firstRow - $Page->listRows;
			if ($mini < 1) $mini = 1;
			for ($i=$maxi; $i>$mini; $i--){
				
				$j = $i - 1;	
				$time1 = strtotime($date11) + 60 * 60 * 24 * ($i - 1);
				$time2 = $time1 + 60 * 60 * 24;
				$date1 = date("Y-m-d", $time1);
				$date2 = date("Y-m-d", $time2);
				
				$table0 = "game_log.log".date("Ymd", $time1);
				$row0 = M($table0);
				//echo $table0."<br>" 
				
				$event1 = array();
				$event1[0] = array('name' => 'tableLevel1', 'meno' => '点击初级场', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
				$event1[1] = array('name' => 'tableLevel2', 'meno' => '点击中级场', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
				$event1[2] = array('name' => 'tableLevel3', 'meno' => '点击高级场', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
				$event1[3] = array('name' => 'tiger', 'meno' => '点击老虎机', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
				$event1[4] = array('name' => 'mall', 'meno' => '点击商城图标', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
				$event1[5] = array('name' => 'task', 'meno' => '点击任务图标', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
				$event1[6] = array('name' => 'rank', 'meno' => '点击排名图标', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
				$event1[7] = array('name' => 'help', 'meno' => '点击帮助图标', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
				$event1[8] = array('name' => 'fast', 'meno' => '点击快速开始', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
				$event1[9] = array('name' => 'firstcharge', 'meno' => '点击首充', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
				$event1[10] = array('name' => 'mail', 'meno' => '点击邮件图标', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
				$event1[11] = array('name' => 'setting', 'meno' => '点击设置图标', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
				$event1[12] = array('name' => 'myinfo', 'meno' => '点击个人中心', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
				$event1[13] = array('name' => 'horn', 'meno' => '大厅点击大喇叭', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
				
				//$table2 = "login_log";
				//$row2 = M($table2, '', DB_CONFIG2);
				
				$flag = 3;
				$total = $row->where("data='$date1' and flag=$flag $sql0")->count();
				if ($total == 0){
					//新增用户
					$table1 = "user_info";
					$row1 = M($table1, '', DB_CONFIG2);
					$new_user_id = "";
					$res = $row1->field("user_id")->where("register_date>='$date1' and register_date<'$date2' $sql1")->select();
					foreach($res as $key => $val){
						$new_user_id[] = $val['user_id'];
					}
					//活跃用户
					$active_user_id = array();
					//$res = $row2->field("distinct user_id")->where("login_date>='$date1' and login_date<'$date2' $sql1")->select();
					//foreach($res as $key => $val){
					//	$active_user_id[] = $val['user_id'];
					//}
					
					$res = $row0->field("distinct user_id")->where("addtime>='$time1' and addtime<'$time2' and tname=1")->select();
					//dump($row1->_sql());
					foreach($res as $key => $val){
						$flag1 = (in_array($val['user_id'], $new_user_id)) ? 1 : 0;
						$flag2 = (in_array($val['user_id'], $active_user_id)) ? 1 : 0;
						foreach($event1 as $key1 => $val1){
							//统计各种事件用户数
							$count1 = $row0->where("addtime>='$time1' and addtime<'$time2' and tname=1 and user_id=".$val['user_id']." and ename='".$val1['name']."'")->count('user_id');
							if ($flag1 == 1){
								if ($count1 > 0) $event1[$key1]['num_new']++;
								$event1[$key1]['clicked_new'] += $count1;
							}else{
								if ($count1 > 0) $event1[$key1]['num_old']++;
								$event1[$key1]['clicked_old'] += $count1;
							}
							if ($flag2 == 1){
								if ($count1 > 0) $event1[$key1]['num_active']++;
								$event1[$key1]['clicked_active'] += $count1;
							}
						}
					}
					
					$tongji = array('data' => $date1,
								    'event' => $event1);
					if ($date1!=date("Y-m-d")){
						$data9 = array('data' => $date1,
									   'flag' => $flag,
									   'channel' => empty($channel) ? "all" : $channel,
									   'version' => empty($version) ? "all" : $version,
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
			//print_r($tongji1);
			
			
			//print_r($data1);
			//print_r($data2);
			//print_r($data3);
			//exit;
		} 
		//print_r($event1)
		if ($act == "exceldo"){
			
			
			if ($showflag == "idact"){
				$xlsName  = "大厅点击(活跃用户)";
			}else if ($showflag == "idnew"){
				$xlsName  = "大厅点击(新增用户)";
			}else if ($showflag == "idold"){
				$xlsName  = "大厅点击(老用户)";
			}
			
			$t = 0;
			$xlsCell  = array();
			$xlsCell[$t][0] = 'date';
			$xlsCell[$t++][1] = '日期';
			foreach($event1 as $key => $val){
				$temp1 = $val['name']."1";
				$temp2 = $val['name']."2";	
				$xlsCell[$t][0] = $temp1;
				$xlsCell[$t++][1] = $val['meno']."人数";
				$xlsCell[$t][0] = $temp2;
				$xlsCell[$t++][1] = $val['meno']."次数";
			}
			//print_r($xlsCell);
			$t = 0;
			$xlsData = array();
			foreach ($tongji1 as $k => $v)
			{
				$xlsData[$t]['date'] = $v['data'];
				foreach ($v['event'] as $k1 => $v1){
					$temp1 = $v1['name']."1";
					$temp2 = $v1['name']."2";
					if ($showflag == "idact"){
						$xlsData[$t][$temp1] = $v1['num_active'];
						$xlsData[$t][$temp2] = $v1['clicked_active'];
					}else if ($showflag == "idnew"){
						$xlsData[$t][$temp1] = $v1['num_new'];
						$xlsData[$t][$temp2] = $v1['clicked_new'];
					}else if ($showflag == "idold"){
						$xlsData[$t][$temp1] = $v1['num_old'];
						$xlsData[$t][$temp2] = $v1['clicked_old'];
					}
				}
				$t++;
			}
			//print_r($xlsData);
			exportExcel($xlsName,$xlsCell,$xlsData);
			exit;
		}
		
		//exit;
		$this->assign('pageshow',$show);
		$this->assign('tongji1',$tongji1);
		
		$this->assign('left_css',"67");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji1";
		$this->display($lib_display);
	}
	
	
	public function tongji2(){
		$table = "fx_tongji1";
		$row = M($table);
		
		//$table2 = "login_log";
		//$row2 = M($table2, '', DB_CONFIG2);
		
		$beginTime = I("beginTime");
		$endTime = I("endTime");
		$channel = I("channel");
		$version = I("version");
		$showflag = I("flag");
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
			$day_jian = PAGE_SHOW;
			$date11 = date("Y-m-d", (strtotime($date12) - 60 * 60 * 24 * ($day_jian - 1)));
		}
		//echo $day_jian;
		//$date11 .= " 00:00:00"; 
		//$date12 .= " 23:59:59"; 
		import('ORG.Util.Page');
		$Page       = new Page($day_jian,PAGE_SHOW);
		$show       = $Page->show();
		
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
		$sql1 = " and !((user_id>=".ROBERT1_BEGIN." and user_id<".ROBERT1_END.") or (user_id>=".ROBERT2_BEGIN." and user_id<=".ROBERT2_END."))";
		if (!empty($channel)){
			$sql1 .= " and channel=$channel";
			$sql0 .= " and channel=$channel";
		}else{
			$sql0 .= " and channel='all'";
		}
		if (!empty($version)){
			$sql0 .= " and version='$version'";
		}else{
			$sql0 .= " and version='all'";
		}
		
		$event1 = array();
		$event1[0] = array('name' => 'tableLevel1', 'meno' => '点击初级场', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event1[1] = array('name' => 'tableLevel2', 'meno' => '点击中级场', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event1[2] = array('name' => 'tableLevel3', 'meno' => '点击高级场', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event1[3] = array('name' => 'tiger', 'meno' => '点击老虎机', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event1[4] = array('name' => 'mall', 'meno' => '点击商城图标', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event1[5] = array('name' => 'task', 'meno' => '点击任务图标', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event1[6] = array('name' => 'rank', 'meno' => '点击排名图标', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event1[7] = array('name' => 'help', 'meno' => '点击帮助图标', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event1[8] = array('name' => 'fast', 'meno' => '点击快速开始', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event1[9] = array('name' => 'firstcharge', 'meno' => '点击首充', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event1[10] = array('name' => 'mail', 'meno' => '点击邮件图标', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event1[11] = array('name' => 'setting', 'meno' => '点击设置图标', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event1[12] = array('name' => 'myinfo', 'meno' => '点击个人中心', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event1[13] = array('name' => 'horn', 'meno' => '大厅点击大喇叭', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		
		$event2 = array();
		$event2[0] = array('name' => 'goldBtn', 'meno' => '点击金币选项', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event2[1] = array('name' => 'vipBtn', 'meno' => '点击VIP选项', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event2[2] = array('name' => 'goods7', 'meno' => '点击10元首充商城', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event2[3] = array('name' => 'goods2', 'meno' => '点击6元商品', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event2[4] = array('name' => 'goods8', 'meno' => '点击10元商品', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event2[5] = array('name' => 'goods3', 'meno' => '点击30元商品', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event2[6] = array('name' => 'goods9', 'meno' => '点击50元商品', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event2[7] = array('name' => 'goods4', 'meno' => '点击100元商品', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event2[8] = array('name' => 'goods5', 'meno' => '点击300元商品', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event2[9] = array('name' => 'goods6', 'meno' => '点击500元商品', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		
		$event3 = array();
		$event3[0] = array('name' => 'myhead', 'meno' => '点击自己头像', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event3[1] = array('name' => 'otherhead', 'meno' => '点击他人头像', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event3[2] = array('name' => 'treasure', 'meno' => '点击在线宝箱', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event3[3] = array('name' => 'quickpay', 'meno' => '点击快速充值', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event3[4] = array('name' => 'ready', 'meno' => '点击准备', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event3[5] = array('name' => 'changetable', 'meno' => '点击换桌', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event3[6] = array('name' => 'allin', 'meno' => '点击全压', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
	
		if ($day_jian >= 0){
			$tongji1 = array();
			$maxi = $day_jian - $Page->firstRow;
			$mini = $day_jian - $Page->firstRow - $Page->listRows;
			if ($mini < 1) $mini = 1;
			for ($i=$maxi; $i>$mini; $i--){
				
				$j = $i - 1;	
				$time1 = strtotime($date11) + 60 * 60 * 24 * ($i - 1);
				$time2 = $time1 + 60 * 60 * 24;
				$date1 = date("Y-m-d", $time1);
				$date2 = date("Y-m-d", $time2);
				
				$table0 = "game_log.log".date("Ymd", $time1);
				$row0 = M($table0);
				
				$event2 = array();
				$event2[0] = array('name' => 'goldBtn', 'meno' => '点击金币选项', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
				$event2[1] = array('name' => 'vipBtn', 'meno' => '点击VIP选项', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
				$event2[2] = array('name' => 'goods7', 'meno' => '点击10元首充商城', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
				$event2[3] = array('name' => 'goods2', 'meno' => '点击6元商品', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
				$event2[4] = array('name' => 'goods8', 'meno' => '点击10元商品', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
				$event2[5] = array('name' => 'goods3', 'meno' => '点击30元商品', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
				$event2[6] = array('name' => 'goods9', 'meno' => '点击50元商品', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
				$event2[7] = array('name' => 'goods4', 'meno' => '点击100元商品', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
				$event2[8] = array('name' => 'goods5', 'meno' => '点击300元商品', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
				$event2[9] = array('name' => 'goods6', 'meno' => '点击500元商品', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
				
				$flag = 4;
				$total = $row->where("data='$date1' and flag=$flag $sql0")->count();
				if ($total == 0){
					//新增用户
					$table1 = "user_info";
					$row1 = M($table1, '', DB_CONFIG2);
					$new_user_id = "";
					$res = $row1->field("user_id")->where("register_date>='$date1' and register_date<'$date2' $sql1")->select();
					foreach($res as $key => $val){
						$new_user_id[] = $val['user_id'];
					}
					//活跃用户
					$active_user_id = array();
					//$res = $row2->field("distinct user_id")->where("login_date>='$date1' and login_date<'$date2' $sql1")->select();
					//foreach($res as $key => $val){
					//	$active_user_id[] = $val['user_id'];
					//}
					
					$res = $row0->field("distinct user_id")->where("addtime>='$time1' and addtime<'$time2' and tname=2")->select();
					//dump($row1->_sql());
					foreach($res as $key => $val){
						$flag1 = (in_array($val['user_id'], $new_user_id)) ? 1 : 0;
						$flag2 = (in_array($val['user_id'], $active_user_id)) ? 1 : 0;
						foreach($event2 as $key1 => $val1){
							//统计各种事件用户数
							$count1 = $row0->where("addtime>='$time1' and addtime<'$time2' and tname=2 and user_id=".$val['user_id']." and ename='".$val1['name']."'")->count('user_id');
							//if ($date1 == "2015-12-19") dump($row0->_sql());
							if ($flag1 == 1){
								if ($count1 > 0) $event2[$key1]['num_new']++;
								$event2[$key1]['clicked_new'] += $count1;
							}else{
								if ($count1 > 0) $event2[$key1]['num_old']++;
								$event2[$key1]['clicked_old'] += $count1;
							}
							if ($flag2 == 1){
								if ($count1 > 0) $event2[$key1]['num_active']++;
								$event2[$key1]['clicked_active'] += $count1;
							}
						}
					}
					
					$tongji = array('data' => $date1,
								    'event' => $event2);
					if ($date1!=date("Y-m-d")){
						/*$data9 = array('data' => $date1,
									   'flag' => $flag,
									   'channel' => empty($channel) ? "all" : $channel,
									   'version' => empty($version) ? "all" : $version,
									   'tongji' => json_encode($tongji),
									   'addtime' => time());
						$result = $row->add($data9);*/			   
					}
					$tongji1[$j] = $tongji;	
				}else{
					$info = $row->where("data='$date1' and flag=$flag $sql0")->find();
					$tongji1[$j] = json_decode($info['tongji'], true);
				}
				
			}
			//print_r($tongji1);
			
			
			//print_r($data1);
			//print_r($data2);
			//print_r($data3);
			//exit;
		} 
		//exit;
		if ($act == "exceldo"){
			
			
			if ($showflag == "idact"){
				$xlsName  = "商城点击(活跃用户)";
			}else if ($showflag == "idnew"){
				$xlsName  = "商城点击(新增用户)";
			}else if ($showflag == "idold"){
				$xlsName  = "商城点击(老用户)";
			}
			
			$t = 0;
			$xlsCell  = array();
			$xlsCell[$t][0] = 'date';
			$xlsCell[$t++][1] = '日期';
			foreach($event2 as $key => $val){
				$temp1 = $val['name']."1";
				$temp2 = $val['name']."2";	
				$xlsCell[$t][0] = $temp1;
				$xlsCell[$t++][1] = $val['meno']."人数";
				$xlsCell[$t][0] = $temp2;
				$xlsCell[$t++][1] = $val['meno']."次数";
			}
			//print_r($xlsCell);
			$t = 0;
			$xlsData = array();
			foreach ($tongji1 as $k => $v)
			{
				$xlsData[$t]['date'] = $v['data'];
				foreach ($v['event'] as $k1 => $v1){
					$temp1 = $v1['name']."1";
					$temp2 = $v1['name']."2";
					if ($showflag == "idact"){
						$xlsData[$t][$temp1] = $v1['num_active'];
						$xlsData[$t][$temp2] = $v1['clicked_active'];
					}else if ($showflag == "idnew"){
						$xlsData[$t][$temp1] = $v1['num_new'];
						$xlsData[$t][$temp2] = $v1['clicked_new'];
					}else if ($showflag == "idold"){
						$xlsData[$t][$temp1] = $v1['num_old'];
						$xlsData[$t][$temp2] = $v1['clicked_old'];
					}
				}
				$t++;
			}
			//print_r($xlsData);
			exportExcel($xlsName,$xlsCell,$xlsData);
			exit;
		}
		
		$this->assign('pageshow',$show);
		$this->assign('tongji1',$tongji1);
		
		$this->assign('left_css',"67");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji2";
		$this->display($lib_display);
	}
	
	
	public function tongji3(){
		$table = "fx_tongji1";
		$row = M($table);
		
		//$table2 = "login_log";
		//$row2 = M($table2, '', DB_CONFIG2);
		
		$beginTime = I("beginTime");
		$endTime = I("endTime");
		$channel = I("channel");
		$version = I("version");
		$showflag = I("flag");
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
			$day_jian = PAGE_SHOW;
			$date11 = date("Y-m-d", (strtotime($date12) - 60 * 60 * 24 * ($day_jian - 1)));
		}
		//echo $day_jian;
		//$date11 .= " 00:00:00"; 
		//$date12 .= " 23:59:59"; 
		import('ORG.Util.Page');
		$Page       = new Page($day_jian,PAGE_SHOW);
		$show       = $Page->show();
		
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
		$sql1 = " and !((user_id>=".ROBERT1_BEGIN." and user_id<".ROBERT1_END.") or (user_id>=".ROBERT2_BEGIN." and user_id<=".ROBERT2_END."))";
		if (!empty($channel)){
			$sql1 .= " and channel=$channel";
			$sql0 .= " and channel=$channel";
		}else{
			$sql0 .= " and channel='all'";
		}
		if (!empty($version)){
			$sql0 .= " and version='$version'";
		}else{
			$sql0 .= " and version='all'";
		}
		
		$event1 = array();
		$event1[0] = array('name' => 'tableLevel1', 'meno' => '点击初级场', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event1[1] = array('name' => 'tableLevel2', 'meno' => '点击中级场', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event1[2] = array('name' => 'tableLevel3', 'meno' => '点击高级场', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event1[3] = array('name' => 'tiger', 'meno' => '点击老虎机', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event1[4] = array('name' => 'mall', 'meno' => '点击商城图标', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event1[5] = array('name' => 'task', 'meno' => '点击任务图标', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event1[6] = array('name' => 'rank', 'meno' => '点击排名图标', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event1[7] = array('name' => 'help', 'meno' => '点击帮助图标', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event1[8] = array('name' => 'fast', 'meno' => '点击快速开始', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event1[9] = array('name' => 'firstcharge', 'meno' => '点击首充', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event1[10] = array('name' => 'mail', 'meno' => '点击邮件图标', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event1[11] = array('name' => 'setting', 'meno' => '点击设置图标', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event1[12] = array('name' => 'myinfo', 'meno' => '点击个人中心', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event1[13] = array('name' => 'horn', 'meno' => '大厅点击大喇叭', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		
		$event2 = array();
		$event2[0] = array('name' => 'goldBtn', 'meno' => '点击金币选项', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event2[1] = array('name' => 'vipBtn', 'meno' => '点击VIP选项', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event2[2] = array('name' => 'goods7', 'meno' => '点击10元首充商城', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event2[3] = array('name' => 'goods2', 'meno' => '点击6元商品', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event2[4] = array('name' => 'goods8', 'meno' => '点击10元商品', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event2[5] = array('name' => 'goods3', 'meno' => '点击30元商品', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event2[6] = array('name' => 'goods9', 'meno' => '点击50元商品', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event2[7] = array('name' => 'goods4', 'meno' => '点击100元商品', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event2[8] = array('name' => 'goods5', 'meno' => '点击300元商品', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event2[9] = array('name' => 'goods6', 'meno' => '点击500元商品', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		
		$event3 = array();
		$event3[0] = array('name' => 'myhead', 'meno' => '点击自己头像', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event3[1] = array('name' => 'otherhead', 'meno' => '点击他人头像', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event3[2] = array('name' => 'treasure', 'meno' => '点击在线宝箱', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event3[3] = array('name' => 'quickpay', 'meno' => '点击快速充值', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event3[4] = array('name' => 'ready', 'meno' => '点击准备', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event3[5] = array('name' => 'changetable', 'meno' => '点击换桌', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
		$event3[6] = array('name' => 'allin', 'meno' => '点击全压', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
	
		if ($day_jian >= 0){
			$tongji1 = array();
			$maxi = $day_jian - $Page->firstRow;
			$mini = $day_jian - $Page->firstRow - $Page->listRows;
			if ($mini < 1) $mini = 1;
			for ($i=$maxi; $i>$mini; $i--){
				
				$j = $i - 1;	
				$time1 = strtotime($date11) + 60 * 60 * 24 * ($i - 1);
				$time2 = $time1 + 60 * 60 * 24;
				$date1 = date("Y-m-d", $time1);
				$date2 = date("Y-m-d", $time2);
				
				$table0 = "game_log.log".date("Ymd", $time1);
				$row0 = M($table0);
				
				$event3 = array();
				$event3[0] = array('name' => 'myhead', 'meno' => '点击自己头像', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
				$event3[1] = array('name' => 'otherhead', 'meno' => '点击他人头像', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
				$event3[2] = array('name' => 'treasure', 'meno' => '点击在线宝箱', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
				$event3[3] = array('name' => 'quickpay', 'meno' => '点击快速充值', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
				$event3[4] = array('name' => 'ready', 'meno' => '点击准备', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
				$event3[5] = array('name' => 'changetable', 'meno' => '点击换桌', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
				$event3[6] = array('name' => 'allin', 'meno' => '点击全压', 'num_new' => 0, 'clicked_new' => 0, 'num_old' => 0, 'clicked_old' => 0, 'num_active' => 0, 'clicked_active' => 0);
				
				
				
				$flag = 5;
				$total = $row->where("data='$date1' and flag=$flag $sql0")->count();
				if ($total == 0){
					//新增用户
					$table1 = "user_info";
					$row1 = M($table1, '', DB_CONFIG2);
					$new_user_id = "";
					$res = $row1->field("user_id")->where("register_date>='$date1' and register_date<'$date2' $sql1")->select();
					foreach($res as $key => $val){
						$new_user_id[] = $val['user_id'];
					}
					//活跃用户
					$active_user_id = array();
					//$res = $row2->field("distinct user_id")->where("login_date>='$date1' and login_date<'$date2' $sql1")->select();
					//foreach($res as $key => $val){
					//	$active_user_id[] = $val['user_id'];
					//}
					
					$res = $row0->field("distinct user_id")->where("addtime>='$time1' and addtime<'$time2' and tname=3")->select();
					//dump($row1->_sql());
					foreach($res as $key => $val){
						$flag1 = (in_array($val['user_id'], $new_user_id)) ? 1 : 0;
						$flag2 = (in_array($val['user_id'], $active_user_id)) ? 1 : 0;
						foreach($event3 as $key1 => $val1){
							//统计各种事件用户数
							$count1 = $row0->where("addtime>='$time1' and addtime<'$time2' and tname=3 and user_id=".$val['user_id']." and ename='".$val1['name']."'")->count('user_id');
							if ($flag1 == 1){
								if ($count1 > 0) $event3[$key1]['num_new']++;
								$event3[$key1]['clicked_new'] += $count1;
							}else{
								if ($count1 > 0) $event3[$key1]['num_old']++;
								$event3[$key1]['clicked_old'] += $count1;
							}
							if ($flag2 == 1){
								if ($count1 > 0) $event3[$key1]['num_active']++;
								$event3[$key1]['clicked_active'] += $count1;
							}
						}
					}
					
					$tongji = array('data' => $date1,
								    'event' => $event3);
					if ($date1!=date("Y-m-d")){
						/*$data9 = array('data' => $date1,
									   'flag' => $flag,
									   'channel' => empty($channel) ? "all" : $channel,
									   'version' => empty($version) ? "all" : $version,
									   'tongji' => json_encode($tongji),
									   'addtime' => time());
						$result = $row->add($data9);*/			   
					}
					$tongji1[$j] = $tongji;	
				}else{
					$info = $row->where("data='$date1' and flag=$flag $sql0")->find();
					$tongji1[$j] = json_decode($info['tongji'], true);
				}
				
			}
			//print_r($tongji1);
			
			
			//print_r($data1);
			//print_r($data2);
			//print_r($data3);
			//exit;
		} 
		//exit;
		if ($act == "exceldo"){
			
			
			if ($showflag == "idact"){
				$xlsName  = "牌桌点击(活跃用户)";
			}else if ($showflag == "idnew"){
				$xlsName  = "牌桌点击(新增用户)";
			}else if ($showflag == "idold"){
				$xlsName  = "牌桌点击(老用户)";
			}
			
			$t = 0;
			$xlsCell  = array();
			$xlsCell[$t][0] = 'date';
			$xlsCell[$t++][1] = '日期';
			foreach($event3 as $key => $val){
				$temp1 = $val['name']."1";
				$temp2 = $val['name']."2";	
				$xlsCell[$t][0] = $temp1;
				$xlsCell[$t++][1] = $val['meno']."人数";
				$xlsCell[$t][0] = $temp2;
				$xlsCell[$t++][1] = $val['meno']."次数";
			}
			//print_r($xlsCell);
			$t = 0;
			$xlsData = array();
			foreach ($tongji1 as $k => $v)
			{
				$xlsData[$t]['date'] = $v['data'];
				foreach ($v['event'] as $k1 => $v1){
					$temp1 = $v1['name']."1";
					$temp2 = $v1['name']."2";
					if ($showflag == "idact"){
						$xlsData[$t][$temp1] = $v1['num_active'];
						$xlsData[$t][$temp2] = $v1['clicked_active'];
					}else if ($showflag == "idnew"){
						$xlsData[$t][$temp1] = $v1['num_new'];
						$xlsData[$t][$temp2] = $v1['clicked_new'];
					}else if ($showflag == "idold"){
						$xlsData[$t][$temp1] = $v1['num_old'];
						$xlsData[$t][$temp2] = $v1['clicked_old'];
					}
				}
				$t++;
			}
			//print_r($xlsData);
			exportExcel($xlsName,$xlsCell,$xlsData);
			exit;
		}
		
		$this->assign('pageshow',$show);
		$this->assign('tongji1',$tongji1);
		
		$this->assign('left_css',"67");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji3";
		$this->display($lib_display);
	}
	
	//导出
	public function exportExceldo($expTitle,$expCellName,$expTableData){
        $xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
        $fileName = $xlsTitle.date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
        $cellNum = count($expCellName);
        $dataNum = count($expTableData);
        vendor("PHPExcel.PHPExcel");
       
        $objPHPExcel = new PHPExcel();
        $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');
        
        //$objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
        //$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle);  
        for($i=0;$i<$cellNum;$i++){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'1', $expCellName[$i][1]); 
        } 
          // Miscellaneous glyphs, UTF-8   
        for($i=0;$i<$dataNum;$i++){
          for($j=0;$j<$cellNum;$j++){
            $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+2), $expTableData[$i][$expCellName[$j][0]]);
          }             
        }  
        
        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');  
        $objWriter->save('php://output'); 
        exit;   
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