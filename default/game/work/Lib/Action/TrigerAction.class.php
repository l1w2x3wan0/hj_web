<?php
// 老虎机管理的文件

class TrigerAction extends BaseAction {
	protected $By_tpl = 'Triger'; 
	protected $Table_prifix = MYTABLE_PRIFIX; 
	
	//老虎机配置开始
	public function Tconfig(){
		$table1 = $this->Table_prifix."manual_configure_tiger";
		$row1 = M($table1);
		$table2 = $this->Table_prifix."profile_tiger_configure";
		$row2 = M($table2);
		
		if(!empty($_POST)){
			$remark = $_POST['remark'];
			if (empty($remark)){
				$this->error('修改原因必须填写');
				exit;
			}
			
			/*
			$gold = $_POST['gold'];
			$mingold1 = $_POST['mingold1'];
			$mingold2 = $_POST['mingold2'];
			$systemwingold = $_POST['systemwingold'];
			$systemwingold = $systemwingold + $gold;
			
			$data1 = array('mingold' => -1,
						   'maxgold' => $mingold1,
						   'systemwingold' => $systemwingold);
			$result = $row1->where("tactics=1")->save($data1);	
			
			$data2 = array('mingold' => $mingold1,
						   'maxgold' => $mingold2);
			$result = $row1->where("tactics=2")->save($data2);
			
			$data3 = array('mingold' => $mingold2,
						   'maxgold' => -1);
			$result = $row1->where("tactics=3")->save($data3);
			
			for($i=0; $i<3; $i++){
				$show1 = "productrate".$i;
				$show2 = "tigerid".$i;
				foreach ($_POST[$show1] as $key => $val){
					$id = $_POST[$show2][$key];
					$data9 = array();
					$data9 = array('productrate' => $val);
					$result = $row2->where("id=".$id)->save($data9);	
				}
				//$data9 = array('productrate' => $val['productrate']);
				//$result = $row2->where("id=".$val['id'])->save($data9);	
				//dump($row2->_sql());
			}
			*/
			
			//增加操作记录
			//$logs = C('TRIGER_MSG_EDIT_SUCCESS');
			//$remark = "(".json_encode($_POST).")";
			//adminlog($logs, $remark);
			
			unset($_POST['__hash__']);
			unset($_POST['Submit']);
			
			//增加操作记录
			$logs = C('TRIGER_MSG_EDIT_SUCCESS');
			$remark2 = "(".json_encode($_POST, JSON_UNESCAPED_UNICODE).")";
			adminlog($logs, $remark2);
			
			//增加修改记录
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = $remark;
			$data['userip'] = get_client_ip();
			$data['cate'] = 51;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$table_name->add($data);
			
			//通知服务器
			//$url = DB_HOST."/Pay/shang.php?showindex=1";
			//$jinbi_result = curlGET($url);
			
			$this->success('提交成功，等待审核',U($this->By_tpl.'/Tconfig'));
			exit;
		}
		
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
		
		//增加操作记录
		$logs = C('TRIGER_MSG_RECORD');
		$remark = "";
		adminlog($logs, $remark);
		
		$this->assign('left_css',"16");
		$this->assign('list',$list);
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl.":Tconfig";
		$this->display($lib_display);
	}
	
	
	//老虎机配置结束
	
