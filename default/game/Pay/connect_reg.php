<?php
session_start();
$HOST = "114.119.37.179";
$LOGIN = "root";
$PWD = "dj_zjh_2015";
$NAMES = "kingflower";

$HOST = "192.168.1.102:3307";
$LOGIN = "root";
$PWD = "dj2015";
$NAMES = "kingflower";

$conn = mysql_connect($HOST, $LOGIN, $PWD);
mysql_query("SET NAMES utf8");
mysql_select_db($NAMES);


?>