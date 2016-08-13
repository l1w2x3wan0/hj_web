<?php
/**
 * User: Beyond_dream
 * Date: 2016/8/12
 * Time: 10:34
 */
header("Content-type: text/html; charset=utf-8");

require 'config.php';

$mysqli = new mysqli($config['db']['DB_HOST'], $config['db']['DB_USER'], $config['db']['DB_PWD'], $config['db']['DB_NAME']);


if ($mysqli->connect_errno) {
    printf("Connect failed: $s \n". $mysqli->connect_error);
    exit();
}
$mysqli->select_db('mysql');
$mysqli->set_charset('uft8');

/*
mysqli_fetch_assoc() - Fetch a result row as an associative array
mysqli_fetch_row() - Get a result row as an enumerated array
mysqli_fetch_object() - Returns the current row of a result set as an object
mysqli_query() - Performs a query on the database
mysqli_data_seek() - Adjusts the result pointer to an arbitrary row in the result
*/
$sql = "SELECT * FROM `users`";
if ($result = $mysqli->query($sql)) {
    while ($row = $result->fetch_array(MYSQLI_BOTH)) {  //MYSQLI_NUM / MYSQLI_BOTH / MYSQLI_ASSOC
        printf ("%s (%s) %s \n", $row[0], $row[1], $row[2]);
        //printf ("%s (%s) %s  \n", $row['Host'], $row['User'], $row['Password']);
    }

    while ($row = $result->fetch_object()) {
        printf ("%s (%s) %s \n", $row->Host, $row->User, $row->Password);
    }
    $result->close();
}

$sql = "SELECT * FROM `db`";
if ($result = $mysqli->query($sql)) {
    while ($row = $result->fetch_object()) {
        printf("%s -- %s -- %s \n", $row->Host, $row->Db, $row->User);
    }
    $result->close();
}

if ($result = $mysqli->query("SELECT DATABASE()")) {
    $row = $result->fetch_row();
    printf("Default database is %s.\n", $row[0]);
    $result->close();
}

$mysqli->close();
