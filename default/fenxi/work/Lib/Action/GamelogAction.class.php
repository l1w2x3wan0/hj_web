<?php
// 员工管理的文件
/*
		员工状态：0 禁用状态
				  1 启用状态
				  2 删除状态
				  3 归档状态
*/
class GamelogAction extends Action {
  
	//员工登陆
	public function record(){
		
		if(!empty($_POST)){
			//写入日表
			$this->write_table();
			//$_POST['params'] = '{"bNum":8,"gold":21580178,"user_id":10337936,"ename":"myinfo","viplevel":2,"tname":1}';
			$post_str = json_decode($_POST['params'], true);
			$post_str['addtime'] = time();
			$table = "game_log.log".date("Ymd");
			$row = M($table);
			$result = $row->add($post_str);
			//dump($row->_sql());
			echo $result;
			exit;
			
		}else{
			
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
		$NAMES2 = "game_log";
		//echo $HOST;
		//exit;
		$conn = mysql_connect($HOST, $LOGIN, $PWD);
		mysql_query("SET NAMES utf8");
		mysql_select_db($NAMES2);
		
		$table_name = "log".date("Ymd");
		$sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
		  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
		  `user_id` int(10) DEFAULT '0' COMMENT '用户UID',
		  `tname` smallint(3) DEFAULT '0' COMMENT '1-大厅，2-商城 ，3-房间',
		  `viplevel` int(11) DEFAULT '0' COMMENT '用户VIP等级',
		  `gold` bigint(20) DEFAULT '0' COMMENT '用户携带金币数',
		  `bNum` int(11) DEFAULT '0' COMMENT '玩的局数',
		  `ename` varchar(20) DEFAULT '0' COMMENT '事件名',
		  `addtime` int(7) DEFAULT NULL COMMENT '添加时间',
		  PRIMARY KEY (`id`),
		  KEY `user_id` (`user_id`),
		  KEY `addtime` (`addtime`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
		//echo $sql;
		if (mysql_query($sql)){
			return true; 
			//echo "1";
		}else{
			return false;
			//echo "0";
		}
	}
	
}