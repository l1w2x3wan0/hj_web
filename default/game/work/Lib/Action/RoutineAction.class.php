<?php
// 老虎机管理的文件

class RoutineAction extends BaseAction {
	protected $By_tpl = 'Routine'; 
	protected $Table_prifix = MYTABLE_PRIFIX; 
	
	//发牌参数配置开始
	public function Pconfig(){
		$table1 = $this->Table_prifix."profile_control_room_cards_config";
		$row1 = M($table1);
		
		if(!empty($_POST)){
			
			$remark = $_POST['remark'];
			if (empty($remark)){
				$this->error('修改原因必须填写');
				exit;
			}
			
			$res = $row1->field("roomtype")->select();
			foreach($res as $key => $val){
				$i = $val['roomtype'];
				$show1 = "delminnum".$i;
				$show2 = "delmax1rate".$i;
				$show3 = "delmax2rate".$i;
				$show4 = "delmax3rate".$i;
				$show5 = "playcount1".$i;
				$show6 = "startpos1".$i;
				$show7 = "playcount2".$i;
				$show8 = "startpos2".$i;
				$show9 = "playcount3".$i;
				$show10 = "startpos3".$i;
				$show11 = "playcount4".$i;
				$show12 = "startpos4".$i;
				$show13 = "playcount5".$i;
				$show14 = "startpos5".$i;
				

				if ($_POST[$show1] < 0 || $_POST[$show1] > 4){
						$this->error('最小注数范围0~4,勿超');
						exit;
				}

				if ($_POST[$show6] < 0 || $_POST[$show6] > 5){
						$this->error('幸运值≤5,否则提交失败');
						exit;
				}
				
				if ($_POST[$show8] < 0 || $_POST[$show8] > 5){
						$this->error('幸运值≤5,否则提交失败');
						exit;
				}
				
				if ($_POST[$show10] < 0 || $_POST[$show10] > 5){
						$this->error('幸运值≤5,否则提交失败');
						exit;
				}
				
				if ($_POST[$show12] < 0 || $_POST[$show12] > 5){
						$this->error('幸运值≤5,否则提交失败');
						exit;
				}
				
				if ($_POST[$show14] < 0 || $_POST[$show14] > 5){
						$this->error('幸运值≤5,否则提交失败');
						exit;
				}

				
				if (!empty($_POST[$show7])){
					if ($_POST[$show7] < $_POST[$show5]){
						$this->error('玩牌局数2必须大于玩牌局数1');
						exit;
					}
				}
				
				if (!empty($_POST[$show9])){
					if ($_POST[$show9] < $_POST[$show7]){
						$this->error('玩牌局数3必须大于玩牌局数2');
						exit;
					}
				}
				
				if (!empty($_POST[$show11])){
					if ($_POST[$show11] < $_POST[$show9]){
						$this->error('玩牌局数4必须大于玩牌局数3');
						exit;
					}
				}
				
				if (!empty($_POST[$show13])){
					if ($_POST[$show13] < $_POST[$show11]){
						$this->error('玩牌局数5必须大于玩牌局数4');
						exit;
					}
				}
				/*
				$data9 = array();
				$data9 = array('delminnum' => $_POST[$show1],
							   'delmax1rate' => $_POST[$show2],
							   'delmax2rate' => $_POST[$show3],
							   'delmax3rate' => $_POST[$show4],
							   'playcount1' => $_POST[$show5],
							   'startpos1' => $_POST[$show6],
							   'playcount2' => $_POST[$show7],
							   'startpos2' => $_POST[$show8],
							   'playcount3' => $_POST[$show9],
							   'startpos3' => $_POST[$show10],
							   'playcount4' => $_POST[$show11],
							   'startpos4' => $_POST[$show12],
							   'playcount5' => $_POST[$show13],
							   'startpos5' => $_POST[$show14]);
				$result = $row1->where("roomtype=".$i)->save($data9);	
				*/

			}
			
			unset($_POST['__hash__']);
			unset($_POST['Submit']);
			
			//增加操作记录
			$logs = C('FAPAI_MSG_EDIT_SUCCESS');
			$remark2 = "(".json_encode($_POST, JSON_UNESCAPED_UNICODE).")";
			adminlog($logs, $remark2);
			
			//增加修改记录
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = $remark;
			$data['userip'] = get_client_ip();
			$data['cate'] = 3;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$table_name->add($data);
			
			//通知服务器
			//$url = DB_HOST."/Pay/shang.php?showindex=4&showtype=4";
			//$jinbi_result = curlGET($url);
			
			$this->success('提交成功，等待审核',U($this->By_tpl.'/Pconfig'));
			exit;
		}
		
		$list = $row1->order('roomtype')->select();
		//print_r($list);
		$this->assign('list',$list);
		
		//增加操作记录
		$logs = C('FAPAI_MSG_RECORD');
		$remark = "";
		adminlog($logs, $remark);
		
		$this->assign('left_css',"28");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl.":Pconfig";
		$this->display($lib_display);
	}
	//发牌参数配置结束
	
	//房间配置开始
	public function Rconfig(){
		$table1 = $this->Table_prifix."room_config";
		$row1 = M($table1);
		$table2 = $this->Table_prifix."dynamic_config";
		$row2 = M($table2);
		$table3 = $this->Table_prifix."room_gold_code";
		$row3 = M($table3);
		
		if(!empty($_POST)){
			
			$remark = $_POST['remark'];
			if (empty($remark)){
				$this->error('修改原因必须填写');
				exit;
			}
			
			/*			
			$res = $row1->field("room_id")->where("room_id<4")->select();
			foreach($res as $key => $val){
				$i = $val['room_id'];
				$show1 = "displayorder".$i;
				$show2 = "cell_gold".$i;
				$show3 = "lower_limit_gold".$i;
				$show4 = "high_limit_gold".$i;
				$show5 = "min_cmp_round".$i;
				$show6 = "max_round".$i;
				$show7 = "tax_rate".$i;

				$data9 = array();
				$data9 = array('displayorder' => $_POST[$show1],
							   'cell_gold' => $_POST[$show2],
							   'lower_limit_gold' => $_POST[$show3],
							   'high_limit_gold' => $_POST[$show4],
							   'min_cmp_round' => $_POST[$show5],
							    'tax_rate' => $_POST[$show7],
							   'max_round' => ($_POST[$show6] - 1));
				$result = $row1->where("room_id=".$i)->save($data9);	

			}*/
			
			unset($_POST['__hash__']);
			unset($_POST['Submit']);
			
			//增加操作记录
			$logs = C('ROOM_MSG_EDIT_SUCCESS');
			$remark2 = "(".json_encode($_POST, JSON_UNESCAPED_UNICODE).")";
			adminlog($logs, $remark2);
			
			//增加修改记录
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = $remark;
			$data['userip'] = get_client_ip();
			$data['cate'] = 4;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$table_name->add($data);
			
			//通知服务器
			//$url = DB_HOST."/Pay/shang.php?showindex=2&showtype=5";
			//$jinbi_result = curlGET($url);
			
			$this->success('提交成功，等待审核',U($this->By_tpl.'/Rconfig'));
			exit;
		}
		
		
		$room = S("GAMEBASE_ROOM_WEB");
		//$version = I("version");
		//$version = (empty($version)) ? 2 : $version; 
		//$this->assign('version',$version);
		
		$list = $row1->order('displayorder')->select();
		foreach($list as $key => $val){
			$list[$key]['actionTime'] = !empty($room["actionTime".$val['room_id']]) ? $room["actionTime".$val['room_id']] : 20;
			$list[$key]['online'] = !empty($room["online".$val['room_id']]) ? $room["online".$val['room_id']] : rand(100,400);
			
			$list[$key]['vmSteps'] = $row3->where('room_id='.$val['room_id'])->select();
			if (empty($list[$key]['vmSteps'])){
				for($t=0; $t<5; $t++){
					$list[$key]['vmSteps'][$t] = array('code_id'=>0,'gold_code'=>0,'room_id'=>0);
				}
				
			}
		}
		//print_r($list);
		$this->assign('list',$list);
		
		
		
		//增加操作记录
		$logs = C('ROOM_MSG_RECORD');
		$remark = "";
		adminlog($logs, $remark);
		
		$this->assign('left_css',"28");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl.":Rconfig";
		$this->display($lib_display);
	}
	//房间配置结束
	
	//常规配置开始
	public function Oconfig(){
		$table1 = $this->Table_prifix."room_config";
		$row1 = M($table1);
		$table2 = $this->Table_prifix."dynamic_config";
		$row2 = M($table2);
		
		if(!empty($_POST)){
			
			$remark = $_POST['remark'];
			if (empty($remark)){
				$this->error('修改原因必须填写');
				exit;
			}
			
			$registergivegold = I("registergivegold");
			$bindphone = I("bindphone");
			$bankruptcy_num = I("bankruptcy_num");
			$bankruptcy_gold = I("bankruptcy_gold");
			$viptablelevel = I("viptablelevel");
			$quickpaytips = I("quickpaytips");
			/*
			$data9 = array();
			$data9 = array('key_value' => $registergivegold);
			$result = $row2->where("key_name='registergivegold'")->save($data9);	
			$data9 = array();
			$data9 = array('key_value' => $bindphone);
			$result = $row2->where("key_name='bindphone'")->save($data9);
			$data9 = array();
			$data9 = array('key_value' => $bankruptcy_num);
			$result = $row2->where("key_name='bankruptcy_num'")->save($data9);
			$data9 = array();
			$data9 = array('key_value' => $bankruptcy_gold);
			$result = $row2->where("key_name='bankruptcy_gold'")->save($data9);
			$data9 = array();
			$data9 = array('key_value' => $privetableviplevel);
			$result = $row2->where("key_name='privetableviplevel'")->save($data9);
			$data9 = array();
			$data9 = array('key_value' => $quickpaytips);
			$result = $row2->where("key_name='quickpaytips'")->save($data9);
			*/
			
			unset($_POST['__hash__']);
			unset($_POST['Submit']);
			
			//增加操作记录
			$logs = C('BASE_MSG_EDIT_SUCCESS');
			$remark2 = "(".json_encode($_POST, JSON_UNESCAPED_UNICODE).")";
			adminlog($logs, $remark2);
			
			//增加修改记录
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = $remark;
			$data['userip'] = get_client_ip();
			$data['cate'] = 5;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$table_name->add($data);
			
			//通知服务器
			//$url = DB_HOST."/Pay/shang.php?showindex=5&showtype=4";
			//$jinbi_result = curlGET($url);
			
			$this->success('提交成功，等待审核',U($this->By_tpl.'/Oconfig'));
			exit;
		}
		
		//$list = $row1->where("room_id<4")->order('displayorder')->select();
		//print_r($list);
		//$this->assign('list',$list);
		
		$info = $row2->select();
		foreach($info as $key => $val){
			$this->assign($val['key_name'],$val['key_value']);
		}
		
		
		//增加操作记录
		$logs = C('BASE_MSG_RECORD');
		$remark = "";
		adminlog($logs, $remark);
		
		$this->assign('left_css',"28");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl.":Oconfig";
		$this->display($lib_display);
	}
	//常规配置结束
	
	//任务配置开始
	public function renwu(){
		$table1 = $this->Table_prifix."task_daily_task_config";
		$row1 = M($table1);
		
		if(!empty($_POST)){
			
			$remark = $_POST['remark'];
			if (empty($remark)){
				$this->error('修改原因必须填写');
				exit;
			}
			
			unset($_POST['__hash__']);
			unset($_POST['Submit']);
			
			//增加操作记录
			$logs = C('RENWU_MSG_EDIT_SUCCESS');
			$remark2 = "(".json_encode($_POST, JSON_UNESCAPED_UNICODE).")";
			adminlog($logs, $remark2);
			
			//增加修改记录
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = $remark;
			$data['userip'] = get_client_ip();
			$data['cate'] = 9;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$table_name->add($data);
			
			
			$this->success('提交成功，等待审核',U($this->By_tpl.'/renwu'));
			exit;
		}
		
		$list = $row1->order('taskid')->select();
		$this->assign('list',$list);
		
		//增加操作记录
		$logs = C('RENWU_MSG_RECORD');
		$remark = "";
		adminlog($logs, $remark);
		
		$this->assign('left_css',"28");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl.":renwu";
		$this->display($lib_display);
	}
	//任务配置结束
	
