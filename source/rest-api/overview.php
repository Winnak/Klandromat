<?php
require_once("rest-helper.php");

$user = get_user_from_auth();

if (!$user) {
    raise_error(401);
}

if (!isset($_GET["team"]) || !is_numeric($_GET["team"])) {
    raise_error(400);
}

$result = get_overview_klandringer($user["id"], $_GET["team"]);

if (!$result) {
    raise_error(404);
}

echo json_encode($result);


$db->close();
?>