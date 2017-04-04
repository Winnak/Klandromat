<?php 
session_start();
require_once("config.php");
header('Content-type: text/html; charset=UTF-8');

$paths = preg_split('/\//', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), -1, PREG_SPLIT_NO_EMPTY);

function route_to($controller, 
    $arguements = array(),
    $header_stuff = array("title" => "Klandromat"),
    $footer_stuff = array())
{
    require_once("template/header.php");
    require_once("routes/" . $controller);
    require_once("template/footer.php");
}

if (isset($_SESSION["oauth-success"])) { // logged in
    if ($paths[0] === "logout") {
        require_once("routes/logout.php");
    } else if ($_SESSION["oauth-success"]) {
        if (count($paths) === 0) {
            header("Location: /" . $_SESSION["auid"]);
        } else {
            $db = new mysqli(MYSQL_PROVIDER, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
            $db->set_charset("utf8");
            if (substr($paths[0], 0, 2) === "au") {
                $auid = $db->real_escape_string($paths[0]);
                $sql = "SELECT * FROM student WHERE `auid` = '$auid' LIMIT 1";
                $result = $db->query($sql);
                $row = $result->fetch_array(MYSQLI_ASSOC);

                if($row) { // A person that is in the database.
                    if($_SESSION["auid"] === $row["auid"]) { // the logged in user
                        if (count($paths) === 2 && $paths[1] === "edit") {
                            route_to("user-edit.php", 
                                $row, 
                                ["title" => $row["name"] . " - Klandromat",
                                "scripts" => [
                                    "https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js",
                                    "https://cdnjs.cloudflare.com/ajax/libs/jquery-validation-unobtrusive/3.2.6/jquery.validate.unobtrusive.min.js"
                                    ]
                                ]);
                        } else {
                            route_to("user.php", 
                                $row, 
                                ["title" => $row["name"] . " - Klandromat"]);
                        }
                    } else { // logged in, looking at another user.
                        route_to("user.php", 
                            $row, 
                            ["title" => $row["name"] . " - Klandromat"]);
                    }
                } else { // a person who is not in the database.
                    route_to("signup.php", 
                        ["auid" => $auid],
                        ["title" => "Signup! - Klandromat"]);
                }
            $result->free();
            } else if (($paths[0] === "klandring") && (count($paths) === 2)) {
                if($paths[1] === "create") {
                    route_to("klandring-create.php");
                } else {
                    $klandid = $db->real_escape_string($paths[1]);
                    $sql = "SELECT id, verdictdate, title, description, `from`, `to`, verdict, paid FROM klandring WHERE id=$klandid LIMIT 1";
                    $result = $db->query($sql);
                    if ($result) {
                        $row = $result->fetch_array(MYSQLI_ASSOC);
                        if($row) {
                            $name = $row["title"];
                            route_to("klandring.php", 
                                $row, 
                                ["title" => "$name - Klandring - Klandromat"]);
                        } else {
                            $result->free();
                            header("Location: /klandring/create");
                        }
                    } else {
                        $result->free();
                        header("Location: /klandring/create");
                    }
                    $result->free();
                }
            }
            $db->close();
        }
    } else { // a person who is not in the database.
        route_to("signup.php", 
            array(),
            ["title" => "Signup! - Klandromat"]);
    }
} else if(count($paths) !== 0) {
    header("Location: /");
} else { // not logged in.
    route_to("login.php");
}
 ?>