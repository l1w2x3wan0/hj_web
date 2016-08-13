<?php
// 本类由系统自动生成，仅供测试用途
class IndexAction extends Action {
    public function index(){
		//$this->redirect("/User/login"); 
		//$link = U('Login/login');
		$this->display("Index/index");
    }
	 public function test(){
		//$this->redirect("/User/login");
		echo "这是第2个方法";
    }















}