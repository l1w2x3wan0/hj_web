<?php
// 员工管理的文件
/*
		员工状态：0 禁用状态
				  1 启用状态
				  2 删除状态
				  3 归档状态
*/
class UserAction extends BaseAction {
  
	//添加员工
	public function user_add(){
		if(!empty($_POST)){
		
			if(empty($_POST['user_js'])){
				$this->error('所属角色不能为空');
				exit;
			}
			
			if(empty($_POST['user']['username']) || empty($_POST['user']['password'])){
				$this->error('数据不完整，请补充完整');
				exit;
			}
			
			if($_POST['user']['password_confirmation']!=$_POST['user']['password']){
				$this->error('两次输入的密码不一致');
				exit;
			}
			
			$this->checkd_username(htmlspecialchars(trim($_POST['user']['username'])));
			
			$data = array();
			$data['username']   = htmlspecialchars(trim($_POST['user']['username']));
			$data['password']   = md5(trim($_POST['user']['password']));
			$data['user_js'] =  $_POST['user_js'];
			$data['permissions'] =  "ALL";
			$data['created_at']   = date("Y-m-d H:i:s");
			$data['status']     = '1';
                        
			$employee=M('users');
			$result=$employee->add($data);
			//dump($employee->_sql()); exit;
			if($result){
				//增加操作记录
				$logs = C('MANAGE_MSG_ADD_SUCCESS');
				$remark = "(新增用户:".$data['username'].",".json_encode($data).")";
				adminlog($logs,$remark);
				
				$this->success('添加成功',U('User/user'));
			}else{
				//增加操作记录
				$logs = C('MANAGE_MSG_ADD_FALSE');
				adminlog($logs);
				
				$this->error('添加失败');
				exit;
			}
		}else{
			$table2 = M('user_js');
			$list = $table2->order('id')->select();
			$this->assign('user_js',$list);
			
			//增加操作记录
			$logs = C('MANAGE_MSG_ADD');
			adminlog($logs);

			$this->assign('left_css',"1");
			$this->display('User:user_add');
		}
		
	}

	//用户列表
	public function user(){
		//重建缓存
		//$this->cache_add();
		$user_jstable = M('user_js');
		$employee=M('users');
		import('ORG.Util.Page');
		$count=$employee->count('id');
		$Page       = new Page($count,15);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		
		
		$list = $employee->order('id')->limit($Page->firstRow.','.$Page->listRows)->select();
		
		foreach($list as $key=>$value){
			if($value['user_type']==1){
				$list[$key]['user_type']='管理员';
			}elseif($value['user_type']==2){
				$list[$key]['user_type']='商务';
			}elseif($value['user_type']==3){
				$list[$key]['user_type']='渠道商';
			}
			$user_js = $user_jstable->where('id='.$value['user_js'])->find();
			$list[$key]['jsname'] = $user_js['js_name'];
			
			$list[$key]['status']=($value['status']=="1") ? "正常" : "下线";
			/*if($value['if_manager']==2){
				$partid['id']=$value['position'];
				$level=$part->where($partid)->field('level')->find();
				$list[$key]['if_manager']=$level['level'];
				unset($level);
			}*/
			if ($value['username']=="robert" && $value['username']==$_SESSION['username']){
				$list[$key]['del'] = "0";
			}else{
				$list[$key]['del'] = "1";
			}
		}
		
		$this->assign('left_css',"1");
		$this->assign('list',$list);
		$this->assign('pageshow',$show);
		
		$this->display('User:user_list');
	}
	
	//员工删除
	public function user_delete(){
		if(empty($_GET)){ 
			//增加操作记录
			$logs = C('MANAGE_MSG_DEL_FALSE');
			adminlog($logs);
			
			$this->error('非法操作');
			exit;
		}else{
			$userid=$_GET['id']?$_GET['id']:$_POST['id'];
			if($userid==$_SESSION['userid']){
				$this->error('不能删除自己的账户否则会导致用户不能登录');
			}

			$employee=M('users');
			$id['id']=$userid;
			$result=$employee->where($id)->delete();
			if($result){
				//增加操作记录
				$logs = C('MANAGE_MSG_DEL_SUCCESS');
				$remark = "(删除用户:".$userid.")";
				adminlog($logs,$remark);
				
				$this->success('删除成功');
			}else{
				//增加操作记录
				$logs = C('MANAGE_MSG_DEL_FALSE');
				adminlog($logs);
				
				$this->error('删除失败');
				exit;
			}
		}
	}

