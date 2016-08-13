<?php
class AnysdkAction extends Action {
  
	public function reg(){
		//认证用户合法性
		require_once("anywhere/anysdk_login.php");
		$login = new Login();
		$arr = $login->check();
		echo $arr; 
	}
	
	public function index(){
		
		header("Content-type:text/html;charset=utf-8"); 
		$result0 = array();
		//echo "***";
		if (!empty($_POST)){
			//$_POST['params'] = '{"version":"1.1.1","model":"H30-U10","sign":"0866ad89a1538efb424473d0cd121c20","externid":"363a277c99e5dc5c8395d8dc926d888c","channel":1,"macaddr":"88:e3:ab:ee:7b:11","imei":"359209021802891","type":1,"externstr":"000255","imsi":"460020174360588","hostversion":"1.1.1"}';
			$post_str = json_decode($_POST['params'], true);
			//echo $_POST['params'];
			
			//print_r($post_str);
			ksort($post_str);
			$str = "";
			foreach($post_str as $key => $val){
				if ($key != "sign") $str .= (!empty($str)) ? "&".$key."=".$val : $key."=".urlencode($val); else $sign = $val;
			}
			$str .= "&key=6F9he9U*cec4kjc168";
			
			//$_POST['md5'] = $str."**".md5($str);
			$logs_file = APP_PATH."Logs/Reguser_".time().".txt";
			file_put_contents($logs_file, json_encode($_POST));
			//echo $sign."<br>".$str."<br>".md5($str); exit;
	
			if ($sign == md5($str) && !empty($sign)){
				
				//认证用户合法性
				$res = M(MYTABLE_PRIFIX."user_reg_log");
				$count = $res->where("uid='".$post_str['externid']."' and flag=1")->count('id');
				if ($count == 0){
					echo -1;
					exit;
				}else{
					$data = array();
					$data['flag'] = 0;
					$result = $res->where("uid='".$post_str['externid']."'")->save($data);
				}
				
				$row = M();
				$v_user_name = $post_str['externid'];
				$v_pwd = "";
				$v_login_ip = $this->ipToInt(get_client_ip());
				$v_fast = "0";
				$v_flatform_type = $post_str['type'];
				$v_channel = $post_str['channel'];
				$v_version = $post_str['version'];
				$v_imsi = $post_str['imsi'];
				$v_imei = $post_str['imei'];
				$v_model = $post_str['model'];
				$v_address = "";
				$externid = is_numeric($post_str['externid']) ? $post_str['externid'] : crc32($post_str['externid']);
				$externstr = $post_str['externstr'];
				$v_gameversion = $post_str['hostversion'];
				$v_macaddr = $post_str['macaddr'];
				
				$sql = " CALL ".MYTABLE_PRIFIX."sp_register_user('$v_user_name', '$v_pwd', $v_login_ip, $v_fast, $v_flatform_type, $v_channel, '$v_version', '$v_imsi', '$v_imei', '$v_model', '$v_address', $externid, '$externstr', '$v_gameversion', '$v_macaddr')";
				//echo $sql; exit;
				$result = $row->query($sql);
				//dump($row->_sql());
				//print_r($result);
				if ($result[0]['result'] == 0 || $result[0]['result'] == 1){
					$data = array();
					$data['user_id'] = (int)$result[0]['user_id'];
					$data['user_name'] = $result[0]['user_name'];
					echo json_encode($data);
					exit;
				}else{
					//echo -1;
				}
			}
		}else{
			/*
			$this->assign('left_css',"50");
			$lib_display = "Black:duihuan";
			$this->display($lib_display);
			exit;
			*/
		}	
		//异常都返回-1
		echo -1;
		exit;
		 
	}

