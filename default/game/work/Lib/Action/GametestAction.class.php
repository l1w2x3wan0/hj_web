<?php
// 接口管理的文件

class GametestAction extends Action {
	protected $Table_prifix = "kingflower.";
	

	public function writetxt(){
		set_time_limit(0);
		
		
		$url = ROOT_PATH."configtxt/gameconfig.bat";
		//生成压缩包
		import('ORG.Util.PhpToZip');
		$save_url = "/configtxt/zip";
		$save_name = "g".time()."_config.zip";
		$save_file = $save_url."/".$save_name;
		echo $url."**".$save_file; //exit;			
		//if (file_exists($save_file)) @unlink($save_file);
		$scandir = new HZip();
		$scandir->zipDir($url, $save_file);
		
		$showurl = (!file_exists($save_file)) ? "" : DB_HOST."/configtxt/zip/".$save_name;
		//$showurl = DB_HOST."/configtxt/gameconfig1.0.1.txt";
		$begin = array();
		$begin['ts'] = $tsnow;
		$begin['url'] = $showurl;
		$beginurl = ROOT_PATH."configtxt/start.txt";
		$result1 = file_put_contents($beginurl, json_encode($begin));			
		
		if ($result1 && $result2){
			echo 1;
		}else{
			echo -1;
		}
	}
	
}