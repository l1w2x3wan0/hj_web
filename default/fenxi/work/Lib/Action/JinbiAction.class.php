<?php
// 金币体系文件

class JinbiAction extends BaseAction {

	protected $By_tpl = 'Jinbi'; 
	
	//金币发放数据统计
	public function tongji1(){
		$table = "fx_base";
		$row = M($table);
		$table1 = "fx_jinbi_tongji1";
		$row1 = M($table1);
		$table3 = "fx_jinbi_base_config";
		$row3 = M($table3);
		$table4 = "fx_jinbi_base";
		$row4 = M($table4);
		
		$beginTime = I("beginTime");
		$endTime = I("endTime");
		
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
			$date11 = $beginTime;
			$date12 = $endTime;
			$day_jian = (strtotime($date12) - strtotime($date11)) / (60 * 60 * 24) + 1;
			
			//$datenow = date("d", strtotime($endTime));
			//$dateend = date("d", strtotime($beginTime));
		}else{
			$date12 = date("Y-m-d");
			$day_jian = 7;
			$date11 = date("Y-m-d", (strtotime($date12) - 60 * 60 * 24 * 7));
		}
		
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
		$module = $row3->where('cate=1')->order('module')->select();
		//初始化统计
		$alltotal = array();
		$alltotal[0] = 0;
		foreach($module as $key => $val){
			$alltotal[$key+1] = 0;
		}
		$this->assign('module',json_encode($module));
	
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
			
			$alltotal2 = 0;
			
			for ($i=1; $i<=$day_jian; $i++){
				$j = $i - 1;	
				$time1 = strtotime($date11) + 60 * 60 * 24 * ($i - 1);
				$time2 = $time1 + 60 * 60 * 24;
				$time3 = $time1 - 60 * 60 * 24;
				$date1 = date("Y-m-d", $time1);
				$date2 = date("Y-m-d", $time2);
				$date3 = date("Y-m-d", $time3);
				//echo $date1."<br>";
				if (!empty($channel)){

				}else{
					//查询默认开始
					$tongji = array();
					$tongji['data'] = $date1;
					
					$info = $row4->where("date='".$date1."' and cate=1 and module=0")->find();
					$alltotal[0] += $info['gold'];
					$showkey1 = "module0";
					$tongji[$showkey1] = !empty($info['gold']) ? $info['gold'] : 0;
					$showkey2 = "module0_format";
					$tongji[$showkey2] = !empty($info['gold']) ? number_format($info['gold']) : 0;
					
					foreach($module as $key => $val){
						$info = $row4->where("date='".$date1."' and cate=1 and module=".$val['module'])->find();
						$alltotal[$key+1] += $info['gold'];
						
						$showkey1 = "module".$val['module'];
						$tongji[$showkey1] = !empty($info['gold']) ? $info['gold'] : 0;
						$showkey2 = "module".$val['module']."_format";
						$tongji[$showkey2] = !empty($info['gold']) ? number_format($info['gold']) : 0;
					}
					//查询默认结束
				}
				$tongji1[$j] = $tongji;
			}