	//员工更新
	public function user_edit(){
		$employee=M('users');
		
		$userid=$_GET['id']?$_GET['id']:$_POST['id'];
		if(!empty($userid)){
			$this->check_user($userid);
		}
		if(!empty($_POST)){
			
			if(empty($_POST['user_js'])){
				$this->error('所属角色不能为空');
				exit;
			}
			
			if($_POST['user']['password_confirmation']!=$_POST['user']['password']){
				$this->error('两次输入的密码不一致');
				exit;
			}
			
			$data['user_js'] = $_POST['user_js'];
			if (!empty($_POST['user']['password'])){
				$data['password']=md5(trim($_POST['user']['password']));
			}
			$data['updated_at']=date("Y-m-d H:i:s");
			$id['id']=intval($_POST['id']);
			$result=$employee->where($id)->save($data);
			if($result){
				//增加操作记录
				$logs = C('MANAGE_MSG_EDIT_SUCCESS');
				$remark = "(修改用户:".$userid.",".json_encode($data).")";
				adminlog($logs, $remark);
				
				$this->success('更新成功',U('User/user'));
			}else{
				//增加操作记录
				$logs = C('MANAGE_MSG_EDIT_FALSE');
				adminlog($logs);
				
				$this->error('更新失败');
			}
		}else{
			$table2 = M('user_js');
			$list = $table2->order('id')->select();
			$this->assign('user_js',$list);
			
			$id['id']=$_GET['id'];
			$employeeinfo=$employee->where($id)->find();
			$this->assign('user',$employeeinfo);
			
			//增加操作记录
			$logs = C('MANAGE_MSG_EDIT');
			adminlog($logs);
			
			$this->assign('left_css',"1");
			$this->display('User:user_edit');
		}
	}
	
	//修改个人密码
	public function change(){
		$usertable = M('users');
		$where['id']=$_SESSION['userid'];
		$user = $usertable->where($where)->find();
		
		$user_jstable = M('user_js');
		$user_js = $user_jstable->where('id='.$user['user_js'])->find();
		$user['jsname'] = $user_js['js_name'];
		$user['user_power'] = ($user['user_power']=="1") ? "超级管理员" : "普通管理员";
		$user['status'] = ($user['status']=="1") ? "正常" : "下线";
		$this->assign('left_css',"1");
		$this->assign('user',$user);
		
		$this->display('User:change');
	}
	
	//修改个人密码
	public function change_pwd(){
		$employee=M('users');
		
		$userid=$_GET['id']?$_GET['id']:$_POST['id'];
		if(!empty($userid)){
			$this->check_user($userid);
		}
		if(!empty($_POST)){
			if($_POST['user']['password_confirmation']!=$_POST['user']['password']){
				$this->error('两次输入的密码不一致');
				exit;
			}
			
			if (!empty($_POST['user']['password'])){
				$data['password']=md5(trim($_POST['user']['password']));
			}
			$data['updated_at']=date("Y-m-d H:i:s");
			$id['id']=intval($_POST['id']);
			$result=$employee->where($id)->save($data);
			if($result){
				//增加操作记录
				$logs = C('USER_MSG_CHANGE_SUCCESS');
				adminlog($logs);
				
				$this->success('更新成功',U('User/change'));
			}else{
				//增加操作记录
				$logs = C('USER_MSG_CHANGE_FALSE');
				adminlog($logs);
				
				$this->error('更新失败');
			}
		}else{
			
			$id['id']=$_GET['id'];
			$employeeinfo=$employee->where($id)->find();
			$this->assign('user',$employeeinfo);
			
			//增加操作记录
			$logs = C('USER_MSG_CHANGE');
			adminlog($logs);
			
			$this->assign('left_css',"1");
			$this->display('User:change_pwd');
		}
	}


