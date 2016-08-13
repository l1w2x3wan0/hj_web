<?php
// 其它文件

class OtherAction extends BaseAction {

	protected $By_tpl = 'Other'; 
	private $Table_prifix = MYTABLE_PRIFIX;
		
	//头像审核
	public function touxiang(){
		$table = $this->Table_prifix."user_info";
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
					//$url = DB_HOST."Pay/touxiang.php?user_id=".$val;
					//echo $url."<br>";
					//$result = curlGET($url);
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
		$count = $row->where($sql1)->count('user_id');;
		$Page       = new Page($count,50);	
		$show       = $Page->show();
		$user = $row->where($sql1)->order("head_picture DESC,register_date DESC")->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($row->_sql());
		//print_r($user);
		$this->assign('user',$user);
		$this->assign('pageshow',$show);
		
		$lib_display = $this->By_tpl."/touxiang";
		$this->display($lib_display);
	}
	
	public function superuser(){
		$row = M(MYTABLE_PRIFIX."user_info");
		$row1 = M(MYTABLE_PRIFIX."dynamic_config");
		
		$user_id = I("user_id");
		$paygiveflag = I("paygiveflag");
		$check_user_id = I("check_user_id");
		$act = I("act");
		
		if ($act == "exceldo"){
			$xlsName  = "超级会员详情";
			
			$xlsCell  = array(
				array('sum01','UID'),
				array('sum02','昵称'),
				array('sum03','账号'),
				array('sum04','充值'),
				array('sum05','注册时间'),
				array('sum06','登录时间'),
				array('sum07','渠道'),
				array('sum08','VIP'),
				
			);
			//print_r($xlsCell);
			$list = $row->where('paygiveflag=1')->order('user_id desc')->select();
			
			$xlsData = array();
			foreach ($list as $k => $v)
			{
				$xlsData[$k]['sum01'] = $v['user_id'];
				$xlsData[$k]['sum02'] = (!empty($v['nickname'])) ? $v['nickname'] : $v['nick_name'];
				$xlsData[$k]['sum03'] = $v['user_name'];
				$xlsData[$k]['sum04'] = $v['total_pay_num'] / 100;
				$xlsData[$k]['sum05'] = $v['register_date'];
				$xlsData[$k]['sum06'] = $v['last_login_date'];
				$xlsData[$k]['sum07'] = $v['channel'];
				$xlsData[$k]['sum08'] = $v['viplevel'];
			}
			//print_r($xlsData);
			//exit;
			exportExcel($xlsName,$xlsCell,$xlsData);
			exit;
		}
		
		if (!empty($user_id)){
			if (strlen($user_id) < 5){
				$this->error('用户UID不能小于5位');
				exit;
			}
			
			$info = $row1->where("key_name='SUPER_USER'")->find();
			$SUPER_USER = empty($info['key_value']) ? 1000 : $info['key_value'];
			
			$user = $row->where("user_id=".$user_id)->find();
			//dump($row->_sql());
			//print_r($user); exit;
			$total_pay_num = $user['total_pay_num'] / 100;
			//判断用户充值是否符合配置
			
			if ((int)$total_pay_num < (int)$SUPER_USER){

				$this->error($user_id.'累计充值'.$total_pay_num.'，不足'.$SUPER_USER);
				exit;
			}
			
			$data = array();
			$data['paygiveflag'] = $paygiveflag;
			//print_r($data);
			$result = $row->where("user_id=".$user_id)->save($data);
			//dump($row->_sql()); exit;
			if($result){
				//增加操作记录
				$logs = C('SUPER_USER_ADD_SUCCESS');
				$remark = "(".$user_id.":累计充值:".$user['total_pay_num'].")";
				adminlog($logs,$remark);
				
				$lib_display = $this->By_tpl."/superuser";
				$this->success('添加成功',U($lib_display));
			}else{
				//增加操作记录
				$logs = C('SUPER_USER_ADD_FALSE');
				adminlog($logs);
				
				$this->error('添加失败');
				exit;
			}
		
		}else{
			//增加操作记录
			$logs = C('SUPER_USER_ADD');
			adminlog($logs);
			
			if (!empty($check_user_id)) $sql = " and user_id=".$check_user_id; else $sql = "";
			
			import('ORG.Util.Page');
			$count = $row->where('paygiveflag=1'.$sql)->count('user_id');
			$Page       = new Page($count,10);//实例化分页类传入总记录数和每页显示的记录数		
			$show       = $Page->show();// 分页显示输出
			$list = $row->where('paygiveflag=1'.$sql)->order('user_id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
			foreach($list as $key=>$value){
				$list[$key]['id'] = $key + 1;
				$list[$key]['nickname'] = (!empty($value['nickname'])) ? $value['nickname'] : $value['nick_name'];
			}
			
			$this->assign('list',$list);
			$this->assign('pageshow',$show);
			
			$this->assign('left_css',"58");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl."/superuser";
			$this->display($lib_display);
		}
	}

	
	//   IP登陆限制, common新增函数ipton，需要更新common.php文件
	public function limitip(){
		$row = M(MYTABLE_PRIFIX."user_info");
		$row1 = M(MYTABLE_PRIFIX."forbid_ip_login_record");
		
		$ip_str = I("ip_str");
		$address = I("address");
		
		if (!empty($ip_str)){
			$ip = ipton($ip_str); 
			
			$data1 = array();
			$data1['ip'] = $ip;
			$data1['ip_str'] = $ip_str;
			$data1['address'] = $address;
			
			$result = $row1->add($data1);
			//dump($row->_sql()); 
			if($result){
				
				//增加操作记录
				$logs = C('LIMITIP_ADD_SUCCESS');
				$remark = "";
				adminlog($logs,$remark);
				//exit;
				$lib_display = $this->By_tpl."/limitip";
				$this->success('添加成功' ,U($lib_display));
			}else{
				//增加操作记录
				$logs = C('LIMITIP_ADD_FALSE');
				adminlog($logs);
				
				$this->error('添加失败');
				exit;
			}
		
		}else{
			//增加操作记录
			$logs = C('LIMITIP_ADD');
			adminlog($logs);
			
			import('ORG.Util.Page');
			$count = $row1->count('ip_str');
			$Page       = new Page($count,10);//实例化分页类传入总记录数和每页显示的记录数		
			$show       = $Page->show();// 分页显示输出
			$list = $row1->limit($Page->firstRow.','.$Page->listRows)->select();
			foreach($list as $key=>$val){
				$list[$key]['id'] = $key + 1;
				//$list[$key]['addtime'] = date("Y-m-d H:i:s", $val['addtime']);
			}
			
			$this->assign('list',$list);
			$this->assign('pageshow',$show);
			
			$this->assign('left_css',"58");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl."/limitip";
			$this->display($lib_display);
		}
	}
	
	public function fenghao(){
		$row = M(MYTABLE_PRIFIX."user_info");
		$row1 = M(MYTABLE_PRIFIX."user_fenghao");
		
		$user_id = I("user_id");
		$enable = I("enable");
		
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
			$Page       = new Page($count,10);//实例化分页类传入总记录数和每页显示的记录数		
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
	
	public function changevip(){
		$user = M(MYTABLE_PRIFIX."user_info");
		$vip  = M(MYTABLE_PRIFIX."profile_vip_level_configure");
		$change_vip = M(MYTABLE_PRIFIX."log_change_user_vip");
		
		$user_id = I("user_id");
		$viplevel = I("viplevel");
		
		if (!empty($user_id)){
			if (strlen($user_id) < 5){
				$this->error('用户UID不能小于5位');
				exit;
			}
			
			$count = $user->where("user_id=".$user_id)->count();
			if ($count == 0){
				$this->error('用户UID不存在');
				exit;
			}
			
			//获取当前用户VIP等级
			$row1 = $user->field('viplevel')->where("user_id=".$user_id)->find();
			$row2 = $change_vip->field('viplevel')->where("user_id=".$user_id)->find();
			$old_viplevel = ($row1['viplevel'] > $row2['viplevel']) ? $row1['viplevel'] : $row2['viplevel'];
			
			if ((int)$viplevel <= (int)$old_viplevel){
				$this->error('申请的VIP等级不能小于当前VIP等级');
				exit;
			}
			
			//获取当前用户VIP所需要充值数
			$row3 = $vip->field('paycount')->where("viplevel=".$old_viplevel)->find();
			$old_paycount = $row3['paycount'];
			//获取申请VIP等级所需要充值数
			$row3 = $vip->field('paycount')->where("viplevel=".$viplevel)->find();
			$paycount = $row3['paycount'];
			//需要填充金额
			$now_paycount = ($paycount - $old_paycount) + rand(1,4);
			//调用储存过程
			$row = M();
			$sql = " CALL ".MYTABLE_PRIFIX."SP_Change_User_VipLevel($user_id, $viplevel, $now_paycount)"; 
			$result = $row->query($sql);
			if ($result[0]['result'] == 0){
				//增加操作记录
				$logs = C('CHANGE_USERVIP_SUCCESS');
				$remark = "(".$user_id.":修改VIP到".$viplevel.")";
				adminlog($logs,$remark);
				
				//调用金币接口
				$url = DB_HOST."/Pay/jinbi.php?user_id=".$user_id."&viplevel=".$viplevel;
				//echo $url; 
				$jinbi_result = curlGET($url);
				//echo $jinbi_result; //exit; 
				
				
				$lib_display = $this->By_tpl."/changevip";
				$this->success('添加成功',U($lib_display));
			}else{
				//增加操作记录
				$logs = C('CHANGE_USERVIP_FALSE');
				adminlog($logs);
				
				$this->error('添加失败');
				exit;
			}
		
		}else{
			//增加操作记录
			$logs = C('CHANGE_USERVIP_ADD');
			adminlog($logs);
			
			$sql0 = "1";
			import('ORG.Util.Page');
			$count = $change_vip->where($sql0)->count('id');
			$Page = new Page($count,20);//实例化分页类传入总记录数和每页显示的记录数		
			$show       = $Page->show();// 分页显示输出
			$list = $change_vip->where($sql0)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
			foreach($list as $key=>$val){
				
				$userinfo = $user->field('nick_name,nickname,total_pay_num')->where("user_id=".$val['user_id'])->find();
				$list[$key]['usernick'] = !empty($userinfo['nickname']) ? $userinfo['nickname'] : $userinfo['nick_name'];
				
				$list[$key]['beforepay'] = $val['beforepay'] / 100;
				$list[$key]['nowpay'] = $userinfo['total_pay_num'] / 100;
				$list[$key]['curpay'] = $val['curpay'] / 100;
					
				//$list[$key]['operatortime'] = (!empty($val['operatortime'])) ? date("Y-m-d H:i:s", $val['operatortime']) : "";
			}
			
			$this->assign('list',$list);
			$this->assign('pageshow',$show);
			
			$this->assign('left_css',"58");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl."/changevip";
			$this->display($lib_display);
		}
	}
	
	//VIP16喇叭开关
	public function vip16(){

		$row1 = M(MYTABLE_PRIFIX."dynamic_config");
		
		$flag = I("flag");
		$color = I("color");
		
		if ($flag!="" && !empty($color)){
			
			
			$info = $row1->where("key_name='USER_BROADCAST_NEW'")->find();
			$list = explode("_", $info['key_value']);
			
			$data = array();
			if (strlen($color) == 4){
				$s1 = substr($color, 1, 1);
				$s2 = substr($color, 2, 1);
				$s3 = substr($color, 3, 1);
				$color = "#".$s1.$s1.$s2.$s2.$s3.$s3;
			} 
			$data['key_value'] = $flag."_".$color."_".$list[2];
			//print_r($data);
			$result = $row1->where("key_name='USER_BROADCAST_NEW'")->save($data);
			//dump($row->_sql()); exit;
			if($result){
				//增加操作记录
				$logs = C('SUPER_USER_ADD_SUCCESS');
				$remark = "";
				adminlog($logs,$remark);
				
				//调通知服务器
				//$url = DB_HOST."/Pay/shang.php?showindex=5&showtype=4";
				//$jinbi_result = curlGET($url);
				
				$lib_display = $this->By_tpl."/vip16";
				$this->success('添加成功',U($lib_display));
			}else{
				//增加操作记录
				$logs = C('SUPER_USER_ADD_FALSE');
				adminlog($logs);
				
				$this->error('添加失败');
				exit;
			}
		
		}else{
			//增加操作记录
			$logs = C('SUPER_USER_ADD');
			adminlog($logs);
			
			$info = $row1->where("key_name='USER_BROADCAST_NEW'")->find();
			$list = explode("_", $info['key_value']);
			$this->assign('list',$list);
			
			$this->assign('left_css',"58");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl."/vip16";
			$this->display($lib_display);
		}
	}
	
	//给某个用户发消息
	public function sendemail(){

		$table_name = M('user_record');
		if(!empty($_POST)){
			
			$Table = $this->Table_prifix."user_email";
			$add_table = M($Table);
			
			$data = array();
			$data['user_id'] = $_POST['user_id'];
			$data['content'] = $_POST['content'];
			$data['opera_date'] = date("Y-m-d H:i:s");
			$data['is_read'] = 0;
			$data['email_type'] = 6;
			$result = $add_table->add($data);
			//dump($add_table->_sql());
			unset($_POST['__hash__']);
			unset($_POST['Submit']);
			
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = '给某个用户发消息';
			$data['userip'] = get_client_ip();
			$data['cate'] = 68;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);

			//$result = $add_table->add($_POST);
			//dump($add_table->_sql()); exit;
			if($result){
				//增加操作记录
				$logs = C('SHANG_MSG_ADD_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				//通知服务器
				//$url = DB_HOST."/Pay/shang.php";
				//$jinbi_result = curlGET($url);
				
				$this->success('提交成功', U($this->By_tpl.'/sendemail'));
			}else{
				//增加操作记录
				$logs = C('SHANG_MSG_ADD_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('添加失败');
				exit;
			}
		}else{
			//增加操作记录
			$logs = C('SHANG_MSG_ADD');
			$remark = "";
			adminlog($logs, $remark);

			import('ORG.Util.Page');
			$count = $table_name->where("cate=68")->count('id');
			$Page  = new Page($count,PAGE_SHOW);//实例化分页类传入总记录数和每页显示的记录数		
			$show  = $Page->show();// 分页显示输出
			
			$list = $table_name->where("cate=68")->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
			//dump($rowlist->_sql());
			foreach($list as $key=>$value){
				$info = json_decode($value['logs'], true);
				
				$list[$key]['info'] = $info;
			}
			$this->assign('list',$list);
			$this->assign('pageshow',$show);
		
			$this->assign('left_css',"58");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl."/sendemail";
			$this->display($lib_display);
		}
	}
	
	public function reward(){

		$reward_model = M(MYTABLE_PRIFIX."user_channel_reward_limit");
		
		$channel = I("channel");
		
		if (!empty($channel)){
			
			$temp_channel = explode(",", $channel);
			foreach($temp_channel as $key => $val){
				//判断是否已添加
				if (intval($val) > 0){
					$count = $reward_model->where("channel=".$val)->count();
					if ($count == 0){
						$data = array();
						$data['channel'] = $val;
						$result = $reward_model->add($data);
					}
				}
				
			}
			
			$this->success('提交成功', U($this->By_tpl.'/reward'));
			
		}else{
			//增加操作记录
			$logs = C('FENGHAO_USER_ADD');
			adminlog($logs);
			
			import('ORG.Util.Page');
			$count = $reward_model->count('id');
			$Page       = new Page($count,10);//实例化分页类传入总记录数和每页显示的记录数		
			$show       = $Page->show();// 分页显示输出
			$list = $reward_model->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
						
			$this->assign('list',$list);
			$this->assign('pageshow',$show);
			
			$this->assign('left_css',"58");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl."/reward";
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