	private function ipToInt($ip){
		 $iparr = explode('.',$ip);
		 $num = 0;
		 for($i=0;$i<count($iparr);$i++){
			 $num += intval($iparr[$i]) * pow(256,count($iparr)-($i+1));
		 }
		 return $num;
	}
	
	
	public function uu(){
		
		header("Content-type:text/html;charset=utf-8"); 
		$result0 = array();
		//echo "***";
		if (!empty($_POST)){
			//$_POST['params'] = '{"version":"1.1.4","model":"H30-U10","token":"eUbPU4SKaJV2l29VAcq2bEt/6c2JiB27XGotPj9gbTts5wzRFKbO0Bc*dPaGgR0pI2uVr/Vhcm4TVcgCcaGe0A==","externid":"100412642","channel":1,"sign":"c19cb312835833d6a2d9060748e597b7","imei":"359209021802891","type":1,"hostversion":"1.1.4","imsi":"18c575624a3adb7","macaddr":"88:e3:ab:ee:7b:11"}';
			$post_str = json_decode($_POST['params'], true);
			//echo $_POST['params'];
			
			//print_r($post_str);
			ksort($post_str);
			$str = "";
			foreach($post_str as $key => $val){
				if ($key != "sign") $str .= (!empty($str)) ? "&".$key."=".$val : $key."=".urlencode($val); else $sign = $val;
			}
			$str .= "&key=6F9he9U*cec4kjc168";
			
			//$_POST['md5'] = $str."**".md5($str);
			$logs_file = APP_PATH."Logs/uu".time().".txt";
			file_put_contents($logs_file, json_encode($_POST));
			//echo $sign."<br>".$str."<br>".md5($str); exit;
	
			if ($sign == md5($str) && !empty($sign)){
				
				//认证用户合法性
				$token = str_replace("*","+",$post_str['token']);
				$url = 'http://uavapi.uuserv20.com/checkAccessToken.do?jsonString={"token":"'.$token.'"}';
				$token = urlencode('{"token":"'.$token.'"}');
				$url = 'http://uavapi.uuserv20.com/checkAccessToken.do?jsonString='.$token;
				$result = curlGET($url);
				$arr = json_decode($result, true);
				if ($arr['returnCode']!=1){
					echo -1;
					exit;
				}
				
				$row = M();
				$v_user_name = $post_str['externid'];
				$v_pwd = "";
				$v_login_ip = $this->ipToInt(get_client_ip());
				$v_fast = "0";
				$v_flatform_type = $post_str['type'];
				$v_channel = $post_str['channel'];
				$v_version = $post_str['version'];
				$v_imsi = $post_str['imsi'];
				$v_imei = $post_str['imei'];
				$v_model = $post_str['model'];
				$v_address = "";
				$externid = is_numeric($post_str['externid']) ? $post_str['externid'] : crc32($post_str['externid']);
				$externstr = $post_str['externstr'];
				$v_gameversion = $post_str['hostversion'];
				$v_macaddr = $post_str['macaddr'];
				
				$sql = " CALL ".MYTABLE_PRIFIX."sp_register_user('$v_user_name', '$v_pwd', $v_login_ip, $v_fast, $v_flatform_type, $v_channel, '$v_version', '$v_imsi', '$v_imei', '$v_model', '$v_address', $externid, '$externstr', '$v_gameversion', '$v_macaddr')";
				//echo $sql; exit;
				$result = $row->query($sql);
				//dump($row->_sql());
				//print_r($result);
				if ($result[0]['result'] == 0 || $result[0]['result'] == 1){
					$data = array();
					$data['user_id'] = (int)$result[0]['user_id'];
					$data['user_name'] = $result[0]['user_name'];
					echo json_encode($data);
					exit;
				}else{
					//echo -1;
				}
			}
		}else{
			/*
			$this->assign('left_css',"50");
			$lib_display = "Black:duihuan";
			$this->display($lib_display);
			exit;
			*/
		}	
		//异常都返回-1
		echo -1;
		exit;
		 
	}
	
	
	public function iyx51(){
		
		header("Content-type:text/html;charset=utf-8"); 
		$result0 = array();
		//echo "***";
		if (!empty($_POST)){
			//$_POST['params'] = '{"version":"1.1.4","model":"H30-U10","sign":"17d851966fc33e16ff357921c04c4a52","externid":"405","channel":1,"sid":"WQBRR1FRS0YAGAAQFBARbVETcnpYRGwbfg1bUWlzQ2lTaFYSUlZYZUlRE1Z4U25VUWhHWxJyEhpDF1RTbGFnERVqcXh3VkF3enVpZGFpd1hGZk8SUnVaZ0d2YXFHE2MTSFV3AA4AV1FHUExDT0cAGABKTUxFWEdaS0NMRQBf","imei":"359209021802891","type":1,"hostversion":"1.1.4","imsi":"18c575624a3adb7","macaddr":"88:e3:ab:ee:7b:11"}';
			$post_str = json_decode($_POST['params'], true);
			//echo $_POST['params'];
			
			//print_r($post_str);
			ksort($post_str);
			$str = "";
			foreach($post_str as $key => $val){
				if ($key != "sign") $str .= (!empty($str)) ? "&".$key."=".$val : $key."=".urlencode($val); else $sign = $val;
			}
			$str .= "&key=6F9he9U*cec4kjc168";
			
			//$_POST['md5'] = $str."**".md5($str);
			$logs_file = APP_PATH."Logs/iyx51".time().".txt";
			file_put_contents($logs_file, json_encode($_POST));
			//echo $sign."<br>".$str."<br>".md5($str); exit;
	
			if ($sign == md5($str) && !empty($sign)){
				
				$appid = 175;
				//认证用户合法性
				$url = "http://api.51iyx.com/sdkapi.php";  
				$arr['ac']="check";
				$arr['appid']=$appid;
				$arr['sessionid']=urldecode($post_str['sid']);
				$arr['time']=time();
				ksort($arr);
				$urlstr=http_build_query($arr);
				$loginkey="4bedcbb1cc23515554d7d63d32100a8d";
				$arr['sign']=md5($urlstr.$loginkey);
				$result = curlPOST2($url, $arr);
				$result = json_decode($result, true);
				//echo $result['code']."<br>".$result['userInfo']['username'];
				$username = $result['userInfo']['username'];
				if ($result['code']!=1){
					echo -1;
					exit;
				}
				
				$row = M();
				$v_user_name = $post_str['externid'];
				$v_pwd = "";
				$v_login_ip = $this->ipToInt(get_client_ip());
				$v_fast = "0";
				$v_flatform_type = $post_str['type'];
				$v_channel = $post_str['channel'];
				$v_version = $post_str['version'];
				$v_imsi = $post_str['imsi'];
				$v_imei = $post_str['imei'];
				$v_model = $post_str['model'];
				$v_address = "";
				$externid = is_numeric($post_str['externid']) ? $post_str['externid'] : crc32($post_str['externid']);
				$externstr = $username;
				$v_gameversion = $post_str['hostversion'];
				$v_macaddr = $post_str['macaddr'];
				
				$sql = " CALL ".MYTABLE_PRIFIX."sp_register_user('$v_user_name', '$v_pwd', $v_login_ip, $v_fast, $v_flatform_type, $v_channel, '$v_version', '$v_imsi', '$v_imei', '$v_model', '$v_address', $externid, '$externstr', '$v_gameversion', '$v_macaddr')";
				//echo $sql; exit;
				$result = $row->query($sql);
				//dump($row->_sql());
				//print_r($result);
				if ($result[0]['result'] == 0 || $result[0]['result'] == 1){
					$data = array();
					$data['user_id'] = (int)$result[0]['user_id'];
					$data['user_name'] = $result[0]['user_name'];
					echo json_encode($data);
					exit;
				}else{
					//echo -1;
				}
			}
		}else{
			
			$this->assign('left_css',"50");
			$lib_display = "Black:duihuan";
			$this->display($lib_display);
			exit;

		}	
		//异常都返回-1
		echo -1;
		exit;
		 
	}
	
