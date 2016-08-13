<?php
// 接口管理的文件

class VersionAction extends Action {
	protected $Table_prifix = 'ver_'; 
	protected $byid = '103'; 
	protected $byname = '宝贝猜拳'; 
	protected $By_tpl = 'Ver'; 
	
	public function ver(){
		//$starttime = explode(' ',microtime());
		header("Content-type:text/html;charset=utf-8");
		$result = array();
		$host = $_GET['host'];
		$oVersion = $_GET['oVersion'];
		$cVersion = $_GET['cVersion'];
		$channel = $_GET['channel'];
		
		//判断获取哪个配置
		//判断配置版本是否低于后台最高版本
		//echo $cVersion."_".S("Max_hot_version");
		$Max_hot_version = S("Max_hot_version");
		if ($cVersion < $Max_hot_version){
			$str21 = $oVersion."_".$Max_hot_version."_src";
			$str22 = $oVersion."_".$Max_hot_version."_flag";
			$str23 = $oVersion."_".$Max_hot_version."_size";
			$str24 = $oVersion."_".$Max_hot_version."_status";
			$str25 = $oVersion."_".$Max_hot_version."_channel";
			
			if (S($str21)==""){
				//没有匹配到获取最低版本的更新包
				$str21 = S("Min_hot_version")."_".$Max_hot_version."_src";
				$str22 = S("Min_hot_version")."_".$Max_hot_version."_flag";
				$str23 = S("Min_hot_version")."_".$Max_hot_version."_size";
				$str24 = S("Min_hot_version")."_".$Max_hot_version."_status";
				$str25 = S("Min_hot_version")."_".$Max_hot_version."_channel";
			}
			//echo $str21."*".S($str24)."--<br>";
			if (S($str24)=="0"){
				//所有渠道不更新		
				$flag1 = 0;
			}elseif (S($str24)=="1"){
				//所有渠道更新		
				$flag1 = 1;
			}elseif (S($str24)=="2"){
				//部分更新
				$flag1 = 0;
				if (S($str25)!=""){
					$arr = explode(",", S($str25));
					if (in_array($channel, $arr)){$flag1 = 1;}
				}
			}

			if ($flag1 == 1){
				$result['info']['hot_src'] = S($str21);
				$result['info']['hot_flag'] = (S($str22)=="1") ? true : false;
				$result['info']['hot_size'] = (int)S($str23);	
			}else{
				$result['info']['hot_src'] = false;
				$result['info']['hot_flag'] = false;
				$result['info']['hot_size'] = 0;	
			}
		}else{
			$result['info']['hot_src'] = false;
			$result['info']['hot_flag'] = false;
			$result['info']['hot_size'] = 0;
		}
		//print_r($result); exit;
		//S("Max_full_version",null);
		//判断配置版本是否低于后台最高整包版本
		$Max_full_version = S("Max_full_version");
		$Max_full_src = S("Max_full_src");
		$str11 = "FULL_".$host."_".$Max_full_version."_flag";
		$str12 = "FULL_".$host."_".$Max_full_version."_status";
		$str13 = "FULL_".$host."_".$Max_full_version."_channel";
		//echo $host."_".$Max_full_version;
		if ($host < $Max_full_version){
			if (S($str11)==""){
				$flag2 = 1;
			}else{
				if (S($str12)=="0"){
					//所有渠道不更新		
					$flag2 = 0;
				}elseif (S($str12)=="1"){
					//所有渠道更新		
					$flag2 = 1;
				}elseif (S($str12)=="2"){
					//部分更新
					$flag2 = 0;
					if (S($str13)!=""){
						$arr2 = explode(",", S($str13));
						if (in_array($channel, $arr2)){$flag2 = 1;}
					}
				}
			}
			//echo $flag2."**".$Max_full_src;
			if ($flag2 == 1){
				$result['info']['strong_src'] = $Max_full_src;
				$result['info']['strong_flag'] = (S($str11)=="1") ? true : false;
			}else{
				$result['info']['strong_src'] = false;
				$result['info']['strong_flag'] = false;
			}
		}else{
			$result['info']['strong_src'] = false;
			$result['info']['strong_flag'] = false;
		}

		
		
		if (S($str11)!="" || S($str21)!=""){
			$result['code'] = 0;
			$result['info']['Max_hot_version'] = $Max_hot_version;
			$result['info']['Max_full_version'] = $Max_full_version;
			//$result['info']['strong_src'] = S($str11);
			//$result['info']['strong_flag'] = S($str12);
		}else{
			$result['code'] = -1;
			$result['info'] = "版本号有误";
		}
		
		echo json_encode($result);
	}
	
	
	
	
}