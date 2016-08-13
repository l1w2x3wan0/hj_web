<?php
// 其它文件

class IpAction extends BaseAction {

	protected $By_tpl = 'Other'; 
	
	public function jinbi(){
		$table = "fx_other_jinbi";
		$row = M($table);
		
		$user_id = I("user_id");
		$gold = I("gold");
		$meno = I("meno");
		
		if (!empty($user_id) && !empty($gold)){
			if (strlen($user_id) < 5){
				$this->error('用户UID不能小于5位');
				exit;
			}
			
			if (empty($gold)){
				$this->error('金币不能为空');
				exit;
			}
			
			$data = array();
			$data['user_id']   = $user_id ;
			$data['gold']   = $gold;
			$data['meno'] =  $meno;
			$data['czz'] =  $_SESSION['username'];
			$data['addtime']   = time();

			$result = $row->add($data);
			//dump($employee->_sql()); exit;
			if($result){
				//增加操作记录
				$logs = C('JINBI_MSG_ADD_SUCCESS');
				$remark = "(增加金币:".$data['gold'].",".json_encode($data).")";
				adminlog($logs,$remark);
				
				//调用金币接口
				$url = DB_HOST."/Pay/jinbi.php?id=".$result;
				//echo $url; 
				$jinbi_result = curlGET($url);
				//echo $jinbi_result; //exit; 
				$len = strlen($jinbi_result)-3;
				$notify_status = substr($jinbi_result,$len,1);
				//echo $notify_status; exit; 
				//修改通知状态  notify_status=1,notify_times=notify_times+1,notify_date=".time()."
				if ($notify_status == "1"){
					$jinbi = array();
					$jinbi['notify_status'] = 1;
					$jinbi['notify_date'] = time();
					$result1 = $row->where("id=".$result)->save($jinbi);
					$result2 = $row->where("id=".$result)->setInc('notify_times',1);
				}
				
				$lib_display = $this->By_tpl."/jinbi";
				$this->success('添加成功',U($lib_display));
			}else{
				//增加操作记录
				$logs = C('JINBI_MSG_ADD_FALSE');
				adminlog($logs);
				
				$this->error('添加失败');
				exit;
			}
		
		}else{
			//增加操作记录
			$logs = C('JINBI_MSG_ADD');
			adminlog($logs);
			
			import('ORG.Util.Page');
			$count = $row->count('id');
			$Page       = new Page($count,10);//实例化分页类传入总记录数和每页显示的记录数		
			$show       = $Page->show();// 分页显示输出
			$list = $row->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
			foreach($list as $key=>$value){
				$list[$key]['notify_status'] = ($list[$key]['notify_status']=="1") ? "成功" : "失败";
				$list[$key]['addtime'] = (!empty($list[$key]['addtime'])) ? date("Y-m-d H:i:s", $list[$key]['addtime']) : "";
				$list[$key]['notify_times'] = (!empty($list[$key]['notify_times'])) ? date("Y-m-d H:i:s", $list[$key]['notify_times']) : "";
			}
			
			$this->assign('list',$list);
			$this->assign('pageshow',$show);
			
			$this->assign('left_css',"41");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl."/jinbi";
			$this->display($lib_display);
		}
	}
	

