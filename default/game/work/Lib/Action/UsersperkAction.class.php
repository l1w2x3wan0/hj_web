<?php
// 客服聊天文件

class UsersperkAction extends Action {

	public function index(){
		
		$table = M("user_sperk");
		
		$post_str = json_decode($_POST['params'], true);
		
		$message = $post_str["message"]; 
		$uid = $post_str["uid"];
		$ver = $post_str["ver"];
		
		if (empty($message)){
			echo -1; 
			exit;
		}
		
		//$put_data = $PostData;  
		//$put_file = time().".txt";
		//file_put_contents($put_file, $put_data);
		
		$tsnow = time();
		$data = array();
		$data['uid'] = $uid;
		$data['ver'] = $ver;
		$data['message'] = $message; 
		$data['addtime'] = $tsnow;
		$result = $table->add($data);
		//dump($table->_sql());
		if ($result){
			/*
			$show = array();
			$list = $table->where("uid=".$_POST['uid']." and ver=".$_POST['ver'])->order('addtime,id')->limit(0,20)->select();
			foreach($list as $key => $val){

				$showuid = ($val['pant_id']==0) ? (int)$val['uid'] : 1;
				$show[$key] = array($showuid,(int)$val['addtime'],$val['message']);
			}
			$pubtext = array('msg' => $show,
							 'ts' => $tsnow);
			$showlist = json_encode($pubtext, JSON_UNESCAPED_UNICODE);
			echo $showlist; */
			echo 1;
		}else{
			echo -1;
		}
		exit;
		
	}
	
	public function myrecord(){
		
		$table = M("user_sperk");
		$uid = I('uid');
		$ver = I('ver');
		$ts = I('ts');
		$ts = empty($ts) ? 0 : $ts;
		
		if (!empty($uid)){
			if (!empty($ts)) $sql = " and pant_id!=0 and addtime>$ts"; else $sql = "";
			$tsnow = 0;
			$list = $table->where("uid=".$uid." and ver='".$ver."'".$sql)->order('addtime,id')->limit(0,20)->select();
			//dump($table->_sql());
			$show = array();
			foreach($list as $key => $val){

				if ($tsnow < $val['addtime']) $tsnow = $val['addtime'];
				$showuid = ($val['pant_id']==0) ? (int)$val['uid'] : 1;
				$show[$key] = array($showuid,(int)$val['addtime'],$val['message']);
			}
			
			if ($tsnow > $ts){
				$pubtext = array('msg' => $show,
								 'ts' => $tsnow);
				$showlist = json_encode($pubtext, JSON_UNESCAPED_UNICODE);
				echo $showlist; 
			}else{
				echo -1;
			}

		}else{
			echo -1;
		}
		exit;
		
	}
	
	
	
	//空方法
	 public function _empty() {  
        $this->display("Index/index");
    }
}