<?php
require_once("rest-helper.php");

$user = get_user_from_auth();

if (!$user) {
    require("403.php");
    die();
}

try {
    $klandringer = get_overview_klandringer($user["id"]);

    echo json_encode($klandringer);

} catch (InvalidArgumentException $e) {
    require("404.php");
    die();
}

$db->close();
?>