			foreach ($tongji1 as $key => $val){
				$data1[0] .= ($key==0) ? "'".$val['data']."'" : ",'".$val['data']."'";
				$data2[0] .= ($key==0) ? "'".$val['data']."'" : ",'".$val['data']."'";

				$val['count5'] = $val['module0'];
				foreach($module as $key1 => $val1){
					$showkey1 = "module".$val1['module'];
					
					$data1[$key1+1] .= ($key==0) ? $val[$showkey1] : ",".$val[$showkey1];
					
					$showkey2 = "bl".$val1['module'];
					$tongji1[$key][$showkey2] = (!empty($val['module0'])) ? round($val[$showkey1]/$val['module0'],3)*100 : 0;
					$data2[$key1+1] .= ($key==0) ? $tongji1[$key][$showkey2] : ",".$tongji1[$key][$showkey2];
				}
			}
		} 
		//print_r($tongji2);
		$bl = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		for($i=0; $i<14; $i++){
			$j = $i + 1;
			$bl[$i] = (!empty($alltotal[0])) ? round($alltotal[$j]/$alltotal[0],3)*100 : 0;
		}
		$this->assign('bl',json_encode($bl));	
		
		for($i=0; $i<15; $i++){
			$alltotal[$i] = number_format($alltotal[$i]);
		}

		$pagesize = ceil($day_jian / 10);
		$this->assign('pagesize',$pagesize);
		$this->assign('tongji1',json_encode($tongji1));
		$this->assign('tongji2',json_encode($tongji2));
		$this->assign('tongji3',json_encode($tongji3));
		$this->assign('tongji4',json_encode($tongji4));
		
		/*引入GoogChart类*/
		import("ORG.Util.GoogChart");
		$chart = new GoogChart();	

		//时间轴\新增账号数据\新增激活数据
		$this->assign('alltotal',json_encode($alltotal));	
		$this->assign('data1',$data1);	
		$this->assign('data2',$data2);
		$this->assign('data3',$data3);
		$this->assign('data4',$data4);
		
		$this->assign('left_css',"20");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji1";
		$this->display($lib_display);
	}
	
	//金币回收数据统计
	public function tongji2(){
		$table = "fx_base";
		$row = M($table);
		
		$table0 = "fx_jinbi_tongji2";
		$row0 = M($table0);
		
		$table3 = "fx_jinbi_base_config";
		$row3 = M($table3);
		$table4 = "fx_jinbi_base";
		$row4 = M($table4);
		
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
			$date11 = date("Y-m-d", (strtotime($date12) - 60 * 60 * 24 * 7));
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
		
		//金币回收类型
		$module = $row3->where('cate=2 and roomid=0')->order('module')->select();
		//初始化统计
		$alltotal = array();
		$alltotal[0] = 0;
		foreach($module as $key => $val){
			$alltotal[$key+1] = 0;
		}
		$this->assign('module',json_encode($module));
		
		//房间号
		//$room = array('1','2','3');
	
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
			$alltotal = array(0,0,0,0,0,0);
			$alltotal2 = 0;
			
			for ($i=1; $i<=$day_jian; $i++){
				$j = $i - 1;	
				$time1 = strtotime($date11) + 60 * 60 * 24 * ($i - 1);
				$time2 = $time1 + 60 * 60 * 24;
				$time3 = $time1 - 60 * 60 * 24;
				$date1 = date("Y-m-d", $time1);
				$date2 = date("Y-m-d", $time2);
				$date3 = date("Y-m-d", $time3);
				//echo $date1."<br>";
				if (!empty($channel)){
					//查询渠道开始

					//查询渠道结束
				}else{
					//查询默认开始
					$tongji = array();
					$tongji['data'] = $date1;
					
					$info = $row4->where("date='".$date1."' and cate=2 and module=0")->find();
					$alltotal[0] += $info['gold'];
					$showkey1 = "module0";
					$tongji[$showkey1] = !empty($info['gold']) ? $info['gold'] : 0;
					$showkey2 = "module0_format";
					$tongji[$showkey2] = !empty($info['gold']) ? number_format($info['gold']) : 0;
					
					foreach($module as $key => $val){
						$info = $row4->where("date='".$date1."' and cate=2 and module=".$val['module'])->find();
						$alltotal[$key+1] += $info['gold'];
						
						$showkey1 = "module".$val['module'];
						$tongji[$showkey1] = !empty($info['gold']) ? $info['gold'] : 0;
						$showkey2 = "module".$val['module']."_format";
						$tongji[$showkey2] = !empty($info['gold']) ? number_format($info['gold']) : 0;
					}
					//查询默认结束
				}
				$tongji1[$j] = $tongji;
			}
			
			//print_r($tongji1);
	
			foreach ($tongji1 as $key => $val){
				$data1[0] .= ($key==0) ? "'".$val['data']."'" : ",'".$val['data']."'";
				$data2[0] .= ($key==0) ? "'".$val['data']."'" : ",'".$val['data']."'";

				$val['count5'] = $val['module0'];
				foreach($module as $key1 => $val1){
					$showkey1 = "module".$val1['module'];
					
					$data1[$key1+1] .= ($key==0) ? $val[$showkey1] : ",".$val[$showkey1];
					
					$showkey2 = "bl".$val1['module'];
					$tongji1[$key][$showkey2] = (!empty($val['module0'])) ? round($val[$showkey1]/$val['module0'],3)*100 : 0;
					$data2[$key1+1] .= ($key==0) ? $tongji1[$key][$showkey2] : ",".$tongji1[$key][$showkey2];
				}
			}
		} 
		
		$bl = array(0,0,0,0,0,0,0,0,0,0);
		for($i=0; $i<11; $i++){
			$j = $i + 1;
			$bl[$i] = (!empty($alltotal[0])) ? round($alltotal[$j]/$alltotal[0],3)*100 : 0;
		}
		$this->assign('bl',json_encode($bl));
		//print_r($tongji2);
		for($i=0; $i<11; $i++){
			$alltotal[$i] = number_format($alltotal[$i]);
		}

		$pagesize = ceil($day_jian / 10);
		$this->assign('pagesize',$pagesize);
		$this->assign('tongji1',json_encode($tongji1));
		$this->assign('tongji2',json_encode($tongji2));
		$this->assign('tongji3',json_encode($tongji3));
		$this->assign('tongji4',json_encode($tongji4));
		
		/*引入GoogChart类*/
		import("ORG.Util.GoogChart");
		$chart = new GoogChart();	

		//时间轴\新增账号数据\新增激活数据
		$this->assign('alltotal',json_encode($alltotal));	
		$this->assign('data1',$data1);	
		$this->assign('data2',$data2);
		$this->assign('data3',$data3);
		$this->assign('data4',$data4);
		
		$this->assign('left_css',"20");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji2";
		$this->display($lib_display);
	}
	
	//总池
	public function tongji3(){
		$table = "fx_base";
		$row = M($table);
		
		$table3 = "fx_jinbi_base_config";
		$row3 = M($table3);
		$table4 = "fx_jinbi_base";
		$row4 = M($table4);
		$table5 = "fx_tongji1";
		$row5 = M($table5);
		
		
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
			$date11 = date("Y-m-d", (strtotime($date12) - 60 * 60 * 24 * 7));
			$date12 = date("Y-m-d", (strtotime($date12) - 60 * 60 * 24 * 1));
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
		if (!empty($channel)){
			$sql1 .= " and channel=$channel";
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
				$time3 = $time1 - 60 * 60 * 24;
				$date1 = date("Y-m-d", $time1);
				$date2 = date("Y-m-d", $time2);
				$date3 = date("Y-m-d", $time3);
				//echo $date1."<br>";
				
				if ($date1>'2016-04-07'){
					
					//获取系统总量
					$res = $row5->where("data='".$date1."' and flag=11 and channel='all' and version='all'")->find();
					$info = json_decode($res['tongji'], true);
					$sum1 = !empty($info['sum_jb_play']) ? $info['sum_jb_play'] : 0;
					$sum_jb_active = !empty($info['sum_jb_active']) ? $info['sum_jb_active'] : 0;
					//获取昨天系统总量
					$res_yes = $row5->where("data='".$date3."' and flag=11 and channel='all' and version='all'")->find();
					$info = json_decode($res_yes['tongji'], true);
					$sum2 = !empty($info['sum_jb_play']) ? $info['sum_jb_play'] : 0;
					$sum3 = $sum1 - $sum2;
					$sum4 = !empty($sum2) ? round($sum3/$sum2, 1) : 0;
					//获取金币总产出
					$res = $row4->where("date='".$date1."' and cate=1 and module=0")->find();
					$sum_jb_send = $res['gold'];
					//获取金币总回收
					$res = $row4->where("date='".$date1."' and cate=2 and module=0")->find();
					$sum_jb_recall = $res['gold'];
					$tongji = array('data' => $date1,
									'count1' => $sum1,
									'count1_format' => !empty($sum1) ? number_format($sum1) : 0,
									'count2' => $sum3,
									'count2_format' => !empty($sum3) ? number_format($sum3) : 0,
									'count3' => $sum4,
									'count4' => !empty($sum_jb_active) ? $sum_jb_active : 0,
									'count4_format' => !empty($sum_jb_active) ? number_format($sum_jb_active) : 0,
									'count5' => !empty($sum_jb_send) ? $sum_jb_send : 0,
									'count5_format' => !empty($sum_jb_send) ? number_format($sum_jb_send) : 0,
									'count6' => !empty($sum_jb_recall) ? $sum_jb_recall : 0,
									'count6_format' => !empty($sum_jb_recall) ? number_format($sum_jb_recall) : 0);
					
				}else{
				
					$res = $row->where("data='".$date1."'")->find();
					$res_yes = $row->where("data='".$date3."'")->find();
					$sum1 = !empty($res['sum_jb_play']) ? $res['sum_jb_play'] : 0;
					$sum2 = !empty($res_yes['sum_jb_play']) ? $res_yes['sum_jb_play'] : 0;
					$sum3 = $sum1 - $sum2;
					$sum4 = !empty($sum2) ? round($sum3/$sum2, 1) : 0;
					$tongji = array('data' => $date1,
									'count1' => !empty($res['sum_jb_play']) ? $res['sum_jb_play'] : 0,
									'count1_format' => !empty($res['sum_jb_play']) ? number_format($res['sum_jb_play']) : 0,
									'count2' => $sum3,
									'count2_format' => !empty($sum3) ? number_format($sum3) : 0,
									'count3' => $sum4,
									'count4' => !empty($res['sum_jb_active']) ? $res['sum_jb_active'] : 0,
									'count4_format' => !empty($res['sum_jb_active']) ? number_format($res['sum_jb_active']) : 0,
									'count5' => !empty($res['sum_jb_send']) ? $res['sum_jb_send'] : 0,
									'count5_format' => !empty($res['sum_jb_send']) ? number_format($res['sum_jb_send']) : 0,
									'count6' => !empty($res['sum_jb_recall']) ? $res['sum_jb_recall'] : 0,
									'count6_format' => !empty($res['sum_jb_recall']) ? number_format($res['sum_jb_recall']) : 0	);
				}
				
				$tongji1[$j] = $tongji;
			}
			
			foreach ($tongji1 as $key => $val){
				$data1[0] .= ($key==0) ? "'".$val['data']."'" : ",'".$val['data']."'";
				$data1[1] .= ($key==0) ? $val['count1'] : ",".$val['count1'];
				$data1[2] .= ($key==0) ? $val['count4'] : ",".$val['count4'];
				$data1[3] .= ($key==0) ? $val['count5'] : ",".$val['count5'];
				$data1[4] .= ($key==0) ? $val['count6'] : ",".$val['count6'];
			}
		} 
		


		$pagesize = ceil($day_jian / 10);
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
		
		$this->assign('left_css',"20");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji3";
		$this->display($lib_display);
	}
	
	//金币top100
	public function tongji4(){
		$table = "user_info";
		$row = M($table, '', DB_CONFIG2);
		
		import('ORG.Util.Page');
		
		$sortscate = I("sortscate");
		$sortsflag = I("sortsflag");
		
		$order = "gold DESC";
			
		$sql1 = "!((user_id>=".ROBERT1_BEGIN." and user_id<".ROBERT1_END.") or (user_id>=".ROBERT2_BEGIN." and user_id<=".ROBERT2_END."))";
		$count = 100;
		$Page       = new Page($count,100);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		$list = $row->field("total_pay_num,lost_count,win_count,user_id,gold,channel,register_date,last_login_date,deposit")->where($sql1)->order($order)->limit(0,100)->select();
		//dump($row->_sql());
		//exit;
		$sql2 = "";
		foreach($list as $key=>$value){
			$list[$key]['id'] = $key + 1;
			$list[$key]['total_pay_num'] = !empty($value['total_pay_num']) ? number_format($value['total_pay_num'] / 100, 2) : 0;
			$list[$key]['pay_count'] = $value['lost_count'] + $value['win_count'];
			$list[$key]['sum01'] = $value['gold'] + $value['deposit'];
			//总回收金币
			/*$time1 = date("Ym");
			$table1 = "log_gold_change_log_".$time1;
			$row1 = M($table1, '', DB_CONFIG2);
			$count1 = $row1->where("module in (3,12,14) and user_id=".$value['user_id'])->sum('taxgold');
			//if ($value['user_id']=="10328209") dump($row1->_sql());
			//if ($value['user_id']=="10328209") echo "<br>".$count1."<br>";
			$count2 = $row1->where("module in (10,11) and user_id=".$value['user_id'])->sum('changegold');
			//if ($value['user_id']=="10328209") dump($row1->_sql());
			//if ($value['user_id']=="10328209") echo "<br>".$count2."<br>";
			$count2 = abs($count2);
			$list[$key]['recall_count'] = $count1 + $count2;
			
			$i = 1;
			while($time1>"201510"){
				$time1 = date("Ym", strtotime("-$i month"));
				$table1 = "log_gold_change_log_".$time1;
				$row1 = M($table1, '', DB_CONFIG2);
				$count1 = $row1->where("module in (3,12,14) and user_id=".$value['user_id'])->sum('taxgold');
				//if ($value['user_id']=="10328209") dump($row1->_sql());
				//if ($value['user_id']=="10328209") echo "<br>".$count1."<br>";
				$count2 = $row1->where("module in (10,11) and user_id=".$value['user_id'])->sum('changegold');
				//if ($value['user_id']=="10328209") dump($row1->_sql());
				//if ($value['user_id']=="10328209") echo "<br>".$count2."<br>";
				$count2 = abs($count2);
				$list[$key]['recall_count'] += $count1 + $count2;
				$i++;
				if ($time1=="201510") break;
			}*/
			/*
			$time1 = date("Ym");
			$table1 = "log_gold_change_log_".$time1;
			$row1 = M($table1, '', DB_CONFIG2);
			$count1 = $row1->where("module in (3,12,14) and user_id=".$value['user_id'])->sum('taxgold');
			//if ($value['user_id']=="10328209") dump($row1->_sql());
			//if ($value['user_id']=="10328209") echo "<br>".$count1."<br>";
			$count2 = $row1->where("module in (10,11) and user_id=".$value['user_id'])->sum('changegold');
			//if ($value['user_id']=="10328209") dump($row1->_sql());
			//if ($value['user_id']=="10328209") echo "<br>".$count2."<br>";
			$count2 = abs($count2);
			$list[$key]['recall_count'] = $count1 + $count2;*/
			
			//$sql2 .= (empty($sql2)) ? $value['user_id'] : ",".$value['user_id'];
			//$sort1[$key] = $list[$key]['sum01'];
			$sort1[$key] = $value['gold'];
			$sort2[$key] = $value['channel'];
			$sort3[$key] = $value['total_pay_num'];
			$sort4[$key] = $list[$key]['pay_count'];
			$sort5[$key] = $value['recall_count'];
			$sort6[$key] = $value['register_date'];
			$sort7[$key] = $value['last_login_date'];
			$sort8[$key] = $value['deposit'];
			
		}
		$sql2 = " and user_id not in ($sql2)";
		
		/*
		$list2 = $row->field("total_pay_num,lost_count,win_count,user_id,gold,channel,register_date,last_login_date,deposit")->where($sql1.$sql2)->order("deposit DESC")->limit(0,150)->select();
		//dump($row->_sql());
		foreach($list2 as $key2=>$value){
			$key++;
			$list[$key]['id'] = $key + 1;
			$list[$key]['total_pay_num'] = !empty($value['total_pay_num']) ? number_format($value['total_pay_num'] / 100, 2) : 0;
			$list[$key]['pay_count'] = $value['lost_count'] + $value['win_count'];
			$list[$key]['sum01'] = $value['gold'] + $value['deposit'];
			//总回收金币
			$time1 = date("Ym");
			$table1 = "log_gold_change_log_".$time1;
			$row1 = M($table1, '', DB_CONFIG2);
			$count1 = $row1->where("module in (3,12,14) and user_id=".$value['user_id'])->sum('taxgold');
			//if ($value['user_id']=="10328209") dump($row1->_sql());
			//if ($value['user_id']=="10328209") echo "<br>".$count1."<br>";
			$count2 = $row1->where("module in (10,11) and user_id=".$value['user_id'])->sum('changegold');
			//if ($value['user_id']=="10328209") dump($row1->_sql());
			//if ($value['user_id']=="10328209") echo "<br>".$count2."<br>";
			$count2 = abs($count2);
			$list[$key]['recall_count'] = $count1 + $count2;
			$i = 1;
			while($time1>"201510"){
				$time1 = date("Ym", strtotime("-$i month"));
				$table1 = "log_gold_change_log_".$time1;
				$row1 = M($table1, '', DB_CONFIG2);
				$count1 = $row1->where("module in (3,12,14) and user_id=".$value['user_id'])->sum('taxgold');
				//if ($value['user_id']=="10328209") dump($row1->_sql());
				//if ($value['user_id']=="10328209") echo "<br>".$count1."<br>";
				$count2 = $row1->where("module in (10,11) and user_id=".$value['user_id'])->sum('changegold');
				//if ($value['user_id']=="10328209") dump($row1->_sql());
				//if ($value['user_id']=="10328209") echo "<br>".$count2."<br>";
				$count2 = abs($count2);
				$list[$key]['recall_count'] += $count1 + $count2;
				$i++;
				if ($time1=="201510") break;
			}
			
			$sort1[$key] = $list[$key]['sum01'];
		}*/
		//array_multisort($sort1, SORT_DESC, $list);
		/*
		$showlist = array();
		for($i=0; $i<100; $i++){
			$showlist[$i] = $list[$i];
			$sort1[$key] = $list[$key]['gold'];
			$sort2[$key] = $list[$key]['channel'];
			$sort3[$key] = $list[$key]['total_pay_num'];
			$sort4[$key] = $list[$key]['pay_count'];
			$sort5[$key] = $list[$key]['recall_count'];
			$sort6[$key] = $list[$key]['register_date'];
			$sort7[$key] = $list[$key]['last_login_date'];
			$sort8[$key] = $list[$key]['deposit'];
		}*/
		
			

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
		}else if ($sortscate=="4"){
			if ($sortsflag=="1"){
				array_multisort($sort4, SORT_ASC, $list);
			}else{
				array_multisort($sort4, SORT_DESC, $list);
			}
		}else if ($sortscate=="5"){
			if ($sortsflag=="1"){
				array_multisort($sort5, SORT_ASC, $list);
			}else{
				array_multisort($sort5, SORT_DESC, $list);
			}
		}else if ($sortscate=="6"){
			if ($sortsflag=="1"){
				array_multisort($sort6, SORT_ASC, $list);
			}else{
				array_multisort($sort6, SORT_DESC, $list);
			}
		}else if ($sortscate=="7"){
			if ($sortsflag=="1"){
				array_multisort($sort7, SORT_ASC, $list);
			}else{
				array_multisort($sort7, SORT_DESC, $list);
			}
		}else if ($sortscate=="8"){
			if ($sortsflag=="1"){
				array_multisort($sort8, SORT_ASC, $list);
			}else{
				array_multisort($sort8, SORT_DESC, $list);
			}
		}
		
		//echo $Page->firstRow."**".$Page->listRows*$Page->nowPage."<br>";
		/*
		$showlist2 = array();
		$p = I("p");
		if (empty($p)) $p = 1;
		for ($i=$Page->firstRow; $i<$Page->listRows*$p; $i++){
			$showlist2[$i] = $list[$i];
		}*/
			
		$this->assign('list',$list);
		$this->assign('pageshow',$show);
		
		$this->assign('left_css',"20");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji4";
		$this->display($lib_display);		
	}
	
	//礼物top100
	public function gift(){
		$table = "user_info";
		$row = M($table, '', DB_CONFIG2);
		
		import('ORG.Util.Page');
		
		$sortscate = I("sortscate");
		$sortsflag = I("sortsflag");
		if (empty($sortscate)) $sortscate = "4";
		if (empty($sortsflag)) $sortsflag = "2";
		
		$order = "yacht DESC,villa DESC,car DESC";
			
		$sql1 = "!((user_id>=".ROBERT1_BEGIN." and user_id<".ROBERT1_END.") or (user_id>=".ROBERT2_BEGIN." and user_id<=".ROBERT2_END."))";
		$count = 100;
		$Page       = new Page($count,100);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		$list = $row->field("user_id,nick_name,nickname,car,villa,yacht,register_date,last_login_date")->where($sql1)->order($order)->limit(0,100)->select();
		//dump($row->_sql());
		//exit;
		foreach($list as $key=>$value){
			$list[$key]['id'] = $key + 1;
			$list[$key]['nickname'] = !empty($value['nickname']) ? $value['nickname'] : $value['nick_name'];
			$list[$key]['sum01'] = $value['car']*48000 + $value['villa']*240000 + $value['yacht']*400000;

			$sort3[$key] = $value['car'];
			$sort2[$key] = $value['villa'];
			$sort1[$key] = $value['yacht'];
			$sort4[$key] = $list[$key]['sum01'];
		}
		
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
		}else if ($sortscate=="4"){
			if ($sortsflag=="1"){
				array_multisort($sort4, SORT_ASC, $list);
			}else{
				array_multisort($sort4, SORT_DESC, $list);
			}
		}
			
		$this->assign('list',$list);
		$this->assign('pageshow',$show);
		
		$this->assign('left_css',"20");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/gift";
		$this->display($lib_display);		
	}
	
	//奖券top100
	public function ticket(){
		$table = "user_info";
		$row = M($table, '', DB_CONFIG2);
		
		import('ORG.Util.Page');
		
		$sortscate = I("sortscate");
		$sortsflag = I("sortsflag");
		if (empty($sortscate)) $sortscate = "4";
		if (empty($sortsflag)) $sortsflag = "2";
		
		$order = "ticket DESC";
			
		$sql1 = "!((user_id>=".ROBERT1_BEGIN." and user_id<".ROBERT1_END.") or (user_id>=".ROBERT2_BEGIN." and user_id<=".ROBERT2_END."))";
		$count = 100;
		$Page       = new Page($count,100);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		$list = $row->field("user_id,nick_name,nickname,ticket,register_date,last_login_date")->where($sql1)->order($order)->limit(0,100)->select();
		//dump($row->_sql());
		//exit;
		foreach($list as $key=>$value){
			$list[$key]['id'] = $key + 1;
			$list[$key]['nickname'] = !empty($value['nickname']) ? $value['nickname'] : $value['nick_name'];

			$sort1[$key] = $value['ticket'];
		}
		
		if ($sortscate=="1"){
			if ($sortsflag=="1"){
				array_multisort($sort1, SORT_ASC, $list);
			}else{
				array_multisort($sort1, SORT_DESC, $list);
			}
		}
			
		$this->assign('list',$list);
		$this->assign('pageshow',$show);
		
		$this->assign('left_css',"20");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/ticket";
		$this->display($lib_display);		
	}
	
	//所有转账记录
	public function alltrans(){
		$table = "user_transfer_gold_record";
		$row = M($table, '', DB_CONFIG2);
		$user = M("user_info", '', DB_CONFIG2);
		
		
		import('ORG.Util.Page');
		
		$order = "operatortime DESC";
			
		$sql1 = "!((user_id>=".ROBERT1_BEGIN." and user_id<".ROBERT1_END.") or (user_id>=".ROBERT2_BEGIN." and user_id<=".ROBERT2_END."))";
		$count = $row->where("fromuserid=0")->count('userid');
		$Page       = new Page($count,20);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		$list = $row->where("fromuserid=0")->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($row->_sql());
		//exit;
		foreach($list as $key=>$val){
			$list[$key]['operatortime'] =  date("Y-m-d H:i:s", $val['operatortime']);
			$list[$key]['transfergold'] = number_format($val['transfergold']);
			$list[$key]['transfergold'] = '<font color="#FF0000">-'.$list[$key]['transfergold'].'</font>'; 
			$userinfo = $user->field('nick_name,nickname')->where("user_id=".$val['userid'])->find();
			$list[$key]['nickname'] = (!empty($userinfo['nickname'])) ? $userinfo['nickname'] : $userinfo['nick_name'];
			
			$list[$key]['type'] = '转出';
			$userinfo = $user->field('nick_name,nickname')->where("user_id=".$val['touserid'])->find();
			$list[$key]['othernickname'] = (!empty($userinfo['nickname'])) ? $userinfo['nickname'] : $userinfo['nick_name'];
		}
		

			
		$this->assign('list',$list);
		$this->assign('pageshow',$show);
		
		$this->assign('left_css',"20");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/alltrans";
		$this->display($lib_display);		
	}
	
	//金币段分布
	public function tongji5(){
		$table = "fx_jinbi_fb";
		$row = M($table);
		
		$beginTime = I("beginTime");
		
		//查询不能大于当天
		$timenow = date("Y-m-d",strtotime("-1 day"));
		if ($beginTime>$timenow){
			$this->error('查询日期不能大于昨天');
			exit;
		}
		
		if (empty($beginTime)){
			$beginTime = $timenow;
		}
		
		$res = $row->where("data='".$beginTime."'")->find();
		//dump($row->_sql());
		if (!empty($res['tongji'])){
			$tongji = json_decode($res['tongji'],true);
		}
		//print_r($tongji); exit;
		
		$this->assign('beginTime',$beginTime);
		$this->assign('tongji',$tongji);
		
		$this->assign('left_css',"20");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji5";
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