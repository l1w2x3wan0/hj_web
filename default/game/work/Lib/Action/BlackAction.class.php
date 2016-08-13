<?php
// 黑名单管理的文件

class BlackAction extends BaseAction {
	protected $By_tpl = 'Black'; 
	protected $Table_prifix = MYTABLE_PRIFIX; 
	//黑名单开始
	//黑名单列表
	public function mingdan(){
		
		$user_id = I("user_id");
		$this->assign('user_id',$user_id);
		
		$sql0 = "";
		if (!empty($user_id)){
			$sql0 .= " user_id like '%$user_id%'";
		}
		
		$Table = $this->Table_prifix."user_blacklist";
		$rowlist = M($Table);
		import('ORG.Util.Page');
		$count = $rowlist->where($sql0)->count('id');
		$Page       = new Page($count,PAGE_SHOW);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		
		$list = $rowlist->where($sql0)->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($rowlist->_sql());
		foreach($list as $key=>$value){
			$list[$key]['hornshow'] = ($value['horn']=="1") ? "禁止" : "正常";
			
		}
		
		//增加操作记录
		$logs = C('BLACK_MSG_RECORD');
		$remark = "";
		adminlog($logs, $remark);
		
		$this->assign('left_css',"50");
		$this->assign('list',$list);
		$this->assign('pageshow',$show);
		
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl.":mingdan";
		$this->display($lib_display);
	}

	
	//黑名单添加
	public function mingdan_add(){
		if(!empty($_POST)){
			$Table = $this->Table_prifix."user_blacklist";
			$add_table = M($Table);
			
			unset($_POST['__hash__']);
			unset($_POST['Submit']);
			
			$count = $add_table->where("user_id=".$_POST['user_id'])->count('user_id');
			if ($count > 0){
				$this->error('USER_ID已存在');
				exit;
			}
						
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = '黑名单新增';
			$data['userip'] = get_client_ip();
			$data['cate'] = 131;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			//$result = $add_table->add($_POST);
			//dump($add_table->_sql()); exit;
			if($result){
				//增加操作记录
				$logs = C('BLACK_MSG_ADD_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->success('提交成功，等待审核', U($this->By_tpl.'/mingdan'));
				exit;
			}else{
				//增加操作记录
				$logs = C('BLACK_MSG_ADD_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('添加失败');
				exit;
			}
		}else{
			//增加操作记录
			$logs = C('BLACK_MSG_ADD');
			$remark = "";
			adminlog($logs, $remark);
		
			$this->assign('left_css',"50");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":mingdan_add";
			$this->display($lib_display);
		}
		
	}

	//黑名单更新
	public function mingdan_update(){
		$Table = $this->Table_prifix."user_blacklist";
		$upate_table = M($Table);
		if(!empty($_POST)){
			
			$GoodsID = $_POST['GoodsID'];
			//unset($_POST['GoodsID']);
			unset($_POST['Submit']);
			unset($_POST['__hash__']);
			
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = '黑名单修改';
			$data['userip'] = get_client_ip();
			$data['cate'] = 132;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			//$id['GoodsID']=intval($GoodsID);
			//$result=$upate_table->where($id)->save($_POST);
			if($result){
				//增加操作记录
				$logs = C('BLACK_MSG_EDIT_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
			
				$this->success('提交成功，等待审核',U($this->By_tpl.'/mingdan'));
			}else{
				//增加操作记录
				$logs = C('BLACK_MSG_EDIT_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('修改失败');
			}
		}else{
			//增加操作记录
			$logs = C('BLACK_MSG_EDIT');
			$remark = "";
			adminlog($logs, $remark);
			
			$id['user_id']=$_GET['user_id'];
			$info = $upate_table->where($id)->find();

			$this->assign('info',$info);
			$this->assign('left_css',"50");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":mingdan_update";
			$this->display($lib_display);
		}
	}
	
	//黑名单删除
	public function mingdan_delete(){
		if(empty($_GET)){ 
			$this->error('非法操作');
			exit;
		}else{
			$user_id = I("user_id");
			$Tablename = $this->Table_prifix."user_blacklist";
			$delete_table = M($Tablename);
			//$where['GoodsID']=$GoodsID;
			//$result=$delete_table->where($where)->delete();
			$arr = $delete_table->where("user_id=".$user_id)->find();
			$arr['tablename'] = $Tablename;
			$arr['act'] = "del";
			
			//增加修改记录
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($arr, JSON_UNESCAPED_UNICODE);
			$data['remark'] = "删除黑名单";
			$data['userip'] = get_client_ip();
			$data['cate'] = 133;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			if($result){
				//增加操作记录
				$logs = C('BLACK_MSG_DEL_SUCCESS');
				$remark = "(".$GoodsID.")";
				adminlog($logs, $remark);
				
				$this->success('提交成功，等待审核', U($this->By_tpl.'/mingdan'));
			}else{
				//增加操作记录
				$logs = C('BLACK_MSG_DEL_FALSE');
				$remark = "(".$GoodsID.")";
				adminlog($logs, $remark);
				
				$this->error('删除失败');
				exit;
			}
		}
	}
	//黑名单结束
	
	//待审核黑名单列表
	public function shenhe(){
	
		$sql0 = "";
		$Table = "user_record";
		$rowlist = M($Table);
		import('ORG.Util.Page');
		
		if ($_SESSION['js_flag']!="1"){$sql0 .= " and username='".$_SESSION['username']."'";}
		
		$count = $rowlist->where("cate in (131,132,133) $sql0")->count('id');
		$Page       = new Page($count,PAGE_SHOW);//实例化分页类传入总记录数和每页显示的记录数		
		$pageshow       = $Page->show();// 分页显示输出
		
		$list = $rowlist->where("cate in (131,132,133) $sql0")->order('flag,id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($rowlist->_sql());
		foreach($list as $key=>$value){
			$show = json_decode($value['logs'], true);
			
			if ($value['cate']=="131"){
				$list[$key]['showcate'] = "新增黑名单";
			}else if ($value['cate']=="132"){
				$list[$key]['showcate'] = "修改黑名单";
			}else if ($value['cate']=="133"){
				$list[$key]['showcate'] = "删除黑名单";
			}

			$list[$key]['user_id'] = $show['user_id'];
			$list[$key]['horn'] = ($show['horn']=="1") ? "禁止" : "正常";
			
			
			if ($value['flag']=="0"){
				$list[$key]['flagshow'] = "<font color='#FF0000'>待审核</font>";
			}else if ($value['flag']=="1"){
				$list[$key]['flagshow'] = "审核通过";
			}else if ($value['flag']=="2"){
				$list[$key]['flagshow'] = "审核取消";
			}
		}
		
		//增加操作记录
		$logs = C('WAITBLACK_MSG_RECORD');
		$remark = "";
		adminlog($logs, $remark);
		
		$this->assign('left_css',"50");
		$this->assign('list',$list);
		$this->assign('pageshow',$pageshow);
		
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl.":shenhe";
		$this->display($lib_display);
	}
	
	//待审核奖券列表
	public function shenhe_show(){
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
			$Table = $this->Table_prifix."user_blacklist";
			$add_table = M($Table);
			if ($act == "on"){
				if ($info['cate'] == "131"){
					//添加商品
					//$goods['addtime'] = time();
					$result = $add_table->add($goods);
					//dump($add_table->_sql());	
					//生成缓存
					$black_str = "";
					$xtlb = $add_table->where("horn=1")->select();
					foreach($xtlb as $key => $val){
						$black_str .= !empty($black_str) ? ",".$val['user_id'] : $val['user_id'];
					}
					$pubtext = array('msg' => $black_str,
									 'ts' => time());
					S("HEIMINGDAN", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
				}else if ($info['cate'] == "132"){
					//修改商品
					$result = $add_table->where("user_id=".$goods['user_id'])->save($goods);
					//dump($add_table->_sql());	
					//生成缓存
					$black_str = "";
					$xtlb = $add_table->where("horn=1")->select();
					foreach($xtlb as $key => $val){
						$black_str .= !empty($black_str) ? ",".$val['user_id'] : $val['user_id'];
					}
					$pubtext = array('msg' => $black_str,
									 'ts' => time());
					S("HEIMINGDAN", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
				}else if ($info['cate'] == "133"){
					//删除商品
					if (!empty($goods['user_id'])){
						$result = $add_table->where("user_id=".$goods['user_id'])->delete();
					}
					//dump($add_table->_sql());	
					//生成缓存
					$black_str = "";
					$xtlb = $add_table->where("horn=1")->select();
					foreach($xtlb as $key => $val){
						$black_str .= !empty($black_str) ? ",".$val['user_id'] : $val['user_id'];
					}
					$pubtext = array('msg' => $black_str,
									 'ts' => time());
					S("HEIMINGDAN", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
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
			$page = "shenhe_show";
		}
		
		//增加操作记录
		$logs = C('WAITSHANG_MSG_RECORD');
		$remark = "";
		adminlog($logs, $remark);
		
		$this->assign('left_css',"50");
		$this->assign('info',$info);
		$this->assign('goods',$goods);
		
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl.":".$page;
		$this->display($lib_display);
	}
	
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