<?php
// 版本管理的文件

class VerAction extends BaseAction {

	protected $Table_prifix = 'ver_'; 
	protected $byid = '103'; 
	protected $byname = '宝贝猜拳'; 
	protected $By_tpl = 'Ver'; 
	
	//版本开始
	//版本列表
	public function hot(){
		//echo "xxx"; exit;
		
		$Tablename = $this->Table_prifix."version";
		$rowlist=M($Tablename);
		//echo "111";
		import('ORG.Util.Page');
		$count=$rowlist->where("states!=2")->count('id');
		$Page       = new Page($count,15);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		//echo "222"; exit;
		$list = $rowlist->where("states!=2")->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($rowlist->_sql());
		foreach($list as $key=>$value){
			$list[$key]['update_show'] = '';
			if (!empty($value['update_info'])){
				$update_info = json_decode($value['update_info'], true);
				//print_r($update_info);
				/*if (!empty($update_info)){
					foreach($update_info as $key2 => $val){
						$flag = ($val['flag']=="1") ? "强制更新" : "非强制更新";
						$list[$key]['update_show'] .= ($key2!=0) ? "<br>" : "";
						$list[$key]['update_show'] .= $update_info[$key2]['prev_ver']."=>".$update_info[$key2]['next_ver'].' <a href="'.$update_info[$key2]['update'].'" target="_blank">'.$update_info[$key2]['update'].'</a> '.$flag ;
					}
				}*/
				$list[$key]['update_show'] = json_decode($value['update_info'], true);
				//$list[$key]['update_info'] = "".$update_info['prev_ver']."=>".$update_info['next_ver'].' <a href="'.$update_info['update'].'" target="_blank">'.$update_info['update'].'</a>';
			}
			if ($value['type']=="1"){
				$list[$key]['type'] = "强制热更新";
			}elseif ($value['type']=="-1"){
				$list[$key]['type'] = "不强制热更新";
			}elseif ($value['type']=="2"){
				$list[$key]['type'] = "强制整包更新";
			}elseif ($value['type']=="-2"){
				$list[$key]['type'] = "不强制整包更新";
			}
			$list[$key]['addtime'] = (!empty($value['addtime'])) ? date("Y-m-d H:i:s", $value['addtime']) : "-";
			$list[$key]['publictime'] = (!empty($value['publictime'])) ? date("Y-m-d H:i:s", $value['publictime']) : "-";
		}
		
		$this->assign('left_css',"7");
		$this->assign('list',$list);
		$this->assign('pageshow',$show);
		
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/hot";
		$this->display($lib_display);
	}
	
	//版本更新记录
	public function record(){
		$Tablename = $this->Table_prifix."version";
		$rowlist=M($Tablename);
		import('ORG.Util.Page');
		$count=$rowlist->where("states=2")->count('id');
		$Page       = new Page($count,15);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		
		$list = $rowlist->where("states=2")->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($rowlist->_sql());
		foreach($list as $key=>$value){
			$list[$key]['update_show'] = '';
			if (!empty($value['update_info'])){
				
				$update_info = json_decode($value['update_info'], true);
				if (!empty($update_info)){
					foreach($update_info as $key2 => $val){
						$flag = ($val['flag']=="1") ? "强制" : "非强制";
						if ($val['status']=="1"){
							$status = "全部更新";
						}else if ($val['status']=="2"){
							$status = "部分更新";
						}else{
							$status = "不更新";
						} 
						
						$list[$key]['update_show'] .= ($key2!=0) ? "<br>" : "";
						$list[$key]['update_show'] .= $update_info[$key2]['prev_ver']."=>".$update_info[$key2]['next_ver'].' <a href="'.$update_info[$key2]['update'].'" target="_blank">'.$update_info[$key2]['update'].'</a>&nbsp;'.$flag.'&nbsp;'.$status.'&nbsp;'.$val['channel'];
					}
				}
				//$list[$key]['update_show'] = json_decode($value['update_info'], true);
				//$list[$key]['update_info'] = "".$update_info['prev_ver']."=>".$update_info['next_ver'].' <a href="'.$update_info['update'].'" target="_blank">'.$update_info['update'].'</a>';
			}
			if ($value['type']=="1"){
				$list[$key]['type'] = "强制热更新";
			}elseif ($value['type']=="-1"){
				$list[$key]['type'] = "不强制热更新";
			}elseif ($value['type']=="2"){
				$list[$key]['type'] = "强制整包更新";
			}elseif ($value['type']=="-2"){
				$list[$key]['type'] = "不强制整包更新";
			}
			$list[$key]['addtime'] = (!empty($value['addtime'])) ? date("Y-m-d H:i:s", $value['addtime']) : "-";
			$list[$key]['publictime'] = (!empty($value['publictime'])) ? date("Y-m-d H:i:s", $value['publictime']) : "-";
		}
		//print_r($list);
		$this->assign('left_css',"7");
		$this->assign('list',$list);
		$this->assign('pageshow',$show);
		
		//增加操作记录
		$logs = C('HOT_MSG_RECORD');
		adminlog($logs);
		
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/hot_record";
		$this->display($lib_display);
	}
	