	public function zhuoyi(){
		
		header("Content-type:text/html;charset=utf-8"); 
		$result0 = array();
		//echo "***";
		if (!empty($_POST)){
			//$_POST['params'] = '{"version":"1.1.4","model":"H30-U10","token":"eUbPU4SKaJV2l29VAcq2bEt/6c2JiB27XGotPj9gbTts5wzRFKbO0Bc*dPaGgR0pI2uVr/Vhcm4TVcgCcaGe0A==","externid":"100412642","channel":1,"sign":"c19cb312835833d6a2d9060748e597b7","imei":"359209021802891","type":1,"hostversion":"1.1.4","imsi":"18c575624a3adb7","macaddr":"88:e3:ab:ee:7b:11"}';
			$post_str = json_decode($_POST['params'], true);
			//echo $_POST['params'];
			
			//print_r($post_str);
			ksort($post_str);
			$str = "";
			foreach($post_str as $key => $val){
				if ($key != "sign") $str .= (!empty($str)) ? "&".$key."=".$val : $key."=".urlencode($val); else $sign = $val;
			}
			$str .= "&key=6F9he9U*cec4kjc168";
			
			//$_POST['md5'] = $str."**".md5($str);
			$logs_file = APP_PATH."Logs/zhuoyi".time().".txt";
			file_put_contents($logs_file, json_encode($_POST));
			//echo $sign."<br>".$str."<br>".md5($str); exit;
	
			if ($sign == md5($str) && !empty($sign)){
				
				//认证用户合法性
				$token = str_replace("*","+",$post_str['token']);
				$url = 'http://uavapi.uuserv20.com/checkAccessToken.do?jsonString={"token":"'.$token.'"}';
				$token = urlencode('{"token":"'.$token.'"}');
				$url = 'http://uavapi.uuserv20.com/checkAccessToken.do?jsonString='.$token;
				$result = curlGET($url);
				$arr = json_decode($result, true);
				if ($arr['returnCode']!=1){
					echo -1;
					exit;
				}
				
				$row = M();
				$v_user_name = $post_str['externid'];
				$v_pwd = "";
				$v_login_ip = $this->ipToInt(get_client_ip());
				$v_fast = "0";
				$v_flatform_type = $post_str['type'];
				$v_channel = $post_str['channel'];
				$v_version = $post_str['version'];
				$v_imsi = $post_str['imsi'];
				$v_imei = $post_str['imei'];
				$v_model = $post_str['model'];
				$v_address = "";
				$externid = is_numeric($post_str['externid']) ? $post_str['externid'] : crc32($post_str['externid']);
				$externstr = $post_str['externstr'];
				$v_gameversion = $post_str['hostversion'];
				$v_macaddr = $post_str['macaddr'];
				
				$sql = " CALL ".MYTABLE_PRIFIX."sp_register_user('$v_user_name', '$v_pwd', $v_login_ip, $v_fast, $v_flatform_type, $v_channel, '$v_version', '$v_imsi', '$v_imei', '$v_model', '$v_address', $externid, '$externstr', '$v_gameversion', '$v_macaddr')";
				//echo $sql; exit;
				$result = $row->query($sql);
				//dump($row->_sql());
				//print_r($result);
				if ($result[0]['result'] == 0 || $result[0]['result'] == 1){
					$data = array();
					$data['user_id'] = (int)$result[0]['user_id'];
					$data['user_name'] = $result[0]['user_name'];
					echo json_encode($data);
					exit;
				}else{
					//echo -1;
				}
			}
		}else{
			/*
			$this->assign('left_css',"50");
			$lib_display = "Black:duihuan";
			$this->display($lib_display);
			exit;
			*/
		}	
		//异常都返回-1
		echo -1;
		exit;
		 
	}
	
	public function daibu(){
		
		header("Content-type:text/html;charset=utf-8"); 
		$result0 = array();
		//echo "***";
		if (!empty($_POST)){
			//$_POST['params'] = '{"version":"1.1.4","model":"H30-U10","token":"ca00f35862246654ca00f3c984185229-1deaaf6462ba451276739fda6f16e22b-20160412105610-5791f0dd7b95d4fd1ac0638212264b9b-d0b51f94584b6b1a8806ce7fa644561a-9b337105d7640ff140d5033af6dc6ebb","externid":"522183484","channel":1,"sign":"0d74fddac2bfa31de65df76b8397bb6a","imei":"359209021802891","type":1,"hostversion":"1.1.4","imsi":"18c575624a3adb7","macaddr":"88:e3:ab:ee:7b:11"}';
			$post_str = json_decode($_POST['params'], true);
			//echo $_POST['params'];
			
			//print_r($post_str);
			ksort($post_str);
			$str = "";
			foreach($post_str as $key => $val){
				if ($key != "sign") $str .= (!empty($str)) ? "&".$key."=".$val : $key."=".urlencode($val); else $sign = $val;
			}
			$str .= "&key=6F9he9U*cec4kjc168";
			
			Log::write(json_encode($_POST),'INFO');
			//echo $sign."<br>".$str."<br>".md5($str); exit;
	
			if ($sign == md5($str) && !empty($sign)){
				
				//认证用户合法性
				$token = $post_str['token'];
				$url = DB_HOST.'/Pay/baidu/LoginState.php?token='.$token;
				$result = curlGET($url);
				$arr = json_decode($result, true);
				if ($arr['ResultCode']!=1){
					echo -1;
					exit;
				}
				
				$row = M();
				$v_user_name = $post_str['externid'];
				$v_pwd = "";
				$v_login_ip = $this->ipToInt(get_client_ip());
				$v_fast = "0";
				$v_flatform_type = $post_str['type'];
				$v_channel = $post_str['channel'];
				$v_version = $post_str['version'];
				$v_imsi = $post_str['imsi'];
				$v_imei = $post_str['imei'];
				$v_model = $post_str['model'];
				$v_address = "";
				$externid = is_numeric($post_str['externid']) ? $post_str['externid'] : crc32($post_str['externid']);
				$externstr = $post_str['externstr'];
				$v_gameversion = $post_str['hostversion'];
				$v_macaddr = $post_str['macaddr'];
				
				$sql = " CALL ".MYTABLE_PRIFIX."sp_register_user('$v_user_name', '$v_pwd', $v_login_ip, $v_fast, $v_flatform_type, $v_channel, '$v_version', '$v_imsi', '$v_imei', '$v_model', '$v_address', $externid, '$externstr', '$v_gameversion', '$v_macaddr')";
				//echo $sql; exit;
				$result = $row->query($sql);
				//dump($row->_sql());
				//print_r($result);
				if ($result[0]['result'] == 0 || $result[0]['result'] == 1){
					$data = array();
					$data['user_id'] = (int)$result[0]['user_id'];
					$data['user_name'] = $result[0]['user_name'];
					echo json_encode($data);
					exit;
				}else{
					//echo -1;
				}
			}
		}else{
			/*
			$this->assign('left_css',"50");
			$lib_display = "Black:duihuan";
			$this->display($lib_display);
			exit;
			*/
		}	
		//异常都返回-1
		echo -1;
		exit;
		 
	}
	