	//玩家详情
	public function userlist(){
		$table = "user_info";
		$row = M($table);
		$table1 = "zjh_order";
		$row1 = M($table1);
		$table2 = "log_gold_change_log_".date("Ym");
		$row2 = M($table2);
		$table3 = "log_game_record_log_".date("Ym");
		$row3 = M($table3);
		
		$cate_id = I("cate_id");
		$keywords = I("keywords");
		$act = I("act");
		$id = I("id");
		$gameid = I("gameid");
		$sortscate = I("sortscate");
		$sortsflag = I("sortsflag");
		import('ORG.Util.Page');
		
		//echo $act."**";
		
		if (empty($act)){
			if (!empty($cate_id) || !empty($keywords)){
			
				if (strlen($keywords) < 5 && $cate_id=="1"){
					$this->error('用户UID不能小于5位');
					exit;
				}
				
				$sql = "";
				if (!empty($keywords) && $cate_id=="1"){
					$sql = "user_id like '%".$keywords."%'";
				}
				if (!empty($keywords) && $cate_id=="2"){
					$sql = "nick_name like '%".$keywords."%' or user_name like '%".$keywords."%'";
				}
				
				$count = $row->where($sql)->count('user_id');
				$Page       = new Page($count,PAGE_SHOW);//实例化分页类传入总记录数和每页显示的记录数		
				$show       = $Page->show();// 分页显示输出
				$list = $row->where($sql)->order('user_id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
				//dump($row->_sql());
				foreach($list as $key=>$value){
					$list[$key]['id'] = $key + 1;
					$list[$key]['nick_name'] = !empty($list[$key]['nick_name']) ? $list[$key]['nick_name'] : $list[$key]['user_name'];
				}
				
				$this->assign('list',$list);
				$this->assign('pageshow',$show);
			}else{
				//增加操作记录
				//$logs = C('JINBI_MSG_ADD');
				//adminlog($logs);
				$order = "";
				if ($sortscate=="1"){
					$order .= "gold";
				}else if ($sortscate=="2"){
					$order .= "channel";
				}else if ($sortscate=="3"){
					$order .= "vip_type";
				}else if ($sortscate=="4"){
					$order .= "register_date";
				}else if ($sortscate=="5"){
					$order .= "last_login_date";
				}else{
					$order .= "user_id";
				}
				if ($sortsflag=="1"){
					$order .= " ASC";
				}else{
					$order .= " DESC";
				}
				
				$sql1 = "!((user_id>=".ROBERT1_BEGIN." and user_id<".ROBERT1_END.") or (user_id>=".ROBERT2_BEGIN." and user_id<=".ROBERT2_END."))";
				$count = $row->where($sql1)->count('user_id');
				$Page       = new Page($count,PAGE_SHOW);//实例化分页类传入总记录数和每页显示的记录数		
				$show       = $Page->show();// 分页显示输出
				$list = $row->where($sql1)->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();
				//dump($row->_sql());
				foreach($list as $key=>$value){
					$list[$key]['id'] = $key + 1;
					$list[$key]['nick_name'] = !empty($list[$key]['nick_name']) ? $list[$key]['nick_name'] : $list[$key]['user_name'];
				}
				
				$this->assign('list',$list);
				$this->assign('pageshow',$show);
			}
		}
		
		//echo "222";
		
		$this->assign('left_css',"41");
		$this->assign('By_tpl',$this->By_tpl);
		if ($act=="info"){
			
			$info = $row->where("user_id=".$id)->find();
			//充值金额
			$count1 = $row1->where("user_id='$id' and payment_status in ('1','-2')")->sum('result_money');
			if (empty($count1)) $count1 = 0;
			$info['czje'] = $count1;
			$info['sex'] = ($info['sex']=="0") ? "女" : "男";
			$info['total_pay_num'] = number_format($info['total_pay_num'] / 100, 2);
			//获取IP
			$table9 = "login_log";
			$row9 = M($table9);
			$res9 = $row9->field("login_ip")->where("user_id=".$id)->order("log_id")->find();
			$info['ip1'] = ntoip($res9['login_ip']);
			
			import('ORG.Net.IpLocation');// 导入IpLocation类
			$Ip = new IpLocation('UTFWry.dat'); // 实例化类 参数表示IP地址库文件
			if (!empty($info['ip1'])){
				$area1 = $Ip->getlocation($info['ip1']);
				//print_r($area1);
				$info['area1'] = "(".$area1['country'].")";
			}else{
				$info['area1'] = "";
			}
			
			
			$res9 = $row9->field("login_ip")->where("user_id=".$id)->order("log_id DESC")->find();
			$info['ip2'] = ntoip($res9['login_ip']);
			if (!empty($info['ip2'])){
				$area2 = $Ip->getlocation($info['ip2']);
				//print_r($area1);
				$info['area2'] = "(".$area2['country'].")";
			}else{
				$info['area2'] = "";
			}
			
			$pai_sum = $info['win_count'] + $info['lost_count'];
			$info['lv'] = (!empty($pai_sum)) ? round($info['win_count']/$pai_sum,3)*100 : 0;
			//dump($row->_sql());
			$this->assign('info',$info);
			$lib_display = $this->By_tpl."/userinfo";
		}elseif ($act=="jinbi"){
			
			$count = $row2->where("user_id=".$id)->count('user_id');
			$Page       = new Page($count,PAGE_SHOW);	
			$show       = $Page->show();
			$info = $row2->where("user_id=".$id)->order("curtime DESC")->limit($Page->firstRow.','.$Page->listRows)->select();
			$this->assign('info',$info);
			$this->assign('pageshow',$show);
			$lib_display = $this->By_tpl."/userjinbi";
		}elseif ($act=="play"){
			
			$date11 = I("beginTime");
			$time1 = strtotime($date11);
			$date12 = I("endTime");
			$time2= strtotime($date12) + 60 * 60 * 24;
			$this->assign('date11',$date11);
			$this->assign('date12',$date12);
			if (!empty($date11) && !empty($date12)) $sql0 = " and curtime>=$time1 and curtime<$time2"; else $sql0 = "";
			//echo $sql0;
			$types = array();
			$types[0]['name'] = '豹子';
			$types[0]['num'] = 0;
			$types[1]['name'] = '顺金';
			$types[1]['num'] = 0;
			$types[2]['name'] = '金花';
			$types[2]['num'] = 0;
			$types[3]['name'] = '顺子';
			$types[3]['num'] = 0;
			$types[4]['name'] = '对子';
			$types[4]['num'] = 0;
			$types[5]['name'] = '单牌';
			$types[5]['num'] = 0;
			
			$order = "";
			if ($sortscate=="1"){
				$order .= "curtime";
			}else{
				$order .= "curtime";
			}
			if ($sortsflag=="1"){
				$order .= " ASC";
			}else{
				$order .= " DESC";
			}
			
			$sumall = 0;
			$count = $row3->where("user_id=".$id.$sql0)->count('user_id');
			$Page       = new Page($count,PAGE_SHOW);	
			$show       = $Page->show();
			$info = $row3->where("user_id=".$id.$sql0)->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();
			//dump($row3->_sql());
			$win_count = 0;
			$lost_count = 0;
			foreach ($info as $key => $val){
				if ($val['iswin']==0) $win_count++; else $lost_count++;
				$num1 = substr($val['cards'],0,2);
				$pai1 = '<img src="'.DB_HOST.'/Public/images/'.$num1.'.png" width="30">';
				$num2 = substr($val['cards'],2,2);
				$pai2 = '<img src="'.DB_HOST.'/Public/images/'.$num2.'.png" width="30">';
				$num3 = substr($val['cards'],4,2);
				$pai3 = '<img src="'.DB_HOST.'/Public/images/'.$num3.'.png" width="30">';
				$showcards = $pai1.$pai2.$pai3;
				$info[$key]['showcards'] = $showcards;
				
				$num11 = substr($val['cards'],1,1);
				if ($num11=="a") $num11 = "10"; elseif ($num11=="b") $num11 = "11"; elseif ($num11=="c") $num11 = "12";  elseif ($num11=="d") $num11 = "13";  
				$num12 = substr($val['cards'],3,1);
				if ($num12=="a") $num12 = "10"; elseif ($num12=="b") $num12 = "11"; elseif ($num12=="c") $num12 = "12";  elseif ($num12=="d") $num12 = "13";  
				$num13 = substr($val['cards'],5,1);
				if ($num13=="a") $num13 = "10"; elseif ($num13=="b") $num13 = "11"; elseif ($num13=="c") $num13 = "12";  elseif ($num13=="d") $num13 = "13";  
				$suits = array(substr($val['cards'],0,1), substr($val['cards'],2,1),substr($val['cards'],4,1));
				$nums = array($num11, $num12, $num13);
				sort($nums);
				if ($nums[0]==$nums[1] && $nums[0]==$nums[2]){
					$info[$key]['cardstype'] = '&nbsp;<font color="#993300">(豹子)</font>';
					$types[0]['num']++;
					$sumall++;
				}else if ((($nums[0]+1==$nums[1] && $nums[1]+1==$nums[2]) || ($nums[0]=="1" && $nums[1]=="12" && $nums[2]=="13")) && ($suits[0]==$suits[1] && $suits[0]==$suits[2])){
					$info[$key]['cardstype'] = '&nbsp;<font color="#CC0000">(顺金)</font>';
					$types[1]['num']++;
					$sumall++;
				}else if ($suits[0]==$suits[1] && $suits[0]==$suits[2]){
					$info[$key]['cardstype'] = '&nbsp;<font color="#FF9900">(金花)</font>';
					$types[2]['num']++;
					$sumall++;
				}else if (($nums[0]+1==$nums[1] && $nums[1]+1==$nums[2]) || ($nums[0]=="1" && $nums[1]=="12" && $nums[2]=="13")){
					$info[$key]['cardstype'] = '&nbsp;<font color="#99CCFF">(顺子)</font>';
					$types[3]['num']++;
					$sumall++;
				}else if ($nums[0]==$nums[1] || $nums[1]==$nums[2]){
					$info[$key]['cardstype'] = '&nbsp;<font color="#66CCCC">(对子)</font>';
					$types[4]['num']++;
					$sumall++;
				}else{
					$info[$key]['cardstype'] = '&nbsp;(单牌)';
					$types[5]['num']++;
					$sumall++;
				}  
			}
			$pai_sum = $win_count + $lost_count;
			$shenglv = (!empty($pai_sum)) ? round($win_count/$pai_sum,3)*100 : 0;
			$this->assign('shenglv',$shenglv);
			
			$this->assign('info',$info);
			$this->assign('pageshow',$show);
			
			foreach($types as $key => $val){
				if ($val['num'] == 0){
					$types[$key]['bl'] = "";
				}else{
					$bl = round($val['num']/$sumall, 3) * 100;
					$types[$key]['bl'] = "(".$bl."%)";
				}
				
				$types[$key]['allnum'] = 0;
				$types[$key]['allbl'] = 0;
			}
			
			$win_count = 0;
			$lost_count = 0;
			$userall = 0;
			$infoall = $row3->where("user_id=".$id)->order($order)->select();
			foreach ($infoall as $key => $val){
				if ($val['iswin']==0) $win_count++; else $lost_count++;
				$num11 = substr($val['cards'],1,1);
				if ($num11=="a") $num11 = "10"; elseif ($num11=="b") $num11 = "11"; elseif ($num11=="c") $num11 = "12";  elseif ($num11=="d") $num11 = "13";  
				$num12 = substr($val['cards'],3,1);
				if ($num12=="a") $num12 = "10"; elseif ($num12=="b") $num12 = "11"; elseif ($num12=="c") $num12 = "12";  elseif ($num12=="d") $num12 = "13";  
				$num13 = substr($val['cards'],5,1);
				if ($num13=="a") $num13 = "10"; elseif ($num13=="b") $num13 = "11"; elseif ($num13=="c") $num13 = "12";  elseif ($num13=="d") $num13 = "13";  
				$suits = array(substr($val['cards'],0,1), substr($val['cards'],2,1),substr($val['cards'],4,1));
				$nums = array($num11, $num12, $num13);
				sort($nums);
				if ($nums[0]==$nums[1] && $nums[0]==$nums[2]){
					$types[0]['allnum']++;
					$userall++;
				}else if ((($nums[0]+1==$nums[1] && $nums[1]+1==$nums[2]) || ($nums[0]=="1" && $nums[1]=="12" && $nums[2]=="13")) && ($suits[0]==$suits[1] && $suits[0]==$suits[2])){
					$types[1]['allnum']++;
					$userall++;
				}else if ($suits[0]==$suits[1] && $suits[0]==$suits[2]){
					$types[2]['allnum']++;
					$userall++;
				}else if (($nums[0]+1==$nums[1] && $nums[1]+1==$nums[2]) || ($nums[0]=="1" && $nums[1]=="12" && $nums[2]=="13")){
					$types[3]['allnum']++;
					$userall++;
				}else if ($nums[0]==$nums[1] || $nums[1]==$nums[2]){
					$types[4]['allnum']++;
					$userall++;
				}else{
					$types[5]['allnum']++;
					$userall++;
				}  
			}
			$pai_sum = $win_count + $lost_count;
			$shenglv = (!empty($pai_sum)) ? round($win_count/$pai_sum,3)*100 : 0;
			$this->assign('shenglv0',$shenglv);
			
			foreach($types as $key => $val){
				if ($val['allnum'] == 0){
					$types[$key]['allbl'] = "";
				}else{
					$allbl = round($val['allnum']/$userall, 3) * 100;
					$types[$key]['allbl'] = "(".$allbl."%)";
				}
			}
			

			
			$this->assign('types',$types);
			$this->assign('id',$id);
			$lib_display = $this->By_tpl."/userplay";
		}elseif ($act=="gameplay"){
			
			$info = $row3->where("gameid=".$gameid)->order("curtime DESC")->select();
			foreach ($info as $key => $val){
				
				$num1 = substr($val['cards'],0,2);
				$pai1 = '<img src="'.DB_HOST.'/Public/images/'.$num1.'.png" width="30">';
				$num2 = substr($val['cards'],2,2);
				$pai2 = '<img src="'.DB_HOST.'/Public/images/'.$num2.'.png" width="30">';
				$num3 = substr($val['cards'],4,2);
				$pai3 = '<img src="'.DB_HOST.'/Public/images/'.$num3.'.png" width="30">';
				$showcards = $pai1.$pai2.$pai3;
				$info[$key]['showcards'] = $showcards;
				
				$num11 = substr($val['cards'],1,1);
				if ($num11=="a") $num11 = "10"; elseif ($num11=="b") $num11 = "11"; elseif ($num11=="c") $num11 = "12";  elseif ($num11=="d") $num11 = "13";  
				$num12 = substr($val['cards'],3,1);
				if ($num12=="a") $num12 = "10"; elseif ($num12=="b") $num12 = "11"; elseif ($num12=="c") $num12 = "12";  elseif ($num12=="d") $num12 = "13";  
				$num13 = substr($val['cards'],5,1);
				if ($num13=="a") $num13 = "10"; elseif ($num13=="b") $num13 = "11"; elseif ($num13=="c") $num13 = "12";  elseif ($num13=="d") $num13 = "13";  
				$suits = array(substr($val['cards'],0,1), substr($val['cards'],2,1),substr($val['cards'],4,1));
				$nums = array($num11, $num12, $num13);
				sort($nums);
				if ($nums[0]==$nums[1] && $nums[0]==$nums[2]){
					$info[$key]['cardstype'] = '&nbsp;<font color="#993300">(豹子)</font>';
				}else if ((($nums[0]+1==$nums[1] && $nums[1]+1==$nums[2]) || ($nums[0]=="1" && $nums[1]=="12" && $nums[2]=="13")) && ($suits[0]==$suits[1] && $suits[0]==$suits[2])){
					$info[$key]['cardstype'] = '&nbsp;<font color="#CC0000">(顺金)</font>';
				}else if ($suits[0]==$suits[1] && $suits[0]==$suits[2]){
					$info[$key]['cardstype'] = '&nbsp;<font color="#FF9900">(金花)</font>';
				}else if (($nums[0]+1==$nums[1] && $nums[1]+1==$nums[2]) || ($nums[0]=="1" && $nums[1]=="12" && $nums[2]=="13")){
					$info[$key]['cardstype'] = '&nbsp;<font color="#99CCFF">(顺子)</font>';
				}else if ($nums[0]==$nums[1] || $nums[1]==$nums[2]){
					$info[$key]['cardstype'] = '&nbsp;<font color="#66CCCC">(对子)</font>';
				}else{
					$info[$key]['cardstype'] = '&nbsp;(单牌)';
				}  
			}
			$this->assign('info',$info);
			$lib_display = $this->By_tpl."/gameplay";
		}else{
			$lib_display = $this->By_tpl."/userlist";
		}
		
		$this->display($lib_display);
	}
	
	//头像审核
	public function touxiang(){
		$table = "user_info";
		$row = M($table);
		import('ORG.Util.Page');
		$beginTime = I("beginTime");
		$endTime = I("endTime");
		$user_id = I("user_id");
		$user_name = I("user_name");
		
		if (!empty($_POST)){
			$flag = 0;
			foreach($_POST['uid'] as $key => $val){
				$num = $key+1;
				$show = "flag".$num;
				//echo $val."**".$_POST[$show]."<br>";
				if (!empty($val) && $_POST[$show]=="1"){
					$flag = 1;
					$url = DB_HOST."Pay/touxiang.php?user_id=".$val;
					//echo $url."<br>";
					$result = curlGET($url);
					//echo $result;
				}
			}
			//exit;
			if ($flag == 1){
				$this->success('提交成功',U($this->By_tpl.'/touxiang'));		
				exit;
			}
			
		}
		
		//查询不能大于当天
		$timenow = strtotime(date("Y-m-d"));
		if (strtotime($beginTime)>strtotime($endTime)){
			$this->error('开始日期不能大于结束日期');
			exit;
		}
		
		if (!empty($beginTime) && !empty($endTime)){
			$date11 = date("Y-m-d", strtotime($beginTime));
			$date12 = date("Y-m-d", strtotime($endTime));
			
			//$datenow = date("d", strtotime($endTime));
			//$dateend = date("d", strtotime($beginTime));
		}else{
			//$date12 = date("Y-m-d");
			//$date11 = date("Y-m-d", strtotime("-2 day"));
		}
		
		$this->assign('date11',$date11);
		$this->assign('date12',$date12);
		$this->assign('user_id',$user_id);
		$this->assign('user_name',$user_name);
		//计算时间区段
		$seldate = array();
		$seldate[0] = array('date1' => date("Y-m-d"),
							'date2' => date("Y-m-d"));
		$seldate[1] = array('date1' => date("Y-m-d",strtotime("-1 day")),
							'date2' => date("Y-m-d",strtotime("-1 day")));
		$seldate[2] = array('date1' => date("Y-m-d",strtotime("-6 day")),
							'date2' => date("Y-m-d"));
		$seldate[3] = array('date1' => date("Y-m-d",strtotime("-29 day")),
							'date2' => date("Y-m-d"));
		$this->assign('seldate',$seldate);
		$sql0 = "";
		if (!empty($date11) && !empty($date12)){
			$sql0 .= " and (register_date>'$date11' and register_date<='$date12 23:59:59')";
		}
		if (!empty($user_id)){
			$sql0 .= " and user_id like '%$user_id%'";
		}
		if (!empty($user_name)){
			$sql0 .= " and user_name like '%$user_name%'";
		}
		
		$sql1 = "head_picture!='0' and !((user_id>=".ROBERT1_BEGIN." and user_id<".ROBERT1_END.") or (user_id>=".ROBERT2_BEGIN." and user_id<=".ROBERT2_END.")) $sql0";
		$count = 200;
		$Page       = new Page($count,50);	
		$show       = $Page->show();
		$user = $row->where($sql1)->order("head_picture DESC,register_date DESC")->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($row->_sql());
		$this->assign('user',$user);
		$this->assign('pageshow',$show);
		
		$lib_display = $this->By_tpl."/touxiang";
		$this->display($lib_display);
	}
	
	//短信记录
	public function duanxin(){
		$table = "duanxin";
		$row = M($table);
		import('ORG.Util.Page');
		$count = $row->count('id');
		$Page       = new Page($count,PAGE_SHOW);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		$list = $row->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		foreach($list as $key=>$value){
				$list[$key]['status'] = ($list[$key]['status']=="1") ? "成功" : "失败";
				$list[$key]['addtime'] = (!empty($list[$key]['addtime'])) ? date("Y-m-d H:i:s", $list[$key]['addtime']) : "";
		}
			
		$this->assign('list',$list);
		$this->assign('pageshow',$show);
		
		//获取短信剩余条数
		$url = DB_HOST."/Pay/sms_queryBalance_demo.php";
		$showduanxin = curlGET($url);
		$this->assign('showduanxin',$showduanxin);
			
		$this->assign('left_css',"41");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/duanxin";
		$this->display($lib_display);
	}
	
	//排行榜
	public function paihang(){
		$table = "fx_paihang";
		$row = M($table);
		$date1 = date("Y-m-d");
		$time1 = strtotime($date1);
		$table1 = "log_game_record_log_".date("Ym");
		$row1 = M($table1);
		$table2 = "user_info";
		$row2 = M($table2);
		
		//机器人
		$sql1 = " and ((user_id>=".ROBERT1_BEGIN." and user_id<".ROBERT1_END.") or (user_id>=".ROBERT2_BEGIN." and user_id<=".ROBERT2_END."))";
		//没有则生成20个机器人号码
		$flag = 0;
		$total = $row->where("flag=$flag")->count();
		if ($total==0){
			$user = array();
			$res = $row1->field("DISTINCT user_id")->where("roomid=3 $sql1")->limit(0,20)->select();
			foreach($res as $key => $val){
				$rs = $row2->field("head_picture,nick_name,sex")->where("user_id=".$val['user_id'])->find();
				$tx_srouce = $rs['head_picture'];
				$tx = "http://api.pic.kk520.com:9103/work/".$rs['head_picture'].".jpg";
				$user[$key] = array('id' => $key+1,
									'user_id' => $val['user_id'],
									'nick_name' => $rs['nick_name'],
									'sex' => $rs['sex'],
									'tx_srouce' => $tx_srouce,
									'tx' => $tx);
			}
			
			$data9 = array('data' => $date1,
						   'flag' => $flag,
						   'tongji' => json_encode($user),
						   'addtime' => time());
			$result = $row->add($data9);
		}else{
			$info = $row->where("flag=$flag")->find();
			$user = json_decode($info['tongji'], true);
		}
		
		//财富榜参数
		$flag = 1;
		$total = $row->where("flag=$flag")->count();
		if ($total==0){
			$can1 = array();
			$can1['jinbi1'] = !empty($_POST['jinbi1']) ? $_POST['jinbi1'] : 20000000;
			$can1['gailv11'] = !empty($_POST['gailv11']) ? $_POST['gailv11'] : 5;
			$can1['gailv12'] = !empty($_POST['gailv12']) ? $_POST['gailv12'] : 10;
			$can1['gailv13'] = !empty($_POST['gailv13']) ? $_POST['gailv13'] : 15;
			$can1['gailv14'] = !empty($_POST['gailv14']) ? $_POST['gailv14'] : 20;
			$can1['gailv15'] = !empty($_POST['gailv15']) ? $_POST['gailv15'] : 50;
			$can1['bian110'] = !empty($_POST['bian110']) ? $_POST['bian110'] : -30;
			$can1['bian111'] = !empty($_POST['bian111']) ? $_POST['bian111'] : 30;
			$can1['bian120'] = !empty($_POST['bian120']) ? $_POST['bian120'] : -25;
			$can1['bian121'] = !empty($_POST['bian121']) ? $_POST['bian121'] : 25;
			$can1['bian130'] = !empty($_POST['bian130']) ? $_POST['bian130'] : -20;
			$can1['bian131'] = !empty($_POST['bian131']) ? $_POST['bian131'] : 20;
			$can1['bian140'] = !empty($_POST['bian140']) ? $_POST['bian140'] : -15;
			$can1['bian141'] = !empty($_POST['bian141']) ? $_POST['bian141'] : 15;
			$can1['bian150'] = !empty($_POST['bian150']) ? $_POST['bian150'] : -10;
			$can1['bian151'] = !empty($_POST['bian151']) ? $_POST['bian151'] : 10;
			$data9 = array('data' => $date1,
						   'flag' => $flag,
						   'tongji' => json_encode($can1),
						   'addtime' => time());
			$result = $row->add($data9);
		}else{
			$info = $row->where("flag=$flag")->find();
			$can1 = json_decode($info['tongji'], true);
		}
		
		//昨日赢金榜参数
		$flag = 2;
		$total = $row->where("flag=$flag")->count();
		if ($total==0){
			$can2 = array();
			$can2['jinbi1'] = !empty($_POST['jinbi2']) ? $_POST['jinbi2'] : 10000000;
			$can2['gailv11'] = !empty($_POST['gailv21']) ? $_POST['gailv21'] : 5;
			$can2['gailv12'] = !empty($_POST['gailv22']) ? $_POST['gailv22'] : 10;
			$can2['gailv13'] = !empty($_POST['gailv23']) ? $_POST['gailv23'] : 15;
			$can2['gailv14'] = !empty($_POST['gailv24']) ? $_POST['gailv24'] : 20;
			$can2['gailv15'] = !empty($_POST['gailv25']) ? $_POST['gailv25'] : 50;
			$can2['bian110'] = !empty($_POST['bian210']) ? $_POST['bian210'] : -30;
			$can2['bian111'] = !empty($_POST['bian211']) ? $_POST['bian211'] : 30;
			$can2['bian120'] = !empty($_POST['bian220']) ? $_POST['bian220'] : -25;
			$can2['bian121'] = !empty($_POST['bian221']) ? $_POST['bian221'] : 25;
			$can2['bian130'] = !empty($_POST['bian230']) ? $_POST['bian230'] : -20;
			$can2['bian131'] = !empty($_POST['bian231']) ? $_POST['bian231'] : 20;
			$can2['bian140'] = !empty($_POST['bian240']) ? $_POST['bian240'] : -15;
			$can2['bian141'] = !empty($_POST['bian241']) ? $_POST['bian241'] : 15;
			$can2['bian150'] = !empty($_POST['bian250']) ? $_POST['bian250'] : -10;
			$can2['bian151'] = !empty($_POST['bian251']) ? $_POST['bian251'] : 10;
			$data9 = array('data' => $date1,
						   'flag' => $flag,
						   'tongji' => json_encode($can2),
						   'addtime' => time());
			$result = $row->add($data9);
		}else{
			$info = $row->where("flag=$flag")->find();
			$can2 = json_decode($info['tongji'], true);
		}
		
		//昨日充值榜参数
		$flag = 3;
		$total = $row->where("flag=$flag")->count();
		if ($total==0){
			$can3 = array();
			$can3['gailv11'] = !empty($_POST['gailv31']) ? $_POST['gailv31'] : 1;
			$can3['gailv12'] = !empty($_POST['gailv32']) ? $_POST['gailv32'] : 2;
			$can3['gailv13'] = !empty($_POST['gailv33']) ? $_POST['gailv33'] : 7;
			$can3['gailv14'] = !empty($_POST['gailv34']) ? $_POST['gailv34'] : 15;
			$can3['gailv15'] = !empty($_POST['gailv35']) ? $_POST['gailv35'] : 25;
			$can3['gailv16'] = !empty($_POST['gailv35']) ? $_POST['gailv35'] : 50;
			$can3['bian110'] = !empty($_POST['bian310']) ? $_POST['bian310'] : 800;
			$can3['bian111'] = !empty($_POST['bian311']) ? $_POST['bian311'] : 1000;
			$can3['bian120'] = !empty($_POST['bian320']) ? $_POST['bian320'] : 600;
			$can3['bian121'] = !empty($_POST['bian321']) ? $_POST['bian321'] : 800;
			$can3['bian130'] = !empty($_POST['bian330']) ? $_POST['bian330'] : 500;
			$can3['bian131'] = !empty($_POST['bian331']) ? $_POST['bian331'] : 600;
			$can3['bian140'] = !empty($_POST['bian340']) ? $_POST['bian340'] : 400;
			$can3['bian141'] = !empty($_POST['bian341']) ? $_POST['bian341'] : 500;
			$can3['bian150'] = !empty($_POST['bian350']) ? $_POST['bian350'] : 300;
			$can3['bian151'] = !empty($_POST['bian351']) ? $_POST['bian351'] : 400;
			$can3['bian160'] = !empty($_POST['bian350']) ? $_POST['bian350'] : 100;
			$can3['bian161'] = !empty($_POST['bian351']) ? $_POST['bian351'] : 300;
			$data9 = array('data' => $date1,
						   'flag' => $flag,
						   'tongji' => json_encode($can3),
						   'addtime' => time());
			$result = $row->add($data9);
		}else{
			$info = $row->where("flag=$flag")->find();
			$can3 = json_decode($info['tongji'], true);
		}
		
		/*
		//排行第1的金币值
		$res = $row2->field("MAX(gold) as maxgold")->where("channel=11")->find();
		$maxgold = $res['maxgold']; 
		
		//生成财富榜
		$gold1 = array();
		$basenum = $maxgold + $can1['jinbi1'];
		$prize_arr = array('a'=>$can1['gailv11'],'b'=>$can1['gailv12'],'c'=>$can1['gailv13'],'d'=>$can1['gailv14'],'e'=>$can1['gailv15']);
		for($i=0; $i<20; $i++){
			$result = get_rand($prize_arr);
			if ($result=="a") {
				$num = rand($can1['bian110'], $can1['bian111']);
			}elseif ($result=="b"){
				$num = rand($can1['bian120'], $can1['bian121']);
			}elseif ($result=="c"){
				$num = rand($can1['bian130'], $can1['bian131']);
			}elseif ($result=="d"){
				$num = rand($can1['bian140'], $can1['bian141']);
			}elseif ($result=="e"){
				$num = rand($can1['bian150'], $can1['bian151']);
			} 
			$user[$i]['gold1'] = round($basenum + $basenum * rand(90,100) / 100 * $num / 100);
			//echo $i."**".$user[$i]['gold1']."<br>";
			$gold1[$i] = $user[$i]['gold1'];
			//echo ($result."<br>");
		}
		
		//生成昨日赢金榜
		$basenum = $maxgold + $can2['jinbi1'];
		$prize_arr = array('a'=>$can2['gailv11'],'b'=>$can2['gailv12'],'c'=>$can2['gailv13'],'d'=>$can2['gailv14'],'e'=>$can2['gailv15']);
		for($i=0; $i<20; $i++){
			$result = get_rand($prize_arr);
			if ($result=="a") {
				$num = rand($can2['bian110'], $can2['bian111']);
			}elseif ($result=="b"){
				$num = rand($can2['bian120'], $can2['bian121']);
			}elseif ($result=="c"){
				$num = rand($can2['bian130'], $can2['bian131']);
			}elseif ($result=="d"){
				$num = rand($can2['bian140'], $can2['bian141']);
			}elseif ($result=="e"){
				$num = rand($can2['bian150'], $can2['bian151']);
			} 
			$user[$i]['gold2'] = round($basenum + $basenum * rand(90,100) / 100 * $num / 100);
			//echo ($result."<br>");
		}
		
		//生成充值榜参数
		$prize_arr = array('a'=>$can3['gailv11'],'b'=>$can3['gailv12'],'c'=>$can3['gailv13'],'d'=>$can3['gailv14'],'e'=>$can3['gailv15'],'f'=>$can3['gailv16']);
		for($i=0; $i<20; $i++){
			$result = get_rand($prize_arr);
			if ($result=="a") {
				$num = rand($can3['bian110'], $can3['bian111']);
			}elseif ($result=="b"){
				$num = rand($can3['bian120'], $can3['bian121']);
			}elseif ($result=="c"){
				$num = rand($can3['bian130'], $can3['bian131']);
			}elseif ($result=="d"){
				$num = rand($can3['bian140'], $can3['bian141']);
			}elseif ($result=="e"){
				$num = rand($can3['bian150'], $can3['bian151']);
			}elseif ($result=="f"){
				$num = rand($can3['bian160'], $can3['bian161']);
			} 
			if ($num % 2==1) $num = $num + 1;
			$user[$i]['gold3'] = $num;
			//echo ($result."<br>");
		}
		
		
		$flag = 4;
		$total = $row->where("flag=$flag")->count();
		if ($total==0){
			$data9 = array('data' => $date1,
						   'flag' => 4,
						   'tongji' => json_encode($user),
						   'addtime' => time());
			$result = $row->add($data9);
		}else{
			$data9 = array('tongji' => json_encode($user),
						   'addtime' => time());
			$result = $row->where("flag=$flag")->save($data9);
		}
		*/
		//print_r($user);
		//$this->assign('can0',$can0);
		$this->assign('can1',$can1);
		$this->assign('can2',$can2);
		$this->assign('can3',$can3);
		
		$wei = I("wei");
		$act = I("act");
		$this->assign('wei',$wei);
		$this->assign('act',$act);
		
		if ($wei=="1" and $act=="can"){
			if (!empty($_POST)){
				$can1 = array();
				$can1['jinbi1'] = !empty($_POST['jinbi1']) ? (int)$_POST['jinbi1'] : 20000000;
				$can1['gailv11'] = !empty($_POST['gailv11']) ? (int)$_POST['gailv11'] : 5;
				$can1['gailv12'] = !empty($_POST['gailv12']) ? (int)$_POST['gailv12'] : 10;
				$can1['gailv13'] = !empty($_POST['gailv13']) ? (int)$_POST['gailv13'] : 15;
				$can1['gailv14'] = !empty($_POST['gailv14']) ? (int)$_POST['gailv14'] : 20;
				$can1['gailv15'] = !empty($_POST['gailv15']) ? (int)$_POST['gailv15'] : 50;
				$can1['bian110'] = !empty($_POST['bian110']) ? (int)$_POST['bian110'] : -30;
				$can1['bian111'] = !empty($_POST['bian111']) ? (int)$_POST['bian111'] : 30;
				$can1['bian120'] = !empty($_POST['bian120']) ? (int)$_POST['bian120'] : -25;
				$can1['bian121'] = !empty($_POST['bian121']) ? (int)$_POST['bian121'] : 25;
				$can1['bian130'] = !empty($_POST['bian130']) ? (int)$_POST['bian130'] : -20;
				$can1['bian131'] = !empty($_POST['bian131']) ? (int)$_POST['bian131'] : 20;
				$can1['bian140'] = !empty($_POST['bian140']) ? (int)$_POST['bian140'] : -15;
				$can1['bian141'] = !empty($_POST['bian141']) ? (int)$_POST['bian141'] : 15;
				$can1['bian150'] = !empty($_POST['bian150']) ? (int)$_POST['bian150'] : -10;
				$can1['bian151'] = !empty($_POST['bian151']) ? (int)$_POST['bian151'] : 10;
				$data9 = array('data' => date("Y-m-d"),
							   'tongji' => json_encode($can1),
							   'addtime' => time());
				//print_r($can1);
				$result = $row->where("flag=1")->save($data9);	
				$this->success('配置修改成功',U($this->By_tpl.'/paihang'));		
				exit;
			}
			
			$this->assign('left_css',"41");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl."/can1";
			$this->display($lib_display);
			exit;
		}
		
		if ($wei=="2" and $act=="can"){
			if (!empty($_POST)){
				$can1 = array();
				$can1['jinbi1'] = !empty($_POST['jinbi1']) ? (int)$_POST['jinbi1'] : 10000000;
				$can1['gailv11'] = !empty($_POST['gailv11']) ? (int)$_POST['gailv11'] : 5;
				$can1['gailv12'] = !empty($_POST['gailv12']) ? (int)$_POST['gailv12'] : 10;
				$can1['gailv13'] = !empty($_POST['gailv13']) ? (int)$_POST['gailv13'] : 15;
				$can1['gailv14'] = !empty($_POST['gailv14']) ? (int)$_POST['gailv14'] : 20;
				$can1['gailv15'] = !empty($_POST['gailv15']) ? (int)$_POST['gailv15'] : 50;
				$can1['bian110'] = !empty($_POST['bian110']) ? (int)$_POST['bian110'] : -30;
				$can1['bian111'] = !empty($_POST['bian111']) ? (int)$_POST['bian111'] : 30;
				$can1['bian120'] = !empty($_POST['bian120']) ? (int)$_POST['bian120'] : -25;
				$can1['bian121'] = !empty($_POST['bian121']) ? (int)$_POST['bian121'] : 25;
				$can1['bian130'] = !empty($_POST['bian130']) ? (int)$_POST['bian130'] : -20;
				$can1['bian131'] = !empty($_POST['bian131']) ? (int)$_POST['bian131'] : 20;
				$can1['bian140'] = !empty($_POST['bian140']) ? (int)$_POST['bian140'] : -15;
				$can1['bian141'] = !empty($_POST['bian141']) ? (int)$_POST['bian141'] : 15;
				$can1['bian150'] = !empty($_POST['bian150']) ? (int)$_POST['bian150'] : -10;
				$can1['bian151'] = !empty($_POST['bian151']) ? (int)$_POST['bian151'] : 10;
				$data9 = array('data' => date("Y-m-d"),
							   'tongji' => json_encode($can1),
							   'addtime' => time());
				//print_r($can1);
				$result = $row->where("flag=2")->save($data9);	
				$this->success('配置修改成功',U($this->By_tpl.'/paihang'));		
				exit;
			}
			
			$this->assign('left_css',"41");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl."/can2";
			$this->display($lib_display);
			exit;
		}
		
		if ($wei=="3" and $act=="can"){
			if (!empty($_POST)){
				$can3 = array();
				$can3['gailv11'] = !empty($_POST['gailv11']) ? (int)$_POST['gailv11'] : 1;
				$can3['gailv12'] = !empty($_POST['gailv12']) ? (int)$_POST['gailv12'] : 2;
				$can3['gailv13'] = !empty($_POST['gailv13']) ? (int)$_POST['gailv13'] : 7;
				$can3['gailv14'] = !empty($_POST['gailv14']) ? (int)$_POST['gailv14'] : 15;
				$can3['gailv15'] = !empty($_POST['gailv15']) ? (int)$_POST['gailv15'] : 25;
				$can3['gailv16'] = !empty($_POST['gailv16']) ? (int)$_POST['gailv16'] : 50;
				$can3['bian110'] = !empty($_POST['bian110']) ? (int)$_POST['bian110'] : 800;
				$can3['bian111'] = !empty($_POST['bian111']) ? (int)$_POST['bian111'] : 1000;
				$can3['bian120'] = !empty($_POST['bian120']) ? (int)$_POST['bian120'] : 600;
				$can3['bian121'] = !empty($_POST['bian121']) ? (int)$_POST['bian121'] : 800;
				$can3['bian130'] = !empty($_POST['bian130']) ? (int)$_POST['bian130'] : 500;
				$can3['bian131'] = !empty($_POST['bian131']) ? (int)$_POST['bian131'] : 600;
				$can3['bian140'] = !empty($_POST['bian140']) ? (int)$_POST['bian140'] : 400;
				$can3['bian141'] = !empty($_POST['bian141']) ? (int)$_POST['bian141'] : 500;
				$can3['bian150'] = !empty($_POST['bian150']) ? (int)$_POST['bian150'] : 300;
				$can3['bian151'] = !empty($_POST['bian151']) ? (int)$_POST['bian151'] : 400;
				$can3['bian160'] = !empty($_POST['bian160']) ? (int)$_POST['bian160'] : 100;
				$can3['bian161'] = !empty($_POST['bian161']) ? (int)$_POST['bian161'] : 300;
				$data9 = array('data' => date("Y-m-d"),
							   'tongji' => json_encode($can3),
							   'addtime' => time());
				//print_r($can1);
				$result = $row->where("flag=3")->save($data9);	
				$this->success('配置修改成功',U($this->By_tpl.'/paihang'));		
				exit;
			}
			
			$this->assign('left_css',"41");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl."/can3";
			$this->display($lib_display);
			exit;
		}
		
		if ($wei=="1" and $act=="do"){
			$url = DB_HOST."/Pay/getpaihang1.php";
			$result = curlGET($url);
			$this->success('重新成功，客户端下次获取时会更新',U($this->By_tpl.'/paihang'));		
			exit;
		}
		
		if ($wei=="2" and $act=="do"){
			$url = DB_HOST."/Pay/getpaihang2.php";
			$result = curlGET($url);
			$this->success('重新成功，客户端下次获取时会更新',U($this->By_tpl.'/paihang'));		
			exit;
		}
		
		if ($wei=="3" and $act=="do"){
			$url = DB_HOST."/Pay/getpaihang3.php";
			$result = curlGET($url);
			$this->success('重新成功，客户端下次获取时会更新',U($this->By_tpl.'/paihang'));		
			exit;
		}
		
		$info = $row->where("flag=5")->find();
		$pai1 = json_decode($info['tongji'], true);
		$info = $row->where("flag=6")->find();
		$pai2 = json_decode($info['tongji'], true);
		$info = $row->where("flag=7")->find();
		$pai3 = json_decode($info['tongji'], true);
		$pai3_show = array();
		foreach ($pai3 as $key => $val){
			if ($key < 10) $pai3_show[$key] = $pai3[$key];
		}
		$this->assign('pai1',$pai1);
		$this->assign('pai2',$pai2);
		$this->assign('pai3',$pai3_show);
		//exit;
		$this->assign('left_css',"41");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/paihang";
		$this->display($lib_display);
	}
	
	//老虎机
	public function tiger(){
		$table1 = "kingflower.manual_configure_tiger";
		$row1 = M($table1);
		$table2 = "kingflower.profile_tiger_configure";
		$row2 = M($table2);
		
		if(!empty($_POST)){
			$tactics = $_POST['tactics'];
			$mingold = $_POST['mingold'];
			$maxgold = $_POST['maxgold'];
			$systemwingold = $_POST['systemwingold'];
			$tiger = $_POST['tiger'];
			
			$data9 = array('mingold' => $mingold,
						   'maxgold' => $maxgold,
						   'systemwingold' => $systemwingold);
			$result = $row1->where("tactics=".$tactics)->save($data9);	
			
			foreach ($tiger as $key => $val){
				$data9 = array('productrate' => $val['productrate']);
				$result = $row2->where("id=".$val['id'])->save($data9);	
				//dump($row2->_sql());
			}
			//exit;
			$this->success('修改成功',U($this->By_tpl.'/tiger'));
			exit;
		}
		
		$list = $row1->order('tactics')->select();
		//dump($row1->_sql());
		foreach($list as $key => $val){
			$list[$key]['sub'] = $row2->where("tactics=".$val['tactics'])->order('id')->select();
			//dump($row2->_sql());
		}
		//print_r($list);
		$this->assign('list',$list);
		//exit;
		$this->assign('left_css',"41");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tiger";
		$this->display($lib_display);
	}
	
	//判断目录是否为空
	public function is_empty_dir($fp)    
    {    
        $H = @opendir($fp); 
        $i=0;    
        while($_file=readdir($H)){    
            $i++;    
        }    
        closedir($H);    
        if($i>2){ 
            return 1; 
        }else{ 
            return 2;  //true
        } 
    } 
	
	//空方法
	 public function _empty() {  
        $this->display("Index/index");
    }
}