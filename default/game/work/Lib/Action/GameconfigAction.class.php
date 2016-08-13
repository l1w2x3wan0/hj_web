<?php
// 接口管理的文件

class GameconfigAction extends Action {
	protected $Table_prifix = "kingflower.";
	
	public function index(){
		//获取配置
		$gamebase = S("GAMEBASE_CONFIG");
		//$vip = S("USERVIP_CONFIG");
		//$goods = S("MALL_GOODS_DATA");
		
		if (empty($gamebase)){
			$Table = $this->Table_prifix."dynamic_config";
			$res = M($Table);
			$row = $res->select();
			$pubtext = array('msg' => $row,
							 'ts' => time());
			S("GAMEBASE_CONFIG", json_encode($pubtext, JSON_UNESCAPED_UNICODE));	
			$gamebase = S("GAMEBASE_CONFIG");		
		}
		
		$xtlb = json_decode($gamebase, true);
		$ts = empty($_GET['ts']) ? 0 : $_GET['ts'];
		
		if ($xtlb['ts'] > $ts){
			echo $gamebase;
		}else{
			echo -1;
		}				
	}	
	
	public function vip(){
		//获取配置
		$vip = S("USERVIP_CONFIG");
		//echo $vip; 
		if (empty($vip)){
			$Table = $this->Table_prifix."profile_vip_level_configure";
			$res = M($Table);
			$row = $res->order("viplevel")->select();
			$pubtext = array('msg' => $row,
							 'ts' => time());
			S("USERVIP_CONFIG", json_encode($pubtext, JSON_UNESCAPED_UNICODE));	
			$vip = S("USERVIP_CONFIG");		
		}
		
		$xtlb = json_decode($vip, true);
		$ts = empty($_GET['ts']) ? 0 : $_GET['ts'];
		
		if ($xtlb['ts'] > $ts){
			echo $vip;
		}else{
			echo -1;
		}				
	}

	public function goods(){
		//获取配置
		$config = S("MALL_GOODS_DATA");
		//echo $vip; 
		/*
		if (empty($vip)){
			$Table = $this->Table_prifix."profile_vip_level_configure";
			$res = M($Table);
			$row = $res->order("viplevel")->select();
			$pubtext = array('msg' => $row,
							 'ts' => time());
			S("MALL_GOODS_DATA", json_encode($pubtext, JSON_UNESCAPED_UNICODE));	
			$config = S("MALL_GOODS_DATA");		
		}*/
		
		$xtlb = json_decode($config, true);
		$ts = empty($_GET['ts']) ? 0 : $_GET['ts'];
		
		if ($xtlb['ts'] > $ts){
			echo $config;
		}else{
			echo -1;
		}				
	}

	public function quickgoods(){
		//获取配置
		$config = S("QUICK_GOODS_DATA");
		//echo $vip; 
		/*
		if (empty($vip)){
			$Table = $this->Table_prifix."profile_vip_level_configure";
			$res = M($Table);
			$row = $res->order("viplevel")->select();
			$pubtext = array('msg' => $row,
							 'ts' => time());
			S("MALL_GOODS_DATA", json_encode($pubtext, JSON_UNESCAPED_UNICODE));	
			$config = S("MALL_GOODS_DATA");		
		}*/
		
		$xtlb = json_decode($config, true);
		$ts = empty($_GET['ts']) ? 0 : $_GET['ts'];
		
		if ($xtlb['ts'] > $ts){
			echo $config;
		}else{
			echo -1;
		}				
	}
	
	public function lottery(){
		//获取配置
		$config = S("LOTTERY_DATA");
		//echo $vip; 
		/*
		if (empty($vip)){
			$Table = $this->Table_prifix."profile_vip_level_configure";
			$res = M($Table);
			$row = $res->order("viplevel")->select();
			$pubtext = array('msg' => $row,
							 'ts' => time());
			S("MALL_GOODS_DATA", json_encode($pubtext, JSON_UNESCAPED_UNICODE));	
			$config = S("MALL_GOODS_DATA");		
		}*/
		
		$xtlb = json_decode($config, true);
		$ts = empty($_GET['ts']) ? 0 : $_GET['ts'];
		
		if ($xtlb['ts'] > $ts){
			echo $config;
		}else{
			echo -1;
		}				
	}
	
	public function signin(){
		//获取配置
		$config = S("MALL_SIGNIN_DATA");
		echo $config."**"; exit; 
		$xtlb = json_decode($config, true);
		$ts = empty($_GET['ts']) ? 0 : $_GET['ts'];
		
		if ($xtlb['ts'] > $ts){
			echo $config;
		}else{
			echo -1;
		}				
	}
	
