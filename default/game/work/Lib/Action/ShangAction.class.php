<?php
// 商品管理的文件

class ShangAction extends BaseAction {
	protected $By_tpl = 'Shang'; 
	protected $Table_prifix = MYTABLE_PRIFIX; 
	
	//待审核商品列表
	public function waitshang(){
	
		$sql0 = "";
		$Table = "user_record";
		$rowlist = M($Table);
		import('ORG.Util.Page');
		
		if ($_SESSION['js_flag']!="1"){$sql0 .= " and username='".$_SESSION['username']."'";}
		
		$count = $rowlist->where("cate in (101,102,103,105,141,142,143,144,145,146,147,148,149) $sql0")->count('id');
		$Page       = new Page($count,PAGE_SHOW);//实例化分页类传入总记录数和每页显示的记录数		
		$pageshow       = $Page->show();// 分页显示输出
		
		$list = $rowlist->where("cate in (101,102,103,105,141,142,143,144,145,146,147,148,149) $sql0")->order('flag,id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($rowlist->_sql());
		foreach($list as $key=>$value){
			$show = json_decode($value['logs'], true);
			
			//$list[$key]['cate'] = ($value['cate']=="101") ? "新增商品" : "修改商品";
			if ($value['cate']=="101"){
				$list[$key]['showcate'] = "新增商品";
			}else if ($value['cate']=="102"){
				$list[$key]['showcate'] = "修改商品";
			}else if ($value['cate']=="103"){
				$list[$key]['showcate'] = "删除商品";
			}else if ($value['cate']=="141"){
				$list[$key]['showcate'] = "新增IOS商品";
			}else if ($value['cate']=="142"){
				$list[$key]['showcate'] = "修改IOS商品";
			}else if ($value['cate']=="143"){
				$list[$key]['showcate'] = "删除IOS商品";
			}else if ($value['cate']=="144"){
				$list[$key]['showcate'] = "新增IOS快充商品";
			}else if ($value['cate']=="145"){
				$list[$key]['showcate'] = "修改IOS快充商品";
			}else if ($value['cate']=="146"){
				$list[$key]['showcate'] = "删除IOS大厅商品";
			}else if ($value['cate']=="147"){
				$list[$key]['showcate'] = "新增IOS大厅商品";
			}else if ($value['cate']=="148"){
				$list[$key]['showcate'] = "修改IOS大厅商品";
			}else if ($value['cate']=="149"){
				$list[$key]['showcate'] = "删除IOS快充商品";
			}else if ($value['cate']=="105"){
				$list[$key]['showcate'] = "修改快速说明";
				$list[$key]['quickpaytips'] = $show['quickpaytips'];
			}

			$list[$key]['GoodsID'] = $show['GoodsID'];
			$list[$key]['IsQuickPay'] = ($show['IsQuickPay']=="1") ? "快充" : "非快充";
			$list[$key]['GoodsName'] = $show['GoodsName'];
			$list[$key]['RoomID'] = $show['RoomID'];
			$list[$key]['ChannelID'] = $show['ChannelID'];
			$list[$key]['GoldNum'] = $show['GoldNum'];
			$list[$key]['GiveGoldNum'] = $show['GiveGoldNum'];
			$list[$key]['GoodsPrice'] = $show['GoodsPrice'];
			$list[$key]['GoodsValue'] = $show['GoodsValue'];
			$list[$key]['DisplayOrder'] = $show['DisplayOrder'];
			$list[$key]['IsFirstPayGive'] = ($show['IsFirstPayGive']=="1") ? "<font color='#FF0000'>是</font>" : "否";
			$list[$key]['IsEnableSms'] = ($show['IsEnableSms']=="1") ? "<font color='#FF0000'>是</font>" : "否";
			$list[$key]['IsFirstPay'] = ($show['IsFirstPay']=="1") ? "<font color='#FF0000'>是</font>" : "否";
			$list[$key]['Enable'] = ($show['Enable']=="1") ? "<font color='#FF0000'>是</font>" : "否";
			$list[$key]['IsPayed'] = ($show['IsPayed']=="1") ? "<font color='#FF0000'>是</font>" : "否";
			$list[$key]['MallDisplay'] = ($show['MallDisplay']=="1") ? "<font color='#FF0000'>是</font>" : "否";
			
			if ($value['flag']=="0"){
				$list[$key]['flagshow'] = "<font color='#FF0000'>待审核</font>";
			}else if ($value['flag']=="1"){
				$list[$key]['flagshow'] = "审核通过";
			}else if ($value['flag']=="2"){
				$list[$key]['flagshow'] = "审核取消";
			}
		}
		
		//增加操作记录
		$logs = C('WAITSHANG_MSG_RECORD');
		$remark = "";
		adminlog($logs, $remark);
		
		$this->assign('left_css',"7");
		$this->assign('list',$list);
		$this->assign('pageshow',$pageshow);
		
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl.":waitshang";
		$this->display($lib_display);
	}
	
	//待审核商品列表
	public function waitshang_show(){
		$id = I("id");
		if (empty($id)){
			$this->error('输入有误');
			exit;
		}

		$row = M("user_record");
		$info = $row->where("id=".$id)->find();
		$goods = json_decode($info['logs'], true);
		
		$act = I("act");
		if (!empty($act)){
			$Table = $this->Table_prifix."profile_mall_goods_data";
			$add_table = M($Table);
			if ($act == "on"){
				if ($info['cate'] == "101"){
					//添加商品
					$result = $add_table->add($goods);
					
					//生成缓存
					$configrow = $add_table->where('IsQuickPay=3 and platform=0 and Enable=1')->order("DisplayOrder,GoodsID")->select();
					$arr = array();
					foreach($configrow as $key => $val){
						$arr[$key]['a'] = (int)$val['GoodsID'];            
						//$arr[$key]['b'] = (int)$val['DisplayOrder'];            
						//$arr[$key]['c'] = (int)$val['Enable'];        
						$arr[$key]['d'] = (int)$val['GoldNum'];        
						$arr[$key]['e'] = (int)$val['GiveGoldNum'];       
						$arr[$key]['f'] = (int)$val['Diamond'];   
						$arr[$key]['g'] = (int)$val['GiveDiamond'];     
						$arr[$key]['h'] = $val['GoodsName'];
						$arr[$key]['h2'] = $val['GoodsName_D']; 		
						$arr[$key]['i'] = $val['GoodsDescribe'];         
						$arr[$key]['j'] = (int)$val['RoomID'];        
						$arr[$key]['k'] = (int)$val['MallDisplay'];          
						$arr[$key]['l'] = (int)$val['GoodsPrice'];        
						$arr[$key]['m'] = (int)$val['GoodsValue'];    
						//$arr[$key]['n'] = (int)$val['IsFirstPayGive'];        
						//$arr[$key]['o'] = (int)$val['IsEnableSms'];    
						$arr[$key]['p'] = (int)$val['IsFirstPay'];        
						//$arr[$key]['q'] = (int)$val['ChannelID'];    
						$arr[$key]['r'] = $val['Remarks'];        
						$arr[$key]['s'] = $val['RemarksDesc'];    
						$arr[$key]['t'] = (int)$val['IsPayed'];        
						//$arr[$key]['u'] = (int)$val['IsQuickPay'];
						$arr[$key]['v'] = (int)$val['Ticket'];						
					}
					//dump($add_table->_sql());
					$pubtext = array('msg' => $arr,
									 'ts' => time());
					S("MALL_GOODS_DATA", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
					
					//生成快速商品
					$configrow = $add_table->where('IsQuickPay=1 and platform=0 and Enable=1')->order("DisplayOrder,GoodsID")->select();
					$arr = array();
					foreach($configrow as $key => $val){
						$arr[$key]['a'] = (int)$val['GoodsID'];            
						//$arr[$key]['b'] = (int)$val['DisplayOrder'];            
						//$arr[$key]['c'] = (int)$val['Enable'];        
						$arr[$key]['d'] = (int)$val['GoldNum'];        
						$arr[$key]['e'] = (int)$val['GiveGoldNum'];       
						$arr[$key]['f'] = (int)$val['Diamond'];   
						$arr[$key]['g'] = (int)$val['GiveDiamond'];     
						$arr[$key]['h'] = $val['GoodsName']; 
						$arr[$key]['h2'] = $val['GoodsName_D'];
						$arr[$key]['i'] = $val['GoodsDescribe'];         
						$arr[$key]['j'] = (int)$val['RoomID'];        
						$arr[$key]['k'] = (int)$val['MallDisplay'];          
						$arr[$key]['l'] = (int)$val['GoodsPrice'];        
						$arr[$key]['m'] = (int)$val['GoodsValue'];    
						//$arr[$key]['n'] = (int)$val['IsFirstPayGive'];        
						//$arr[$key]['o'] = (int)$val['IsEnableSms'];    
						$arr[$key]['p'] = (int)$val['IsFirstPay'];        
						//$arr[$key]['q'] = (int)$val['ChannelID'];    
						$arr[$key]['r'] = $val['Remarks'];        
						$arr[$key]['s'] = $val['RemarksDesc'];    
						$arr[$key]['t'] = (int)$val['IsPayed'];
						$arr[$key]['v'] = (int)$val['Ticket'];				
						//$arr[$key]['u'] = (int)$val['IsQuickPay']; 
						
					}
					//dump($add_table->_sql());
					$pubtext = array('msg' => $arr,
									 'ts' => time());
					S("QUICK_GOODS_DATA", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
					
					//生成快速商品2
					$configrow = $add_table->where('IsQuickPay=4 and platform=0 and Enable=1')->order("DisplayOrder,GoodsID")->select();
					$arr = array();
					foreach($configrow as $key => $val){
						$arr[$key]['a'] = (int)$val['GoodsID'];            
						//$arr[$key]['b'] = (int)$val['DisplayOrder'];            
						//$arr[$key]['c'] = (int)$val['Enable'];        
						$arr[$key]['d'] = (int)$val['GoldNum'];        
						$arr[$key]['e'] = (int)$val['GiveGoldNum'];       
						$arr[$key]['f'] = (int)$val['Diamond'];   
						$arr[$key]['g'] = (int)$val['GiveDiamond'];     
						$arr[$key]['h'] = $val['GoodsName']; 
						$arr[$key]['h2'] = $val['GoodsName_D'];
						$arr[$key]['i'] = $val['GoodsDescribe'];         
						$arr[$key]['j'] = (int)$val['RoomID'];        
						$arr[$key]['k'] = (int)$val['MallDisplay'];          
						$arr[$key]['l'] = (int)$val['GoodsPrice'];        
						$arr[$key]['m'] = (int)$val['GoodsValue'];    
						//$arr[$key]['n'] = (int)$val['IsFirstPayGive'];        
						//$arr[$key]['o'] = (int)$val['IsEnableSms'];    
						$arr[$key]['p'] = (int)$val['IsFirstPay'];        
						//$arr[$key]['q'] = (int)$val['ChannelID'];    
						$arr[$key]['r'] = $val['Remarks'];        
						$arr[$key]['s'] = $val['RemarksDesc'];    
						$arr[$key]['t'] = (int)$val['IsPayed'];
						$arr[$key]['v'] = (int)$val['Ticket'];				
						//$arr[$key]['u'] = (int)$val['IsQuickPay']; 
						
					}
					//dump($add_table->_sql());
					$pubtext = array('msg' => $arr,
									 'ts' => time());
					S("QUICKNEW_GOODS_DATA", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
					
					//生成大厅商品
					$configrow = $add_table->where('IsQuickPay=2 and platform=0 and Enable=1')->order("DisplayOrder,GoodsID")->select();
					$arr = array();
					foreach($configrow as $key => $val){
						$arr[$key]['a'] = (int)$val['GoodsID'];            
						//$arr[$key]['b'] = (int)$val['DisplayOrder'];            
						//$arr[$key]['c'] = (int)$val['Enable'];        
						$arr[$key]['d'] = (int)$val['GoldNum'];        
						$arr[$key]['e'] = (int)$val['GiveGoldNum'];       
						$arr[$key]['f'] = (int)$val['Diamond'];   
						$arr[$key]['g'] = (int)$val['GiveDiamond'];     
						$arr[$key]['h'] = $val['GoodsName']; 
						$arr[$key]['h2'] = $val['GoodsName_D'];
						$arr[$key]['i'] = $val['GoodsDescribe'];         
						$arr[$key]['j'] = (int)$val['RoomID'];        
						$arr[$key]['k'] = (int)$val['MallDisplay'];          
						$arr[$key]['l'] = (int)$val['GoodsPrice'];        
						$arr[$key]['m'] = (int)$val['GoodsValue'];    
						//$arr[$key]['n'] = (int)$val['IsFirstPayGive'];        
						//$arr[$key]['o'] = (int)$val['IsEnableSms'];    
						$arr[$key]['p'] = (int)$val['IsFirstPay'];        
						//$arr[$key]['q'] = (int)$val['ChannelID'];    
						$arr[$key]['r'] = $val['Remarks'];        
						$arr[$key]['s'] = $val['RemarksDesc'];    
						$arr[$key]['t'] = (int)$val['IsPayed'];        
						$arr[$key]['v'] = (int)$val['Ticket'];	
						//$arr[$key]['u'] = (int)$val['IsQuickPay'];    
					}
					//dump($add_table->_sql());
					$pubtext = array('msg' => $arr,
									 'ts' => time());
					S("MALL_TGOODS_DATA", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
					
					//生成静态文件
					$url = DB_HOST.U('Gameconfig/writetxt');
					$jingtxt = curlGET($url);
					
				}else if ($info['cate'] == "102"){
					//修改商品
					$result = $add_table->where("GoodsID=".$goods['GoodsID'])->save($goods);
					//dump($add_table->_sql());	
					//生成缓存
					$configrow = $add_table->where('IsQuickPay=3 and platform=0 and Enable=1')->order("DisplayOrder,GoodsID")->select();
					$arr = array();
					foreach($configrow as $key => $val){
						$arr[$key]['a'] = (int)$val['GoodsID'];            
						//$arr[$key]['b'] = (int)$val['DisplayOrder'];            
						//$arr[$key]['c'] = (int)$val['Enable'];        
						$arr[$key]['d'] = (int)$val['GoldNum'];        
						$arr[$key]['e'] = (int)$val['GiveGoldNum'];       
						$arr[$key]['f'] = (int)$val['Diamond'];   
						$arr[$key]['g'] = (int)$val['GiveDiamond'];     
						$arr[$key]['h'] = $val['GoodsName'];
						$arr[$key]['h2'] = $val['GoodsName_D'];	
						$arr[$key]['i'] = $val['GoodsDescribe'];         
						$arr[$key]['j'] = (int)$val['RoomID'];        
						$arr[$key]['k'] = (int)$val['MallDisplay'];          
						$arr[$key]['l'] = (int)$val['GoodsPrice'];        
						$arr[$key]['m'] = (int)$val['GoodsValue'];    
						//$arr[$key]['n'] = (int)$val['IsFirstPayGive'];        
						//$arr[$key]['o'] = (int)$val['IsEnableSms'];    
						$arr[$key]['p'] = (int)$val['IsFirstPay'];        
						//$arr[$key]['q'] = (int)$val['ChannelID'];    
						$arr[$key]['r'] = $val['Remarks'];        
						$arr[$key]['s'] = $val['RemarksDesc'];    
						$arr[$key]['t'] = (int)$val['IsPayed'];        
						$arr[$key]['v'] = (int)$val['Ticket'];	
						//$arr[$key]['u'] = (int)$val['IsQuickPay'];    
					}
					//dump($add_table->_sql());
					$pubtext = array('msg' => $arr,
									 'ts' => time());
					S("MALL_GOODS_DATA", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
					
					//生成快速商品
					$configrow = $add_table->where('IsQuickPay=1 and platform=0 and Enable=1')->order("DisplayOrder,GoodsID")->select();
					$arr = array();
					foreach($configrow as $key => $val){
						$arr[$key]['a'] = (int)$val['GoodsID'];            
						//$arr[$key]['b'] = (int)$val['DisplayOrder'];            
						//$arr[$key]['c'] = (int)$val['Enable'];        
						$arr[$key]['d'] = (int)$val['GoldNum'];        
						$arr[$key]['e'] = (int)$val['GiveGoldNum'];       
						$arr[$key]['f'] = (int)$val['Diamond'];   
						$arr[$key]['g'] = (int)$val['GiveDiamond'];     
						$arr[$key]['h'] = $val['GoodsName']; 
						$arr[$key]['h2'] = $val['GoodsName_D'];	
						$arr[$key]['i'] = $val['GoodsDescribe'];         
						$arr[$key]['j'] = (int)$val['RoomID'];        
						$arr[$key]['k'] = (int)$val['MallDisplay'];          
						$arr[$key]['l'] = (int)$val['GoodsPrice'];        
						$arr[$key]['m'] = (int)$val['GoodsValue'];    
						//$arr[$key]['n'] = (int)$val['IsFirstPayGive'];        
						//$arr[$key]['o'] = (int)$val['IsEnableSms'];    
						$arr[$key]['p'] = (int)$val['IsFirstPay'];        
						//$arr[$key]['q'] = (int)$val['ChannelID'];    
						$arr[$key]['r'] = $val['Remarks'];        
						$arr[$key]['s'] = $val['RemarksDesc'];    
						$arr[$key]['t'] = (int)$val['IsPayed'];        
						$arr[$key]['v'] = (int)$val['Ticket'];	
						//$arr[$key]['u'] = (int)$val['IsQuickPay'];    
					}
					//dump($add_table->_sql());
					$pubtext = array('msg' => $arr,
									 'ts' => time());
					S("QUICK_GOODS_DATA", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
					
					//生成快速商品2
					$configrow = $add_table->where('IsQuickPay=4 and platform=0 and Enable=1')->order("DisplayOrder,GoodsID")->select();
					$arr = array();
					foreach($configrow as $key => $val){
						$arr[$key]['a'] = (int)$val['GoodsID'];            
						//$arr[$key]['b'] = (int)$val['DisplayOrder'];            
						//$arr[$key]['c'] = (int)$val['Enable'];        
						$arr[$key]['d'] = (int)$val['GoldNum'];        
						$arr[$key]['e'] = (int)$val['GiveGoldNum'];       
						$arr[$key]['f'] = (int)$val['Diamond'];   
						$arr[$key]['g'] = (int)$val['GiveDiamond'];     
						$arr[$key]['h'] = $val['GoodsName']; 
						$arr[$key]['h2'] = $val['GoodsName_D'];
						$arr[$key]['i'] = $val['GoodsDescribe'];         
						$arr[$key]['j'] = (int)$val['RoomID'];        
						$arr[$key]['k'] = (int)$val['MallDisplay'];          
						$arr[$key]['l'] = (int)$val['GoodsPrice'];        
						$arr[$key]['m'] = (int)$val['GoodsValue'];    
						//$arr[$key]['n'] = (int)$val['IsFirstPayGive'];        
						//$arr[$key]['o'] = (int)$val['IsEnableSms'];    
						$arr[$key]['p'] = (int)$val['IsFirstPay'];        
						//$arr[$key]['q'] = (int)$val['ChannelID'];    
						$arr[$key]['r'] = $val['Remarks'];        
						$arr[$key]['s'] = $val['RemarksDesc'];    
						$arr[$key]['t'] = (int)$val['IsPayed'];
						$arr[$key]['v'] = (int)$val['Ticket'];				
						//$arr[$key]['u'] = (int)$val['IsQuickPay']; 
						
					}
					//dump($add_table->_sql());
					$pubtext = array('msg' => $arr,
									 'ts' => time());
					S("QUICKNEW_GOODS_DATA", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
					//$quicknewgoods = S("QUICKNEW_GOODS_DATA");
					//echo $quicknewgoods; exit; 
					
					//生成大厅商品
					$configrow = $add_table->where('IsQuickPay=2 and platform=0 and Enable=1')->order("DisplayOrder,GoodsID")->select();
					$arr = array();
					foreach($configrow as $key => $val){
						$arr[$key]['a'] = (int)$val['GoodsID'];            
						//$arr[$key]['b'] = (int)$val['DisplayOrder'];            
						//$arr[$key]['c'] = (int)$val['Enable'];        
						$arr[$key]['d'] = (int)$val['GoldNum'];        
						$arr[$key]['e'] = (int)$val['GiveGoldNum'];       
						$arr[$key]['f'] = (int)$val['Diamond'];   
						$arr[$key]['g'] = (int)$val['GiveDiamond'];     
						$arr[$key]['h'] = $val['GoodsName']; 
						$arr[$key]['h2'] = $val['GoodsName_D'];
						$arr[$key]['i'] = $val['GoodsDescribe'];         
						$arr[$key]['j'] = (int)$val['RoomID'];        
						$arr[$key]['k'] = (int)$val['MallDisplay'];          
						$arr[$key]['l'] = (int)$val['GoodsPrice'];        
						$arr[$key]['m'] = (int)$val['GoodsValue'];    
						//$arr[$key]['n'] = (int)$val['IsFirstPayGive'];        
						//$arr[$key]['o'] = (int)$val['IsEnableSms'];    
						$arr[$key]['p'] = (int)$val['IsFirstPay'];        
						//$arr[$key]['q'] = (int)$val['ChannelID'];    
						$arr[$key]['r'] = $val['Remarks'];        
						$arr[$key]['s'] = $val['RemarksDesc'];    
						$arr[$key]['t'] = (int)$val['IsPayed'];        
						$arr[$key]['v'] = (int)$val['Ticket'];	
						//$arr[$key]['u'] = (int)$val['IsQuickPay'];    
					}
					//dump($add_table->_sql());
					$pubtext = array('msg' => $arr,
									 'ts' => time());
					S("MALL_TGOODS_DATA", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
					
					//生成静态文件
					$url = DB_HOST.U('Gameconfig/writetxt');
					$jingtxt = curlGET($url);
					
				}else if ($info['cate'] == "103"){
					//删除商品
					if (!empty($goods['GoodsID'])){
						$result = $add_table->where("GoodsID=".$goods['GoodsID'])->delete();
					}
					//dump($add_table->_sql());	
					//生成缓存
					$configrow = $add_table->where('IsQuickPay=3 and platform=0 and Enable=1')->order("DisplayOrder,GoodsID")->select();
					$arr = array();
					foreach($configrow as $key => $val){
						$arr[$key]['a'] = (int)$val['GoodsID'];            
						//$arr[$key]['b'] = (int)$val['DisplayOrder'];            
						//$arr[$key]['c'] = (int)$val['Enable'];        
						$arr[$key]['d'] = (int)$val['GoldNum'];        
						$arr[$key]['e'] = (int)$val['GiveGoldNum'];       
						$arr[$key]['f'] = (int)$val['Diamond'];   
						$arr[$key]['g'] = (int)$val['GiveDiamond'];     
						$arr[$key]['h'] = $val['GoodsName']; 
						$arr[$key]['h2'] = $val['GoodsName_D'];	
						$arr[$key]['i'] = $val['GoodsDescribe'];         
						$arr[$key]['j'] = (int)$val['RoomID'];        
						$arr[$key]['k'] = (int)$val['MallDisplay'];          
						$arr[$key]['l'] = (int)$val['GoodsPrice'];        
						$arr[$key]['m'] = (int)$val['GoodsValue'];    
						//$arr[$key]['n'] = (int)$val['IsFirstPayGive'];        
						//$arr[$key]['o'] = (int)$val['IsEnableSms'];    
						$arr[$key]['p'] = (int)$val['IsFirstPay'];        
						//$arr[$key]['q'] = (int)$val['ChannelID'];    
						$arr[$key]['r'] = $val['Remarks'];        
						$arr[$key]['s'] = $val['RemarksDesc'];    
						$arr[$key]['t'] = (int)$val['IsPayed'];        
						$arr[$key]['v'] = (int)$val['Ticket'];	
						//$arr[$key]['u'] = (int)$val['IsQuickPay'];    
					}
					//dump($add_table->_sql());
					$pubtext = array('msg' => $arr,
									 'ts' => time());
					S("MALL_GOODS_DATA", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
					
					//生成快速商品
					$configrow = $add_table->where('IsQuickPay=1 and platform=0 and Enable=1')->order("DisplayOrder,GoodsID")->select();
					$arr = array();
					foreach($configrow as $key => $val){
						$arr[$key]['a'] = (int)$val['GoodsID'];            
						//$arr[$key]['b'] = (int)$val['DisplayOrder'];            
						//$arr[$key]['c'] = (int)$val['Enable'];        
						$arr[$key]['d'] = (int)$val['GoldNum'];        
						$arr[$key]['e'] = (int)$val['GiveGoldNum'];       
						$arr[$key]['f'] = (int)$val['Diamond'];   
						$arr[$key]['g'] = (int)$val['GiveDiamond'];     
						$arr[$key]['h'] = $val['GoodsName'];    
						$arr[$key]['h2'] = $val['GoodsName_D'];	
						$arr[$key]['i'] = $val['GoodsDescribe'];         
						$arr[$key]['j'] = (int)$val['RoomID'];        
						$arr[$key]['k'] = (int)$val['MallDisplay'];          
						$arr[$key]['l'] = (int)$val['GoodsPrice'];        
						$arr[$key]['m'] = (int)$val['GoodsValue'];    
						//$arr[$key]['n'] = (int)$val['IsFirstPayGive'];        
						//$arr[$key]['o'] = (int)$val['IsEnableSms'];    
						$arr[$key]['p'] = (int)$val['IsFirstPay'];        
						//$arr[$key]['q'] = (int)$val['ChannelID'];    
						$arr[$key]['r'] = $val['Remarks'];        
						$arr[$key]['s'] = $val['RemarksDesc'];    
						$arr[$key]['t'] = (int)$val['IsPayed'];        
						$arr[$key]['v'] = (int)$val['Ticket'];	
						//$arr[$key]['u'] = (int)$val['IsQuickPay'];    
					}
					//dump($add_table->_sql());
					$pubtext = array('msg' => $arr,
									 'ts' => time());
					S("QUICK_GOODS_DATA", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
					
					//生成快速商品2
					$configrow = $add_table->where('IsQuickPay=4 and platform=0 and Enable=1')->order("DisplayOrder,GoodsID")->select();
					$arr = array();
					foreach($configrow as $key => $val){
						$arr[$key]['a'] = (int)$val['GoodsID'];            
						//$arr[$key]['b'] = (int)$val['DisplayOrder'];            
						//$arr[$key]['c'] = (int)$val['Enable'];        
						$arr[$key]['d'] = (int)$val['GoldNum'];        
						$arr[$key]['e'] = (int)$val['GiveGoldNum'];       
						$arr[$key]['f'] = (int)$val['Diamond'];   
						$arr[$key]['g'] = (int)$val['GiveDiamond'];     
						$arr[$key]['h'] = $val['GoodsName']; 
						$arr[$key]['h2'] = $val['GoodsName_D'];
						$arr[$key]['i'] = $val['GoodsDescribe'];         
						$arr[$key]['j'] = (int)$val['RoomID'];        
						$arr[$key]['k'] = (int)$val['MallDisplay'];          
						$arr[$key]['l'] = (int)$val['GoodsPrice'];        
						$arr[$key]['m'] = (int)$val['GoodsValue'];    
						//$arr[$key]['n'] = (int)$val['IsFirstPayGive'];        
						//$arr[$key]['o'] = (int)$val['IsEnableSms'];    
						$arr[$key]['p'] = (int)$val['IsFirstPay'];        
						//$arr[$key]['q'] = (int)$val['ChannelID'];    
						$arr[$key]['r'] = $val['Remarks'];        
						$arr[$key]['s'] = $val['RemarksDesc'];    
						$arr[$key]['t'] = (int)$val['IsPayed'];
						$arr[$key]['v'] = (int)$val['Ticket'];				
						//$arr[$key]['u'] = (int)$val['IsQuickPay']; 
						
					}
					//dump($add_table->_sql());
					$pubtext = array('msg' => $arr,
									 'ts' => time());
					S("QUICKNEW_GOODS_DATA", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
					
					//生成大厅商品
					$configrow = $add_table->where('IsQuickPay=2 and platform=0 and Enable=1')->order("DisplayOrder,GoodsID")->select();
					$arr = array();
					foreach($configrow as $key => $val){
						$arr[$key]['a'] = (int)$val['GoodsID'];            
						//$arr[$key]['b'] = (int)$val['DisplayOrder'];            
						//$arr[$key]['c'] = (int)$val['Enable'];        
						$arr[$key]['d'] = (int)$val['GoldNum'];        
						$arr[$key]['e'] = (int)$val['GiveGoldNum'];       
						$arr[$key]['f'] = (int)$val['Diamond'];   
						$arr[$key]['g'] = (int)$val['GiveDiamond'];     
						$arr[$key]['h'] = $val['GoodsName']; 
						$arr[$key]['h2'] = $val['GoodsName_D'];
						$arr[$key]['i'] = $val['GoodsDescribe'];         
						$arr[$key]['j'] = (int)$val['RoomID'];        
						$arr[$key]['k'] = (int)$val['MallDisplay'];          
						$arr[$key]['l'] = (int)$val['GoodsPrice'];        
						$arr[$key]['m'] = (int)$val['GoodsValue'];    
						//$arr[$key]['n'] = (int)$val['IsFirstPayGive'];        
						//$arr[$key]['o'] = (int)$val['IsEnableSms'];    
						$arr[$key]['p'] = (int)$val['IsFirstPay'];        
						//$arr[$key]['q'] = (int)$val['ChannelID'];    
						$arr[$key]['r'] = $val['Remarks'];        
						$arr[$key]['s'] = $val['RemarksDesc'];    
						$arr[$key]['t'] = (int)$val['IsPayed'];        
						$arr[$key]['v'] = (int)$val['Ticket'];	
						//$arr[$key]['u'] = (int)$val['IsQuickPay'];    
					}
					//dump($add_table->_sql());
					$pubtext = array('msg' => $arr,
									 'ts' => time());
					S("MALL_TGOODS_DATA", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
					
					//生成静态文件
					$url = DB_HOST.U('Gameconfig/writetxt');
					$jingtxt = curlGET($url);
					
				}else if ($info['cate'] == "141"){
					//添加商品
					$result = $add_table->add($goods);
					
					//生成IOS商品
					$configrow = $add_table->where('IsQuickPay=0 and platform=1 and Enable=1')->order("DisplayOrder,GoodsID")->select();
					$arr = array();
					foreach($configrow as $key => $val){
						$arr[$key]['a'] = (int)$val['GoodsID'];            
						//$arr[$key]['b'] = (int)$val['DisplayOrder'];            
						//$arr[$key]['c'] = (int)$val['Enable'];        
						$arr[$key]['d'] = (int)$val['GoldNum'];        
						$arr[$key]['e'] = (int)$val['GiveGoldNum'];       
						$arr[$key]['f'] = (int)$val['Diamond'];   
						$arr[$key]['g'] = (int)$val['GiveDiamond'];     
						$arr[$key]['h'] = $val['GoodsName'];
						$arr[$key]['h2'] = $val['GoodsName_D']; 		
						$arr[$key]['i'] = $val['GoodsDescribe'];         
						$arr[$key]['j'] = (int)$val['RoomID'];        
						$arr[$key]['k'] = (int)$val['MallDisplay'];          
						$arr[$key]['l'] = (int)$val['GoodsPrice'];        
						$arr[$key]['m'] = (int)$val['GoodsValue'];    
						//$arr[$key]['n'] = (int)$val['IsFirstPayGive'];        
						//$arr[$key]['o'] = (int)$val['IsEnableSms'];    
						$arr[$key]['p'] = (int)$val['IsFirstPay'];        
						//$arr[$key]['q'] = (int)$val['ChannelID'];    
						$arr[$key]['r'] = $val['Remarks'];        
						$arr[$key]['s'] = $val['RemarksDesc'];    
						$arr[$key]['t'] = (int)$val['IsPayed'];        
						//$arr[$key]['u'] = (int)$val['IsQuickPay'];
						$arr[$key]['v'] = (int)$val['Ticket'];						
					}
					//dump($add_table->_sql());
					$pubtext = array('msg' => $arr,
									 'ts' => time());
					S("MALL_GOODS_IOS_DATA", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
					
					//生成静态文件
					$url = DB_HOST.U('Gameconfig/writetxt');
					$jingtxt = curlGET($url);
					
				}else if ($info['cate'] == "142"){
					//修改商品
					$result = $add_table->where("GoodsID=".$goods['GoodsID'])->save($goods);
					//dump($add_table->_sql());	
					//生成IOS商品
					$configrow = $add_table->where('IsQuickPay=0 and platform=1 and Enable=1')->order("DisplayOrder,GoodsID")->select();
					$arr = array();
					foreach($configrow as $key => $val){
						$arr[$key]['a'] = (int)$val['GoodsID'];            
						//$arr[$key]['b'] = (int)$val['DisplayOrder'];            
						//$arr[$key]['c'] = (int)$val['Enable'];        
						$arr[$key]['d'] = (int)$val['GoldNum'];        
						$arr[$key]['e'] = (int)$val['GiveGoldNum'];       
						$arr[$key]['f'] = (int)$val['Diamond'];   
						$arr[$key]['g'] = (int)$val['GiveDiamond'];     
						$arr[$key]['h'] = $val['GoodsName'];
						$arr[$key]['h2'] = $val['GoodsName_D']; 		
						$arr[$key]['i'] = $val['GoodsDescribe'];         
						$arr[$key]['j'] = (int)$val['RoomID'];        
						$arr[$key]['k'] = (int)$val['MallDisplay'];          
						$arr[$key]['l'] = (int)$val['GoodsPrice'];        
						$arr[$key]['m'] = (int)$val['GoodsValue'];    
						//$arr[$key]['n'] = (int)$val['IsFirstPayGive'];        
						//$arr[$key]['o'] = (int)$val['IsEnableSms'];    
						$arr[$key]['p'] = (int)$val['IsFirstPay'];        
						//$arr[$key]['q'] = (int)$val['ChannelID'];    
						$arr[$key]['r'] = $val['Remarks'];        
						$arr[$key]['s'] = $val['RemarksDesc'];    
						$arr[$key]['t'] = (int)$val['IsPayed'];        
						//$arr[$key]['u'] = (int)$val['IsQuickPay'];
						$arr[$key]['v'] = (int)$val['Ticket'];						
					}
					//dump($add_table->_sql());
					$pubtext = array('msg' => $arr,
									 'ts' => time());
					S("MALL_GOODS_IOS_DATA", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
					
					//生成静态文件
					$url = DB_HOST.U('Gameconfig/writetxt');
					$jingtxt = curlGET($url);
					
				}else if ($info['cate'] == "143"){
					//删除商品
					if (!empty($goods['GoodsID'])){
						$result = $add_table->where("GoodsID=".$goods['GoodsID'])->delete();
					}
					//dump($add_table->_sql());	
					//生成IOS商品
					$configrow = $add_table->where('IsQuickPay=0 and platform=1 and Enable=1')->order("DisplayOrder,GoodsID")->select();
					$arr = array();
					foreach($configrow as $key => $val){
						$arr[$key]['a'] = (int)$val['GoodsID'];            
						//$arr[$key]['b'] = (int)$val['DisplayOrder'];            
						//$arr[$key]['c'] = (int)$val['Enable'];        
						$arr[$key]['d'] = (int)$val['GoldNum'];        
						$arr[$key]['e'] = (int)$val['GiveGoldNum'];       
						$arr[$key]['f'] = (int)$val['Diamond'];   
						$arr[$key]['g'] = (int)$val['GiveDiamond'];     
						$arr[$key]['h'] = $val['GoodsName'];
						$arr[$key]['h2'] = $val['GoodsName_D']; 		
						$arr[$key]['i'] = $val['GoodsDescribe'];         
						$arr[$key]['j'] = (int)$val['RoomID'];        
						$arr[$key]['k'] = (int)$val['MallDisplay'];          
						$arr[$key]['l'] = (int)$val['GoodsPrice'];        
						$arr[$key]['m'] = (int)$val['GoodsValue'];    
						//$arr[$key]['n'] = (int)$val['IsFirstPayGive'];        
						//$arr[$key]['o'] = (int)$val['IsEnableSms'];    
						$arr[$key]['p'] = (int)$val['IsFirstPay'];        
						//$arr[$key]['q'] = (int)$val['ChannelID'];    
						$arr[$key]['r'] = $val['Remarks'];        
						$arr[$key]['s'] = $val['RemarksDesc'];    
						$arr[$key]['t'] = (int)$val['IsPayed'];        
						//$arr[$key]['u'] = (int)$val['IsQuickPay'];
						$arr[$key]['v'] = (int)$val['Ticket'];						
					}
					//dump($add_table->_sql());
					$pubtext = array('msg' => $arr,
									 'ts' => time());
					S("MALL_GOODS_IOS_DATA", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
					
					//生成静态文件
					$url = DB_HOST.U('Gameconfig/writetxt');
					$jingtxt = curlGET($url);
					
				}else if ($info['cate'] == "144"){
					//添加商品
					$result = $add_table->add($goods);
					
					//生成IOS商品
					$configrow = $add_table->where('IsQuickPay=1 and platform=1 and Enable=1')->order("DisplayOrder,GoodsID")->select();
					$arr = array();
					foreach($configrow as $key => $val){
						$arr[$key]['a'] = (int)$val['GoodsID'];            
						//$arr[$key]['b'] = (int)$val['DisplayOrder'];            
						//$arr[$key]['c'] = (int)$val['Enable'];        
						$arr[$key]['d'] = (int)$val['GoldNum'];        
						$arr[$key]['e'] = (int)$val['GiveGoldNum'];       
						$arr[$key]['f'] = (int)$val['Diamond'];   
						$arr[$key]['g'] = (int)$val['GiveDiamond'];     
						$arr[$key]['h'] = $val['GoodsName'];
						$arr[$key]['h2'] = $val['GoodsName_D']; 		
						$arr[$key]['i'] = $val['GoodsDescribe'];         
						$arr[$key]['j'] = (int)$val['RoomID'];        
						$arr[$key]['k'] = (int)$val['MallDisplay'];          
						$arr[$key]['l'] = (int)$val['GoodsPrice'];        
						$arr[$key]['m'] = (int)$val['GoodsValue'];    
						//$arr[$key]['n'] = (int)$val['IsFirstPayGive'];        
						//$arr[$key]['o'] = (int)$val['IsEnableSms'];    
						$arr[$key]['p'] = (int)$val['IsFirstPay'];        
						//$arr[$key]['q'] = (int)$val['ChannelID'];    
						$arr[$key]['r'] = $val['Remarks'];        
						$arr[$key]['s'] = $val['RemarksDesc'];    
						$arr[$key]['t'] = (int)$val['IsPayed'];        
						//$arr[$key]['u'] = (int)$val['IsQuickPay'];
						$arr[$key]['v'] = (int)$val['Ticket'];						
					}
					//dump($add_table->_sql());
					$pubtext = array('msg' => $arr,
									 'ts' => time());
					S("MALL_QGOODS_IOS_DATA", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
					
					//生成静态文件
					$url = DB_HOST.U('Gameconfig/writetxt');
					$jingtxt = curlGET($url);
					
				}else if ($info['cate'] == "145"){
					//修改商品
					$result = $add_table->where("GoodsID=".$goods['GoodsID'])->save($goods);
					//dump($add_table->_sql());	
					//生成IOS商品
					$configrow = $add_table->where('IsQuickPay=1 and platform=1 and Enable=1')->order("DisplayOrder,GoodsID")->select();
					$arr = array();
					foreach($configrow as $key => $val){
						$arr[$key]['a'] = (int)$val['GoodsID'];            
						//$arr[$key]['b'] = (int)$val['DisplayOrder'];            
						//$arr[$key]['c'] = (int)$val['Enable'];        
						$arr[$key]['d'] = (int)$val['GoldNum'];        
						$arr[$key]['e'] = (int)$val['GiveGoldNum'];       
						$arr[$key]['f'] = (int)$val['Diamond'];   
						$arr[$key]['g'] = (int)$val['GiveDiamond'];     
						$arr[$key]['h'] = $val['GoodsName'];
						$arr[$key]['h2'] = $val['GoodsName_D']; 		
						$arr[$key]['i'] = $val['GoodsDescribe'];         
						$arr[$key]['j'] = (int)$val['RoomID'];        
						$arr[$key]['k'] = (int)$val['MallDisplay'];          
						$arr[$key]['l'] = (int)$val['GoodsPrice'];        
						$arr[$key]['m'] = (int)$val['GoodsValue'];    
						//$arr[$key]['n'] = (int)$val['IsFirstPayGive'];        
						//$arr[$key]['o'] = (int)$val['IsEnableSms'];    
						$arr[$key]['p'] = (int)$val['IsFirstPay'];        
						//$arr[$key]['q'] = (int)$val['ChannelID'];    
						$arr[$key]['r'] = $val['Remarks'];        
						$arr[$key]['s'] = $val['RemarksDesc'];    
						$arr[$key]['t'] = (int)$val['IsPayed'];        
						//$arr[$key]['u'] = (int)$val['IsQuickPay'];
						$arr[$key]['v'] = (int)$val['Ticket'];						
					}
					//dump($add_table->_sql());
					$pubtext = array('msg' => $arr,
									 'ts' => time());
					S("MALL_QGOODS_IOS_DATA", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
					
					//生成静态文件
					$url = DB_HOST.U('Gameconfig/writetxt');
					$jingtxt = curlGET($url);
					
				}else if ($info['cate'] == "146"){
					//删除商品
					if (!empty($goods['GoodsID'])){
						$result = $add_table->where("GoodsID=".$goods['GoodsID'])->delete();
					}
					//dump($add_table->_sql());	
					//生成IOS商品
					$configrow = $add_table->where('IsQuickPay=1 and platform=1 and Enable=1')->order("DisplayOrder,GoodsID")->select();
					$arr = array();
					foreach($configrow as $key => $val){
						$arr[$key]['a'] = (int)$val['GoodsID'];            
						//$arr[$key]['b'] = (int)$val['DisplayOrder'];            
						//$arr[$key]['c'] = (int)$val['Enable'];        
						$arr[$key]['d'] = (int)$val['GoldNum'];        
						$arr[$key]['e'] = (int)$val['GiveGoldNum'];       
						$arr[$key]['f'] = (int)$val['Diamond'];   
						$arr[$key]['g'] = (int)$val['GiveDiamond'];     
						$arr[$key]['h'] = $val['GoodsName'];
						$arr[$key]['h2'] = $val['GoodsName_D']; 		
						$arr[$key]['i'] = $val['GoodsDescribe'];         
						$arr[$key]['j'] = (int)$val['RoomID'];        
						$arr[$key]['k'] = (int)$val['MallDisplay'];          
						$arr[$key]['l'] = (int)$val['GoodsPrice'];        
						$arr[$key]['m'] = (int)$val['GoodsValue'];    
						//$arr[$key]['n'] = (int)$val['IsFirstPayGive'];        
						//$arr[$key]['o'] = (int)$val['IsEnableSms'];    
						$arr[$key]['p'] = (int)$val['IsFirstPay'];        
						//$arr[$key]['q'] = (int)$val['ChannelID'];    
						$arr[$key]['r'] = $val['Remarks'];        
						$arr[$key]['s'] = $val['RemarksDesc'];    
						$arr[$key]['t'] = (int)$val['IsPayed'];        
						//$arr[$key]['u'] = (int)$val['IsQuickPay'];
						$arr[$key]['v'] = (int)$val['Ticket'];						
					}
					//dump($add_table->_sql());
					$pubtext = array('msg' => $arr,
									 'ts' => time());
					S("MALL_QGOODS_IOS_DATA", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
					
					//生成静态文件
					$url = DB_HOST.U('Gameconfig/writetxt');
					$jingtxt = curlGET($url);
					
				}else if ($info['cate'] == "147"){
					//添加商品
					$result = $add_table->add($goods);
					
					//生成IOS商品
					$configrow = $add_table->where('IsQuickPay=2 and platform=1 and Enable=1')->order("DisplayOrder,GoodsID")->select();
					$arr = array();
					foreach($configrow as $key => $val){
						$arr[$key]['a'] = (int)$val['GoodsID'];            
						//$arr[$key]['b'] = (int)$val['DisplayOrder'];            
						//$arr[$key]['c'] = (int)$val['Enable'];        
						$arr[$key]['d'] = (int)$val['GoldNum'];        
						$arr[$key]['e'] = (int)$val['GiveGoldNum'];       
						$arr[$key]['f'] = (int)$val['Diamond'];   
						$arr[$key]['g'] = (int)$val['GiveDiamond'];     
						$arr[$key]['h'] = $val['GoodsName'];
						$arr[$key]['h2'] = $val['GoodsName_D']; 		
						$arr[$key]['i'] = $val['GoodsDescribe'];         
						$arr[$key]['j'] = (int)$val['RoomID'];        
						$arr[$key]['k'] = (int)$val['MallDisplay'];          
						$arr[$key]['l'] = (int)$val['GoodsPrice'];        
						$arr[$key]['m'] = (int)$val['GoodsValue'];    
						//$arr[$key]['n'] = (int)$val['IsFirstPayGive'];        
						//$arr[$key]['o'] = (int)$val['IsEnableSms'];    
						$arr[$key]['p'] = (int)$val['IsFirstPay'];        
						//$arr[$key]['q'] = (int)$val['ChannelID'];    
						$arr[$key]['r'] = $val['Remarks'];        
						$arr[$key]['s'] = $val['RemarksDesc'];    
						$arr[$key]['t'] = (int)$val['IsPayed'];        
						//$arr[$key]['u'] = (int)$val['IsQuickPay'];
						$arr[$key]['v'] = (int)$val['Ticket'];						
					}
					//dump($add_table->_sql());
					$pubtext = array('msg' => $arr,
									 'ts' => time());
					S("MALL_TGOODS_IOS_DATA", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
					
					//生成静态文件
					$url = DB_HOST.U('Gameconfig/writetxt');
					$jingtxt = curlGET($url);
					
				}else if ($info['cate'] == "148"){
					//修改商品
					$result = $add_table->where("GoodsID=".$goods['GoodsID'])->save($goods);
					//dump($add_table->_sql());	
					//生成IOS商品
					$configrow = $add_table->where('IsQuickPay=2 and platform=1 and Enable=1')->order("DisplayOrder,GoodsID")->select();
					$arr = array();
					foreach($configrow as $key => $val){
						$arr[$key]['a'] = (int)$val['GoodsID'];            
						//$arr[$key]['b'] = (int)$val['DisplayOrder'];            
						//$arr[$key]['c'] = (int)$val['Enable'];        
						$arr[$key]['d'] = (int)$val['GoldNum'];        
						$arr[$key]['e'] = (int)$val['GiveGoldNum'];       
						$arr[$key]['f'] = (int)$val['Diamond'];   
						$arr[$key]['g'] = (int)$val['GiveDiamond'];     
						$arr[$key]['h'] = $val['GoodsName'];
						$arr[$key]['h2'] = $val['GoodsName_D']; 		
						$arr[$key]['i'] = $val['GoodsDescribe'];         
						$arr[$key]['j'] = (int)$val['RoomID'];        
						$arr[$key]['k'] = (int)$val['MallDisplay'];          
						$arr[$key]['l'] = (int)$val['GoodsPrice'];        
						$arr[$key]['m'] = (int)$val['GoodsValue'];    
						//$arr[$key]['n'] = (int)$val['IsFirstPayGive'];        
						//$arr[$key]['o'] = (int)$val['IsEnableSms'];    
						$arr[$key]['p'] = (int)$val['IsFirstPay'];        
						//$arr[$key]['q'] = (int)$val['ChannelID'];    
						$arr[$key]['r'] = $val['Remarks'];        
						$arr[$key]['s'] = $val['RemarksDesc'];    
						$arr[$key]['t'] = (int)$val['IsPayed'];        
						//$arr[$key]['u'] = (int)$val['IsQuickPay'];
						$arr[$key]['v'] = (int)$val['Ticket'];						
					}
					//dump($add_table->_sql());
					$pubtext = array('msg' => $arr,
									 'ts' => time());
					S("MALL_TGOODS_IOS_DATA", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
					
					//生成静态文件
					$url = DB_HOST.U('Gameconfig/writetxt');
					$jingtxt = curlGET($url);
					
				}else if ($info['cate'] == "149"){
					//删除商品
					if (!empty($goods['GoodsID'])){
						$result = $add_table->where("GoodsID=".$goods['GoodsID'])->delete();
					}
					//dump($add_table->_sql());	
					//生成IOS商品
					$configrow = $add_table->where('IsQuickPay=2 and platform=1 and Enable=1')->order("DisplayOrder,GoodsID")->select();
					$arr = array();
					foreach($configrow as $key => $val){
						$arr[$key]['a'] = (int)$val['GoodsID'];            
						//$arr[$key]['b'] = (int)$val['DisplayOrder'];            
						//$arr[$key]['c'] = (int)$val['Enable'];        
						$arr[$key]['d'] = (int)$val['GoldNum'];        
						$arr[$key]['e'] = (int)$val['GiveGoldNum'];       
						$arr[$key]['f'] = (int)$val['Diamond'];   
						$arr[$key]['g'] = (int)$val['GiveDiamond'];     
						$arr[$key]['h'] = $val['GoodsName'];
						$arr[$key]['h2'] = $val['GoodsName_D']; 		
						$arr[$key]['i'] = $val['GoodsDescribe'];         
						$arr[$key]['j'] = (int)$val['RoomID'];        
						$arr[$key]['k'] = (int)$val['MallDisplay'];          
						$arr[$key]['l'] = (int)$val['GoodsPrice'];        
						$arr[$key]['m'] = (int)$val['GoodsValue'];    
						//$arr[$key]['n'] = (int)$val['IsFirstPayGive'];        
						//$arr[$key]['o'] = (int)$val['IsEnableSms'];    
						$arr[$key]['p'] = (int)$val['IsFirstPay'];        
						//$arr[$key]['q'] = (int)$val['ChannelID'];    
						$arr[$key]['r'] = $val['Remarks'];        
						$arr[$key]['s'] = $val['RemarksDesc'];    
						$arr[$key]['t'] = (int)$val['IsPayed'];        
						//$arr[$key]['u'] = (int)$val['IsQuickPay'];
						$arr[$key]['v'] = (int)$val['Ticket'];						
					}
					//dump($add_table->_sql());
					$pubtext = array('msg' => $arr,
									 'ts' => time());
					S("MALL_TGOODS_IOS_DATA", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
					
					//生成静态文件
					$url = DB_HOST.U('Gameconfig/writetxt');
					$jingtxt = curlGET($url);
					
				}else if ($info['cate'] == "105"){
					//修改快速说明
					$Table1 = $this->Table_prifix."dynamic_config";
					$row1 = M($Table1);
					$data9 = array();
					$data9['key_value'] = $goods['quickpaytips'];
					$result = $row1->where("key_name='quickpaytips'")->save($data9);
					$data9 = array();
					$data9['key_value'] = $goods['quickpaytips_ios'];
					$result = $row1->where("key_name='quickpaytips_ios'")->save($data9);
					//dump($add_table->_sql());	
					//生成缓存
					//自用
					//S("GAMEBASE_CONFIG_WEB", $res);
					//静态文件
					$configrow = $row1->order("key_name")->select();
					$arr = array();
					foreach($configrow as $key => $val){
						switch ($val['key_name']){
							case 'bankruptcy_gold' : $arr['a'] = (int)$val['key_value']; break;       //破产金币数
							case 'bankruptcy_num' : $arr['b'] = (int)$val['key_value']; break;        //破产次数
							case 'bindphone' : $arr['c'] = (int)$val['key_value']; break;             //绑定手机
							case 'expressgold' : $arr['d'] = (int)$val['key_value']; break;           //一个付费表情的金币数
							case 'GOLDMALL_SELL_MAX' : $arr['e'] = (int)$val['key_value']; break;     //金币商城最高售价范围
							case 'GOLDMALL_SELL_MIN' : $arr['f'] = (int)$val['key_value']; break;     //金币商城最低售价范围
							case 'GOLDMALL_TAX' : $arr['g'] = (int)$val['key_value']; break;          //金币商城税率(百分比)
							case 'horngold' : $arr['h'] = (int)$val['key_value']; break;              //发喇叭的金币数
							case 'kickplayergold' : $arr['i'] = (int)$val['key_value']; break;        //T人的金币数
							case 'kickplayerviplieve' : $arr['j'] = (int)$val['key_value']; break;    //T人最低VIP等级
							case 'LOWER_GOLD' : $arr['k'] = (int)$val['key_value']; break;            //用户金币小于这个数时，赠送
							case 'novice_award' : $arr['l'] = (int)$val['key_value']; break;          //新手奖励，推荐人推荐加入的人
							case 'viptablelevel' : $arr['m'] = (int)$val['key_value']; break;         //创建私人房的最低VIP等级
							case 'quickpaytips' : $arr['n'] = $val['key_value']; break;               //快充
							case 'RECOMMEND_AWARD' : $arr['o'] = (int)$val['key_value']; break;       //推荐奖励的金币
							//case 'recomm_hint' : $arr['p'] = $val['key_value']; break;              //推荐通知内容模板
							case 'registergivegold' : $arr['q'] = (int)$val['key_value']; break;      //注册赠送金币数
							case 'REGISTER_GIVE_GOLD' : $arr['r'] = (int)$val['key_value']; break;    //注册赠送金币
							case 'SYSTEM_GIVE_GOLD' : $arr['s'] = (int)$val['key_value']; break;      //系统赠送的金币,不足这个数时
							case 'SYSTEM_GIVE_GOLD_TIMES' : $arr['t'] = (int)$val['key_value']; break;//系统赠送金币的次数
							case 'SYS_CLIENT_VERTION' : $arr['u'] = $val['key_value']; break;         //版本号
							case 'DIAMOND_BL_MENO' : $arr['v'] = $val['key_value']; break;            //购买钻石比例说明
							case 'DIAMOND_KF_TEL' : $arr['w'] = $val['key_value']; break;             //购买钻石客服电话
							case 'ONLINE_SWITCH' : $arr['x'] = (int)$val['key_value']; break;         //用户在线判断开关(1开启0关闭)
							case 'broadcast_play_limit' : $arr['y'] = (int)$val['key_value']; break;  //新手发大喇叭牌局限制
							case 'private_play_limit' : $arr['z'] = (int)$val['key_value']; break;    //进私人房游戏局数限制
							case 'private_vip_limit' : $arr['a1'] = (int)$val['key_value']; break;    //进入私人房的最低VIP等级
							case 'quickpaytips_ios' : $arr['n_ios'] = $val['key_value']; break;          //IOS快充
							default : break; 
						}
					}
					//dump($add_table->_sql());
					$pubtext = array('msg' => $arr,
									 'ts' => time());
					S("GAMEBASE_CONFIG", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
					
					//生成静态文件
					$url = DB_HOST.U('Gameconfig/writetxt');
					$jingtxt = curlGET($url);
				}
				if($result){
					//修改状态
					$data = array();
					$data['flag'] = '1';
					$data['pubtime'] = time();
					$data['pubname'] = $_SESSION['username'];
					$result2 = $row->where("id=".$id)->save($data);
					//dump($row->_sql());	
					echo "1";
				}else{
					echo "0";
				}
			}else if ($act == "off"){
				//修改状态
				$data = array();
				$data['flag'] = '2';
				$data['pubtime'] = time();
				$data['pubname'] = $_SESSION['username'];
				$result = $row->where("id=".$id)->save($data);
				if($result){
					echo "1";
				}else{
					echo "0";
				}
				
			}
			
			exit;
		}
		
		if ($info['flag']=="0"){
			$info['flagshow'] = "<font color='#FF0000'>待审核</font>";
		}else if ($info['flag']=="1"){
			$info['flagshow'] = "审核通过";
		}else if ($value['flag']=="2"){
			$info['flagshow'] = "审核取消";
		}
		$info['addtime'] = date("Y-m-d H:i:s", $info['addtime']);
		
		if ($info['cate']=="105"){
			$page = "detail_show";
		}else{
			$page = "waitshang_show";
		}
		
		//增加操作记录
		$logs = C('WAITSHANG_MSG_RECORD');
		$remark = "";
		adminlog($logs, $remark);
		
		$this->assign('left_css',"7");
		$this->assign('info',$info);
		$this->assign('goods',$goods);
		
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl.":".$page;
		$this->display($lib_display);
	}
	
	//商品开始
	//商品列表
	public function goods(){
		
		$RoomID = I("RoomID");
		$ChannelID = I("ChannelID");
		$this->assign('RoomID',$RoomID);
		$this->assign('ChannelID',$ChannelID);
		
		$sql0 = "";
		if (!empty($RoomID)){
			$sql0 .= " and RoomID=$RoomID";
		}
		if (!empty($ChannelID)){
			$sql0 .= " and ChannelID=$ChannelID";
		}
		
		$Table = $this->Table_prifix."profile_mall_goods_data";
		$rowlist = M($Table);
		import('ORG.Util.Page');
		$count = $rowlist->where("IsQuickPay=0 and platform=0 $sql0")->count('GoodsID');
		$Page       = new Page($count,PAGE_SHOW);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		
		$list = $rowlist->where("IsQuickPay=0 and platform=0 $sql0")->order('GoodsID')->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($rowlist->_sql());
		foreach($list as $key=>$value){
			$list[$key]['IsFirstPayGive'] = ($value['IsFirstPayGive']=="1") ? "<font color='#FF0000'>是</font>" : "否";
			$list[$key]['IsEnableSms'] = ($value['IsEnableSms']=="1") ? "<font color='#FF0000'>是</font>" : "否";
			$list[$key]['IsFirstPay'] = ($value['IsFirstPay']=="1") ? "<font color='#FF0000'>是</font>" : "否";
			$list[$key]['Enable'] = ($value['Enable']=="1") ? "<font color='#FF0000'>是</font>" : "否";
			$list[$key]['IsPayed'] = ($value['IsPayed']=="1") ? "<font color='#FF0000'>是</font>" : "否";
			$list[$key]['MallDisplay'] = ($value['MallDisplay']=="1") ? "<font color='#FF0000'>是</font>" : "否";
			if (!empty($value['GoodsName_D'])) $list[$key]['GoodsName'] .= "<br>".$value['GoodsName_D'];
			$list[$key]['GoldNum'] = number_format($value['GoldNum']);
		}
		
		//增加操作记录
		$logs = C('SHANG_MSG_RECORD');
		$remark = "";
		adminlog($logs, $remark);
		
		$this->assign('left_css',"7");
		$this->assign('list',$list);
		$this->assign('pageshow',$show);
		
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl.":goods";
		$this->display($lib_display);
	}
	
	//商品添加
	public function goods_add(){
		if(!empty($_POST)){
			$Table = $this->Table_prifix."profile_mall_goods_data";
			$add_table = M($Table);
			
			//unset($_POST['__hash__']);
			
			$count = $add_table->where("GoodsID=".$_POST['GoodsID'])->count('GoodsID');
			if ($count > 0){
				$this->error('GoodsID已存在');
				exit;
			}
			
			unset($_POST['__hash__']);
			unset($_POST['Submit']);
			
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = '商品新增';
			$data['userip'] = get_client_ip();
			$data['cate'] = 101;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			//$result = $add_table->add($_POST);
			//dump($add_table->_sql()); exit;
			if($result){
				//增加操作记录
				$logs = C('SHANG_MSG_ADD_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				//通知服务器
				//$url = DB_HOST."/Pay/shang.php";
				//$jinbi_result = curlGET($url);
				
				$this->success('提交成功，等待审核', U($this->By_tpl.'/goods'));
				exit;
			}else{
				//增加操作记录
				$logs = C('SHANG_MSG_ADD_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('添加失败');
				exit;
			}
		}else{
			//增加操作记录
			$logs = C('SHANG_MSG_ADD');
			$remark = "";
			adminlog($logs, $remark);
		
			$this->assign('left_css',"7");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":goods_add";
			$this->display($lib_display);
		}
		
	}

	//商品更新
	public function goods_update(){
		$Table = $this->Table_prifix."profile_mall_goods_data";
		$upate_table = M($Table);
		if(!empty($_POST)){
			
			$GoodsID = $_POST['GoodsID'];
			//unset($_POST['GoodsID']);
			unset($_POST['Submit']);
			unset($_POST['__hash__']);
			
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = '商品修改';
			$data['userip'] = get_client_ip();
			$data['cate'] = 102;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			//$id['GoodsID']=intval($GoodsID);
			//$result=$upate_table->where($id)->save($_POST);
			if($result){
				//增加操作记录
				$logs = C('SHANG_MSG_EDIT_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				//通知服务器
				//$url = DB_HOST."/Pay/shang.php";
				//$jinbi_result = curlGET($url);
				
				$this->success('提交成功，等待审核',U($this->By_tpl.'/goods'));
			}else{
				//增加操作记录
				$logs = C('SHANG_MSG_EDIT_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('修改失败');
			}
		}else{
			//增加操作记录
			$logs = C('SHANG_MSG_EDIT');
			$remark = "";
			adminlog($logs, $remark);
			
			$id['GoodsID']=$_GET['GoodsID'];
			$info = $upate_table->where($id)->find();
			$this->assign('info',$info);
			$this->assign('left_css',"7");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":goods_update";
			$this->display($lib_display);
		}
	}
	
	//商品删除
	public function goods_delete(){
		if(empty($_GET)){ 
			$this->error('非法操作');
			exit;
		}else{
			$GoodsID=$_GET['GoodsID']?$_GET['GoodsID']:$_POST['GoodsID'];
			$Tablename = $this->Table_prifix."profile_mall_goods_data";
			$delete_table = M($Tablename);
			//$where['GoodsID']=$GoodsID;
			//$result=$delete_table->where($where)->delete();
			$arr = $delete_table->where("GoodsID=".$GoodsID)->find();
			$arr['tablename'] = $Tablename;
			$arr['act'] = "del";
			
			//增加修改记录
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($arr, JSON_UNESCAPED_UNICODE);
			$data['remark'] = "删除商品";
			$data['userip'] = get_client_ip();
			$data['cate'] = 103;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			if($result){
				//增加操作记录
				$logs = C('SHANG_MSG_DEL_SUCCESS');
				$remark = "(".$GoodsID.")";
				adminlog($logs, $remark);
				
				//通知服务器
				//$url = DB_HOST."/Pay/shang.php";
				//$jinbi_result = curlGET($url);
				
				$this->success('提交成功，等待审核', U($this->By_tpl.'/goods'));
			}else{
				//增加操作记录
				$logs = C('SHANG_MSG_DEL_FALSE');
				$remark = "(".$GoodsID.")";
				adminlog($logs, $remark);
				
				$this->error('删除失败');
				exit;
			}
		}
	}
	//商品结束
	
	//商品开始
	//商品列表
	public function goodsnew(){
		
		$RoomID = I("RoomID");
		$ChannelID = I("ChannelID");
		$this->assign('RoomID',$RoomID);
		$this->assign('ChannelID',$ChannelID);
		
		$sql0 = "";
		if (!empty($RoomID)){
			$sql0 .= " and RoomID=$RoomID";
		}
		if (!empty($ChannelID)){
			$sql0 .= " and ChannelID=$ChannelID";
		}
		
		$Table = $this->Table_prifix."profile_mall_goods_data";
		$rowlist = M($Table);
		import('ORG.Util.Page');
		$count = $rowlist->where("IsQuickPay=3 and platform=0 $sql0")->count('GoodsID');
		$Page       = new Page($count,PAGE_SHOW);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		
		$list = $rowlist->where("IsQuickPay=3 and platform=0 $sql0")->order('GoodsID')->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($rowlist->_sql());
		foreach($list as $key=>$value){
			$list[$key]['IsFirstPayGive'] = ($value['IsFirstPayGive']=="1") ? "<font color='#FF0000'>是</font>" : "否";
			$list[$key]['IsEnableSms'] = ($value['IsEnableSms']=="1") ? "<font color='#FF0000'>是</font>" : "否";
			$list[$key]['IsFirstPay'] = ($value['IsFirstPay']=="1") ? "<font color='#FF0000'>是</font>" : "否";
			$list[$key]['Enable'] = ($value['Enable']=="1") ? "<font color='#FF0000'>是</font>" : "否";
			$list[$key]['IsPayed'] = ($value['IsPayed']=="1") ? "<font color='#FF0000'>是</font>" : "否";
			$list[$key]['MallDisplay'] = ($value['MallDisplay']=="1") ? "<font color='#FF0000'>是</font>" : "否";
			if (!empty($value['GoodsName_D'])) $list[$key]['GoodsName'] .= "<br>".$value['GoodsName_D'];
			$list[$key]['GoldNum'] = number_format($value['GoldNum']);
		}
		
		//增加操作记录
		$logs = C('SHANG_MSG_RECORD');
		$remark = "";
		adminlog($logs, $remark);
		
		$this->assign('left_css',"7");
		$this->assign('list',$list);
		$this->assign('pageshow',$show);
		
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl.":goodsnew";
		$this->display($lib_display);
	}
	
	//商品添加
	public function goodsnew_add(){
		if(!empty($_POST)){
			$Table = $this->Table_prifix."profile_mall_goods_data";
			$add_table = M($Table);
			
			//unset($_POST['__hash__']);
			
			$count = $add_table->where("GoodsID=".$_POST['GoodsID'])->count('GoodsID');
			if ($count > 0){
				$this->error('GoodsID已存在');
				exit;
			}
			
			unset($_POST['__hash__']);
			unset($_POST['Submit']);
			
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = '商品新增';
			$data['userip'] = get_client_ip();
			$data['cate'] = 101;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			//$result = $add_table->add($_POST);
			//dump($add_table->_sql()); exit;
			if($result){
				//增加操作记录
				$logs = C('SHANG_MSG_ADD_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				//通知服务器
				//$url = DB_HOST."/Pay/shang.php";
				//$jinbi_result = curlGET($url);
				
				$this->success('提交成功，等待审核', U($this->By_tpl.'/goodsnew'));
				exit;
			}else{
				//增加操作记录
				$logs = C('SHANG_MSG_ADD_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('添加失败');
				exit;
			}
		}else{
			//增加操作记录
			$logs = C('SHANG_MSG_ADD');
			$remark = "";
			adminlog($logs, $remark);
		
			$this->assign('left_css',"7");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":goodsnew_add";
			$this->display($lib_display);
		}
		
	}

	//商品更新
	public function goodsnew_update(){
		$Table = $this->Table_prifix."profile_mall_goods_data";
		$upate_table = M($Table);
		if(!empty($_POST)){
			
			$GoodsID = $_POST['GoodsID'];
			//unset($_POST['GoodsID']);
			unset($_POST['Submit']);
			unset($_POST['__hash__']);
			
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = '商品修改';
			$data['userip'] = get_client_ip();
			$data['cate'] = 102;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			//$id['GoodsID']=intval($GoodsID);
			//$result=$upate_table->where($id)->save($_POST);
			if($result){
				//增加操作记录
				$logs = C('SHANG_MSG_EDIT_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				//通知服务器
				//$url = DB_HOST."/Pay/shang.php";
				//$jinbi_result = curlGET($url);
				
				$this->success('提交成功，等待审核',U($this->By_tpl.'/goodsnew'));
			}else{
				//增加操作记录
				$logs = C('SHANG_MSG_EDIT_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('修改失败');
			}
		}else{
			//增加操作记录
			$logs = C('SHANG_MSG_EDIT');
			$remark = "";
			adminlog($logs, $remark);
			
			$id['GoodsID']=$_GET['GoodsID'];
			$info = $upate_table->where($id)->find();
			$this->assign('info',$info);
			$this->assign('left_css',"7");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":goodsnew_update";
			$this->display($lib_display);
		}
	}
	
	//商品删除
	public function goodsnew_delete(){
		if(empty($_GET)){ 
			$this->error('非法操作');
			exit;
		}else{
			$GoodsID=$_GET['GoodsID']?$_GET['GoodsID']:$_POST['GoodsID'];
			$Tablename = $this->Table_prifix."profile_mall_goods_data";
			$delete_table = M($Tablename);
			//$where['GoodsID']=$GoodsID;
			//$result=$delete_table->where($where)->delete();
			$arr = $delete_table->where("GoodsID=".$GoodsID)->find();
			$arr['tablename'] = $Tablename;
			$arr['act'] = "del";
			
			//增加修改记录
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($arr, JSON_UNESCAPED_UNICODE);
			$data['remark'] = "删除商品";
			$data['userip'] = get_client_ip();
			$data['cate'] = 103;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			if($result){
				//增加操作记录
				$logs = C('SHANG_MSG_DEL_SUCCESS');
				$remark = "(".$GoodsID.")";
				adminlog($logs, $remark);
				
				//通知服务器
				//$url = DB_HOST."/Pay/shang.php";
				//$jinbi_result = curlGET($url);
				
				$this->success('提交成功，等待审核', U($this->By_tpl.'/goodsnew'));
			}else{
				//增加操作记录
				$logs = C('SHANG_MSG_DEL_FALSE');
				$remark = "(".$GoodsID.")";
				adminlog($logs, $remark);
				
				$this->error('删除失败');
				exit;
			}
		}
	}
	//商品结束
	
	//快速商品开始
	//快速商品列表
	public function quickgoods(){
		
		$RoomID = I("RoomID");
		$ChannelID = I("ChannelID");
		$this->assign('RoomID',$RoomID);
		$this->assign('ChannelID',$ChannelID);
		
		$sql0 = "";
		if (!empty($RoomID)){
			$sql0 .= " and RoomID=$RoomID";
		}
		if (!empty($ChannelID)){
			$sql0 .= " and ChannelID=$ChannelID";
		}
		
		$Table = $this->Table_prifix."profile_mall_goods_data";
		$rowlist = M($Table);
		import('ORG.Util.Page');
		$count = $rowlist->where("IsQuickPay=1 and platform=0 $sql0")->count('GoodsID');
		$Page       = new Page($count,PAGE_SHOW);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		
		$list = $rowlist->where("IsQuickPay=1 and platform=0 $sql0")->order('GoodsID')->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($rowlist->_sql());
		foreach($list as $key=>$value){
			$list[$key]['IsFirstPayGive'] = ($value['IsFirstPayGive']=="1") ? "是" : "否";
			$list[$key]['IsEnableSms'] = ($value['IsEnableSms']=="1") ? "是" : "否";
			$list[$key]['IsFirstPay'] = ($value['IsFirstPay']=="1") ? "是" : "否";
			$list[$key]['Enable'] = ($value['Enable']=="1") ? "是" : "否";
			$list[$key]['IsPayed'] = ($value['IsPay']=="1") ? "是" : "否";
			$list[$key]['MallDisplay'] = ($value['MallDisplay']=="1") ? "是" : "否";
			if (!empty($value['GoodsName_D'])) $list[$key]['GoodsName'] .= "<br>".$value['GoodsName_D'];
			$list[$key]['GoldNum'] = number_format($value['GoldNum']);
		}
		
		//增加操作记录
		$logs = C('QSHANG_MSG_RECORD');
		$remark = "";
		adminlog($logs, $remark);
		
		$this->assign('left_css',"7");
		$this->assign('list',$list);
		$this->assign('pageshow',$show);
		
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl.":qgoods";
		$this->display($lib_display);
	}
	
	//快速商品添加
	public function qgoods_add(){
		if(!empty($_POST)){
			$Table = $this->Table_prifix."profile_mall_goods_data";
			$add_table = M($Table);
			
			//unset($_POST['__hash__']);
			
			$count = $add_table->where("GoodsID=".$_POST['GoodsID'])->count('GoodsID');
			if ($count > 0){
				$this->error('GoodsID已存在');
				exit;
			}
			
			unset($_POST['__hash__']);
			unset($_POST['Submit']);
			
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = '快速商品新增';
			$data['userip'] = get_client_ip();
			$data['cate'] = 101;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			//$result = $add_table->add($_POST);
			//dump($add_table->_sql()); exit;
			if($result){
				//增加操作记录
				$logs = C('QSHANG_MSG_ADD_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				//通知服务器
				//$url = DB_HOST."/Pay/shang.php";
				//$jinbi_result = curlGET($url);
				
				$this->success('提交成功，等待审核', U($this->By_tpl.'/quickgoods'));
				exit;
			}else{
				//增加操作记录
				$logs = C('QSHANG_MSG_ADD_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('添加失败');
				exit;
			}
		}else{
			//增加操作记录
			$logs = C('QSHANG_MSG_ADD');
			$remark = "";
			adminlog($logs, $remark);
		
			$this->assign('left_css',"7");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":qgoods_add";
			$this->display($lib_display);
		}
		
	}

	//快速商品更新
	public function qgoods_update(){
		$Table = $this->Table_prifix."profile_mall_goods_data";
		$upate_table = M($Table);
		if(!empty($_POST)){
			
			$GoodsID = $_POST['GoodsID'];
			unset($_POST['Submit']);
			unset($_POST['__hash__']);
			
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = '快速商品修改';
			$data['userip'] = get_client_ip();
			$data['cate'] = 102;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			//$id['GoodsID']=intval($GoodsID);
			//$result=$upate_table->where($id)->save($_POST);
			if($result){
				//增加操作记录
				$logs = C('QSHANG_MSG_EDIT_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				//通知服务器
				//$url = DB_HOST."/Pay/shang.php";
				//$jinbi_result = curlGET($url);
				
				$this->success('提交成功，等待审核',U($this->By_tpl.'/quickgoods'));
			}else{
				//增加操作记录
				$logs = C('QSHANG_MSG_EDIT_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('修改失败');
			}
		}else{
			//增加操作记录
			$logs = C('QSHANG_MSG_EDIT');
			$remark = "";
			adminlog($logs, $remark);
			
			$id['GoodsID']=$_GET['GoodsID'];
			$info = $upate_table->where($id)->find();
			$this->assign('info',$info);
			$this->assign('left_css',"7");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":qgoods_update";
			$this->display($lib_display);
		}
	}
	
	//快速商品删除
	public function qgoods_delete(){
		if(empty($_GET)){ 
			$this->error('非法操作');
			exit;
		}else{
			$GoodsID=$_GET['GoodsID']?$_GET['GoodsID']:$_POST['GoodsID'];
			$Tablename = $this->Table_prifix."profile_mall_goods_data";
			$delete_table = M($Tablename);
			//$where['GoodsID']=$GoodsID;
			//$result=$delete_table->where($where)->delete();
			$arr = $delete_table->where("GoodsID=".$GoodsID)->find();
			$arr['tablename'] = $Tablename;
			$arr['act'] = "del";
			
			//增加修改记录
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($arr, JSON_UNESCAPED_UNICODE);
			$data['remark'] = "删除快速商品";
			$data['userip'] = get_client_ip();
			$data['cate'] = 103;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			if($result){
				//增加操作记录
				$logs = C('QSHANG_MSG_DEL_SUCCESS');
				$remark = "(".$GoodsID.")";
				adminlog($logs, $remark);
				
				//通知服务器
				//$url = DB_HOST."/Pay/shang.php";
				//$jinbi_result = curlGET($url);
				
				$this->success('提交成功，等待审核', U($this->By_tpl.'/quickgoods'));
			}else{
				//增加操作记录
				$logs = C('QSHANG_MSG_DEL_FALSE');
				$remark = "(".$GoodsID.")";
				adminlog($logs, $remark);
				
				$this->error('删除失败');
				exit;
			}
		}
	}
	//快速商品结束
	
	//快速商品2开始
	//快速商品列表
	public function quicknew(){
		
		$RoomID = I("RoomID");
		$ChannelID = I("ChannelID");
		$this->assign('RoomID',$RoomID);
		$this->assign('ChannelID',$ChannelID);
		
		$sql0 = "";
		if (!empty($RoomID)){
			$sql0 .= " and RoomID=$RoomID";
		}
		if (!empty($ChannelID)){
			$sql0 .= " and ChannelID=$ChannelID";
		}
		
		$Table = $this->Table_prifix."profile_mall_goods_data";
		$rowlist = M($Table);
		import('ORG.Util.Page');
		$count = $rowlist->where("IsQuickPay=4 and platform=0 $sql0")->count('GoodsID');
		$Page       = new Page($count,PAGE_SHOW);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		
		$list = $rowlist->where("IsQuickPay=4 and platform=0 $sql0")->order('GoodsID')->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($rowlist->_sql());
		foreach($list as $key=>$value){
			$list[$key]['IsFirstPayGive'] = ($value['IsFirstPayGive']=="1") ? "是" : "否";
			$list[$key]['IsEnableSms'] = ($value['IsEnableSms']=="1") ? "是" : "否";
			$list[$key]['IsFirstPay'] = ($value['IsFirstPay']=="1") ? "是" : "否";
			$list[$key]['Enable'] = ($value['Enable']=="1") ? "是" : "否";
			$list[$key]['IsPayed'] = ($value['IsPay']=="1") ? "是" : "否";
			$list[$key]['MallDisplay'] = ($value['MallDisplay']=="1") ? "是" : "否";
			if (!empty($value['GoodsName_D'])) $list[$key]['GoodsName'] .= "<br>".$value['GoodsName_D'];
			$list[$key]['GoldNum'] = number_format($value['GoldNum']);
		}
		
		//增加操作记录
		$logs = C('QSHANG_MSG_RECORD');
		$remark = "";
		adminlog($logs, $remark);
		
		$this->assign('left_css',"7");
		$this->assign('list',$list);
		$this->assign('pageshow',$show);
		
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl.":qgoodsnew";
		$this->display($lib_display);
	}
	
	//快速商品添加
	public function qgoodsnew_add(){
		if(!empty($_POST)){
			$Table = $this->Table_prifix."profile_mall_goods_data";
			$add_table = M($Table);
			
			//unset($_POST['__hash__']);
			
			$count = $add_table->where("GoodsID=".$_POST['GoodsID'])->count('GoodsID');
			if ($count > 0){
				$this->error('GoodsID已存在');
				exit;
			}
			
			unset($_POST['__hash__']);
			unset($_POST['Submit']);
			
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = '快速商品新增';
			$data['userip'] = get_client_ip();
			$data['cate'] = 101;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			//$result = $add_table->add($_POST);
			//dump($add_table->_sql()); exit;
			if($result){
				//增加操作记录
				$logs = C('QSHANG_MSG_ADD_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				//通知服务器
				//$url = DB_HOST."/Pay/shang.php";
				//$jinbi_result = curlGET($url);
				
				$this->success('提交成功，等待审核', U($this->By_tpl.'/quicknew'));
				exit;
			}else{
				//增加操作记录
				$logs = C('QSHANG_MSG_ADD_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('添加失败');
				exit;
			}
		}else{
			//增加操作记录
			$logs = C('QSHANG_MSG_ADD');
			$remark = "";
			adminlog($logs, $remark);
		
			$this->assign('left_css',"7");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":qgoodsnew_add";
			$this->display($lib_display);
		}
		
	}

	//快速商品更新
	public function qgoodsnew_update(){
		$Table = $this->Table_prifix."profile_mall_goods_data";
		$upate_table = M($Table);
		if(!empty($_POST)){
			
			$GoodsID = $_POST['GoodsID'];
			unset($_POST['Submit']);
			unset($_POST['__hash__']);
			
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = '快速商品修改';
			$data['userip'] = get_client_ip();
			$data['cate'] = 102;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			//$id['GoodsID']=intval($GoodsID);
			//$result=$upate_table->where($id)->save($_POST);
			if($result){
				//增加操作记录
				$logs = C('QSHANG_MSG_EDIT_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				//通知服务器
				//$url = DB_HOST."/Pay/shang.php";
				//$jinbi_result = curlGET($url);
				
				$this->success('提交成功，等待审核',U($this->By_tpl.'/quicknew'));
			}else{
				//增加操作记录
				$logs = C('QSHANG_MSG_EDIT_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('修改失败');
			}
		}else{
			//增加操作记录
			$logs = C('QSHANG_MSG_EDIT');
			$remark = "";
			adminlog($logs, $remark);
			
			$id['GoodsID']=$_GET['GoodsID'];
			$info = $upate_table->where($id)->find();
			$this->assign('info',$info);
			$this->assign('left_css',"7");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":qgoodsnew_update";
			$this->display($lib_display);
		}
	}
	
	//快速商品删除
	public function qgoodsnew_delete(){
		if(empty($_GET)){ 
			$this->error('非法操作');
			exit;
		}else{
			$GoodsID=$_GET['GoodsID']?$_GET['GoodsID']:$_POST['GoodsID'];
			$Tablename = $this->Table_prifix."profile_mall_goods_data";
			$delete_table = M($Tablename);
			//$where['GoodsID']=$GoodsID;
			//$result=$delete_table->where($where)->delete();
			$arr = $delete_table->where("GoodsID=".$GoodsID)->find();
			$arr['tablename'] = $Tablename;
			$arr['act'] = "del";
			
			//增加修改记录
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($arr, JSON_UNESCAPED_UNICODE);
			$data['remark'] = "删除快速商品";
			$data['userip'] = get_client_ip();
			$data['cate'] = 103;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			if($result){
				//增加操作记录
				$logs = C('QSHANG_MSG_DEL_SUCCESS');
				$remark = "(".$GoodsID.")";
				adminlog($logs, $remark);
				
				//通知服务器
				//$url = DB_HOST."/Pay/shang.php";
				//$jinbi_result = curlGET($url);
				
				$this->success('提交成功，等待审核', U($this->By_tpl.'/quicknew'));
			}else{
				//增加操作记录
				$logs = C('QSHANG_MSG_DEL_FALSE');
				$remark = "(".$GoodsID.")";
				adminlog($logs, $remark);
				
				$this->error('删除失败');
				exit;
			}
		}
	}
	//快速商品结束
	
	//大厅商品开始
	//大厅商品列表
	public function tgoods(){
		
		$RoomID = I("RoomID");
		$ChannelID = I("ChannelID");
		$this->assign('RoomID',$RoomID);
		$this->assign('ChannelID',$ChannelID);
		
		$sql0 = "";
		if (!empty($RoomID)){
			$sql0 .= " and RoomID=$RoomID";
		}
		if (!empty($ChannelID)){
			$sql0 .= " and ChannelID=$ChannelID";
		}
		
		$Table = $this->Table_prifix."profile_mall_goods_data";
		$rowlist = M($Table);
		import('ORG.Util.Page');
		$count = $rowlist->where("IsQuickPay=2 and platform=0 $sql0")->count('GoodsID');
		$Page       = new Page($count,PAGE_SHOW);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		
		$list = $rowlist->where("IsQuickPay=2 and platform=0 $sql0")->order('GoodsID')->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($rowlist->_sql());
		foreach($list as $key=>$value){
			$list[$key]['IsFirstPayGive'] = ($value['IsFirstPayGive']=="1") ? "是" : "否";
			$list[$key]['IsEnableSms'] = ($value['IsEnableSms']=="1") ? "是" : "否";
			$list[$key]['IsFirstPay'] = ($value['IsFirstPay']=="1") ? "是" : "否";
			$list[$key]['Enable'] = ($value['Enable']=="1") ? "是" : "否";
			$list[$key]['IsPayed'] = ($value['IsPay']=="1") ? "是" : "否";
			$list[$key]['MallDisplay'] = ($value['MallDisplay']=="1") ? "是" : "否";
			if (!empty($value['GoodsName_D'])) $list[$key]['GoodsName'] .= "<br>".$value['GoodsName_D'];
			$list[$key]['GoldNum'] = number_format($value['GoldNum']);
		}
		
		//增加操作记录
		$logs = C('TSHANG_MSG_RECORD');
		$remark = "";
		adminlog($logs, $remark);
		
		$this->assign('left_css',"7");
		$this->assign('list',$list);
		$this->assign('pageshow',$show);
		
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl.":tgoods";
		$this->display($lib_display);
	}
	
	//大厅商品添加
	public function tgoods_add(){
		if(!empty($_POST)){
			$Table = $this->Table_prifix."profile_mall_goods_data";
			$add_table = M($Table);
			
			//unset($_POST['__hash__']);
			
			$count = $add_table->where("GoodsID=".$_POST['GoodsID'])->count('GoodsID');
			if ($count > 0){
				$this->error('GoodsID已存在');
				exit;
			}
			
			unset($_POST['__hash__']);
			unset($_POST['Submit']);
			
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = '大厅商品新增';
			$data['userip'] = get_client_ip();
			$data['cate'] = 101;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			//$result = $add_table->add($_POST);
			//dump($add_table->_sql()); exit;
			if($result){
				//增加操作记录
				$logs = C('TSHANG_MSG_ADD_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				//通知服务器
				//$url = DB_HOST."/Pay/shang.php";
				//$jinbi_result = curlGET($url);
				
				$this->success('提交成功，等待审核', U($this->By_tpl.'/tgoods'));
				exit;
			}else{
				//增加操作记录
				$logs = C('TSHANG_MSG_ADD_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('添加失败');
				exit;
			}
		}else{
			//增加操作记录
			$logs = C('TSHANG_MSG_ADD');
			$remark = "";
			adminlog($logs, $remark);
		
			$this->assign('left_css',"7");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":tgoods_add";
			$this->display($lib_display);
		}
		
	}

	//大厅商品更新
	public function tgoods_update(){
		$Table = $this->Table_prifix."profile_mall_goods_data";
		$upate_table = M($Table);
		if(!empty($_POST)){
			
			$GoodsID = $_POST['GoodsID'];
			unset($_POST['Submit']);
			unset($_POST['__hash__']);
			
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = '大厅商品修改';
			$data['userip'] = get_client_ip();
			$data['cate'] = 102;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			//$id['GoodsID']=intval($GoodsID);
			//$result=$upate_table->where($id)->save($_POST);
			if($result){
				//增加操作记录
				$logs = C('TSHANG_MSG_EDIT_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				//通知服务器
				//$url = DB_HOST."/Pay/shang.php";
				//$jinbi_result = curlGET($url);
				
				$this->success('提交成功，等待审核',U($this->By_tpl.'/tgoods'));
			}else{
				//增加操作记录
				$logs = C('TSHANG_MSG_EDIT_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('修改失败');
			}
		}else{
			//增加操作记录
			$logs = C('TSHANG_MSG_EDIT');
			$remark = "";
			adminlog($logs, $remark);
			
			$id['GoodsID']=$_GET['GoodsID'];
			$info = $upate_table->where($id)->find();
			$this->assign('info',$info);
			$this->assign('left_css',"7");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":tgoods_update";
			$this->display($lib_display);
		}
	}
	
	//大厅商品删除
	public function tgoods_delete(){
		if(empty($_GET)){ 
			$this->error('非法操作');
			exit;
		}else{
			$GoodsID=$_GET['GoodsID']?$_GET['GoodsID']:$_POST['GoodsID'];
			$Tablename = $this->Table_prifix."profile_mall_goods_data";
			$delete_table = M($Tablename);
			//$where['GoodsID']=$GoodsID;
			//$result=$delete_table->where($where)->delete();
			$arr = $delete_table->where("GoodsID=".$GoodsID)->find();
			$arr['tablename'] = $Tablename;
			$arr['act'] = "del";
			
			//增加修改记录
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($arr, JSON_UNESCAPED_UNICODE);
			$data['remark'] = "删除大厅商品";
			$data['userip'] = get_client_ip();
			$data['cate'] = 103;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			if($result){
				//增加操作记录
				$logs = C('TSHANG_MSG_DEL_SUCCESS');
				$remark = "(".$GoodsID.")";
				adminlog($logs, $remark);
				
				//通知服务器
				//$url = DB_HOST."/Pay/shang.php";
				//$jinbi_result = curlGET($url);
				
				$this->success('提交成功，等待审核', U($this->By_tpl.'/tgoods'));
			}else{
				//增加操作记录
				$logs = C('TSHANG_MSG_DEL_FALSE');
				$remark = "(".$GoodsID.")";
				adminlog($logs, $remark);
				
				$this->error('删除失败');
				exit;
			}
		}
	}
	//大厅商品结束
	
	//IOS商品开始
	//IOS商品列表
	public function igoods(){
		
		$RoomID = I("RoomID");
		$ChannelID = I("ChannelID");
		$this->assign('RoomID',$RoomID);
		$this->assign('ChannelID',$ChannelID);
		
		$sql0 = "";
		if (!empty($RoomID)){
			$sql0 .= " and RoomID=$RoomID";
		}
		if (!empty($ChannelID)){
			$sql0 .= " and ChannelID=$ChannelID";
		}
		
		$Table = $this->Table_prifix."profile_mall_goods_data";
		$rowlist = M($Table);
		import('ORG.Util.Page');
		$count = $rowlist->where("IsQuickPay=0 and platform=1 $sql0")->count('GoodsID');
		$Page       = new Page($count,PAGE_SHOW);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		
		$list = $rowlist->where("IsQuickPay=0 and platform=1 $sql0")->order('GoodsID')->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($rowlist->_sql());
		foreach($list as $key=>$value){
			$list[$key]['IsFirstPayGive'] = ($value['IsFirstPayGive']=="1") ? "是" : "否";
			$list[$key]['IsEnableSms'] = ($value['IsEnableSms']=="1") ? "是" : "否";
			$list[$key]['IsFirstPay'] = ($value['IsFirstPay']=="1") ? "是" : "否";
			$list[$key]['Enable'] = ($value['Enable']=="1") ? "是" : "否";
			$list[$key]['IsPayed'] = ($value['IsPay']=="1") ? "是" : "否";
			$list[$key]['MallDisplay'] = ($value['MallDisplay']=="1") ? "是" : "否";
			if (!empty($value['GoodsName_D'])) $list[$key]['GoodsName'] .= "<br>".$value['GoodsName_D'];
			$list[$key]['GoldNum'] = number_format($value['GoldNum']);
		}
		
		//增加操作记录
		$logs = C('TSHANG_MSG_RECORD');
		$remark = "";
		adminlog($logs, $remark);
		
		$this->assign('left_css',"7");
		$this->assign('list',$list);
		$this->assign('pageshow',$show);
		
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl.":igoods";
		$this->display($lib_display);
	}
	
	//IOS商品添加
	public function igoods_add(){
		if(!empty($_POST)){
			$Table = $this->Table_prifix."profile_mall_goods_data";
			$add_table = M($Table);
			
			//unset($_POST['__hash__']);
			
			$count = $add_table->where("GoodsID=".$_POST['GoodsID'])->count('GoodsID');
			if ($count > 0){
				$this->error('GoodsID已存在');
				exit;
			}
			
			unset($_POST['__hash__']);
			unset($_POST['Submit']);
			
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = 'IOS商品新增';
			$data['userip'] = get_client_ip();
			$data['cate'] = 141;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			//$result = $add_table->add($_POST);
			//dump($add_table->_sql()); exit;
			if($result){
				//增加操作记录
				$logs = C('TSHANG_MSG_ADD_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				//通知服务器
				//$url = DB_HOST."/Pay/shang.php";
				//$jinbi_result = curlGET($url);
				
				$this->success('提交成功，等待审核', U($this->By_tpl.'/igoods'));
				exit;
			}else{
				//增加操作记录
				$logs = C('TSHANG_MSG_ADD_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('添加失败');
				exit;
			}
		}else{
			//增加操作记录
			$logs = C('TSHANG_MSG_ADD');
			$remark = "";
			adminlog($logs, $remark);
		
			$this->assign('left_css',"7");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":igoods_add";
			$this->display($lib_display);
		}
		
	}

	//IOS商品更新
	public function igoods_update(){
		$Table = $this->Table_prifix."profile_mall_goods_data";
		$upate_table = M($Table);
		if(!empty($_POST)){
			
			$GoodsID = $_POST['GoodsID'];
			unset($_POST['Submit']);
			unset($_POST['__hash__']);
			
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = 'IOS商品修改';
			$data['userip'] = get_client_ip();
			$data['cate'] = 142;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			//$id['GoodsID']=intval($GoodsID);
			//$result=$upate_table->where($id)->save($_POST);
			if($result){
				//增加操作记录
				$logs = C('TSHANG_MSG_EDIT_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				//通知服务器
				//$url = DB_HOST."/Pay/shang.php";
				//$jinbi_result = curlGET($url);
				
				$this->success('提交成功，等待审核',U($this->By_tpl.'/igoods'));
			}else{
				//增加操作记录
				$logs = C('TSHANG_MSG_EDIT_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('修改失败');
			}
		}else{
			//增加操作记录
			$logs = C('TSHANG_MSG_EDIT');
			$remark = "";
			adminlog($logs, $remark);
			
			$id['GoodsID']=$_GET['GoodsID'];
			$info = $upate_table->where($id)->find();
			$this->assign('info',$info);
			$this->assign('left_css',"7");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":igoods_update";
			$this->display($lib_display);
		}
	}
	
	//IOS商品删除
	public function igoods_delete(){
		if(empty($_GET)){ 
			$this->error('非法操作');
			exit;
		}else{
			$GoodsID=$_GET['GoodsID']?$_GET['GoodsID']:$_POST['GoodsID'];
			$Tablename = $this->Table_prifix."profile_mall_goods_data";
			$delete_table = M($Tablename);
			//$where['GoodsID']=$GoodsID;
			//$result=$delete_table->where($where)->delete();
			$arr = $delete_table->where("GoodsID=".$GoodsID)->find();
			$arr['tablename'] = $Tablename;
			$arr['act'] = "del";
			
			//增加修改记录
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($arr, JSON_UNESCAPED_UNICODE);
			$data['remark'] = "删除IOS商品";
			$data['userip'] = get_client_ip();
			$data['cate'] = 143;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			if($result){
				//增加操作记录
				$logs = C('TSHANG_MSG_DEL_SUCCESS');
				$remark = "(".$GoodsID.")";
				adminlog($logs, $remark);
				
				//通知服务器
				//$url = DB_HOST."/Pay/shang.php";
				//$jinbi_result = curlGET($url);
				
				$this->success('提交成功，等待审核', U($this->By_tpl.'/igoods'));
			}else{
				//增加操作记录
				$logs = C('TSHANG_MSG_DEL_FALSE');
				$remark = "(".$GoodsID.")";
				adminlog($logs, $remark);
				
				$this->error('删除失败');
				exit;
			}
		}
	}
	//IOS商品结束
	
	
	//IOS快充商品开始
	//IOS快充商品列表
	public function iqgoods(){
		
		$RoomID = I("RoomID");
		$ChannelID = I("ChannelID");
		$this->assign('RoomID',$RoomID);
		$this->assign('ChannelID',$ChannelID);
		
		$sql0 = "";
		if (!empty($RoomID)){
			$sql0 .= " and RoomID=$RoomID";
		}
		if (!empty($ChannelID)){
			$sql0 .= " and ChannelID=$ChannelID";
		}
		
		$Table = $this->Table_prifix."profile_mall_goods_data";
		$rowlist = M($Table);
		import('ORG.Util.Page');
		$count = $rowlist->where("IsQuickPay=1 and platform=1 $sql0")->count('GoodsID');
		$Page       = new Page($count,PAGE_SHOW);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		
		$list = $rowlist->where("IsQuickPay=1 and platform=1 $sql0")->order('GoodsID')->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($rowlist->_sql());
		foreach($list as $key=>$value){
			$list[$key]['IsFirstPayGive'] = ($value['IsFirstPayGive']=="1") ? "是" : "否";
			$list[$key]['IsEnableSms'] = ($value['IsEnableSms']=="1") ? "是" : "否";
			$list[$key]['IsFirstPay'] = ($value['IsFirstPay']=="1") ? "是" : "否";
			$list[$key]['Enable'] = ($value['Enable']=="1") ? "是" : "否";
			$list[$key]['IsPayed'] = ($value['IsPay']=="1") ? "是" : "否";
			$list[$key]['MallDisplay'] = ($value['MallDisplay']=="1") ? "是" : "否";
			if (!empty($value['GoodsName_D'])) $list[$key]['GoodsName'] .= "<br>".$value['GoodsName_D'];
			$list[$key]['GoldNum'] = number_format($value['GoldNum']);
		}
		
		//增加操作记录
		$logs = C('TSHANG_MSG_RECORD');
		$remark = "";
		adminlog($logs, $remark);
		
		$this->assign('left_css',"7");
		$this->assign('list',$list);
		$this->assign('pageshow',$show);
		
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl.":iqgoods";
		$this->display($lib_display);
	}
	
	//IOS快充商品添加
	public function iqgoods_add(){
		if(!empty($_POST)){
			$Table = $this->Table_prifix."profile_mall_goods_data";
			$add_table = M($Table);
			
			//unset($_POST['__hash__']);
			
			$count = $add_table->where("GoodsID=".$_POST['GoodsID'])->count('GoodsID');
			if ($count > 0){
				$this->error('GoodsID已存在');
				exit;
			}
			
			unset($_POST['__hash__']);
			unset($_POST['Submit']);
			
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = 'IOS快充商品新增';
			$data['userip'] = get_client_ip();
			$data['cate'] = 144;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			//$result = $add_table->add($_POST);
			//dump($add_table->_sql()); exit;
			if($result){
				//增加操作记录
				$logs = C('TSHANG_MSG_ADD_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				//通知服务器
				//$url = DB_HOST."/Pay/shang.php";
				//$jinbi_result = curlGET($url);
				
				$this->success('提交成功，等待审核', U($this->By_tpl.'/iqgoods'));
				exit;
			}else{
				//增加操作记录
				$logs = C('TSHANG_MSG_ADD_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('添加失败');
				exit;
			}
		}else{
			//增加操作记录
			$logs = C('TSHANG_MSG_ADD');
			$remark = "";
			adminlog($logs, $remark);
		
			$this->assign('left_css',"7");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":iqgoods_add";
			$this->display($lib_display);
		}
		
	}

	//IOS快充商品更新
	public function iqgoods_update(){
		$Table = $this->Table_prifix."profile_mall_goods_data";
		$upate_table = M($Table);
		if(!empty($_POST)){
			
			$GoodsID = $_POST['GoodsID'];
			unset($_POST['Submit']);
			unset($_POST['__hash__']);
			
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = 'IOS快充商品修改';
			$data['userip'] = get_client_ip();
			$data['cate'] = 145;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			//$id['GoodsID']=intval($GoodsID);
			//$result=$upate_table->where($id)->save($_POST);
			if($result){
				//增加操作记录
				$logs = C('TSHANG_MSG_EDIT_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				//通知服务器
				//$url = DB_HOST."/Pay/shang.php";
				//$jinbi_result = curlGET($url);
				
				$this->success('提交成功，等待审核',U($this->By_tpl.'/iqgoods'));
			}else{
				//增加操作记录
				$logs = C('TSHANG_MSG_EDIT_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('修改失败');
			}
		}else{
			//增加操作记录
			$logs = C('TSHANG_MSG_EDIT');
			$remark = "";
			adminlog($logs, $remark);
			
			$id['GoodsID']=$_GET['GoodsID'];
			$info = $upate_table->where($id)->find();
			$this->assign('info',$info);
			$this->assign('left_css',"7");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":iqgoods_update";
			$this->display($lib_display);
		}
	}
	
	//IOS快充商品删除
	public function iqgoods_delete(){
		if(empty($_GET)){ 
			$this->error('非法操作');
			exit;
		}else{
			$GoodsID=$_GET['GoodsID']?$_GET['GoodsID']:$_POST['GoodsID'];
			$Tablename = $this->Table_prifix."profile_mall_goods_data";
			$delete_table = M($Tablename);
			//$where['GoodsID']=$GoodsID;
			//$result=$delete_table->where($where)->delete();
			$arr = $delete_table->where("GoodsID=".$GoodsID)->find();
			$arr['tablename'] = $Tablename;
			$arr['act'] = "del";
			
			//增加修改记录
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($arr, JSON_UNESCAPED_UNICODE);
			$data['remark'] = "删除IOS快充商品";
			$data['userip'] = get_client_ip();
			$data['cate'] = 146;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			if($result){
				//增加操作记录
				$logs = C('TSHANG_MSG_DEL_SUCCESS');
				$remark = "(".$GoodsID.")";
				adminlog($logs, $remark);
				
				//通知服务器
				//$url = DB_HOST."/Pay/shang.php";
				//$jinbi_result = curlGET($url);
				
				$this->success('提交成功，等待审核', U($this->By_tpl.'/iqgoods'));
			}else{
				//增加操作记录
				$logs = C('TSHANG_MSG_DEL_FALSE');
				$remark = "(".$GoodsID.")";
				adminlog($logs, $remark);
				
				$this->error('删除失败');
				exit;
			}
		}
	}
	//IOS快充商品结束
	
	//IOS大厅商品开始
	//IOS大厅商品列表
	public function itgoods(){
		
		$RoomID = I("RoomID");
		$ChannelID = I("ChannelID");
		$this->assign('RoomID',$RoomID);
		$this->assign('ChannelID',$ChannelID);
		
		$sql0 = "";
		if (!empty($RoomID)){
			$sql0 .= " and RoomID=$RoomID";
		}
		if (!empty($ChannelID)){
			$sql0 .= " and ChannelID=$ChannelID";
		}
		
		$Table = $this->Table_prifix."profile_mall_goods_data";
		$rowlist = M($Table);
		import('ORG.Util.Page');
		$count = $rowlist->where("IsQuickPay=2 and platform=1 $sql0")->count('GoodsID');
		$Page       = new Page($count,PAGE_SHOW);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		
		$list = $rowlist->where("IsQuickPay=2 and platform=1 $sql0")->order('GoodsID')->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($rowlist->_sql());
		foreach($list as $key=>$value){
			$list[$key]['IsFirstPayGive'] = ($value['IsFirstPayGive']=="1") ? "是" : "否";
			$list[$key]['IsEnableSms'] = ($value['IsEnableSms']=="1") ? "是" : "否";
			$list[$key]['IsFirstPay'] = ($value['IsFirstPay']=="1") ? "是" : "否";
			$list[$key]['Enable'] = ($value['Enable']=="1") ? "是" : "否";
			$list[$key]['IsPayed'] = ($value['IsPay']=="1") ? "是" : "否";
			$list[$key]['MallDisplay'] = ($value['MallDisplay']=="1") ? "是" : "否";
			if (!empty($value['GoodsName_D'])) $list[$key]['GoodsName'] .= "<br>".$value['GoodsName_D'];
			$list[$key]['GoldNum'] = number_format($value['GoldNum']);
		}
		
		//增加操作记录
		$logs = C('TSHANG_MSG_RECORD');
		$remark = "";
		adminlog($logs, $remark);
		
		$this->assign('left_css',"7");
		$this->assign('list',$list);
		$this->assign('pageshow',$show);
		
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl.":itgoods";
		$this->display($lib_display);
	}
	
	//IOS大厅商品添加
	public function itgoods_add(){
		if(!empty($_POST)){
			$Table = $this->Table_prifix."profile_mall_goods_data";
			$add_table = M($Table);
			
			//unset($_POST['__hash__']);
			
			$count = $add_table->where("GoodsID=".$_POST['GoodsID'])->count('GoodsID');
			if ($count > 0){
				$this->error('GoodsID已存在');
				exit;
			}
			
			unset($_POST['__hash__']);
			unset($_POST['Submit']);
			
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = 'IOS大厅商品新增';
			$data['userip'] = get_client_ip();
			$data['cate'] = 147;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			//$result = $add_table->add($_POST);
			//dump($add_table->_sql()); exit;
			if($result){
				//增加操作记录
				$logs = C('TSHANG_MSG_ADD_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				//通知服务器
				//$url = DB_HOST."/Pay/shang.php";
				//$jinbi_result = curlGET($url);
				
				$this->success('提交成功，等待审核', U($this->By_tpl.'/itgoods'));
				exit;
			}else{
				//增加操作记录
				$logs = C('TSHANG_MSG_ADD_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('添加失败');
				exit;
			}
		}else{
			//增加操作记录
			$logs = C('TSHANG_MSG_ADD');
			$remark = "";
			adminlog($logs, $remark);
		
			$this->assign('left_css',"7");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":itgoods_add";
			$this->display($lib_display);
		}
		
	}

	//IOS大厅商品更新
	public function itgoods_update(){
		$Table = $this->Table_prifix."profile_mall_goods_data";
		$upate_table = M($Table);
		if(!empty($_POST)){
			
			$GoodsID = $_POST['GoodsID'];
			unset($_POST['Submit']);
			unset($_POST['__hash__']);
			
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = 'IOS大厅商品修改';
			$data['userip'] = get_client_ip();
			$data['cate'] = 148;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			//$id['GoodsID']=intval($GoodsID);
			//$result=$upate_table->where($id)->save($_POST);
			if($result){
				//增加操作记录
				$logs = C('TSHANG_MSG_EDIT_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				//通知服务器
				//$url = DB_HOST."/Pay/shang.php";
				//$jinbi_result = curlGET($url);
				
				$this->success('提交成功，等待审核',U($this->By_tpl.'/itgoods'));
			}else{
				//增加操作记录
				$logs = C('TSHANG_MSG_EDIT_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('修改失败');
			}
		}else{
			//增加操作记录
			$logs = C('TSHANG_MSG_EDIT');
			$remark = "";
			adminlog($logs, $remark);
			
			$id['GoodsID']=$_GET['GoodsID'];
			$info = $upate_table->where($id)->find();
			$this->assign('info',$info);
			$this->assign('left_css',"7");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":itgoods_update";
			$this->display($lib_display);
		}
	}
	
	//IOS大厅商品删除
	public function itgoods_delete(){
		if(empty($_GET)){ 
			$this->error('非法操作');
			exit;
		}else{
			$GoodsID=$_GET['GoodsID']?$_GET['GoodsID']:$_POST['GoodsID'];
			$Tablename = $this->Table_prifix."profile_mall_goods_data";
			$delete_table = M($Tablename);
			//$where['GoodsID']=$GoodsID;
			//$result=$delete_table->where($where)->delete();
			$arr = $delete_table->where("GoodsID=".$GoodsID)->find();
			$arr['tablename'] = $Tablename;
			$arr['act'] = "del";
			
			//增加修改记录
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($arr, JSON_UNESCAPED_UNICODE);
			$data['remark'] = "删除IOS大厅商品";
			$data['userip'] = get_client_ip();
			$data['cate'] = 149;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			if($result){
				//增加操作记录
				$logs = C('TSHANG_MSG_DEL_SUCCESS');
				$remark = "(".$GoodsID.")";
				adminlog($logs, $remark);
				
				//通知服务器
				//$url = DB_HOST."/Pay/shang.php";
				//$jinbi_result = curlGET($url);
				
				$this->success('提交成功，等待审核', U($this->By_tpl.'/itgoods'));
			}else{
				//增加操作记录
				$logs = C('TSHANG_MSG_DEL_FALSE');
				$remark = "(".$GoodsID.")";
				adminlog($logs, $remark);
				
				$this->error('删除失败');
				exit;
			}
		}
	}
	//IOS大厅商品结束
	
	//快速说明开始
	public function detail(){
		$Table = $this->Table_prifix."dynamic_config";
		$row = M($Table);
		
		if(!empty($_POST)){
			
			$table_name = M('user_record');
			$arr = array();
			$arr['quickpaytips'] = $_POST['key_value'];
			$arr['quickpaytips_ios'] = $_POST['key_value_ios'];
			$data = array();
			$data['logs'] = json_encode($arr, JSON_UNESCAPED_UNICODE);
			$data['remark'] = '快速说明修改';
			$data['userip'] = get_client_ip();
			$data['cate'] = 105;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			//$result = $row->where("key_name='quickpaytips'")->save($_POST);
			$key_value = $_POST['key_value'];	
			
			//通知服务器
			//$url = DB_HOST."/Pay/shang.php";
			//$jinbi_result = curlGET($url);
			if($result){
				//增加操作记录
				//$logs = C('QSHANG_MSG_DEL_SUCCESS');
				//$remark = "(".$GoodsID.")";
				//adminlog($logs, $remark);
				
				//通知服务器
				//$url = DB_HOST."/Pay/shang.php";
				//$jinbi_result = curlGET($url);
				
				$this->success('提交成功，等待审核', U($this->By_tpl.'/detail'));
				exit;
			}else{
				//增加操作记录
				//$logs = C('QSHANG_MSG_DEL_FALSE');
				//$remark = "(".$GoodsID.")";
				//adminlog($logs, $remark);
				
				$this->error('提交失败');
				exit;
			}
			
		}else{
			$res = $row->where("key_name='quickpaytips'")->find();
			$key_value = $res['key_value'];
			
			$res = $row->where("key_name='quickpaytips_ios'")->find();
			$key_value_ios = $res['key_value'];
		}
		$this->assign('key_value',$key_value);
		$this->assign('key_value_ios',$key_value_ios);
		$this->assign('left_css',"7");
		$lib_display = $this->By_tpl."/detail";
		$this->display($lib_display);
	}
	//快速说明结束
	
	//通知服务器开始
	public function notice(){
		//通知服务器
		$url = DB_HOST."/Pay/shang.php";
		$result = curlGET($url);
		$len = strlen($result) - 3;
		$status = substr($result, $len, 1);
		
		if ($status == "1"){
			$table = "user_record";
			$row = M($table);
			$data = array();
			$data['notice'] = "1";
			$data['noname'] = $_SESSION['username'];
			$data['nourl'] = $url;
			$result = $row->where("cate in (101,102,103,105) and notice=0")->save($data);
		} 
		
		echo $status;
	}
	//通知服务器结束
}