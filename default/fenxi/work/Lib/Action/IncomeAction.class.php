<?php
// 收入分析文件

class IncomeAction extends BaseAction {

	protected $By_tpl = 'Income'; 
	
	//收入数据
	public function tongji1(){
		$table = "fx_income_tongji1";
		$row = M($table);
		$table1 = "pay_now_config.zjh_order";
		$row1 = M($table1);
		$table2 = "payment";
		$row2 = M($table2);
		$table3 = "zjh_goods";
		$row3 = M($table3);
		$table4 = "user_info";
		$row4 = M($table4, '', DB_CONFIG2);
		
		$beginTime = I("beginTime");
		$endTime = I("endTime");
		$channel = I("channel");
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
		
		$sql1 = "";
		$sql0 = "";
		if (!empty($channel)){
			$sql1 .= " and package_id=$channel";
			$sql0 .= " and channel=$channel";
		}else{
			$sql0 .= " and channel='all'";
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
				
				//收入数据
				$total = $row->where("data='$date1' and flag=1 $sql0")->count();
				if ($total == 0){
					//收入金额
					$count1 = $row1->where("order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') $sql1")->sum('result_money');
					if (empty($count1)) $count1 = 0; 
					//充值次数
					$count2 = $row1->where("order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') $sql1")->count('id');
					//充值人数
					$count3 = $row1->where("order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') $sql1")->count('distinct user_id');
					//判断充值用户是否新用户
					$res1 = $row1->field('distinct user_id')->where("order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') $sql1")->select();
					//dump($row1->_sql());
					$sql2 = "";
					foreach($res1 as $key => $val){
						$sql2 .= ($key==0) ? $val['user_id'] : ",".$val['user_id'];
					}
					if (!empty($sql2)){
						$count4 = $row4->where("user_id in ($sql2) and (register_date>'$date1' and register_date<'$date2')")->count('user_id');
						//dump($row4->_sql());
						$count5 = $count3 - $count4;
					}else{
						$count4 = 0;
						$count5 = 0;
					}
					
					$tongji = array('data' => $date1,
									'count1' => $count1/100,
								    'count2' => $count2,
								    'count3' => $count3,
									'count4' => $count4,
									'count5' => $count5);
					if ($date1<date("Y-m-d") and date("H")>1){
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
				}
				
				//支付方式
				$res2 = $row2->field('payment_id,payment_name')->where("payment_status='1'")->select();
				$this->assign('payment',json_encode($res2));
				$total = $row->where("data='$date1' and flag=2 $sql0")->count();
				if ($total == 0){
					$tongji = array('data' => $date1);
					$sumall = 0;
					$sum1 = 0;
					$sum2 = 0;
					$tongji['payment'][] = array();
                    $sql11 = "";
					foreach($res2 as $key2 => $val2){
						$count1 = $row1->where("order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') and payment_id='".$val2['payment_id']."' $sql1")->sum('result_money');
						if (empty($count1)) $count1 = 0; 
						$count2 = $row1->where("order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') and payment_id='".$val2['payment_id']."' $sql1")->count('id');
						$count3 = $row1->where("order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') and payment_id='".$val2['payment_id']."' $sql1")->count('distinct user_id');
						//dump($row1->_sql());
						//echo $count3."<br>"; 
						$tongji['payment'][] = array('payment_id' => $val2['payment_id'],
												     'show_count1' => '¥'.$count1/100,
													 'count1' => $count1,
													 'count2' => $count2,
													 'count3' => $count3);
						$sumall += $count1;
						$sum1 += $count2;
                        $sql11 .= (empty($sql11)) ? $val2['payment_id'] : ",".$val2['payment_id'];
					}
                    //其它新增支付
                    $sql11 = " and payment_id not in ($sql11)";
                    $count11 = $row1->where("order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') $sql1 $sql11")->sum('result_money');
                    if (empty($count11)) $count11 = 0;
                    $count12 = $row1->where("order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') $sql1 $sql11")->count('id');
                    //$count13 = $row1->where("order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') and payment_id='".$val2['payment_id']."' $sql1 $sql11")->count('distinct user_id');
                    $tongji['show_othercount1'] = '¥'.$count11/100;
                    $tongji['othercount1'] = $count11;
                    $tongji['othercount2'] = $count12;

                    $sum2 = $row1->where("order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') $sql1")->count('distinct user_id');
					$tongji['sumall'] = $sumall + $count11;
					$tongji['sumshow'] = '¥'.$tongji['sumall']/100;
					$tongji['sum1'] = $sum1 + $count12;
					$tongji['sum2'] = $sum2;


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
				
				//充值类型分布 (订单数)
				$res3 = $row3->field('goods_id,goods_name,remark')->order("order_by_value")->select();
				$this->assign('goods',json_encode($res3));
				$total = $row->where("data='$date1' and flag=3 $sql0")->count();
				if ($total == 0){
					$tongji = array('data' => $date1);
					$sumall = 0;
					foreach($res3 as $key3 => $val3){
						$count1 = $row1->where("order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') and goods_id='".$val3['goods_id']."' $sql1")->count('id');
						$tongji['goods'][] = array('goods_id' => $val3['goods_id'],
												   'count1' => $count1);
						$sumall += $count1;
					}
					$tongji['sumall'] = $sumall;
					if ($date1<date("Y-m-d")){
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
				
			}
			
			$sum = array(0,0,0);
			foreach ($tongji1 as $key => $val){
				$data1[0] .= ($key==0) ? "'".$val['data']."'" : ",'".$val['data']."'";
				$data1[1] .= ($key==0) ? $val['count1'] : ",".$val['count1'];
				
				$data2[0] .= ($key==0) ? "'".$val['data']."'" : ",'".$val['data']."'";
				$data2[1] .= ($key==0) ? $val['count2'] : ",".$val['count2'];
				
				$data3[0] .= ($key==0) ? "'".$val['data']."'" : ",'".$val['data']."'";
				$data3[1] .= ($key==0) ? $val['count3'] : ",".$val['count3'];
				
				$sum[0] += $val['count1'];
				$sum[1] += $val['count2'];
				//$sum[2] += $val['count3'];
			}
		} 
		
		if ($act == "exceldo1"){
			$xlsName  = "支付方式分布";
			
			$t = 0;
			$xlsCell  = array();
			$xlsCell[$t][0] = 'date';
			$xlsCell[$t++][1] = '日期';
			foreach($res2 as $key2 => $val2){
				$xlsCell[$t][0] = $val2['payment_id'];
				$xlsCell[$t++][1] = $val2['payment_name'];
			}
			$xlsCell[$t][0] = 'payorder';
			$xlsCell[$t++][1] = '支付订单';
			$xlsCell[$t][0] = 'paynum';
			$xlsCell[$t++][1] = '支付人数';
			$xlsCell[$t][0] = 'paytotal';
			$xlsCell[$t++][1] = '总计';
			
			$xlsData = array();
			foreach ($tongji2 as $k => $v)
			{
				$xlsData[$k]['date'] = $v['data'];
				foreach($res2 as $key2 => $val2){
					foreach($v['payment'] as $key3 => $val3){
						if ($val3['payment_id'] == $val2['payment_id']) $xlsData[$k][$val2['payment_id']] = $val3['show_count1'];
					}
				}
				$xlsData[$k]['payorder'] = $v['sum1'];
				$xlsData[$k]['paynum'] = $v['sum2'];
				$xlsData[$k]['paytotal'] = $v['sumshow'];
			}
			exportExcel($xlsName,$xlsCell,$xlsData);
			exit;
		}
		
		if ($act == "exceldo2"){
			$xlsName  = "充值类型分布";
			
			$t = 0;
			$xlsCell  = array();
			$xlsCell[$t][0] = 'date';
			$xlsCell[$t++][1] = '日期';
			foreach($res3 as $key2 => $val2){
				$xlsCell[$t][0] = $val2['goods_id'];
				$xlsCell[$t++][1] = $val2['goods_name']." \n (".$val2['remark'].")";
			}
			$xlsCell[$t][0] = 'ordertotal';
			$xlsCell[$t++][1] = '总计(订单数)';

			
			$xlsData = array();
			foreach ($tongji3 as $k => $v)
			{
				$xlsData[$k]['date'] = $v['data'];
				foreach($res3 as $key2 => $val2){
					foreach($v['goods'] as $key3 => $val3){
						if ($val3['goods_id'] == $val2['goods_id']) $xlsData[$k][$val2['goods_id']] = $val3['count1'];
					}
				}
				$xlsData[$k]['ordertotal'] = $v['sumall'];
			}
			exportExcel($xlsName,$xlsCell,$xlsData);
			exit;
		}
		
		//充值人数
		$time1 = strtotime($date11);
		$time2 = strtotime($date12) + 60 * 60 * 24;
		$sum[2] = $row1->where("order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') $sql1")->count('distinct user_id');
		//print_r($tongji2); print_r($tongji3);
		
		$this->assign('sum',$sum);
		$pagesize = ceil($day_jian / 10);
		$this->assign('pagesize',$pagesize);
		$this->assign('tongji1',json_encode($tongji1));
		$this->assign('tongji2',json_encode($tongji1));
		$this->assign('tongji3',json_encode($tongji1));
		$this->assign('tongji4',json_encode($tongji2));
		$this->assign('tongji5',json_encode($tongji3));
		
		/*引入GoogChart类*/
		import("ORG.Util.GoogChart");
		$chart = new GoogChart();	

		//时间轴\新增账号数据\新增激活数据
		$this->assign('data0',$data0);	
		$this->assign('data1',$data1);	
		$this->assign('data2',$data2);
		$this->assign('data3',$data3);
		$this->assign('data4',$data4);
		
		$this->assign('left_css',"32");
		$this->assign('list',$tongji_show);
		$this->assign('totalall',$totalall);
		
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji1";
		$this->display($lib_display);
	}
	
	//付费玩家数据统计
	public function tongji2(){
		$table = "fx_income_tongji2";
		$row = M($table);
		$table1 = "pay_now_config.zjh_order";
		$row1 = M($table1);
		$table2 = "user_info";
		$row2 = M($table2, '', DB_CONFIG2);
		
		$beginTime = I("beginTime");
		$endTime = I("endTime");
		$channel = I("channel");
		$user_id = I("user_id");
		$act = I("act");
		$id = I("id");
		$sortscate = I("sortscate");
		$sortsflag = I("sortsflag");
		
		if ($act == "info" && !empty($id)){
			$row3 = M("payment");
			$row4 = M("zjh_goods");
			
			import('ORG.Util.Page');
			$count = $row1->where("user_id='$id' and payment_status in ('1','-2') ")->count('id');
			$Page       = new Page($count,PAGE_SHOW);	
			$show       = $Page->show();
			
			$list = $row1->where("user_id='$id' and payment_status in ('1','-2') ")->order("order_create_time desc")->select();
			//dump($row1->_sql());
			foreach ($list as $key => $val){
				$res3 = $row3->field("payment_name")->where("payment_id=".$val['payment_id'])->find();
				$list[$key]['payment_name'] = $res3['payment_name'];
				
				$res4 = $row4->field("goods_name")->where("goods_id=".$val['goods_id'])->find();
				$list[$key]['goods_name'] = $res4['goods_name'];
				
				$list[$key]['money'] = $list[$key]['money'] / 100;
				$list[$key]['result_money'] = $list[$key]['result_money'] / 100;
				$list[$key]['order_pay_time'] = date("Y-m-d H:i:s", $list[$key]['order_pay_time']);
				
			}
			
			$this->assign('user_id',$user_id);
			$this->assign('left_css',"32");
			$this->assign('list',$list);
			$this->assign('pageshow',$show);
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl."/paylist";
			$this->display($lib_display);
			exit;
		}
		
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
		
		$sql1 = "";
		$sql0 = "";
		if (!empty($channel)){
			$sql1 .= " and channel=$channel";
		}
		if (!empty($user_id)){
			$sql1 .= " and user_id=$user_id";
		}
	

		
		$time1 = strtotime($date11) ;
		$time2 = $time1 + 60 * 60 * 24 * $day_jian;
		
		//import('ORG.Util.Page');
		//$count = $row1->where("order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') $sql1")->count('distinct user_id');
		//$Page       = new Page($count,15);//实例化分页类传入总记录数和每页显示的记录数		
		//$show       = $Page->show();// 分页显示输出
		
		$cate1 = array();
		$cate2 = array();
		$cate3 = array();
		$cate4 = array();
		$cate5 = array();
		$cate6 = array();
		$cate7 = array();
		$cate8 = array();
		$list = $row1->field("user_id")->where("order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') $sql1")->group('user_id')->select();
		foreach($list as $key=>$val2){
			//充值次数
			$count1 = $row1->where("order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') and user_id='".$val2['user_id']."' $sql1")->count('id');
			//充值金额
			$count2 = $row1->where("order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') and user_id='".$val2['user_id']."' $sql1")->sum('result_money');
			if (empty($count2)) $count2 = 0; 
			//最后支付日期
			$res3 = $row1->field('order_pay_time')->where("order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') and user_id='".$val2['user_id']."' $sql1")->order('order_pay_time desc')->find();
			$order_pay_time = $res3['order_pay_time'];
			//会员信息
			$res4 = $row2->where("user_id='".$val2['user_id']."'")->find();
			//dump($row2->_sql());
			
			$list[$key]['count1'] = $count1;
			$list[$key]['count2'] = $count2 / 100;
			$list[$key]['gold'] = empty($res4['gold']) ? '0' : $res4['gold'];
			$list[$key]['vip'] = empty($res4['viplevel']) ? '0' : $res4['viplevel'];
			$list[$key]['channel'] = empty($res4['channel']) ? '' : $res4['channel'];
			$list[$key]['register_date'] = empty($res4['register_date']) ? '' : $res4['register_date'];
			$list[$key]['last_login_date'] = empty($res4['last_login_date']) ? '' : $res4['last_login_date'];
			$list[$key]['order_pay_time'] = date("Y-m-d H:i:s",$order_pay_time);
			
			$cate1[$key] = $count1;
			$cate2[$key] = $count2;
			$cate3[$key] = $list[$key]['gold'];
			$cate4[$key] = $res4['vip'];
			$cate5[$key] = $res4['channel'];
			$cate6[$key] = $res4['register_date'];
			$cate7[$key] = $res4['last_login_date'];
			$cate8[$key] = $res3['order_pay_time'];
		}
		if (empty($list)) {
			$list = array();
		} else{
			if (!empty($sortscate) && !empty($sortsflag)){
				if ($sortscate=="1"){
					if ($sortsflag=="1"){
						array_multisort($cate1, SORT_DESC, $list);
					}else{
						array_multisort($cate1, SORT_ASC, $list);
					}
				}elseif ($sortscate=="2"){
					if ($sortsflag=="1"){
						array_multisort($cate2, SORT_DESC, $list);
					}else{
						array_multisort($cate2, SORT_ASC, $list);
					}
				}elseif ($sortscate=="3"){
					if ($sortsflag=="1"){
						array_multisort($cate3, SORT_DESC, $list);
					}else{
						array_multisort($cate3, SORT_ASC, $list);
					}
				}elseif ($sortscate=="4"){
					if ($sortsflag=="1"){
						array_multisort($cate4, SORT_DESC, $list);
					}else{
						array_multisort($cate4, SORT_ASC, $list);
					}
				}elseif ($sortscate=="5"){
					if ($sortsflag=="1"){
						array_multisort($cate5, SORT_DESC, $list);
					}else{
						array_multisort($cate5, SORT_ASC, $list);
					}
				}elseif ($sortscate=="6"){
					if ($sortsflag=="1"){
						array_multisort($cate6, SORT_DESC, $list);
					}else{
						array_multisort($cate6, SORT_ASC, $list);
					}
				}elseif ($sortscate=="7"){
					if ($sortsflag=="1"){
						array_multisort($cate7, SORT_DESC, $list);
					}else{
						array_multisort($cate7, SORT_ASC, $list);
					}
				}elseif ($sortscate=="8"){
					if ($sortsflag=="1"){
						array_multisort($cate8, SORT_DESC, $list);
					}else{
						array_multisort($cate8, SORT_ASC, $list);
					}
				}
				
			}
		}

		$pagesize = ceil($key / PAGE_SHOW);
		$this->assign('pagesize',$pagesize);
		
		$this->assign('left_css',"32");
		$this->assign('list',json_encode($list));
		//$this->assign('pageshow',$show);
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji2";
		$this->display($lib_display);
	}
	
	//付费渗透
	public function tongji3(){
		$table = "fx_income_tongji3";
		$row = M($table);
		$table1 = "pay_now_config.zjh_order";
		$row1 = M($table1);
		
		
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
		
		$sql1 = "";
		$sql2 = "  and !((user_id>=".ROBERT1_BEGIN." and user_id<".ROBERT1_END.") or (user_id>=".ROBERT2_BEGIN." and user_id<=".ROBERT2_END."))";
		$sql0 = "";
		if (!empty($channel)){
			$sql1 .= " and package_id=$channel";
			$sql2 .= " and channel=$channel";
			$sql0 .= " and channel=$channel";
		}else{
			$sql0 .= " and channel='all'";
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
				$row2 = M($table2, '', DB_CONFIG2);
				//echo $date1."<br>";
				//付费率
				$flag = 1;
				$total = $row->where("data='$date1' and flag=$flag $sql0")->count();
				if ($total == 0){
					//当日进行付费的玩家
					$count1 = $row1->where("order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') $sql1")->count('distinct user_id');
					//echo $count1."*"; 
					//dump($row1->_sql());
					//当日活跃玩家
					$count2 = $row2->where("login_date>='$date1' and login_date<'$date2' $sql2 $sql1")->count('distinct user_id');
					//echo $count2."*"; 
					//dump($row2->_sql());
					//日付费率
					$count3 = $count2==0 ? 0 : round($count1/$count2,3) * 100;

					$tongji = array('data' => $date1,
								    'count1' => $count1,
								    'count2' => $count2,
								    'count3' => $count3);
					if ($date1<date("Y-m-d") and date("H")>1){
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
				//exit;
				
				//ARPU
				$flag = 2;
				$total = $row->where("data='$date1' and flag=$flag $sql0")->count();
				if ($total == 0){
					//收入
					$count3 = $row1->where("order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') $sql1")->sum('result_money');
					if (empty($count3)) $count3 = 0; 
					//ARPU
					$count4 = $count2==0 ? 0 : round($count3/100/$count2,3) ;
					//ARPPU
					$count5 = $count1==0 ? 0 : round($count3/100/$count1,3) ;
					
					$tongji = array('data' => $date1,
								    'count1' => $count3,
								    'count2' => $count4,
								    'count3' => $count5);
					if ($date1<date("Y-m-d") and date("H")>1){
						$data9 = array('data' => $date1,
									   'channel' => empty($channel) ? "all" : $channel,
									   'flag' => $flag,
									   'tongji' => json_encode($tongji),
									   'addtime' => time());
						$result = $row->add($data9);			   
					}
					$tongji2[$j] = $tongji;	
				}else{
					$info = $row->where("data='$date1' and flag=$flag $sql0")->find();
					$tongji2[$j] = json_decode($info['tongji'], true);
				}
				
			}
			
			foreach ($tongji1 as $key => $val){
				$data1[0] .= ($key==0) ? "'".$val['data']."'" : ",'".$val['data']."'";
				$data1[1] .= ($key==0) ? $val['count3'] : ",".$val['count3'];
			}
			
			foreach ($tongji2 as $key => $val){
				$data2[0] .= ($key==0) ? "'".$val['data']."'" : ",'".$val['data']."'";
				$data2[1] .= ($key==0) ? $val['count2'] : ",".$val['count2'];
				
				$data3[0] .= ($key==0) ? "'".$val['data']."'" : ",'".$val['data']."'";
				$data3[1] .= ($key==0) ? $val['count3'] : ",".$val['count3'];
			}
		} 
		//print_r($tongji2); print_r($tongji3);
		
		$this->assign('sum',$sum);
		$pagesize = ceil($day_jian / 10);
		$this->assign('pagesize',$pagesize);
		$this->assign('tongji1',json_encode($tongji1));
		$this->assign('tongji2',json_encode($tongji2));
		$this->assign('tongji3',json_encode($tongji2));
		//$this->assign('tongji4',json_encode($tongji2));
		//$this->assign('tongji5',json_encode($tongji3));
		
		/*引入GoogChart类*/
		import("ORG.Util.GoogChart");
		$chart = new GoogChart();	

		//时间轴\新增账号数据\新增激活数据
		$this->assign('data0',$data0);	
		$this->assign('data1',$data1);	
		$this->assign('data2',$data2);
		$this->assign('data3',$data3);
		$this->assign('data4',$data4);
		
		$this->assign('left_css',"32");
		$this->assign('list',$tongji_show);
		$this->assign('totalall',$totalall);
		
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji3";
		$this->display($lib_display);
	}
	
	//付费趋势
	public function tongji4(){
		$table = "fx_income_tongji3";
		$row = M($table);
		$table1 = "pay_now_config.zjh_order";
		$row1 = M($table1);
		$table3 = "fx_user_base";
		$row3 = M($table3);
		$table2 = "user_info";
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
		
		$sql1 = "  and !((user_id>=".ROBERT1_BEGIN." and user_id<".ROBERT1_END.") or (user_id>=".ROBERT2_BEGIN." and user_id<=".ROBERT2_END."))";
		$sql0 = "";
		if (!empty($channel)){
			$sql1 .= " and channel=$channel";
			$sql0 .= " and channel=$channel";
		}else{
			$sql0 .= " and channel='all'";
		}
		
		import('ORG.Util.Page');
		$Page       = new Page($day_jian,PAGE_SHOW);
		$show       = $Page->show();
	
		if ($day_jian >= 0){
			$tongji = array();
			
			$maxi = $day_jian - $Page->firstRow;
			$mini = $day_jian - $Page->firstRow - $Page->listRows;
			if ($mini < 1) $mini = 1;
			//echo $mini."**".$maxi."<br>";
			$t = 0;
			for ($i=$maxi; $i>$mini; $i--){
				
				$time1 = strtotime($date11) + 60 * 60 * 24 * ($i - 1);
				$time2 = $time1 + 60 * 60 * 24;
				$date1 = date("Y-m-d", $time1);
				$date2 = date("Y-m-d", $time2);
				$arr = array(2,3,5,7,10,15,30,60);
				//echo $date1."**".$date2."<br>"; 
				if (!empty($channel)){
					//渠道查询开始
					
					//当天新增玩家
					$res = $row2->field("user_id")->where("register_date>='$date1' and register_date<'$date2' $sql1")->select();
					//dump($row2->_sql());
					$count1 = 0;
					$sql4 = "";
					foreach($res as $key => $val){
						$count1++;
						$sql4 .= (empty($sql4)) ? $val['user_id'] : ",".$val['user_id'];
					}
					if (!empty($sql4)) $sql4 = " and user_id in ($sql4)";
					if ($count1 == 0){
						$count2 = 0;
						$count3 = 0;
					}else{
						//当天付费人数
						$count2 = $row1->where("order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') $sql4")->count('distinct user_id');
						//dump($row1->_sql());
						//当天付费金额
						$count3 = $row1->where("order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') $sql4")->sum('result_money');
						//dump($row1->_sql());
						if (empty($count3)) $count3 = 0;
					}
					
					$tongji[$t] = array('date' => $date1,
										'num' => 1,
										'user_add' => $count1,
										'user_pay_num' => $count2,
										'user_pay_money' => $count3/100);
					$tongji[$t]['sub'] = array();
					$k = 1;
					foreach($arr as $key => $val){
						
						$time31 = $time1 + 60 * 60 * 24 * ($val-1);
						$date31 = date("Y-m-d", $time31);
						$time41 = $time31 + 60 * 60 * 24;
						$date41 = date("Y-m-d", $time41);
						//echo $date31."**".$date12."<br>"; 
						if ($date31 < date("Y-m-d")){
							
							if ($count1 == 0){
								$tongji[$t]['sub'][$k] = array('date' => $date31,
															   'num' => $val,
															   'user_pay_num' => "0",
															   'user_pay_money' => "0");
							}else{
								//当天新增玩家
								/*$res = $row2->field("user_id")->where("register_date>='$date31' and register_date<'$date41' $sql1")->select();
								$count1 = 0;
								$sql5 = "";
								foreach($res as $key => $val){
									$count1++;
									$sql5 .= (empty($sql5)) ? $val['user_id'] : ",".$val['user_id'];
								}
								if (!empty($sql5)) $sql5 = " and user_id in ($sql5)";*/
								
								//当天付费人数
								$count2 = $row1->where("order_create_time>='$time31' and order_create_time<'$time41' and payment_status in ('1','-2') $sql4")->count('distinct user_id');
								//当天付费金额
								$count3 = $row1->where("order_create_time>='$time1' and order_create_time<'$time41' and payment_status in ('1','-2') $sql4")->sum('result_money');
								if (empty($count3)) $count3 = 0;
								
								
								$tongji[$t]['sub'][$k] = array('date' => $date31,
															   'num' => $val,
															   'user_pay_num' => $count2);
								$tongji[$t]['sub'][$k]['user_pay_money'] = $tongji[$t]['user_pay_money'] + $count3/100;
							}
							

						}else{
							$tongji[$t]['sub'][$k] = array('date' => $date31,
														   'num' => $val,
														   'user_pay_num' => "-",
														   'user_pay_money' => "-");
						}
						$k++;
					}
					
					//渠道查询结束
				}else{
					//默认开始
					$res = $row3->where("data='".$date1."'")->find();
					//dump($row3->_sql());
					$tongji[$t] = array('date' => $date1,
										'num' => 1,
										'user_add' => $res['user_add'],
										'user_pay_num' => $res['user_pay_num'],
										'user_pay_money' => $res['user_pay_money']/100);
					
					$tongji[$t]['sub'] = array();
					
					/*
					$k = 1;
					foreach($arr as $key => $val){
						$time31 = $time1 + 60 * 60 * 24 * ($val-1);
						$date31 = date("Y-m-d", $time31);
						//echo $date31."**".$date12."<br>"; 
						if ($date31 < date("Y-m-d")){
							$res = $row3->where("data='".$date31."'")->find();
							$tongji[$t]['sub'][$k] = array('date' => $date31,
														   'num' => $val,
														   'user_pay_num' => $res['user_pay_num']);
							$tongji[$t]['sub'][$k]['user_pay_money'] = $tongji[$t]['user_pay_money'];
							for($j=$val-1; $j>=1; $j--){
								$time12 = $time1 + 60 * 60 * 24 * $j;
								$date12 = date("Y-m-d", $time12);
								$res = $row3->where("data='".$date12."'")->find();
								$tongji[$t]['sub'][$k]['user_pay_money'] += $res['user_pay_money']/100;
							}
						}else{
							$tongji[$t]['sub'][$k] = array('date' => $date31,
														   'num' => $val,
														   'user_pay_num' => "-",
														   'user_pay_money' => "-");
						}
						$k++;
					}*/
					
					
					$res = $row2->field("user_id")->where("register_date>='$date1' and register_date<'$date2' $sql1")->select();
					$sql4 = "";
					foreach($res as $key => $val){
						$sql4 .= (empty($sql4)) ? $val['user_id'] : ",".$val['user_id'];
					}
					if (!empty($sql4)) $sql4 = " and user_id in ($sql4)";
					$k = 1;
					foreach($arr as $key => $val){
						$time31 = $time1 + 60 * 60 * 24 * ($val-1);
						$date31 = date("Y-m-d", $time31);
						$time41 = $time31 + 60 * 60 * 24;
						$date41 = date("Y-m-d", $time41);
						//echo $date31."**".$date12."<br>"; 
						if ($date31 < date("Y-m-d")){
							//当天新增玩家
							/*$res = $row2->field("user_id")->where("register_date>='$date31' and register_date<'$date41' $sql1")->select();
							$count1 = 0;
							$sql5 = "";
							foreach($res as $key => $val){
								$count1++;
								$sql5 .= (empty($sql5)) ? $val['user_id'] : ",".$val['user_id'];
							}
							if (!empty($sql5)) $sql5 = " and user_id in ($sql5)";*/
							
							//当天付费人数
							$count2 = $row1->where("order_create_time>='$time31' and order_create_time<'$time41' and payment_status in ('1','-2') $sql4")->count('distinct user_id');
							//当天付费金额
							$count3 = $row1->where("order_create_time>='$time1' and order_create_time<'$time41' and payment_status in ('1','-2') $sql4")->sum('result_money');
							if (empty($count3)) $count3 = 0;
							
							
							$tongji[$t]['sub'][$k] = array('date' => $date31,
														   'num' => $val,
														   'user_pay_num' => $count2);
							$tongji[$t]['sub'][$k]['user_pay_money'] = $tongji[$t]['user_pay_money'] + $count3/100;

						}else{
							$tongji[$t]['sub'][$k] = array('date' => $date31,
														   'num' => $val,
														   'user_pay_num' => "-",
														   'user_pay_money' => "-");
						}
						$k++;
					}
					//默认结束
				}
				
				
				$t++;
			}
		} 
		//print_r($tongji); //print_r($tongji3);
		//exit;

		$this->assign('list',$tongji);
		$this->assign('pageshow',$show);
		
		$this->assign('left_css',"32");
		$this->assign('totalall',$totalall);
		
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji4";
		$this->display($lib_display);
	}
	
	//LTV
	public function tongji5(){
		$table = "fx_income_tongji3";
		$row = M($table);
		$table1 = "pay_now_config.zjh_order";
		$row1 = M($table1);
		$table2 = "user_info";
		$row2 = M($table2, '', DB_CONFIG2);
		$table3 = "fx_user_base";
		$row3 = M($table3);
		
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
		
		$sql1 = "  and !((user_id>=".ROBERT1_BEGIN." and user_id<".ROBERT1_END.") or (user_id>=".ROBERT2_BEGIN." and user_id<=".ROBERT2_END."))";
		$sql0 = "";
		if (!empty($channel)){
			$sql1 .= " and channel=$channel";
			$sql0 .= " and channel=$channel";
		}else{
			$sql0 .= " and channel='all'";
		}
		
		import('ORG.Util.Page');
		$Page       = new Page($day_jian,PAGE_SHOW);
		$show       = $Page->show();
	
		if ($day_jian >= 0){
			$tongji = array();
			
			$maxi = $day_jian - $Page->firstRow;
			$mini = $day_jian - $Page->firstRow - $Page->listRows;
			if ($mini < 1) $mini = 1;
			//echo $mini."**".$maxi."<br>";
			$t = 0;
			for ($i=$maxi; $i>$mini; $i--){
				
				$time1 = strtotime($date11) + 60 * 60 * 24 * ($i - 1);
				$time2 = $time1 + 60 * 60 * 24;
				$date1 = date("Y-m-d", $time1);
				$date2 = date("Y-m-d", $time2);
				//echo $date1."**".$date2."<br>"; 
				$tongji[$t] = array('date' => $date1);
				$tongji[$t]['sub'] = array();
				if (!empty($channel)){
					//渠道查询开始
					$k = 1;
					//当天新增玩家
					$res = $row2->field("user_id")->where("register_date>='$date1' and register_date<'$date2' $sql1")->select();
					$count1 = 0;
					$sql4 = "";
					foreach($res as $key => $val1){
						$count1++;
						$sql4 .= (empty($sql4)) ? $val1['user_id'] : ",".$val1['user_id'];
					}
					if (!empty($sql4)) $sql4 = " and user_id in ($sql4)";
					for($val=1; $val<=15; $val++){
						$time31 = $time1 + 60 * 60 * 24 * ($val-1);
						$date31 = date("Y-m-d", $time31);
						$time41 = $time31 + 60 * 60 * 24;
						$date41 = date("Y-m-d", $time41);
						//echo "<br><br>".$date31."<br>"; 
						if ($date31 < date("Y-m-d")){
							//$res = $row3->where("data='".$date31."'")->find();
							
							
							$tongji[$t]['sub'][$k] = array('date' => $date31,
														   'num' => $val,
														   'user_add' => $count1);
							//当天付费金额
							$count3 = $row1->where("order_create_time>='$time1' and order_create_time<'$time41' and payment_status in ('1','-2') $sql4")->sum('result_money');
							//dump($row1->_sql());
							if (empty($count3)) $count3 = 0;
							$tongji[$t]['sub'][$k]['user_pay_money'] = $count3/100;
							//echo "<br>";
							//if ($date31=="2015-11-04") echo $date31."**".$count1."**".$tongji[$t]['sub'][$k]['user_pay_money']."<br>";
							$tongji[$t]['sub'][$k]['ltv'] = (empty($count1)) ? 0 : round($tongji[$t]['sub'][$k]['user_pay_money']/$count1,2);
						}else{
							$tongji[$t]['sub'][$k] = array('date' => $date31,
														   'num' => $val,
														   'user_add' => "-",
														   'user_pay_money' => "-",
														   'ltv' => "-");
						}
						$k++;
					}
					//渠道查询结束
				}else{
					//默认开始
					//$res = $row3->where("data='".$date1."'")->find();
					//dump($row3->_sql());
					$k = 1;
					$res = $row2->field("user_id")->where("register_date>='$date1' and register_date<'$date2' $sql1")->select();
					$count1 = 0;
					$sql4 = "";
					foreach($res as $key => $val1){
						$count1++;
						$sql4 .= (empty($sql4)) ? $val1['user_id'] : ",".$val1['user_id'];
					}
					if (!empty($sql4)) $sql4 = " and user_id in ($sql4)";
					for($val=1; $val<=15; $val++){
						/*
						$time31 = $time1 + 60 * 60 * 24 * ($val-1);
						$date31 = date("Y-m-d", $time31);
						//echo "<br><br>".$date31."<br>"; 
						if ($date31 < date("Y-m-d")){
							$res = $row3->where("data='".$date31."'")->find();
							$tongji[$t]['sub'][$k] = array('date' => $date31,
														   'num' => $val,
														   'user_add' => $res['user_add']);
							$tongji[$t]['sub'][$k]['user_pay_money'] = 0;
							for($j=$val; $j>=1; $j--){
								$time12 = $time1 + 60 * 60 * 24 * ($j-1);
								$date12 = date("Y-m-d", $time12);
								//echo $date12."**"; 
								$res = $row3->where("data='".$date12."'")->find();
								$tongji[$t]['sub'][$k]['user_pay_money'] += $res['user_pay_money']/100;
							}
							//echo "<br>";
							//if ($date31=="2015-11-04") echo $date31."**".$res['user_add']."**".$tongji[$t]['sub'][$k]['user_pay_money']."<br>";
							$tongji[$t]['sub'][$k]['ltv'] = (empty($res['user_add'])) ? 0 : round($tongji[$t]['sub'][$k]['user_pay_money']/$res['user_add'],2);
						}else{
							$tongji[$t]['sub'][$k] = array('date' => $date31,
														   'num' => $val,
														   'user_add' => "-",
														   'user_pay_money' => "-",
														   'ltv' => "-");
						}*/
						$time31 = $time1 + 60 * 60 * 24 * ($val-1);
						$date31 = date("Y-m-d", $time31);
						$time41 = $time31 + 60 * 60 * 24;
						$date41 = date("Y-m-d", $time41);
						//echo "<br><br>".$date31."<br>"; 
						if ($date31 < date("Y-m-d")){
							//$res = $row3->where("data='".$date31."'")->find();
							
							
							$tongji[$t]['sub'][$k] = array('date' => $date31,
														   'num' => $val,
														   'user_add' => $count1);
							//当天付费金额
							$count3 = $row1->where("order_create_time>='$time1' and order_create_time<'$time41' and payment_status in ('1','-2') $sql4")->sum('result_money');
							//dump($row1->_sql());
							if (empty($count3)) $count3 = 0;
							$tongji[$t]['sub'][$k]['user_pay_money'] = $count3/100;
							//echo "<br>";
							//if ($date31=="2015-11-04") echo $date31."**".$count1."**".$tongji[$t]['sub'][$k]['user_pay_money']."<br>";
							$tongji[$t]['sub'][$k]['ltv'] = (empty($count1)) ? 0 : round($tongji[$t]['sub'][$k]['user_pay_money']/$count1,2);
						}else{
							$tongji[$t]['sub'][$k] = array('date' => $date31,
														   'num' => $val,
														   'user_add' => "-",
														   'user_pay_money' => "-",
														   'ltv' => "-");
						}
						
						$k++;
					}
					//默认结束
				}		
				
				$t++;
			}
		} 
		//print_r($tongji); //print_r($tongji3);
		//exit;

		$this->assign('list',$tongji);
		$this->assign('pageshow',$show);
		
		$this->assign('left_css',"32");
		$this->assign('totalall',$totalall);
		
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji5";
		$this->display($lib_display);
	}
	
	//用户列表
	public function chong(){
		//重建缓存
		//$this->cache_add();
		$orderid = I("orderid");
		$userid = I("userid");
		$act = I("act");
		
		if ($act == "exceldo"){
			$xlsName  = "异常订单";
			$xlsCell  = array(
			array('order_code','订单号'),
			array('user_id','用户UID'),
			array('package_id','渠道号'),
			array('goods_id','商品ID'),
			array('payment_id','支付方式'),
			array('money','支付金额'),
			array('result_money','实际金额'),
			array('notify_status','通知状态'),
			array('notify_times','通知次数'),
			array('notify_date','通知日期')   
			);
			$xlsData = array();
			$table = M('pay_now_config.zjh_order');
			$list = $table->where("(payment_id = '106' ) AND (payment_status = '-2' ) AND ( (order_create_time > 1453996800) AND (order_create_time < 1454342400) )")->order('id desc')->select();
			foreach ($list as $k => $v)
			{
				$xlsData[$k]['order_code'] = " ".$v['order_code']." ";
				$xlsData[$k]['user_id'] = $v['user_id'];
				$xlsData[$k]['package_id'] = $v['package_id'];
				$xlsData[$k]['goods_id'] = $v['goods_id'];
				$xlsData[$k]['payment_id'] = $v['payment_id'];
				$xlsData[$k]['money'] = $v['money'];
				$xlsData[$k]['result_money'] = $v['result_money'];
				$xlsData[$k]['notify_status'] = $v['notify_status'];
				$xlsData[$k]['notify_times'] = $v['notify_times'];
				$xlsData[$k]['notify_date'] = $v['notify_date'];
			}
			exportExcel($xlsName,$xlsCell,$xlsData);
			exit;
		}
		
		$sql1 = "";
		if (!empty($orderid)) $sql1 .= " and orderid like '%$orderid%'";
		if (!empty($userid)) $sql1 .= " and userid like '%$userid%'";
		
		$employee = M('user_pay_log', '', DB_CONFIG2);
		import('ORG.Util.Page');
		$count = $employee->where('success=1'.$sql1)->count('orderid');
		$Page       = new Page($count,20);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		
		$list = $employee->where('success=1'.$sql1)->order('pay_date desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		
		foreach($list as $key=>$value){
			
			$list[$key]['money'] = $value['money']/100;
		}
		
		$this->assign('left_css',"1");
		$this->assign('list',$list);
		$this->assign('pageshow',$show);
		
		$this->display($this->By_tpl.'/chong');
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