	//礼物配置开始
	public function gift(){
		$table1 = $this->Table_prifix."profile_gift_config";
		$row1 = M($table1);
		
		if(!empty($_POST)){
			
			$remark = $_POST['remark'];
			if (empty($remark)){
				$this->error('修改原因必须填写');
				exit;
			}
			
			unset($_POST['__hash__']);
			unset($_POST['Submit']);
			
			//增加操作记录
			$logs = C('RENWU_MSG_EDIT_SUCCESS');
			$remark2 = "(".json_encode($_POST, JSON_UNESCAPED_UNICODE).")";
			adminlog($logs, $remark2);
			
			//增加修改记录
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = $remark;
			$data['userip'] = get_client_ip();
			$data['cate'] = 14;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$table_name->add($data);
			
			
			$this->success('提交成功，等待审核',U($this->By_tpl.'/gift'));
			exit;
		}
		
		$list = $row1->order('id')->select();
		$this->assign('list',$list);
		
		//增加操作记录
		$logs = C('RENWU_MSG_RECORD');
		$remark = "";
		adminlog($logs, $remark);
		
		$this->assign('left_css',"28");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl.":gift";
		$this->display($lib_display);
	}
	//礼物配置结束
	
	//大喇叭配置开始
	public function dalaba(){
		$table1 = $this->Table_prifix."profile_horn_min_gold_config";
		$row1 = M($table1);
		
		if(!empty($_POST)){
			
			$remark = $_POST['remark'];
			if (empty($remark)){
				$this->error('修改原因必须填写');
				exit;
			}
			
			unset($_POST['__hash__']);
			unset($_POST['Submit']);
			
			//增加操作记录
			$logs = C('DALABA_MSG_EDIT_SUCCESS');
			$remark2 = "(".json_encode($_POST, JSON_UNESCAPED_UNICODE).")";
			adminlog($logs, $remark2);
			
			//增加修改记录
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = $remark;
			$data['userip'] = get_client_ip();
			$data['cate'] = 10;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$table_name->add($data);
			
			
			$this->success('提交成功，等待审核',U($this->By_tpl.'/dalaba'));
			exit;
		}
		
		$list = $row1->order('type')->select();
		$this->assign('list',$list);
		
		//增加操作记录
		$logs = C('DALABA_MSG_RECORD');
		$remark = "";
		adminlog($logs, $remark);
		
		$this->assign('left_css',"28");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl.":dalaba";
		$this->display($lib_display);
	}
	//大喇叭配置结束
	
	//登陆奖励配置开始
	public function signin(){
		$table1 = $this->Table_prifix."profile_signin_data";
		$row1 = M($table1);
		
		if(!empty($_POST)){
			
			$remark = $_POST['remark'];
			if (empty($remark)){
				$this->error('修改原因必须填写');
				exit;
			}
			
			/*			
			$res = $row1->field("DayIndex")->select();
			foreach($res as $key => $val){
				$show1 = "GoldNumber".$val['DayIndex'];

				$data9 = array();
				$data9 = array('GoldNumber' => $_POST[$show1]);
				$result = $row1->where("DayIndex=".$val['DayIndex'])->save($data9);	

			}
			*/
			unset($_POST['__hash__']);
			unset($_POST['Submit']);
			
			//增加操作记录
			$logs = C('SIGNIN_MSG_EDIT_SUCCESS');
			$remark2 = "(".json_encode($_POST, JSON_UNESCAPED_UNICODE).")";
			adminlog($logs, $remark2);
			
			//增加修改记录
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = $remark;
			$data['userip'] = get_client_ip();
			$data['cate'] = 6;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$table_name->add($data);
			
			//通知服务器
			//$url = DB_HOST."/Pay/shang.php?showindex=7&showtype=4";
			//$jinbi_result = curlGET($url);
			
			$this->success('提交成功，等待审核',U($this->By_tpl.'/signin'));
			exit;
		}
		
		$list = $row1->order('DayIndex')->select();
		//print_r($list);
		$this->assign('list',$list);
		
		
		
		//增加操作记录
		$logs = C('SIGNIN_MSG_RECORD');
		$remark = "";
		adminlog($logs, $remark);
		
		$this->assign('left_css',"28");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl.":signin";
		$this->display($lib_display);
	}
	//登陆奖励配置结束
	
	//在线宝箱配置开始
	public function chest(){
		$table1 = $this->Table_prifix."online_reward_conf";
		$row1 = M($table1);
		
		if(!empty($_POST)){
			
			$remark = $_POST['remark'];
			if (empty($remark)){
				$this->error('修改原因必须填写');
				exit;
			}
			
			/*			
			$res = $row1->field("conf_id")->select();
			foreach($res as $key => $val){
				$show1 = "minute".$val['conf_id'];
				$show2 = "coin".$val['conf_id'];

				$data9 = array();
				$data9 = array('minute' => $_POST[$show1],
							   'coin' => $_POST[$show2]);
				$result = $row1->where("conf_id=".$val['conf_id'])->save($data9);	

			}*/
			
			unset($_POST['__hash__']);
			unset($_POST['Submit']);
			
			//增加操作记录
			$logs = C('CHEST_MSG_EDIT_SUCCESS');
			$remark2 = "(".json_encode($_POST, JSON_UNESCAPED_UNICODE).")";
			adminlog($logs, $remark2);
			
			//增加修改记录
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = $remark;
			$data['userip'] = get_client_ip();
			$data['cate'] = 7;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$table_name->add($data);
			
			//通知服务器
			//$url = DB_HOST."/Pay/shang.php?showindex=6&showtype=4";
			//$jinbi_result = curlGET($url);
			
			$this->success('提交成功，等待审核',U($this->By_tpl.'/chest'));
			exit;
		}
		
		$list = $row1->order('conf_id')->select();
		//print_r($list);
		$this->assign('list',$list);
		
		
		
		//增加操作记录
		$logs = C('CHEST_MSG_RECORD');
		$remark = "";
		adminlog($logs, $remark);
		
		$this->assign('left_css',"28");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl.":chest";
		$this->display($lib_display);
	}
	//在线宝箱配置结束
	
	//百人场配置开始
	public function brc(){
		$table1 = $this->Table_prifix."brc_all_config";
		$row1 = M($table1);
		
		if(!empty($_POST)){
			
			$remark = $_POST['remark'];
			if (empty($remark)){
				$this->error('修改原因必须填写');
				exit;
			}
			
			/*			
			$res = $row1->field("conf_id")->select();
			foreach($res as $key => $val){
				$show1 = "minute".$val['conf_id'];
				$show2 = "coin".$val['conf_id'];

				$data9 = array();
				$data9 = array('minute' => $_POST[$show1],
							   'coin' => $_POST[$show2]);
				$result = $row1->where("conf_id=".$val['conf_id'])->save($data9);	

			}*/
			
			unset($_POST['__hash__']);
			unset($_POST['Submit']);
			
			//增加操作记录
			$logs = C('CHEST_MSG_EDIT_SUCCESS');
			$remark2 = "(".json_encode($_POST, JSON_UNESCAPED_UNICODE).")";
			adminlog($logs, $remark2);
			
			//增加修改记录
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = $remark;
			$data['userip'] = get_client_ip();
			$data['cate'] = 28;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$table_name->add($data);
			
			//通知服务器
			//$url = DB_HOST."/Pay/shang.php?showindex=6&showtype=4";
			//$jinbi_result = curlGET($url);
			
			$this->success('提交成功，等待审核',U($this->By_tpl.'/brc'));
			exit;
		}
		
		$config = $row1->order("id desc")->find();
		
		$model = M();
		$sql = "SELECT column_name,column_comment from Information_schema.columns where table_Name='brc_all_config'";
		$table_meno = $model->query($sql);
		$show_meno = array();
		foreach ($table_meno as $key => $val){
			$show_meno[$key] = array('keyname' => $val['column_name'], 'keymeno' => $val['column_comment'], 'keyvalue' => $config[$val['column_name']]);
		}
		$this->assign('show_meno', $show_meno);
		//print_r($show_meno);
		//增加操作记录
		$logs = C('CHEST_MSG_RECORD');
		$remark = "";
		adminlog($logs, $remark);
		
		$this->assign('left_css',"28");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl.":brc";
		$this->display($lib_display);
	}
	//百人场配置结束
	
	//VIP配置开始
	public function Vconfig(){
		$table1 = $this->Table_prifix."profile_vip_level_configure";
		$row1 = M($table1);
		
		if(!empty($_POST)){
			
			$remark = $_POST['remark'];
			if (empty($remark)){
				$this->error('修改原因必须填写');
				exit;
			}
			
			$temp_num = array(0,0,0,0,0,0);
			$res = $row1->field("viplevel")->select();
			foreach($res as $key => $val){
				$show1 = "paycount".$val['viplevel'];
				$show2 = "maxsavegold".$val['viplevel'];
				$show3 = "maxfriendnum".$val['viplevel'];
				$show4 = "logingivegold".$val['viplevel'];
				$show5 = "transfergoldrate".$val['viplevel'];
				$show6 = "raffleticketnum".$val['viplevel'];
				$show7 = "maxtransfergold".$val['viplevel'];
								
				if ($_POST[$show1] < $temp_num[0]){
					$this->error('VIP'.$val['viplevel'].'支付金额输入有误');
					exit;
				}
				
				if ($_POST[$show2] < $temp_num[1]){
					$this->error('VIP'.$val['viplevel'].'保险箱额度输入有误');
					exit;
				}
				
				if ($_POST[$show3] < $temp_num[2]){
					$this->error('VIP'.$val['viplevel'].'好友上限输入有误');
					exit;
				}
				
				if ($_POST[$show4] < $temp_num[3]){
					$this->error('VIP'.$val['viplevel'].'签到加赠输入有误');
					exit;
				}
				
				if ($_POST[$show6] < $temp_num[5]){
					$this->error('VIP'.$val['viplevel'].'免费抽奖次数输入有误');
					exit;
				}
				
				if ($_POST[$show7] > 10000000000){
					$this->error('单笔转账限额不能超过100亿');
					exit;
				}
				
				$temp_num[0] = $_POST[$show1];
				$temp_num[1] = $_POST[$show2];
				$temp_num[2] = $_POST[$show3];
				$temp_num[3] = $_POST[$show4];
				$temp_num[4] = $_POST[$show5];
				$temp_num[5] = $_POST[$show6];
			}
			/*			
			$res = $row1->field("viplevel")->select();
			foreach($res as $key => $val){
				$show1 = "paycount".$val['viplevel'];
				$show2 = "maxsavegold".$val['viplevel'];
				$show3 = "maxfriendnum".$val['viplevel'];
				$show4 = "logingivegold".$val['viplevel'];
				$show5 = "transfergoldrate".$val['viplevel'];

				$data9 = array();
				$data9 = array('paycount' => ($_POST[$show1] * 100),
							   'maxsavegold' => $_POST[$show2],
							   'maxfriendnum' => $_POST[$show3],
							   'transfergoldrate' => $_POST[$show5],
							   'logingivegold' => $_POST[$show4]);
				$result = $row1->where("viplevel=".$val['viplevel'])->save($data9);	

			}
			*/
			
			unset($_POST['__hash__']);
			unset($_POST['Submit']);
			
			//增加操作记录
			$logs = C('VIP_MSG_EDIT_SUCCESS');
			$remark2 = "(".json_encode($_POST, JSON_UNESCAPED_UNICODE).")";
			adminlog($logs, $remark2);
			
			//增加修改记录
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = $remark;
			$data['userip'] = get_client_ip();
			$data['cate'] = 8;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$table_name->add($data);
			
			//通知服务器
			//$url = DB_HOST."/Pay/shang.php?showindex=5&showtype=4";
			//$jinbi_result = curlGET($url);
			
			$this->success('提交成功，等待审核',U($this->By_tpl.'/Vconfig'));
			exit;
		}
		
		$list = $row1->order('viplevel')->select();
		//print_r($list);
		$this->assign('list',$list);
		
		
		
		//增加操作记录
		$logs = C('VIP_MSG_RECORD');
		$remark = "";
		adminlog($logs, $remark);
		
		$this->assign('left_css',"28");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl.":Vconfig";
		$this->display($lib_display);
	}
	//VIP配置结束
	
	//系统喇叭开始
	//系统喇叭列表
	public function laba(){
		
		
		$Table = $this->Table_prifix."profile_system_speaker_config";
		$rowlist = M($Table);
		import('ORG.Util.Page');
		$count = $rowlist->count('GoodsID');
		$Page       = new Page($count,PAGE_SHOW);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		
		$list = $rowlist->order('id')->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($rowlist->_sql());
		foreach($list as $key=>$value){
			$list[$key]['type'] = ($value['type']=="1") ? "紧急喇叭" : "一般喇叭";
			$list[$key]['status'] = ($value['status']=="1") ? "正常" : "失效";
		}
		
		//增加操作记录
		$logs = C('LABA_MSG_RECORD');
		$remark = "";
		adminlog($logs, $remark);
		
		$this->assign('left_css',"28");
		$this->assign('list',$list);
		$this->assign('pageshow',$show);
		
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl.":laba";
		$this->display($lib_display);
	}
	