	//版本添加
	public function hot_add(){
		$Tablename = $this->Table_prifix."version";
		$add_table = M($Tablename);
		if(!empty($_POST)){
			if (empty($_POST['version'])){
				$this->error('版本号不能为空');
				exit;
			}
			
			if (empty($_POST['type'])){
				$this->error('更新类型不能为空');
				exit;
			}
			
			//判断这个版本是否已存在
			$count = $add_table->where("version='".$_POST['version']."'")->count();
			//dump($add_table->_sql());
			//echo $count; 
			//exit;
			if ($count > 0){
				$this->error('该版本已存在');
				exit;
			}
			
			$data = array();
			$data['version']   = $_POST['version'];
			$data['type']   = $_POST['type'];
			$data['flag']   = json_encode($_POST['flag']);
			if ($data['type']=="-1") $data['flag'] = "";
			//$data['ip']   = trim($_POST['ip']);
			$data['detail']   = htmlspecialchars($_POST['detail']);
			$data['remark']   = htmlspecialchars($_POST['remark']);
			$data['createuser']   = $_SESSION['username'];
			$data['addtime']   = time();
			
			//判断这个目录是否存在
			$file_src = "apk/hot/".$data['version'];
			//echo $file_src; exit; 
			if (!file_exists($file_src)){
				$this->error($file_src.'目录未上传');
				exit;
			}
			
			//生成MD5值
			$res_list_file = md5_json($data['version']);
			if (!$res_list_file){
				$this->error('加密文件生成异常');
				exit;
			}
			
			$result=$add_table->add($data);
			
			//判断是否是整包更新，不是则判断是否有更早的版本，有则生成差异包
			if ($data['type']=="1" || $data['type']=="-1"){
				$list = $add_table->where('id!='.$result.' and states!=0')->order('id desc')->select();
				$show = array();
				foreach($list as $key=>$value){
					//判断是否强制更新
					$flag = 0;
					if ($data['type']=="1"){
						foreach($_POST['flag'] as $key2 => $val){
							if ($val == $value['version']){
								$flag = 1; break;
							}
						}
					}
					
					
					//生成差异包
					import("ORG.Util.Dir");
					//echo $value['version']."".$data['version']; exit;
					$update_now = version_chayi($value['version'], $data['version']);
					//echo $update_now; exit; 
					//update,src目录生成压缩文件
					//$items = $update_now."/update";
					//$zipname = dirname(__FILE__)."/".$update_now."/update.zip";
					/*
					$para = array();
					$para['items'] = $update_now."/update";
					$para['zipname'] = $update_now."/update.zip";
					//echo $para['items']."<br>".$para['zipname'];
					
					$oo = curlPOST2(DB_HOST."/zip.php", $para);
					//echo "**".$oo; exit;
					//echo ROOT_PATH.$para['items'];
					//exit;
					if (is_dir(ROOT_PATH.$para['items'])) Dir::delDir(ROOT_PATH.$para['items']);
					$para = array();
					$para['items'] = $update_now."/src";
					$para['zipname'] = $update_now."/src.zip";
					$oo = curlPOST2(DB_HOST."/zip.php", $para);
					if (is_dir(ROOT_PATH.$para['items'])) Dir::delDir(ROOT_PATH.$para['items']);
					*/
					//生成ZIP文件
					import('ORG.Util.PhpToZip');
					$cur_file = $update_now;
					$temp = explode("/", $update_now);
					$save_url = "";
					for($i=0; $i<count($temp)-1; $i++){
						$save_url .= ($i==0) ? $temp [$i] : "/".$temp [$i];	
					}
					$save_file = $save_url."/".$data['version']."_update.zip";
					//echo $save_file; 
					if (file_exists($save_file)) @unlink($save_file);
					//$zip = new PHPZip(); 
					//$zip->Zip($cur_file, $save_file); 
					$scandir = new HZip();
					$scandir->zipDir($cur_file, $save_file);
					$update = (!file_exists($save_file)) ? "" : DB_HOST."/".$save_file;
					$size = (!file_exists($save_file)) ? 0 : filesize($save_file);
					$show[] = array('prev_ver' => $value['version'],
									'next_ver' => $data['version'],
									'update' => $update,
									'size' => $size,
									'status' => '0',
									'channel' => '',
									'flag' => $flag);
					
					
					//dump($add_table->_sql());
					//echo $cur_file."***".$save_file."***".$this->is_empty_dir($cur_file)."<br>"; //exit;
				}
				$data1 = array();
				$data1['update_info'] = json_encode($show);
				$where = array();
				$where['id']=$result;
				$add_table->where($where)->save($data1);
			}
			//exit;
			
			//dump($employee->_sql());
			if($result){
				//上传文件
				/*
				import('ORG.Net.UploadFile');
				$upload = new UploadFile();
				$upload->savePath =  'apk/apk/';
				$upload->autoSub = true;
				$upload->subType   =  'date';
				if(!$upload->upload()) {// 上传错误提示错误信息
					//$this->error($upload->getErrorMsg());
				}else{
					$info =  $upload->getUploadFileInfo();
					
					$pic_src = DB_HOST."/".$upload->savePath.$info[0]['savename'];
					$data1 = array();
					$data1['src'] = $pic_src;
					$where = array();
					$where['id']=intval($result);
					$add_table->where($where)->save($data1);
				}*/
				//增加操作记录
				$logs = C('HOT_MSG_ADD_SUCCESS');
				$remark = "(新增热更新版本:".$_POST['version'].",".json_encode($data).")";
				adminlog($logs,$remark);
				
				$this->success('添加成功', U($this->By_tpl.'/hot'));
			}else{
				//增加操作记录
				$logs = C('HOT_MSG_ADD_FALSE');
				adminlog($logs);
				
				$this->error('添加失败');
				exit;
			}
		}else{
			//增加操作记录
			$logs = C('HOT_MSG_ADD');
			adminlog($logs);
			
			$list = $add_table->where("states!=0")->order('id')->select();
			$this->assign('old_ver',$list);

			$this->assign('left_css',"7");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl."/hot_add";
			$this->display($lib_display);
		}
		
	}

