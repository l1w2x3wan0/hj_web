<?php
// 其它文件

class OtherAction extends BaseAction {

	protected $By_tpl = 'Other'; 
	
	public function jinbi(){
		$table = "fx_other_jinbi";
		$row = M($table);
		
		$user_id = I("user_id");
		$gold = I("gold");
		$diamond = I("diamond");
		$deposit = I("deposit");
		$meno = I("meno");
		
		if (!empty($user_id) && !(empty($gold) && empty($diamond) && empty($deposit))){
			if (strlen($user_id) < 5){
				$this->error('用户UID不能小于5位');
				exit;
			}
			
			if (empty($gold) && empty($diamond) && empty($deposit)){
				$this->error('金币、钻石和存款不能同时为空');
				exit;
			}
			
			if ($gold>2000000000 or $gold<-2000000000){
				$this->error('金币不能大于20亿小于-20亿');
				exit;
			}
			
			if ($diamond>2000000000 or $diamond<-2000000000){
				$this->error('钻石不能大于20亿小于-20亿');
				exit;
			}
			
			if ($deposit>2000000000 or $deposit<-2000000000){
				$this->error('存款不能大于20亿小于-20亿');
				exit;
			}
			
			$data = array();
			$data['user_id']   = $user_id ;
			$data['gold']   = $gold;
			$data['diamond']   = $diamond;
			$data['deposit']   = $deposit;
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
			$count = $row->where('type=0')->count('id');
			$Page       = new Page($count,10);//实例化分页类传入总记录数和每页显示的记录数		
			$show       = $Page->show();// 分页显示输出
			$list = $row->where('type=0')->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
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
	
	//VIP增值卡发放
	public function SVIP(){
		$table = "fx_other_jinbi";
		$row = M($table);
		$table1 = "profile_mall_lottery";
		$row1 = M($table1, '', DB_CONFIG2);
		$table2 = "user_info";
		$row2 = M($table2, '', DB_CONFIG2);
		$table4 = "profile_vip_level_configure";
		$row4 = M($table4, '', DB_CONFIG2);
		$table5 = "log_change_user_vip";
		$row5 = M($table5, '', DB_CONFIG2);
		
		$user_id = I("user_id");
		$gold = I("gold");
		$diamond = I("diamond");
		$deposit = I("deposit");
		$meno = I("meno");
		
		if (!empty($user_id) && !(empty($gold) && empty($diamond) && empty($deposit))){
			if (strlen($user_id) < 5){
				$this->error('用户UID不能小于5位');
				exit;
			}
			
			if (empty($gold) && empty($diamond) && empty($deposit)){
				$this->error('增值卡、数量和成长值不能同时为空');
				exit;
			}
			
			$data = array();
			$data['type']   = 3;
			$data['user_id']   = $user_id ;
			$data['gold']   = $gold;
			$data['diamond']   = $diamond;
			$data['deposit']   = $deposit;
			$data['meno'] =  $meno;
			$data['czz'] =  $_SESSION['username'];
			$data['addtime']   = time();

			$result = $row->add($data);
			//dump($employee->_sql()); exit;
			if($result){
				//增加操作记录
				$logs = C('JINBI_MSG_ADD_SUCCESS');
				$remark = "(VIP增值卡发放)";
				adminlog($logs,$remark);
				
				//获取用户信息
				$user = $row2->where("user_id=".$user_id)->find();
				
				//判断是发卡还是成长值
				if (!empty($diamond)){
					$res1 = $row1->where('id='.$diamond)->find();
					if (empty($gold)) $gold = 1;
					$cheng = $res1['gold'] * $gold * 100;
				}else{
					$cheng = $deposit;
				}
				$vippoint = $cheng + $user['vippoint'];
				$data1 = array();
				$data1['vippoint'] = $vippoint;
				
				//判断VIP等级是否需要修改
				$notice_service = 0;
				$res00 = $row4->field('viplevel')->where('paycount<='.$vippoint)->order('viplevel DESC')->find();
				if ($res00['viplevel'] > $user['viplevel']){
					$data1['viplevel'] = $res00['viplevel'];
					$res01 = $row5->field('viplevel')->where('user_id='.$user_id)->order('viplevel DESC')->find();
					if ($res00['viplevel'] > $res01['viplevel']) $notice_service = 1;
				}
				$result1 = $row2->where("user_id=".$user_id)->save($data1);
				//dump($row2->_sql());
				/*
				if ($notice_service == 1){
					//VIP变动通知服务器
					$url = DB_HOST."/Pay/vip.php?user_id=".$user_id."&viplevel=".$res00['viplevel'];
					$jinbi_result = curlGET($url);
					$len = strlen($jinbi_result)-3;
					$notify_status = substr($jinbi_result,$len,1);
					//echo $url."**".$jinbi_result;
					if ($notify_status == '1'){
						$jinbi = array();
						$jinbi['notify_status'] = 1;
						$jinbi['notify_date'] = time();
						$result1 = $row->where("id=".$result)->save($jinbi);
						$result2 = $row->where("id=".$result)->setInc('notify_times',1);
					}
				}else{
					$jinbi = array();
					$jinbi['notify_status'] = 2;
					$jinbi['notify_date'] = time();
					$result1 = $row->where("id=".$result)->save($jinbi);
				}*/
				//通知服务器
				$url = DB_HOST."/Pay/vip.php?user_id=".$user_id."&viplevel=".$res00['viplevel']."&vippoint=".$deposit."&lottery_id=".$diamond."&nums=".$gold;
				$jinbi_result = curlGET($url);
				$len = strlen($jinbi_result)-3;
				$notify_status = substr($jinbi_result,$len,1);
				//echo $url."**".$jinbi_result;
				if ($notify_status == '1'){
					$jinbi = array();
					$jinbi['notify_status'] = 1;
					$jinbi['notify_date'] = time();
					$result1 = $row->where("id=".$result)->save($jinbi);
					$result2 = $row->where("id=".$result)->setInc('notify_times',1);
				}
				//exit;
				$lib_display = $this->By_tpl."/SVIP";
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
			$count = $row->where('type=3')->count('id');
			$Page       = new Page($count,10);//实例化分页类传入总记录数和每页显示的记录数		
			$show       = $Page->show();// 分页显示输出
			$list = $row->where('type=3')->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
			foreach($list as $key=>$value){
				if ($list[$key]['notify_status']=="1"){
					$list[$key]['notify_status'] = "成功";
				}else if ($list[$key]['notify_status']=="2"){
					$list[$key]['notify_status'] = "等级未修改";
				}else{
					$list[$key]['notify_status'] = "失败";
				}
				
				if (!empty($value['diamond'])){
					$res1 = $row1->where('id='.$value['diamond'])->find();
					$list[$key]['vipname'] = $res1['names'];
				}else{
					$list[$key]['vipname'] = '';
				}
				

				$list[$key]['addtime'] = (!empty($list[$key]['addtime'])) ? date("Y-m-d H:i:s", $list[$key]['addtime']) : "";
				$list[$key]['notify_times'] = (!empty($list[$key]['notify_times'])) ? date("Y-m-d H:i:s", $list[$key]['notify_times']) : "";
			}
			
			$this->assign('list',$list);
			$this->assign('pageshow',$show);
			
			$lottery = $row1->where('type=4')->order('sorts')->select();
			$this->assign('lottery',$lottery);
			
			$this->assign('left_css',"41");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl."/SVIP";
			$this->display($lib_display);
		}
	}
	
	public function qq(){
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
			
			$count = $row->where('type=1 and user_id='.$user_id)->count('id');
			if ($count > 0){
				$this->error('该用户已添加');
				exit;
			}
			
			$data = array();
			$data['type']   = 1;
			$data['user_id']   = $user_id ;
			$data['gold']   = $gold;
			$data['meno'] =  $meno;
			$data['czz'] =  $_SESSION['username'];
			$data['addtime']   = time();

			$result = $row->add($data);
			//dump($row->_sql()); exit;
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
				
				$lib_display = $this->By_tpl."/qq";
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
			$count = $row->where('type=1')->count('id');
			$Page       = new Page($count,10);//实例化分页类传入总记录数和每页显示的记录数		
			$show       = $Page->show();// 分页显示输出
			$list = $row->where('type=1')->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
			foreach($list as $key=>$value){
				$list[$key]['notify_status'] = ($list[$key]['notify_status']=="1") ? "成功" : "失败";
				$list[$key]['addtime'] = (!empty($list[$key]['addtime'])) ? date("Y-m-d H:i:s", $list[$key]['addtime']) : "";
				$list[$key]['notify_times'] = (!empty($list[$key]['notify_times'])) ? date("Y-m-d H:i:s", $list[$key]['notify_times']) : "";
			}
			
			$this->assign('list',$list);
			$this->assign('pageshow',$show);
			
			$this->assign('left_css',"41");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl."/qq";
			$this->display($lib_display);
		}
	}
	
	public function tiren(){
		$table = "fx_other_jinbi";
		$row = M($table);
		
		$user_id = I("user_id");
		$meno = I("meno");
		
		if (!empty($user_id)){
			if (strlen($user_id) < 5){
				$this->error('用户UID不能小于5位');
				exit;
			}
			
			$data = array();
			$data['type']   = 2;
			$data['user_id']   = $user_id ;
			$data['gold']   = 0;
			$data['meno'] =  $meno;
			$data['czz'] =  $_SESSION['username'];
			$data['addtime']   = time();

			$result = $row->add($data);
			//dump($row->_sql()); exit;
			if($result){
				//增加操作记录
				$logs = C('JINBI_MSG_ADD_SUCCESS');
				$remark = "(踢人下线UID:".$data['user_id'].",".json_encode($data).")";
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
				
				$lib_display = $this->By_tpl."/tiren";
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
			$count = $row->where('type=2')->count('id');
			$Page       = new Page($count,10);//实例化分页类传入总记录数和每页显示的记录数		
			$show       = $Page->show();// 分页显示输出
			$list = $row->where('type=2')->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
			foreach($list as $key=>$value){
				$list[$key]['notify_status'] = ($list[$key]['notify_status']=="1") ? "成功" : "失败";
				$list[$key]['addtime'] = (!empty($list[$key]['addtime'])) ? date("Y-m-d H:i:s", $list[$key]['addtime']) : "";
				$list[$key]['notify_times'] = (!empty($list[$key]['notify_times'])) ? date("Y-m-d H:i:s", $list[$key]['notify_times']) : "";
			}
			
			$this->assign('list',$list);
			$this->assign('pageshow',$show);
			
			$this->assign('left_css',"41");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl."/tiren";
			$this->display($lib_display);
		}
	}

	//玩家详情
	public function userlist(){
		$table1 = "pay_now_config.zjh_order";
		$row1 = M($table1);
		$table = "user_info";
		$row = M($table, '', DB_CONFIG2);
		$table2 = "log_gold_change_log_".date("Ym");
		$row2 = M($table2, '', DB_CONFIG2);
		$table3 = "log_game_record_log_".date("Ym");
		$row3 = M($table3, '', DB_CONFIG2);
		
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
					$sql = "nick_name like '%".$keywords."%' or nickname like '%".$keywords."%' or user_name like '%".$keywords."%'";
				}
				if ($cate_id=="3"){
					$sql = "lost_count=0 and win_count=0";
				}
				if ($cate_id=="4"){
					$sql = "((lost_count+win_count)>=1 AND (lost_count+win_count)<=2)";
				}
                if ($cate_id=="5"){
                    $sql = "channel=".$keywords;
                }
                if ($cate_id=="6"){
                   //获取注册IP
                    $table21 = "login_log_".date("Ym");
                    $row21 = M($table21, '', DB_CONFIG2);
                    $ipnum = ipton($keywords);
                    $res21 = $row21->field('distinct user_id')->where('login_ip='.$ipnum)->select();
                    $sql = "";
                    foreach($res21 as $key21 => $val21){
                        $sql .= ($key21==0) ? $val21['user_id'] : ",".$val21['user_id'];
                    }
                    if (!empty($sql)) $sql = " user_id in ($sql)";
                }
				if (!empty($keywords) && $cate_id=="7"){
					$sql = "phone_number like '%".$keywords."%'";
				}
				if (!empty($keywords) && $cate_id=="8"){
					$sql = "imei like '%".$keywords."%'";
				}
				if (!empty($keywords) && $cate_id=="9"){
					$sql = "imsi like '%".$keywords."%'";
				}
				$this->assign('cate_id',$cate_id);
				$this->assign('keywords',$keywords);
				
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
                $todaytime = strtotime(date("Y-m-d"));
                $nexttime = $todaytime + 60 * 60 * 24;
				$count = $row->where($sql)->count('user_id');
				$Page       = new Page($count,20);//实例化分页类传入总记录数和每页显示的记录数		
				$show       = $Page->show();// 分页显示输出
				$list = $row->where($sql)->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();
				foreach($list as $key=>$value){
					$list[$key]['id'] = $key + 1;
					$list[$key]['nick_name'] = !empty($list[$key]['nick_name']) ? $list[$key]['nick_name'] : $list[$key]['user_name'];
                    //获取今日充值
                    $user_pay = $row1->where("user_id=".$value['user_id']." and (order_create_time>=$todaytime and order_create_time<$nexttime)")->sum('result_money');
                    $list[$key]['today_pay'] = empty($user_pay) ? 0 : $user_pay / 100;
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
					$order .= "viplevel";
				}else if ($sortscate=="4"){
					$order .= "register_date";
				}else if ($sortscate=="5"){
					$order .= "last_login_date";
				}else if ($sortscate=="6"){
                    $order .= "car";
                }else if ($sortscate=="7"){
                    $order .= "villa";
                }else if ($sortscate=="8"){
                    $order .= "yacht";
                }else{
					$order .= "user_id";
				}
				if ($sortsflag=="1"){
					$order .= " ASC";
				}else{
					$order .= " DESC";
				}
                $todaytime = strtotime(date("Y-m-d"));
                $nexttime = $todaytime + 60 * 60 * 24;
				$sql1 = "!((user_id>=".ROBERT1_BEGIN." and user_id<".ROBERT1_END.") or (user_id>=".ROBERT2_BEGIN." and user_id<=".ROBERT2_END."))";
				$count = $row->where($sql1)->count('user_id');
				$Page       = new Page($count,20);//实例化分页类传入总记录数和每页显示的记录数		
				$show       = $Page->show();// 分页显示输出
				$list = $row->where($sql1)->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();
				//dump($row->_sql());
				foreach($list as $key=>$value){
					$list[$key]['id'] = $key + 1;
					$list[$key]['nickname'] = !empty($list[$key]['nickname']) ? $list[$key]['nickname'] : $list[$key]['nick_name'];
                    //获取今日充值
                    $user_pay = $row1->where("user_id=".$value['user_id']." and (order_create_time>=$todaytime and order_create_time<$nexttime)")->sum('result_money');
                    $list[$key]['today_pay'] = empty($user_pay) ? 0 : $user_pay / 100;
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
			$info['vipovertime'] = !empty($info['vipovertime']) ? date("Y-m-d H:i:s", $info['vipovertime']) : "";
			//获取IP
			$table9 = "login_log_".date("Ym");
			$row9 = M($table9, '', DB_CONFIG2);
			$res9 = $row9->field("login_ip,channel")->where("user_id=".$id)->order("log_id DESC")->find();
			$info['ip1'] = ntoip($res9['login_ip']);
			$info['last_channel'] = $res9['channel'];
			//echo $$info['last_channel']."**"; exit;
			
			import('ORG.Net.IpLocation');// 导入IpLocation类
			$Ip = new IpLocation('UTFWry.dat'); // 实例化类 参数表示IP地址库文件
			if (!empty($info['ip1'])){
				$area1 = $Ip->getlocation($info['ip1']);
				//print_r($area1);
				$info['area1'] = "(".iconv("gbk","utf-8",$area1['country'].$area1['area']).")";
			}else{
				$info['area1'] = "";
			}
			
			$info['nickname'] = !empty($info['nickname']) ? $info['nickname'] : $info['nick_name'];
			
			
			$res9 = $row9->field("login_ip")->where("user_id=".$id)->order("log_id DESC")->find();
			$info['ip2'] = ntoip($res9['login_ip']);
			if (!empty($info['ip2'])){
				$area2 = $Ip->getlocation($info['ip2']);
				//print_r($area1);
				$info['area2'] = "(".iconv("gbk","utf-8",$area2['country'].$area2['area']).")";
			}else{
				$info['area2'] = "";
			}
			
			//获取最新的游戏版本
			$row11 = M("fx_user_version");
			$res11 = $row11->where("user_id=".$id)->order("version_new DESC")->find();
			$info['gameversion'] = (!empty($res11['version_new']) && $res11['version_new']>$info['gameversion']) ? $res11['version_new'] : $info['gameversion'];
			
			$pai_sum = $info['win_count'] + $info['lost_count'];
			$info['lv'] = (!empty($pai_sum)) ? round($info['win_count']/$pai_sum,3)*100 : 0;
			//dump($row->_sql());
			$this->assign('info',$info);
			
			//获取用户20局内活动详情
			
			
			
			$lib_display = $this->By_tpl."/userinfo";
		}elseif ($act=="jinbi"){
			$module = I("module");
			$sql11 = "";
			if (!empty($module)) $sql11 .= " and module=".$module;
			
			$date11 = $_GET['date11'];
			if (empty($date11)) $date11 = date("Y-m-01");
			$this->assign('date11',$date11);
			$time11 = strtotime($date11);
			$table2 = "log_gold_change_log_".date("Ym", $time11);
			$row2 = M($table2, '', DB_CONFIG2);
			
			$count = $row2->where("user_id=".$id.$sql11)->count('user_id');
			$Page       = new Page($count,20);	
			$show       = $Page->show();
			$info = $row2->where("user_id=".$id.$sql11)->order("curtime DESC")->limit($Page->firstRow.','.$Page->listRows)->select();
			$this->assign('info',$info);
			$this->assign('id',$id);
			$this->assign('pageshow',$show);
			$lib_display = $this->By_tpl."/userjinbi";
		}elseif ($act=="login"){

			$sql11 = "";
			
			$date11 = $_GET['date11'];
			if (empty($date11)) $date11 = date("Y-m-01");
			$this->assign('date11',$date11);
			$time11 = strtotime($date11);
			$table2 = "login_log_".date("Ym", $time11);
			$row2 = M($table2, '', DB_CONFIG2);
			
			$count = $row2->where("user_id=".$id.$sql11)->count('user_id');
			$Page       = new Page($count,20);	
			$show       = $Page->show();
			$info = $row2->where("user_id=".$id.$sql11)->order("login_date DESC")->limit($Page->firstRow.','.$Page->listRows)->select();
			foreach($info as $key => $val){
				$info[$key]['ip'] = ntoip($val['login_ip']);
				if ($val['type'] == "1"){
					$info[$key]['type'] = "登录";
				}else{
					$info[$key]['type'] = "注册";
				}
			}
			$this->assign('info',$info);
			$this->assign('id',$id);
			$this->assign('pageshow',$show);
			$lib_display = $this->By_tpl."/userlogin";
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
			$Page       = new Page($count,20);	
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
		}elseif ($act=="trans"){
			
			$table8 = "user_transfer_gold_record";
			$row8 = M($table8, '', DB_CONFIG2);
			
			
			$transtype = I("transtype");
			if ($transtype=="1") $sql11 = " and touserid=0"; else if ($transtype=="2") $sql11 = " and fromuserid=0"; else $sql11 = "";
			
			$count = $row8->where("userid=".$id.$sql11)->count('userid');
			$Page       = new Page($count,20);	
			$show       = $Page->show();
			$info = $row8->where("userid=".$id.$sql11)->order("operatortime DESC")->limit($Page->firstRow.','.$Page->listRows)->select();
			foreach ($info as $key => $val){
				$info[$key]['operatortime'] =  date("Y-m-d H:i:s", $val['operatortime']);
				$info[$key]['transfergold'] = number_format($val['transfergold']);
				
				$userinfo = $row->field('nick_name,nickname')->where("user_id=".$val['userid'])->find();
				$info[$key]['nickname'] = (!empty($userinfo['nickname'])) ? $userinfo['nickname'] : $userinfo['nick_name'];
				
				if ($val['touserid']==0) {
					$info[$key]['type'] = '转入';
					$info[$key]['otheruid'] = $val['fromuserid'];
					$userinfo = $row->field('nick_name,nickname')->where("user_id=".$val['fromuserid'])->find();
					$info[$key]['othernickname'] = (!empty($userinfo['nickname'])) ? $userinfo['nickname'] : $userinfo['nick_name'];
				}
				
				if ($val['fromuserid']==0) {
					$info[$key]['type'] = '<font color="#FF0000">转出</font>'; 
					
					$info[$key]['transfergold'] = '<font color="#FF0000">-'.$info[$key]['transfergold'].'</font>'; 
					$info[$key]['otheruid'] = $val['touserid'];
					$userinfo = $row->field('nick_name,nickname')->where("user_id=".$val['touserid'])->find();
					$info[$key]['othernickname'] = (!empty($userinfo['nickname'])) ? $userinfo['nickname'] : $userinfo['nick_name'];
				}
			}
			$this->assign('info',$info);
			$this->assign('id',$id);
			$this->assign('pageshow',$show);
			$lib_display = $this->By_tpl."/usertrans";
		}elseif ($act=="gift"){
			
			$giftdate = I("giftdate");
			if (empty($giftdate)) $giftdate = date("Y-m-d");
			$this->assign('giftdate',$giftdate);
			
			$sql11 = "";
			$time11 = strtotime($giftdate);
			$time12 = $time11 + 86400;
			if ($giftdate < date("Y-m-d")){
				if ($giftdate < "2016-01-01"){
					$table9 = "log_gift_2015";
				}else{
					$table9 = "log_gift_".date("Ym", $time11);
				}
				$row9 = M($table9, '', DB_CONFIG3);
			}else{
				$table9 = "log_gift_record_log";
				$row9 = M($table9, '', DB_CONFIG2);
				$sql11 .= " and (operatortime>=$time11 AND operatortime<$time12)";
			}
			
			
			
			$transtype = I("transtype");
			if ($transtype=="1") $sql11 .= " and from_userid=0"; else if ($transtype=="2") $sql11 .= " and from_userid=1"; else if ($transtype=="3") $sql11 .= " and from_userid!=$id"; else if ($transtype=="4") $sql11 .= " and from_userid=$id"; 
			
			$count = $row9->where("(user_id=".$id." or from_userid=".$id.")".$sql11)->count('user_id');
			//dump( $row9->_sql());
			$Page       = new Page($count,20);	
			$show       = $Page->show();
			$info = $row9->where("(user_id=".$id." or from_userid=".$id.")".$sql11)->order("operatortime DESC")->limit($Page->firstRow.','.$Page->listRows)->select();
			foreach ($info as $key => $val){
				//$info[$key]['operatortime'] =  date("Y-m-d H:i:s", $val['operatortime']);
				
				$userinfo = $row->field('nick_name,nickname')->where("user_id=".$val['user_id'])->find();
				$info[$key]['nickname'] = (!empty($userinfo['nickname'])) ? $userinfo['nickname'] : $userinfo['nick_name'];
				
				if ($val['from_userid']==0) {
					$info[$key]['type'] = '卖礼物'; 

					$info[$key]['otheruid'] = '';
					$info[$key]['othernickname'] = '';
				}else if ($val['from_userid']==1) {
					$info[$key]['type'] = '大转盘抽奖'; 
					
					$info[$key]['otheruid'] = '';
					$info[$key]['othernickname'] = '';
				}else{
					if ($val['from_userid'] == $id){
						$info[$key]['type'] = '转出礼物'; 
					}else{
						$info[$key]['type'] = '转入礼物'; 
					}
					
					$info[$key]['otheruid'] = $val['from_userid'];
					$userinfo = $row->field('nick_name,nickname')->where("user_id=".$val['from_userid'])->find();
					$info[$key]['othernickname'] = (!empty($userinfo['nickname'])) ? $userinfo['nickname'] : $userinfo['nick_name'];
				}
				
				switch($val['giftid']){
					case 1: $info[$key]['gift'] = "鲜花"; break;
					case 2: $info[$key]['gift'] = "鸡蛋"; break;
					case 3: $info[$key]['gift'] = "车"; break;
					case 4: $info[$key]['gift'] = "房"; break;
					case 5: $info[$key]['gift'] = "飞机"; break;
					default: $info[$key]['gift'] = ""; break;
				}
			}
			$this->assign('info',$info);
			$this->assign('id',$id);
			$this->assign('pageshow',$show);
			$lib_display = $this->By_tpl."/usergift";
		}else{
			$lib_display = $this->By_tpl."/userlist";
		}
		
		$this->display($lib_display);
	}
	
	
	
	//玩家基本信息
	public function userbase(){
		$table = "user_info";
		$row = M($table, '', DB_CONFIG2);
		$table1 = "zjh_order";
		$row1 = M($table1);
		$table2 = "log_gold_change_log_".date("Ym");
		$row2 = M($table2, '', DB_CONFIG2);
		
		
		$cate_id = I("cate_id");
		$keywords = I("keywords");
		$act = I("act");
		$id = I("id");
		$beginTime = I("beginTime");
		$endTime = I("endTime");
		$sortscate = I("sortscate");
		$sortsflag = I("sortsflag");
		import('ORG.Util.Page');
		
		if (strtotime($beginTime)>strtotime($endTime)){
			$this->error('开始日期不能大于结束日期');
			exit;
		}
		
		//echo $act."**";
		if (!empty($beginTime) && !empty($endTime)){
			$date11 = $beginTime;
			$date12 = $endTime;
		}else{
			$date11 = date("Y-m-d",strtotime("-3 day"));
			$date12 = date("Y-m-d");
		}
		$time11 = strtotime($date11);
		$time12 = strtotime($date12) + 60 * 60 * 24;
		$day_jian = (strtotime($date12) - strtotime($date11)) / (60 * 60 * 24) + 1;
		$this->assign('beginTime',$date11);
		$this->assign('endTime',  $date12);
		$this->assign('sortscate',$sortscate);
		$this->assign('sortsflag',$sortsflag);
		
		$sql1 = "!((user_id>=".ROBERT1_BEGIN." and user_id<".ROBERT1_END.") or (user_id>=".ROBERT2_BEGIN." and user_id<=".ROBERT2_END."))";
		$count = $row->where($sql1." and register_date>='$date11' and register_date<'$date12'")->count('user_id');
		$Page       = new Page($count,PAGE_SHOW);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		$list = $row->where($sql1." and register_date>='$date11' and register_date<'$date12'")->order("register_date DESC")->select();
		//dump($row->_sql());
		$sort1 = array();
		$sort2 = array();
		$sort3 = array();
		$sort4 = array();
		$sort5 = array();
		$sort6 = array();
		$sum = array(0,0,0);
		foreach($list as $key=>$value){
			$list[$key]['id'] = $Page->firstRow + 1 + $key;
			$list[$key]['sign'] = empty($list[$key]['sign']) ? "" : $list[$key]['sign'];
			//充值金额
			//$list[$key]['chongzhi'] = $row1->where("user_id='".$value['user_id']."' and payment_status in ('1','-2') and (order_create_time>=".$time11." and order_create_time<".$time12.")")->sum('result_money');
			$list[$key]['chongzhi'] = $value['total_pay_num'] / 100;
			//总局数
			$list[$key]['gamenum'] = 0;
			for($t=1; $t<=$day_jian; $t++){
				
				$time1 = strtotime($date11) + 60 * 60 * 24 * ($t - 1);
				$time2 = $time1 + 60 * 60 * 24;
				$date1 = date("Y-m-d", $time1);
				$date2 = date("Y-m-d", $time2);
				$table11 = "log_game_record_log_".date("Ym", $time1);
				$row11 = M($table11);
				$list[$key]['gamenum'] += $row11->where("curtime>='$time1' and curtime<'$time2' and user_id='".$value['user_id']."'")->count('id');
			}
			
			$sum[0] += $value['gold'];
			$sum[1] += $list[$key]['gamenum'];
			$sum[2] += $list[$key]['chongzhi'];
			
			$sort1[$key] = $list[$key]['register_date'];
			$sort2[$key] = $list[$key]['gold'];
			$sort3[$key] = $list[$key]['sign'];
			$sort4[$key] = $list[$key]['contact'];
			$sort5[$key] = $list[$key]['gamenum'];
			$sort6[$key] = $list[$key]['chongzhi'];
		}
		
		if ($sortscate=="1"){
			if ($sortsflag=="1"){
				array_multisort($sort1, SORT_ASC,  $list);
			}else{
				array_multisort($sort1, SORT_DESC, $list);
			}
		}
		if ($sortscate=="2"){
			if ($sortsflag=="1"){
				array_multisort($sort2, SORT_ASC,  $list);
			}else{
				array_multisort($sort2, SORT_DESC, $list);
			}
		}
		if ($sortscate=="3"){
			if ($sortsflag=="1"){
				array_multisort($sort3, SORT_ASC,  $list);
			}else{
				array_multisort($sort3, SORT_DESC, $list);
			}
		}
		if ($sortscate=="4"){
			if ($sortsflag=="1"){
				array_multisort($sort4, SORT_ASC,  $list);
			}else{
				array_multisort($sort4, SORT_DESC, $list);
			}
		}
		if ($sortscate=="5"){
			if ($sortsflag=="1"){
				array_multisort($sort5, SORT_ASC,  $list);
			}else{
				array_multisort($sort5, SORT_DESC, $list);
			}
		}
		if ($sortscate=="6"){
			if ($sortsflag=="1"){
				array_multisort($sort6, SORT_ASC,  $list);
			}else{
				array_multisort($sort6, SORT_DESC, $list);
			}
		}
		
		$showlist = array();
		for($i=$Page->firstRow; $i<$Page->listRows; $i++){
			$showlist[$i] = $list[$i];
		}
		
		$this->assign('sum',$sum);		
		$this->assign('list',$showlist);
		$this->assign('pageshow',$show);
		
		//echo "222";
		
		$this->assign('left_css',"41");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/userbase";
		$this->display($lib_display);
	}
	
	//玩家基本信息
	public function userbaseexcel(){
		set_time_limit(0);
		$table = "user_info";
		$row = M($table);
		$table1 = "zjh_order";
		$row1 = M($table1);
		$table2 = "log_gold_change_log_".date("Ym");
		$row2 = M($table2, '', DB_CONFIG2);
		
		
		$cate_id = I("cate_id");
		$keywords = I("keywords");
		$act = I("act");
		$id = I("id");
		$beginTime = I("beginTime");
		$endTime = I("endTime");
		$sortscate = I("sortscate");
		$sortsflag = I("sortsflag");
		import('ORG.Util.Page');
		
		if (strtotime($beginTime)>strtotime($endTime)){
			$this->error('开始日期不能大于结束日期');
			exit;
		}
		
		//echo $act."**";
		if (!empty($beginTime) && !empty($endTime)){
			$date11 = $beginTime;
			$date12 = $endTime;
		}else{
			$date11 = date("Y-m-d",strtotime("-1 month"));
			$date12 = date("Y-m-d");
		}
		$time11 = strtotime($date11);
		$time12 = strtotime($date12) + 60 * 60 * 24;
		$day_jian = (strtotime($date12) - strtotime($date11)) / (60 * 60 * 24) + 1;
		$this->assign('beginTime',$date11);
		$this->assign('endTime',  $date12);
		$this->assign('sortscate',$sortscate);
		$this->assign('sortsflag',$sortsflag);
		
		$sql1 = "!((user_id>=".ROBERT1_BEGIN." and user_id<".ROBERT1_END.") or (user_id>=".ROBERT2_BEGIN." and user_id<=".ROBERT2_END."))";
		$count = $row->where($sql1." and register_date>='$date11' and register_date<'$date12'")->count('user_id');
		$Page       = new Page($count,PAGE_SHOW);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		$list = $row->where($sql1." and register_date>='$date11' and register_date<'$date12'")->order("register_date DESC")->select();
		//dump($row->_sql());
		$sort1 = array();
		$sort2 = array();
		$sort3 = array();
		$sort4 = array();
		$sort5 = array();
		$sort6 = array();
		foreach($list as $key=>$value){
			$list[$key]['id'] = $Page->firstRow + 1 + $key;
			$list[$key]['sign'] = empty($list[$key]['sign']) ? "" : $list[$key]['sign'];
			//充值金额
			//$list[$key]['chongzhi'] = $row1->where("user_id='".$value['user_id']."' and payment_status in ('1','-2') and (order_create_time>=".$time11." and order_create_time<".$time12.")")->sum('result_money');
			$list[$key]['chongzhi'] = $value['total_pay_num'] / 100;
			//总局数
			$list[$key]['gamenum'] = 0;
			for($t=1; $t<=$day_jian; $t++){
				
				$time1 = strtotime($date11) + 60 * 60 * 24 * ($t - 1);
				$time2 = $time1 + 60 * 60 * 24;
				$date1 = date("Y-m-d", $time1);
				$date2 = date("Y-m-d", $time2);
				$table11 = "log_game_record_log_".date("Ym", $time1);
				$row11 = M($table11);
				$list[$key]['gamenum'] += $row11->where("curtime>='$time1' and curtime<'$time2' and user_id='".$value['user_id']."'")->count('id');
			}
			$sort1[$key] = $list[$key]['register_date'];
			$sort2[$key] = $list[$key]['gold'];
			$sort3[$key] = $list[$key]['sign'];
			$sort4[$key] = $list[$key]['contact'];
			$sort5[$key] = $list[$key]['gamenum'];
			$sort6[$key] = $list[$key]['chongzhi'];
		}
		
		if ($sortscate=="1"){
			if ($sortsflag=="1"){
				array_multisort($sort1, SORT_ASC,  $list);
			}else{
				array_multisort($sort1, SORT_DESC, $list);
			}
		}
		if ($sortscate=="2"){
			if ($sortsflag=="1"){
				array_multisort($sort2, SORT_ASC,  $list);
			}else{
				array_multisort($sort2, SORT_DESC, $list);
			}
		}
		if ($sortscate=="3"){
			if ($sortsflag=="1"){
				array_multisort($sort3, SORT_ASC,  $list);
			}else{
				array_multisort($sort3, SORT_DESC, $list);
			}
		}
		if ($sortscate=="4"){
			if ($sortsflag=="1"){
				array_multisort($sort4, SORT_ASC,  $list);
			}else{
				array_multisort($sort4, SORT_DESC, $list);
			}
		}
		if ($sortscate=="5"){
			if ($sortsflag=="1"){
				array_multisort($sort5, SORT_ASC,  $list);
			}else{
				array_multisort($sort5, SORT_DESC, $list);
			}
		}
		if ($sortscate=="6"){
			if ($sortsflag=="1"){
				array_multisort($sort6, SORT_ASC,  $list);
			}else{
				array_multisort($sort6, SORT_DESC, $list);
			}
		}
		
		if ($act == "exceldo"){
			$xlsName  = "玩家基本信息";
			$xlsCell  = array(
			array('id','ID'),
			array('user_id','用户UID'),
			array('register_date','注册时间'),
			array('gold','金币'),
			array('gamenum','总局数'),
			array('chongzhi','充值金额'),
			array('sign','签名'),
			array('contact','联系方式')   
			);
			$xlsData = array();
			foreach ($list as $k => $v)
			{
				$xlsData[$k]['id'] = $v['id'];
				$xlsData[$k]['user_id'] = $v['user_id'];
				$xlsData[$k]['register_date'] = $v['register_date'];
				$xlsData[$k]['gold'] = $v['gold'];
				$xlsData[$k]['gamenum'] = $v['gamenum'];
				$xlsData[$k]['chongzhi'] = $v['chongzhi'];
				$xlsData[$k]['sign'] = $v['sign'];
				$xlsData[$k]['contact'] = $v['contact'];
			}
			exportExcel($xlsName,$xlsCell,$xlsData);
			exit;
		}
				
		$this->assign('list',$list);
		$this->assign('pageshow',$show);
		
		//echo "222";
		
		$this->assign('left_css',"41");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/userbase";
		$this->display($lib_display);
	}
	
	//头像审核
	public function touxiang(){
		$table = "user_info";
		$row = M($table, '', DB_CONFIG2);
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
					$url = DB_HOST."/Pay/touxiang.php?user_id=".$val;
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
		$count = 1000;
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
		$user_id = I("user_id");
		$sql1= "";
		if (!empty($user_id)) $sql1 .= " and user_id=".$user_id;
		$table = "duanxin";
		$row = M($table);
		import('ORG.Util.Page');
		$count = $row->where("1".$sql1)->count('id');
		$Page       = new Page($count,PAGE_SHOW);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		$list = $row->where("1".$sql1)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
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
	
	//用户聊天统计
	public function sperk(){
		
		$user_id = I("user_id");
		$type = I("type");
		$roomid = I("roomid");
		$tableid = I("tableid");
		$uid = I("uid");
		//if (empty($type)) $type = 4;
		$sql0 = "1";
		if (!empty($user_id)) $sql0 .= " and uid=".$user_id;
		if (!empty($type)) $sql0 .=  ($type==1) ? " and (type=1 or type=2)" : " and type=".$type;
		if (!empty($roomid)) $sql0 .= " and roomid=".$roomid;
		$this->assign('user_id',$user_id);
		$this->assign('type',$type);
		$this->assign('roomid',$roomid);
		//$this->assign('tableid',$tableid);
		
		$table = "sperk_log.log".date("Ym");
		$row = M($table);
		$table2 = "user_info";
		$row2 = M($table2, '', DB_CONFIG2);
		import('ORG.Util.Page');
		//echo $type; exit;
		if ($type==3){
			
			
			if (!empty($tableid)){
				
				$sql0 .= " and tableno=".$tableid;
				$count = $row->where($sql0)->count('id');
				$Page       = new Page($count,20);//实例化分页类传入总记录数和每页显示的记录数		
				$show       = $Page->show();// 分页显示输出
				$key1 = 0;
				$sort1 = array();
				$showlist = $row->where($sql0)->order('addtime')->limit($Page->firstRow.','.$Page->listRows)->select();
				foreach($showlist as $key=>$value){
					
					$user = $row2->where('user_id='.$value['uid'])->find();
					$nickname = (!empty($user['nickname'])) ? $user['nickname'] : $user['nick_name'];

					$showlist[$key]['nickname'] = $nickname;
					$showlist[$key]['tableid'] = $value['tableno'];
					$showlist[$key]['addtime'] = (!empty($value['addtime'])) ? date("Y-m-d H:i:s", $value['addtime']) : "";
					$showlist[$key]['errmessage'] = $value['message'];
				}
				$lib_display = $this->By_tpl."/sperkroom";
				
			}else{
				
				//$arr = $row->field('tableno')->where($sql0)->group('tableno')->select();
				//$count = count($arr);
				//$Page       = new Page($count,20);//实例化分页类传入总记录数和每页显示的记录数		
				//$show       = $Page->show();// 分页显示输出
				$key1 = 0;
				$sort1 = array();
				$list = $row->field('tableno')->where($sql0)->group('tableno')->select();
				foreach($list as $key=>$value){
					
					$info = $row->where($sql0.' and tableno='.$value['tableno'])->order('addtime desc')->find();
					
					$user = $row2->where('user_id='.$info['uid'])->find();
					$nickname = (!empty($user['nickname'])) ? $user['nickname'] : $user['nick_name'];
					
					$list[$key]['id'] = $info['id'];
					$list[$key]['uid'] = $info['uid'];
					$list[$key]['nickname'] = $nickname;
					$list[$key]['tableid'] = $info['tableno'];
					$list[$key]['ver'] = $info['ver'];
					$list[$key]['addtime'] = (!empty($info['addtime'])) ? date("Y-m-d H:i:s", $info['addtime']) : "";
					$list[$key]['errmessage'] = $info['message'];
					
					$sort1[] = $info['addtime'];
					
					$key1++;
				}
				$lib_display = $this->By_tpl."/sperkroom";
				
				//print_r($list);
				
				array_multisort($sort1, SORT_DESC, $list);	
				
				$pagenum = 20;
				$count = $key1;
				$Page       = new Page($count,$pagenum);//实例化分页类传入总记录数和每页显示的记录数		
				$show       = $Page->show();// 分页显示输出
				$showlist = array();
				$p = I("p");
				if (empty($p)) $p = 1;
				$i = ($p - 1) * $pagenum;
				$maxi = $i + $pagenum;
				if ($maxi > $count) $maxi = $count;
				for($i; $i<$maxi; $i++){
					$showlist[$i] = $list[$i];
				}
			}
			
			/*
			$count = $row->where($sql0)->count('id');
			$Page       = new Page($count,20);//实例化分页类传入总记录数和每页显示的记录数		
			$show       = $Page->show();// 分页显示输出
			$list = $row->where($sql0)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();

			$listnow = array();
			$room = array();
			$key1 = 0;
			foreach($list as $key=>$value){

				//$temp = explode(":", $value['errmessage']);
				$tempid  = $value['tableno'];
				
				if (!empty($tableid)){
					if ($tableid == $tempid){
						$room[$key1] = $value['addtime'];
						$user = $row2->where('user_id='.$value['uid'])->find();
						//dump($row2->_sql());
						$nickname = (!empty($user['nickname'])) ? $user['nickname'] : $user['nick_name'];
						
						$listnow[$key1] = array('id' => $value['id'],
												'uid' => $value['uid'],
												'nickname' => $nickname,
												'tableid' => $tempid,
												'ver' => $value['ver'],
												'addtime' => (!empty($value['addtime'])) ? date("Y-m-d H:i:s", $value['addtime']) : "",
												'errmessage' => $value['message']);
						$key1++;	
					}
					$lib_display = $this->By_tpl."/sperktable";
				}else{
					if (!in_array($tempid, $room)){
						$room[] = $tempid;
						$user = $row2->where('user_id='.$value['uid'])->find();
						$nickname = (!empty($user['nickname'])) ? $user['nickname'] : $user['nick_name'];
						$nickname = "";
						
						$listnow[$key1] = array('id' => $value['id'],
												'uid' => $value['uid'],
												'nickname' => $nickname,
												'tableid' => $tempid,
												'ver' => $value['ver'],
												'addtime' => (!empty($value['addtime'])) ? date("Y-m-d H:i:s", $value['addtime']) : "",
												'errmessage' => $value['message']);
						$key1++;					 
					}
					$lib_display = $this->By_tpl."/sperkroom";
				}
				
				
			}
			
			if (!empty($tableid)){
				array_multisort($room, SORT_ASC, $listnow);	
			}
			
			
			$pagenum = 20;
			$count = $key1;
			$Page       = new Page($count,$pagenum);//实例化分页类传入总记录数和每页显示的记录数		
			$show       = $Page->show();// 分页显示输出
			$showlist = array();
			$p = I("p");
			if (empty($p)) $p = 1;
			$i = ($p - 1) * $pagenum;
			$maxi = $i + $pagenum;
			if ($maxi > $count) $maxi = $count;
			for($i; $i<$maxi; $i++){
				$showlist[$i] = $listnow[$i];
			}
			*/
			
			$this->assign('list',$showlist);
			
		}else{
			
			if (empty($type) or $type == 1){
				$count = $row->where($sql0)->count('id');
				$Page       = new Page($count,20);//实例化分页类传入总记录数和每页显示的记录数		
				$show       = $Page->show();// 分页显示输出
				$list = $row->where($sql0)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
				foreach($list as $key=>$value){
					if ($type == 1){
						$user1 = $row2->where('user_id='.$value['uid'])->find();
						$nickname = (!empty($user1['nickname'])) ? $user1['nickname'] : $user1['nick_name'];
						$list[$key]['uid'] = $nickname." (".$value['uid'].")";
					}
					$list[$key]['addtime'] = (!empty($value['addtime'])) ? date("Y-m-d H:i:s", $value['addtime']) : "";
				}
				$this->assign('list',$list);
				$lib_display = $this->By_tpl."/sperk";
			}else{
				
				if (empty($uid)){
					$user = $row->field('uid')->where($sql0)->group('uid')->select();
					$count = count($user);
					$Page       = new Page($count,20);//实例化分页类传入总记录数和每页显示的记录数		
					$show       = $Page->show();// 分页显示输出
					$list = $row->field('uid')->where($sql0)->group('uid')->select();
					$sort1 = array();
					foreach($list as $key=>$value){
						if ($type == 4){
							$info = $row->where('type='.$type.' and uid='.$value['uid'])->order('id desc')->find();
							//dump($row->_sql());
							
							$temp = explode(":", $info['errmessage']);
							$tempid  = $temp[0];
							if ($tempid == "1"){
								$list[$key]['errmessage'] = "在线客服：".$temp[1];
							}else{
								$user2 = $row2->where('user_id='.$tempid)->find();
								$nickname = (!empty($user2['nickname'])) ? $user2['nickname'] : $user2['nick_name'];
								$list[$key]['errmessage'] = $nickname." (".$tempid.")：".$temp[1];
							} 
							
							
							$user1 = $row2->where('user_id='.$info['uid'])->find();
							$nickname = (!empty($user1['nickname'])) ? $user1['nickname'] : $user1['nick_name'];
							$list[$key]['showuid'] = $nickname." (".$value['uid'].")";
							
							$list[$key]['id'] = $info['id'];
							$list[$key]['uid'] = $info['uid'];
							$list[$key]['ver'] = $info['ver'];
							$list[$key]['addtime'] = (!empty($info['addtime'])) ? date("Y-m-d H:i:s", $info['addtime']) : "";
							$sort1[$key] = $info['addtime'];
						}
						
					}
					array_multisort($sort1, SORT_DESC, $list);	
					
					$pagenum = 20;
					$showlist = array();
					$p = I("p");
					if (empty($p)) $p = 1;
					$i = ($p - 1) * $pagenum;
					$maxi = $i + $pagenum;
					if ($maxi > $count) $maxi = $count;
					for($i; $i<$maxi; $i++){
						$showlist[$i] = $list[$i];
					}
					$this->assign('list',$showlist);
					
					$lib_display = $this->By_tpl."/sperkfriend";
				}else{
					$count = $row->where('type='.$type.' and uid='.$uid)->count('id');
					$Page       = new Page($count,20);//实例化分页类传入总记录数和每页显示的记录数		
					$show       = $Page->show();// 分页显示输出
					$list = $row->where('type='.$type.' and uid='.$uid)->order('id')->limit($Page->firstRow.','.$Page->listRows)->select();
					foreach($list as $key=>$value){
						if ($type == 4){
							$temp = explode(":", $value['errmessage']);
							$tempid  = $temp[0];
							if ($tempid == "1"){
								$list[$key]['errmessage'] = "在线客服：".$temp[1];
							}else{
								$user2 = $row2->where('user_id='.$tempid)->find();
								$nickname = (!empty($user2['nickname'])) ? $user2['nickname'] : $user2['nick_name'];
								$list[$key]['errmessage'] = $nickname." (".$tempid.")：".$temp[1];
							}
							
							$user1 = $row2->where('user_id='.$value['uid'])->find();
							$nickname = (!empty($user1['nickname'])) ? $user1['nickname'] : $user1['nick_name'];
							$list[$key]['uid'] = $nickname." (".$value['uid'].")";
						}
						$list[$key]['addtime'] = (!empty($value['addtime'])) ? date("Y-m-d H:i:s", $value['addtime']) : "";
					}
					$lib_display = $this->By_tpl."/sperk";
					$this->assign('list',$list);
				}
				
				
			}
			
			
			
			
		}
			
		
		$this->assign('pageshow',$show);
			
		$this->assign('left_css',"41");
		$this->assign('By_tpl',$this->By_tpl);
		
		$this->display($lib_display);
	}
	
	//在线用户聊天统计
	public function Usersperk(){
		
		$user_id = I("user_id");
		$sql0 = "pant_id=0";
		if (!empty($user_id)) $sql0 .= " and uid=".$user_id;
		$this->assign('user_id',$user_id);
		
		$table = "user_sperk";
		$row = M($table);
		
		$act = I("act");
		if ($act == "recalldo"){
			
			$recall = I("recall");
			$pant_id = I("id");
			
			$count = $row->where("pant_id=".$pant_id)->count('id');
			if ($count == 0){
				$info = $row->where("id=".$pant_id)->find();
				
				$data = array();
				$data['uid'] = $info['uid'];
				$data['message'] = $recall;
				$data['pant_id'] = $pant_id;
				$data['ver'] = $info['ver'];
				$data['addtime'] = time();
				$result = $row->add($data);
			}else{
				$data = array();
				$data['uid'] = $_SESSION['userid'];
				$data['message'] = $recall;
				$data['addtime'] = time();
				$result = $row->where("pant_id=".$pant_id)->save($data);
			}
			
			if($result){
				echo "1";
				exit;
			}else{
				echo "0";
				exit;
			}
		}
		
		import('ORG.Util.Page');
		$res = $row->field('uid')->where($sql0)->group('uid')->select();
		$count = count($res);
		$pagenum = 20;
		$Page       = new Page($count,$pagenum);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		$list = $row->field('uid')->where($sql0)->group('uid')->select();
		//dump($row->_sql());
		$sort1 = array();
		foreach($list as $key=>$value){
			
			$info = $row->where('pant_id=0 and uid='.$value['uid'])->order('addtime desc')->find();
			//dump($row->_sql());
			$list[$key]['addtime'] = (!empty($info['addtime'])) ? date("Y-m-d H:i:s", $info['addtime']) : "";
			$list[$key]['ver'] = $info['ver'];
			$list[$key]['id'] = $info['id'];
			$list[$key]['message'] = urldecode($info['message']);
			
			$sort1[$key] = $list[$key]['addtime'];
			
			$info = $row->where('pant_id!=0 and uid='.$value['uid'])->order('addtime desc')->find();
			//dump($row->_sql());
			$recall = (!empty($info['message'])) ? '【最新回复】'.$info['message'].'【'.date("Y-m-d H:i:s", $info['addtime']).'】' : "-";
			$list[$key]['recall'] = $recall;
		}
		array_multisort($sort1, SORT_DESC, $list);	
		
		$showlist = array();
		$p = I("p");
		if (empty($p)) $p = 1;
		$i = ($p - 1) * $pagenum;
		$maxi = $i + $pagenum;
		if ($maxi > $count) $maxi = $count;
		for($i; $i<$maxi; $i++){
			$showlist[$i] = $list[$i];
		}
		
		$this->assign('list',$showlist);
		$this->assign('pageshow',$show);
		
			
		$this->assign('left_css',"41");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/Usersperk";
		$this->display($lib_display);
	}
	
	public function Usersperklist(){
		
		$uid = I("uid");
		if (empty($uid)){
			$this->error('uid不能为空');
			exit;
		}
		$this->assign('uid',$uid);
		
		$table = "user_sperk";
		$row = M($table);
		
		$act = I("act");
		if ($act == "recalldo"){
			
			$recall = I("recall");
			
			$info = $row->where("uid=".$uid)->order('addtime desc')->find();
				
			$data = array();
			$data['uid'] = $uid;
			$data['message'] = $recall;
			$data['pant_id'] = 1;
			$data['ver'] = $info['ver'];
			$data['addtime'] = time();
			$result = $row->add($data);
			
			if($result){
				echo "1";
				exit;
			}else{
				echo "0";
				exit;
			}
		}
		
		$list = $row->where('uid='.$uid)->order('addtime,id')->select();
		//dump($row->_sql());
		foreach($list as $key=>$val){
			$list[$key]['addtime'] = (!empty($val['addtime'])) ? date("Y-m-d H:i:s", $val['addtime']) : "";
			$list[$key]['showuid'] = ($val['pant_id']==0) ? $val['uid'].'：' : '【客服回复】：';
			$list[$key]['showflag'] = ($val['pant_id']==0) ? $val['uid'] : 1;
		}
		$this->assign('list',$list);
		
		$this->assign('left_css',"41");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/Usersperklist";
		$this->display($lib_display);
	} 
	
	//排行榜
	public function paihang(){
		$table = "fx_paihang";
		$row = M($table);
		$date1 = date("Y-m-d");
		$time1 = strtotime($date1);
		$table1 = "log_game_record_log_".date("Ym");
		$row1 = M($table1, '', DB_CONFIG2);
		$table2 = "user_info";
		$row2 = M($table2, '', DB_CONFIG2);
		
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
			/*
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
			$result = $row->add($data9);*/
			$can1 = "";
		}else{
			//$info = $row->where("flag=$flag")->find();
			//$can1 = json_decode($info['tongji'], true);
			$info = $row->where("flag=$flag")->find();
			$can1 = $info['tongji'];
		}
		
		//昨日赢金榜参数
		$flag = 2;
		$total = $row->where("flag=$flag")->count();
		if ($total==0){
			/*
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
			$result = $row->add($data9);*/
			$can2 = "";
		}else{
			//$info = $row->where("flag=$flag")->find();
			//$can2 = json_decode($info['tongji'], true);
			$info = $row->where("flag=$flag")->find();
			$can2 = $info['tongji'];
		}
		
		//昨日充值榜参数
		$flag = 3;
		$total = $row->where("flag=$flag")->count();
		if ($total==0){
			/*$can3 = array();
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
			$result = $row->add($data9);*/
            $can3 = "";
		}else{
			$info = $row->where("flag=$flag")->find();
            $can3 = $info['tongji'];
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
				$can1 = !empty($_POST['uid']) ? str_replace('，', ',', $_POST['uid']) : "";
				$data9 = array('data' => date("Y-m-d"),
							   'tongji' => $can1,
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
				$can2 = !empty($_POST['uid']) ? str_replace('，', ',', $_POST['uid']) : "";
				$data9 = array('data' => date("Y-m-d"),
							   'tongji' => $can2,
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
                $can3 = !empty($_POST['uid']) ? str_replace('，', ',', $_POST['uid']) : "";
                $data9 = array('data' => date("Y-m-d"),
                    'tongji' => $can3,
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
		
		if ($wei=="4" and $act=="do"){
			$url = DB_HOST."/Pay/getpaihang4.php";
			$result = curlGET($url);
			$this->success('重新成功，客户端下次获取时会更新',U($this->By_tpl.'/paihang'));		
			exit;
		}
		
		$info = $row->where("flag=5")->find();
		//echo trim($info['tongji']);
		$pai1 = json_decode(trim($info['tongji']), true);
		//print_r($pai1);
		$info = $row->where("flag=6")->find();
		$pai2 = json_decode($info['tongji'], true);
		$info = $row->where("flag=7")->find();
		$pai3 = json_decode($info['tongji'], true);

		$this->assign('pai1',$pai1);
		$this->assign('pai2',$pai2);
		$this->assign('pai3',$pai3);
		//exit;
		$this->assign('left_css',"41");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/paihang";
		$this->display($lib_display);
	}
	
	//老虎机
	public function tiger(){
		$table1 = "kingflower.manual_configure_tiger";
		$row1 = M($table1, '', DB_CONFIG2);
		$table2 = "kingflower.profile_tiger_configure";
		$row2 = M($table2, '', DB_CONFIG2);
		
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
	
	//获取渠道数据
	public function channel(){

		$row = M("fx_online_tongji1");
		$row4 = M("payment");
		$beginTime = I("beginTime");
		$today = date("Y-m-d");
		
		$limit_model = M("channel_limit_user");
		$info = $limit_model->field("GROUP_CONCAT(user_id) AS limit_user_id")->find();
		$limit_user_id = $info['limit_user_id'];
		
		if (!empty($beginTime)){
			$time1 = strtotime($beginTime);
			$time2 = $time1 + 60 * 60 * 24;
			$date1 = date("Y-m-d", $time1);
			$date2 = date("Y-m-d", $time2);
			
			//小于今天数据库没有的数据才保存
			if ($beginTime < $today){
				//排除机器人
				$sql1 = " !((user_id>=".ROBERT1_BEGIN." and user_id<".ROBERT1_END.") or (user_id>=".ROBERT2_BEGIN." and user_id<=".ROBERT2_END."))";
				
				//获取所有渠道
				$table3 = "pay_now_config.zjh_order";
				$row3 = M($table3);
				$row1 = M('user_info', '', DB_CONFIG2);
				$table2 = "log_login_".date("Ymd", $time1);
				$row2 = M($table2, '', DB_CONFIG3);
				//$res10 = $row1->field('user_id')->limit(0,100)->select();
				
				
				if (!empty($limit_user_id)) {
					$sql1 .= " and user_id not in ($limit_user_id) and channel != 2";
					$total = $row->where("data='$date1' and channel=2")->count();
					if ($total == 0){
						//活跃用户
						$count2 = $row2->where("user_id in ($limit_user_id)")->count('distinct user_id');
						$count13 = $row3->where("order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') and user_id in ($limit_user_id)")->sum('result_money');
						if (empty($count13)) $count13 = 0;  else $count13 = $count13 / 100;
						$count8 = ($count2==0) ? 0 : round($count13/$count2,2);
						$payment = $row4->field('payment_id,payment_name')->where('payment_status=1')->order('order_by_value')->select();
						foreach($payment as $key2 => $val2){
							$payment[$key2]['count'] = 0;
						}
						
						$tongji = array('data' => date("Ymd", $time1),
										'channel' => 2,
										'gameid' => '102',
										'game' => '皇家AAA',
										'count1' => 0,
										'count2' => $count2,
										'count3' => 0,
										'count4' => 0,
										'count5' => 0,
										'count6' => 0,
										'count7' => 0,
										'count8' => $count8,
										'count9' => 0,
										'count10' => 0,
										'count11' => 0,
										'count12' => 0,
										'count13' => $count13,
										'payment' => $payment);
							
						$data9 = array('data' => $date1,
									   'channel' => 2,
									   'tongji' => json_encode($tongji),
									   'addtime' => time());
						$result = $row->add($data9);
					}
				}
				//获取已分析的渠道
				$info = $row->field("GROUP_CONCAT(channel) AS channel_id")->where("data='$date1'")->find();
				$channel_id = $info['channel_id'];
				if (!empty($channel_id)) $sql2 = $sql1." and channel not in ($channel_id)";
				
				$res1 = $row1->field('channel')->where($sql2)->group('channel')->select(); 
				foreach($res1 as $key1 => $val1){
					if (!empty($val1['channel'])){
						
						$total = $row->where("data='$date1' and channel='".$val1['channel']."'")->count();
						if ($total == 0){
							//新增用户
							$count1 = $row1->where("$sql1 and register_date>='$date1' and register_date<'$date2' and channel=".$val1['channel'])->count('user_id');
							//活跃用户
							$count2 = $row2->where("$sql1 and login_date>='$date1' and login_date<'$date2' and channel=".$val1['channel'])->count('distinct user_id');
							//新增有效用户
							$count3 = $row1->where("$sql1 and register_date>='$date1' and register_date<'$date2' and today_game_counts>=3 and channel=".$val1['channel'])->count('user_id');
							//dump($row1->_sql());
							//echo "<br>".$count3."<br>";
							//独立付费用户
							$count4 = $row3->where("order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') and package_id=".$val1['channel'])->count('distinct user_id');
							//新增付费用户
							$res9 = $row1->field('user_id')->where("$sql1 and register_date>='$date1' and register_date<'$date2' and channel=".$val1['channel'])->select();
							$sql40 = "";
							foreach($res9 as $key9 => $val9){
								$sql40 .= (empty($sql40)) ? $val9['user_id'] : ",".$val9['user_id'];
							}
							if (!empty($sql40)) $sql40 = " and user_id in ($sql40)";
							//$count9 = $row3->where("order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') $sql40 and package_id=".$val1['channel'])->count('distinct user_id');
							if (empty($sql40)) $count9 = 0; else $count9 = $row3->where("order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') $sql40 and package_id=".$val1['channel'])->count('distinct user_id');
							//新增用户总收入
							if (empty($sql40)) {$count5 = 0;} else {
								$count5 = $row3->where("order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') $sql40 and package_id=".$val1['channel'])->sum('result_money');
								if (empty($count5)) $count5 = 0;  else $count5 = $count5 / 100;
							}
							//总收入
							$count13 = $row3->where("order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') and package_id=".$val1['channel'])->sum('result_money');
							if (empty($count13)) $count13 = 0;  else $count13 = $count13 / 100;
							//付费率(付费用户/活跃用户)
							$count6 = ($count2==0) ? 0 : round($count4/$count2,3) * 100;
							//付费用户ARPU(总收入/付费用户)
							$count7 = ($count3==0) ? 0 : round($count13/$count3,2);
							//活跃用户ARPU(总收入/活跃用户)
							$count8 = ($count2==0) ? 0 : round($count13/$count2,2);
							
							//次日留存
							$time3 = $time1 - 60 * 60 * 24;
							$date3 = date("Y-m-d", $time3);
							$res10 = $row1->field('user_id')->where("$sql1 and register_date>='$date3' and register_date<'$date1' and channel=".$val1['channel'])->select();
							$sql4 = "";
							$sum1 = 0;
							foreach($res10 as $key10 => $val10){
								$sql4 .= (empty($sql4)) ? $val10['user_id'] : ",".$val10['user_id'];
								$sum1++;
							}
							if (!empty($sql4)) $sql4 = " and user_id in ($sql4)";
							if ($sum1 == 0){
								$count10 = 0;
							}else{
								$sum2 = $row2->where("$sql1 and login_date>='$date1' and login_date<'$date2' $sql4 and channel=".$val1['channel'])->count('distinct user_id');
								$count10 = ($sum1==0) ? 0 : round($sum2/$sum1, 3)*100;
							}
							//7日留存
							$time3 = $time1 - 60 * 60 * 24 * 7;
							$date3 = date("Y-m-d", $time3);
							$time4 = $time3 + 60 * 60 * 24;
							$date4 = date("Y-m-d", $time4);
							$res10 = $row1->field('user_id')->where("$sql1 and register_date>='$date3' and register_date<'$date4' and channel=".$val1['channel'])->select();
							//dump($row1->_sql());
							$sql4 = "";
							$sum1 = 0;
							foreach($res10 as $key10 => $val10){
								$sql4 .= (empty($sql4)) ? $val10['user_id'] : ",".$val10['user_id'];
								$sum1++;
							}
							if (!empty($sql4)) $sql4 = " and user_id in ($sql4)";
							if ($sum1 == 0){
								$count11 = 0;
							}else{
								$sum2 = $row2->where("$sql1 and login_date>='$date1' and login_date<'$date2' $sql4 and channel=".$val1['channel'])->count('distinct user_id');
								//dump($row2->_sql());
								$count11 = ($sum1==0) ? 0 : round($sum2/$sum1, 3)*100;
							}
							//echo $sum1."**".$sum2."<br>";
							//15日留存
							$time3 = $time1 - 60 * 60 * 24 * 15;
							$date3 = date("Y-m-d", $time3);
							$time4 = $time3 + 60 * 60 * 24;
							$date4 = date("Y-m-d", $time4);
							$res10 = $row1->field('user_id')->where("$sql1 and register_date>='$date3' and register_date<'$date4' and channel=".$val1['channel'])->select();
							$sql4 = "";
							$sum1 = 0;
							foreach($res10 as $key10 => $val10){
								$sql4 .= (empty($sql4)) ? $val10['user_id'] : ",".$val10['user_id'];
								$sum1++;
							}
							if (!empty($sql4)) $sql4 = " and user_id in ($sql4)";
							if ($sum1 == 0){
								$count12 = 0;
							}else{
								$sum2 = $row2->where("$sql1 and login_date>='$date1' and login_date<'$date2' $sql4 and channel=".$val1['channel'])->count('distinct user_id');
								$count12 = ($sum1==0) ? 0 : round($sum2/$sum1, 3)*100;
							}
							
							//支付详情
							$payment = $row4->field('payment_id,payment_name')->where('payment_status=1')->order('order_by_value')->select();
							foreach($payment as $key2 => $val2){
								if (empty($sql40)) {$count = 0;} else{
									$count = $row3->where("order_create_time>='$time1' and order_create_time<'$time2' and payment_status in ('1','-2') $sql40 and payment_id=".$val2['payment_id']." and package_id=".$val1['channel'])->sum('result_money');
									if (empty($count)) $count = 0;  else $count = $count / 100;
								}
								$payment[$key2]['count'] = $count;
							}
							
							
							$tongji = array('data' => date("Ymd", $time1),
											'channel' => empty($val1['channel']) ? "all" : $val1['channel'],
											'gameid' => '102',
											'game' => '皇家AAA',
											'count1' => $count1,
											'count2' => $count2,
											'count3' => $count3,
											'count4' => $count4,
											'count5' => $count5,
											'count6' => (empty($count6)) ? 0 : $count6.'%',
											'count7' => $count7,
											'count8' => $count8,
											'count9' => $count9,
											'count10' => (empty($count10)) ? 0 : $count10.'%',
											'count11' => (empty($count11)) ? 0 : $count11.'%',
											'count12' => (empty($count12)) ? 0 : $count12.'%',
											'count13' => $count13,
											'payment' => $payment);
							
							$data9 = array('data' => $date1,
										   'channel' => empty($val1['channel']) ? "all" : $val1['channel'],
										   'tongji' => json_encode($tongji),
										   'addtime' => time());
							$result = $row->add($data9);				
						}
						
					}
				}
				//exit;
				$this->success('添加成功', U('Other/channel'));
				exit;
			}else{
				$this->error('小于当天的数据不保存');
				exit;
			}
			
			
		}else{
			$beginTime = date("Y-m-d",strtotime("-1 day"));
			
			import('ORG.Util.Page');
			$count = $row->count('id');
			$Page = new Page($count,20);
			$show = $Page->show();
			$list = $row->order('addtime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
			//dump($row->_sql());
			foreach($list as $key => $value){
				$list[$key]['addtime'] = date("Y-m-d H:i:s", $value['addtime']);
				$list[$key]['tongji'] = json_decode($value['tongji'], true);
				//$list[$key]['pay'] = $list[$key]['tongji']['payment'];
				$list[$key]['showflag'] = ($list[$key]['flag']=="1") ? "已发送" : "未发送";
			}
			//print_r($list);	
			$this->assign('list',$list);
			$this->assign('pageshow',$show);
			
			$payment = $row4->field('payment_id,payment_name')->where('payment_status=1')->order('order_by_value')->select();
			$this->assign('payment',$payment);
			
			$this->assign('beginTime',$beginTime);
			$this->assign('left_css',"41");
			$this->display("Other/channel");
		}
	}

	//异步发送渠道数据
	public function channel_ajax(){
		$row = M("fx_online_tongji1");
		$list = $row->where('flag=0')->order('addtime desc')->select();
		$gameid = "102";
		$url = CHANNEL_RUL;
		foreach($list as $key => $value){
			//$show[$key] = $value['tongji'];
			
			$sign = md5($gameid.$value['data'].GAME_KEYS);
			$post_data = array('channel' => $value['tongji'],
							   'gameid' => $gameid,
							   'data' => $value['data'],
							   'showchannel' => $value['channel'],
							   'sign' => $sign);
			$result = curlPOST2($url, $post_data);	
			if ($result == "1"){
				$data1 = array('flag' => 1);
				$row->where('id='.$value['id'])->save($data1);
			} 	
		}
		
		echo $result;
		exit;
	}	
	
	//用户推广统计
	public function spread(){
		
		$config = M("config");
		
		$spread_id = I("spread_id");
		$type = I("type");
		$SPREAD_SWITCH = I("SPREAD_SWITCH");
		if (empty($type)) $type = 1;
		if (empty($SPREAD_SWITCH)){
			$info = $config->where("config_name='SPREAD_SWITCH'")->find();
			$SPREAD_SWITCH = $info['config_value'];
		}else{
			$data = array();
			$data['config_value'] = $SPREAD_SWITCH;
			$result = $config->where("config_name='SPREAD_SWITCH'")->save($data);
		} 
		$sql0 = "1";
		if (!empty($spread_id)) $sql0 .= " and spread_id=".$spread_id;
		
		$this->assign('spread_id',$spread_id);
		$this->assign('type',$type);
		$this->assign('SPREAD_SWITCH',$SPREAD_SWITCH);
		
		$row = M("user_spread", '', DB_CONFIG2);
		$user = M("user_info", '', DB_CONFIG2);
		import('ORG.Util.Page');
		//echo $type; exit;
		if ($type==1){

			$count = $row->where($sql0)->count('id');
			$Page = new Page($count,20);//实例化分页类传入总记录数和每页显示的记录数		
			$show       = $Page->show();// 分页显示输出
			$list = $row->where($sql0)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
			foreach($list as $key=>$val){
				$userinfo = $user->field('nick_name,nickname')->where("user_id=".$val['user_id'])->find();
				$list[$key]['usernick'] = !empty($userinfo['nickname']) ? $userinfo['nickname'] : $userinfo['nick_name'];
				
				$userinfo = $user->field('nick_name,nickname')->where("user_id=".$val['spread_id'])->find();
				$list[$key]['spreadnick'] = !empty($userinfo['nickname']) ? $userinfo['nickname'] : $userinfo['nick_name'];
				
				$list[$key]['user_flag'] = ($val['user_flag']=="1") ? "是" : "否";
				$list[$key]['spread_flag'] = ($val['spread_flag']=="1") ? "是" : "否";
				$list[$key]['notice_flag'] = ($val['notice_flag']=="1") ? "已通知" : "未通知";
					
				$list[$key]['addtime'] = (!empty($val['addtime'])) ? date("Y-m-d H:i:s", $val['addtime']) : "";
			}
			$lib_display = $this->By_tpl."/spread";
			$this->assign('list',$list);
			
		}else{
			
			
		}
			
		
		$this->assign('pageshow',$show);
			
		$this->assign('left_css',"41");
		$this->assign('By_tpl',$this->By_tpl);
		
		$this->display($lib_display);
	}
	
	//手工修改展示
	public function vip(){
		
		$user_id = I("user_id");
		$type = I("type");
		if (empty($type)) $type = 1;
		$sql0 = "1";
		
		
		$this->assign('user_id',$user_id);
		$this->assign('type',$type);
		//$this->assign('SPREAD_SWITCH',$SPREAD_SWITCH);
		
		$user = M("user_info", '', DB_CONFIG2);
		import('ORG.Util.Page');
		//echo $type; exit;
		if ($type==1){
			if (!empty($user_id)) $sql0 .= " and user_id=".$user_id;
			$row = M("log_change_user_vip", '', DB_CONFIG2);
			$count = $row->where($sql0)->count('id');
			$Page = new Page($count,20);//实例化分页类传入总记录数和每页显示的记录数		
			$show       = $Page->show();// 分页显示输出
			$list = $row->where($sql0)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
			foreach($list as $key=>$val){
				
				$userinfo = $user->field('nick_name,nickname,total_pay_num')->where("user_id=".$val['user_id'])->find();
				$list[$key]['usernick'] = !empty($userinfo['nickname']) ? $userinfo['nickname'] : $userinfo['nick_name'];
				
				$list[$key]['beforepay'] = $val['beforepay'] / 100;
				$list[$key]['nowpay'] = $userinfo['total_pay_num'] / 100;
				$list[$key]['curpay'] = $val['curpay'] / 100;
					
				//$list[$key]['operatortime'] = (!empty($val['operatortime'])) ? date("Y-m-d H:i:s", $val['operatortime']) : "";
			}
			$lib_display = $this->By_tpl."/vip";
			$this->assign('list',$list);
			
		}if ($type==2){
			if (!empty($user_id)) $sql0 .= " and (res_userid=".$user_id." or des_userid=".$user_id.")";
			$row = M("log_change_userid", '', DB_CONFIG2);
			$count = $row->where($sql0)->count('id');
			$Page = new Page($count,20);//实例化分页类传入总记录数和每页显示的记录数		
			$show       = $Page->show();// 分页显示输出
			$list = $row->where($sql0)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
			foreach($list as $key=>$val){
				
				$userinfo = $user->field('nick_name,nickname')->where("user_id=".$val['des_userid'])->find();
				$list[$key]['usernick'] = !empty($userinfo['nickname']) ? $userinfo['nickname'] : $userinfo['nick_name'];

			}
			$lib_display = $this->By_tpl."/user";
			$this->assign('list',$list);
			
		}else{
			
			
		}
			
		
		$this->assign('pageshow',$show);
			
		$this->assign('left_css',"41");
		$this->assign('By_tpl',$this->By_tpl);
		
		$this->display($lib_display);
	}
	
	//添加员工
	public function limituser_add(){
		if(!empty($_POST)){
		
			if(empty($_POST['user_id'])){
				$this->error('user_id不能为空');
				exit;
			}
			
			$table = M('channel_limit_user');
			
			$count = $table->where('user_id='.trim($_POST['user_id']))->count('id');
			//dump($table->_sql());
			//echo $count; exit;
			if ($count > 0){
				$this->error($_POST['user_id'].'已添加');
				exit;
			}
			
			$data = array();
			$data['user_id'] =  trim($_POST['user_id']);
			
			$result = $table->add($data);
			//dump($employee->_sql()); exit;
			if($result){
			
				$this->success('添加成功',U('Other/limituser'));
			}else{
				
				$this->error('添加失败');
				exit;
			}
		}else{

			$this->assign('left_css',"41");
			$this->display('Other:limituser_add');
		}
		
	}

	//用户列表
	public function limituser(){

		$table = M('channel_limit_user');
		import('ORG.Util.Page');
		$count = $table->count('id');
		$Page       = new Page($count,15);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		
		$list = $table->order('id')->limit($Page->firstRow.','.$Page->listRows)->select();
		
		$this->assign('left_css',"41");
		$this->assign('list',$list);
		$this->assign('pageshow',$show);
		
		$this->display('Other:limituser_list');
	}
	
	//员工删除
	public function limituser_delete(){
		if(empty($_GET)){ 
			
			$this->error('非法操作');
			exit;
		}else{
			$id = $_GET['id']?$_GET['id']:$_POST['id'];
			$table = M('channel_limit_user');
			$where['id'] = $id;
			$result = $table->where($where)->delete();
			if($result){
				
				$this->success('删除成功');
			}else{
				
				$this->error('删除失败');
				exit;
			}
		}
	}

	//员工更新
	public function limituser_edit(){
		$table = M('channel_limit_user');

		if(!empty($_POST)){
			
			$data = array();
			$data['user_id'] =  trim($_POST['user_id']);
			$id['id']=intval($_POST['id']);
			$result=$table->where($id)->save($data);
			if($result){
				
				$this->success('更新成功',U('Other/limituser'));
			}else{
				
				$this->error('更新失败');
			}
		}else{
			
			$id['id']=$_GET['id'];
			$info = $table->where($id)->find();
			$this->assign('info',$info);
			
			$this->assign('left_css',"41");
			$this->display('Other:limituser_edit');
		}
	}
	
	//封号
	public function fenghao(){
		
		$tiren_model = M("fx_other_jinbi");
		$row = M("zjhmysql.user_info", "", DB_CON_GAME);
		$row1 = M("zjhmysql.user_fenghao", "", DB_CON_GAME);
		
		$user_id = I("user_id");
		$enable = I("enable");
		$meno = I("meno");
		
		if (!empty($user_id)){
			if (strlen($user_id) < 5){
				$this->error('用户UID不能小于5位');
				exit;
			}
			
			$user = $row->where("user_id=".$user_id)->find();
			//dump($row->_sql());
			//print_r($user); exit;
			if (empty($user['user_name'])){
				$this->error('用户UID有误');
				exit;
			}
			
			if ($enable=="1" && $user['enable']=="1"){
				$this->error('该用户已是启动状态');
				exit;
			}
			
			if ($enable=="0" && $user['enable']=="0"){
				$this->error('该用户已是封号状态');
				exit;
			}
			
			$data1 = array();
			$data1['user_id'] = $user_id;
			$data1['nickname'] = !empty($user['nickname']) ? $user['nickname'] : $user['nick_name'];
			$data1['gold'] = $user['gold'];
			$data1['deposit'] = $user['deposit'];
			$data1['diamond'] = $user['diamond'];
			$data1['flower'] = $user['flower'];
			$data1['aggs'] = $user['aggs'];
			$data1['car'] = $user['car'];
			$data1['villa'] = $user['villa'];
			$data1['yacht'] = $user['yacht'];
			$data1['czr'] = $_SESSION['username'];
			$data1['addtime'] = time();
			$data1['meno'] = $meno;
			
			$data = array();
			if ($enable=="0"){
				$data['enable'] = $enable;
				$data['gold'] = 0;
				$data['deposit'] = 0;
				$data['diamond'] = 0;
				$data['flower'] = 0;
				$data['aggs'] = 0;
				$data['car'] = 0;
				$data['villa'] = 0;
				$data['yacht'] = 0;
				$show_msg = $user_id."封号成功";
				
				//踢人下线开始
				$data2 = array();
				$data2['type']   = 2;
				$data2['user_id']   = $user_id ;
				$data2['gold']   = 0;
				$data2['meno'] =  "封号踢人下线";
				$data2['czz'] =  $_SESSION['username'];
				$data2['addtime']   = time();

				$result2 = $tiren_model->add($data2);
				//dump($row->_sql()); exit;
				if($result2){
					//增加操作记录
					$logs = C('JINBI_MSG_ADD_SUCCESS');
					$remark = "(踢人下线UID:".$data2['user_id'].",".json_encode($data2).")";
					adminlog($logs,$remark);
					
					//调用金币接口
					$url = DB_HOST."/Pay/jinbi.php?id=".$result2;
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
						$result11 = $tiren_model->where("id=".$result2)->save($jinbi);
						$result12 = $tiren_model->where("id=".$result2)->setInc('notify_times',1);
					}
					
				}
				//踢人下线结束
				
			}else{
				$user_fenghao = $row1->where("user_id=".$user_id)->order('addtime desc')->find();
				if (empty($user_fenghao)){
					$this->error('该用户没有封号信息');
					exit;
				}
				$data['enable'] = $enable;
				$data['gold'] = $user_fenghao['gold'];
				$data['deposit'] = $user_fenghao['deposit'];
				$data['diamond'] = $user_fenghao['diamond'];
				$data['flower'] = $user_fenghao['flower'];
				$data['aggs'] = $user_fenghao['aggs'];
				$data['car'] = $user_fenghao['car'];
				$data['villa'] = $user_fenghao['villa'];
				$data['yacht'] = $user_fenghao['yacht'];
				$show_msg = $user_id."解封成功";
			}
			
			//print_r($data);
			$result = $row->where("user_id=".$user_id)->save($data);
			//dump($row->_sql()); 
			if($result){
				//增加日志
				$result1 = $row1->add($data1);
				//dump($row1->_sql()); 
				
				//增加操作记录
				$logs = C('FENGHAO_USER_ADD_SUCCESS');
				$remark = "";
				adminlog($logs,$remark);
				//exit;
				$lib_display = $this->By_tpl."/fenghao";
				$this->success($show_msg ,U($lib_display));
			}else{
				//增加操作记录
				$logs = C('FENGHAO_USER_ADD_FALSE');
				adminlog($logs);
				
				$this->error('添加失败');
				exit;
			}
		
		}else{
			//增加操作记录
			$logs = C('FENGHAO_USER_ADD');
			adminlog($logs);
			
			import('ORG.Util.Page');
			$count = $row1->count('user_id');
			$Page       = new Page($count,100);//实例化分页类传入总记录数和每页显示的记录数		
			$show       = $Page->show();// 分页显示输出
			$list = $row1->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
			foreach($list as $key=>$val){
				$list[$key]['addtime'] = date("Y-m-d H:i:s", $val['addtime']);
			}
			
			$this->assign('list',$list);
			$this->assign('pageshow',$show);
			
			$this->assign('left_css',"58");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl."/fenghao";
			$this->display($lib_display);
		}
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