	//员工登陆
	public function login(){
		if(!empty($_POST)){
			$where['username']=I('username');
			$where['password']=md5(I('userpwd'));

			$employee=M('users');
			$result=$employee->where($where)->field('id,username,user_power,user_js,is_lock')->find();
			//dump($employee->sql()); exit;
			if($result){
				$data['updated_at']=date("Y-m-d H:i:s");
				$employee->where($where)->save($data);
				
				$user_js_table = M('user_js');
				$user_js = $user_js_table->where('id='.$result['user_js'])->field('js_power,js_flag')->find();
				
				$_SESSION['userid']=$result['id'];
				$_SESSION['username']=$result['username'];
				$_SESSION['user_js']=$result['user_js'];
				$_SESSION['js_power']=$user_js['js_power'];
				$_SESSION['js_flag']=$user_js['js_flag'];
				$_SESSION['wname']=C('wname');
				
				//增加操作记录
				$logs = C('USER_MSG_LOGIN');
				adminlog($logs);
				
				$this->success("登陆成功",U('Gai/wel'));
			}else{
				$this->error("登陆失败");
			}
		}else{
			$this->assign('left_css',"1");
			$this->display('User:login');
		}
		
	}

	//欢迎页
	public function welcome(){
		$this->display('User:welcome');
	}

    //判断用户名是否存在
	public function checkd_username($username){
		$data['username']=$username;
		$employee=M('users');
		$result=false;
		$result=$employee->field('id')->where($data)->find();
		if($result){
			$this->error("用户名已存在");
			exit;
		}
	}
	
	//登出
	public function logout(){
		session_destroy();
		$link = U('Login/login');
		
		//增加操作记录
		$logs = C('USER_MSG_LOGOUT');
		adminlog($logs);
		
		$this->success('注销成功', $link);
    }
	
	//清空缓存
	public function cache(){
		$this->clearCache(0);
		
		$this->success('清空缓存成功',U('User/user_list'));
	}
		
	
	//删除对应目录
	public function clearCache($type=0,$path=NULL) {
        if(is_null($path)) {
            switch($type) {
				case 0:// 模版缓存目录
					$path = APP_PATH."Runtime";
					break;
				case 1:// 数据缓存目录
					$path   =   APP_PATH."Runtime/Data/";
					break;
				case 2:// 日志目录
					$path   =   APP_PATH."Runtime/Logs/";
					break;
				case 3:// 缓存目录
					$path   =   APP_PATH."Runtime/Cache/";
					break;
				case 4:// 临时目录
					$path   =   APP_PATH."Runtime/Temp/";
					break;
            }
        }
        import("ORG.Util.Dir");
        Dir::delDir($path);
		//echo $path; exit;
    }
	
	public function check_user($id){

		$employee = M('users');
		$data['id'] = $id;
		$result = $employee->where($data)->field('id')->select();
		if(!$result){
			$this->error('这个ID不存在');
			exit;
		}
	}


	//添加图片
	public function user_pic(){
		if(!empty($_POST)){
			
			import('ORG.Net.UploadFile');
			$upload = new UploadFile();// 实例化上传类
			$upload->maxSize  = 3145728 ;// 设置附件上传大小
			$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			$upload->savePath =  APP_PATH.'laoxie_photo/';// 设置附件上传目录
			 if(!$upload->upload()) {// 上传错误提示错误信息
			$this->error($upload->getErrorMsg());
			 }else{// 上传成功 获取上传文件信息
			$info =  $upload->getUploadFileInfo();
			 }
			 // 保存表单数据 包括附件数据
			$User = M("xie_pic"); // 实例化User对象
			foreach($info as $val){
				$User->create(); // 创建数据对象
				$User->pic_src = DB_HOST."/".APP_NAME."/laoxie_photo/".$val['savename']; // 保存上传的照片根据需要自行组装
				$User->addtime = time();
				$User->add(); // 写入用户数据到数据库
			}
			
			$this->success('数据保存成功！');
			
		}else{

			$this->assign('left_css',"1");
			$this->display('User:photo_add');
		}
		
	}