	//系统喇叭添加
	public function laba_add(){
		if(!empty($_POST)){
			$Table = $this->Table_prifix."profile_system_speaker_config";
			$add_table = M($Table);
			
			//unset($_POST['__hash__']);
			
			$count = $add_table->where("GoodsID=".$_POST['GoodsID'])->count('GoodsID');
			if ($count > 0){
				$this->error('GoodsID已存在');
				exit;
			}
			
			unset($_POST['__hash__']);
			unset($_POST['Submit']);
			
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = '系统喇叭新增';
			$data['userip'] = get_client_ip();
			$data['cate'] = 11;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			//$result = $add_table->add($_POST);
			//dump($add_table->_sql()); exit;
			if($result){
				//增加操作记录
				$logs = C('LABA_MSG_ADD_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->success('提交成功，等待审核', U($this->By_tpl.'/laba'));
				exit;
			}else{
				//增加操作记录
				$logs = C('LABA_MSG_ADD_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('添加失败');
				exit;
			}
		}else{
			//增加操作记录
			$logs = C('LABA_MSG_ADD');
			$remark = "";
			adminlog($logs, $remark);
		
			$this->assign('left_css',"28");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":laba_add";
			$this->display($lib_display);
		}
		
	}

	//系统喇叭更新
	public function laba_update(){
		$Table = $this->Table_prifix."profile_system_speaker_config";
		$upate_table = M($Table);
		if(!empty($_POST)){
			
			$GoodsID = $_POST['GoodsID'];
			//unset($_POST['GoodsID']);
			unset($_POST['Submit']);
			unset($_POST['__hash__']);
			
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = '系统喇叭修改';
			$data['userip'] = get_client_ip();
			$data['cate'] = 12;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			//$id['GoodsID']=intval($GoodsID);
			//$result=$upate_table->where($id)->save($_POST);
			if($result){
				//增加操作记录
				$logs = C('LABA_MSG_EDIT_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->success('提交成功，等待审核',U($this->By_tpl.'/laba'));
			}else{
				//增加操作记录
				$logs = C('LABA_MSG_EDIT_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('修改失败');
			}
		}else{
			//增加操作记录
			$logs = C('LABA_MSG_EDIT');
			$remark = "";
			adminlog($logs, $remark);
			
			$id['id']=$_GET['id'];
			$info = $upate_table->where($id)->find();
			$this->assign('info',$info);
			$this->assign('left_css',"28");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":laba_update";
			$this->display($lib_display);
		}
	}
	
	//系统喇叭删除
	public function laba_delete(){
		if(empty($_GET)){ 
			$this->error('非法操作');
			exit;
		}else{
			$id=$_GET['id']?$_GET['id']:$_POST['id'];
			$Tablename = $this->Table_prifix."profile_system_speaker_config";
			$delete_table = M($Tablename);
			//$where['GoodsID']=$GoodsID;
			//$result=$delete_table->where($where)->delete();
			$arr = $delete_table->where("id=".$id)->find();
			$arr['tablename'] = $Tablename;
			$arr['act'] = "del";
			
			//增加修改记录
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($arr, JSON_UNESCAPED_UNICODE);
			$data['remark'] = "删除系统喇叭";
			$data['userip'] = get_client_ip();
			$data['cate'] = 13;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			if($result){
				//增加操作记录
				$logs = C('LABA_MSG_DEL_SUCCESS');
				$remark = "(".$GoodsID.")";
				adminlog($logs, $remark);
				
				$this->success('提交成功，等待审核', U($this->By_tpl.'/laba'));
			}else{
				//增加操作记录
				$logs = C('LABA_MSG_DEL_FALSE');
				$remark = "(".$GoodsID.")";
				adminlog($logs, $remark);
				
				$this->error('删除失败');
				exit;
			}
		}
	}
	//商品结束
	
	//银行开关开始
	//银行开关列表
	public function bankswitch(){
		
		
		$Table = $this->Table_prifix."bank_switch";
		$rowlist = M($Table);
		import('ORG.Util.Page');
		$count = $rowlist->count('id');
		$Page       = new Page($count,PAGE_SHOW);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		
		$list = $rowlist->order('channel,id')->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($rowlist->_sql());
		foreach($list as $key=>$value){
			$list[$key]['channel'] = ($value['channel']=="0") ? "所有渠道" : $value['channel'];
			$list[$key]['status'] = ($value['status']=="1") ? "开启" : "关闭";
		}
		
		//增加操作记录
		$logs = C('BANK_MSG_RECORD');
		$remark = "";
		adminlog($logs, $remark);
		
		$this->assign('left_css',"28");
		$this->assign('list',$list);
		$this->assign('pageshow',$show);
		
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl.":bankswitch";
		$this->display($lib_display);
	}
	
	//银行开关添加
	public function bankswitch_add(){
		if(!empty($_POST)){
			$Table = $this->Table_prifix."bank_switch";
			$add_table = M($Table);
			
			//unset($_POST['__hash__']);
			
			$count = $add_table->where("channel=".$_POST['channel'])->count('id');
			if ($count > 0){
				$this->error('渠道ID已存在');
				exit;
			}
			
			unset($_POST['__hash__']);
			unset($_POST['Submit']);
			
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = '银行开关新增';
			$data['userip'] = get_client_ip();
			$data['cate'] = 15;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			//$result = $add_table->add($_POST);
			//dump($add_table->_sql()); exit;
			if($result){
				//增加操作记录
				$logs = C('BANK_MSG_ADD_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->success('提交成功，等待审核', U($this->By_tpl.'/bankswitch'));
				exit;
			}else{
				//增加操作记录
				$logs = C('BANK_MSG_ADD_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('添加失败');
				exit;
			}
		}else{
			//增加操作记录
			$logs = C('BANK_MSG_ADD');
			$remark = "";
			adminlog($logs, $remark);
		
			$this->assign('left_css',"28");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":bankswitch_add";
			$this->display($lib_display);
		}
		
	}

	//银行开关更新
	public function bankswitch_update(){
		$Table = $this->Table_prifix."bank_switch";
		$upate_table = M($Table);
		if(!empty($_POST)){
			
			//$GoodsID = $_POST['GoodsID'];
			//unset($_POST['GoodsID']);
			unset($_POST['Submit']);
			unset($_POST['__hash__']);
			
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = '银行开关修改';
			$data['userip'] = get_client_ip();
			$data['cate'] = 16;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			//$id['GoodsID']=intval($GoodsID);
			//$result=$upate_table->where($id)->save($_POST);
			if($result){
				//增加操作记录
				$logs = C('BANK_MSG_EDIT_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->success('提交成功，等待审核',U($this->By_tpl.'/bankswitch'));
			}else{
				//增加操作记录
				$logs = C('BANK_MSG_EDIT_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('修改失败');
			}
		}else{
			//增加操作记录
			$logs = C('BANK_MSG_EDIT');
			$remark = "";
			adminlog($logs, $remark);
			
			$id['id']=$_GET['id'];
			$info = $upate_table->where($id)->find();
			$this->assign('info',$info);
			$this->assign('left_css',"28");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":bankswitch_update";
			$this->display($lib_display);
		}
	}
	
	//银行开关删除
	public function bankswitch_delete(){
		if(empty($_GET)){ 
			$this->error('非法操作');
			exit;
		}else{
			$id=$_GET['id']?$_GET['id']:$_POST['id'];
			$Tablename = $this->Table_prifix."bank_switch";
			$delete_table = M($Tablename);
			//$where['GoodsID']=$GoodsID;
			//$result=$delete_table->where($where)->delete();
			$arr = $delete_table->where("id=".$id)->find();
			$arr['tablename'] = $Tablename;
			$arr['act'] = "del";
			
			//增加修改记录
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($arr, JSON_UNESCAPED_UNICODE);
			$data['remark'] = "删除银行开关";
			$data['userip'] = get_client_ip();
			$data['cate'] = 17;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			if($result){
				//增加操作记录
				$logs = C('BANK_MSG_DEL_SUCCESS');
				$remark = "(".$GoodsID.")";
				adminlog($logs, $remark);
				
				$this->success('提交成功，等待审核', U($this->By_tpl.'/bankswitch'));
			}else{
				//增加操作记录
				$logs = C('BANK_MSG_DEL_FALSE');
				$remark = "(".$GoodsID.")";
				adminlog($logs, $remark);
				
				$this->error('删除失败');
				exit;
			}
		}
	}
	//银行开关结束
	
	//游戏场景开关开始
	//游戏场景开关列表
	public function gameswitch(){
		
		
		$Table = $this->Table_prifix."game_switch";
		$rowlist = M($Table);
		import('ORG.Util.Page');
		$count = $rowlist->count('id');
		$Page       = new Page($count,30);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		
		$list = $rowlist->order('platform,channel,id')->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($rowlist->_sql());
		foreach($list as $key=>$value){
			$list[$key]['channel'] = ($value['channel']=="0") ? "所有渠道" : $value['channel'];
			$list[$key]['version'] = ($value['version']=="0") ? "所有版本" : $value['version'];
			
			switch ($value['platform']){
				case '0' : $list[$key]['showplatform'] = '全系列'; break;
				case '1' : $list[$key]['showplatform'] = 'Ios'; break;
				case '2' : $list[$key]['showplatform'] = 'Android'; break;
				default  : $list[$key]['showplatform'] = '未知'; break;
			}
			
			
		}
		
		//增加操作记录
		$logs = C('BANK_MSG_RECORD');
		$remark = "";
		adminlog($logs, $remark);
		
		$this->assign('left_css',"28");
		$this->assign('list',$list);
		$this->assign('pageshow',$show);
		
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl.":gameswitch";
		$this->display($lib_display);
	}
	
	//游戏场景开关添加
	public function gameswitch_add(){
		if(!empty($_POST)){
			$Table = $this->Table_prifix."game_switch";
			$add_table = M($Table);
			
			//unset($_POST['__hash__']);
			
			$count = $add_table->where("channel=".$_POST['channel']." and platform=".$_POST['platform']." and scene=".$_POST['scene'])->count('id');
			if ($count > 0){
				$this->error('游戏场景开关已存在');
				exit;
			}
			
			unset($_POST['__hash__']);
			unset($_POST['Submit']);
			
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = '游戏场景开关新增';
			$data['userip'] = get_client_ip();
			$data['cate'] = 25;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			//$result = $add_table->add($_POST);
			//dump($add_table->_sql()); exit;
			if($result){
				//增加操作记录
				$logs = C('BANK_MSG_ADD_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->success('提交成功，等待审核', U($this->By_tpl.'/gameswitch'));
				exit;
			}else{
				//增加操作记录
				$logs = C('BANK_MSG_ADD_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('添加失败');
				exit;
			}
		}else{
			//增加操作记录
			$logs = C('BANK_MSG_ADD');
			$remark = "";
			adminlog($logs, $remark);
		
			$this->assign('left_css',"28");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":gameswitch_add";
			$this->display($lib_display);
		}
		
	}

	//游戏场景开关更新
	public function gameswitch_update(){
		$Table = $this->Table_prifix."game_switch";
		$upate_table = M($Table);
		if(!empty($_POST)){
			
			//$GoodsID = $_POST['GoodsID'];
			//unset($_POST['GoodsID']);
			unset($_POST['Submit']);
			unset($_POST['__hash__']);
			
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = '游戏场景开关修改';
			$data['userip'] = get_client_ip();
			$data['cate'] = 26;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			//$id['GoodsID']=intval($GoodsID);
			//$result=$upate_table->where($id)->save($_POST);
			if($result){
				//增加操作记录
				$logs = C('BANK_MSG_EDIT_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->success('提交成功，等待审核',U($this->By_tpl.'/gameswitch'));
			}else{
				//增加操作记录
				$logs = C('BANK_MSG_EDIT_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('修改失败');
			}
		}else{
			//增加操作记录
			$logs = C('BANK_MSG_EDIT');
			$remark = "";
			adminlog($logs, $remark);
			
			$id['id']=$_GET['id'];
			$info = $upate_table->where($id)->find();
			$this->assign('info',$info);
			$this->assign('left_css',"28");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":gameswitch_update";
			$this->display($lib_display);
		}
	}
	
	//游戏场景开关删除
	public function gameswitch_delete(){
		if(empty($_GET)){ 
			$this->error('非法操作');
			exit;
		}else{
			$id=$_GET['id']?$_GET['id']:$_POST['id'];
			$Tablename = $this->Table_prifix."game_switch";
			$delete_table = M($Tablename);
			//$where['GoodsID']=$GoodsID;
			//$result=$delete_table->where($where)->delete();
			$arr = $delete_table->where("id=".$id)->find();
			$arr['tablename'] = $Tablename;
			$arr['act'] = "del";
			
			//增加修改记录
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($arr, JSON_UNESCAPED_UNICODE);
			$data['remark'] = "删除游戏场景开关";
			$data['userip'] = get_client_ip();
			$data['cate'] = 27;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			if($result){
				//增加操作记录
				$logs = C('BANK_MSG_DEL_SUCCESS');
				$remark = "(".$GoodsID.")";
				adminlog($logs, $remark);
				
				$this->success('提交成功，等待审核', U($this->By_tpl.'/gameswitch'));
			}else{
				//增加操作记录
				$logs = C('BANK_MSG_DEL_FALSE');
				$remark = "(".$GoodsID.")";
				adminlog($logs, $remark);
				
				$this->error('删除失败');
				exit;
			}
		}
	}
	//游戏场景开关结束
	
	//登陆接口开始
	//登陆接口列表
	public function logininter(){
		
		
		$Table = $this->Table_prifix."user_login_inter";
		$rowlist = M($Table);
		import('ORG.Util.Page');
		$count = $rowlist->count('id');
		$Page       = new Page($count,PAGE_SHOW);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		
		$list = $rowlist->order('id')->limit($Page->firstRow.','.$Page->listRows)->select();
		
		//增加操作记录
		$logs = C('LOGININTER_MSG_RECORD');
		$remark = "";
		adminlog($logs, $remark);
		
		$this->assign('left_css',"28");
		$this->assign('list',$list);
		$this->assign('pageshow',$show);
		
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl.":logininter";
		$this->display($lib_display);
	}
	
	//登陆接口添加
	public function logininter_add(){
		if(!empty($_POST)){
			$Table = $this->Table_prifix."user_login_inter";
			$add_table = M($Table);
			
			//unset($_POST['__hash__']);
			unset($_POST['__hash__']);
			unset($_POST['Submit']);
			
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = '登陆接口新增';
			$data['userip'] = get_client_ip();
			$data['cate'] = 18;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			//$result = $add_table->add($_POST);
			//dump($add_table->_sql()); exit;
			if($result){
				//增加操作记录
				$logs = C('LOGININTER_MSG_ADD_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->success('提交成功，等待审核', U($this->By_tpl.'/logininter'));
				exit;
			}else{
				//增加操作记录
				$logs = C('LOGININTER_MSG_ADD_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('添加失败');
				exit;
			}
		}else{
			//增加操作记录
			$logs = C('LOGININTER_MSG_ADD');
			$remark = "";
			adminlog($logs, $remark);
		
			$this->assign('left_css',"28");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":logininter_add";
			$this->display($lib_display);
		}
		
	}

	//登陆接口更新
	public function logininter_update(){
		$Table = $this->Table_prifix."user_login_inter";
		$upate_table = M($Table);
		if(!empty($_POST)){
			
			//$GoodsID = $_POST['GoodsID'];
			//unset($_POST['GoodsID']);
			unset($_POST['Submit']);
			unset($_POST['__hash__']);
			
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = '登陆接口修改';
			$data['userip'] = get_client_ip();
			$data['cate'] = 19;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			//$id['GoodsID']=intval($GoodsID);
			//$result=$upate_table->where($id)->save($_POST);
			if($result){
				//增加操作记录
				$logs = C('LOGININTER_MSG_EDIT_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->success('提交成功，等待审核',U($this->By_tpl.'/logininter'));
			}else{
				//增加操作记录
				$logs = C('LOGININTER_MSG_EDIT_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('修改失败');
			}
		}else{
			//增加操作记录
			$logs = C('LOGININTER_MSG_EDIT');
			$remark = "";
			adminlog($logs, $remark);
			
			$id['id']=$_GET['id'];
			$info = $upate_table->where($id)->find();
			$this->assign('info',$info);
			$this->assign('left_css',"28");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":logininter_update";
			$this->display($lib_display);
		}
	}
	
	//登陆接口删除
	public function logininter_delete(){
		if(empty($_GET)){ 
			$this->error('非法操作');
			exit;
		}else{
			$id=$_GET['id']?$_GET['id']:$_POST['id'];
			$Tablename = $this->Table_prifix."user_login_inter";
			$delete_table = M($Tablename);
			//$where['GoodsID']=$GoodsID;
			//$result=$delete_table->where($where)->delete();
			$arr = $delete_table->where("id=".$id)->find();
			$arr['tablename'] = $Tablename;
			$arr['act'] = "del";
			
			//增加修改记录
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($arr, JSON_UNESCAPED_UNICODE);
			$data['remark'] = "删除银行开关";
			$data['userip'] = get_client_ip();
			$data['cate'] = 20;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			if($result){
				//增加操作记录
				$logs = C('LOGININTER_MSG_DEL_SUCCESS');
				$remark = "(".$GoodsID.")";
				adminlog($logs, $remark);
				
				$this->success('提交成功，等待审核', U($this->By_tpl.'/logininter'));
			}else{
				//增加操作记录
				$logs = C('LOGININTER_MSG_DEL_FALSE');
				$remark = "(".$GoodsID.")";
				adminlog($logs, $remark);
				
				$this->error('删除失败');
				exit;
			}
		}
	}
	//登陆接口结束
	
	//登陆IP管理开始
	//登陆IP管理列表
	public function loginip(){
		
		$Table = $this->Table_prifix."login_ip_config";
		$rowlist = M($Table);
		import('ORG.Util.Page');
		$count = $rowlist->count('id');
		$Page       = new Page($count,PAGE_SHOW);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		
		$list = $rowlist->order('id')->limit($Page->firstRow.','.$Page->listRows)->select();
		foreach($list as $key => $val){
			if ($val['cate']==0){
				$list[$key]['cate'] = "全系列";
			}else if ($val['cate']==1){
				$list[$key]['cate'] = "Android";
			}else if ($val['cate']==2){
				$list[$key]['cate'] = "Ios";
			}
			
			$list[$key]['version'] = (empty($val['version'])) ? "所有版本" : $val['version'];
			$list[$key]['channel'] = (empty($val['channel'])) ? "所有渠道" : $val['channel'];
		}
		
		//增加操作记录
		$logs = C('LOGINIP_MSG_RECORD');
		$remark = "";
		adminlog($logs, $remark);
		
		$this->assign('left_css',"28");
		$this->assign('list',$list);
		$this->assign('pageshow',$show);
		
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl.":loginip";
		$this->display($lib_display);
	}
	
	//登陆IP管理添加
	public function loginip_add(){
		if(!empty($_POST)){
			$Table = $this->Table_prifix."login_ip_config";
			$add_table = M($Table);
			
			//unset($_POST['__hash__']);
			unset($_POST['__hash__']);
			unset($_POST['Submit']);
			
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = '登陆IP新增';
			$data['userip'] = get_client_ip();
			$data['cate'] = 22;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			//$result = $add_table->add($_POST);
			//dump($add_table->_sql()); exit;
			if($result){
				//增加操作记录
				$logs = C('LOGINIP_MSG_ADD_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->success('提交成功，等待审核', U($this->By_tpl.'/loginip'));
				exit;
			}else{
				//增加操作记录
				$logs = C('LOGINIP_MSG_ADD_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('添加失败');
				exit;
			}
		}else{
			//增加操作记录
			$logs = C('LOGINIP_MSG_ADD');
			$remark = "";
			adminlog($logs, $remark);
		
			$this->assign('left_css',"28");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":loginip_add";
			$this->display($lib_display);
		}
		
	}

	//登陆IP管理更新
	public function loginip_update(){
		$Table = $this->Table_prifix."login_ip_config";
		$upate_table = M($Table);
		if(!empty($_POST)){
			
			//$GoodsID = $_POST['GoodsID'];
			//unset($_POST['GoodsID']);
			unset($_POST['Submit']);
			unset($_POST['__hash__']);
			
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = '登陆IP修改';
			$data['userip'] = get_client_ip();
			$data['cate'] = 23;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			//$id['GoodsID']=intval($GoodsID);
			//$result=$upate_table->where($id)->save($_POST);
			if($result){
				//增加操作记录
				$logs = C('LOGINIP_MSG_EDIT_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->success('提交成功，等待审核',U($this->By_tpl.'/loginip'));
			}else{
				//增加操作记录
				$logs = C('LOGINIP_MSG_EDIT_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('修改失败');
			}
		}else{
			//增加操作记录
			$logs = C('LOGINIP_MSG_EDIT');
			$remark = "";
			adminlog($logs, $remark);
			
			$id['id']=$_GET['id'];
			$info = $upate_table->where($id)->find();
			$this->assign('info',$info);
			$this->assign('left_css',"28");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":loginip_update";
			$this->display($lib_display);
		}
	}
	
	//登陆IP管理删除
	public function loginip_delete(){
		if(empty($_GET)){ 
			$this->error('非法操作');
			exit;
		}else{
			$id=$_GET['id']?$_GET['id']:$_POST['id'];
			$Tablename = $this->Table_prifix."login_ip_config";
			$delete_table = M($Tablename);
			//$where['GoodsID']=$GoodsID;
			//$result=$delete_table->where($where)->delete();
			$arr = $delete_table->where("id=".$id)->find();
			$arr['tablename'] = $Tablename;
			$arr['act'] = "del";
			
			//增加修改记录
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($arr, JSON_UNESCAPED_UNICODE);
			$data['remark'] = "删除登陆IP";
			$data['userip'] = get_client_ip();
			$data['cate'] = 24;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			if($result){
				//增加操作记录
				$logs = C('LOGINIP_MSG_DEL_SUCCESS');
				$remark = "(".$GoodsID.")";
				adminlog($logs, $remark);
				
				$this->success('提交成功，等待审核', U($this->By_tpl.'/loginip'));
			}else{
				//增加操作记录
				$logs = C('LOGINIP_MSG_DEL_FALSE');
				$remark = "(".$GoodsID.")";
				adminlog($logs, $remark);
				
				$this->error('删除失败');
				exit;
			}
		}
	}
	//登陆接口结束
	
	//渠道房间控制
	public function Rchannel(){
		
		$table_name = M('user_record');
		$table1 = $this->Table_prifix."room_config";
		$row1 = M($table1);
		
		if(!empty($_POST)){
			
			//$GoodsID = $_POST['GoodsID'];
			//unset($_POST['GoodsID']);
			unset($_POST['Submit']);
			unset($_POST['__hash__']);
			
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = '渠道房间控制修改';
			$data['userip'] = get_client_ip();
			$data['cate'] = 21;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			//$id['GoodsID']=intval($GoodsID);
			//$result=$upate_table->where($id)->save($_POST);
			if($result){
				//增加操作记录
				$logs = C('LOGININTER_MSG_EDIT_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->success('提交成功，等待审核',U($this->By_tpl.'/Rchannel'));
			}else{
				//增加操作记录
				$logs = C('LOGININTER_MSG_EDIT_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('修改失败');
			}
		}else{
			//增加操作记录
			$logs = C('LOGININTER_MSG_EDIT');
			$remark = "";
			adminlog($logs, $remark);
			
			$info = $table_name->where('cate=21 and flag=1')->order('id desc')->find();
			$info_arr = json_decode($info['logs'], true);
			$this->assign('info', $info_arr);
			
			$list = $row1->order('displayorder')->select();
			foreach($list as $key => $val){
				$tempid = "room_id".$val['room_id'];
				$list[$key]['flag'] = ($info_arr[$tempid] == "1") ? "1" : "0";
			}
			$this->assign('room',$list);
			
			$this->assign('left_css',"28");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":room_channel";
			$this->display($lib_display);
		}
	}
	
	//审核基本配置开始
	public function shenhe(){
		$table = "user_record";
		$row = M($table);
		
		$sql0 = "";
		if ($_SESSION['js_flag']!="1"){$sql0 .= " and username='".$_SESSION['username']."'";}
		
		import('ORG.Util.Page');
		$count = $row->where("cate<51 $sql0")->count('id');
		$Page       = new Page($count,PAGE_SHOW);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		
		$list = $row->where("cate<51 $sql0")->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($row->_sql());
		foreach($list as $key=>$value){
			if ($value['cate']=="1"){
				$list[$key]['showcate'] = "老虎机";
			}else if ($value['cate']=="2"){
				$list[$key]['showcate'] = "时时彩";
			}else if ($value['cate']=="3"){
				$list[$key]['showcate'] = "发牌参数";
			}else if ($value['cate']=="4"){
				$list[$key]['showcate'] = "房间配置";
			}else if ($value['cate']=="5"){
				$list[$key]['showcate'] = "常规配置";
			}else if ($value['cate']=="6"){
				$list[$key]['showcate'] = "登陆奖励配置";
			}else if ($value['cate']=="7"){
				$list[$key]['showcate'] = "在线宝箱配置";
			}else if ($value['cate']=="8"){
				$list[$key]['showcate'] = "VIP配置";
			}else if ($value['cate']=="9"){
				$list[$key]['showcate'] = "任务配置";
			}else if ($value['cate']=="10"){
				$list[$key]['showcate'] = "大喇叭配置";
			}else if ($value['cate']=="11"){
				$list[$key]['showcate'] = "系统喇叭新增";
			}else if ($value['cate']=="12"){
				$list[$key]['showcate'] = "系统喇叭编辑";
			}else if ($value['cate']=="13"){
				$list[$key]['showcate'] = "系统喇叭删除";
			}else if ($value['cate']=="14"){
				$list[$key]['showcate'] = "礼物配置";
			}else if ($value['cate']=="15"){
				$list[$key]['showcate'] = "银行开关新增";
			}else if ($value['cate']=="16"){
				$list[$key]['showcate'] = "银行开关编辑";
			}else if ($value['cate']=="17"){
				$list[$key]['showcate'] = "银行开关删除";
			}else if ($value['cate']=="18"){
				$list[$key]['showcate'] = "登陆接口新增";
			}else if ($value['cate']=="19"){
				$list[$key]['showcate'] = "登陆接口编辑";
			}else if ($value['cate']=="20"){
				$list[$key]['showcate'] = "登陆接口删除";
			}else if ($value['cate']=="21"){
				$list[$key]['showcate'] = "渠道房间控制";
			}else if ($value['cate']=="22"){
				$list[$key]['showcate'] = "登陆IP新增";
			}else if ($value['cate']=="23"){
				$list[$key]['showcate'] = "登陆IP编辑";
			}else if ($value['cate']=="24"){
				$list[$key]['showcate'] = "登陆IP删除";
			}else if ($value['cate']=="25"){
				$list[$key]['showcate'] = "游戏场景开关新增";
			}else if ($value['cate']=="26"){
				$list[$key]['showcate'] = "游戏场景开关编辑";
			}else if ($value['cate']=="27"){
				$list[$key]['showcate'] = "游戏场景开关删除";
			}else if ($value['cate']=="28"){
				$list[$key]['showcate'] = "百人场配置";
			}
			$list[$key]['addtime'] = date("Y-m-d H:i:s", $value['addtime']);
			if ($value['flag']=="0"){
				$list[$key]['flagshow'] = "<font color='#FF0000'>待审核</font>";
			}else if ($value['flag']=="1"){
				$list[$key]['flagshow'] = "审核通过";
			}else if ($value['flag']=="2"){
				$list[$key]['flagshow'] = "审核取消";
			}
			
			$notice_no = array(11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28);
			if ($value['cate']=="4"){
				$list[$key]['flagshow'] .= "&nbsp;需重启服务器";
			}else if (in_array($value['cate'], $notice_no)){
				
			}else{
				if ($value['notice']=="0"){
					$list[$key]['flagshow'] .= "&nbsp;<font color='#FF0000'>未通知服务器</font>";
				}else if ($value['flag']=="1"){
					$list[$key]['flagshow'] .= "&nbsp;已通知服务器";
				}
			}
			
		}
		
		//增加操作记录
		$logs = C('RECORD_HISTORY');
		$remark = "";
		adminlog($logs, $remark);
		//print_r($list);
		$this->assign('list',$list);
		$this->assign('pageshow',$show);
		
		$this->assign('left_css',"28");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl.":shenhe";
		$this->display($lib_display);
	}
	//审核基本配置结束
	
	//查看详细配置开始
	public function shenhe_more(){
		$table = "user_record";
		$row = M($table);
		
		$id = I("id");
		if (empty($id)){
			$this->error('输入有误');
			exit;
		}
		$this->assign('id',$id);

		$info = $row->where('id='.$id)->find();
		$this->assign('flag',$info['flag']);
		$this->assign('notice',$info['notice']);
		$this->assign('cate',$info['cate']);
		//dump($row->_sql());
		if ($info['cate']=="1"){
			
			$table1 = $this->Table_prifix."manual_configure_tiger";
			$row1 = M($table1);
			$table2 = $this->Table_prifix."profile_tiger_configure";
			$row2 = M($table2);
			$list = $row1->order('tactics')->select();
			//dump($row1->_sql());
			$num = 0;
			foreach($list as $key => $val){
				
				if ($key == 0) $systemwingold = $val['systemwingold'];
				if ($key == 1) $mingold1 = $val['mingold'];
				if ($key == 2) $mingold2 = $val['mingold'];
				$list[$key]['sum'] = 0;
				$list[$key]['sub']  = array();
				
				$list[$key]['sub'] = $row2->where("tactics=".$val['tactics'])->order('id')->select();
				foreach($list[$key]['sub'] as $key1 => $val1){
					$list[$key]['sum']+=$val1['productrate'];
					if ($key == 0) $num++;
				}
				//dump($row2->_sql());
				
			}
			//print_r($list);
			$this->assign('list',$list);
			$this->assign('num',$num);
			$this->assign('systemwingold',$systemwingold);
			$this->assign('mingold1',$mingold1);
			$this->assign('mingold2',$mingold2);
			
			$page = "more1";
		}else if ($info['cate']=="2"){
			
			$table1 = $this->Table_prifix."lottery_bet_control";
			$row1 = M($table1);
			$table2 = $this->Table_prifix."lottery_configure";
			$row2 = M($table2);
			$list = $row1->order('id')->select();
			$this->assign('list',$list);
			
			$page = "more2";
		}else if ($info['cate']=="3"){
			$page = "more3";
		}else if ($info['cate']=="4"){
			$page = "more4";
			
			$room = json_decode($info['logs'], true);
			//print_r($room);
			$Table = $this->Table_prifix."room_config";
			$add_table = M($Table);
			$Table2 = $this->Table_prifix."room_gold_code";
			$add_table2 = M($Table2);
			$res0 = $add_table->order("displayorder")->select();
			foreach($res0 as $key => $val){
				$res0[$key]['show1'] = $room["displayorder".$val['room_id']];
				$res0[$key]['show2'] = $room["cell_gold".$val['room_id']];
				$res0[$key]['show3'] = $room["lower_limit_gold".$val['room_id']];
				$res0[$key]['show4'] = $room["high_limit_gold".$val['room_id']];
				$res0[$key]['show5'] = $room["min_cmp_round".$val['room_id']];
				$res0[$key]['show6'] = $room["max_round".$val['room_id']];
				$res0[$key]['show7'] = $room["tax_rate".$val['room_id']];
				$res0[$key]['show8'] = $room["room_name".$val['room_id']];
				$res0[$key]['show9'] = $room["max_cell_gold".$val['room_id']];
				$res0[$key]['show10'] = $room["room_type".$val['room_id']];
				$res0[$key]['show11'] = $room["pitrue_url".$val['room_id']];
				$res0[$key]['show12'] = $room["actionTime".$val['room_id']];
				$res0[$key]['show13'] = $room["online".$val['room_id']];
				
				$res0[$key]['show14'] = array();
				$count00 = $add_table2->where("room_id=".$val['room_id'])->count();
				if ($count00 == 0){
					for($t=0; $t<5; $t++){
						$res0[$key]['show14'][$t]['show'] = 0;
					}
				}else{
					$res1 = $add_table2->where("room_id=".$val['room_id'])->select();
					foreach($res1 as $key1 => $val1){
						$res0[$key]['show14'][$key1]['show'] = $room["gold_code".$val['room_id'].$val1['code_id']];
					}
				}
				
			}
			$this->assign('list',$res0);
		}else if ($info['cate']=="5"){
			$page = "more5";
		}else if ($info['cate']=="6"){
			$page = "more6";
		}else if ($info['cate']=="7"){
			$page = "more7";
		}else if ($info['cate']=="8"){
			$page = "more8";
			
			$vip = json_decode($info['logs'], true);
			$Table = $this->Table_prifix."profile_vip_level_configure";
			$add_table = M($Table);
			$res0 = $add_table->order("viplevel")->select();
			foreach($res0 as $key => $val){
				$res0[$key]['show1'] = $vip["paycount".$val['viplevel']];
				$res0[$key]['show2'] = $vip["maxsavegold".$val['viplevel']];
				$res0[$key]['show3'] = $vip["maxfriendnum".$val['viplevel']];
				$res0[$key]['show4'] = $vip["logingivegold".$val['viplevel']];
				$res0[$key]['show5'] = $vip["transfergoldrate".$val['viplevel']];
				$res0[$key]['show6'] = $vip["raffleticketnum".$val['viplevel']];
				$res0[$key]['show7'] = $vip["maxtransfergold".$val['viplevel']];
				$res0[$key]['show8'] = $vip["maxsellgold".$val['viplevel']];
				$res0[$key]['show9'] = $vip["maxsellnum".$val['viplevel']];
				$res0[$key]['show10'] = ($vip["maxsellflag".$val['viplevel']]=="1") ? "选中" : "";
			}
			$this->assign('list',$res0);
		}else if ($info['cate']=="9"){
			$page = "more9";
			
			$renwu = json_decode($info['logs'], true);
			$Table = $this->Table_prifix."task_daily_task_config";
			$add_table = M($Table);
			$res0 = $add_table->order("taskid")->select();
			foreach($res0 as $key => $val){
				$res0[$key]['show1'] = $renwu["value".$val['taskid']];
				$res0[$key]['show2'] = $renwu["reward".$val['taskid']];
				$res0[$key]['show3'] = $renwu["name".$val['taskid']];
				$res0[$key]['show4'] = $renwu["explain".$val['taskid']];
			}
			$this->assign('list',$res0);
		}else if ($info['cate']=="10"){
			$page = "more10";
			
			$dalaba = json_decode($info['logs'], true);
			$Table = $this->Table_prifix."profile_horn_min_gold_config";
			$add_table = M($Table);
			$res0 = $add_table->order("type")->select();
			foreach($res0 as $key => $val){
				$res0[$key]['show1'] = $dalaba["mingold".$val['type']];
			}
			$this->assign('list',$res0);
		}else if ($info['cate']=="11"){
			$page = "more11";
		}else if ($info['cate']=="12"){
			$page = "more12";
		}else if ($info['cate']=="13"){
			$page = "more13";
		}else if ($info['cate']=="14"){
			$page = "more14";
			
			$renwu = json_decode($info['logs'], true);
			$Table = $this->Table_prifix."profile_gift_config";
			$add_table = M($Table);
			$res0 = $add_table->order("id")->select();
			foreach($res0 as $key => $val){
				$res0[$key]['show1'] = $renwu["name".$val['id']];
				$res0[$key]['show2'] = $renwu["buygold".$val['id']];
				$res0[$key]['show3'] = $renwu["sellgold".$val['id']];
				$res0[$key]['show4'] = $renwu["charm".$val['id']];
				$res0[$key]['show5'] = $renwu["cansell".$val['id']];
			}
			$this->assign('list',$res0);
		}else if ($info['cate']=="15" or $info['cate']=="16" or $info['cate']=="17"){
			$page = "more15";
		}else if ($info['cate']=="18" or $info['cate']=="19" or $info['cate']=="20"){
			$page = "more16";
		}else if ($info['cate']=="21"){
			$page = "more21";
			
			$room_channel = json_decode($info['logs'], true);
			$Table = $this->Table_prifix."room_config";
			$add_table = M($Table);
			$res0 = $add_table->order("displayorder")->select();
			foreach($res0 as $key => $val){
				$tempid = "room_id".$val['room_id'];
				$res0[$key]['flag'] = ($room_channel[$tempid] == "1") ? "1" : "0";
			}
			$this->assign('list',$res0);
			$this->assign('room_channel',$room_channel);
		}else if ($info['cate']=="22" or $info['cate']=="23" or $info['cate']=="24"){
			$page = "more22";
		}else if ($info['cate']=="25" or $info['cate']=="26" or $info['cate']=="27"){
			$page = "more23";
		}else if ($info['cate']=="28"){
			$page = "more24";
			
			$config = json_decode($info['logs'], true);
			$model = M();
			$sql = "SELECT column_name,column_comment from Information_schema.columns where table_Name='brc_all_config'";
			$table_meno = $model->query($sql);
			$show_meno = array();
			foreach ($table_meno as $key => $val){
				$show_meno[$key] = array('keyname' => $val['column_name'], 'keymeno' => $val['column_comment'], 'keyvalue' => $config[$val['column_name']]);
			}
			$this->assign('show_meno', $show_meno);
		}else{
			$page = "history";
		} 
		
		$act = I("act");
		if (!empty($act)){
			
			if ($act == "on"){
				if ($info['cate'] == "5"){
					$Table = $this->Table_prifix."dynamic_config";
					$add_table = M($Table);
					$res = json_decode($info['logs'], true);
					
					foreach($res as $key => $val){
						//$this->assign($val['key_name'],$val['key_value']);
						if ($key != "remark"){
							$data = array();
							$data['key_value'] = $val;
							$add_table->where("key_name='".$key."'")->save($data);
						}
					}
					//生成缓存
					//自用
					S("GAMEBASE_CONFIG_WEB", $res);
					//静态文件
					$configrow = $add_table->order("key_name")->select();
					$arr = array();
					foreach($configrow as $key => $val){
						switch ($val['key_name']){
							case 'bankruptcy_gold' : $arr['a'] = (int)$val['key_value']; break;       //破产金币数
							case 'bankruptcy_num' : $arr['b'] = (int)$val['key_value']; break;        //破产次数
							case 'bindphone' : $arr['c'] = (int)$val['key_value']; break;             //绑定手机
							case 'expressgold' : $arr['d'] = (int)$val['key_value']; break;           //一个付费表情的金币数
							case 'GOLDMALL_SELL_MAX' : $arr['e'] = (int)$val['key_value']; break;     //金币商城最高售价范围
							case 'GOLDMALL_SELL_MIN' : $arr['f'] = (int)$val['key_value']; break;     //金币商城最低售价范围
							case 'GOLDMALL_TAX' : $arr['g'] = (int)$val['key_value']; break;          //金币商城税率(百分比)
							case 'horngold' : $arr['h'] = (int)$val['key_value']; break;              //发喇叭的金币数
							case 'kickplayergold' : $arr['i'] = (int)$val['key_value']; break;        //T人的金币数
							case 'kickplayerviplieve' : $arr['j'] = (int)$val['key_value']; break;    //T人最低VIP等级
							case 'LOWER_GOLD' : $arr['k'] = (int)$val['key_value']; break;            //用户金币小于这个数时，赠送
							case 'novice_award' : $arr['l'] = (int)$val['key_value']; break;          //新手奖励，推荐人推荐加入的人
							case 'viptablelevel' : $arr['m'] = (int)$val['key_value']; break;         //创建私人房的最低VIP等级
							case 'quickpaytips' : $arr['n'] = $val['key_value']; break;               //快充
							case 'RECOMMEND_AWARD' : $arr['o'] = (int)$val['key_value']; break;       //推荐奖励的金币
							//case 'recomm_hint' : $arr['p'] = $val['key_value']; break;              //推荐通知内容模板
							case 'registergivegold' : $arr['q'] = (int)$val['key_value']; break;      //注册赠送金币数
							case 'REGISTER_GIVE_GOLD' : $arr['r'] = (int)$val['key_value']; break;    //注册赠送金币
							case 'SYSTEM_GIVE_GOLD' : $arr['s'] = (int)$val['key_value']; break;      //系统赠送的金币,不足这个数时
							case 'SYSTEM_GIVE_GOLD_TIMES' : $arr['t'] = (int)$val['key_value']; break;//系统赠送金币的次数
							case 'SYS_CLIENT_VERTION' : $arr['u'] = $val['key_value']; break;         //版本号
							case 'DIAMOND_BL_MENO' : $arr['v'] = $val['key_value']; break;            //购买钻石比例说明
							case 'DIAMOND_KF_TEL' : $arr['w'] = $val['key_value']; break;             //购买钻石客服电话
							case 'ONLINE_SWITCH' : $arr['x'] = (int)$val['key_value']; break;         //用户在线判断开关(1开启0关闭)
							case 'broadcast_play_limit' : $arr['y'] = (int)$val['key_value']; break;  //新手发大喇叭牌局限制
							case 'private_play_limit' : $arr['z'] = (int)$val['key_value']; break;    //进私人房游戏局数限制
							case 'private_vip_limit' : $arr['a1'] = (int)$val['key_value']; break;    //进入私人房的最低VIP等级
							case 'quickpaytips_ios' : $arr['n_ios'] = $val['key_value']; break;          //IOS快充
							case 'CUSTOMER_AUTO_RECALL' : $arr['customer_auto_recall'] = $val['key_value']; break;          //客服自动回复内容
							default : break; 
						}
					}
					//print_r($configrow); 
					//dump($add_table->_sql());
					$pubtext = array('msg' => $arr,
									 'ts' => time());
					//print_r($arr); exit;
					S("GAMEBASE_CONFIG", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
					
					//生成静态文件
					$url = DB_HOST.U('Gameconfig/writetxt');
					$jingtxt = curlGET($url);
					//$result = $add_table->add($goods);
				}else if ($info['cate'] == "4"){
					$Table = $this->Table_prifix."room_config";
					$add_table = M($Table);
					$Table2 = $this->Table_prifix."room_gold_code";
					$add_table2 = M($Table2);
					$res = json_decode($info['logs'], true);
					$room = $add_table->select();
					//dump($add_table->_sql());
					foreach($room as $key => $val){
						$show1 = "displayorder".$val['room_id'];
						$show2 = "cell_gold".$val['room_id'];
						$show3 = "lower_limit_gold".$val['room_id'];
						$show4 = "high_limit_gold".$val['room_id'];
						$show5 = "min_cmp_round".$val['room_id'];
						$show6 = "max_round".$val['room_id'];
						$show7 = "tax_rate".$val['room_id'];
						$show8 = "room_name".$val['room_id'];
						$show9 = "max_cell_gold".$val['room_id'];
						$show10 = "room_type".$val['room_id'];
						$show11 = "pitrue_url".$val['room_id'];
						$show12 = "actionTime".$val['room_id'];
						$show13 = "online".$val['room_id'];
						$show14 = "guo_di".$val['room_id'];
						//echo $val['room_id'];
						if (!empty($res[$show8])){
							$data9 = array();
							$data9 = array('displayorder' => $res[$show1],
										   'cell_gold' => $res[$show2],
										   'lower_limit_gold' => $res[$show3],
										   'high_limit_gold' => $res[$show4],
										   'min_cmp_round' => $res[$show5],
										   'tax_rate' => $res[$show7],
										   'room_name' => $res[$show8],
										   'max_cell_gold' => $res[$show9],
										   'room_type' => $res[$show10],
										   'pitrue_url' => $res[$show11],
										   'guo_di' => $res[$show14],
										   'max_round' => ($res[$show6] - 1));
							$result = $add_table->where("room_id=".$val['room_id'])->save($data9);
							
							if ($val['room_id'] < 10){
								$room_gold_code = $add_table2->where('room_id='.$val['room_id'])->select();
								foreach($room_gold_code as $key1 => $val1){
									$show15 = "gold_code".$val['room_id'].$val1['code_id'];
									$data8 = array();
									$data8 = array('gold_code' => $res[$show15]);
									$result2 = $add_table2->where("code_id=".$val1['code_id'])->save($data8);
								}
							}
						}
						
						//dump($add_table->_sql());
					}
					//exit;
					//生成缓存
					//自用
					S("GAMEBASE_ROOM_WEB", $res);
					//静态文件
					$configrow = $add_table->where('room_id<6')->order("displayorder")->select();
					$arr = array();
					foreach($configrow as $key => $val){
						$show12 = "actionTime".$val['room_id'];
						$show13 = "online".$val['room_id'];
						$arr[$key+1]['actionTime'] = (int)$res[$show12];
						$arr[$key+1]['cellVm'] = (int)$val['cell_gold'];
						$arr[$key+1]['imageUrl'] = $val['pitrue_url'];
						$arr[$key+1]['lowerLimit'] = (int)$val['lower_limit_gold'];
						$arr[$key+1]['maxRound'] = (int)$val['max_round'];
						$arr[$key+1]['minRound'] = (int)$val['min_cmp_round'];
						$arr[$key+1]['online'] = (int)$res[$show13];
						$arr[$key+1]['roomName'] = $val['room_name'];
						$arr[$key+1]['tableLevel'] = (int)$val['room_id'];
						$arr[$key+1]['tableType'] = (int)$val['room_type'];
						$arr[$key+1]['topCellVm'] = (int)$val['max_cell_gold'];
						$arr[$key+1]['upperLimit'] = (int)$val['high_limit_gold'];
						$arr[$key+1]['guo_di'] = (int)$val['guo_di'];
						
						$count0 = $add_table2->where('room_id='.$val['room_id'])->count();
						if ($count0 > 0){
							$room_gold_code = $add_table2->where('room_id='.$val['room_id'])->select();
							foreach($room_gold_code as $key1 => $val1){
								$arr[$key+1]['vmSteps'][$key1+1] = (int)$val1['gold_code'];
							}
						}else{
							for($t=1; $t<=5; $t++){
								$arr[$key+1]['vmSteps'][$t] = 0;
							}
						}
					}
					//dump($add_table->_sql());
					$pubtext = array('msg' => $arr,
									 'ts' => time());
					S("GAMEBASE_ROOM", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
					
					$configrow = $add_table->order("displayorder")->select();
					$arr = array();
					foreach($configrow as $key => $val){
						$show12 = "actionTime".$val['room_id'];
						$show13 = "online".$val['room_id'];
						$arr[$key+1]['actionTime'] = (int)$res[$show12];
						$arr[$key+1]['cellVm'] = (int)$val['cell_gold'];
						$arr[$key+1]['imageUrl'] = $val['pitrue_url'];
						$arr[$key+1]['lowerLimit'] = (int)$val['lower_limit_gold'];
						$arr[$key+1]['maxRound'] = (int)$val['max_round'];
						$arr[$key+1]['minRound'] = (int)$val['min_cmp_round'];
						$arr[$key+1]['online'] = (int)$res[$show13];
						$arr[$key+1]['roomName'] = $val['room_name'];
						$arr[$key+1]['tableLevel'] = (int)$val['room_id'];
						$arr[$key+1]['tableType'] = (int)$val['room_type'];
						$arr[$key+1]['topCellVm'] = (int)$val['max_cell_gold'];
						$arr[$key+1]['upperLimit'] = (int)$val['high_limit_gold'];
						$arr[$key+1]['guo_di'] = (int)$val['guo_di'];
						
						$count0 = $add_table2->where('room_id='.$val['room_id'])->count();
						if ($count0 > 0){
							$room_gold_code = $add_table2->where('room_id='.$val['room_id'])->select();
							foreach($room_gold_code as $key1 => $val1){
								$arr[$key+1]['vmSteps'][$key1+1] = (int)$val1['gold_code'];
							}
						}else{
							for($t=1; $t<=5; $t++){
								$arr[$key+1]['vmSteps'][$t] = 0;
							}
						}
					}
					//dump($add_table->_sql());
					$pubtext = array('msg' => $arr,
									 'ts' => time());
					S("GAMEBASE_ROOM_NEW", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
					
					//生成静态文件
					$url = DB_HOST.U('Gameconfig/writetxt');
					$jingtxt = curlGET($url);
					//exit;
					//$result = $add_table->add($goods);
				}else if ($info['cate'] == "6"){
					$Table = $this->Table_prifix."profile_signin_data";
					$add_table = M($Table);
					$res = json_decode($info['logs'], true);
					
					$resnow = $add_table->field("DayIndex")->select();
					foreach($resnow as $key => $val){
						$show1 = "GoldNumber".$val['DayIndex'];

						$data9 = array();
						$data9 = array('GoldNumber' => $res[$show1]);
						$result = $add_table->where("DayIndex=".$val['DayIndex'])->save($data9);	

					}
					
					//生成缓存
					$configrow = $add_table->order("DayIndex")->select();
					$arr = array();
					foreach($configrow as $key => $val){
						$arr[$key] = (int)$val['GoldNumber'];        
  
						
					}
					//dump($add_table->_sql());
					$pubtext = array('msg' => $arr,
									 'ts' => time());
					S("MALL_SIGNIN_DATA", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
					
					//生成静态文件
					$url = DB_HOST.U('Gameconfig/writetxt');
					$jingtxt = curlGET($url);

				}else if ($info['cate'] == "7"){
					$Table = $this->Table_prifix."online_reward_conf";
					$add_table = M($Table);
					$res = json_decode($info['logs'], true);
					
					$resnow = $add_table->field("conf_id")->select();
					foreach($resnow as $key => $val){
						$show1 = "minute".$val['conf_id'];
						$show2 = "coin".$val['conf_id'];

						$data9 = array();
						$data9 = array('minute' => $res[$show1],
									   'coin' => $res[$show2]);
						$result = $add_table->where("conf_id=".$val['conf_id'])->save($data9);	

					}

				}else if ($info['cate'] == "8"){
					$Table = $this->Table_prifix."profile_vip_level_configure";
					$add_table = M($Table);
					$res = json_decode($info['logs'], true);
					
					$resnow = $add_table->field("viplevel")->select();
					foreach($resnow as $key => $val){
						$show1 = "paycount".$val['viplevel'];
						$show2 = "maxsavegold".$val['viplevel'];
						$show3 = "maxfriendnum".$val['viplevel'];
						$show4 = "logingivegold".$val['viplevel'];
						$show5 = "transfergoldrate".$val['viplevel'];
						$show6 = "raffleticketnum".$val['viplevel'];
						$show7 = "maxtransfergold".$val['viplevel'];
						$show8 = "maxsellgold".$val['viplevel'];
						$show9 = "maxsellnum".$val['viplevel'];
						$show10 = "maxsellflag".$val['viplevel'];
						$maxsellflag = ($res[$show10]=="1") ? "1" : "0";

						$data9 = array();
						$data9 = array('paycount' => ($res[$show1] * 100),
									   'maxsavegold' => $res[$show2],
									   'maxfriendnum' => $res[$show3],
									   'transfergoldrate' => $res[$show5],
									   'raffleticketnum' => $res[$show6],
									   'logingivegold' => $res[$show4],
									   'maxtransfergold' => $res[$show7],
									   'maxsellgold' => $res[$show8],
									   'maxsellflag' => $maxsellflag,
									   'maxsellnum' => $res[$show9]);
						$result = $add_table->where("viplevel=".$val['viplevel'])->save($data9);	
						
					}
					//生成缓存
					//自用
					S("USERVIP_CONFIG_WEB", $res);
					//静态文件
					$viprow = $add_table->order("viplevel")->select();
					$vip = array();
					foreach($viprow as $key => $val){
						$vip[$key]['v'] = (int)$val['viplevel'];            //VIP等级
						$vip[$key]['p'] = (int)$val['paycount'];            //支付金额,以分为单位
						$vip[$key]['g'] = (int)$val['maxsavegold'];         //保险箱额度
						$vip[$key]['f'] = (int)$val['maxfriendnum'];        //好友上限
						$vip[$key]['l'] = (int)$val['logingivegold'];       //签到加赠
						$vip[$key]['t'] = (int)$val['transfergoldrate'];    //转账手续费(-1是不能转)
						$vip[$key]['r'] = (int)$val['maxtransfergold'];     //单笔转账限额(0是不能转)
						$vip[$key]['a'] = (int)$val['raffleticketnum'];     //免费抽奖次数
						$vip[$key]['b'] = (int)$val['maxsellgold'];         //售卖最大单笔金币
						$vip[$key]['c'] = (int)$val['maxsellflag'];         //是否显示下拉
						$vip[$key]['n'] = (int)$val['maxsellnum'];          //允许同时在售笔数
					}
					//dump($add_table->_sql());
					$pubtext = array('msg' => $vip,
									 'ts' => time());
					S("USERVIP_CONFIG", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
					
					//生成静态文件
					$url = DB_HOST.U('Gameconfig/writetxt');
					$jingtxt = curlGET($url);

				}else if ($info['cate'] == "3"){
					$Table = $this->Table_prifix."profile_control_room_cards_config";
					$add_table = M($Table);
					$res = json_decode($info['logs'], true);
					
					$resnow = $add_table->field("roomtype")->select();
					foreach($resnow as $key => $val){
						$i = $val['roomtype'];
						$show1 = "delminnum".$i;
						$show2 = "delmax1rate".$i;
						$show3 = "delmax2rate".$i;
						$show4 = "delmax3rate".$i;
						$show5 = "playcount1".$i;
						$show6 = "startpos1".$i;
						$show7 = "playcount2".$i;
						$show8 = "startpos2".$i;
						$show9 = "playcount3".$i;
						$show10 = "startpos3".$i;
						$show11 = "playcount4".$i;
						$show12 = "startpos4".$i;
						$show13 = "playcount5".$i;
						$show14 = "startpos5".$i;


						$data9 = array();
						$data9 = array('delminnum' => $res[$show1],
									   'delmax1rate' => $res[$show2],
									   'delmax2rate' => $res[$show3],
									   'delmax3rate' => $res[$show4],
									   'playcount1' => $res[$show5],
									   'startpos1' => $res[$show6],
									   'playcount2' => $res[$show7],
									   'startpos2' => $res[$show8],
									   'playcount3' => $res[$show9],
									   'startpos3' => $res[$show10],
									   'playcount4' => $res[$show11],
									   'startpos4' => $res[$show12],
									   'playcount5' => $res[$show13],
									   'startpos5' => $res[$show14]);
						$result = $add_table->where("roomtype=".$i)->save($data9);	
					}

				}else if ($info['cate'] == "9"){
					$Table = $this->Table_prifix."task_daily_task_config";
					$add_table = M($Table);
					$res = json_decode($info['logs'], true);
					
					$resnow = $add_table->field("taskid")->select();
					foreach($resnow as $key => $val){
						$show1 = "value".$val['taskid'];
						$show2 = "reward".$val['taskid'];
						$show3 = "name".$val['taskid'];
						$show4 = "explain".$val['taskid'];

						$data9 = array();
						$data9 = array('value' => $res[$show1],
									   'reward' => $res[$show2],
									   'name' => $res[$show3],
									   'explain' => $res[$show4]);
						$result = $add_table->where("taskid=".$val['taskid'])->save($data9);	

					}

				}else if ($info['cate'] == "10"){
					$Table = $this->Table_prifix."profile_horn_min_gold_config";
					$add_table = M($Table);
					$res = json_decode($info['logs'], true);
					
					$resnow = $add_table->field("type")->select();
					foreach($resnow as $key => $val){
						$show1 = "mingold".$val['type'];
						$mingold = ($val['type'] == "7") ? ($res[$show1] * 100) : $res[$show1];
						$data9 = array();
						$data9 = array('mingold' => $mingold);
						$result = $add_table->where("type=".$val['type'])->save($data9);	

					}

				}else if ($info['cate'] == "11"){
					//添加系统喇叭
					$Table = $this->Table_prifix."profile_system_speaker_config";
					$add_table = M($Table);
					$res = json_decode($info['logs'], true);
					$result = $add_table->add($res);
					
					//生成缓存
					$xtlb = $add_table->field('id,type,RGB,content,intervaltime,displaytime,displaycount')->where("status=1")->order("id")->select();
					/*foreach($xtlb as $key => $val){
						$xtlb[$key]['displaycount'] = ($val['type']==2) ? -1 : $val['type'];
					}*/
					$pubtext = array('msg' => $xtlb,
									 'ts' => time());
					S("XTDLB", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
				}else if ($info['cate'] == "12"){
					//修改系统喇叭
					$Table = $this->Table_prifix."profile_system_speaker_config";
					$add_table = M($Table);
					$res = json_decode($info['logs'], true);
					$result = $add_table->where("id=".$res['id'])->save($res);
					
					//生成缓存
					$xtlb = $add_table->field('id,type,RGB,content,intervaltime,displaytime,displaycount')->where("status=1")->order("id")->select();
					/*
					foreach($xtlb as $key => $val){
						$xtlb[$key]['displaycount'] = $val['displaycount'];
					}*/
					$pubtext = array('msg' => $xtlb,
									 'ts' => time());
					S("XTDLB", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
				}else if ($info['cate'] == "13"){
					//删除系统喇叭
					$Table = $this->Table_prifix."profile_system_speaker_config";
					$add_table = M($Table);
					$res = json_decode($info['logs'], true);
					if (!empty($res['id'])){
						$result = $add_table->where("id=".$res['id'])->delete();
					}
					
					//生成缓存
					$xtlb = $add_table->field('id,type,RGB,content,intervaltime,displaytime,displaycount')->where("status=1")->order("id")->select();
					/*foreach($xtlb as $key => $val){
						$xtlb[$key]['displaycount'] = ($val['type']==2) ? -1 : $val['type'];
					}*/
					$pubtext = array('msg' => $xtlb,
									 'ts' => time());
					S("XTDLB", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
				}else if ($info['cate'] == "14"){
					$Table = $this->Table_prifix."profile_gift_config";
					$add_table = M($Table);
					$res = json_decode($info['logs'], true);
					
					$resnow = $add_table->field("id")->select();
					foreach($resnow as $key => $val){
						$show1 = "name".$val['id'];
						$show2 = "buygold".$val['id'];
						$show3 = "sellgold".$val['id'];
						$show4 = "charm".$val['id'];
						$show5 = "cansell".$val['id'];

						$data9 = array();
						$data9 = array('name' => $res[$show1],
									   'buygold' => $res[$show2],
									   'sellgold' => $res[$show3],
									   'charm' => $res[$show4],
									   'cansell' => $res[$show5]);
						$result = $add_table->where("id=".$val['id'])->save($data9);	

					}
					//生成缓存
					//自用
					S("GAMEBASE_GIFT_WEB", $res);
					//静态文件
					$configrow = $add_table->order("id")->select();
					$arr = array();
					foreach($configrow as $key => $val){
						$arr[$key]['id'] = (int)$val['id'];
						$arr[$key]['name'] = $val['name'];
						$arr[$key]['buygold'] = (int)$val['buygold'];
						$arr[$key]['sellgold'] = (int)$val['sellgold'];
						$arr[$key]['charm'] = (int)$val['charm'];
						$arr[$key]['cansell'] = (int)$val['cansell'];
					}
					//dump($add_table->_sql());
					$pubtext = array('msg' => $arr,
									 'ts' => time());
					S("GAMEBASE_GIFT", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
					
					//生成静态文件
					$url = DB_HOST.U('Gameconfig/writetxt');
					$jingtxt = curlGET($url);

				}else if ($info['cate'] == "15"){
					//添加银行开关
					$Table = $this->Table_prifix."bank_switch";
					$add_table = M($Table);
					$res = json_decode($info['logs'], true);
					$result = $add_table->add($res);
					
					//生成缓存
					$yhkg = $add_table->field('channel')->where("status=0")->order("channel,id")->select();
					$yhkg_cache = '';
					foreach($yhkg as $key => $val){
						if ($val['channel']==0){
							$yhkg_cache = 'ALL'; break;
						}else{
							$yhkg_cache .= (empty($yhkg_cache)) ? $val['channel'] : ','.$val['channel'];
						}
					}
					$pubtext = array('msg' => $yhkg_cache,
									 'ts' => time());
					S("GAMEBASE_YHKG", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
					
					//生成静态文件
					$url = DB_HOST.U('Gameconfig/writetxt');
					$jingtxt = curlGET($url);
					
				}else if ($info['cate'] == "16"){
					//修改银行开关
					$Table = $this->Table_prifix."bank_switch";
					$add_table = M($Table);
					$res = json_decode($info['logs'], true);
					$result = $add_table->where("id=".$res['id'])->save($res);
					
					//生成缓存
					$yhkg = $add_table->field('channel')->where("status=0")->order("channel,id")->select();
					$yhkg_cache = '';
					foreach($yhkg as $key => $val){
						if ($val['channel']==0){
							$yhkg_cache = 'ALL'; break;
						}else{
							$yhkg_cache .= (empty($yhkg_cache)) ? $val['channel'] : ','.$val['channel'];
						}
					}
					$pubtext = array('msg' => $yhkg_cache,
									 'ts' => time());
					S("GAMEBASE_YHKG", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
					
					//生成静态文件
					$url = DB_HOST.U('Gameconfig/writetxt');
					$jingtxt = curlGET($url);
					
				}else if ($info['cate'] == "17"){
					//删除银行开关
					$Table = $this->Table_prifix."bank_switch";
					$add_table = M($Table);
					$res = json_decode($info['logs'], true);
					if (!empty($res['id'])){
						$result = $add_table->where("id=".$res['id'])->delete();
					}
					
					//生成缓存
					$yhkg = $add_table->field('channel')->where("status=0")->order("channel,id")->select();
					$yhkg_cache = '';
					foreach($yhkg as $key => $val){
						if ($val['channel']==0){
							$yhkg_cache = 'ALL'; break;
						}else{
							$yhkg_cache .= (empty($yhkg_cache)) ? $val['channel'] : ','.$val['channel'];
						}
					}
					$pubtext = array('msg' => $yhkg_cache,
									 'ts' => time());
					S("GAMEBASE_YHKG", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
					
					//生成静态文件
					$url = DB_HOST.U('Gameconfig/writetxt');
					$jingtxt = curlGET($url);
					
				}else if ($info['cate'] == "18"){
					//添加登陆接口
					$Table = $this->Table_prifix."user_login_inter";
					$add_table = M($Table);
					$res = json_decode($info['logs'], true);
					$result = $add_table->add($res);
					
					//生成缓存
					$logininter = $add_table->order("id")->select();
					$pubtext = array('msg' => $logininter,
									 'ts' => time());
					S("GAMEBASE_LOGININTER", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
					
					//生成静态文件
					$url = DB_HOST.U('Gameconfig/writetxt');
					$jingtxt = curlGET($url);
					
				}else if ($info['cate'] == "19"){
					//修改银行开关
					$Table = $this->Table_prifix."user_login_inter";
					$add_table = M($Table);
					$res = json_decode($info['logs'], true);
					$result = $add_table->where("id=".$res['id'])->save($res);
					
					//生成缓存
					$logininter = $add_table->order("id")->select();
					$pubtext = array('msg' => $logininter,
									 'ts' => time());
					S("GAMEBASE_LOGININTER", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
					
					//生成静态文件
					$url = DB_HOST.U('Gameconfig/writetxt');
					$jingtxt = curlGET($url);
					
				}else if ($info['cate'] == "20"){
					//删除银行开关
					$Table = $this->Table_prifix."user_login_inter";
					$add_table = M($Table);
					$res = json_decode($info['logs'], true);
					if (!empty($res['id'])){
						$result = $add_table->where("id=".$res['id'])->delete();
					}
					
					//生成缓存
					$logininter = $add_table->order("id")->select();
					$pubtext = array('msg' => $logininter,
									 'ts' => time());
					S("GAMEBASE_LOGININTER", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
					
					//生成静态文件
					$url = DB_HOST.U('Gameconfig/writetxt');
					$jingtxt = curlGET($url);
					
				}else if ($info['cate'] == "21"){
					$Table = $this->Table_prifix."room_config";
					$add_table = M($Table);
					$Table2 = $this->Table_prifix."room_gold_code";
					$add_table2 = M($Table2);
					$res = json_decode($info['logs'], true);
					//$room = $add_table->select();
					//dump($add_table->_sql());
					$res0 = $add_table->order("displayorder")->select();
					$sql11 = "";
					foreach($res0 as $key => $val){
						$tempid = "room_id".$val['room_id'];
						if ($res[$tempid] == "1"){
							$sql11 .= empty($sql11) ? $val['room_id'] : ",".$val['room_id'];
						}
					}
					if (!empty($sql11)) $sql11 = " and room_id in ($sql11)";
					//exit;
					//生成缓存
					$configrow = $add_table->where('1'.$sql11)->order("displayorder")->select();
					
					$arr = array();
					foreach($configrow as $key => $val){

						$arr[$key+1]['actionTime'] = 20;
						$arr[$key+1]['cellVm'] = (int)$val['cell_gold'];
						$arr[$key+1]['imageUrl'] = $val['pitrue_url'];
						$arr[$key+1]['lowerLimit'] = (int)$val['lower_limit_gold'];
						$arr[$key+1]['maxRound'] = (int)$val['max_round'];
						$arr[$key+1]['minRound'] = (int)$val['min_cmp_round'];
						$arr[$key+1]['online'] = rand(100,500);
						$arr[$key+1]['roomName'] = $val['room_name'];
						$arr[$key+1]['tableLevel'] = (int)$val['room_id'];
						$arr[$key+1]['tableType'] = (int)$val['room_type'];
						$arr[$key+1]['topCellVm'] = (int)$val['max_cell_gold'];
						$arr[$key+1]['upperLimit'] = (int)$val['high_limit_gold'];
						$arr[$key+1]['guo_di'] = (int)$val['guo_di'];
						
						$count0 = $add_table2->where('room_id='.$val['room_id'])->count();
						if ($count0 > 0){
							$room_gold_code = $add_table2->where('room_id='.$val['room_id'])->select();
							foreach($room_gold_code as $key1 => $val1){
								$arr[$key+1]['vmSteps'][$key1+1] = (int)$val1['gold_code'];
							}
						}else{
							for($t=1; $t<=5; $t++){
								$arr[$key+1]['vmSteps'][$t] = 0;
							}
						}
					}
					//dump($add_table->_sql());
					//exit;
					$pubtext = array('msg' => $arr,
									 'channel' => $res['channel'],
									 'ts' => time());
					S("GAMEBASE_ROOM_CHANNEL", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
					
					//生成静态文件
					$url = DB_HOST.U('Gameconfig/writetxt');
					$jingtxt = curlGET($url);
					//exit;
					//$result = $add_table->add($goods);
				}else if ($info['cate'] == "22"){
					//添加登陆IP
					$Table = $this->Table_prifix."login_ip_config";
					$add_table = M($Table);
					$res = json_decode($info['logs'], true);
					$result = $add_table->add($res);
					
					//生成缓存
					$loginip = $add_table->order("id")->select();
					$pubtext = array('msg' => $loginip,
									 'ts' => time());
					S("GAMEBASE_LOGINIP", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
					
					//生成静态文件
					$url = DB_HOST.U('Gameconfig/writetxt');
					$jingtxt = curlGET($url);
					
				}else if ($info['cate'] == "23"){
					//修改登陆IP
					$Table = $this->Table_prifix."login_ip_config";
					$add_table = M($Table);
					$res = json_decode($info['logs'], true);
					$result = $add_table->where("id=".$res['id'])->save($res);
					
					//生成缓存
					$loginip = $add_table->order("id")->select();
					$pubtext = array('msg' => $loginip,
									 'ts' => time());
					S("GAMEBASE_LOGINIP", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
					
					//生成静态文件
					$url = DB_HOST.U('Gameconfig/writetxt');
					$jingtxt = curlGET($url);
					
				}else if ($info['cate'] == "24"){
					//删除登陆IP
					$Table = $this->Table_prifix."login_ip_config";
					$add_table = M($Table);
					$res = json_decode($info['logs'], true);
					if (!empty($res['id'])){
						$result = $add_table->where("id=".$res['id'])->delete();
					}
					
					//生成缓存
					$loginip = $add_table->order("id")->select();
					$pubtext = array('msg' => $loginip,
									 'ts' => time());
					S("GAMEBASE_LOGINIP", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
					
					//生成静态文件
					$url = DB_HOST.U('Gameconfig/writetxt');
					$jingtxt = curlGET($url);
					
				}else if ($info['cate'] == "25"){
					//添加游戏场景开关
					$Table = $this->Table_prifix."game_switch";
					$add_table = M($Table);
					$res = json_decode($info['logs'], true);
					
					$result = $add_table->add($res);
					
				}else if ($info['cate'] == "26"){
					//修改游戏场景开关
					$Table = $this->Table_prifix."game_switch";
					$add_table = M($Table);
					$res = json_decode($info['logs'], true);
					
					$result = $add_table->where("id=".$res['id'])->save($res);
				
				}else if ($info['cate'] == "27"){
					//删除游戏场景开关
					$Table = $this->Table_prifix."game_switch";
					$add_table = M($Table);
					$res = json_decode($info['logs'], true);
					if (!empty($res['id'])){
						$result = $add_table->where("id=".$res['id'])->delete();
					}
					
				}else if ($info['cate'] == "28"){
					//修改百人场配置
					$Table = $this->Table_prifix."brc_all_config";
					$add_table = M($Table);
					$res = json_decode($info['logs'], true);
					$result = $add_table->where("id=".$res['id'])->save($res);
					
					//生成缓存
					$brc_all_config = $add_table->order("id desc")->select();
					$pubtext = array('msg' => $brc_all_config,
									 'ts' => time());
					S("BRC_ALL_CONFIG", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
					
					//生成静态文件
					$url = DB_HOST.U('Gameconfig/writetxt');
					$jingtxt = curlGET($url);
				}else{
					//修改商品
					//$result = $add_table->where("GoodsID=".$goods['GoodsID'])->save($goods);
					//dump($add_table->_sql());	
				}
				//修改状态
				$data = array();
				$data['flag'] = '1';
				$data['pubtime'] = time();
				$data['pubname'] = $_SESSION['username'];
				$result = $row->where("id=".$id)->save($data);
			}else if ($act == "off"){
				//修改状态
				$data = array();
				$data['flag'] = '2';
				$data['pubtime'] = time();
				$data['pubname'] = $_SESSION['username'];
				$result = $row->where("id=".$id)->save($data);
			}
			if($result){
				echo "1";
			}else{
				echo "0";
			}
			exit;
		}
		
		//增加操作记录
		$logs = C('RECORD_HISTORY');
		$remark = "";
		adminlog($logs, $remark);
		//print_r(json_decode($info['logs'], true));
		$this->assign('info',json_decode($info['logs'], true));
		
		$this->assign('left_css',"28");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl.":".$page;
		$this->display($lib_display);
	}
	//查看详细配置结束
	
	//通知服务器开始
	public function notice(){
		$table = "user_record";
		$row = M($table);
		//通知服务器
		$flag = $_GET['flag'];
		$id = $_GET['id'];
		
		if ($flag == "5"){
			$url = DB_HOST."/Pay/shang.php?showindex=5&showtype=4";
		}else if ($flag == "6"){
			$url = DB_HOST."/Pay/shang.php?showindex=7&showtype=4";
		}else if ($flag == "7"){
			$url = DB_HOST."/Pay/shang.php?showindex=6&showtype=4";
		}else if ($flag == "8"){
			$url = DB_HOST."/Pay/shang.php?showindex=5&showtype=4";
		}else if ($flag == "3"){
			$url = DB_HOST."/Pay/shang.php?showindex=4&showtype=4";
		}else if ($flag == "9"){
			$url = DB_HOST."/Pay/shang.php?showindex=9&showtype=4";
		}else if ($flag == "10"){
			$url = DB_HOST."/Pay/shang.php?showindex=8&showtype=4";
		}else{
			$url = DB_HOST."/Pay/shang.php";
		}
		
		if (empty($id)){
			echo "0"; exit;
		}
		//echo $url;
		$result = curlGET($url);
		$len = strlen($result) - 3;
		$status = substr($result, $len, 1);
		
		if ($status == "1"){
			$data = array();
			$data['notice'] = "1";
			$data['noname'] = $_SESSION['username'];
			$data['nourl'] = $url;
			$result = $row->where("id=".$id)->save($data);
		}else{
			$data = array();
			$data['notice'] = "0";
			$data['noname'] = $_SESSION['username'];
			$data['nourl'] = $url;
			$result = $row->where("id=".$id)->save($data);
		}
		
		echo $status;
	}
	//通知服务器结束
	
	//通知服务器开始
	public function notice_brc(){
		//通知服务器
		$url = DB_HOST."/Pay/shang.php?showindex=11&showtype=4";
		$result = curlGET($url);
		$len = strlen($result) - 3;
		$status = substr($result, $len, 1);
		
		echo $status;
	}
	//通知服务器结束
	
	//获取待审核信息开始
	public function shenhemsg(){
		$shenhe_msg = "";
		if ($_SESSION['js_flag']=="1"){
			$shenhe = M('user_record');
			$count1 = $shenhe->where("cate in (101,102) and flag=0")->count('id'); 
			if ($count1 > 0) $shenhe_msg .= empty($shenhe_msg) ? "商品管理" : "，商品管理";
			$count2 = $shenhe->where("cate=104 and flag=0")->count('id'); 
			if ($count2 > 0) $shenhe_msg .= empty($shenhe_msg) ? "公告管理" : "，公告管理";
			$count3 = $shenhe->where("cate<100 and flag=0")->count('id'); 
			if ($count3 > 0) $shenhe_msg .= empty($shenhe_msg) ? "基本配置" : "，基本配置";
			if (!empty($shenhe_msg)) $shenhe_msg .= "有需要审核的内容，请尽快审核!";
		}
		echo  $shenhe_msg;
	}
	//获取待审核信息结束
}