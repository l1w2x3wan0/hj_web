<?php
// 员工管理的文件
/*
		员工状态：0 禁用状态
				  1 启用状态
				  2 删除状态
				  3 归档状态
*/
class LoginAction extends Action {
  
	//员工登陆
	public function login(){
		if(!empty($_POST)){
			$where['username']=I('username');
			$where['password']=md5(I('userpwd'));
			//echo I('userpwd'); exit;
			$employee=M('users');
			$result=$employee->where($where)->field('id,username,permissions,user_power,user_js,is_lock')->find();
			//dump($employee->_sql()); exit;
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
				
				$this->success("登陆成功",U('User/change'));
			}else{
				$this->error("登陆失败");
			}
		}else{
			$this->assign('left_css',"1");
			$this->display('Login:login');
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
	
	
}