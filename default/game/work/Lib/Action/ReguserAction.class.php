<?php
class ReguserAction extends Action {
  

	public function index(){
		
		$login_id = "11767";
		$login_key = "djELOpdxaOPfveyQ";
		
		header("Content-type:text/html;charset=utf-8");
		$result0 = array();
		//echo "***";
		if (!empty($_POST)){
			//$_POST['params'] = '{"version":"1.1.1","model":"HM2014812","macaddr":"74:51:ba:39:61:38","imsi":"460030237403108","imei":"99000554964909","externstr":"YYH1118184514","name":"YYH1118184514","hostversion":"1.1.1","token":"49a157d2-9108-4036-9561-c2b3e10fdfaf","externid":5582263,"channel":1,"type":1,"sign":"ca1ecad1b67ec26f4fefc973c8cfdea3"}';
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
			//$mulu = APP_PATH."Logs/".date("Ymd");
			//if (!file_exists($mulu)) mkdir($mulu,0777); 
			//$logs_file = $mulu."/Reguser_".time().".txt";
			//file_put_contents($logs_file, json_encode($_POST));
			Log::write(json_encode($_POST),'INFO');
			//echo $sign."<br>".$str."<br>".md5($str); exit;
	
			if ($sign == md5($str) && !empty($sign)){
				
				//认证用户合法性
				$url = "http://api.appchina.com/appchina-usersdk/user/v2/get.json?login_id=".$login_id."&login_key=".$login_key."&ticket=".$post_str['token'];
				$result = curlGET($url);
				$result = json_decode($result, true);
				//print_r($result);
				if ($result['data']['status'] != 0){
					echo -1;
					exit;
				} 
				
				$row = M();
				$v_user_name = $post_str['model'];
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
				$externid = $post_str['externid'];
				$externstr = $post_str['externstr'];
				$v_gameversion = $post_str['hostversion'];
				$v_macaddr = $post_str['macaddr'];
				
				$sql = " CALL ".MYTABLE_PRIFIX."sp_register_user('$v_user_name', '$v_pwd', $v_login_ip, $v_fast, $v_flatform_type, $v_channel, '$v_version', '$v_imsi', '$v_imei', '$v_model', '$v_address', $externid, '$externstr', '$v_gameversion', '$v_macaddr')";
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
	
	//对接UC
	public function uc(){
		
		$gameId = "663843";
		$apikey = "6d3f402eab11586b0110d6c30aaa35be";
		
		header("Content-type:text/html;charset=utf-8");
		$result0 = array();
		//echo "***";
		if (!empty($_POST)){
			//$_POST['params'] = '{"version":"1.1.2","model":"H30-U10","externid":"ssh1mobid03bd23066d24f71b6f4557d1074034c102860","channel":1,"sign":"49a11b3f94459974d8613465f894d83a","imei":"359209021802891","type":1,"hostversion":"1.1.2","imsi":"18c575624a3adb7","macaddr":"88:e3:ab:ee:7b:11"}';
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
			//$mulu = APP_PATH."Logs/".date("Ymd");
			//if (!file_exists($mulu)) mkdir($mulu,0777); 
			//$logs_file = $mulu."/Reguser_uc_".time().".txt";
			//file_put_contents($logs_file, json_encode($_POST));
			//echo $sign."<br>".$str."<br>".md5($str); exit;
			Log::write(json_encode($_POST),'INFO');
	
			if ($sign == md5($str) && !empty($sign)){
				
				//认证用户合法性
				$url = DB_HOST."/Pay/uc/samples/VerifySessionTest.php?sid=".$post_str['externid'];
				$result = curlGET($url);
				//$logs_file = $mulu."/Reguser_result_".time().".txt";
				//file_put_contents($logs_file, $result);
				//$result = json_decode($result, true);
				Log::write(json_encode($result),'INFO');
				if ($result == -1){
					echo -1;
					exit;
				}else{
					$arr = explode("|", $result);
					$uid = $arr[0];
					$nickname = $arr[2];
					$uchannel = $arr[1];
				} 
				
				$row = M();
				$v_user_name = $post_str['model'];
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
				$externid = is_numeric($uid) ? $uid : crc32($uid);
				$externstr = $uchannel;
				$v_gameversion = $post_str['hostversion'];
				$v_macaddr = $post_str['macaddr'];
				
				$sql = " CALL ".MYTABLE_PRIFIX."sp_register_user('$v_user_name', '$v_pwd', $v_login_ip, $v_fast, $v_flatform_type, $v_channel, '$v_version', '$v_imsi', '$v_imei', '$v_model', '$v_address', $externid, '$externstr', '$v_gameversion', '$v_macaddr')";
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
	
	//对接UC
	public function newuc(){
		
		$gameId = "728539";
		$apikey = "0c4fac9b36011026d1bcd9fc4a663404";
		
		header("Content-type:text/html;charset=utf-8");
		$result0 = array();
		//echo "***";
		if (!empty($_POST)){
			//$_POST['params'] = '{"version":"1.1.5","model":"ZTE U960E","externid":"sst1gamefafd153948a9495aa401c1c9c5785bf6149006","channel":2115,"sign":"756c66936864d8227a282c8f60be11be","imei":"864730011986699","type":1,"hostversion":"1.1.5","imsi":"f7e63418622d867","macaddr":"30:f3:1d:71:43:16"}';
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
			//$mulu = APP_PATH."Logs/".date("Ymd");
			//if (!file_exists($mulu)) mkdir($mulu,0777); 
			//$logs_file = $mulu."/Reguser_uc_".time().".txt";
			//file_put_contents($logs_file, json_encode($_POST));
			//echo $sign."<br>".$str."<br>".md5($str); exit;
			Log::write(json_encode($_POST),'INFO');
	
			if ($sign == md5($str) && !empty($sign)){
				
				//认证用户合法性
				
				$url = DB_HOST."/Pay/newuc/samples/VerifySessionTest.php?sid=".$post_str['externid'];
				$result = curlGET($url);
				
				//$logs_file = $mulu."/Reguser_result_".time().".txt";
				//file_put_contents($logs_file, $result);
				//$result = json_decode($result, true);
				Log::write($result,'INFO');
				if ($result == -1){
					echo -1;
					exit;
				}else{
					$arr = explode("|", $result);
					$uid = $arr[0];
					$nickname = $arr[2];
					$uchannel = $arr[1];
				}
				//print_r($arr);
				$uid = "uc".rand(100000, 999999);
				$row = M();
				$v_user_name = $post_str['model'];
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
				$externid = is_numeric($uid) ? $uid : crc32($uid);
				$externstr = $uchannel;
				$v_gameversion = $post_str['hostversion'];
				$v_macaddr = $post_str['macaddr'];
				
				$sql = " CALL ".MYTABLE_PRIFIX."sp_register_user('$v_user_name', '$v_pwd', $v_login_ip, $v_fast, $v_flatform_type, $v_channel, '$v_version', '$v_imsi', '$v_imei', '$v_model', '$v_address', $externid, '$externstr', '$v_gameversion', '$v_macaddr')";
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
	
	public function new360(){
		
		header("Content-type:text/html;charset=utf-8");
		$result0 = array();
		//echo "***";
		if (!empty($_POST)){
			//$_POST['params'] = '{"version":"1.1.6","model":"ZTE U960E","externid":"2709016432d672d07cb759f3ddeb0d4337be3fe7c4a33449e8","channel":360,"sign":"9f055d9cdf903be1de9d13e8cbe61c20","imei":"864730011986699","type":1,"hostversion":"1.1.6","imsi":"f7e63418622d867","macaddr":"30:f3:1d:71:43:16"}';
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
			//$mulu = APP_PATH."Logs/".date("Ymd");
			//if (!file_exists($mulu)) mkdir($mulu,0777); 
			//$logs_file = $mulu."/Reguser_uc_".time().".txt";
			//file_put_contents($logs_file, json_encode($_POST));
			//echo $sign."<br>".$str."<br>".md5($str); exit;
			Log::write(json_encode($_POST),'INFO');
	
			if ($sign == md5($str) && !empty($sign)){
				
				//认证用户合法性
				
				$url = "https://openapi.360.cn/user/me.json?access_token=".$post_str['externid']."&fields=id,name,avatar,sex,area";
				//echo $url;
				$result = curlGET($url);
				//echo $result; exit;
				//$logs_file = $mulu."/Reguser_result_".time().".txt";
				//file_put_contents($logs_file, $result);
				$result = json_decode($result, true);
				//Log::write($result,'INFO');
				if (!empty($result['error_code'])){
					echo -1;
					exit;
				}else{
					$uid = $result['id'];
					$name = $result['name'];
					$avatar = $result['avatar'];
				}
				//print_r($result); exit;
				$uid = $result['id'];
				$row = M();
				$v_user_name = $result['name'];;
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
				$externid = is_numeric($uid) ? $uid : crc32($uid);
				$externstr = $uchannel;
				$v_gameversion = $post_str['hostversion'];
				$v_macaddr = $post_str['macaddr'];
				
				$sql = " CALL ".MYTABLE_PRIFIX."sp_register_user('$v_user_name', '$v_pwd', $v_login_ip, $v_fast, $v_flatform_type, $v_channel, '$v_version', '$v_imsi', '$v_imei', '$v_model', '$v_address', $externid, '$externstr', '$v_gameversion', '$v_macaddr')";
				$result = $row->query($sql);
				//dump($row->_sql());
				//print_r($result);
				if ($result[0]['result'] == 0 || $result[0]['result'] == 1){
					$data = array();
					$data['user_id'] = (int)$result[0]['user_id'];
					$data['user_name'] = $result[0]['user_name'];
					$data['id_360'] = $uid;
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
	
	public function yijie(){
		
		header("Content-type:text/html;charset=utf-8");
		$result0 = array();
		//echo "***";
		if (!empty($_POST)){
			//$_POST['params'] = '{"version":"1.1.6","sign":"bb4730750c31051ab2a96c8d0138c3c6","app":"{EA87DD92-0F9C1B2F}","model":"HM2013022","imsi":"a1adf16ca4d36b3","externid":"selfServer1467427235952","channel":360,"sdk":"{4ff036a1-3254eafe}","imei":"863925028414484","type":1,"hostversion":"1.1.6","uin":"{4ff036a1-3254eafe}","macaddr":"68:df:dd:d5:fe:fa"}';
			$post_str = json_decode($_POST['params'], true);
			//echo $_POST['params'];
			
			//print_r($post_str);
			ksort($post_str);
			$str = "";
			foreach($post_str as $key => $val){
				if ($key != "sign") $str .= (!empty($str)) ? "&".$key."=".$val : $key."=".$val; else $sign = $val;
			}
			$str .= "&key=6F9he9U*cec4kjc168";
			
			//$_POST['md5'] = $str."**".md5($str);
			//$mulu = APP_PATH."Logs/".date("Ymd");
			//if (!file_exists($mulu)) mkdir($mulu,0777); 
			//$logs_file = $mulu."/Reguser_uc_".time().".txt";
			//file_put_contents($logs_file, json_encode($_POST));
			//echo $sign."<br>".$str."<br>".md5($str); exit;
			Log::write(json_encode($_POST),'INFO');
	
			if ($sign == md5($str) && !empty($sign)){
				
				//认证用户合法性
				
				$url = "http://sync.1sdk.cn/login/check.html?sdk=".$post_str['sdk']."&app=".$post_str['app']."&uin=".$post_str['uin']."&sess=".$post_str['externid'];
				//echo $url;
				$result = curlGET($url);
				//echo $result; exit;
				//$logs_file = $mulu."/Reguser_result_".time().".txt";
				//file_put_contents($logs_file, $result);
				//$result = json_decode($result, true);
				//Log::write($result,'INFO');
				if ($result != 0){
					echo -1;
					exit;
				}else{
					//$uid = $result['id'];
					//$name = $result['name'];
					//$avatar = $result['avatar'];
				}
				//print_r($result); exit;
				$uid = $post_str['uin'];
				$row = M();
				$v_user_name = is_numeric($uid) ? $uid : crc32($uid);
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
				$externid = is_numeric($uid) ? $uid : crc32($uid);
				$externstr = $uchannel;
				$v_gameversion = $post_str['hostversion'];
				$v_macaddr = $post_str['macaddr'];
				
				$sql = " CALL ".MYTABLE_PRIFIX."sp_register_user('$v_user_name', '$v_pwd', $v_login_ip, $v_fast, $v_flatform_type, $v_channel, '$v_version', '$v_imsi', '$v_imei', '$v_model', '$v_address', $externid, '$externstr', '$v_gameversion', '$v_macaddr')";
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
	
	public function mzm(){
		
		header("Content-type:text/html;charset=utf-8");
		$result0 = array();
		//echo "***";
		if (!empty($_POST)){
			//$_POST['params'] = '{"type":1,"externid":"189","sign":"cd2eed66536043285b09e7c180a4738f","token":"1d4b77ca797a4a7b1c262e0a1176a22c5783463242687","model":"HM2013022","version":"2.1.2","channel":2123,"hostversion":"2.1.2","macaddr":"68:df:dd:d5:fe:fa","imei":"863925028414484","imsi":"a1adf16ca4d36b3"}';
			$post_str = json_decode($_POST['params'], true);
			//echo $_POST['params'];
			
			//print_r($post_str);
			ksort($post_str);
			$str = "";
			foreach($post_str as $key => $val){
				if ($key != "sign") $str .= (!empty($str)) ? "&".$key."=".$val : $key."=".$val; else $sign = $val;
			}
			$str .= "&key=6F9he9U*cec4kjc168";
			
			//$_POST['md5'] = $str."**".md5($str);
			//$mulu = APP_PATH."Logs/".date("Ymd");
			//if (!file_exists($mulu)) mkdir($mulu,0777); 
			//$logs_file = $mulu."/Reguser_uc_".time().".txt";
			//file_put_contents($logs_file, json_encode($_POST));
			//echo $sign."<br>".$str."<br>".md5($str); exit;
			Log::write(json_encode($_POST),'INFO');
	
			if ($sign == md5($str) && !empty($sign)){
				
				//认证用户合法性
				$appid = "577b6d3b939a6000045";
				$appkey = "4976577b6d3b91fdb";
				$sign = md5($post_str['externid'].$post_str['token'].$appkey);
				//echo $post_str['externid']."**".$post_str['token']."**".$appkey."<br>";
				$url = "http://i.datugames.com/game_player_register/check_login?appid=".$appid."&token=".$post_str['token']."&sign=".$sign;
				//echo $url."<br>";
				$result = curlGET($url);
				//echo $result; exit;
				//$logs_file = $mulu."/Reguser_result_".time().".txt";
				//file_put_contents($logs_file, $result);
				$result = json_decode($result, true);
				//Log::write($result,'INFO');
				if ($result['code'] != 1){
					echo -1;
					exit;
				}else{
					//$uid = $result['id'];
					//$name = $result['name'];
					//$avatar = $result['avatar'];
				}
				
				
				//print_r($result); exit;
				$uid = $post_str['externid'];
				$row = M();
				$v_user_name = is_numeric($uid) ? $uid : crc32($uid);
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
				$externid = is_numeric($uid) ? $uid : crc32($uid);
				$externstr = $uchannel;
				$v_gameversion = $post_str['hostversion'];
				$v_macaddr = $post_str['macaddr'];
				
				$sql = " CALL ".MYTABLE_PRIFIX."sp_register_user('$v_user_name', '$v_pwd', $v_login_ip, $v_fast, $v_flatform_type, $v_channel, '$v_version', '$v_imsi', '$v_imei', '$v_model', '$v_address', $externid, '$externstr', '$v_gameversion', '$v_macaddr')";
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









}