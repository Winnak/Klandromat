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
            if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
                if (isset($_GET["team"]) && is_numeric($_GET["team"])) {

                    if (isset($_GET["prev"])) {
                        $symbol = "<";
                        $order = "DESC";
                    } else if (isset($_GET["next"])) {
                        $symbol = ">";
                        $order = "ASC";
                    } else {
                        require("400.php");
                        die();
                    }

                    $next = "SELECT * FROM klandring
                             WHERE creationdate $symbol (SELECT creationdate FROM `klandring`
                                                   WHERE id=$_GET[id]
                                                     AND team=$_GET[team])
                               AND team=$_GET[team]
                             ORDER BY creationdate $order
                             LIMIT 1";

                    $result = $db->query($next);

                    $row = $result->fetch_array(MYSQLI_ASSOC);
                    $result->free();

                    if ($row == null) {
                        require("404.php");
                        die();
                    }

                    echo json_encode($row);

                } else {
                    $row = get_klandring_from_id($_GET["id"]);
                    if (get_user_role($user["id"], $row["team"]) == ROLE_APPLICANT) {
                        require("404.php");
                        die();
                    }

                    // TODO: validate that verdict is correct.

                    echo json_encode($row);
                }

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