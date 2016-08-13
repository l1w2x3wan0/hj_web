<?php
class TestAction extends BaseAction {
  

	public function index(){
		$res = M();
		//$sql = " CALL sp_user_online_del(5)";
		$sql = " CALL ".MYTABLE_PRIFIX."sp_user_online_del(5)"; 
		$row = $res->query($sql);
		print_r($row);
	}

	public function add(){
		echo '这是add 方法';
	}










}