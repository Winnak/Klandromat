<?php 
session_start();
require_once("config.php");
header('Content-type: text/html; charset=UTF-8');

define("ROLE_USER", 1);
define("ROLE_ADMIN", 2);

$db = new mysqli(MYSQL_PROVIDER, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
$db->set_charset("utf8");

$paths = preg_split('/\//', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), -1, PREG_SPLIT_NO_EMPTY);

function route_to($controller, 
    $arguments = array(),
    $header_stuff = array("title" => "Klandromat"),
    $footer_stuff = array()) {
    global $db;
    require_once("template/header.php");
    require_once("routes/" . $controller);
    require_once("template/footer.php");
}

 /**
  * Fetches the infos from a 1... users from the database. 
  *
  * @var int $ids,... ids corresponding to the user id in the database.
  *
  * @throws InvalidArgumentException if the input was not strictly integers.
  *
  * @return mixed array of user infos (including id, auid, year, name, email, phone).
  *           note: result will be in sorted order, and not id order.
  */
function get_user_infos(... $ids) {
    return get_user_infos_arr($ids);
}
/**
 * @see get_user_infos
 */
function get_user_infos_arr($ids) {
    global $db;
    assert($db !== null, "DB is not ready for get_user_infos.");
    assert(count($ids) > 0, "get_user_infos needs at least 1 id.");
    
    $manifest = "";
    for ($i=0; $i < count($ids); $i++) { 
        $id = $ids[$i];
        if (!is_int($id) || ($id < 0)) {
            throw new InvalidArgumentException("get_user_infos function only accepts integers. Input was: ".$id." (".gettype($id).")");
        }
        $manifest .= $id.",";
    }
    $manifest = substr($manifest, 0, -1); // remove trailing comma

    $sql = "SELECT * FROM student WHERE id IN ($manifest) LIMIT " . count($ids);
    $result = $db->query($sql);
    
    $rows = [];
    while($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $rows[] = $row;
    }
    return $rows;
}

if (isset($_SESSION["oauth-success"])) { // logged in
    if (count($paths) === 0) {
            route_to("upcoming.php", 
                array(), 
                ["title" => "Upkommende klandringer"]);
    } else {
        if ($paths[0] === "logout") {
            require_once("routes/logout.php");
        } else if (substr($paths[0], 0, 2) === "au") {
            $auid = $db->real_escape_string($paths[0]);
            $sql = "SELECT * FROM student WHERE auid = '$auid' LIMIT 1";
            $result = $db->query($sql);
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $result->free();

            if($row) { // A person that is in the database.
                if((count($paths) === 2) && ($paths[1] === "edit")) {
                    if ($_SESSION["auid"] === $row["auid"]) {
                        route_to("user-edit.php", 
                            $row, 
                            ["title" => $row["name"],
                            "scripts" => [
                                "https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js",
                                "https://cdnjs.cloudflare.com/ajax/libs/jquery-validation-unobtrusive/3.2.6/jquery.validate.unobtrusive.min.js"
                                ]
                            ]);
                    } else {
                        route_to("404.php", array(), ["title" => "404"]);
                    }
                } else { // logged in, looking at a specific user.
                    route_to("user-index.php", 
                        $row, 
                        ["title" => $row["name"]]);
                }
            } else { // a person who is not in the database.
                route_to("signup.php", 
                    ["auid" => $auid],
                    ["title" => "Signup!"]);
            }
        } else if (($paths[0] === "klandring")) {
            if (count($paths) === 2) {
                if($paths[1] === "create") {
                    route_to("klandring-create.php", 
                        array(), 
                        ["title" => "Ny klandring",
                        "scripts" => []
                        ]);
                    
                } else {
                    $klandid = $paths[1];
                    if (is_numeric($klandid)) {
                        $sql = "SELECT * FROM klandring WHERE id = $klandid LIMIT 1";
                        $result = $db->query($sql);
                        $row = $result->fetch_array(MYSQLI_ASSOC);
                        $result->free();

                        // TODO: Verify that we are allowed to see this.

                        if($row) {
                            $name = $row["title"];
                            route_to("klandring-show.php", 
                                $row, 
                                ["title" => "Klandring"]);
                        } else {
                            header("Location: /klandring/create");
                        }
                    } else {
                        header("Location: /klandring/create");
                    }
                }
            } else {
                header("Location: /klandring/create");
            }
        } else {
            $found = false;
            foreach ($_SESSION["teams"] as $team) {
                if ($team["slug"] == $paths[0]) {
                    $found = true;
                    if ((count($paths) === 2) && ($paths[1] === "admin") && ($team["roleid"] == ROLE_ADMIN)) {
                        route_to("team-admin.php", 
                            $team, 
                            ["title" => $team["name"] . " admin"]);
                    } else if (count($paths) === 1) {
                        route_to("team-index.php", 
                            $team, 
                            ["title" => $team["name"]]);
                    } else {
                        header("Location: /$team[slug]"); // sanitize url in case it was gibberish.
                    }

                    break;
                }
            }

            if (!$found) {
                route_to("404.php", array(), ["title" => "404"]);
            }
        }
    }
} else if(count($paths) !== 0) { 
    route_to("signup.php", 
        ["auid" => $auid],
        ["title" => "Signup!"]);
} else { // not logged in.
    route_to("login.php");
}
$db->close();
 ?>