	//时时彩配置开始
	public function Cconfig(){
		$table1 = $this->Table_prifix."lottery_bet_control";
		$row1 = M($table1);
		$table2 = $this->Table_prifix."lottery_configure";
		$row2 = M($table2);
		
		if(!empty($_POST)){
			$remark = $_POST['remark'];
			if (empty($remark)){
				$this->error('修改原因必须填写');
				exit;
			}
			/*
			$deftaxrate = $_POST['deftaxrate'];
			$mintaxrate = $_POST['mintaxrate'];
			$maxtaxrate = $_POST['maxtaxrate'];
			$multiplebet = $_POST['multiplebet'];
			$xzmaxcount = $_POST['xzmaxcount'];
			$xztime = $_POST['xztime'];
			$jstime = $_POST['jstime'];
			
			$data1 = array('deftaxrate' => $deftaxrate,
						   'mintaxrate' => $mintaxrate,
						   'maxtaxrate' => $maxtaxrate,
						   'multiplebet' => $multiplebet,
						   'xzmaxcount' => $xzmaxcount,
						   'xztime' => $xztime,
						   'jstime' => $jstime);
			$result = $row2->where("id=1")->save($data1);	
			
			for($i=1; $i<=7; $i++){
				$show1 = "minuserbet".$i;
				$show2 = "maxuserbet".$i;
				$show3 = "minperrobitbet".$i;
				$show4 = "maxperrobitbet".$i;
				$show5 = "minrobitsumbet".$i;
				$show6 = "maxrobitsumbet".$i;

				$data9 = array();
				$data9 = array('minuserbet' => $_POST[$show1],
							   'maxuserbet' => $_POST[$show2],
							   'minperrobitbet' => $_POST[$show3],
							   'maxperrobitbet' => $_POST[$show4],
							   'minrobitsumbet' => $_POST[$show5],
							   'maxrobitsumbet' => $_POST[$show6]);
				$result = $row1->where("id=".$i)->save($data9);	

			}
			*/
			//增加操作记录
			//$logs = C('SSC_CONFIG_EDIT_SUCCESS');
			//$remark = "(".json_encode($_POST).")";
			//adminlog($logs, $remark);
			
			unset($_POST['__hash__']);
			unset($_POST['Submit']);
			
			//增加操作记录
			$logs = C('SSC_CONFIG_EDIT_SUCCESS');
			$remark2 = "(".json_encode($_POST, JSON_UNESCAPED_UNICODE).")";
			adminlog($logs, $remark2);
			
			//增加修改记录
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = $remark;
			$data['userip'] = get_client_ip();
			$data['cate'] = 52;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$table_name->add($data);
			
			//通知服务器
			//$url = DB_HOST."/Pay/shang.php?showindex=3";
			//$jinbi_result = curlGET($url);
			
			$this->success('提交成功，等待审核',U($this->By_tpl.'/Cconfig'));
			exit;
		}
		
		$list = $row1->order('id')->select();
		//print_r($list);
		$this->assign('list',$list);
		
		$info = $row2->where("id=1")->find();
		$this->assign('info',$info);
		
		//增加操作记录
		$logs = C('SSC_CONFIG_RECORD');
		$remark = "";
		adminlog($logs, $remark);
		
		$this->assign('left_css',"16");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl.":Cconfig";
		$this->display($lib_display);
	}
	
	//时时彩开关列表
	public function Cswitch(){
		$table = $this->Table_prifix."config_switch";
		$row = M($table);
		
		import('ORG.Util.Page');
		$count = $row->where("type=2")->count('id');
		$Page       = new Page($count,PAGE_SHOW);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		
		$list = $row->where("type=2")->order('id')->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($rowlist->_sql());
		foreach($list as $key=>$value){
			$list[$key]['ison'] = ($value['ison']=="1") ? "<font color='#FF0000'>启动</font>" : "关闭";
		}
		
		//增加操作记录
		$logs = C('SSC_MSG_RECORD');
		$remark = "";
		adminlog($logs, $remark);
		
		$this->assign('list',$list);
		$this->assign('pageshow',$show);
		$this->assign('left_css',"16");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl.":Cswitch";
		$this->display($lib_display);
	}
	
