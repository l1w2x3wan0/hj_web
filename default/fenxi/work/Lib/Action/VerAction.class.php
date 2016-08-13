<?php
// 运营分析文件

class VerAction extends BaseAction {

	protected $By_tpl = 'Ver'; 
	
	public function tongji1(){
		$table = "fx_tongji5";
		$row = M($table);
		
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
			$date11 = date("Y-m-d", (strtotime($date12) - 60 * 60 * 24 * 6));
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
		$sql1 = " and !((user_id>=".ROBERT1_BEGIN." and user_id<".ROBERT1_END.") or (user_id>=".ROBERT2_BEGIN." and user_id<=".ROBERT2_END."))";
		if (!empty($channel)){
			$sql1 .= " and channel=$channel";
			$sql0 .= " and channel=$channel";
		}else{
			$sql0 .= " and channel='all'";
		}
		if (!empty($version)){
			$sql0 .= " and version=$version";
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
			
			$table1 = "user_info";
			$row1 = M($table1, '', DB_CONFIG2);
			$table2 = "game_version_total";
			$row2 = M($table2, '', DB_CONFIG3);
			
			for ($i=1; $i<=$day_jian; $i++){
				$j = $i - 1;	
				$time1 = strtotime($date11) + 60 * 60 * 24 * ($i - 1);
				$time2 = $time1 + 60 * 60 * 24;
				$date1 = date("Y-m-d", $time1);
				$date2 = date("Y-m-d", $time2);
			
				$flag = 12;
				$total = $row->where("data='$date1' and flag=$flag $sql0")->count();
				//dump($row->_sql());
				if ($total == 0){
					
					$str = array();
					$res = $row2->where("date='$date1'")->select();
					//$res = $row1->field('version')->where("register_date>='$date11' and register_date<'$date12 23:59:59' $sql1")->group('version')->select();
					//dump($row2->_sql());
					//print_r($res);
					foreach($res as $key => $val){
						$str[$key]['version'] = $val['gameversion'];
						//新增用户
						$str[$key]['count1'] = $val['count1'];
						//活跃用户
						$str[$key]['count2'] = $val['count2'];
						//启动次数
						$str[$key]['count3'] = $val['count3'];
					}
					
					$tongji = array('data' => $date1,
								    'str' => $str);
					if ($date1<date("Y-m-d")){
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
			$str1 = array();
			$str2 = array();
			foreach ($tongji1 as $key => $val){
				$data1[0] .= ($key==0) ? "'".$val['data']."'" : ",'".$val['data']."'";
				//echo $data1[0]."**";
				
				foreach($val['str'] as $key1 => $val1){
					$str1[$key1] = $val1['version'];
					$str2[$key1] .= ($str2[$key1]=="") ? $val1['count1'] : ",".$val1['count1'];
					//echo $key."**".$key1."**".$str1[$key1]."**".$str2[$key1]."<br>";
				}
				if ($day_jian-1==$key){
					foreach($val['str'] as $key1 => $val1){
						$data1[1] .= ($key1==0) ? "" : ",";
						$data1[1] .= "{name: '".$str1[$key1]."',data: [".$str2[$key1]."]}";
					}
				}
				
			}

			$str1 = array();
			$str2 = array();
			foreach ($tongji1 as $key => $val){
				$data2[0] .= ($key==0) ? "'".$val['data']."'" : ",'".$val['data']."'";
				//echo $data1[0]."**";
				
				foreach($val['str'] as $key1 => $val1){
					$str1[$key1] = $val1['version'];
					$str2[$key1] .= ($str2[$key1]=="") ? $val1['count2'] : ",".$val1['count2'];
					//echo $key."**".$key1."**".$str1[$key1]."**".$str2[$key1]."<br>";
				}
				if ($day_jian-1==$key){
					foreach($val['str'] as $key1 => $val1){
						$data2[1] .= ($key1==0) ? "" : ",";
						$data2[1] .= "{name: '".$str1[$key1]."',data: [".$str2[$key1]."]}";
					}
				}
				
			}
			
			$str1 = array();
			$str2 = array();
			foreach ($tongji1 as $key => $val){
				$data3[0] .= ($key==0) ? "'".$val['data']."'" : ",'".$val['data']."'";
				//echo $data1[0]."**";
				
				foreach($val['str'] as $key1 => $val1){
					$str1[$key1] = $val1['version'];
					$str2[$key1] .= ($str2[$key1]=="") ? $val1['count3'] : ",".$val1['count3'];
					//echo $key."**".$key1."**".$str1[$key1]."**".$str2[$key1]."<br>";
				}
				if ($day_jian-1==$key){
					foreach($val['str'] as $key1 => $val1){
						$data3[1] .= ($key1==0) ? "" : ",";
						$data3[1] .= "{name: '".$str1[$key1]."',data: [".$str2[$key1]."]}";
					}
				}
				
			}
			
			//print_r($data1);
			//print_r($data2);
			//print_r($data3);
			//exit;
		} 

		$pagesize = ceil($day_jian / 10);
		$this->assign('pagesize',$pagesize);
		$this->assign('tongji1',json_encode($tongji1));
		
		/*引入GoogChart类*/
		import("ORG.Util.GoogChart");
		$chart = new GoogChart();	

		//时间轴\新增账号数据\新增激活数据
		$this->assign('data0',$data0);	
		$this->assign('data1',$data1);	
		$this->assign('data2',$data2);
		$this->assign('data3',$data3);
		
		$table = "game_version";
		$version_model = M($table, '', DB_CONFIG3);
		$version_now = $version_model->field("GROUP_CONCAT(version) as version_now")->find();
		$this->assign('version_now',$version_now['version_now']);	
		
		$this->assign('left_css',"63");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji1";
		$this->display($lib_display);
	}
	
	public function addversion(){
		$table = "game_version";
		$version_model = M($table, '', DB_CONFIG3);
		
		$version = I("version");
	
		if (!empty($version)){
			
			$data = array('version' => $version);
			$result = $version_model->add($data);
			if ($result){
				
				$lib_display = $this->By_tpl."/addversion";
				$this->success('添加成功',U($lib_display));
			}else{
				
				$this->error('添加失败');
				exit;
			}
		
		}else{
			
			$sql0 = "1";
			import('ORG.Util.Page');
			$count = $version_model->where($sql0)->count('id');
			$Page = new Page($count,20);//实例化分页类传入总记录数和每页显示的记录数		
			$show       = $Page->show();// 分页显示输出
			$list = $version_model->where($sql0)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
			
			$this->assign('list',$list);
			$this->assign('pageshow',$show);
			
			$this->assign('left_css',"63");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl."/addversion";
			$this->display($lib_display);
		}

	}
	
	public function version_delete(){
		$table = "game_version";
		$version_model = M($table, '', DB_CONFIG3);
		
		$id = I("id");
	
		if (!empty($id)){
			
			$where['id'] = $id;
			$result = $version_model->where($where)->delete();
			if($result){
				
				$this->success('删除成功');
			}else{
				
				$this->error('删除失败');
				exit;
			}
		
		}

	}
	
	
	public function tongji2(){
		$table = "fx_tongji5";
		$row = M($table);
	
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
			$day_jian = 10;
			$date11 = date("Y-m-d", (strtotime($date12) - 60 * 60 * 24 * ($day_jian - 1)));
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
		
		$sql1 = " and !((user_id>=".ROBERT1_BEGIN." and user_id<".ROBERT1_END.") or (user_id>=".ROBERT2_BEGIN." and user_id<=".ROBERT2_END."))";
		$sql0 = "";
		$sql2 = " and !((user_id>=".ROBERT1_BEGIN." and user_id<".ROBERT1_END.") or (user_id>=".ROBERT2_BEGIN." and user_id<=".ROBERT2_END."))";
		if (!empty($channel)){
			$sql1 .= " and channel='$channel'";
			$sql0 .= " and channel='$channel'";
			$sql2 .= " and channel='$channel'";
		}else{
			$sql0 .= " and channel='all'";
		}
		if (!empty($version)){
			$sql1 .= " and version='$version'";
			$sql0 .= " and version='$version'";
			$sql2 .= " and version_new='$version'";
		}else{
			$sql0 .= " and version='all'";
		}
		
		import('ORG.Util.Page');
		$Page       = new Page($day_jian,PAGE_SHOW);
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
				$table1 = "user_info";
				$row1 = M($table1, '', DB_CONFIG2);
				$table2 = "game_version_total";
				$row2 = M($table2, '', DB_CONFIG3);
				$table3 = "user_base";
				$row3 = M($table3, '', DB_CONFIG3);
				
				$flag = "3";
				$total = $row->where("data='$date1' and flag=$flag $sql0")->count();
				//dump($row->_sql());
				if ($total == 0){
					$tongji = array();
					
					//所有用户
					$info = $row3->where("key_adddate='$date1' and key_name='count1'")->find();
					$count0 = $info['key_value'];
					//dump($row1->_sql());
					//echo $count0; 
					//当天总活跃
					$info = $row3->where("key_adddate='$date1' and key_name='count5'")->find();
					$huoyue = $info['key_value'];
					//dump($row2->_sql());
					//echo "**".$huoyue."<br>";
					//所有版本
					$res = $row2->where("date='$date1'")->select();
					//if ($date1=='2015-11-25') dump($row2->_sql());
					foreach ($res as $key => $val){

							//累计
							$count1 = $val['count4'];
							//if ($date1=='2015-11-25') dump($row1->_sql());
							//比例
							$count2 = ($count0==0) ? 0 : round($count1/$count0,3)*100;
							//新增用户
							$count3 = $val['count1'];
							//升级用户
							$count4 = $val['count5'];
							//dump($row3->_sql());
							//新增+升级
							$count5 = $count3 + $count4;
							//活跃用户
							$count6 = $val['count2'];
							//比例
							$count7 = ($huoyue==0) ? 0 : round($count6/$huoyue,3)*100;
							//启动次数
							$count8 = $val['count3'];
							
							$tongji[$key] = array('data' => $date1,
												  'version' => $val['gameversion'],
												  'count1' => $count1,
												  'count2' => $count2."%",
												  'count3' => $count3,
												  'count4' => $count4,
												  'count5' => $count5,
												  'count6' => $count6,
												  'count7' => $count7."%",
												  'count8' => $count8);
						
					}

					$tongji0 = array('data' => $date1,
									 'tongji' => $tongji);
									
					if ($date1<date("Y-m-d")){
						/*$data9 = array('data' => $date1,
									   'channel' => empty($channel) ? "all" : $channel,
									   'flag' => $flag,
									   'tongji' => json_encode($tongji0),
									   'addtime' => time());
						$result = $row->add($data9);	*/		   
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
		$this->assign('left_css',"63");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji2";
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