	public function room(){
		//获取配置
		$config = S("GAMEBASE_ROOM");
		echo $config."**"; exit; 
		$xtlb = json_decode($config, true);
		$ts = empty($_GET['ts']) ? 0 : $_GET['ts'];
		
		if ($xtlb['ts'] > $ts){
			echo $config;
		}else{
			echo -1;
		}				
	}
	
	public function room_new(){
		//获取配置
		$config = S("GAMEBASE_ROOM_NEW");
		echo $config."**"; exit; 
		$xtlb = json_decode($config, true);
		$ts = empty($_GET['ts']) ? 0 : $_GET['ts'];
		
		if ($xtlb['ts'] > $ts){
			echo $config;
		}else{
			echo -1;
		}				
	}
	
	public function room_channel(){
		//获取配置
		$config = S("GAMEBASE_ROOM_CHANNEL");
		echo $config."**"; exit; 
		$xtlb = json_decode($config, true);
		$ts = empty($_GET['ts']) ? 0 : $_GET['ts'];
		
		if ($xtlb['ts'] > $ts){
			echo $config;
		}else{
			echo -1;
		}				
	}
	
	public function gift(){
		//获取配置
		$config = S("GAMEBASE_GIFT");
		//echo $vip; 
		/*
		if (empty($vip)){
			$Table = $this->Table_prifix."profile_vip_level_configure";
			$res = M($Table);
			$row = $res->order("viplevel")->select();
			$pubtext = array('msg' => $row,
							 'ts' => time());
			S("MALL_GOODS_DATA", json_encode($pubtext, JSON_UNESCAPED_UNICODE));	
			$config = S("MALL_GOODS_DATA");		
		}*/
		
		$xtlb = json_decode($config, true);
		$ts = empty($_GET['ts']) ? 0 : $_GET['ts'];
		
		if ($xtlb['ts'] > $ts){
			echo $config;
		}else{
			echo -1;
		}				
	}
	
	public function bankswitch(){
		//获取配置
		$config = S("GAMEBASE_YHKG");
		
		$yhkg = json_decode($config, true);
		$ts = empty($_GET['ts']) ? 0 : $_GET['ts'];
		
		if ($yhkg['ts'] > $ts){
			echo $config;
		}else{
			echo -1;
		}				
	}
	
	public function logininter(){
		//获取配置
		$config = S("GAMEBASE_LOGININTER");
		
		$logininter = json_decode($config, true);
		$ts = empty($_GET['ts']) ? 0 : $_GET['ts'];
		
		if ($logininter['ts'] > $ts){
			echo $config;
		}else{
			echo -1;
		}				
	}
	