	//版本更新
	public function hot_edit(){
		$Tablename = $this->Table_prifix."version";
		$upate_table = M($Tablename);
		
		if(!empty($_POST)){
			
			$id=intval($_POST['id']);
			if (empty($_POST['version'])){
				$this->error('版本号不能为空');
				exit;
			}
			
			if (empty($_POST['type'])){
				$this->error('更新类型不能为空');
				exit;
			}
			
			//判断这个版本是否已存在
			$count = $upate_table->where("version='".$_POST['version']."' and states=1 and id!=".$id)->count();
			if ($count > 0){
				$this->error('该版本已存在');
				exit;
			}
			
			$data = array();
			$data['version']   = $_POST['version'];
			$data['type']   = $_POST['type'];
			$data['flag']   = json_encode($_POST['flag']);
			if ($data['type']=="-1") $data['flag'] = "";
			//$data['ip']   = trim($_POST['ip']);
			$data['detail']   = htmlspecialchars($_POST['detail']);
			$data['remark']   = htmlspecialchars($_POST['remark']);
			$data['createuser']   = $_SESSION['username'];
			$data['addtime']   = time();
			
			//判断这个目录是否存在
			$file_src = "apk/hot/".$data['version'];
			//echo $file_src; exit; 
			if (!file_exists($file_src)){
				$this->error($file_src.'目录未上传');
				exit;
			}
			
			//生成MD5值
			$res_list_file = md5_json($data['version']);
			if (!$res_list_file){
				$this->error('加密文件生成异常');
				exit;
			}
			
			$where = array('id' => $id);
			$result=$upate_table->where($where)->save($data);
			
			//判断是否是整包更新，不是则判断是否有更早的版本，有则生成差异包
			if ($data['type']=="1" || $data['type']=="-1"){
				$list = $upate_table->where('id!='.$id.' and states!=0')->order('id desc')->select();
				$show = array();
				foreach($list as $key=>$value){
					//判断是否强制更新
					$flag = 0;
					if ($data['type']=="1"){
						foreach($_POST['flag'] as $key2 => $val){
							if ($val == $value['version']){
								$flag = 1; break;
							}
						}
					}
					//生成差异包
					import("ORG.Util.Dir");
					//echo $value['version']."".$data['version']; exit;
					$update_now = version_chayi($value['version'], $data['version']);
					//echo $update_now; exit; 
					//update,src目录生成压缩文件
					//$items = $update_now."/update";
					//$zipname = dirname(__FILE__)."/".$update_now."/update.zip";
					$para = array();
					$para['items'] = $update_now."/update";
					$para['zipname'] = $update_now."/update.zip";
					$oo = curlPOST2(DB_HOST."/zip.php", $para);
					//echo "**".$oo; exit;
					//echo ROOT_PATH.$para['items'];
					if (is_dir(ROOT_PATH.$para['items'])) Dir::delDir(ROOT_PATH.$para['items']);
					$para = array();
					$para['items'] = $update_now."/src";
					$para['zipname'] = $update_now."/src.zip";
					$oo = curlPOST2(DB_HOST."/zip.php", $para);
					if (is_dir(ROOT_PATH.$para['items'])) Dir::delDir(ROOT_PATH.$para['items']);
					
					//生成ZIP文件
					import('ORG.Util.PhpToZip');
					$cur_file = $update_now;
					$temp = explode("/", $update_now);
					$save_url = "";
					for($i=0; $i<count($temp)-1; $i++){
						$save_url .= ($i==0) ? $temp [$i] : "/".$temp [$i];	
					}
					$save_file = $save_url."/".$data['version']."_update.zip";
					//echo $save_file; 
					if (file_exists($save_file)) @unlink($save_file);
					//$zip = new PHPZip(); 
					//$zip->Zip($cur_file, $save_file); 
					$scandir = new HZip();
					$scandir->zipDir($cur_file, $save_file);
					$update = (!file_exists($save_file)) ? "" : DB_HOST."/".$save_file;
					$size = (!file_exists($save_file)) ? 0 : filesize($save_file);
					$show[] = array('prev_ver' => $value['version'],
									'next_ver' => $data['version'],
									'update' => $update,
									'size' => $size,
									'status' => '0',
									'channel' => '',
									'flag' => $flag);
					
					
					//dump($add_table->_sql());
					//echo $cur_file."***".$save_file."***".$this->is_empty_dir($cur_file)."<br>"; //exit;
				}
				$data1 = array();
				$data1['update_info'] = json_encode($show);
				$where = array();
				$where['id']=$id;
				$upate_table->where($where)->save($data1);
			}
			
			if($result){
				//上传文件
				/*
				import('ORG.Net.UploadFile');
				$upload = new UploadFile();
				$upload->savePath =  'apk/apk/';
				$upload->autoSub = true;
				$upload->subType   =  'date';
				if(!$upload->upload()) {// 上传错误提示错误信息
					//$this->error($upload->getErrorMsg());
				}else{
					$info =  $upload->getUploadFileInfo();
					
					$pic_src = DB_HOST."/".$upload->savePath.$info[0]['savename'];
					$data1 = array();
					$data1['src'] = $pic_src;
					$where = array();
					$where['id']=intval($result);
					$add_table->where($where)->save($data1);
				}*/
				//增加操作记录
				$logs = C('HOT_MSG_EDIT_SUCCESS');
				$remark = "(修改热更新版本:".$_POST['version'].",".json_encode($data).")";
				adminlog($logs, $remark);
				
				$this->success('更新成功',U($this->By_tpl.'/hot'));
			}else{
				//增加操作记录
				$logs = C('HOT_MSG_EDIT_FALSE');
				adminlog($logs);
				
				$this->error('更新失败');
			}
		}else{
			//增加操作记录
			$logs = C('HOT_MSG_EDIT');
			adminlog($logs);
			
			$id['id']=$_GET['id'];
			$info=$upate_table->where($id)->find();
			$info['flag'] = json_decode($info['flag'], true);
			$this->assign('info',$info);
			
			$list = $upate_table->where("states!=0")->order('id')->select();
			foreach($list as $key => $val){
				$list[$key]['checked'] = in_array($val['version'],$info['flag']);
			}
			$this->assign('old_ver',$list);
			//print_r($info);
			
			$this->assign('left_css',"7");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl."/hot_edit";
			$this->display($lib_display);
		}
	}
	