	//时时彩开关添加
	public function Cswitch_add(){
		if(!empty($_POST)){
			
			$remark = $_POST['remark'];
			if (empty($remark)){
				$this->error('修改原因必须填写');
				exit;
			}
			
			$Table = $this->Table_prifix."config_switch";
			$add_table = M($Table);
			
			unset($_POST['__hash__']);
			unset($_POST['Submit']);
			
			$count = $add_table->where("type=2 and channelid=".$_POST['channelid'])->count('channelid');
			if ($count > 0){
				$this->error('channelid已存在');
				exit;
			}
			
			//增加修改记录
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = $remark;
			$data['userip'] = get_client_ip();
			$data['cate'] = 53;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			//$result = $add_table->add($_POST);
			//dump($add_table->_sql()); exit;
			if($result){
				//增加操作记录
				$logs = C('SSC_MSG_ADD_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				//通知服务器
				//$url = DB_HOST."/Pay/shang.php?showindex=3";
				//$jinbi_result = curlGET($url);
				
				$this->success('提交成功，等待审核', U($this->By_tpl.'/Cswitch'));
				exit;
			}else{
				//增加操作记录
				$logs = C('SSC_MSG_ADD_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('添加失败');
				exit;
			}
		}else{
			//增加操作记录
			$logs = C('SSC_MSG_ADD');
			$remark = "";
			adminlog($logs, $remark);
		
			$this->assign('left_css',"16");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":Cswitch_add";
			$this->display($lib_display);
		}
		
	}

	//时时彩开关更新
	public function Cswitch_edit(){
		$Table = $this->Table_prifix."config_switch";
		$upate_table = M($Table);
		if(!empty($_POST)){
			
			$remark = $_POST['remark'];
			if (empty($remark)){
				$this->error('修改原因必须填写');
				exit;
			}
			
			$id = $_POST['id'];
			unset($_POST['__hash__']);
			unset($_POST['Submit']);
			
			//增加修改记录
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = $remark;
			$data['userip'] = get_client_ip();
			$data['cate'] = 54;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			//$where = array();
			//$where['id']=intval($id);
			//$result=$upate_table->where($where)->save($_POST);
			//dump($upate_table->_sql());
			//exit;
			if($result){
				//增加操作记录
				$logs = C('SSC_MSG_EDIT_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				//通知服务器
				//$url = DB_HOST."/Pay/shang.php?showindex=3";
				//$jinbi_result = curlGET($url);
				
				$this->success('提交成功，等待审核',U($this->By_tpl.'/Cswitch'));
			}else{
				//增加操作记录
				$logs = C('SSC_MSG_EDIT_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('修改失败');
			}
		}else{
			//增加操作记录
			$logs = C('SSC_MSG_EDIT');
			$remark = "";
			adminlog($logs, $remark);
			
			$id['id']=$_GET['id'];
			$info = $upate_table->where($id)->find();
			$this->assign('info',$info);
			$this->assign('left_css',"16");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":Cswitch_edit";
			$this->display($lib_display);
		}
	}
	
	//时时彩开关删除
	public function Cswitch_delete(){
		if(empty($_GET)){ 
			$this->error('非法操作');
			exit;
		}else{
			$id=$_GET['id']?$_GET['id']:$_POST['id'];
			$Tablename = $this->Table_prifix."config_switch";
			$delete_table = M($Tablename);
			//$where['id']=$id;
			//$result=$delete_table->where($where)->delete();
			
			$info = $delete_table->where("id=".$id)->find();
			
			$arr = array();
			$arr['id'] = $id;
			$arr['tablename'] = $Tablename;
			$arr['act'] = "del";
			$arr['info'] = $info;
			
			//增加修改记录
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($arr, JSON_UNESCAPED_UNICODE);
			$data['remark'] = "删除时时彩开关";
			$data['userip'] = get_client_ip();
			$data['cate'] = 55;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			if($result){
				//增加操作记录
				$logs = C('SSC_MSG_DEL_SUCCESS');
				$remark = "(".$id.")";
				adminlog($logs, $remark);
				
				//通知服务器
				//$url = DB_HOST."/Pay/shang.php?showindex=3";
				//$jinbi_result = curlGET($url);
				
				$this->success('提交成功，等待审核', U($this->By_tpl.'/Cswitch'));
			}else{
				//增加操作记录
				$logs = C('SSC_MSG_DEL_FALSE');
				$remark = "(".$GoodsID.")";
				adminlog($logs, $remark);
				
				$this->error('删除失败');
				exit;
			}
		}
	}
	//时时彩配置结束
	
	//抽奖列表
	public function choujiang(){
		$table = $this->Table_prifix."profile_lottery_draw_config";
		$row = M($table);
		
		$vernow = I("vernow");
		$list = $row->field('version')->group("version")->order('version DESC')->select();
		$ver = array();
		foreach($list as $key => $val){
			$ver[$key]['version'] = $val['version'];
			if (empty($vernow) && $key==0) $vernow = $val['version'];
			$ver[$key]['showname'] = ($vernow == $val['version']) ? '<font color="#FF0000">抽奖版本'.$val['version'].'</font>' : '抽奖版本'.$val['version']; 
		}
		$this->assign('ver',$ver);
		
		$list = $row->where('version='.$vernow)->order('version DESC,goodsid')->limit(0,12)->select();
		$sum = array(0,0,0);
		$arr = array();
		foreach($list as $key => $val){
			$sum[0] += $val['freerate'];
			$sum[1] += $val['goldrate'];
			$sum[2] += $val['diamondrate'];
			$arr[$key] = array('goodsid' => $val['goodsid']);
		}
		
		//import('ORG.Util.Page');
		//$count = $row->count('goodsid');
		//$Page       = new Page($count,PAGE_SHOW);//实例化分页类传入总记录数和每页显示的记录数		
		//$show       = $Page->show();// 分页显示输出
		if(!empty($_POST)){
			
			$remark = $_POST['remark'];
			if (empty($remark)){
				$this->error('修改原因必须填写');
				exit;
			}
			
			$sum0 = 0;
			$sum1 = 0;
			$sum2 = 0;
			foreach($list as $key => $val){
				$temp1 = "freerate".$val['goodsid'];
				$sum0 += $_POST[$temp1];
				
				$temp2 = "goldrate".$val['goodsid'];
				$sum1 += $_POST[$temp2];
				
				$temp3 = "diamondrate".$val['goodsid'];
				$sum2 += $_POST[$temp3];
			}
			if ($sum0 != 10000){
				$this->error('免费概率和必须为10000');
				exit;
			}
			if ($sum1 != 10000){
				$this->error('金币概率和必须为10000');
				exit;
			}
			
			if ($sum2 != 10000){
				$this->error('钻石概率和必须为10000');
				exit;
			}
			
			unset($_POST['__hash__']);
			unset($_POST['Submit']);
			
			//增加操作记录
			$logs = C('CHOUJIANG_MSG_EDIT_SUCCESS');
			$remark2 = "(".json_encode($_POST, JSON_UNESCAPED_UNICODE).")";
			adminlog($logs, $remark2);
			
			//增加修改记录
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = $remark;
			$data['userip'] = get_client_ip();
			$data['cate'] = 56;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$table_name->add($data);
			
			
			$this->success('提交成功，等待审核',U($this->By_tpl.'/choujiang'));
			exit;
		}
		
		
		
		
		//增加操作记录
		$logs = C('CHOUJIANG_MSG_RECORD');
		$remark = "";
		adminlog($logs, $remark);
		
		$this->assign('list',$list);
		$this->assign('sum',$sum);
		$this->assign('arr',json_encode($arr));
		$this->assign('left_css',"16");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl.":choujiang";
		$this->display($lib_display);
	}
	
	//抽奖添加
	public function choujiang_add(){
		if(!empty($_POST)){
			
			$remark = $_POST['remark'];
			if (empty($remark)){
				$this->error('修改原因必须填写');
				exit;
			}
			
			$Table = $this->Table_prifix."profile_lottery_draw_config";
			$add_table = M($Table);
			
			unset($_POST['__hash__']);
			unset($_POST['Submit']);
			
			$count = $add_table->where("goodsid=".$_POST['goodsid'])->count('goodsid');
			if ($count > 0){
				$this->error('goodsid已存在');
				exit;
			}
			
			//增加修改记录
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = $remark;
			$data['userip'] = get_client_ip();
			$data['cate'] = 56;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			//$result = $add_table->add($_POST);
			//dump($add_table->_sql()); exit;
			if($result){
				//增加操作记录
				$logs = C('CHOUJIANG_MSG_ADD_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->success('提交成功，等待审核', U($this->By_tpl.'/choujiang'));
				exit;
			}else{
				//增加操作记录
				$logs = C('CHOUJIANG_MSG_ADD_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('添加失败');
				exit;
			}
		}else{
			//增加操作记录
			$logs = C('CHOUJIANG_MSG_ADD');
			$remark = "";
			adminlog($logs, $remark);
		
			$this->assign('left_css',"16");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":choujiang_add";
			$this->display($lib_display);
		}
		
	}

	//抽奖更新
	public function choujiang_edit(){
		$Table = $this->Table_prifix."profile_lottery_draw_config";
		$upate_table = M($Table);
		if(!empty($_POST)){
			
			$remark = $_POST['remark'];
			if (empty($remark)){
				$this->error('修改原因必须填写');
				exit;
			}
			
			$goodsid = $_POST['goodsid'];
			unset($_POST['__hash__']);
			unset($_POST['Submit']);
			
			//增加修改记录
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = $remark;
			$data['userip'] = get_client_ip();
			$data['cate'] = 57;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			//$where = array();
			//$where['id']=intval($id);
			//$result=$upate_table->where($where)->save($_POST);
			//dump($upate_table->_sql());
			//exit;
			if($result){
				//增加操作记录
				$logs = C('CHOUJIANG_MSG_EDIT_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->success('提交成功，等待审核',U($this->By_tpl.'/choujiang'));
			}else{
				//增加操作记录
				$logs = C('CHOUJIANG_MSG_EDIT_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('修改失败');
			}
		}else{
			//增加操作记录
			$logs = C('CHOUJIANG_MSG_EDIT');
			$remark = "";
			adminlog($logs, $remark);
			
			$id['goodsid'] = $_GET['goodsid'];
			$info = $upate_table->where($id)->find();
			$this->assign('info',$info);
			$this->assign('left_css',"16");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":choujiang_edit";
			$this->display($lib_display);
		}
	}
	
	//抽奖删除
	public function choujiang_delete(){
		if(empty($_GET)){ 
			$this->error('非法操作');
			exit;
		}else{
			$goodsid = $_GET['goodsid']?$_GET['goodsid']:$_POST['goodsid'];
			$Tablename = $this->Table_prifix."profile_lottery_draw_config";
			$delete_table = M($Tablename);
			//$where['id']=$id;
			//$result=$delete_table->where($where)->delete();
			
			$info = $delete_table->where("goodsid=".$goodsid)->find();
			$info['tablename'] = $Tablename;
			$info['act'] = "del";

			
			//增加修改记录
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($info, JSON_UNESCAPED_UNICODE);
			$data['remark'] = "删除抽奖记录";
			$data['userip'] = get_client_ip();
			$data['cate'] = 58;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			if($result){
				//增加操作记录
				$logs = C('CHOUJIANG_MSG_DEL_SUCCESS');
				$remark = "(".$id.")";
				adminlog($logs, $remark);
				
				$this->success('提交成功，等待审核', U($this->By_tpl.'/Cswitch'));
			}else{
				//增加操作记录
				$logs = C('SSC_MSG_DEL_FALSE');
				$remark = "(".$GoodsID.")";
				adminlog($logs, $remark);
				
				$this->error('删除失败');
				exit;
			}
		}
	}
	//抽奖配置结束
	
	//机器人抽奖
	public function robertcj(){
		//$Table = $this->Table_prifix."profile_lottery_draw_config";
		//$upate_table = M($Table);
		if(!empty($_POST)){
			
			$remark = $_POST['remark'];
			if (empty($remark)){
				$this->error('修改原因必须填写');
				exit;
			}
			
			//$goodsid = $_POST['goodsid'];
			unset($_POST['__hash__']);
			unset($_POST['Submit']);
			
			//增加修改记录
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = $remark;
			$data['userip'] = get_client_ip();
			$data['cate'] = 59;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			//$where = array();
			//$where['id']=intval($id);
			//$result=$upate_table->where($where)->save($_POST);
			//dump($upate_table->_sql());
			//exit;
			if($result){
				//增加操作记录
				$logs = C('CHOUJIANG_MSG_EDIT_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->success('提交成功，等待审核',U($this->By_tpl.'/robertcj'));
			}else{
				//增加操作记录
				$logs = C('ROBERT_CHOUJIANG_EDIT_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('修改失败');
			}
		}else{
			//增加操作记录
			$logs = C('ROBERT_CHOUJIANG_RECORD');
			$remark = "";
			adminlog($logs, $remark);
			
			$robertchou = S("ROBERT_CHOUJIANG");
			if (empty($robertchou)){
				$arr = array('show1' => '中奖10元',
										'pei1' => '50',
										'show2' => '中奖50元',
										'pei2' => '120',
										'show3' => '中奖100W金币',
										'pei3' => '15',
										'show4' => '中奖1000W金币',
										'pei4' => '20');
				S("ROBERT_CHOUJIANG", $arr);	
				$robertchou = S("ROBERT_CHOUJIANG");		
			}
			//print_r($robertchou); exit;
			$this->assign('info',$robertchou);
			$this->assign('left_css',"16");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":robertcj";
			$this->display($lib_display);
		}
	}
	
	
	//历史修改记录开始
	public function history(){
		$table = "user_record";
		$row = M($table);
		
		import('ORG.Util.Page');
		$count = $row->count('id');
		$Page       = new Page($count,PAGE_SHOW);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		
		$list = $row->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($row->_sql());
		foreach($list as $key=>$value){
			if ($value['cate']=="1"){
				$list[$key]['cate'] = "老虎机";
			}else if ($value['cate']=="2"){
				$list[$key]['cate'] = "时时彩";
			}else if ($value['cate']=="3"){
				$list[$key]['cate'] = "发牌参数";
			}else if ($value['cate']=="4"){
				$list[$key]['cate'] = "房间配置";
			}else if ($value['cate']=="5"){
				$list[$key]['cate'] = "常规配置";
			}else if ($value['cate']=="6"){
				$list[$key]['cate'] = "登陆奖励配置";
			}else if ($value['cate']=="7"){
				$list[$key]['cate'] = "在线宝箱配置";
			}else if ($value['cate']=="8"){
				$list[$key]['cate'] = "VIP配置";
			}
			$list[$key]['addtime'] = date("Y-m-d H:i:s", $value['addtime']);
		}
		
		//增加操作记录
		$logs = C('RECORD_HISTORY');
		$remark = "";
		adminlog($logs, $remark);
		//print_r($list);
		$this->assign('list',$list);
		$this->assign('pageshow',$show);
		
		$this->assign('left_css',"16");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl.":history";
		$this->display($lib_display);
	}
	//历史修改记录结束
	
	//查看详细配置开始
	public function history_more(){
		$table = "user_record";
		$row = M($table);
		
		$id = I("id");
		if (empty($id)){
			$this->error('输入有误');
			exit;
		}

		$info = $row->where('id='.$id)->find();
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
		}else if ($info['cate']=="5"){
			$page = "more5";
		}else if ($info['cate']=="6"){
			$page = "more6";
		}else if ($info['cate']=="7"){
			$page = "more7";
		}else if ($info['cate']=="8"){
			$page = "more8";
		}else{
			$page = "history";
		} 
		
		//增加操作记录
		$logs = C('RECORD_HISTORY');
		$remark = "";
		adminlog($logs, $remark);
		//print_r(json_decode($info['logs'], true));
		$this->assign('info',json_decode($info['logs'], true));
		
		$this->assign('left_css',"16");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl.":".$page;
		$this->display($lib_display);
	}
	//查看详细配置结束
	
	//审核基本配置开始
	public function shenhe(){
		$table = "user_record";
		$row = M($table);
		
		$sql0 = "";
		if ($_SESSION['js_flag']!="1"){$sql0 .= " and username='".$_SESSION['username']."'";}
		
		import('ORG.Util.Page');
		$count = $row->where("cate>50 and cate<65 $sql0")->count('id');
		$Page       = new Page($count,PAGE_SHOW);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		
		$list = $row->where("cate>50 and cate<65 $sql0")->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($row->_sql());
		foreach($list as $key=>$value){
			if ($value['cate']=="51"){
				$list[$key]['showcate'] = "老虎机";
			}else if ($value['cate']=="52"){
				$list[$key]['showcate'] = "时时彩";
			}else if ($value['cate']=="53"){
				$list[$key]['showcate'] = "添加时时彩开关";
			}else if ($value['cate']=="54"){
				$list[$key]['showcate'] = "编辑时时彩开关";
			}else if ($value['cate']=="55"){
				$list[$key]['showcate'] = "删除时时彩开关";
			}else if ($value['cate']=="56"){
				$list[$key]['showcate'] = "抽奖修改";
			}else if ($value['cate']=="57"){
				$list[$key]['showcate'] = "编辑抽奖记录";
			}else if ($value['cate']=="58"){
				$list[$key]['showcate'] = "删除抽奖记录";
			}else if ($value['cate']=="59"){
				$list[$key]['showcate'] = "机器人抽奖配置";
			}
			
			$list[$key]['addtime'] = date("Y-m-d H:i:s", $value['addtime']);
			if ($value['flag']=="0"){
				$list[$key]['flagshow'] = "<font color='#FF0000'>待审核</font>";
			}else if ($value['flag']=="1"){
				$list[$key]['flagshow'] = "审核通过";
			}else if ($value['flag']=="2"){
				$list[$key]['flagshow'] = "审核取消";
			}
			
			if ($value['cate']=="4"){
				$list[$key]['flagshow'] .= "&nbsp;需重启服务器";
			}else if ($value['cate']=="59"){
				
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
		
		$this->assign('left_css',"16");
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
		if ($info['cate']=="51"){
			
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
		}else if ($info['cate']=="52"){
			
			$table1 = $this->Table_prifix."lottery_bet_control";
			$row1 = M($table1);
			$table2 = $this->Table_prifix."lottery_configure";
			$row2 = M($table2);
			$list = $row1->order('id')->select();
			$this->assign('list',$list);
			
			$page = "more2";
		}else if ($info['cate']=="53"){
			$page = "more3";
		}else if ($info['cate']=="54"){
			$page = "more4";
		}else if ($info['cate']=="55"){
			$page = "more5";
		}else if ($info['cate']=="56"){
			$page = "more6";
			
			$chou = json_decode($info['logs'], true);
			$Table = $this->Table_prifix."profile_lottery_draw_config";
			$add_table = M($Table);
			$sum = array(0,0);
			$res0 = $add_table->order("goodsid")->select();
			$show0 = array();
			$key0 = 0;
			foreach($res0 as $key => $val){
				
				$res0[$key]['show1'] = $chou["awardtype".$val['goodsid']];
				$res0[$key]['show2'] = $chou["awardnum".$val['goodsid']];
				$res0[$key]['show3'] = $chou["freerate".$val['goodsid']];
				$res0[$key]['show4'] = $chou["memo".$val['goodsid']];
				$res0[$key]['show5'] = $chou["goldrate".$val['goodsid']];
				$res0[$key]['show6'] = $chou["diamondrate".$val['goodsid']];
				
				if (!empty($res0[$key]['show1']) ){
					
					$show0[$key0]['goodsid'] = $res0[$key]['goodsid'];
					$show0[$key0]['version'] = $res0[$key]['version'];
					$show0[$key0]['show1'] = $res0[$key]['show1'];
					$show0[$key0]['show2'] = $res0[$key]['show2'];
					$show0[$key0]['show3'] = $res0[$key]['show3'];
					$show0[$key0]['show4'] = $res0[$key]['show4'];
					$show0[$key0]['show5'] = $res0[$key]['show5'];
					$show0[$key0]['show6'] = $res0[$key]['show6'];
					$key0++;
					
					$sum[0] += $chou["freerate".$val['goodsid']];
					$sum[1] += $chou["goldrate".$val['goodsid']];
					$sum[2] += $chou["diamondrate".$val['goodsid']];
 				}
				
			}
			//print_r($show0);
			$this->assign('list',$show0);
			$this->assign('sum',$sum);
		}else if ($info['cate']=="57"){
			$page = "more7";
		}else if ($info['cate']=="58"){
			$page = "more8";
		}else if ($info['cate']=="59"){
			$page = "more9";
			
			$robertchou = S("ROBERT_CHOUJIANG");
			if (empty($robertchou)){
				$arr = array('show1' => '中奖10元',
										'pei1' => '50',
										'show2' => '中奖50元',
										'pei2' => '120',
										'show3' => '中奖100W金币',
										'pei3' => '15',
										'show4' => '中奖1000W金币',
										'pei4' => '20');
				S("ROBERT_CHOUJIANG", $arr);	
				$robertchou = S("ROBERT_CHOUJIANG");		
			}
			$this->assign('robertchou',$robertchou);
		}else{
			$page = "history";
		} 
		
		$act = I("act");
		if (!empty($act)){
			
			if ($act == "on"){
				if ($info['cate'] == "51"){
					
					$table1 = $this->Table_prifix."manual_configure_tiger";
					$row1 = M($table1);
					$table2 = $this->Table_prifix."profile_tiger_configure";
					$row2 = M($table2);
					$res = json_decode($info['logs'], true);
					
					$gold = $res['gold'];
					$mingold1 = $res['mingold1'];
					$mingold2 = $res['mingold2'];
					$systemwingold = $res['systemwingold'];
					$systemwingold = $systemwingold + $gold;
					
					$data1 = array('mingold' => -1,
								   'maxgold' => $mingold1,
								   'systemwingold' => $systemwingold);
					$result = $row1->where("tactics=1")->save($data1);	
					
					$data2 = array('mingold' => $mingold1,
								   'maxgold' => $mingold2);
					$result = $row1->where("tactics=2")->save($data2);
					
					$data3 = array('mingold' => $mingold2,
								   'maxgold' => -1);
					$result = $row1->where("tactics=3")->save($data3);
					
					for($i=0; $i<3; $i++){
						$show1 = "productrate".$i;
						$show2 = "tigerid".$i;
						foreach ($res[$show1] as $key => $val){
							//$id = $res[$show2][$key];
							$data9 = array();
							$data9 = array('productrate' => $val);
							$result = $row2->where("id=".$res[$show2][$key])->save($data9);	
						}
						//$data9 = array('productrate' => $val['productrate']);
						//$result = $row2->where("id=".$val['id'])->save($data9);	
						//dump($row2->_sql());
					}
					//$result = $add_table->add($goods);
				}else if ($info['cate'] == "52"){
					
					$table1 = $this->Table_prifix."lottery_bet_control";
					$row1 = M($table1);
					$table2 = $this->Table_prifix."lottery_configure";
					$row2 = M($table2);
					$res = json_decode($info['logs'], true);
					
					$deftaxrate = $res['deftaxrate'];
					$mintaxrate = $res['mintaxrate'];
					$maxtaxrate = $res['maxtaxrate'];
					$multiplebet = $res['multiplebet'];
					$xzmaxcount = $res['xzmaxcount'];
					$xztime = $res['xztime'];
					$jstime = $res['jstime'];
					
					$data1 = array('deftaxrate' => $deftaxrate,
								   'mintaxrate' => $mintaxrate,
								   'maxtaxrate' => $maxtaxrate,
								   'multiplebet' => $multiplebet,
								   'xzmaxcount' => $xzmaxcount,
								   'xztime' => $xztime,
								   'jstime' => $jstime);
					$result = $row2->where("id=1")->save($data1);	
					
					for($i=1; $i<=7; $i++){
						$show1 = "minuserbet".$i;
						$show2 = "maxuserbet".$i;
						$show3 = "minperrobitbet".$i;
						$show4 = "maxperrobitbet".$i;
						$show5 = "minrobitsumbet".$i;
						$show6 = "maxrobitsumbet".$i;

						$data9 = array();
						$data9 = array('minuserbet' => $res[$show1],
									   'maxuserbet' => $res[$show2],
									   'minperrobitbet' => $res[$show3],
									   'maxperrobitbet' => $res[$show4],
									   'minrobitsumbet' => $res[$show5],
									   'maxrobitsumbet' => $res[$show6]);
						$result = $row1->where("id=".$i)->save($data9);	

					}
					//exit;
					//$result = $add_table->add($goods);
				}else if ($info['cate'] == "53"){
					$Table = $this->Table_prifix."config_switch";
					$add_table = M($Table);
					$res = json_decode($info['logs'], true);
					
					$data9 = array();
					$data9 = array('type' => $res['type'],
								   'channelid' => $res['channelid'],
								   'ison' => $res['ison']);
					$result = $add_table->add($data9);
					
				}else if ($info['cate'] == "54"){
					$Table = $this->Table_prifix."config_switch";
					$add_table = M($Table);
					$res = json_decode($info['logs'], true);
					
					$data9 = array();
					$data9 = array('channelid' => $res['channelid'],
								   'ison' => $res['ison']);
					$result = $add_table->where("id=".$res['id'])->save($data9);			   

				}else if ($info['cate'] == "55"){

					$res = json_decode($info['logs'], true);
					$add_table = M($res['tablename']);
					if (!empty($res['id'])){
						$result = $add_table->where("id=".$res['id'])->delete();
					}

				}else if ($info['cate'] == "56"){
					
					$table1 = $this->Table_prifix."profile_lottery_draw_config";
					$row1 = M($table1);
					$res = json_decode($info['logs'], true);
					
					$list = $row1->order('version DESC,goodsid')->select();
					foreach ($list as $key => $val){
						$show1 = "awardtype".$val['goodsid'];
						$show2 = "awardnum".$val['goodsid'];
						$show3 = "freerate".$val['goodsid'];
						$show4 = "memo".$val['goodsid'];
						$show5 = "goldrate".$val['goodsid'];
						$show6 = "diamondrate".$val['goodsid'];

						if (!empty($res[$show1])){
							$data9 = array();
							$data9 = array('awardtype' => $res[$show1],
										   'awardnum' => $res[$show2],
										   'freerate' => $res[$show3],
										   'goldrate' => $res[$show5],
										   'diamondrate' => $res[$show6],
										   'memo' => $res[$show4]);
							$result = $row1->where("goodsid=".$val['goodsid'])->save($data9);
						}
					}
					//exit;
					//生成大转轮缓存
					$configrow = $row1->order("goodsid")->select();
					$arr = array();
					foreach($configrow as $key => $val){
						$arr[$key]['a'] = (int)$val['goodsid'];            
						$arr[$key]['b'] = (int)$val['awardtype'];            
						$arr[$key]['c'] = (int)$val['awardnum'];        
						//$arr[$key]['d'] = (int)$val['freerate'];        
						//$arr[$key]['e'] = (int)$val['goldrate'];       
						$arr[$key]['f'] = (int)$val['version'];   
						$arr[$key]['g'] = $val['memo'];     
 
					}
					//dump($add_table->_sql());
					$pubtext = array('msg' => $arr,
									 'ts' => time());
					S("LOTTERY_DATA", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
					
					//生成静态文件
					$url = DB_HOST.U('Gameconfig/writetxt');
					$jingtxt = curlGET($url);
					//echo $url."**".$jingtxt; exit;
					//$result = $add_table->add($goods);
				}else if ($info['cate'] == "59"){
					
					$res = json_decode($info['logs'], true);
					$arr = array('show1' => '中奖10元',
										'pei1' => $res['pei1'],
										'show2' => '中奖50元',
										'pei2' => $res['pei2'],
										'show3' => '中奖100W金币',
										'pei3' => $res['pei3'],
										'show4' => '中奖1000W金币',
										'pei4' => $res['pei4']);
					S("ROBERT_CHOUJIANG", $arr);	
					//exit;
					//$result = $add_table->add($goods);
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
				//dump($row->_sql());	
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
		
		$this->assign('left_css',"16");
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
		
		if ($flag == "51"){
			$url = DB_HOST."/Pay/shang.php?showindex=1";
		}else if ($flag == "52"){
			$url = DB_HOST."/Pay/shang.php?showindex=3";
		}else if ($flag == "53"){
			$url = DB_HOST."/Pay/shang.php?showindex=3";
		}else if ($flag == "54"){
			$url = DB_HOST."/Pay/shang.php?showindex=3";
		}else if ($flag == "55"){
			$url = DB_HOST."/Pay/shang.php?showindex=3";
		}else if ($flag == "56"){
			$url = DB_HOST."/Pay/shang.php?showindex=10&showtype=4";
		}else{
			$url = DB_HOST."/Pay/shang.php?showindex=1";
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
		} 
		
		echo $status;
	}
	//通知服务器结束
}