	public function writetxt(){

		//echo "OK";
        set_time_limit(0);

		
		$txt = array();
		$tsnow = 0;
		$gamebase = S("GAMEBASE_CONFIG");
		$info = json_decode($gamebase, true);
		$txt['base'] = $info['msg'];
		if ($info['ts'] > $tsnow) $tsnow = $info['ts'];
		
		$vip = S("USERVIP_CONFIG");
		$info = json_decode($vip, true);
		$txt['vip'] = $info['msg'];
		if ($info['ts'] > $tsnow) $tsnow = $info['ts'];
		
		$goods = S("MALL_GOODS_DATA");
		$info = json_decode($goods, true);
		$txt['goods'] = $info['msg'];
		if ($info['ts'] > $tsnow) $tsnow = $info['ts'];
		
		$quickgoods = S("QUICK_GOODS_DATA");
		$info = json_decode($quickgoods, true);
		$txt['quick'] = $info['msg'];
		if ($info['ts'] > $tsnow) $tsnow = $info['ts'];
		
		$quicknewgoods = S("QUICKNEW_GOODS_DATA");
		$info = json_decode($quicknewgoods, true);
		$txt['quicknew'] = $info['msg'];
		if ($info['ts'] > $tsnow) $tsnow = $info['ts'];
		//print_r($txt['quicknew']); exit;
		
		$tgoods = S("MALL_TGOODS_DATA");
		$info = json_decode($tgoods, true);
		$txt['tgoods'] = $info['msg'];
		if ($info['ts'] > $tsnow) $tsnow = $info['ts'];
		
		$igoods = S("MALL_GOODS_IOS_DATA");
		$info = json_decode($igoods, true);
		$txt['iosgoods'] = $info['msg'];
		if ($info['ts'] > $tsnow) $tsnow = $info['ts'];
		
		$iqgoods = S("MALL_QGOODS_IOS_DATA");
		$info = json_decode($iqgoods, true);
		$txt['iosqgoods'] = $info['msg'];
		if ($info['ts'] > $tsnow) $tsnow = $info['ts'];
		
		$itgoods = S("MALL_TGOODS_IOS_DATA");
		$info = json_decode($itgoods, true);
		$txt['iostgoods'] = $info['msg'];
		if ($info['ts'] > $tsnow) $tsnow = $info['ts'];
		
		$lottery = S("LOTTERY_DATA");
		$info = json_decode($lottery, true);
		$txt['lottery'] = $info['msg'];
		if ($info['ts'] > $tsnow) $tsnow = $info['ts'];
		
		$signin = S("MALL_SIGNIN_DATA");
		$info = json_decode($signin, true);
		$txt['base']['login'] = $info['msg'];
		if ($info['ts'] > $tsnow) $tsnow = $info['ts'];
		
		$room = S("GAMEBASE_ROOM");
		$info = json_decode($room, true);
		$txt['room'] = $info['msg'];
		if ($info['ts'] > $tsnow) $tsnow = $info['ts'];
		
		$room = S("GAMEBASE_ROOM_NEW");
		$info = json_decode($room, true);
		$txt['room_new'] = $info['msg'];
		if ($info['ts'] > $tsnow) $tsnow = $info['ts'];
		
		$room_channel = S("GAMEBASE_ROOM_CHANNEL");
		$info = json_decode($room_channel, true);
		$txt['room_channel'] = array('channel'=>$info['channel'], 'data'=>$info['msg']);
		if ($info['ts'] > $tsnow) $tsnow = $info['ts'];
		
		$gift = S("GAMEBASE_GIFT");
		$info = json_decode($gift, true);
		$txt['gift'] = $info['msg'];
		if ($info['ts'] > $tsnow) $tsnow = $info['ts'];
		
		$yhkg = S("GAMEBASE_YHKG");
		$info = json_decode($yhkg, true);
		$txt['yhkg'] = $info['msg'];
		if ($info['ts'] > $tsnow) $tsnow = $info['ts'];
		
		$logininter = S("GAMEBASE_LOGININTER");
		$info = json_decode($logininter, true);
		$txt['logininter'] = $info['msg'];
		if ($info['ts'] > $tsnow) $tsnow = $info['ts'];
		
		//奖券配置
		$jq = S("YHJQ");
		$info = json_decode($jq, true);
		$txt['jiangquan'] = $info['msg'];
		if ($info['ts'] > $tsnow) $tsnow = $info['ts'];
		
		//SVIP配置
		$jq_svip = S("YHJQ_SVIP");
		$info = json_decode($jq_svip, true);
		$txt['svip'] = $info['msg'];
		if ($info['ts'] > $tsnow) $tsnow = $info['ts'];
		
		//登陆IP管理
		$loginip = S("GAMEBASE_LOGINIP");
		$info = json_decode($loginip, true);
		$txt['loginip'] = $info['msg'];
		if ($info['ts'] > $tsnow) $tsnow = $info['ts'];
		
		//百人场配置
		$brc_all_config = S("BRC_ALL_CONFIG");
		$info = json_decode($brc_all_config, true);
		$txt['brc_all_config'] = $info['msg'];
		if ($info['ts'] > $tsnow) $tsnow = $info['ts'];
		
		$txtinfo = json_encode($txt);
		$url = ROOT_PATH."configtxt/config/config.txt";
		$zipurl = ROOT_PATH."configtxt/config";
        //echo $url."<br>";
		$result2 = file_put_contents($url, $txtinfo);

		//echo "**".$result2."**"; 
        //echo "**".$result2."**";
        //EXIT;

		//生成压缩包
		import('ORG.Util.PhpToZip');
		$save_url = ROOT_PATH."configtxt/zip";
		$save_name = "g".time()."_config.zip";
		$save_file = $save_url."/".$save_name;
		//echo $url."**".$save_file."<br>"; 
		//exit;			
		//if (file_exists($save_file)) @unlink($save_file);
		$scandir = new HZip();
		$scandir->zipDir($zipurl, $save_file);
		//echo "kkk"; exit;
		$showurl = (!file_exists($save_file)) ? "" : DB_HOST."/configtxt/zip/".$save_name;
		//$showurl = DB_HOST."/configtxt/gameconfig1.0.1.txt";
		$begin = array();
		$begin['ts'] = $tsnow;
		$begin['url'] = $showurl;
		$beginurl = ROOT_PATH."configtxt/start.txt";
		$result1 = file_put_contents($beginurl, json_encode($begin));			
		//echo $beginurl."<br>";
		if ($result1 && $result2){
			echo 1;
		}else{
			echo -1;
		}
	}
	
}