	//版本更新状态
	public function hot_do(){
		$Tablename = $this->Table_prifix."version";
		$upate_table = M($Tablename);
		$id['id']=$_POST['id'];
		if (!empty($id['id'])){
			$data1 = array();
			$data1['states'] = 1;
			$data1['publictime'] = time();
			$info = array();

			foreach($_POST['prev_ver'] as $key => $val){
					$info[$key]['prev_ver']	= $val;
					$info[$key]['next_ver']	= $_POST['next_ver'][$key];	
					$info[$key]['update']	= $_POST['update'][$key];
					$info[$key]['size']	= $_POST['size'][$key];
					$info[$key]['flag']	= $_POST['flag'][$key];
					$info[$key]['status']	= $_POST['status'][$key];
					$info[$key]['channel']	= ($_POST['status'][$key]=="2") ? str_replace("，",",",$_POST['channel'][$key]) : "";
			}	
			//print_r($info); exit;
			$data1['update_info'] = json_encode($info);
			$upate_table->where($id)->save($data1);
			
			//生成缓存
			//记录当前最低版本号
			$row = $upate_table->order('id ASC')->select();
			S("Min_hot_version", $row[0]['version']);
			$info=$upate_table->where($id)->find();
			//当前版本号
			S("Max_hot_version", $info['version']);
			if (!empty($info['update_info'])){
				$update_info = json_decode($info['update_info'], true);
				foreach($update_info as $key => $val){
					$s_name = $val['prev_ver']."_".$val['next_ver']."_src";
					S($s_name, $val['update']);
					//echo $s_name."***".$val['update']."<br>"; 
					$s_name = $val['prev_ver']."_".$val['next_ver']."_flag";
					S($s_name, $val['flag']);
					
					$s_name = $val['prev_ver']."_".$val['next_ver']."_size";
					S($s_name, $val['size']);
					
					$s_name = $val['prev_ver']."_".$val['next_ver']."_status";
					S($s_name, $val['status']);
					
					$s_name = $val['prev_ver']."_".$val['next_ver']."_channel";
					S($s_name, $val['channel']);
					//echo $s_name."***".$val['flag']."<br>"; 
				}
			}
			
			$data1 = array();
			$data1['states'] = 2;
			$upate_table->where('id!='.$_POST['id'].' and states=1')->save($data1);
			//exit;
			//增加操作记录
			$logs = C('HOT_MSG_PUB_SUCCESS');
			adminlog($logs);
			
			$this->success('更新成功',U($this->By_tpl.'/hot'));
		}else{
			//增加操作记录
			$logs = C('HOT_MSG_PUB_FALSE');
			adminlog($logs);
			
			$this->error('更新失败');
		}
	}
	
	//版本删除
	public function version_delete(){
		if(empty($_GET)){ 
			$this->error('非法操作');
			exit;
		}else{
			$id=$_GET['id']?$_GET['id']:$_POST['id'];
			$Tablename = $this->Table_prifix."goods";
			$delete_table = M($Tablename);
			$where['id']=$id;
			$result=$delete_table->where($where)->delete();
			if($result){
				//建立产品缓存
				$tablename = $this->Table_prifix."goods";
				$table = M($tablename);
				$goods = $table->field('goods_id,goods_name,goods_price,goods_details')->order('id')->select();
				S($tablename, $goods);
				
				$this->success('删除成功', U($this->By_tpl.'/goods'));
			}else{
				$this->error('删除失败');
				exit;
			}
		}
	}
	//版本结束
	
	
	//版本开始
	//APK版本列表
	public function apk(){
		$Tablename = $this->Table_prifix."apk";
		$rowlist=M($Tablename);
		$Tablename2 = $this->Table_prifix."apk_channel";
		$rowlist2 = M($Tablename2);
		import('ORG.Util.Page');
		$count=$rowlist->where("states!=2")->count('id');
		$Page       = new Page($count,15);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		
		$list = $rowlist->where("states!=2")->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($rowlist->_sql());
		foreach($list as $key=>$value){
			$list[$key]['update_show'] = '';
			if (!empty($value['update_info'])){
				$update_info = json_decode($value['update_info'], true);
				//print_r($update_info);
				/*if (!empty($update_info)){
					foreach($update_info as $key2 => $val){
						$flag = ($val['flag']=="1") ? "强制更新" : "非强制更新";
						$list[$key]['update_show'] .= ($key2!=0) ? "<br>" : "";
						$list[$key]['update_show'] .= $update_info[$key2]['prev_ver']."=>".$update_info[$key2]['next_ver'].' <a href="'.$update_info[$key2]['update'].'" target="_blank">'.$update_info[$key2]['update'].'</a> '.$flag ;
					}
				}*/
				$list[$key]['update_show'] = json_decode($value['update_info'], true);
				//$list[$key]['update_info'] = "".$update_info['prev_ver']."=>".$update_info['next_ver'].' <a href="'.$update_info['update'].'" target="_blank">'.$update_info['update'].'</a>';
			}
			
			if ($value['type']=="1"){
				$list[$key]['type'] = "强制热更新";
			}elseif ($value['type']=="-1"){
				$list[$key]['type'] = "不强制热更新";
			}elseif ($value['type']=="2"){
				$list[$key]['type'] = "强制整包更新";
			}elseif ($value['type']=="-2"){
				$list[$key]['type'] = "不强制整包更新";
			}
			
			$str = "";
			if (!empty($value['flag'])){
				$flag = json_decode($value['flag'], true);
				foreach($flag as $key2=> $val){
					$str .= ($key2!=0) ? ",".$val : $val;	
				}
			}
			$list[$key]['str'] = $str;
			
			$list[$key]['addtime'] = (!empty($value['addtime'])) ? date("Y-m-d H:i:s", $value['addtime']) : "-";
			$list[$key]['publictime'] = (!empty($value['publictime'])) ? date("Y-m-d H:i:s", $value['publictime']) : "-";
			
			//$list[$key]['sub'] = array();
			$list3 = $rowlist2->where("version='".$value['version']."'")->order('id DESC')->limit(0,10)->select();
			//dump($rowlist2->_sql());
			$list[$key]['channel'] = "";
			foreach($list3 as $key3=>$val3){
				$list[$key]['channel'] .= (empty($list[$key]['channel'])) ? $val3['channel'] : ",".$val3['channel'];
			}
		}
		//print_r($list);
		$this->assign('left_css',"7");
		$this->assign('list',$list);
		$this->assign('pageshow',$show);
		
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/apk";
		$this->display($lib_display);
	}
	