	//获取图片
	public function piclist(){
		$pic = M("xie_pic");
		$list = $pic->order('id')->select();
		echo json_encode($list);
	}

	
	//权限开始
	//权限列表
	public function power(){
		$table = M('user_power');
		import('ORG.Util.Page');
		$count = $table->count('id');
		$Page       = new Page($count,15);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		$list = $table->order('id')->limit($Page->firstRow.','.$Page->listRows)->select();
		foreach($list as $key=>$value){
			$list[$key]['addtime'] = date("Y-m-d H:i:s");
		}
		$this->assign('left_css',"1");
		$this->assign('list',$list);
		$this->assign('pageshow',$show);
		$this->display('User:power_list');
	}
	
	//权限添加
	public function power_add(){
		if(!empty($_POST)){
			if(empty($_POST['power_name']) || empty($_POST['power_do'])){
				$this->error('数据不完整，请补充完整');
				exit;
			}
			$_POST['addtime'] = time();
			$table = M('user_power');
			$result = $table->add($_POST);
			//dump($employee->_sql());
			if($result){
				//增加操作记录
				$logs = C('POWER_MSG_ADD_SUCCESS');
				$remark = "(新增操作:".$_POST['power_name'].",".json_encode($_POST).")";
				adminlog($logs,$remark);
				
				$this->success('添加成功',U('User/power'));
			}else{
				//增加操作记录
				$logs = C('POWER_MSG_ADD_FALSE');
				adminlog($logs);
				
				$this->error('添加失败');
				exit;
			}
		}else{
			//增加操作记录
			$logs = C('POWER_MSG_ADD');
			adminlog($logs);
			
			$this->assign('left_css',"1");
			$this->display('User:power_add');
		}
		
	}

	//权限更新
	public function power_edit(){
		$table = M('user_power');
		$id = I('id');
		$where['id'] = $id;
		if(empty($id)){
			$this->error('数据异常');
			exit;
		}
		if(!empty($_POST)){
			if(empty($_POST['power_name']) || empty($_POST['power_do'])){
				$this->error('数据不完整，请补充完整');
				exit;
			}
			$result = $table->where($where)->save($_POST);
			if($result){
				//增加操作记录
				$logs = C('POWER_MSG_EDIT_SUCCESS');
				$remark = "(修改操作:".$id.",".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->success('更新成功',U('User/power'));
			}else{
				//增加操作记录
				$logs = C('POWER_MSG_EDIT_FALSE');
				adminlog($logs);
				
				$this->error('更新失败');
			}
		}else{
			//增加操作记录
			$logs = C('POWER_MSG_EDIT');
			adminlog($logs);
			
			$info = $table->where($where)->find();
			$this->assign('info',$info);
			$this->assign('left_css',"1");
			$this->display('User:power_edit');
		}
	}
	
	//权限删除
	public function power_delete(){
		$table = M('user_power');
		$id = I('id');
		$where['id'] = $id;
		if(empty($id)){
			$this->error('数据异常');
			exit;
		}else{
			$result = $table->where($where)->delete();
			if($result){
				//增加操作记录
				$logs = C('POWER_MSG_DEL_SUCCESS');
				$remark = "(删除操作:".$id.")";
				adminlog($logs,$remark);
				
				$this->success('删除成功',U('User/power'));
			}else{
				//增加操作记录
				$logs = C('POWER_MSG_DEL_FALSE');
				adminlog($logs);
				
				$this->error('删除失败');
				exit;
			}
		}
	}
	//权限结束
	
	
	//栏目开始
	//栏目列表
	public function lanmu(){
		$table1 = M('user_lanmu');
		$list = $table1->where('lanmu_num=0')->order('lanmu_sort,id')->select();
		foreach($list as $key=>$val){
			$list[$key]['addtime'] = date("Y-m-d H:i:s",$val['addtime']);
			$list[$key]['sub'] = array();
			$list2 = $table1->where('lanmu_num='.$val['id'])->order('lanmu_sort,id')->select();
			$list[$key]['sub'] = $list2;
			foreach($list2 as $key2=>$val2){
				$list[$key]['sub'][$key2]['addtime'] = date("Y-m-d H:i:s",$val2['addtime']);
			}
		}

		$this->assign('left_css',"1");
		$this->assign('list',$list);
		$this->assign('pageshow',$show);
		$this->display('User:lanmu_list');
	}
	
