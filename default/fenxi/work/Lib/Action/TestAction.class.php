<?php
class TestAction extends Action {
  

	public function index(){
		$row1 = M('user_info', '', DB_CONFIG2);
		$res = $row1->where("!((user_id>=10000000 and user_id<10002000) or (user_id>=10325805 and user_id<=10327804))")->select();
		$arr = array();
		$i = 0;
		foreach ($res as $key => $val){
			
			$temp1 = substr($val['user_id'],0,6);
			$temp2 = substr($val['user_id'],2,6);
			$temp3 = $temp1 + $temp2;
			if (!in_array($temp3, $arr)) $arr[] = $temp3;
			$i++;
		}
		$j = count($arr);
		echo $i."**".$j."<br>";
		print_r($arr);
	}

	public function add(){
		echo '这是add 方法';
	}


	public function sperk(){
		set_time_limit(0);
		$table = "sperk_log.log".date("Ym");
		$row = M($table);
		
		$page = I("page");
		if (empty($page)) $page = 1;
		$num0 = ($page - 1) * 1000;
		$num1 = 1000;
		
		$res = $row->where('id<511584 and type=3')->order('id')->limit($num0.','.$num1)->select();
		//dump($row->_sql()); exit;
		foreach ($res as $key => $val){
			//echo $val['id'];
			$data = array();
			$show1 = explode(":", $val['errmessage']);				
			$data['roomid'] = $show1[0];				
			$data['tableno'] = $show1[1];				
			$data['message'] = $show1[2];
			//print_r($data); 
			$result = $row->where('id='.$val['id'])->save($data);
			//dump($row->_sql()); exit;
		}
		
		$nextpage = $page + 1;
		$url = "http://ysz.kk520.com:9102/index.php?m=Test&a=sperk&page=".$nextpage;
		header("location:$url");
		echo "OK";
		sleep(10);
	}







}