	//APK版本更新记录
	public function history(){
		$Tablename = $this->Table_prifix."apk";
		$rowlist=M($Tablename);
		import('ORG.Util.Page');
		$count=$rowlist->where("states=2")->count('id');
		$Page       = new Page($count,15);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		
		$list = $rowlist->where("states=2")->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($rowlist->_sql());
		foreach($list as $key=>$value){
			$list[$key]['update_show'] = '';
			if (!empty($value['update_info'])){
				$update_info = json_decode($value['update_info'], true);
				//print_r($update_info);
				if (!empty($update_info)){
					foreach($update_info as $key2 => $val){
						$flag = ($val['flag']=="1") ? "强制" : "非强制";
						if ($val['status']=="1"){
							$status = "全部更新";
						}else if ($val['status']=="2"){
							$status = "部分更新";
						}else{
							$status = "不更新";
						} 
						
						$list[$key]['update_show'] .= ($key2!=0) ? "<br>" : "";
						$list[$key]['update_show'] .= $update_info[$key2]['prev_ver']."=>".$update_info[$key2]['next_ver'].'&nbsp;'.$flag.'&nbsp;'.$status.'&nbsp;'.$val['channel'];
					}
				}
				//$list[$key]['update_show'] = json_decode($value['update_info'], true);
				//$list[$key]['update_info'] = "".$update_info['prev_ver']."=>".$update_info['next_ver'].' <a href="'.$update_info['update'].'" target="_blank">'.$update_info['update'].'</a>';
			}
			
			if ($value['type']=="1"){
				$list[$key]['type'] = "强制热更新";
			}elseif ($value['type']=="-1"){
				$list[$key]['type'] = "不强制热更新";
			}elseif ($value['type']=="2"){
				$list[$key]['type'] = "强制整包更新";
			}elseif ($value['type']=="-2"){
				$list[$key]['type'] = "不强制整包更新";
			}
			
			$str = "";
			if (!empty($value['flag'])){
				$flag = json_decode($value['flag'], true);
				foreach($flag as $key2=> $val){
					$str .= ($key2!=0) ? ",".$val : $val;	
				}
			}
			$list[$key]['str'] = $str;
			
			$list[$key]['addtime'] = (!empty($value['addtime'])) ? date("Y-m-d H:i:s", $value['addtime']) : "-";
			$list[$key]['publictime'] = (!empty($value['publictime'])) ? date("Y-m-d H:i:s", $value['publictime']) : "-";
		}
		
		$this->assign('left_css',"7");
		$this->assign('list',$list);
		$this->assign('pageshow',$show);
		
		//增加操作记录
		$logs = C('VER_MSG_HIRTORY');
		adminlog($logs);
		
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/history";
		$this->display($lib_display);
	}
	
