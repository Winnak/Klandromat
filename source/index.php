<?php 
session_start();
require_once("config.php");


$paths = preg_split('/\//', $_SERVER['REQUEST_URI'], -1, PREG_SPLIT_NO_EMPTY);

function route_to($controller, 
    array $header_stuff = array("title" => "Klandromat"), 
    array $arguements = array())
{
    require_once("template/header.php");
    require_once("routes/" . $controller);
    require_once("template/footer.php");
}

if (isset($_SESSION["oauth-success"]) && $_SESSION["oauth-success"]) { // logged in
    if (count($paths) === 1) {
        header("Location: /" . SITE_ROOT . "/" . $_SESSION["auid"]);
    }

    if ($paths[1] === "logout") {
        require_once("routes/logout.php");
    } else {
        if($_SESSION["auid"] === $paths[1]) {
            route_to("user.php", [
                "title" => $_SESSION["auid"] . " - Klandromat"
            ]);
        }
    }
} else { // not logged in.
    route_to("login.php");
}
 ?>