	//栏目添加
	public function lanmu_add(){
		$table = M('user_lanmu');
		if(!empty($_POST)){
			if(empty($_POST['lanmu_name']) ){
				$this->error('数据不完整，请补充完整');
				exit;
			}
			$_POST['addtime'] = time();
			
			$result = $table->add($_POST);
			//dump($table->_sql()); exit;
			if($result){
				//增加操作记录
				$logs = C('LANMU_MSG_ADD_SUCCESS');
				$remark = "(新增栏目:".$_POST['lanmu_name'].",".json_encode($_POST).")";
				adminlog($logs,$remark);
				
				$this->success('添加成功',U('User/lanmu'));
			}else{
				//增加操作记录
				$logs = C('LANMU_MSG_ADD_FALSE');
				adminlog($logs);
				
				$this->error('添加失败');
				exit;
			}
		}else{
			//增加操作记录
			$logs = C('LANMU_MSG_ADD');
			adminlog($logs);
			
			$list = $table->where('lanmu_num=0')->order('lanmu_sort,id')->select();
			$this->assign('lanmu',$list);
			$this->assign('left_css',"1");
			$this->display('User:lanmu_add');
		}
		
	}

	//栏目更新
	public function lanmu_edit(){
		$table = M('user_lanmu');
		$id = I('id');
		$where['id'] = $id;
		if(empty($id)){
			$this->error('数据异常');
			exit;
		}
		if(!empty($_POST)){
			if(empty($_POST['lanmu_name']) ){
				$this->error('数据不完整，请补充完整');
				exit;
			}
			$result = $table->where($where)->save($_POST);
			if($result){
				//增加操作记录
				$logs = C('LANMU_MSG_EDIT_SUCCESS');
				$remark = "(修改栏目:".$id.",".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->success('更新成功',U('User/lanmu'));
			}else{
				//增加操作记录
				$logs = C('LANMU_MSG_EDIT_FALSE');
				adminlog($logs);
				
				$this->error('更新失败');
			}
		}else{
			//增加操作记录
			$logs = C('LANMU_MSG_EDIT');
			adminlog($logs);
			
			$list = $table->where('lanmu_num=0')->order('lanmu_sort,id')->select();
			$this->assign('lanmu',$list);
			$info = $table->where($where)->find();
			$this->assign('info',$info);
			$this->assign('left_css',"1");
			$this->display('User:lanmu_edit');
		}
	}
	
	//栏目删除
	public function lanmu_delete(){
		$table = M('user_lanmu');
		$id = I('id');
		$where['id'] = $id;
		if(empty($id)){
			$this->error('数据异常');
			exit;
		}else{
			$result = $table->where($where)->delete();
			if($result){
				//增加操作记录
				$logs = C('LANMU_MSG_DEL_SUCCESS');
				$remark = "(删除栏目:".$id.")";
				adminlog($logs,$remark);
				
				$this->success('删除成功',U('User/lanmu'));
			}else{
				//增加操作记录
				$logs = C('LANMU_MSG_DEL_FALSE');
				adminlog($logs);
				
				$this->error('删除失败');
				exit;
			}
		}
	}
	//栏目结束
	
	
	//角色开始
	//角色列表
	public function jiaose(){
		$table = M('user_js');
		import('ORG.Util.Page');
		$count = $table->count('id');
		$Page       = new Page($count,15);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		$list = $table->order('id')->limit($Page->firstRow.','.$Page->listRows)->select();
		foreach($list as $key=>$value){
			$list[$key]['js_flag'] = ($value['js_flag']=="1") ? "超级管理员" : "普通管理员";
			$list[$key]['addtime'] = date("Y-m-d H:i:s");
		}
		$this->assign('left_css',"1");
		$this->assign('list',$list);
		$this->assign('pageshow',$show);
		$this->display('User:jiaose_list');
	}
	