	//APK版本添加
	public function apk_add(){
		$Tablename = $this->Table_prifix."apk";
		$add_table = M($Tablename);
		$Tablename2 = $this->Table_prifix."apk_channel";
		$add_table2 = M($Tablename2);
		if(!empty($_POST)){
			
			if (empty($_POST['version'])){
				$this->error('版本号不能为空');
				exit;
			}
			
			if ($_POST['type']==""){
				$this->error('更新类型不能为空');
				exit;
			}
			
			//判断这个版本是否已存在
			$count = $add_table->where("version='".$_POST['version']."' and states=1")->count();
			if ($count > 0){
				$this->error('该版本已存在');
				exit;
			}
			
			//判断这个目录是否存在
			$file_src = "apk/down/".$_POST['version'];
			//echo $file_src; exit; 
			if (!file_exists($file_src)){
				$this->error($file_src.'目录未上传');
				exit;
			}
		
			$data = array();
			$data['version']   = $_POST['version'];
			$data['flag']   = json_encode($_POST['flag']);
			$data['channel']   = $_POST['channel'];
			$data['type']   = $_POST['type'];
			if ($data['type']=="-1") $data['flag'] = "";
			//$data['ip']   = trim($_POST['ip']);
			$data['detail']   = htmlspecialchars($_POST['detail']);
			//$data['remark']   = htmlspecialchars($_POST['remark']);
			$data['createuser']   = $_SESSION['username'];
			$data['addtime']   = time();
			
			
			$result=$add_table->add($data);
			
			//dump($employee->_sql());
			if($result){
				//生成差异更新记录
				$list = $add_table->where('id!='.$result.' and states!=0')->order('id desc')->select();
				$show = array();
				foreach($list as $key=>$value){
					//判断是否强制更新
					$flag = 0;
					if ($data['type']=="1"){
						foreach($_POST['flag'] as $key2 => $val){
							if ($val == $value['version']){
								$flag = 1; break;
							}
							//echo $val."**".$val."<br>";
						}
					}
					$show[] = array('prev_ver' => $value['version'],
									'next_ver' => $data['version'],
									'status' => '0',
									'channel' => '',
									'flag' => $flag);
				}
				//print_r($show); exit;
				$data1 = array();
				$data1['update_info'] = json_encode($show);
				$where = array();
				$where['id']=$result;
				$add_table->where($where)->save($data1);
				/*
				//上传文件
				import('ORG.Net.UploadFile');
				$upload = new UploadFile();
				$upload->savePath =  ROOT_PATH.'apk/down/';
				$upload->autoSub = true;
				$upload->subType   =  'date';
				if(!$upload->upload()) {// 上传错误提示错误信息
					//$this->error($upload->getErrorMsg());
				}else{
					$info =  $upload->getUploadFileInfo();
					
					$pic_src = DB_HOST."/apk/down/".$info[0]['savename'];
					$data1 = array();
					$data1['src'] = $pic_src;
					$where = array();
					$where['id']=intval($result);
					$add_table->where($where)->save($data1);
				}*/
				$data2 = array();
				$files = $this->listDir($file_src);
				foreach($files as $key => $val){
					$temp1 = explode($file_src, $val);
					$temp2 = explode("-", $temp1[1]);
					$name1 = str_replace("/","",$temp2[0]);
					$name2 = $temp2[1];
					$name3 = $temp2[2];
					$name4 = str_replace(".apk","",$temp2[3]);
					$name4 = str_replace("c","",$name4);
					//echo $name1."*".$name2."*".$name3."*".$name4."<br>";
					$data2['version']   = $_POST['version'];
					$data2['channel']   = $name4;
					$data2['apk_src']   = DB_HOST."/".$val;
					$data2['flag']   = "-1";
					$add_table2->add($data2);
				}
				//exit;
				
				//增加操作记录
				$logs = C('VER_MSG_ADD_SUCCESS');
				$remark = "(新增整包更新:".$_POST['version'].",".json_encode($data).")";
				adminlog($logs,$remark);
				//exit;
				$this->success('添加成功', U($this->By_tpl.'/apk'));
			}else{
				//增加操作记录
				$logs = C('VER_MSG_ADD_FALSE');
				adminlog($logs);
				
				$this->error('添加失败');
				exit;
			}
		}else{
			//增加操作记录
			$logs = C('VER_MSG_ADD');
			adminlog($logs);
			
			$list = $add_table->where("states!=0")->order('id')->select();
			$this->assign('old_ver',$list);

			$this->assign('left_css',"7");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl."/apk_add";
			$this->display($lib_display);
		}
		
	}

	//APK版本更新
	public function apk_edit(){
		$Tablename = $this->Table_prifix."apk";
		$upate_table = M($Tablename);
		$Tablename2 = $this->Table_prifix."apk_channel";
		$add_table2 = M($Tablename2);
		
		if(!empty($_POST)){
			
			$id=intval($_POST['id']);
			if (empty($_POST['version'])){
				$this->error('版本号不能为空');
				exit;
			}
			
			if ($_POST['type']==""){
				$this->error('更新类型不能为空');
				exit;
			}
			
			//判断这个版本是否已存在
			$count = $upate_table->where("version='".$_POST['version']."' and states=1 and id!=".$id)->count();
			if ($count > 0){
				$this->error('该版本已存在');
				exit;
			}
			
			//判断这个目录是否存在
			$file_src = "apk/down/".$_POST['version'];
			//echo $file_src; exit; 
			if (!file_exists($file_src)){
				$this->error($file_src.'目录未上传');
				exit;
			}
			
			$data = array();
			$data['version']   = $_POST['version'];
			$data['flag']   = json_encode($_POST['flag']);
			$data['channel']   = $_POST['channel'];
			$data['type']   = $_POST['type'];
			if ($data['type']=="-1") $data['flag'] = "";
			//$data['ip']   = trim($_POST['ip']);
			$data['detail']   = htmlspecialchars($_POST['detail']);
			$data['remark']   = htmlspecialchars($_POST['remark']);
			$data['createuser']   = $_SESSION['username'];
			$data['addtime']   = time();
			
		
			$where = array('id' => $id);
			$result=$upate_table->where($where)->save($data);
			
		
			if($result){
				//生成差异更新记录
				$list = $upate_table->where('id!='.$id.' and states!=0')->order('id desc')->select();
				$show = array();
				foreach($list as $key=>$value){
					//判断是否强制更新
					$flag = 0;
					if ($data['type']=="1"){
						foreach($_POST['flag'] as $key2 => $val){
							if ($val == $value['version']){
								$flag = 1; break;
							}
						}
					}
					$show[] = array('prev_ver' => $value['version'],
									'next_ver' => $data['version'],
									'status' => '0',
									'channel' => '',
									'flag' => $flag);
				}
				$data1 = array();
				$data1['update_info'] = json_encode($show);
				$where = array();
				$where['id']=$id;
				$upate_table->where($where)->save($data1);
				
				//上传文件
				/*import('ORG.Net.UploadFile');
				$upload = new UploadFile();
				$upload->savePath =  ROOT_PATH.'apk/down/';
				//echo $upload->savePath; exit; 
				$upload->autoSub = true;
				$upload->subType   =  'date';
				if(!$upload->upload()) {// 上传错误提示错误信息
					//$this->error($upload->getErrorMsg());
				}else{
					$info =  $upload->getUploadFileInfo();
					
					$pic_src = DB_HOST."/apk/down/".$info[0]['savename'];
					$data1 = array();
					$data1['src'] = $pic_src;
					$where = array();
					$where['id']=intval($id);
					$upate_table->where($where)->save($data1);
				}*/
				$data2 = array();
				$files = $this->listDir($file_src);
				foreach($files as $key => $val){
					$temp1 = explode($file_src, $val);
					$temp2 = explode("-", $temp1[1]);
					$name1 = str_replace("/","",$temp2[0]);
					$name2 = $temp2[1];
					$name3 = $temp2[2];
					$name4 = str_replace(".apk","",$temp2[3]);
					$name4 = str_replace("c","",$name4);
					//echo $name1."*".$name2."*".$name3."*".$name4."<br>";
					$data2['version']   = $_POST['version'];
					$data2['channel']   = $name4;
					$data2['apk_src']   = DB_HOST."/".$val;
					$data2['flag']   = "-1";
					$where2 = array();
					$where2['version'] = $_POST['version'];
					$where2['channel'] = $name4;
					$count = $add_table2->where($where2)->count();
					if ($count==0){
						$add_table2->add($data2);
					} 
				
				}
				
				//增加操作记录
				$logs = C('VER_MSG_EDIT_SUCCESS');
				$remark = "(修改整包更新:".$_POST['version'].",".json_encode($data).")";
				adminlog($logs, $remark);
				
				$this->success('更新成功',U($this->By_tpl.'/apk'));
			}else{
				//增加操作记录
				$logs = C('VER_MSG_EDIT_FALSE');
				adminlog($logs);
				
				$this->error('更新失败');
			}
		}else{
			//增加操作记录
			$logs = C('VER_MSG_EDIT');
			adminlog($logs);
			
			$id['id']=$_GET['id'];
			$info=$upate_table->where($id)->find();
			$info['flag'] = json_decode($info['flag'], true);
			$list = $upate_table->where("states!=0")->order('id')->select();
			foreach($list as $key => $val){
				$list[$key]['checked'] = in_array($val['version'],$info['flag']);
			}
			$this->assign('old_ver',$list);
			//print_r($info);
			$this->assign('info',$info);
			$this->assign('left_css',"7");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl."/apk_edit";
			$this->display($lib_display);
		}
	}
	
