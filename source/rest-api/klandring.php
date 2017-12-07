<?php
require_once("rest-helper.php");

$user = get_user_from_auth();


if (!$user) {
    raise_error(401);
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
                                                     AND team=$_GET[team]
                                                     AND `from`=$user[id])
                               AND team=$_GET[team]
                             ORDER BY creationdate $order
                             LIMIT 1";

                    $result = $db->query($next);

                    $row = $result->fetch_array(MYSQLI_ASSOC);
                    $result->free();

                    if ($row == null) {
                        raise_error(404);
                    }
                    
                    echo json_encode(array(
                        "klandring" => $row,
                        "manifest" => get_klandring_media_from_id($_GET["id"]),
                        "users" => get_user_infos($row["from"], $row["to"])
                    ));

                } else {
                    $row = get_klandring_from_id($_GET["id"]);
                    if (get_user_role($user["id"], $row["team"]) == ROLE_APPLICANT) {
                        raise_error(404);
                    }

                    echo json_encode(array(
                        "klandring" => $row,
                        "manifest" => get_klandring_media_from_id($_GET["id"]),
                        "users" => get_user_infos($row["from"], $row["to"])
                    ));
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
            raise_error(400, $e->getMessage()); // while debugging we use this. uncomment line below when ready.
            // raise_error(404);
        }
        break;

    case "POST":
        // first validate
        foreach ($_FILES as $key => $file) {
            if ($file["error"] != UPLOAD_ERR_OK || $file["size"] == 0) {
                raise_error(400, "$key, $file[name] was not uploaded correctly");
            } else if ($file["size"] > 8388608) { // 8 MB in bytes
                raise_error(413, "$key, $file[name] was more than 8MB large");
            }
        }

        // create the klandring (since all went well)
        $result = post_klandring($_POST["title"], $_POST["desc"], $user["id"], $_POST["to"], $_POST["team"]);
        if (!$result) {
            raise_error(500, "Unexpected error");
        }

        // attach all media to the klandring.
        $klandring_id = $db->insert_id;

        foreach ($_FILES as $key => $file) {
            $ext = pathinfo($file["name"], PATHINFO_EXTENSION);
            if (strlen($ext) == 0) {
                $ext = "bin";
            }
            $target_filename = get_random_filename(59 -  strlen($ext)).".$ext";
            
            if (move_uploaded_file($file["tmp_name"],  DATA_PATH.$target_filename)) {
                // sql the stuff.
            } else {
                raise_error(400, "File malformed");
            }

            // hopefully no need to check for errors at this point, since it has all been checked once before.
            post_klandring_meta($klandring_id, $user["id"], $file["type"], $file["name"], $target_filename);
        }

        // raise_success("Klandring successfully registered at: $klandring_id");

        http_response_code(200);
        echo json_encode(array(
            "code" => 200,
            "message" => "Klandring successfully registered",
            "id" => $klandring_id
        ));
        break;
        
    case "DELETE":
        try {
            if (isset($_GET["id"])) {
                $result = remove_klandring_from_user($_GET["id"], $user["id"]);
                if (!$result) {
                    raise_error(404, "Could not find klandring, maybe you are not the klandre, or your window of deletion has expired.");
                }

                $row = $db->affected_rows;
                raise_success("Succesfully deleted klandring:  " + $row["title"]);
            }
        } catch (InvalidArgumentException $e) {
            raise_error(404);
        }
        break;

    default:
        raise_error(405);
        break;

}

$db->close();
?>