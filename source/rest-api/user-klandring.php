<?php
require_once("rest-helper.php");

$user = get_user_from_auth();

if (!$user) {
    raise_error(403);
}

try {
    $klandringer = get_overview_klandringer($user["id"]);

    echo json_encode($klandringer);

} catch (InvalidArgumentException $e) {
    raise_error(404, $e->getMessage());
}

$db->close();
?>