	//APK版本更新状态
	public function apk_do(){
		$Tablename = $this->Table_prifix."apk";
		$upate_table = M($Tablename);
		$Tablename2 = $this->Table_prifix."apk_channel";
		$add_table2 = M($Tablename2);
		$id['id']=$_POST['id'];
		if (!empty($id['id'])){
			$data1 = array();
			$data1['states'] = 1;
			$data1['publictime'] = time();
			$info = array();
			foreach($_POST['prev_ver'] as $key => $val){
					$info[$key]['prev_ver']	= $val;
					$info[$key]['next_ver']	= $_POST['next_ver'][$key];	
					$info[$key]['flag']	= $_POST['flag'][$key];
					$info[$key]['status']	= $_POST['status'][$key];
					$info[$key]['channel']	= ($_POST['status'][$key]=="2") ? str_replace("，",",",$_POST['channel'][$key]) : "";
			}	
			//print_r($info); exit;
			$data1['update_info'] = json_encode($info);
			$upate_table->where($id)->save($data1);
			
			$data1 = array();
			$data1['states'] = 2;
			$upate_table->where('id!='.$_POST['id'].' and states=1')->save($data1);
			
			//生成缓存
			$info=$upate_table->where($id)->find();
			//当前版本号
			S("Max_full_version", $info['version']);
			//S("Max_full_src", $info['src']);
			if (!empty($info['update_info'])){
				$update_info = json_decode($info['update_info'], true);
				foreach($update_info as $key => $val){
					$s_name = "FULL_".$val['prev_ver']."_".$val['next_ver']."_flag";
					S($s_name, $val['flag']);
					
					$s_name = "FULL_".$val['prev_ver']."_".$val['next_ver']."_status";
					S($s_name, $val['status']);
					
					$s_name = "FULL_".$val['prev_ver']."_".$val['next_ver']."_channel";
					S($s_name, $val['channel']);
					//echo $s_name."***".$val['flag']."<br>"; 
				}
			}
			
			$list3 = $add_table2->where("version='".$info['version']."'")->order('id DESC')->select();
			foreach($list3 as $key3=>$val3){
				$s_name = "FULL_".$info['version']."_".$val3['channel']."_src";
				S($s_name, $val3['apk_src']);
				//echo $s_name."**".S($s_name)."<br>";
			}
			//exit;
			//增加操作记录
			$logs = C('VER_MSG_PUB_SUCCESS');
			adminlog($logs);

			//exit;
			$this->success('更新成功',U($this->By_tpl.'/apk'));
		}else{
			//增加操作记录
			$logs = C('VER_MSG_PUB_FALSE');
			adminlog($logs);
			
			$this->error('更新失败');
		}
	}
	
	//APK版本删除
	public function apk_delete(){
		if(empty($_GET)){ 
			$this->error('非法操作');
			exit;
		}else{
			$id=$_GET['id']?$_GET['id']:$_POST['id'];
			$Tablename = $this->Table_prifix."goods";
			$delete_table = M($Tablename);
			$where['id']=$id;
			$result=$delete_table->where($where)->delete();
			if($result){
				//建立产品缓存
				$tablename = $this->Table_prifix."goods";
				$table = M($tablename);
				$goods = $table->field('goods_id,goods_name,goods_price,goods_details')->order('id')->select();
				S($tablename, $goods);
				
				$this->success('删除成功', U($this->By_tpl.'/goods'));
			}else{
				$this->error('删除失败');
				exit;
			}
		}
	}
	//APK版本结束
	
	//历遍目录
	public function listDir($dir){
		$dir .= substr($dir, -1) == '/' ? '' : '/';
		$dirInfo = array();
		foreach (glob($dir.'*') as $v) {
			$dirInfo[] = $v; 
			if(is_dir($v)){
				$dirInfo = array_merge($dirInfo, listDir($v));
			}
		}
		return $dirInfo;
	}
	
