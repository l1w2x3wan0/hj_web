<?php
// 员工管理的文件
/*
		员工状态：0 禁用状态
				  1 启用状态
				  2 删除状态
				  3 归档状态
*/
class PicAction extends Action {
  
	
	//添加图片
	public function user_pic(){
		
		$logs_file = APP_PATH."Logs/pic_".time().".txt";
		file_put_contents($logs_file, json_encode($_POST));
		//exit;
		if(!empty($_POST)){
			
			import('ORG.Net.UploadFile');
			$upload = new UploadFile();// 实例化上传类
			$upload->maxSize  = 3145728 ;// 设置附件上传大小
			$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			$upload->savePath =  APP_PATH.'laoxie_photo/';// 设置附件上传目录
			 if(!$upload->upload()) {// 上传错误提示错误信息
			$this->error($upload->getErrorMsg());
			 }else{// 上传成功 获取上传文件信息
			$info =  $upload->getUploadFileInfo();
			 }
			 // 保存表单数据 包括附件数据
			$User = M("xie_pic"); // 实例化User对象
			foreach($info as $val){
				$User->create(); // 创建数据对象
				$User->pic_src = DB_HOST."/".APP_NAME."/laoxie_photo/".$val['savename']; // 保存上传的照片根据需要自行组装
				$User->addtime = time();
				$User->add(); // 写入用户数据到数据库
			}
			
			$this->success('数据保存成功！');
			
		}else{

			$this->assign('left_css',"2");
			$this->display('User:photo_add');
		}
		
	}

	//获取图片
	public function piclist(){
		$pic = M("xie_pic");
		$list = $pic->order('id')->select();
		echo json_encode($list);
	}


}