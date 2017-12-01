<?php
require("../config.php");
require("../database-helper.php");
header("Content-Type: application/json; charset=UTF-8");

$db = new mysqli(MYSQL_PROVIDER, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
$db->set_charset("utf8");


function valid_auth($token) {
    global $db;
    $auth = explode(" ", $token);

    if ($auth[0] != "Bearer") {
        return FALSE;
    }

    $token = $db->real_escape_string($auth[1]);

    $sql = "SELECT id FROM `student` WHERE `apitoken`=X'$token'";
    $result = $db->query($sql);

    if (($result == FALSE) || ($result->fetch_array(MYSQLI_ASSOC) == NULL)) {
        return FALSE;
    }

    return TRUE;
}

function validate() {
    return isset($_SERVER["HTTP_AUTHORIZATION"])
        && valid_auth($_SERVER["HTTP_AUTHORIZATION"]);
}

function get_user_from_auth() {
    if (!isset($_SERVER["HTTP_AUTHORIZATION"])) {
        return FALSE;
    }

    global $db;
    $auth = explode(" ", $_SERVER["HTTP_AUTHORIZATION"]);

    if ($auth[0] != "Bearer") {
        return FALSE;
    }

    $token = $db->real_escape_string($auth[1]);
    $sql = "SELECT * FROM `student` WHERE `apitoken`=X'$token'";
    $result = $db->query($sql);

    if ($result == FALSE) {
        return FALSE;
    }

    $row = $result->fetch_array(MYSQLI_ASSOC);

    return $row;
}

?>