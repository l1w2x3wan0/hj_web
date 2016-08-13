<?php
header('Content-type:text/html; charset=utf-8');

$config['db'] = array(

'DB_HOST'               => 'localhost',
'DB_NAME'               => 'fenxi_ben',
'DB_USER'               => 'root',
'DB_PWD'                => '123456',
'DB_PORT'               => '3306',
/*
    'DB_HOST'               => '192.168.1.252',
    'DB_NAME'               => 'fenxi_ben',
    'DB_USER'               => 'root',
    'DB_PWD'                => 'dj2015',
    'DB_PORT'               => '3306',
*/
);

$conn = mysql_connect($config['db']['DB_HOST'], $config['db']['DB_USER'], $config['db']['DB_PWD'], $config['db']['DB_PORT']) OR die('could not connect db: '. mysql_error());
$db = mysql_select_db($config['db']['DB_NAME']);
mysql_query('set names UTF8');

var_dump($conn);

$sql = 'SELECT * FROM `users` LIMIT 0, 10';
$result = mysql_query($sql);
var_dump($result);
if ($result) {
    while ($row = mysql_fetch_array($result, MYSQL_BOTH)) {
        print $row[0] . '---' . $row[1] . '---' . $row[2] . '---' . $row[3] . "<br/>\n";
    }
}

//mysql_free_result($result);
mysql_close($conn);