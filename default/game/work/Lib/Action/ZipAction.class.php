<?php
// 压缩管理的文件

class ZipAction extends Action {
	
	public function filetozip(){
		import('ORG.Util.FileToZip');//引入zip下载类文件FileToZip
		// 打包下载
		$cur_file = "/html2";
		$save_path = "/1.zip";
		
		$handler = opendir($cur_file); //$cur_file 文件所在目录
		$download_file = array();
		$i = 0;
		while( ($filename = readdir($handler)) !== false ) {
		 if($filename != '.' && $filename != '..') {
		 $download_file[$i++] = $filename;
		 }
		}
		closedir($handler);
		$scandir=new traverseDir($cur_file,$save_path); //$save_path zip包文件目录
		$scandir->tozip($download_file);

		
	}

}