<?php
// 奖券管理的文件

class LotteryAction extends BaseAction {
	protected $By_tpl = 'Lottery'; 
	protected $Table_prifix = MYTABLE_PRIFIX; 
	//奖券开始
	//奖券列表
	public function goods(){
		
		
		$Table = $this->Table_prifix."profile_mall_lottery";
		$rowlist = M($Table);
		import('ORG.Util.Page');
		$count = $rowlist->where('type<4')->count('id');
		$Page       = new Page($count,PAGE_SHOW);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		
		$list = $rowlist->where('type<4')->order('id')->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($rowlist->_sql());
		foreach($list as $key=>$value){
			$list[$key]['status'] = ($value['status']=="1") ? "正常" : "失效";
			$list[$key]['addtime'] = date("Y-m-d H:i:s", $value['addtime']);
			$list[$key]['pic'] = !empty($value['pic']) ? '<img src="'.DB_HOST.$value['pic'].'" width="50">' : "";
			if ($value['type']=="1"){
				$list[$key]['typename'] = "虚拟物品";
			}else if ($value['type']=="2"){
				$list[$key]['typename'] = "钻石";
			}else if ($value['type']=="3"){
				$list[$key]['typename'] = "实物";
			}
		}
		
		//增加操作记录
		$logs = C('Lottery_MSG_RECORD');
		$remark = "";
		adminlog($logs, $remark);
		
		$this->assign('left_css',"46");
		$this->assign('list',$list);
		$this->assign('pageshow',$show);
		
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl.":goods";
		$this->display($lib_display);
	}

	
	//奖券添加
	public function goods_add(){
		if(!empty($_POST)){
			$Table = $this->Table_prifix."profile_mall_lottery";
			$add_table = M($Table);
			
			unset($_POST['__hash__']);
			unset($_POST['Submit']);
			
			import("ORG.Net.UploadFile");
			//导入上传类
			$upload = new UploadFile();
			 //设置上传文件大小
			$upload->maxSize = 3292200;
			 //设置上传文件类型
			$upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
			 //设置附件上传目录
			$upload->savePath = './Public/Uploads/';
			 //设置需要生成缩略图，仅对图像文件有效
			//$upload->thumb = true;
			 // 设置引用图片类库包路径
			$upload->imageClassPath = 'ORG.Util.Image';
			 //设置需要生成缩略图的文件后缀
			//$upload->thumbPrefix = 'm_,s_';  //生产2张缩略图
			 //设置缩略图最大宽度
			//$upload->thumbMaxWidth = '400,100';
			 //设置缩略图最大高度
			//$upload->thumbMaxHeight = '400,100';
			 //设置上传文件规则
			$upload->saveRule = 'uniqid';
			 //删除原图
			$upload->thumbRemoveOrigin = true;
			if (!$upload->upload()) {
				//捕获上传异常
				$_POST['pic'] = $this->error($upload->getErrorMsg());
			} else {
				//取得成功上传的文件信息
				$uploadList = $upload->getUploadFileInfo();
				//import("@.ORG.Image");
				//给m_缩略图添加水印, Image::water('原文件名','水印图片地址')
				//Image::water($uploadList[0]['savepath'] . 'm_' . $uploadList[0]['savename'], APP_PATH.'Tpl/Public/Images/logo.png');
				$_POST['pic'] = "/Public/Uploads/".$uploadList[0]['savename'];
			}
			
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = '奖券新增';
			$data['userip'] = get_client_ip();
			$data['cate'] = 121;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			//$result = $add_table->add($_POST);
			//dump($add_table->_sql()); exit;
			if($result){
				//增加操作记录
				$logs = C('Lottery_MSG_ADD_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->success('提交成功，等待审核', U($this->By_tpl.'/goods'));
				exit;
			}else{
				//增加操作记录
				$logs = C('Lottery_MSG_ADD_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('添加失败');
				exit;
			}
		}else{
			//增加操作记录
			$logs = C('Lottery_MSG_ADD');
			$remark = "";
			adminlog($logs, $remark);
		
			$this->assign('left_css',"46");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":goods_add";
			$this->display($lib_display);
		}
		
	}

	//奖券更新
	public function goods_update(){
		$Table = $this->Table_prifix."profile_mall_lottery";
		$upate_table = M($Table);
		if(!empty($_POST)){
			
			$GoodsID = $_POST['GoodsID'];
			//unset($_POST['GoodsID']);
			unset($_POST['Submit']);
			unset($_POST['__hash__']);

			import("ORG.Net.UploadFile");
			//echo $_FILES['photo']['name']; exit;
			if (!empty($_FILES['photo']['name'])){
				
				//导入上传类
				$upload = new UploadFile();
				 //设置上传文件大小
				$upload->maxSize = 3292200;
				 //设置上传文件类型
				$upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
				 //设置附件上传目录
				$upload->savePath = './Public/Uploads/';
				 //设置需要生成缩略图，仅对图像文件有效
				//$upload->thumb = true;
				 // 设置引用图片类库包路径
				$upload->imageClassPath = 'ORG.Util.Image';
				 //设置需要生成缩略图的文件后缀
				//$upload->thumbPrefix = 'm_,s_';  //生产2张缩略图
				 //设置缩略图最大宽度
				//$upload->thumbMaxWidth = '400,100';
				 //设置缩略图最大高度
				//$upload->thumbMaxHeight = '400,100';
				 //设置上传文件规则
				$upload->saveRule = 'uniqid';
				 //删除原图
				$upload->thumbRemoveOrigin = true;
				if (!$upload->upload()) {
					//捕获上传异常
					$_POST['pic'] = $this->error($upload->getErrorMsg());
				} else {
					//取得成功上传的文件信息
					$uploadList = $upload->getUploadFileInfo();
					//import("@.ORG.Image");
					//给m_缩略图添加水印, Image::water('原文件名','水印图片地址')
					//Image::water($uploadList[0]['savepath'] . 'm_' . $uploadList[0]['savename'], APP_PATH.'Tpl/Public/Images/logo.png');
					$_POST['pic'] = "/Public/Uploads/".$uploadList[0]['savename'];
				}
			}
			unset($_POST['photo']);
			
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = '奖券修改';
			$data['userip'] = get_client_ip();
			$data['cate'] = 122;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			//$id['GoodsID']=intval($GoodsID);
			//$result=$upate_table->where($id)->save($_POST);
			if($result){
				//增加操作记录
				$logs = C('Lottery_MSG_EDIT_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				//通知服务器
				//$url = DB_HOST."/Pay/shang.php";
				//$jinbi_result = curlGET($url);
				
				$this->success('提交成功，等待审核',U($this->By_tpl.'/goods'));
			}else{
				//增加操作记录
				$logs = C('Lottery_MSG_EDIT_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('修改失败');
			}
		}else{
			//增加操作记录
			$logs = C('Lottery_MSG_EDIT');
			$remark = "";
			adminlog($logs, $remark);
			
			$id['id']=$_GET['id'];
			$info = $upate_table->where($id)->find();
			$info['pic'] = !empty($info['pic']) ? '<br><img src="'.DB_HOST.$info['pic'].'" width="50">' : "";
			$this->assign('info',$info);
			$this->assign('left_css',"46");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":goods_update";
			$this->display($lib_display);
		}
	}
	
	//奖券删除
	public function goods_delete(){
		if(empty($_GET)){ 
			$this->error('非法操作');
			exit;
		}else{
			$id = I("id");
			$Tablename = $this->Table_prifix."profile_mall_lottery";
			$delete_table = M($Tablename);
			//$where['GoodsID']=$GoodsID;
			//$result=$delete_table->where($where)->delete();
			$arr = $delete_table->where("id=".$id)->find();
			$arr['tablename'] = $Tablename;
			$arr['act'] = "del";
			
			//增加修改记录
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($arr, JSON_UNESCAPED_UNICODE);
			$data['remark'] = "删除奖券";
			$data['userip'] = get_client_ip();
			$data['cate'] = 123;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			if($result){
				//增加操作记录
				$logs = C('Lottery_MSG_DEL_SUCCESS');
				$remark = "(".$GoodsID.")";
				adminlog($logs, $remark);
				
				//通知服务器
				//$url = DB_HOST."/Pay/shang.php";
				//$jinbi_result = curlGET($url);
				
				$this->success('提交成功，等待审核', U($this->By_tpl.'/goods'));
			}else{
				//增加操作记录
				$logs = C('Lottery_MSG_DEL_FALSE');
				$remark = "(".$GoodsID.")";
				adminlog($logs, $remark);
				
				$this->error('删除失败');
				exit;
			}
		}
	}
	//奖券结束
	
	//SVIP开始
	//SVIP列表
	public function svip(){
		
		
		$Table = $this->Table_prifix."profile_mall_lottery";
		$rowlist = M($Table);
		import('ORG.Util.Page');
		$count = $rowlist->where('type=4')->count('id');
		$Page       = new Page($count,PAGE_SHOW);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		
		$list = $rowlist->where('type=4')->order('id')->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($rowlist->_sql());
		foreach($list as $key=>$value){
			$list[$key]['status'] = ($value['status']=="1") ? "正常" : "失效";
			$list[$key]['addtime'] = date("Y-m-d H:i:s", $value['addtime']);
			$list[$key]['pic'] = !empty($value['pic']) ? '<img src="'.DB_HOST.$value['pic'].'" width="50">' : "";
			if ($value['type']=="1"){
				$list[$key]['typename'] = "虚拟物品";
			}else if ($value['type']=="2"){
				$list[$key]['typename'] = "钻石";
			}else if ($value['type']=="3"){
				$list[$key]['typename'] = "实物";
			}else if ($value['type']=="4"){
				$list[$key]['typename'] = "SVIP";
			}
		}
		
		//增加操作记录
		$logs = C('Lottery_MSG_RECORD');
		$remark = "";
		adminlog($logs, $remark);
		
		$this->assign('left_css',"46");
		$this->assign('list',$list);
		$this->assign('pageshow',$show);
		
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl.":svip";
		$this->display($lib_display);
	}

	
	//SVIP添加
	public function svip_add(){
		if(!empty($_POST)){
			$Table = $this->Table_prifix."profile_mall_lottery";
			$add_table = M($Table);
			
			unset($_POST['__hash__']);
			unset($_POST['Submit']);
			
			import("ORG.Net.UploadFile");
			//导入上传类
			$upload = new UploadFile();
			 //设置上传文件大小
			$upload->maxSize = 3292200;
			 //设置上传文件类型
			$upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
			 //设置附件上传目录
			$upload->savePath = './Public/Uploads/';
			 //设置需要生成缩略图，仅对图像文件有效
			//$upload->thumb = true;
			 // 设置引用图片类库包路径
			$upload->imageClassPath = 'ORG.Util.Image';
			 //设置需要生成缩略图的文件后缀
			//$upload->thumbPrefix = 'm_,s_';  //生产2张缩略图
			 //设置缩略图最大宽度
			//$upload->thumbMaxWidth = '400,100';
			 //设置缩略图最大高度
			//$upload->thumbMaxHeight = '400,100';
			 //设置上传文件规则
			$upload->saveRule = 'uniqid';
			 //删除原图
			$upload->thumbRemoveOrigin = true;
			if (!$upload->upload()) {
				//捕获上传异常
				$_POST['pic'] = $this->error($upload->getErrorMsg());
			} else {
				//取得成功上传的文件信息
				$uploadList = $upload->getUploadFileInfo();
				//import("@.ORG.Image");
				//给m_缩略图添加水印, Image::water('原文件名','水印图片地址')
				//Image::water($uploadList[0]['savepath'] . 'm_' . $uploadList[0]['savename'], APP_PATH.'Tpl/Public/Images/logo.png');
				$_POST['pic'] = "/Public/Uploads/".$uploadList[0]['savename'];
			}
			
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = 'SVIP新增';
			$data['userip'] = get_client_ip();
			$data['cate'] = 124;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			//$result = $add_table->add($_POST);
			//dump($add_table->_sql()); exit;
			if($result){
				//增加操作记录
				$logs = C('Lottery_MSG_ADD_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->success('提交成功，等待审核', U($this->By_tpl.'/svip'));
				exit;
			}else{
				//增加操作记录
				$logs = C('Lottery_MSG_ADD_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('添加失败');
				exit;
			}
		}else{
			//增加操作记录
			$logs = C('Lottery_MSG_ADD');
			$remark = "";
			adminlog($logs, $remark);
		
			$this->assign('left_css',"46");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":svip_add";
			$this->display($lib_display);
		}
		
	}

	//SVIP更新
	public function svip_update(){
		$Table = $this->Table_prifix."profile_mall_lottery";
		$upate_table = M($Table);
		if(!empty($_POST)){
			
			$GoodsID = $_POST['GoodsID'];
			//unset($_POST['GoodsID']);
			unset($_POST['Submit']);
			unset($_POST['__hash__']);

			import("ORG.Net.UploadFile");
			//echo $_FILES['photo']['name']; exit;
			if (!empty($_FILES['photo']['name'])){
				
				//导入上传类
				$upload = new UploadFile();
				 //设置上传文件大小
				$upload->maxSize = 3292200;
				 //设置上传文件类型
				$upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
				 //设置附件上传目录
				$upload->savePath = './Public/Uploads/';
				 //设置需要生成缩略图，仅对图像文件有效
				//$upload->thumb = true;
				 // 设置引用图片类库包路径
				$upload->imageClassPath = 'ORG.Util.Image';
				 //设置需要生成缩略图的文件后缀
				//$upload->thumbPrefix = 'm_,s_';  //生产2张缩略图
				 //设置缩略图最大宽度
				//$upload->thumbMaxWidth = '400,100';
				 //设置缩略图最大高度
				//$upload->thumbMaxHeight = '400,100';
				 //设置上传文件规则
				$upload->saveRule = 'uniqid';
				 //删除原图
				$upload->thumbRemoveOrigin = true;
				if (!$upload->upload()) {
					//捕获上传异常
					$_POST['pic'] = $this->error($upload->getErrorMsg());
				} else {
					//取得成功上传的文件信息
					$uploadList = $upload->getUploadFileInfo();
					//import("@.ORG.Image");
					//给m_缩略图添加水印, Image::water('原文件名','水印图片地址')
					//Image::water($uploadList[0]['savepath'] . 'm_' . $uploadList[0]['savename'], APP_PATH.'Tpl/Public/Images/logo.png');
					$_POST['pic'] = "/Public/Uploads/".$uploadList[0]['savename'];
				}
			}
			unset($_POST['photo']);
			
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($_POST, JSON_UNESCAPED_UNICODE);
			$data['remark'] = 'SVIP修改';
			$data['userip'] = get_client_ip();
			$data['cate'] = 125;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			//$id['GoodsID']=intval($GoodsID);
			//$result=$upate_table->where($id)->save($_POST);
			if($result){
				//增加操作记录
				$logs = C('Lottery_MSG_EDIT_SUCCESS');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				//通知服务器
				//$url = DB_HOST."/Pay/shang.php";
				//$jinbi_result = curlGET($url);
				
				$this->success('提交成功，等待审核',U($this->By_tpl.'/svip'));
			}else{
				//增加操作记录
				$logs = C('Lottery_MSG_EDIT_FALSE');
				$remark = "(".json_encode($_POST).")";
				adminlog($logs, $remark);
				
				$this->error('修改失败');
			}
		}else{
			//增加操作记录
			$logs = C('Lottery_MSG_EDIT');
			$remark = "";
			adminlog($logs, $remark);
			
			$id['id']=$_GET['id'];
			$info = $upate_table->where($id)->find();
			$info['pic'] = !empty($info['pic']) ? '<br><img src="'.DB_HOST.$info['pic'].'" width="50">' : "";
			$this->assign('info',$info);
			$this->assign('left_css',"46");
			$this->assign('By_tpl',$this->By_tpl);
			$lib_display = $this->By_tpl.":svip_update";
			$this->display($lib_display);
		}
	}
	
	//SVIP删除
	public function svip_delete(){
		if(empty($_GET)){ 
			$this->error('非法操作');
			exit;
		}else{
			$id = I("id");
			$Tablename = $this->Table_prifix."profile_mall_lottery";
			$delete_table = M($Tablename);
			//$where['GoodsID']=$GoodsID;
			//$result=$delete_table->where($where)->delete();
			$arr = $delete_table->where("id=".$id)->find();
			$arr['tablename'] = $Tablename;
			$arr['act'] = "del";
			
			//增加修改记录
			$table_name = M('user_record');
			$data = array();
			$data['logs'] = json_encode($arr, JSON_UNESCAPED_UNICODE);
			$data['remark'] = "删除SVIP";
			$data['userip'] = get_client_ip();
			$data['cate'] = 126;
			$data['username'] = $_SESSION['username'];
			$data['addtime'] = time();
			$result = $table_name->add($data);
			
			if($result){
				//增加操作记录
				$logs = C('Lottery_MSG_DEL_SUCCESS');
				$remark = "(".$GoodsID.")";
				adminlog($logs, $remark);
				
				//通知服务器
				//$url = DB_HOST."/Pay/shang.php";
				//$jinbi_result = curlGET($url);
				
				$this->success('提交成功，等待审核', U($this->By_tpl.'/svip'));
			}else{
				//增加操作记录
				$logs = C('Lottery_MSG_DEL_FALSE');
				$remark = "(".$GoodsID.")";
				adminlog($logs, $remark);
				
				$this->error('删除失败');
				exit;
			}
		}
	}
	//SVIP结束
	
	//待审核奖券列表
	public function shenhe(){
	
		$sql0 = "";
		$Table = "user_record";
		$rowlist = M($Table);
		import('ORG.Util.Page');
		
		if ($_SESSION['js_flag']!="1"){$sql0 .= " and username='".$_SESSION['username']."'";}
		
		$count = $rowlist->where("cate in (121,122,123,124,125,126) $sql0")->count('id');
		$Page       = new Page($count,PAGE_SHOW);//实例化分页类传入总记录数和每页显示的记录数		
		$pageshow       = $Page->show();// 分页显示输出
		
		$list = $rowlist->where("cate in (121,122,123,124,125,126) $sql0")->order('flag,id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($rowlist->_sql());
		foreach($list as $key=>$value){
			$show = json_decode($value['logs'], true);
			
			//$list[$key]['cate'] = ($value['cate']=="101") ? "新增商品" : "修改商品";
			if ($value['cate']=="121"){
				$list[$key]['showcate'] = "新增奖券";
			}else if ($value['cate']=="122"){
				$list[$key]['showcate'] = "修改奖券";
			}else if ($value['cate']=="123"){
				$list[$key]['showcate'] = "删除奖券";
			}else if ($value['cate']=="124"){
				$list[$key]['showcate'] = "新增SVIP";
			}else if ($value['cate']=="125"){
				$list[$key]['showcate'] = "修改SVIP";
			}else if ($value['cate']=="126"){
				$list[$key]['showcate'] = "删除SVIP";
			}

			$list[$key]['goodsid'] = $show['id'];
			$list[$key]['status'] = ($show['status']=="1") ? "正常" : "失效";
			if ($show['type']=="1"){
				$list[$key]['typename'] = "虚拟物品";
			}else if ($show['type']=="2"){
				$list[$key]['typename'] = "钻石";
			}else if ($show['type']=="3"){
				$list[$key]['typename'] = "实物";
			}else if ($show['type']=="4"){
				$list[$key]['typename'] = "SVIP";
			}
			$list[$key]['names'] = $show['names'];
			$list[$key]['nums'] = $show['nums'];
			$list[$key]['gold'] = $show['gold'];
			$list[$key]['pic'] = !empty($show['pic']) ? '<img src="'.DB_HOST.$show['pic'].'" width="50">' : "";
			$list[$key]['sorts'] = $show['sorts'];
			$list[$key]['meno'] = $show['meno'];
			
			if ($value['flag']=="0"){
				$list[$key]['flagshow'] = "<font color='#FF0000'>待审核</font>";
			}else if ($value['flag']=="1"){
				$list[$key]['flagshow'] = "审核通过";
			}else if ($value['flag']=="2"){
				$list[$key]['flagshow'] = "审核取消";
			}
		}
		
		//增加操作记录
		$logs = C('WAITLottery_MSG_RECORD');
		$remark = "";
		adminlog($logs, $remark);
		
		$this->assign('left_css',"46");
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
			$Table = $this->Table_prifix."profile_mall_lottery";
			$add_table = M($Table);
			if ($act == "on"){
				if ($info['cate'] == "121"){
					//添加商品
					$goods['addtime'] = time();
					$result = $add_table->add($goods);
					//dump($add_table->_sql());	
					//生成缓存
					$xtlb = $add_table->field("id,type,names,nums,pic")->where("status=1 and type<4")->order("sorts,id")->select();
					foreach($xtlb as $key => $val){
						$xtlb[$key]['pic'] = !empty($val['pic']) ? DB_HOST.$val['pic'] : "";
						$xtlb[$key]['id'] = (int)$val['id'];
						$xtlb[$key]['type'] = (int)$val['type'];
						$xtlb[$key]['nums'] = (int)$val['nums'];
					}
					$pubtext = array('msg' => $xtlb,
									 'ts' => time());
					S("YHJQ", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
				}else if ($info['cate'] == "122"){
					//修改商品
					$result = $add_table->where("id=".$goods['id'])->save($goods);
					//dump($add_table->_sql());	
					//生成缓存
					$xtlb = $add_table->field("id,type,names,nums,pic")->where("status=1 and type<4")->order("sorts,id")->select();
					foreach($xtlb as $key => $val){
						$xtlb[$key]['pic'] = !empty($val['pic']) ? DB_HOST.$val['pic'] : "";
						$xtlb[$key]['id'] = (int)$val['id'];
						$xtlb[$key]['type'] = (int)$val['type'];
						$xtlb[$key]['nums'] = (int)$val['nums'];
					}
					$pubtext = array('msg' => $xtlb,
									 'ts' => time());
					S("YHJQ", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
				}else if ($info['cate'] == "123"){
					//删除商品
					if (!empty($goods['id'])){
						$result = $add_table->where("id=".$goods['id'])->delete();
					}
					//dump($add_table->_sql());	
					//生成缓存
					$xtlb = $add_table->field("id,type,names,nums,pic")->where("status=1 and type<4")->order("sorts,id")->select();
					foreach($xtlb as $key => $val){
						$xtlb[$key]['pic'] = !empty($val['pic']) ? DB_HOST.$val['pic'] : "";
						$xtlb[$key]['id'] = (int)$val['id'];
						$xtlb[$key]['type'] = (int)$val['type'];
						$xtlb[$key]['nums'] = (int)$val['nums'];
					}
					$pubtext = array('msg' => $xtlb,
									 'ts' => time());
					S("YHJQ", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
				}else if ($info['cate'] == "124"){
					//添加商品
					$goods['addtime'] = time();
					$result = $add_table->add($goods);
					//dump($add_table->_sql());	
					//生成缓存
					$xtlb = $add_table->where("status=1 and type=4")->order("sorts,id")->select();
					foreach($xtlb as $key => $val){
						$xtlb[$key]['pic'] = !empty($val['pic']) ? DB_HOST.$val['pic'] : "";
						$xtlb[$key]['id'] = (int)$val['id'];
						$xtlb[$key]['type'] = (int)$val['type'];
						$xtlb[$key]['nums'] = (int)$val['nums'];
						$xtlb[$key]['pay'] = (int)$val['pay'];
						$xtlb[$key]['gold'] = (int)$val['gold'];
						$xtlb[$key]['meno'] = $val['meno'];
					}
					$pubtext = array('msg' => $xtlb,
									 'ts' => time());
					S("YHJQ_SVIP", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
				}else if ($info['cate'] == "125"){
					//修改商品
					$result = $add_table->where("id=".$goods['id'])->save($goods);
					//dump($add_table->_sql());	
					//生成缓存
					$xtlb = $add_table->where("status=1 and type=4")->order("sorts,id")->select();
					foreach($xtlb as $key => $val){
						$xtlb[$key]['pic'] = !empty($val['pic']) ? DB_HOST.$val['pic'] : "";
						$xtlb[$key]['id'] = (int)$val['id'];
						$xtlb[$key]['type'] = (int)$val['type'];
						$xtlb[$key]['nums'] = (int)$val['nums'];
						$xtlb[$key]['pay'] = (int)$val['pay'];
						$xtlb[$key]['gold'] = (int)$val['gold'];
						$xtlb[$key]['meno'] = $val['meno'];
					}
					$pubtext = array('msg' => $xtlb,
									 'ts' => time());
					S("YHJQ_SVIP", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
				}else if ($info['cate'] == "126"){
					//删除商品
					if (!empty($goods['id'])){
						$result = $add_table->where("id=".$goods['id'])->delete();
					}
					//dump($add_table->_sql());	
					//生成缓存
					$xtlb = $add_table->where("status=1 and type=4")->order("sorts,id")->select();
					foreach($xtlb as $key => $val){
						$xtlb[$key]['pic'] = !empty($val['pic']) ? DB_HOST.$val['pic'] : "";
						$xtlb[$key]['id'] = (int)$val['id'];
						$xtlb[$key]['type'] = (int)$val['type'];
						$xtlb[$key]['nums'] = (int)$val['nums'];
						$xtlb[$key]['pay'] = (int)$val['pay'];
						$xtlb[$key]['gold'] = (int)$val['gold'];
						$xtlb[$key]['meno'] = $val['meno'];
					}
					$pubtext = array('msg' => $xtlb,
									 'ts' => time());
					S("YHJQ_SVIP", json_encode($pubtext, JSON_UNESCAPED_UNICODE));
				}
				if($result){
					//生成静态文件
					$url = DB_HOST.U('Gameconfig/writetxt');
					$jingtxt = curlGET($url);
					
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
		
		$this->assign('left_css',"46");
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