	//判断目录是否为空
	public function is_empty_dir($fp)    
    {    
        $H = @opendir($fp); 
        $i=0;    
        while($_file=readdir($H)){    
            $i++;    
        }    
        closedir($H);    
        if($i>2){ 
            return 1; 
        }else{ 
            return 2;  //true
        } 
    } 
	
	//空方法
	 public function _empty() {  
        $this->display("Index/index");
    }
	
	//APK渠道限制开始
	//APK渠道限制列表
	public function climit(){
		$table = M('ver_apk_limit');
		import('ORG.Util.Page');
		$count = $table->count('id');
		$Page       = new Page($count,50);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		$list = $table->order('id')->limit($Page->firstRow.','.$Page->listRows)->select();
		foreach($list as $key=>$value){
			//$list[$key]['addtime'] = date("Y-m-d H:i:s");
		}
		$this->assign('left_css',"7");
		$this->assign('list',$list);
		$this->assign('pageshow',$show);
		$this->display('Ver:climit_list');
	}
	
	//APK渠道限制添加
	public function climit_add(){
		if(!empty($_POST)){
			if(empty($_POST['channel'])){
				$this->error('数据不完整，请补充完整');
				exit;
			}
			//$_POST['addtime'] = time();
			$table = M('ver_apk_limit');
			$result = $table->add($_POST);
			//dump($employee->_sql());
			if($result){
				//增加操作记录
				$logs = C('CLIMIT_MSG_ADD_SUCCESS');
				$remark = "(新增操作:".$_POST['channel'].",".json_encode($_POST).")";
				adminlog($logs,$remark);
				
				//生成整包限制缓存
				$list = $table->order('id')->select();
				$s_name = "Full_channel_limit";
				$s_val = "";
				foreach($list as $key=>$val){
					$s_val .= ($key==0) ? $val['channel'] : ",".$val['channel'];
				}
				S($s_name, $s_val);
				
				$this->success('添加成功',U('Ver/climit'));
			}else{
				//增加操作记录
				$logs = C('CLIMIT_MSG_ADD_FALSE');
				adminlog($logs);
				
				$this->error('添加失败');
				exit;
			}
		}else{
			//增加操作记录
			$logs = C('CLIMIT_MSG_ADD');
			adminlog($logs);
			
			$this->assign('left_css',"7");
			$this->display('Ver:climit_add');
		}
		
	}

	//APK渠道限制更新
	public function climit_edit(){
		$table = M('ver_apk_limit');
		$id = I('id');
		$where['id'] = $id;
		if(empty($id)){
			$this->error('数据异常');
			exit;
		}
		if(!empty($_POST)){
			if(empty($_POST['channel'])){
				$this->error('数据不完整，请补充完整');
				exit;
			}
			$result = $table->where($where)->save($_POST);
			if($result){
				//增加操作记录
				$logs = C('CLIMIT_MSG_EDIT_SUCCESS');
				$remark = "(修改操作:".$id.",".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				//生成整包限制缓存
				$list = $table->order('id')->select();
				$s_name = "Full_channel_limit";
				$s_val = "";
				foreach($list as $key=>$val){
					$s_val .= ($key==0) ? $val['channel']: ",".$val['channel'];
				}
				S($s_name, $s_val);
				
				$this->success('更新成功',U('Ver/climit'));
			}else{
				//增加操作记录
				$logs = C('CLIMIT_MSG_EDIT_FALSE');
				adminlog($logs);
				
				$this->error('更新失败');
			}
		}else{
			//增加操作记录
			$logs = C('CLIMIT_MSG_EDIT');
			adminlog($logs);
			
			$info = $table->where($where)->find();
			$this->assign('info',$info);
			$this->assign('left_css',"7");
			$this->display('Ver:climit_edit');
		}
	}
	
	//APK渠道限制删除
	public function climit_delete(){
		$table = M('ver_apk_limit');
		$id = I('id');
		$where['id'] = $id;
		if(empty($id)){
			$this->error('数据异常');
			exit;
		}else{
			$result = $table->where($where)->delete();
			if($result){
				//增加操作记录
				$logs = C('CLIMIT_MSG_DEL_SUCCESS');
				$remark = "(删除操作:".$id.")";
				adminlog($logs,$remark);
				
				//生成整包限制缓存
				$list = $table->order('id')->select();
				$s_name = "Full_channel_limit";
				$s_val = NULL;
				foreach($list as $key=>$val){
					$s_val .= ($key==0) ? $val['channel'] : ",".$val['channel'];
				}
				S($s_name, $s_val);
				
				$this->success('删除成功',U('Ver/climit'));
			}else{
				//增加操作记录
				$logs = C('CLIMIT_MSG_DEL_FALSE');
				adminlog($logs);
				
				$this->error('删除失败');
				exit;
			}
		}
	}
	//APK渠道限制结束
	
	//APK生成渠道包开始
	public function channel(){
		//$path = I('path');
		$channel_begin = (int)I('channel_begin');
		$channel_end = (int)I('channel_end');
		
		if ($channel_begin > $channel_end){
			$this->error('开始渠道号不能大于结束渠道号');
			exit;
		}
		
		if (!empty($channel_begin) && !empty($channel_end)){
			//修改channel.txt
			$channel_txt = $channel_begin."\n".$channel_end;
			$channel_file = "PythonTool/info/channel.txt";
			file_put_contents($channel_file, $channel_txt);
			
			
			$url = DB_HOST."/PythonTool/test.php";
			$result = curlGET($url);
			$this->assign('result',$result);
			$this->assign('left_css',"7");
			$this->display('Ver:channel');
		}else{
			$this->assign('left_css',"7");
			$this->display('Ver:channel');
		}
	}
	//APK生成渠道包结束
}