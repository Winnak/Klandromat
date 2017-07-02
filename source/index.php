<?php 
session_start();
require_once("config.php");
header("Content-type: text/html; charset=UTF-8");

$db = new mysqli(MYSQL_PROVIDER, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
$db->set_charset("utf8");

require_once("database-helper.php");

require_once("routing.php");

$db->close();
 ?>