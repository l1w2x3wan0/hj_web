<?php
abstract class BaseAction extends Action{
	public function _initialize(){
			/*
			$r_url=$_SERVER['PATH_INFO'];
			$rarray=array();
			$rarray=explode("/",$r_url);
			if($rarray[2]==''){
				$rarray[2]='index';
			}
			if($rarray[1]==''){
				$rarray[1]='Index';
			}*/
			

			$link = U('Login/login');
			if(!isset($_SESSION['userid']) && empty($_SESSION['userid'])){
				$this->error('尚未登陆，请先登录',$link);
			}
			
			if(!isset($_SESSION['fenxi_js_power']) && empty($_SESSION['fenxi_js_power'])){
				//echo "***"; exit;
				$this->error('您没有该权限，请联系管理员',$link);
			}else{
				$js_power = json_decode($_SESSION['fenxi_js_power'], true);
				$lanmu_table = M('user_lanmu');
				$caozuo = array();
				//print_r($js_power); exit;
				//echo $m."**".$a."<br>";
				if ($_SESSION['fenxi_js_flag']=="1"){
					$list1 = $lanmu_table->where('lanmu_num=0')->order('lanmu_sort,id')->select();
					foreach($list1 as $key1 => $val1){
						$list1[$key1]['sub'] = array();
						$list2 = $lanmu_table->where('lanmu_num='.$val1['id'])->order('lanmu_sort,id')->select();
						$list1[$key1]['sub'] = $list2;
						foreach($list2 as $key2 => $val2){
							$str = $val2['id']."_power";
							$list1[$key1]['sub'][$key2]['url'] = U($val2['lanmu_m'].'/'.$val2['lanmu_a']);
							/*
							if ($val2['lanmu_m']==MODULE_NAME && $val2['lanmu_a']==ACTION_NAME){
								$caozuo['add'] = in_array('add',$js_power[$str]);
								$caozuo['edit'] = in_array('edit',$js_power[$str]);
								$caozuo['del'] = in_array('del',$js_power[$str]);
								$caozuo['pub'] = in_array('pub',$js_power[$str]);
								
								if ($js_power[$val2['id']]!="1"){
									$this->error('您没有该权限，请联系管理员',$link);
								}
							}*/
						}
					}
					$this->assign('show_lanmu',$list1);
					$caozuo['add'] = "1";
					$caozuo['edit'] = "1";
					$caozuo['del'] = "1";
					$caozuo['pub'] = "1";
				}else{
					$show_lanmu = array();
					$list1 = $lanmu_table->where('lanmu_num=0')->order('lanmu_sort,id')->select();
					$i = 0;
					foreach($list1 as $key1 => $val1){
						if ($js_power[$val1['id']]=="1"){
							$show_lanmu[$i]['id'] = $val1['id'];
							$show_lanmu[$i]['lanmu_name'] = $val1['lanmu_name'];
							$show_lanmu[$i]['lanmu_m'] = $val1['lanmu_m'];
							$show_lanmu[$i]['lanmu_a'] = $val1['lanmu_a'];
							$show_lanmu[$i]['lanmu_css'] = $val1['lanmu_css'];
							$show_lanmu[$i]['lanmu_num'] = $val1['lanmu_num'];
							$show_lanmu[$i]['status'] = $val1['status'];
							
							$show_lanmu[$i]['sub'] = array();
							$j = 0;
							$list2 = $lanmu_table->where('lanmu_num='.$val1['id'])->order('lanmu_sort,id')->select();
							foreach($list2 as $key2 => $val2){
								if ($js_power[$val2['id']]=="1"){
									$show_lanmu[$i]['sub'][$j]['id'] = $val2['id'];
									$show_lanmu[$i]['sub'][$j]['lanmu_name'] = $val2['lanmu_name'];
									$show_lanmu[$i]['sub'][$j]['lanmu_m'] = $val2['lanmu_m'];
									$show_lanmu[$i]['sub'][$j]['lanmu_a'] = $val2['lanmu_a'];
									$show_lanmu[$i]['sub'][$j]['lanmu_css'] = $val2['lanmu_css'];
									$show_lanmu[$i]['sub'][$j]['lanmu_num'] = $val2['lanmu_num'];
									$show_lanmu[$i]['sub'][$j]['status'] = $val2['status'];
									$show_lanmu[$i]['sub'][$j]['url'] = U($val2['lanmu_m'].'/'.$val2['lanmu_a']);
									
									$str = $val2['id']."_power";
									if ($val2['lanmu_m']==MODULE_NAME && $val2['lanmu_a']==ACTION_NAME){
										$caozuo['add'] = in_array('add',$js_power[$str]);
										$caozuo['edit'] = in_array('edit',$js_power[$str]);
										$caozuo['del'] = in_array('del',$js_power[$str]);
										$caozuo['pub'] = in_array('pub',$js_power[$str]);
									}
									$j++;
								}else{
									if ($val2['lanmu_m']==MODULE_NAME && $val2['lanmu_a']==ACTION_NAME){
										$this->error('您没有该权限，请联系管理员',$link);
									}
								}
							}
							$i++;
						}
					}
					$this->assign('show_lanmu',$show_lanmu);
				}
				$this->assign('caozuo',$caozuo);
				//$this->assign('js_power',$js_power);
				//$this->assign('js_flag',$_SESSION['js_flag']);
			}
			
			//获取基本配置
			$row = M("config");
			$list = $row->select();
			foreach ($list as $key => $val){
				define($val['config_name'], $val['config_value']);
			}

			//if($rarray[1].'-'.$rarray[2]!='User-login'){
				/*if(!isset($_SESSION['userid']) && empty($_SESSION['userid'])){
					$this->error('尚未登陆，请先登录',$link);
				}*/
				//var_dump($rarray[1].'-'.$rarray[2]);exit;
				/*if(C('wname')!=$_SESSION['wname']){
					//echo 1;exit;

					$this->error('尚未登陆，请先登录',$link);
				}*/
			//}
			//var_dump(C('wname'));exit;
			

			/*
			$purview_list = require 'purview.php';
			$current_action = $rarray[1].'-'.$rarray[2];
			//var_dump($purview_list[$rarray[1]].','.$rarray[1].'-'.$rarray[2].'|');
			//var_dump(strpos($purview_list[$rarray[1]],','.$rarray[1].'-'.$rarray[2].'|'));

			//var_dump($_SESSION);
			if(strpos($purview_list[$rarray[1]],$rarray[1].'-'.$rarray[2].'|')){
				if(!strpos($_SESSION['permissions'],$rarray[1].'-'.$rarray[2]) && $_SESSION['permissions']!='all'){
					$this->error('您没有该权限，请联系管理员');
				}
			}*/

			


    }
		
