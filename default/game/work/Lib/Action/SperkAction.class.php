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
		
		if(!empty($_POST)){
			//写入日表
			$this->write_table();
			//$_POST['params'] = '{"bNum":8,"gold":21580178,"user_id":10337936,"ename":"myinfo","viplevel":2,"tname":1}';
			$data1 = array();
			$data1['uid'] = addslashes($_POST['uid']);
			$data1['ver'] = addslashes($_POST['ver']);
			$data1['type'] = !empty($_POST['type']) ? addslashes($_POST['type']) : 0;
			$data1['errmessage'] = addslashes(trim($_POST['err']));
			$data1['addtime'] = time();
			if ($_POST['type'] == 3){				
				$show1 = explode(":", $_POST['err']);				
				$data1['roomid'] = $show1[0];				
				$data1['tableno'] = $show1[1];				
				$data1['message'] = $show1[2];
			}else if ($_POST['type'] == 4){				
				$show1 = explode(":", $_POST['err']);				
				$data1['roomid'] = '';				
				$data1['tableno'] = $show1[0];				
				$data1['message'] = $show1[1];			
			}
			
			$table = "sperk_log.log".date("Ym");
			$row = M($table);
			$result = $row->add($data1);
			//dump($row->_sql());
			echo $result;
			exit;
			
		}else{
			//$this->write_table();
			
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
			  KEY `uid` (`uid`)
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