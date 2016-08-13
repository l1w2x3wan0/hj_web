<?php
class HtcAction extends Action {
	
	public function index(){
		
		header("Content-type:text/html;charset=utf-8"); 
		$result0 = array();
		//echo "***";
		if (!empty($_POST)){
			//$_POST['params'] = '{"version":"1.1.4","account":"eyJ1c2VyX2NvZGUiOiIxMDE2MDU4ODMiLCJ1c2VyX25hbWUiOiJob25neGV4aWFuZyIsImdhbWVf\nY29kZSI6IjI5MTk1MTA5MjA5NjIiLCJzZXNzaW9uX2lkIjoiNTIxNTg1NDQiLCJpZCI6IjE0NjA3\nMDM4OTMwMDAifQ==\n","model":"H30-U10","token":"zXcTqyIWmAYODsHdXrg6Ztu7NWYX6u9jLLwhiG69aTV6Xxl7qZBh8bztN1tj3SeN6UWUeZXf/OLJNSPPYRK/abg7Acdh/P*yhsv8Do6EzdJ/ksiy4yV3oGUuIQAOQseN50PYjnxyrDgrlzSMhaM3N0P*FTQ8DP4xdNa20c2qM44=","externid":"101605883","channel":2079,"sign":"704f91d6d5cc00eebc416da25619111f","imei":"359209021802891","type":1,"hostversion":"1.1.4","imsi":"18c575624a3adb7","macaddr":"88:e3:ab:ee:7b:11"}';
			$post_str = json_decode($_POST['params'], true);
			//echo $_POST['params'];
			
			//print_r($post_str);
			ksort($post_str);
			$str = "";
			foreach($post_str as $key => $val){
				if ($key != "sign") $str .= (!empty($str)) ? "&".$key."=".$val : $key."=".$val; else $sign = $val;
			}
			$str .= "&key=6F9he9U*cec4kjc168";
			
			Log::write(json_encode($_POST),'INFO');
			//echo $sign."<br>".$str."<br>".md5($str); exit;
	
			if ($sign == md5($str) && !empty($sign)){
				
				//认证用户合法性
				/*$token = str_replace("*","+",$post_str['token']);
				$account = base64_decode($post_str['account']);
				$account = json_decode($account, true);
				$arr = $this->getUid($token, $account);
				if ($arr!=1){
					echo -1;
					exit;
				}*/

				
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
	
	private function ipToInt($ip){
		 $iparr = explode('.',$ip);
		 $num = 0;
		 for($i=0;$i<count($iparr);$i++){
			 $num += intval($iparr[$i]) * pow(256,count($iparr)-($i+1));
		 }
		 return $num;
	}
	
	public function getUid($account_sign, $account) {

		$msg = 1;
	    if (!$account || !$account_sign) {
	        //$this->returnError(301, "account and accountSign must be needed");
			$msg = -1;
	    }
	   // echo $account."<br>".$account_sign."<br>";
	    $ret = $this->sign($account, $account_sign);
	    if (!$ret) {
	        //$this->returnError(301, "sign error sign: " . $account_sign);
			$msg = -1;
	    }
	    
	    $account = json_decode($account, true);
		print_r($account);
	    return $msg;
	}
	
	private function sign($input, $sign, $pub_key_file = 'Pay/htc/public_key.pem') {
	    if (empty($input) || empty($sign)) {
	        return false;
	    }
	    if (!is_file($pub_key_file)) {
	        return false;
	    }
	    
	    $pk = file_get_contents($pub_key_file);
		//echo $pk;
	    $res = openssl_get_publickey($pk);
		print_r($input);
		echo "<br>".$sign."<br>";
		echo $res;
	    $result = openssl_verify($input, base64_decode($sign), $res);
	    if ($res) {
	        openssl_free_key($res);
	    }
	    echo "<br>".$result."<br>"; 
	    return $result == 1 ? true : false;
	}
}