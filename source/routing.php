<?php
$paths = preg_split("/\//", parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), -1, PREG_SPLIT_NO_EMPTY);

function route_to($controller, 
    $arguments = array(),
    $header_stuff = array("title" => "Klandromat"),
    $footer_stuff = array()) {
    global $db;
    require_once("template/header.php");
    require_once("routes/" . $controller);
    require_once("template/footer.php");
}

if (!isset($_SESSION["oauth-success"])) {
    route_to("login.php");
} else if ($_SESSION["oauth-success"] === 2) {
    if (count($paths)) {
        if ($paths[0] === "logout") {
            require_once("routes/logout.php");
        } else {
            header("Location: /");
        }
    } else {
        route_to("signup.php",
            array(),
            ["title" => "Signup!"]);
    }
} else if ($_SESSION["oauth-success"] === 1) { // logged in
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
                    if (($team["roleid"] != ROLE_TREASURER) || (count($paths) === 1)) {
                        route_to("team-index.php",
                            $team,
                            ["title" => $team["name"]]);
                    } else if (count($paths) === 2) {
                        if ($paths[1] === "admin") {
                            route_to("team-admin.php",
                                $team,
                                ["title" => $team["name"] . " admin"]);
                        } else if ($paths[1] === "admin-klandring") {
                            route_to("team-admin-klandring.php",
                                $team,
                                ["title" => $team["name"] . " admin"]);
                        }
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
}
?>