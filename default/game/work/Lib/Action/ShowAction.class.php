<?php
// 接口管理的文件

class ShowAction extends Action {
	//protected $Table_prifix = "zjhmysql."; 
	
	public function notice(){
		$rowlist = M("user_record");
		$list = $rowlist->where("cate=104 and flag=1")->order('id DESC')->select();
		$showlist = array();
		$sort1 = array();
		$show = "";
		$tsnow = 0;
		foreach($list as $key=>$value){
			$show = json_decode($value['logs'], true);
			$showlist[$key]['title'] = $show['title'];
			$showlist[$key]['contents'] = $show['contents'];
			$showlist[$key]['sorts'] = $show['sorts'];
			
			$sort1[$key] = $show['sorts'];
			if ($tsnow < $value['pubtime']) $tsnow = $value['pubtime'];
		}
		array_multisort($sort1, SORT_ASC, $showlist);
		
		$post_str = json_decode($_POST['params'], true);
		if (!empty($post_str)){
			$ts = empty($post_str['ts']) ? 0 : $post_str['ts'];
		}else{
			$ts = empty($_GET['ts']) ? 0 : $_GET['ts'];
		}
		
		if ($tsnow > $ts){
			$pubtext = array('msg' => $showlist,
							 'ts' => $tsnow);
			$showlist = json_encode($pubtext, JSON_UNESCAPED_UNICODE);
			echo $showlist; 
		}else{
			echo -1;
		}
		
		
	}	
	
	public function renwu(){
		$rowlist = M("user_record");
		$list = $rowlist->where("cate=9 and flag=1 and notice=1")->order('id DESC')->select();
		$showlist = array();
		$sort1 = array();
		$show = "";
		$tsnow = 0;
		foreach($list as $key=>$value){
			if ($tsnow < $value['pubtime']) $tsnow = $value['pubtime'];
		}
		
		$post_str = json_decode($_POST['params'], true);
		if (!empty($post_str)){
			$ts = empty($post_str['ts']) ? 0 : $post_str['ts'];
		}else{
			$ts = empty($_GET['ts']) ? 0 : $_GET['ts'];
		}
		//echo $tsnow."**".$ts."<br>".$this->Table_prifix;
		if ($tsnow > $ts){
			$res = M(MYTABLE_PRIFIX."task_daily_task_config");
			$showlist = $res->order("type,taskid")->select();
			//dump($res->_sql());
			foreach($showlist as $key => $val){

				$showlist[$key]["taskid"] = (int)$val['taskid'];
				$showlist[$key]["type"] = (int)$val['type'];
				$showlist[$key]["attribute"] = (int)$val['attribute'];
				$showlist[$key]["value"] = (int)$val['value'];
				$showlist[$key]["reward"] = (int)$val['reward'];
			}
			$pubtext = array('msg' => $showlist,
							 'ts' => $tsnow);
			$showlist = json_encode($pubtext, JSON_UNESCAPED_UNICODE);
			echo $showlist; 
		}else{
			echo -1;
		}
		
		
	}
	
	public function laba(){
		$xtlb = json_decode(S("XTDLB"), true);
		
		$post_str = json_decode($_POST['params'], true);
		if (!empty($post_str)){
			$ts = empty($post_str['ts']) ? 0 : $post_str['ts'];
		}else{
			$ts = empty($_GET['ts']) ? 0 : $_GET['ts'];
		}
	
		if ($xtlb['ts'] > $ts){
			echo S("XTDLB");
		}else{
			echo -1;
		}
		
	}
	
	public function jiangquan(){
		$xtlb = json_decode(S("YHJQ"), true);
		
		$post_str = json_decode($_POST['params'], true);
		if (!empty($post_str)){
			$ts = empty($post_str['ts']) ? 0 : $post_str['ts'];
		}else{
			$ts = empty($_GET['ts']) ? 0 : $_GET['ts'];
		}
		
		if ($xtlb['ts'] > $ts){
			echo S("YHJQ");
		}else{
			echo -1;
		}
		
	}
	
	public function jiangquan_record(){
		
		
		$post_str = json_decode($_POST['params'], true);
		if (!empty($post_str)){
			$user_id = $post_str['user_id'];
			$page = empty($post_str['page']) ? 10 : $post_str['page'];
		}else{
			$user_id = $_GET['user_id'];
			$page = !empty($_GET['page']) ? $_GET['page'] : 10;
		}
		
		if ($page > 50) $page = 50; 
		
		if (!empty($user_id)){
			$table1 = M(MYTABLE_PRIFIX."log_mall_lottery_log");
			$total = $table1->where('user_id='.$user_id)->count('id');
			if ($total > 0){
				$res = $table1->field('meno,addtime')->where('status=1 and user_id='.$user_id)->order('addtime desc')->limit(0,$page)->select();
				foreach($res as $key => $val){
					$res[$key]['addtime'] = date("Y-m-d H:i", $val['addtime']);
				}
				echo json_encode($res, JSON_UNESCAPED_UNICODE);
			}else{
				echo -1;
			}
		}else{
			echo -1;
		}
		
	}
	
