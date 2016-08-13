<?php
// 员工管理的文件
/*
		员工状态：0 禁用状态
				  1 启用状态
				  2 删除状态
				  3 归档状态
*/
class SperkAction extends Action {
  
	//员工登陆
	public function index(){
		
		//$_POST = 1;
		if(!empty($_POST)){
			//写入日表
			$this->write_table();
			
			//$params = '{"type":1,"ver":"1.0.6","uid":10337931,"err":"ffregrgrg"}';
			
			
			//echo "****";
			//print_r($post_str);
			$data = array();
			if (!empty($_POST['params'])){
				$post_str = json_decode($_POST['params'], true);
				$data['uid'] = addslashes($post_str['uid']);
				$data['ver'] = addslashes($post_str['ver']);
				$data['type'] = !empty($post_str['type']) ? addslashes($post_str['type']) : 0;
				$data['errmessage'] = addslashes(trim($post_str['err']));
			}else{
				$uid = I("uid");
				$ver = I("ver");
				$type = I("type");
				$errmessage = I("errmessage");
				
				$data['uid'] = addslashes($uid);
				$data['ver'] = addslashes($ver);
				$data['type'] = !empty($type) ? addslashes($type) : 0;
				$data['errmessage'] = addslashes(trim($errmessage));
			}
			if ($data['type'] == "3"){				
				$show1 = explode(":", $data['errmessage']);				
				$data['roomid'] = $show1[0];				
				$data['tableno'] = $show1[1];				
				$data['message'] = $show1[2];
			}else if ($data['type'] == "4"){				
				$show1 = explode(":", $data['errmessage']);				
				$data['roomid'] = '';				
				$data['tableno'] = $show1[0];				
				$data['message'] = $show1[1];			
			}
			$data['addtime'] = time();
			
			$table = "sperk_log.log".date("Ym");
			$row = M($table);
			if (!empty($data['uid']) && !empty($data['errmessage'])){
				$result = $row->add($data);
			}else{
				$ip = get_client_ip();
				
				$data = array();
				$data['uid'] = addslashes($post_str['uid']);
				$data['ver'] = addslashes($post_str['ver']);
				$data['type'] = !empty($post_str['type']) ? addslashes($post_str['type']) : 0;
				$data['errmessage'] = $ip."**".$uid."**".$ver."**".$type."**".$errmessage."**".$_POST['params'];
				$data['addtime'] = time();
				$result = $row->add($data);
			}
			//dump($row->_sql());
			echo $result;
			exit;
			
		}else{
			$this->write_table();
			
			echo "页面不存在";
			exit;
		}
		
	}
	
	//创建日表
	public function write_table(){
		$HOST = C('DB_HOST');
		$DB_PORT = C('DB_PORT');
		if (!empty($DB_PORT)) $HOST .= ":".$DB_PORT;
		$LOGIN = C('DB_USER');
		$PWD =  C('DB_PWD');
		$NAMES2 = "sperk_log";
		//echo $HOST;
		//exit;
		$conn = mysql_connect($HOST, $LOGIN, $PWD);
		mysql_query("SET NAMES utf8");
		mysql_select_db($NAMES2);
		
		$table_name = "log".date("Ym");
		$sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
			  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
			  `uid` int(11) DEFAULT '0',
			  `type` tinyint(3) DEFAULT '0',
			  `ver` varchar(50) DEFAULT '0',
			  `errmessage` text,
			  `addtime` int(7) DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `uid` (`uid`),
			  KEY `type` (`type`),
			  KEY `roomid` (`roomid`),
			  KEY `tableno` (`tableno`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		//echo $sql;
		if (mysql_query($sql)){
			//echo "1";
			return true; 
			
		}else{
			//echo "0";
			return false;
			
		}
	}
	
}