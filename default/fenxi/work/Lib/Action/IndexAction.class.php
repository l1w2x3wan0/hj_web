<?php
// 本类由系统自动生成，仅供测试用途
class IndexAction extends Action {
    public function index(){
		//$this->redirect("/User/login"); 
		//$link = U('Login/login');
        echo __CLASS__ . __METHOD__ ."\n";
        exit('fff');
		$this->display("Index/index");
    }
	 public function test(){
		//$this->redirect("/User/login");
         echo __CLASS__ . __METHOD__ ."\n";
		echo "这是第2个方法sss";
         exit('asdf');
    }















}