	public function g9665(){
		
		header("Content-type:text/html;charset=utf-8"); 
		$result0 = array();
		//echo "***";
		if (!empty($_POST)){
			//$_POST['params'] = '{"version":"1.1.4","model":"H30-U10","sign":"b3bf13d27d48589a781e532bf777b688","externid":"53644","channel":1,"hostversion":"1.1.4","imei":"359209021802891","type":1,"username":"15361668535","imsi":"18c575624a3adb7","macaddr":"88:e3:ab:ee:7b:11"}';
			$post_str = json_decode($_POST['params'], true);
			//echo $_POST['params'];
			
			//print_r($post_str);
			ksort($post_str);
			$str = "";
			foreach($post_str as $key => $val){
				if ($key != "sign") $str .= (!empty($str)) ? "&".$key."=".$val : $key."=".urlencode($val); else $sign = $val;
			}
			$str .= "&key=6F9he9U*cec4kjc168";
			
			Log::write(json_encode($_POST),'INFO');
			//echo $sign."<br>".$str."<br>".md5($str); exit;
	
			if ($sign == md5($str) && !empty($sign)){
				
				//认证用户合法性
				$appId = 309;
				$key = "cgdv81kwvcnlpeo4nfbjsrvb125b79uu";
				$username = $post_str['username'];
				$userid = $post_str['externid'];
				$para = array();
				$para['appId'] = $appId;
				$para['username'] = $username;
				$para['userid'] = $userid;
				
				ksort($para);
				$str = "";
				foreach($para as $key1 => $val){
					$str .= (!empty($str)) ? "&".$key1."=".$val : $key1."=".urlencode($val); 
				}
				$str .= $key;
				$sign = md5($str);
				$para['sign'] = $sign;
				$url = 'http://www.9665.com/index.php?m=member&c=android&a=ucValid';
				$result = curlPOST2($url, $para);
				$arr = json_decode($result, true);
				if ($arr['result']!=1){
					echo -1;
					exit;
				}
				
				$row = M();
				$v_user_name = $post_str['externid'];
				$v_pwd = "";
				$v_login_ip = $this->ipToInt(get_client_ip());
				$v_fast = "0";
				$v_flatform_type = $post_str['type'];
				$v_channel = $post_str['channel'];
				$v_version = $post_str['version'];
				$v_imsi = $post_str['imsi'];
				$v_imei = $post_str['imei'];
				$v_model = $post_str['model'];
				$v_address = "";
				$externid = is_numeric($post_str['externid']) ? $post_str['externid'] : crc32($post_str['externid']);
				$externstr = $post_str['externstr'];
				$v_gameversion = $post_str['hostversion'];
				$v_macaddr = $post_str['macaddr'];
				
				$sql = " CALL ".MYTABLE_PRIFIX."sp_register_user('$v_user_name', '$v_pwd', $v_login_ip, $v_fast, $v_flatform_type, $v_channel, '$v_version', '$v_imsi', '$v_imei', '$v_model', '$v_address', $externid, '$externstr', '$v_gameversion', '$v_macaddr')";
				//echo $sql; exit;
				$result = $row->query($sql);
				//dump($row->_sql());
				//print_r($result);
				if ($result[0]['result'] == 0 || $result[0]['result'] == 1){
					$data = array();
					$data['user_id'] = (int)$result[0]['user_id'];
					$data['user_name'] = $result[0]['user_name'];
					echo json_encode($data);
					exit;
				}else{
					//echo -1;
				}
			}
		}else{
			
			$this->assign('left_css',"50");
			$lib_display = "Black:duihuan";
			$this->display($lib_display);
			exit;
			
		}	
		//异常都返回-1
		echo -1;
		exit;
		 
	}
	