	public function heimingdan(){
		$xtlb = json_decode(S("HEIMINGDAN"), true);
		//$ts = empty($_GET['ts']) ? 0 : $_GET['ts'];
		$post_str = json_decode($_POST['params'], true);
		if (!empty($post_str)){
			$ts = empty($post_str['ts']) ? 0 : $post_str['ts'];
		}else{
			$ts = empty($_GET['ts']) ? 0 : $_GET['ts'];
		}
		
		if ($xtlb['ts'] > $ts){
			echo S("HEIMINGDAN");
		}else{
			echo -1;
		}
		
	}
	
	public function robertcj(){
		//获取基本配置
		$row = M("manage_config");
		$list = $row->select();
		//dump($row->_sql());
		foreach ($list as $key => $val){
			define($val['config_name'], $val['config_value']);
		}
		
		$xtlb = S("ROBERT_CHOUJIANG");
		//echo $xtlb; exit;
		$tsnow = time();
		$post_str = json_decode($_POST['params'], true);
		if (!empty($post_str)){
			$ts = empty($post_str['ts']) ? 0 : $post_str['ts'];
		}else{
			$ts = empty($_GET['ts']) ? 0 : $_GET['ts'];
		}
		
		if ($tsnow > $ts){
			$table1 = M(MYTABLE_PRIFIX."user_info");
			$robert = array();
			for ($i=0; $i<4; $i++){
				$robert_id = rand(ROBERT2_BEGIN,ROBERT2_END);
				$res = $table1->where("user_id=".$robert_id)->find();
				$robert[$i] = (!empty($res['nickname'])) ? $res['nickname'] : $res['nick_name'];
			}
			//print_r($robert);
			
			$xtlb['robert1'] = $robert[0];
			$xtlb['robert2'] = $robert[1];
			$xtlb['robert3'] = $robert[2];
			$xtlb['robert4'] = $robert[3];
			$pubtext = array('msg' => $xtlb,
							 'ts' => $tsnow);
			$showlist = json_encode($pubtext, JSON_UNESCAPED_UNICODE);
			echo $showlist; 
		}else{
			echo -1;
		}
		
	}
	
	//抽奖排行
	public function choujiang(){
		
		$tablename = MYTABLE_PRIFIX."user_lotterydraw_record";
		$row = M($tablename);
		$res = $row->order("wingold desc")->limit(0,3)->select();
		//dump($row->_sql());
		echo json_encode($res, JSON_UNESCAPED_UNICODE);
		
		
	}
	
	//好友列表
	public function friend(){
		$uid = I("uid");
		$page = I("page");
		if (empty($page)) $page = 1;
		if (!empty($uid)){
			
			$row1 = M(MYTABLE_PRIFIX."user_friend");
			$row2 = M(MYTABLE_PRIFIX."user_info");
			
			//echo "***"; exit;
			$count = $row1->where('user_id='.$uid)->count();
			$pagenum = 50;
			$pagemax = ceil($count / $pagenum);
			$shownow = ($page -1) * $pagenum;
			
			$res1 = $row1->where('user_id='.$uid)->limit($shownow.','.$pagenum)->select();
			//dump($row1->_sql());
			$show = array();
			foreach($res1 as $key1 => $val1){
				//echo $key1."<br>";
				$info = $row2->where('user_id='.$val1['friend_id'])->find();
				$show[$key1] = array('uid' => (int)$val1['friend_id'],
									 'addtime' => (int)$val1['add_time'],
									 'nickname' => !empty($info['nickname']) ? $info['nickname'] : $info['nick_name'],
									 'avatar' => $info['head_picture'],
									 'gender' => (int)$info['sex'],
									 'vm' => (int)$info['gold'],
									 'descr' => $info['sign']);
			}
			$show_json = array('show' => $show,
							   'pagemax' => $pagemax);
			echo json_encode($show_json, JSON_UNESCAPED_UNICODE);
		}else{
			echo -1;
		}
	}
	
	//抽奖记录列表
	public function lottery(){
		$uid = I("uid");
		$page = I("page");
		if (empty($page)) $page = 1;
		if (!empty($uid)){
			
			$row1 = M(MYTABLE_PRIFIX."log_lotterydraw_record_log");
			
			//echo "***"; exit;
			$count = $row1->where('user_id='.$uid)->count();
			$pagenum = 20;
			$pagemax = ceil($count / $pagenum);
			$shownow = ($page -1) * $pagenum;
			
			$res1 = $row1->where('user_id='.$uid)->limit($shownow.','.$pagenum)->order('id desc')->select();
			//dump($row1->_sql());
			$show = array();
			foreach($res1 as $key1 => $val1){
				//echo $key1."<br>";
				$show[$key1] = array('id' => (int)$val1['id'],
									 'user_id' => (int)$val1['user_id'],
									 'drawtype' => (int)$val1['drawtype'],
									 'drawtimes' => (int)$val1['drawtimes'],
									 'awardtype' => (int)$val1['awardtype'],
									 'awardvalue' => (int)$val1['awardvalue'],
									 'operator' => date("Y-m-d H:i:s", $val1['operator']));
			}
			$show_json = array('show' => $show,
							   'pagemax' => $pagemax);
			echo json_encode($show_json, JSON_UNESCAPED_UNICODE);
		}else{
			echo -1;
		}
	}
	
}