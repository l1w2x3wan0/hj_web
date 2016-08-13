<?php
/**
 * User: Beyond_dream
 * Date: 2016/8/12
 * Time: 11:47
 */
header("Content-type: text/html; charset=utf-8");

require 'config.php';

try {
    $pdo = new PDO("mysql:dbname={$config['db']['DB_NAME']};host={$config['db']['DB_HOST']}", $config['db']['DB_USER'], $config['db']['DB_PWD']);
} catch (PDOException $e) {
    echo 'Could not connect: ' . $e->getMessage();
}

$pdo->query('set names utf8');

$sql = 'SELECT * FROM `user`';

foreach ($pdo->query($sql) as $row) {
    printf ("%s (%s) %s \n", $row[0], $row[1], $row[2]);
    printf ("%s (%s) %s  \n", $row['Host'], $row['User'], $row['Password']);
}

$pdo->exec();