	public function yxt(){

		header("Content-type:text/html;charset=utf-8");
		$result0 = array();
		//echo "***";
		if (!empty($_POST)){
			//$_POST['params'] = '{"version":"1.1.4","model":"H30-U10","externid":"LncmMCYmPDF3b3dsMDBtbQl6MS82ADllMBEAD2EgP34GGz4xYRw8YjswNjwkIgMQNAElBzA+IgUTABIdPGMJeiA0PyAWA2ERDzoeBAl6PjwWOh89LQNiOxl+D2M/AGEeYB4sPDxtd3l3ICYwJzs0ODB3b3dkYGZjZGNjbWBmYHco","channel":2083,"sign":"c65e4410233eaa5b6db910fc08640f83","imei":"359209021802891","type":1,"hostversion":"1.1.4","imsi":"18c575624a3adb7","macaddr":"88:e3:ab:ee:7b:11"}';
			$post_str = json_decode($_POST['params'], true);
			//echo $_POST['params'];

			//print_r($post_str);
			ksort($post_str);
			$str = "";
			foreach($post_str as $key => $val){
				if ($key == "externid") $val = urlencode($val);
				if ($key != "sign") $str .= (!empty($str)) ? "&".$key."=".$val : $key."=".$val; else $sign = $val;
			}
			$str .= "&key=6F9he9U*cec4kjc168";

			Log::write(json_encode($_POST),'INFO');
			//echo $sign."<br>".$str."<br>".md5($str); exit;

			if ($sign == md5($str) && !empty($sign)){

				//认证用户合法性
				$appid = 5373;
				$sessionid = $post_str['externid'];
				$url = 'http://api.yxtsy.com/sdkapi.php';
				$para['ac']="check";
				$para['appid']=$appid;
				$para['sessionid']=$sessionid;
				$para['time']=time();
				ksort($para);
				$urlstr=http_build_query($para);
				$key = "6309444d64222fbcb5ad1a876f8d989f";
				$urlstr .= $key;
				$sign = md5($urlstr);
				$para['sign']=$sign;
				$result = curlPOST2($url, $para);
				//echo $result;
				$arr = json_decode($result, true);
				//print_r($arr);
				if ($arr['code']!=1){
					echo -1;
					exit;
				}

				$row = M();
				$v_user_name = $arr['userInfo']['username'];
				$v_pwd = "";
				$v_login_ip = $this->ipToInt(get_client_ip());
				$v_fast = "0";
				$v_flatform_type = $post_str['type'];
				$v_channel = $post_str['channel'];
				$v_version = $post_str['version'];
				$v_imsi = $post_str['imsi'];
				$v_imei = $post_str['imei'];
				$v_model = $post_str['model'];
				$v_address = "";
				$externid = is_numeric($arr['userInfo']['uid']) ? $arr['userInfo']['uid'] : crc32($arr['userInfo']['uid']);
				$externstr = $post_str['externstr'];
				$v_gameversion = $post_str['hostversion'];
				$v_macaddr = $post_str['macaddr'];

				$sql = " CALL ".MYTABLE_PRIFIX."sp_register_user('$v_user_name', '$v_pwd', $v_login_ip, $v_fast, $v_flatform_type, $v_channel, '$v_version', '$v_imsi', '$v_imei', '$v_model', '$v_address', $externid, '$externstr', '$v_gameversion', '$v_macaddr')";
				//echo $sql; exit;
				$result = $row->query($sql);
				//dump($row->_sql());
				//print_r($result);
				if ($result[0]['result'] == 0 || $result[0]['result'] == 1){
					$data = array();
					$data['user_id'] = (int)$result[0]['user_id'];
					$data['user_name'] = $result[0]['user_name'];
					echo json_encode($data);
					exit;
				}else{
					//echo -1;
				}
			}
		}else{
			/*
			$this->assign('left_css',"50");
			$lib_display = "Black:duihuan";
			$this->display($lib_display);
			exit;
			*/
		}
		//异常都返回-1
		echo -1;
		exit;

	}

    public function kupai(){

        header("Content-type:text/html;charset=utf-8");
        $result0 = array();
        //echo "***";
        if (!empty($_POST)){
            //$_POST['params'] = '{"version":"1.1.4","model":"H30-U10","token":"1.1921b525503265edeec2adf1adfd1cc1.f18cac8fe48b70254fd3b5e007416105.1461292806495","externid":"62649089","channel":1,"sign":"5ab2e66225c3a7feb32917a09b92966d","imei":"359209021802891","type":1,"hostversion":"1.1.4","imsi":"18c575624a3adb7","macaddr":"88:e3:ab:ee:7b:11"}';
            $post_str = json_decode($_POST['params'], true);
            //echo $_POST['params'];

            //print_r($post_str);
            ksort($post_str);
            $str = "";
            foreach($post_str as $key => $val){
                if ($key == "externid") $val = urlencode($val);
                if ($key != "sign") $str .= (!empty($str)) ? "&".$key."=".$val : $key."=".$val; else $sign = $val;
            }
            $str .= "&key=6F9he9U*cec4kjc168";

            Log::write(json_encode($_POST),'INFO');
            //echo $sign."<br>".$str."<br>".md5($str); exit;

            if ($sign == md5($str) && !empty($sign)){

                //认证用户合法性

                $url = "https://openapi.coolyun.com/oauth2/api/get_user_info?access_token=".$post_str['token']."&oauth_consumer_key=5000003560&openid=".$post_str['externid'];
                $result = curlGET($url, 1);
                //$result2 = file_get_contents($url);
                $arr = json_decode($result, true);
                //print_r($arr);
                if ($arr['rtn_code']!=0){
                    echo -1;
                    exit;
                }

                $row = M();
                $v_user_name = $post_str['externid'];
                $v_pwd = "";
                $v_login_ip = $this->ipToInt(get_client_ip());
                $v_fast = "0";
                $v_flatform_type = $post_str['type'];
                $v_channel = $post_str['channel'];
                $v_version = $post_str['version'];
                $v_imsi = $post_str['imsi'];
                $v_imei = $post_str['imei'];
                $v_model = $post_str['model'];
                $v_address = "";
                $externid = is_numeric($post_str['externid']) ? $post_str['externid'] : crc32($post_str['externid']);
                $externstr = $post_str['externstr'];
                $v_gameversion = $post_str['hostversion'];
                $v_macaddr = $post_str['macaddr'];

                $sql = " CALL ".MYTABLE_PRIFIX."sp_register_user('$v_user_name', '$v_pwd', $v_login_ip, $v_fast, $v_flatform_type, $v_channel, '$v_version', '$v_imsi', '$v_imei', '$v_model', '$v_address', $externid, '$externstr', '$v_gameversion', '$v_macaddr')";
                //echo $sql; exit;
                $result = $row->query($sql);
                //dump($row->_sql());
                //print_r($result);
                if ($result[0]['result'] == 0 || $result[0]['result'] == 1){
                    $data = array();
                    $data['user_id'] = (int)$result[0]['user_id'];
                    $data['user_name'] = $result[0]['user_name'];
                    echo json_encode($data);
                    exit;
                }else{
                    //echo -1;
                }
            }
        }else{
            /*
            $this->assign('left_css',"50");
            $lib_display = "Black:duihuan";
            $this->display($lib_display);
            exit;*/

        }
        //异常都返回-1
        echo -1;
        exit;

    }

