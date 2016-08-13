<?php
// 接口管理的文件
class GameswitchAction extends InterbaseAction {
	
	//条件判断
	public function before() {
		
		return true;
	}
	
	//运行逻辑
	public function logic() {
		
		$platform = $this->response['platform'];
		$channel = $this->response['channel'];
		$version = $this->response['version'];
		if (empty($platform)) $platform = 0;
		if (empty($channel)) $channel = 0;
		if (empty($version)) $version = 0;

		//获取总开关，默认开启
		$game_switch = M(MYTABLE_PRIFIX."game_switch");
		//获取全平台默认配置
		$switch = $game_switch->where("platform=0 and channel=0 and version='0'")->select();
		$arr = array();
		foreach($switch as $key => $val){
			$arr[$val['keyname']] = $val['keyvalue'];
		}
		//获取平台默认配置
		$switch = $game_switch->where("platform=$platform and channel=0 and version='0'")->select();
		foreach($switch as $key => $val){
			$arr[$val['keyname']] = $val['keyvalue'];
		}
		//获取渠道默认配置
		$switch = $game_switch->where("platform=$platform and channel=$channel and version='0'")->select();
		foreach($switch as $key => $val){
			$arr[$val['keyname']] = $val['keyvalue'];
		}
		//获取渠道、版本默认配置
		$switch = $game_switch->where("platform=$platform and channel=$channel and version='$version'")->select();
		foreach($switch as $key => $val){
			$arr[$val['keyname']] = $val['keyvalue'];
		}
		
		return $this->returnData($arr);
	
	}
	
}