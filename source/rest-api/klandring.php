<?php
require_once("rest-helper.php");

$user = get_user_from_auth();

if (!$user) {
    require("403.php");
    die();
}

switch ($_SERVER['REQUEST_METHOD']) {
    case "GET":
        try {
            if (isset($_GET["id"])) {
                $row = get_klandring_from_id($_GET["id"]);
                if (get_user_role($user["id"], $row["team"]) == ROLE_APPLICANT) {
                    require("404.php");
                    die();
                }

                // TODO: validate that verdict is correct.

                echo json_encode($row);
            } else if (isset($_GET["team"])) {
                if (get_user_role($user["id"], $_GET["team"]) == ROLE_APPLICANT) {
                    require("404.php");
                    die();
                }

                echo json_encode(get_klandring_from_team($_GET["team"]));
            } else {
                require("400.php");
                die();
            }

        } catch (InvalidArgumentException $e) {
            require("404.php");
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