	//初始化缓存
	public function cache_add(){
		//重建短信配置
		$duanxin_config = array(
			'payMaxStatus' => intval(1),
			'messagePayDayMax' => intval(3000),
			'payStepStatus' => intval(0),
			'messagePayStep' => intval(1500)
		);
		S('verifyMessagePay', $duanxin_config);
		
		//建立配置缓存
		$table = M('payment_cache');
		$payment_cache = $table->field('payment_str,payment_value')->order('id')->select();
		foreach($payment_cache as $val){
			S($val['payment_str'], $val['payment_value']);
		}
		
		//建立游戏缓存
		$table = M('game');
		$game = $table->field('game_id,game_name,game_prifix')->order('game_id,id')->select();
		S('game', $game);
		foreach($game as $key => $val){
			//建立产品，配置缓存
			$tablename = $val['game_prifix']."_goods";
			$table = M($tablename);
			$goods = $table->field('goods_id,goods_name,goods_price,goods_details')->order('id')->select();
			S($tablename, $goods);
			
			$tablename = $val['game_prifix']."_sdk_package";
			$table = M($tablename);
			$sdk_package = $table->order('id')->select();
			foreach($sdk_package as $key1 => $val1){
				$sdk_package[$key1]['package_id'] = explode(",", $val1['package_id']);
			} 
			S($tablename, $sdk_package);
			
			$tablename = $val['game_prifix']."_sdk_config";
			$table = M($tablename);
			$sdk_config = $table->order('id')->select();
			S($tablename, $sdk_config);
			
			$tablename = $val['game_prifix']."_sdk_to_config";
			$table = M($tablename);
			$sdk_to_config = $table->order('id')->select();
			S($tablename, $sdk_to_config);
		}
		
	
	}
	
		
}

	