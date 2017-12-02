<?php
require_once("rest-helper.php");

$user = get_user_from_auth();

if (!$user) {
    raise_error(403);
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
                        raise_error(400);
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
                        raise_error(404);
                    }

                    echo json_encode($row);

                } else {
                    $row = get_klandring_from_id($_GET["id"]);
                    if (get_user_role($user["id"], $row["team"]) == ROLE_APPLICANT) {
                        raise_error(404);
                    }

                    // TODO: validate that verdict is correct.

                    echo json_encode($row);
                }

            } else if (isset($_GET["team"])) {
                if (get_user_role($user["id"], $_GET["team"]) == ROLE_APPLICANT) {
                    raise_error(404);
                }

                echo json_encode(get_klandring_from_team($_GET["team"]));
            } else {
                raise_error(400);
            }

        } catch (InvalidArgumentException $e) {
            raise_error(404, $e);
        }
        break;

    case "POST":
        break;

    default:
        raise_error(405);
        break;

}

$db->close();
?>