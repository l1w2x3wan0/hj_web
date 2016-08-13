<?php
define("work", true);

define( 'ROOT_PATH', __DIR__ .'/' );

define( 'THINK_PATH', ROOT_PATH  . 'ThinkPHP/' );

define( 'DB_HOST', 'http://www.fenxi.dev');

define( 'APP_NAME', 'work' );

define( 'APP_PATH', ROOT_PATH  . 'work/' );

define( 'CSS_PATH', DB_HOST . '/Public/css/' );

define( 'JS_PATH', DB_HOST . '/Public/js/' );

define('APP_DEBUG', true);

date_default_timezone_set("Asia/Shanghai");
//echo THINK_PATH . "\n"; exit;
require(THINK_PATH . "ThinkPHP.php");


/*
 * //定义一个常量防止非法链接本网站
define("work",true);
//定义 ThinkPHP 框架路径
define( 'THINK_PATH' , '../../ThinkPHP/' );

//定义项目 名称和路径
define( 'DB_HOST'  , 'http://test.ysz.kk520.com:9102'); //设置主机
define( 'APP_NAME' , 'work' );
define( 'APP_PATH' , dirname($_SERVER['SCRIPT_FILENAME']).'/work/' );
define( 'ROOT_PATH' , dirname($_SERVER['SCRIPT_FILENAME']).'/' );
define( 'CSS_PATH' , DB_HOST.'/Public/css/' );
define( 'JS_PATH' ,  DB_HOST.'/Public/js/' );
//缓存路径
define( 'APP_PATH' ,  dirname($_SERVER['SCRIPT_FILENAME']).'/WWW/web/' );


date_default_timezone_set("Asia/Shanghai");

//加载框架入口文件
require(THINK_PATH."/ThinkPHP.php");*/
