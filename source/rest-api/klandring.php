<?php
require_once("rest-helper.php");

$user = get_user_from_auth();

if (!$user) {
    require("403.php");
    die();
}

switch ($_SERVER['REQUEST_METHOD']) {
    case "GET":
        if (isset($_GET["id"])) {
            try {
                $row = get_klandring_from_id($_GET["id"]);
                if (get_user_role($user["id"], $row["team"]) == ROLE_APPLICANT) {
                    require("404.php");
                    die();
                }

                // TODO: validate that verdict is correct.

                echo json_encode($row);

            } catch (InvalidArgumentException $e) {
                require("404.php");
                die();
            }
        } else {
            require("400.php");
            die();
        }
        break;

    case "POST":
        break;

    default:
        require("405.php");
        die();
        break;
    
}

$db->close();
?>