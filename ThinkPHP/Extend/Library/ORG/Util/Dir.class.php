<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

/**
 +------------------------------------------------------------------------------
 * DirectoryIteratorʵ���� PHP5����������DirectoryIterator��
 +------------------------------------------------------------------------------
 * @category   ORG
 * @package  ORG
 * @subpackage  Io
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class Dir extends Think implements IteratorAggregate
{//�ඨ�忪ʼ

	private $_values = array();
	/**
	 +----------------------------------------------------------
	 * �ܹ�����
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param string $path  Ŀ¼·��
	 +----------------------------------------------------------
	 */
	function __construct($path,$pattern='*')
	{
		if(substr($path, -1) != "/")    $path .= "/";
		$this->listFile($path,$pattern);
	}

	/**
	 +----------------------------------------------------------
	 * ȡ��Ŀ¼������ļ���Ϣ
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param mixed $pathname ·��
	 +----------------------------------------------------------
	 */
	function listFile($pathname,$pattern='*')
	{
		static $_listDirs = array();
		$guid	=	md5($pathname.$pattern);
		if(!isset($_listDirs[$guid])){
			$dir = array();
			$list	=	glob($pathname.$pattern);
			foreach ($list as $i=>$file){
					$dir[$i]['filename']    = basename($file);
					$dir[$i]['pathname']    = realpath($file);
					$dir[$i]['owner']        = fileowner($file);
					$dir[$i]['perms']        = fileperms($file);
					$dir[$i]['inode']        = fileinode($file);
					$dir[$i]['group']        = filegroup($file);
					$dir[$i]['path']        = dirname($file);
					$dir[$i]['atime']        = fileatime($file);
					$dir[$i]['ctime']        = filectime($file);
					$dir[$i]['size']        = filesize($file);
					$dir[$i]['type']        = filetype($file);
					$dir[$i]['ext']      =  is_file($file)?strtolower(substr(strrchr(basename($file), '.'),1)):'';
					$dir[$i]['mtime']        = filemtime($file);
					$dir[$i]['isDir']        = is_dir($file);
					$dir[$i]['isFile']        = is_file($file);
					$dir[$i]['isLink']        = is_link($file);
					//$dir[$i]['isExecutable']= function_exists('is_executable')?is_executable($file):'';
					$dir[$i]['isReadable']    = is_readable($file);
					$dir[$i]['isWritable']    = is_writable($file);
			}
			$cmp_func = create_function('$a,$b','
			$k  =  "isDir";
			if($a[$k]  ==  $b[$k])  return  0;
			return  $a[$k]>$b[$k]?-1:1;
			');
			// �Խ������ ��֤Ŀ¼��ǰ��
			usort($dir,$cmp_func);
			$this->_values = $dir;
			$_listDirs[$guid] = $dir;
		}else{
			$this->_values = $_listDirs[$guid];
		}
	}

	/**
	 +----------------------------------------------------------
	 * �ļ��ϴη���ʱ��
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return integer
	 +----------------------------------------------------------
	 */
	function getATime()
	{
		$current = $this->current($this->_values);
		return $current['atime'];
	}

	/**
	 +----------------------------------------------------------
	 * ȡ���ļ��� inode �޸�ʱ��
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return integer
	 +----------------------------------------------------------
	 */
	function getCTime()
	{
		$current = $this->current($this->_values);
		return $current['ctime'];
	}

	/**
	 +----------------------------------------------------------
	 * ������Ŀ¼�ļ���Ϣ
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return DirectoryIterator
	 +----------------------------------------------------------
	 */
	function getChildren()
	{
		$current = $this->current($this->_values);
		if($current['isDir']){
			return new Dir($current['pathname']);
		}
		return false;
	}

	/**
	 +----------------------------------------------------------
	 * ȡ���ļ���
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 */
	function getFilename()
	{
		$current = $this->current($this->_values);
		return $current['filename'];
	}

	/**
	 +----------------------------------------------------------
	 * ȡ���ļ�����
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return integer
	 +----------------------------------------------------------
	 */
	function getGroup()
	{
		$current = $this->current($this->_values);
		return $current['group'];
	}

	/**
	 +----------------------------------------------------------
	 * ȡ���ļ��� inode
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return integer
	 +----------------------------------------------------------
	 */
	function getInode()
	{
		$current = $this->current($this->_values);
		return $current['inode'];
	}

	/**
	 +----------------------------------------------------------
	 * ȡ���ļ����ϴ��޸�ʱ��
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return integer
	 +----------------------------------------------------------
	 */
	function getMTime()
	{
		$current = $this->current($this->_values);
		return $current['mtime'];
	}

	/**
	 +----------------------------------------------------------
	 * ȡ���ļ���������
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 */
	function getOwner()
	{
		$current = $this->current($this->_values);
		return $current['owner'];
	}

	/**
	 +----------------------------------------------------------
	 * ȡ���ļ�·�����������ļ���
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 */
	function getPath()
	{
		$current = $this->current($this->_values);
		return $current['path'];
	}

	/**
	 +----------------------------------------------------------
	 * ȡ���ļ�������·���������ļ���
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 */
	function getPathname()
	{
		$current = $this->current($this->_values);
		return $current['pathname'];
	}

	/**
	 +----------------------------------------------------------
	 * ȡ���ļ���Ȩ��
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return integer
	 +----------------------------------------------------------
	 */
	function getPerms()
	{
		$current = $this->current($this->_values);
		return $current['perms'];
	}

	/**
	 +----------------------------------------------------------
	 * ȡ���ļ��Ĵ�С
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return integer
	 +----------------------------------------------------------
	 */
	function getSize()
	{
		$current = $this->current($this->_values);
		return $current['size'];
	}

	/**
	 +----------------------------------------------------------
	 * ȡ���ļ�����
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 */
	function getType()
	{
		$current = $this->current($this->_values);
		return $current['type'];
	}

	/**
	 +----------------------------------------------------------
	 * �Ƿ�ΪĿ¼
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return boolen
	 +----------------------------------------------------------
	 */
	function isDir()
	{
		$current = $this->current($this->_values);
		return $current['isDir'];
	}

	/**
	 +----------------------------------------------------------
	 * �Ƿ�Ϊ�ļ�
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return boolen
	 +----------------------------------------------------------
	 */
	function isFile()
	{
		$current = $this->current($this->_values);
		return $current['isFile'];
	}

	/**
	 +----------------------------------------------------------
	 * �ļ��Ƿ�Ϊһ����������
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return boolen
	 +----------------------------------------------------------
	 */
	function isLink()
	{
		$current = $this->current($this->_values);
		return $current['isLink'];
	}


	/**
	 +----------------------------------------------------------
	 * �ļ��Ƿ����ִ��
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return boolen
	 +----------------------------------------------------------
	 */
	function isExecutable()
	{
		$current = $this->current($this->_values);
		return $current['isExecutable'];
	}


	/**
	 +----------------------------------------------------------
	 * �ļ��Ƿ�ɶ�
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return boolen
	 +----------------------------------------------------------
	 */
	function isReadable()
	{
		$current = $this->current($this->_values);
		return $current['isReadable'];
	}

	/**
	 +----------------------------------------------------------
	 * ��ȡforeach�ı�����ʽ
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 */
	function getIterator()
	{
		 return new ArrayObject($this->_values);
	}

	// ����Ŀ¼��������Ϣ
	function toArray() {
		return $this->_values;
	}

	// ��̬����

	/**
	 +----------------------------------------------------------
	 * �ж�Ŀ¼�Ƿ�Ϊ��
	 +----------------------------------------------------------
	 * @access static
	 +----------------------------------------------------------
	 * @return void
	 +----------------------------------------------------------
	 */
	function isEmpty($directory)
	{
		$handle = opendir($directory);
		while (($file = readdir($handle)) !== false)
		{
			if ($file != "." && $file != "..")
			{
				closedir($handle);
				return false;
			}
		}
		closedir($handle);
		return true;
	}

	/**
	 +----------------------------------------------------------
	 * ȡ��Ŀ¼�еĽṹ��Ϣ
	 +----------------------------------------------------------
	 * @access static
	 +----------------------------------------------------------
	 * @return void
	 +----------------------------------------------------------
	 */
	function getList($directory)
	{
		return scandir($directory);
	}

	/**
	 +----------------------------------------------------------
	 * ɾ��Ŀ¼������������ļ���
	 +----------------------------------------------------------
	 * @access static
	 +----------------------------------------------------------
	 * @return void
	 +----------------------------------------------------------
	 */
	function delDir($directory,$subdir=true)
	{
		if (is_dir($directory) == false)
		{
			exit("The Directory Is Not Exist!");
		}
		$handle = opendir($directory);
		while (($file = readdir($handle)) !== false)
		{
			if ($file != "." && $file != "..")
			{
			is_dir("$directory/$file")?
				Dir::delDir("$directory/$file"):
				unlink("$directory/$file");
			}
		}
		if (readdir($handle) == false)
		{
			closedir($handle);
			rmdir($directory);
		}
	}

	/**
	 +----------------------------------------------------------
	 * ɾ��Ŀ¼����������ļ�������ɾ��Ŀ¼
	 +----------------------------------------------------------
	 * @access static
	 +----------------------------------------------------------
	 * @return void
	 +----------------------------------------------------------
	 */
	function del($directory)
	{
		if (is_dir($directory) == false)
		{
			exit("The Directory Is Not Exist!");
		}
		$handle = opendir($directory);
		while (($file = readdir($handle)) !== false)
		{
			if ($file != "." && $file != ".." && is_file("$directory/$file"))
			{
				unlink("$directory/$file");
			}
		}
		closedir($handle);
	}

	/**
	 +----------------------------------------------------------
	 * ����Ŀ¼
	 +----------------------------------------------------------
	 * @access static
	 +----------------------------------------------------------
	 * @return void
	 +----------------------------------------------------------
	 */
	function copyDir($source, $destination)
	{
		if (is_dir($source) == false)
		{
			exit("The Source Directory Is Not Exist!");
		}
		if (is_dir($destination) == false)
		{
			mkdir($destination, 0700);
		}
		$handle=opendir($source);
		while (false !== ($file = readdir($handle)))
		{
			if ($file != "." && $file != "..")
			{
				is_dir("$source/$file")?
				Dir::copyDir("$source/$file", "$destination/$file"):
				copy("$source/$file", "$destination/$file");
			}
		}
		closedir($handle);
	}

}//�ඨ�����

if(!class_exists('DirectoryIterator')) {
	class DirectoryIterator extends Dir {}
}
?>