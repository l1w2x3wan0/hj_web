<?php
// 商品管理的文件

class NoticeAction extends BaseAction {
	protected $By_tpl = 'Notice'; 
	protected $Table_prifix = MYTABLE_PRIFIX; 
	//商品开始
	//商品列表
	public function index(){
		
		$sql0 = "";
		if ($_SESSION['js_flag']!="1"){$sql0 .= " and username='".$_SESSION['username']."'";}
		
		$rowlist = M("user_record");
		import('ORG.Util.Page');
		$count = $rowlist->where("cate=104 $sql0")->count('id');
		$Page       = new Page($count,PAGE_SHOW);//实例化分页类传入总记录数和每页显示的记录数		
		$pageshow       = $Page->show();// 分页显示输出
		
		$list = $rowlist->where("cate=104 $sql0")->order('flag,id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($rowlist->_sql());
		foreach($list as $key=>$value){
			if ($value['flag']=="0"){
				$list[$key]['flagshow'] = "<font color='#FF0000'>待审核</font>";
			}else if ($value['flag']=="1"){
				$list[$key]['flagshow'] = "审核通过";
			}else if ($value['flag']=="2"){
				$list[$key]['flagshow'] = "审核取消";
			}
			
			$show = json_decode($value['logs'], true);
			$list[$key]['title'] = $show['title'];
			$list[$key]['contents'] = $show['contents'];
			$list[$key]['sorts'] = $show['sorts'];
			
			$list[$key]['addtime'] = date("Y-m-d H:i:s", $value['addtime']);
		}
		
		//增加操作记录
		$logs = C('NOTICE_MSG_RECORD');
		$remark = "";
		adminlog($logs, $remark);
		
		$this->assign('left_css',"36");
		$this->assign('list',$list);
		$this->assign('pageshow',$pageshow);
		
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl.":index";
		$this->display($lib_display);
	}
	
	
	
	//商品添加
	public function notice_add(){
		if(!empty($_POST)){
			
			unset($_POST['__hash__']);
			unset($_POST['Submit']);
			
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = '公告新增';
			$data['userip'] = get_client_ip();
			$data['cate'] = 104;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			//$result = $add_table->add($_POST);
			//dump($add_table->_sql()); exit;
			if($result){
				//增加操作记录
				$logs = C('NOTICE_MSG_ADD_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
			
				$this->success('提交成功，等待审核', U($this->By_tpl.'/notice_add'));
				exit;
			}else{
				//增加操作记录
				$logs = C('NOTICE_MSG_ADD_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('添加失败');
				exit;
			}
		}else{
			//增加操作记录
			$logs = C('NOTICE_MSG_ADD');
			$remark = "";
			adminlog($logs, $remark);
		
			$this->assign('left_css',"36");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":notice_add";
			$this->display($lib_display);
		}
		
	}

	//商品更新
	public function notice_edit(){
		$upate_table = M('user_record');
		if(!empty($_POST)){
			
			$id = $_POST['id'];
			//unset($_POST['GoodsID']);
			unset($_POST['Submit']);
			unset($_POST['__hash__']);
			
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = '公告修改';
			$data['userip'] = get_client_ip();
			$data['flag'] = 0;
			$data['addtime'] = time();
			$result = $upate_table->where('id='.$id)->save($data);
			
			//$id['GoodsID']=intval($GoodsID);
			//$result=$upate_table->where($id)->save($_POST);
			if($result){
				//增加操作记录
				$logs = C('NOTICE_MSG_EDIT_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->success('提交成功，等待审核');
			}else{
				//增加操作记录
				$logs = C('NOTICE_MSG_EDIT_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('修改失败');
			}
		}else{
			//增加操作记录
			$logs = C('NOTICE_MSG_EDIT');
			$remark = "";
			adminlog($logs, $remark);
			
			$id['id']=$_GET['id'];
			$info = $upate_table->where($id)->find();
			$this->assign('info',$info);
			$notice = json_decode($info['logs'], true);
			$this->assign('notice',$notice);
			
			$this->assign('left_css',"36");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":notice_edit";
			$this->display($lib_display);
		}
	}
	
	//系统维护通知开始
	//系统维护通知列表
	public function weihu(){
		
		$sql0 = "";
		if ($_SESSION['js_flag']!="1"){$sql0 .= " and username='".$_SESSION['username']."'";}
		
		$rowlist = M("user_record");
		import('ORG.Util.Page');
		$count = $rowlist->where("cate=106 $sql0")->count('id');
		$Page       = new Page($count,PAGE_SHOW);//实例化分页类传入总记录数和每页显示的记录数		
		$pageshow       = $Page->show();// 分页显示输出
		
		$list = $rowlist->where("cate=106 $sql0")->order('flag,id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($rowlist->_sql());
		foreach($list as $key=>$value){
			if ($value['flag']=="0"){
				$list[$key]['flagshow'] = "<font color='#FF0000'>待审核</font>";
			}else if ($value['flag']=="1"){
				$list[$key]['flagshow'] = "审核通过";
			}else if ($value['flag']=="2"){
				$list[$key]['flagshow'] = "审核取消";
			}
			
			if ($value['notice']=="0"){
				$list[$key]['flagshow'] .= "&nbsp;<font color='#FF0000'>未通知服务器</font>";
			}else if ($value['flag']=="1"){
				$list[$key]['flagshow'] .= "&nbsp;已通知服务器";
			}
			
			if ($value['cate']=="106"){
				$list[$key]['showcate'] = "系统维护";
			}
			
			$show = json_decode($value['logs'], true);
			$list[$key]['startime'] = $show['startime'];
			$list[$key]['endtime'] = $show['endtime'];
			$list[$key]['message'] = $show['message'];
			
			$list[$key]['addtime'] = date("Y-m-d H:i:s", $value['addtime']);
		}
		
		//增加操作记录
		$logs = C('WEIHU_MSG_RECORD');
		$remark = "";
		adminlog($logs, $remark);
		
		$this->assign('left_css',"36");
		$this->assign('list',$list);
		$this->assign('pageshow',$pageshow);
		
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl.":weihu";
		$this->display($lib_display);
	}
	
	
	//系统维护通知添加
	public function weihu_add(){
		if(!empty($_POST)){
			
			unset($_POST['__hash__']);
			unset($_POST['Submit']);
			
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = '系统维护通知新增';
			$data['userip'] = get_client_ip();
			$data['cate'] = 106;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			//$result = $add_table->add($_POST);
			//dump($add_table->_sql()); exit;
			if($result){
				//增加操作记录
				$logs = C('WEIHU_MSG_ADD_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
			
				$this->success('提交成功，等待审核', U($this->By_tpl.'/weihu_add'));
				exit;
			}else{
				//增加操作记录
				$logs = C('WEIHU_MSG_ADD_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('添加失败');
				exit;
			}
		}else{
			//增加操作记录
			$logs = C('WEIHU_MSG_ADD');
			$remark = "";
			adminlog($logs, $remark);
		
			$this->assign('left_css',"36");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":weihu_add";
			$this->display($lib_display);
		}
		
	}

	//系统维护通知更新
	public function weihu_edit(){
		$upate_table = M('user_record');
		if(!empty($_POST)){
			
			$id = $_POST['id'];
			//unset($_POST['GoodsID']);
			unset($_POST['Submit']);
			unset($_POST['__hash__']);
			
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = '系统维护通知修改';
			$data['userip'] = get_client_ip();
			$data['flag'] = 0;
			$data['notice'] = 0;
			
			$data['addtime'] = time();
			$result = $upate_table->where('id='.$id)->save($data);
			
			//$id['GoodsID']=intval($GoodsID);
			//$result=$upate_table->where($id)->save($_POST);
			if($result){
				//增加操作记录
				$logs = C('WEIHU_MSG_EDIT_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->success('提交成功，等待审核', U($this->By_tpl.'/weihu'));
			}else{
				//增加操作记录
				$logs = C('WEIHU_MSG_EDIT_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('修改失败');
			}
		}else{
			//增加操作记录
			$logs = C('WEIHU_MSG_EDIT');
			$remark = "";
			adminlog($logs, $remark);
			
			$id['id']=$_GET['id'];
			$info = $upate_table->where($id)->find();
			$this->assign('info',$info);
			$notice = json_decode($info['logs'], true);
			$this->assign('notice',$notice);
			
			$this->assign('left_css',"36");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":weihu_edit";
			$this->display($lib_display);
		}
	}
	
	
	
	//待审核商品列表
	public function waitshang_show(){
		$id = I("id");
		if (empty($id)){
			$this->error('输入有误');
			exit;
		}

		$row = M("user_record");
		$info = $row->where("id=".$id)->find();
		
		$act = I("act");
		if (!empty($act)){
			
			if ($act == "on"){
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
		
		echo "该页面不存在";
		exit;
	}
	
	//通知服务器开始
	public function notice(){
		//通知服务器
		$table = "user_record";
		$row = M($table);
		//通知服务器
		$flag = $_GET['flag'];
		$id = $_GET['id'];
		//$res = $row->where("id=".$id)->find();
		
		if ($flag == "106"){
			$url = DB_HOST."/Pay/notice.php?id=".$id;
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
		} 
		
		echo $status;
	}
	//通知服务器结束
}