    public function ben(){

        header("Content-type:text/html;charset=utf-8");
        $result0 = array();
        //echo "***";
        if (!empty($_POST)){
            //$_POST['params'] = '{"version":"1.1.4","model":"H30-U10","token":"2d470b0f761d4794a867cfd4328c97d8","externid":"53067","channel":2079,"sign":"4a3cb345bc76c24a275d415e8b7e3277","imei":"359209021802891","type":1,"hostversion":"1.1.4","imsi":"18c575624a3adb7","macaddr":"88:e3:ab:ee:7b:11"}';
            $post_str = json_decode($_POST['params'], true);
            //echo $_POST['params'];

            //print_r($post_str);
            ksort($post_str);
            $str = "";
            foreach($post_str as $key => $val){
                if ($key == "externid") $val = urlencode($val);
                if ($key != "sign") $str .= (!empty($str)) ? "&".$key."=".$val : $key."=".$val; else $sign = $val;
            }
            $str .= "&key=6F9he9U*cec4kjc168";

            Log::write(json_encode($_POST),'INFO');
            //echo $sign."<br>".$str."<br>".md5($str); exit;

            if ($sign == md5($str) && !empty($sign)){

                //认证用户合法性
                $sign = md5('f4a6af1ae45c47d39bb13f317d906b51'.$post_str['token']);
                $sign = strtoupper($sign);
                $url = "http://us.benshouji.com:8080/appserver/access/checkUserStatus?token=".$post_str['token']."&sign=".$sign;
                $result = curlGET($url);
                $arr = json_decode($result, true);
                if ($arr['succeed']!=1){
                    echo -1;
                    exit;
                }

                $row = M();
                $v_user_name = $post_str['externid'];
                $v_pwd = "";
                $v_login_ip = $this->ipToInt(get_client_ip());
                $v_fast = "0";
                $v_flatform_type = $post_str['type'];
                $v_channel = $post_str['channel'];
                $v_version = $post_str['version'];
                $v_imsi = $post_str['imsi'];
                $v_imei = $post_str['imei'];
                $v_model = $post_str['model'];
                $v_address = "";
                $externid = is_numeric($post_str['externid']) ? $post_str['externid'] : crc32($post_str['externid']);
                $externstr = $post_str['externstr'];
                $v_gameversion = $post_str['hostversion'];
                $v_macaddr = $post_str['macaddr'];

                $sql = " CALL ".MYTABLE_PRIFIX."sp_register_user('$v_user_name', '$v_pwd', $v_login_ip, $v_fast, $v_flatform_type, $v_channel, '$v_version', '$v_imsi', '$v_imei', '$v_model', '$v_address', $externid, '$externstr', '$v_gameversion', '$v_macaddr')";
                //echo $sql; exit;
                $result = $row->query($sql);
                //dump($row->_sql());
                //print_r($result);
                if ($result[0]['result'] == 0 || $result[0]['result'] == 1){
                    $data = array();
                    $data['user_id'] = (int)$result[0]['user_id'];
                    $data['user_name'] = $result[0]['user_name'];
                    echo json_encode($data);
                    exit;
                }else{
                    //echo -1;
                }
            }
        }else{

            $this->assign('left_css',"50");
            $lib_display = "Black:duihuan";
            $this->display($lib_display);
            exit;

        }
        //异常都返回-1
        echo -1;
        exit;

    }
	
	public function mumu(){

        header("Content-type:text/html;charset=utf-8");
        $result0 = array();
        //echo "***";
        if (!empty($_POST)){
           //$_POST['params'] = '{"version":"1.1.6","model":"H30-U10","token":"f8103M8cgFkNS*PYMoyzfIuYfcw2sJJBhLOcNzPQL81tFpWxc","externid":"9776005","channel":1,"sign":"8482b3e23056843caaa7ce5a171db49c","imei":"359209021802891","type":1,"hostversion":"1.1.6","imsi":"18c575624a3adb7","macaddr":"88:e3:ab:ee:7b:11"}';
            $post_str = json_decode($_POST['params'], true);
            //echo $_POST['params'];

            //print_r($post_str);
            ksort($post_str);
            $str = "";
            foreach($post_str as $key => $val){
                if ($key == "externid") $val = urlencode($val);
                if ($key != "sign") $str .= (!empty($str)) ? "&".$key."=".$val : $key."=".$val; else $sign = $val;
            }
            $str .= "&key=6F9he9U*cec4kjc168";

            Log::write(json_encode($_POST),'INFO');
            //echo $sign."<br>".$str."<br>".md5($str); exit;

            if ($sign == md5($str) && !empty($sign)){

                //认证用户合法性
                $token = str_replace("*","+",$post_str['token']);
				$url = "http://pay.mumayi.com/user/index/validation?token=".$token."&uid=".$post_str['externid'];
                $result = curlGET($url);
				//echo $result; exit;
                //$arr = json_decode($result, true);
                if ($result!="success"){
                    echo -1;
                    exit;
                }

                $row = M();
                $v_user_name = $post_str['externid'];
                $v_pwd = "";
                $v_login_ip = $this->ipToInt(get_client_ip());
                $v_fast = "0";
                $v_flatform_type = $post_str['type'];
                $v_channel = $post_str['channel'];
                $v_version = $post_str['version'];
                $v_imsi = $post_str['imsi'];
                $v_imei = $post_str['imei'];
                $v_model = $post_str['model'];
                $v_address = "";
                $externid = is_numeric($post_str['externid']) ? $post_str['externid'] : crc32($post_str['externid']);
                $externstr = $post_str['externstr'];
                $v_gameversion = $post_str['hostversion'];
                $v_macaddr = $post_str['macaddr'];

                $sql = " CALL ".MYTABLE_PRIFIX."sp_register_user('$v_user_name', '$v_pwd', $v_login_ip, $v_fast, $v_flatform_type, $v_channel, '$v_version', '$v_imsi', '$v_imei', '$v_model', '$v_address', $externid, '$externstr', '$v_gameversion', '$v_macaddr')";
                //echo $sql; exit;
                $result = $row->query($sql);
                //dump($row->_sql());
                //print_r($result);
                if ($result[0]['result'] == 0 || $result[0]['result'] == 1){
                    $data = array();
                    $data['user_id'] = (int)$result[0]['user_id'];
                    $data['user_name'] = $result[0]['user_name'];
                    echo json_encode($data);
                    exit;
                }else{
                    //echo -1;
                }
            }
        }else{
			/*
            $this->assign('left_css',"50");
            $lib_display = "Black:duihuan";
            $this->display($lib_display);
            exit;
			*/
        }
        //异常都返回-1
        echo -1;
        exit;

    }
	
