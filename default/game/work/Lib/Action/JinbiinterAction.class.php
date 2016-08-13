<?php
// 接口管理的文件

class JinbiinterAction extends Action {
	
	public function record(){
		$table1 = M(MYTABLE_PRIFIX."log_mall_diamond_log");
		$table2 = M(MYTABLE_PRIFIX."user_info");
		
		
		$page = !empty($_GET['page']) ? $_GET['page'] : 1;
		$num = !empty($_GET['num']) ? $_GET['num'] : 10;
		$type = !empty($_GET['type']) ? $_GET['type'] : "";
		$sql0 = "";
		$ord0 = "";
		if (!empty($type)) {
			if ($type == "1"){
				$ord0 = "bl DESC,";
			}else if ($type == "2"){
				$sql0 = " and (diamond>=1 and diamond<10)";
			}else if ($type == "3"){
				$sql0 = " and (diamond>=10 and diamond<50)";
			}else if ($type == "4"){
				$sql0 = " and (diamond>=50)";
			}
		}
		$first = ($page - 1) * $num;
		$tsnow = 0;
		$list = $table1->field('ordercode,user_id,nickname,diamond,gold,bl,addtime')->where("status=1 $sql0")->order($ord0.'addtime DESC,id DESC')->limit($first,$num)->select();
		//dump($table1->_sql());
		$showlist = array();
		foreach($list as $key => $val){
			$user = $table2->field('nick_name,nickname')->where("user_id=".$val['user_id'])->find();
			$nickname = !empty($user['nickname']) ? $user['nickname'] : $user['nick_name'];
			
			$showlist[$key]['ordercode'] = $val['ordercode'];
			$showlist[$key]['user_id'] = (int)$val['user_id'];
			$showlist[$key]['nickname'] = $nickname;
			$showlist[$key]['diamond'] = (int)$val['diamond'];
			$showlist[$key]['gold'] = (int)$val['gold'];
			$showlist[$key]['msg'] = "1钻石=".$val['bl']."金币";
			if ($tsnow < $val['addtime']) $tsnow = $val['addtime'];
		}

		$ts = empty($_GET['ts']) ? 0 : $_GET['ts'];
		if ($tsnow > $ts){
			$pubtext = array('msg' => $showlist,
							 'ts' => $tsnow);
			$showlist = json_encode($pubtext, JSON_UNESCAPED_UNICODE);
			echo $showlist; 
		}else{
			echo -1;
		}
		exit;
		
	}	
	
	public function myrecord(){
		$table1 = M(MYTABLE_PRIFIX."log_mall_diamond_log");
		$table2 = M(MYTABLE_PRIFIX."user_info");
		$user_id = $_GET['user_id'];
		if (empty($user_id)){
			echo -1;
			exit;
		}
		
		$page = !empty($_GET['page']) ? $_GET['page'] : 1;
		$num = 20;
		$first = ($page - 1) * $num;
		$tsnow = 0;
		$list = $table1->where("user_id=$user_id")->order('status,addtime DESC,id DESC')->limit($first,$num)->select();
		$showlist = array();
		foreach($list as $key => $val){
			$user = $table2->field('nick_name,nickname')->where("user_id=".$val['user_id'])->find();
			$nickname = !empty($user['nickname']) ? $user['nickname'] : $user['nick_name'];
			
			$showlist[$key]['ordercode'] = $val['ordercode'];
			$showlist[$key]['user_id'] = (int)$val['user_id'];
			$showlist[$key]['nickname'] = $nickname;
			$showlist[$key]['buyer_id'] = (int)$val['buyer_id'];
			$showlist[$key]['buyer_nick'] = $val['buyer_nick'];
			$showlist[$key]['diamond'] = (int)$val['diamond'];
			$showlist[$key]['gold'] = (int)$val['gold'];
			$showlist[$key]['status'] = (int)$val['status'];
			$showlist[$key]['msg'] = "1钻石=".$val['bl']."金币";
			if ($tsnow < $val['addtime']) $tsnow = $val['addtime'];
		}

		$ts = empty($_GET['ts']) ? 0 : $_GET['ts'];
		if ($tsnow > $ts){
			$pubtext = array('msg' => $showlist,
							 'ts' => $tsnow);
			$showlist = json_encode($pubtext, JSON_UNESCAPED_UNICODE);
			echo $showlist; 
		}else{
			echo -1;
		}
		exit;
		
	}
	
	
}