	//角色添加
	public function jiaose_add(){
		if(!empty($_POST)){
			if(empty($_POST['js_name'])){
				$this->error('数据不完整，请补充完整');
				exit;
			}
			$post_str = array();
			$post_str['js_name'] = $_POST['js_name'];
			$post_str['js_flag'] = $_POST['js_flag'];
			$post_str['addtime'] = time();
			$table = M('user_js');
			
			$js_power = array();
			$table1 = M('user_lanmu');
			$table2 = M('user_power');
			$list = $table1->field('id,lanmu_num')->order('id')->select();
			foreach($list as $key=>$val){
				$temp1 = $val['id'];
				$js_power[$temp1] = ($_POST[$temp1]=="1") ? "1" : "0";
				if ($val['lanmu_num']!=0){
					$temp2 = $val['id']."_power";
					$js_power[$temp2] = $_POST[$temp2];
				}
			}
			$post_str['js_power'] = json_encode($js_power);
			$result = $table->add($post_str);
			//dump($table->_sql()); exit;
			if($result){
				//增加操作记录
				$logs = C('JIAOSE_MSG_ADD_SUCCESS');
				$remark = "(新增角色:".$post_str['js_name'].",".json_encode($post_str).")";
				adminlog($logs,$remark);
				
				$this->success('添加成功',U('User/jiaose'));
			}else{
				//增加操作记录
				$logs = C('JIAOSE_MSG_ADD_FALSE');
				adminlog($logs);
				
				$this->error('添加失败');
				exit;
			}
		}else{
			$table1 = M('user_lanmu');
			$list = $table1->where('lanmu_num=0')->order('id')->select();
			foreach($list as $key=>$val){
				$list[$key]['sub'] = array();
				$list2 = $table1->where('lanmu_num='.$val['id'])->order('id')->select();
				$list[$key]['sub'] = $list2;
			}
			$this->assign('lanmu',$list);
			
			$table2 = M('user_power');
			$list = $table2->order('id')->select();
			$this->assign('caozuo',$list);
			
			//增加操作记录
			$logs = C('JIAOSE_MSG_ADD');
			adminlog($logs);
			
			$this->assign('left_css',"1");
			$this->display('User:jiaose_add');
		}
		
	}