	public function tiantian(){

        header("Content-type:text/html;charset=utf-8");
        $result0 = array();
        //echo "***";
        if (!empty($_POST)){
           //$_POST['params'] = '{"version":"1.1.6","model":"H30-U10","externid":"QZBbIHOINW7BsIu","channel":2049,"sign":"a40dded8ab5fa1cfe3c6c4aec0c06f87","imei":"359209021802891","type":1,"hostversion":"1.1.6","imsi":"18c575624a3adb7","macaddr":"88:e3:ab:ee:7b:11"}';
            $post_str = json_decode($_POST['params'], true);
            //echo $_POST['params'];

            //print_r($post_str);
            ksort($post_str);
            $str = "";
            foreach($post_str as $key => $val){
                if ($key == "externid") $val = urlencode($val);
                if ($key != "sign") $str .= (!empty($str)) ? "&".$key."=".$val : $key."=".$val; else $sign = $val;
            }
            $str .= "&key=6F9he9U*cec4kjc168";

            Log::write(json_encode($_POST),'INFO');
            //echo $sign."<br>".$str."<br>".md5($str); exit;

            if ($sign == md5($str) && !empty($sign)){

                //认证用户合法性
                $arr = array('token' => $post_str['externid'], 'cpId' => 'LT2016054-165');
				$sign = md5("/sdk/cp/user/info".json_encode($arr)."89ad40a540a701b205472925c4ce2de2");
				$url = "http://sdk.ttigame.com/sdk/cp/user/info?data=".json_encode($arr)."&sign=".$sign;
                $result = curlGET($url);
                $arr = json_decode($result, true);
                if ($arr['status'] != "1"){
                    echo -1;
                    exit;
                }

                $row = M();
                $v_user_name = $arr['data']['nickname'];
                $v_pwd = "";
                $v_login_ip = $this->ipToInt(get_client_ip());
                $v_fast = "0";
                $v_flatform_type = $post_str['type'];
                $v_channel = $post_str['channel'];
                $v_version = $post_str['version'];
                $v_imsi = $post_str['imsi'];
                $v_imei = $post_str['imei'];
                $v_model = $post_str['model'];
                $v_address = "";
                $externid = $arr['data']['id'];
                $externstr = $post_str['externstr'];
                $v_gameversion = $post_str['hostversion'];
                $v_macaddr = $post_str['macaddr'];

                $sql = " CALL ".MYTABLE_PRIFIX."sp_register_user('$v_user_name', '$v_pwd', $v_login_ip, $v_fast, $v_flatform_type, $v_channel, '$v_version', '$v_imsi', '$v_imei', '$v_model', '$v_address', $externid, '$externstr', '$v_gameversion', '$v_macaddr')";
                //echo $sql; exit;
                $result = $row->query($sql);
                //dump($row->_sql());
                //print_r($result);
                if ($result[0]['result'] == 0 || $result[0]['result'] == 1){
                    $data = array();
                    $data['user_id'] = (int)$result[0]['user_id'];
                    $data['user_name'] = $result[0]['user_name'];
                    echo json_encode($data);
                    exit;
                }else{
                    //echo -1;
                }
            }
        }else{
			/*
            $this->assign('left_css',"50");
            $lib_display = "Black:duihuan";
            $this->display($lib_display);
            exit;*/

        }
        //异常都返回-1
        echo -1;
        exit;

    }
	
