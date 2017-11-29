<?php
require("rest-helper.php");
header("Content-Type: application/json; charset=UTF-8");

$user = get_user_from_auth();

if (!$user) {
    require("403.php");
    die();
}

if (isset($_GET["id"])) {
    try {
        $row = get_klandring_from_id($_GET["id"]);
        if (get_user_role($user["id"], $row["team"]) == ROLE_APPLICANT) {
            require("404.php");
            die();
        }

        // TODO: validate that verdict is correct.

        header("status: 200");
        echo json_encode($row);

    } catch (InvalidArgumentException $e) {
        require("404.php");
        die();
    }
} else {
    require("400.php");
    die();
}

$db->close();
?>