	//角色更新
	public function jiaose_edit(){
		$table = M('user_js');
		$id = I('id');
		$where['id'] = $id;
		if(empty($id)){
			$this->error('数据异常');
			exit;
		}
		if(!empty($_POST)){
			if(empty($_POST['js_name'])){
				$this->error('数据不完整，请补充完整');
				exit;
			}
			$post_str = array();
			$post_str['js_name'] = $_POST['js_name'];
			$post_str['js_flag'] = $_POST['js_flag'];
			$js_power = array();
			$table1 = M('user_lanmu');
			$table2 = M('user_power');
			$list = $table1->field('id,lanmu_num')->order('id')->select();
			foreach($list as $key=>$val){
				$temp1 = $val['id'];
				$js_power[$temp1] = ($_POST[$temp1]=="1") ? "1" : "0";
				if ($val['lanmu_num']!=0){
					$temp2 = $val['id']."_power";
					$js_power[$temp2] = $_POST[$temp2];
				}
			}
			$post_str['js_power'] = json_encode($js_power);
			
			$result = $table->where($where)->save($post_str);
			if($result){
				//增加操作记录
				$logs = C('JIAOSE_MSG_EDIT_SUCCESS');
				$remark = "(修改用户:".$userid.",".json_encode($data).")";
				adminlog($logs, $remark);
				
				$this->success('更新成功',U('User/jiaose'));
			}else{
				//增加操作记录
				$logs = C('JIAOSE_MSG_EDIT_FALSE');
				adminlog($logs);
				
				$this->error('更新失败');
			}
		}else{
			$info = $table->where($where)->find();
			$js_power = json_decode($info['js_power'], true);
			
			$table2 = M('user_power');
			$lanmu = $table2->order('id')->select();
			$this->assign('lanmu',$lanmu);
			
			$table1 = M('user_lanmu');
			$list = $table1->where('lanmu_num=0')->order('id')->select();
			foreach($list as $key=>$val){
				$list[$key]['checked'] = $js_power[$val['id']];
				$list[$key]['sub'] = array();
				$list2 = $table1->where('lanmu_num='.$val['id'])->order('id')->select();
				$list[$key]['sub'] = $list2;
				foreach($list2 as $key2=>$val2){
					$list[$key]['sub'][$key2]['checked'] = $js_power[$val2['id']];
					$list[$key]['sub'][$key2]['caozuo'] = array();
					$list[$key]['sub'][$key2]['caozuo'] = $lanmu;
					$temp1 = $val2['id']."_power";
					foreach($lanmu as $key3=> $val3){
						$list[$key]['sub'][$key2]['caozuo'][$key3]['checked'] = in_array($val3['power_do'],$js_power[$temp1]);
					}
					
				}
			}
			//print_r($list);
			$this->assign('caozuo',$list);
			
			//增加操作记录
			$logs = C('JIAOSE_MSG_EDIT');
			adminlog($logs);
			
			$this->assign('info',$info);
			$this->assign('left_css',"1");
			$this->display('User:jiaose_edit');
		}
	}
	
	//角色删除
	public function jiaose_delete(){
		$table = M('user_power');
		$id = I('id');
		$where['id'] = $id;
		if(empty($id)){
			$this->error('数据异常');
			exit;
		}else{
			$result = $table->where($where)->delete();
			if($result){
				//增加操作记录
				$logs = C('JIAOSE_MSG_DEL_SUCCESS');
				$remark = "(删除角色:".$id.")";
				adminlog($logs,$remark);
				
				$this->success('删除成功',U('User/jiaose'));
			}else{
				//增加操作记录
				$logs = C('JIAOSE_MSG_DEL_FALSE');
				adminlog($logs);
				
				$this->error('删除失败');
				exit;
			}
		}
	}
	//角色结束
	
	//支付通知IP配置开始
	public function config(){
		$table = M('config');
		$id = 1;
		$where['id'] = $id;
		if(empty($id)){
			$this->error('数据异常');
			exit;
		}
		if(!empty($_POST)){
			
			$list = $table->select();
			foreach($list as $key => $val){
				$postname = $val['config_name'];
				$data = array();
				$data['config_value'] = $_POST[$postname];
				$where = array();
				$where['config_name'] = $postname;
				$table->where($where)->save($data);
			}
			$result = true;
			
			if($result){
				$this->success('更新成功',U('User/config'));
			}else{
				$this->error('更新失败');
			}
		}else{
			$list = $table->select();
			$this->assign('list',$list);
			$this->assign('left_css',"1");
			$this->display('User:config');
		}
	}
	//支付通知IP配置结束
	
	//操作日志开始
	//操作日志列表
	public function logs(){
		$table = M('user_logs');
		import('ORG.Util.Page');
		$count = $table->count('id');
		$Page = new Page($count,15);//实例化分页类传入总记录数和每页显示的记录数		
		$show = $Page->show();// 分页显示输出
		
		$list = $table->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		foreach($list as $key => $val){
			$list[$key]['addtime'] = date("Y-m-d H:i:s", $val['addtime']);
		}

		$this->assign('left_css',"1");
		$this->assign('list',$list);
		$this->assign('pageshow',$show);
		$this->display('User:logs');
	}
	//操作日志结束
	
	//空方法
	 public function _empty() {  
        $this->display("Index/index");
    }
}