	public function shuowan(){

        header("Content-type:text/html;charset=utf-8");
        $result0 = array();
        //echo "***";
        if (!empty($_POST)){
           //$_POST['params'] = '{"version":"1.1.6","model":"H30-U10","logintime":"1463448563","sign":"25858c532ab77ee2ac2629fddabbb478","externid":"D713857393003d7d7c","channel":2107,"loginsign":"2252a07513af265f319b161d43f4860b","imei":"359209021802891","type":1,"hostversion":"1.1.6","imsi":"18c575624a3adb7","macaddr":"88:e3:ab:ee:7b:11"}';
            $post_str = json_decode($_POST['params'], true);
            //echo $_POST['params'];

            //print_r($post_str);
            ksort($post_str);
            $str = "";
            foreach($post_str as $key => $val){
                if ($key == "externid") $val = urlencode($val);
                if ($key != "sign") $str .= (!empty($str)) ? "&".$key."=".$val : $key."=".$val; else $sign = $val;
            }
            $str .= "&key=6F9he9U*cec4kjc168";

            Log::write(json_encode($_POST),'INFO');
            //echo $sign."<br>".$str."<br>".md5($str); exit;

            if ($sign == md5($str) && !empty($sign)){

                //认证用户合法性
				$sign = md5("username=".$post_str['externid']."&appkey=90fb593fe2d345d0deb857477529913b&logintime=".$post_str['logintime']);
                if ($post_str['loginsign'] != $sign){
                    echo -1;
                    exit;
                }

                $row = M();
                $v_user_name = $post_str['externid'];
                $v_pwd = "";
                $v_login_ip = $this->ipToInt(get_client_ip());
                $v_fast = "0";
                $v_flatform_type = $post_str['type'];
                $v_channel = $post_str['channel'];
                $v_version = $post_str['version'];
                $v_imsi = $post_str['imsi'];
                $v_imei = $post_str['imei'];
                $v_model = $post_str['model'];
                $v_address = "";
                $externid = is_numeric($post_str['externid']) ? $post_str['externid'] : crc32($post_str['externid']);
                $externstr = $post_str['externstr'];
                $v_gameversion = $post_str['hostversion'];
                $v_macaddr = $post_str['macaddr'];

                $sql = " CALL ".MYTABLE_PRIFIX."sp_register_user('$v_user_name', '$v_pwd', $v_login_ip, $v_fast, $v_flatform_type, $v_channel, '$v_version', '$v_imsi', '$v_imei', '$v_model', '$v_address', $externid, '$externstr', '$v_gameversion', '$v_macaddr')";
                //echo $sql; exit;
                $result = $row->query($sql);
                //dump($row->_sql());
                //print_r($result);
                if ($result[0]['result'] == 0 || $result[0]['result'] == 1){
                    $data = array();
                    $data['user_id'] = (int)$result[0]['user_id'];
                    $data['user_name'] = $result[0]['user_name'];
                    echo json_encode($data);
                    exit;
                }else{
                    //echo -1;
                }
            }
        }else{
			/*
            $this->assign('left_css',"50");
            $lib_display = "Black:duihuan";
            $this->display($lib_display);
            exit;
			*/
        }
        //异常都返回-1
        echo -1;
        exit;

    }
	
	public function qq(){

        header("Content-type:text/html;charset=utf-8");
        $result0 = array();
        //echo "***";
        if (!empty($_POST)){
           //$_POST['params'] = '{"version": "1.1.6","sign": "7f58c8afb2b8102810bc6a82dbdeaba3","model": "ZTE U960E","openkey": "0884796DD66F93217D8E92F2783A965F","externid": "5FD3BBA380373D3186041FD278BCE860",    "channel": 2048,"imsi": "f7e63418622d867","imei": "864730011986699","type": 1,"hostversion": "1.1.6","platform": "1","macaddr": "30:f3:1d:71:43:16"}';
            $post_str = json_decode($_POST['params'], true);
            //echo $_POST['params'];

            //print_r($post_str);
            ksort($post_str);
            $str = "";
            foreach($post_str as $key => $val){
                if ($key == "externid") $val = urlencode($val);
                if ($key != "sign") $str .= (!empty($str)) ? "&".$key."=".$val : $key."=".$val; else $sign = $val;
            }
            $str .= "&key=6F9he9U*cec4kjc168";

            Log::write(json_encode($_POST),'INFO');
            //echo $sign."<br>".$str."<br>".md5($str); exit;

            if ($sign == md5($str) && !empty($sign)){

                //认证用户合法性
				if ($post_str['platform'] == "1"){
					$appid = "1105273510";
					$Appkey = "Avxc7DoBwVPD0f6L";
					$url = "http://ysdk.qq.com/auth/qq_check_token";
				}else{
					$appid = "wx98a8e2f65c8b5243";
					$Appkey = "6692e333ce3f054c83ff0d328643f12e";
					$url = "http://ysdk.qq.com/auth/wx_check_token";
				}
				$timestamp = time();
				$sign = md5($Appkey.$timestamp);
				$ip = get_client_ip();
				
				$url = $url."?timestamp=".$timestamp."&appid=".$appid."&sig=".$sign."&openid=".$post_str['externid']."&openkey=".$post_str['openkey']."&userip=".$ip;
                $result = curlGET($url);
                $arr = json_decode($result, true);
				//echo $url."<br>";
				//print_r($result); exit;
                if ($arr['ret'] != 0){
                    echo -1;
                    exit;
                }

                $row = M();
                $v_user_name = $post_str['externid'];
                $v_pwd = "";
                $v_login_ip = $this->ipToInt(get_client_ip());
                $v_fast = "0";
                $v_flatform_type = $post_str['type'];
                $v_channel = $post_str['channel'];
                $v_version = $post_str['version'];
                $v_imsi = $post_str['imsi'];
                $v_imei = $post_str['imei'];
                $v_model = $post_str['model'];
                $v_address = "";
                $externid = is_numeric($post_str['externid']) ? $post_str['externid'] : crc32($post_str['externid']);
                $externstr = $post_str['externstr'];
                $v_gameversion = $post_str['hostversion'];
                $v_macaddr = $post_str['macaddr'];

                $sql = " CALL ".MYTABLE_PRIFIX."sp_register_user('$v_user_name', '$v_pwd', $v_login_ip, $v_fast, $v_flatform_type, $v_channel, '$v_version', '$v_imsi', '$v_imei', '$v_model', '$v_address', $externid, '$externstr', '$v_gameversion', '$v_macaddr')";
                //echo $sql; exit;
                $result = $row->query($sql);
                //dump($row->_sql());
                //print_r($result);
                if ($result[0]['result'] == 0 || $result[0]['result'] == 1){
                    $data = array();
                    $data['user_id'] = (int)$result[0]['user_id'];
                    $data['user_name'] = $result[0]['user_name'];
                    echo json_encode($data);
                    exit;
                }else{
                    //echo -1;
                }
            }
        }else{
			
            $this->assign('left_css',"50");
            $lib_display = "Black:duihuan";
            $this->display($lib_display);
            exit;
			
        }
        //异常都返回-1
        echo -1;
        exit;

    }
}