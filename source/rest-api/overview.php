<?php
require_once("rest-helper.php");

$user = get_user_from_auth();

if (!$user) {
    require("403.php");
    die();
}

if (!isset($_GET["team"]) || !is_numeric($_GET["team"])) {
    require("400.php");
    die();
}

$result = get_overview_klandringer($user["id"], $_GET["team"]);

if (!$result) {
    require("404.php");
    die();
}

echo json_encode($result);


$db->close();
?>