<?php
// 接口管理的文件

class UserconfigAction extends InterAction {
	
	protected $online_status = 0;  //接口是否需要判断用户在线
	
	//用户上线广播开关接口
	public function index(){
		
		header("Content-type:text/html;charset=utf-8");
		$check = $this->check_sign();
		if ($check['status'] == 1){
			//echo "***"; exit;
			$paihang = $check['info']['paihang'];
			$user_id = $check['info']['user_id'];

			$table1 = M(MYTABLE_PRIFIX."user_info_config");
			//判断是否有记录
			$count = $table1->where("user_id=".$user_id)->count();
			$arr = array();
			$arr['paihang'] = $paihang;
			if ($count == 0){
				//新增记录
				$arr['user_id'] = $user_id;
				$result = $table1->add($arr);
			}else{
				//修改记录
				$result = $table1->where("user_id=".$user_id)->save($arr);
			}
			
			$result0 = array();			
			$result0['status'] = 1;
			$result0['paihang'] = $paihang;
			return $this->answerResquest('1','',$result0);
		}
	}	
	
	//用户上线广播开关查询接口
	public function show(){
		
		$check = $this->check_sign();
		if ($check['status'] == 1){

			//判断是否有记录
			$user_id = $check['info']['user_id'];
			$table1 = M(MYTABLE_PRIFIX."user_info_config");
			$count = $table1->where("user_id=".$user_id)->count();
			if ($count == 0){
				$paihang = 1;
			}else{
				$info = $table1->where("user_id=".$user_id)->find();
				$paihang = empty($info['paihang']) ? 0 :  $info['paihang'];
			}
				
			$result0 = array();			
			$result0['status'] = 1;
			$result0['paihang'] = $paihang;
			return $this->answerResquest('1','',$result0);
		}
	}
	

}