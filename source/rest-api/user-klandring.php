<?php
require("rest-helper.php");
header("Content-Type: application/json; charset=UTF-8");

$user = get_user_from_auth();

if (!$user) {
    require("403.php");
    die();
}

try {
    $klandringer = get_overview_klandringer($user["id"]);

    header("status: 200");
    echo json_encode($klandringer);

} catch (InvalidArgumentException $e) {
    require("404.php");
    die();
}

$db->close();
?>