<?php
define("NONE",                 0     );
define("NO_TEMPLATE",          1 << 0);
define("NO_AUTH_REQ",          1 << 1); // I.e. required that the user has not been authenticated.
define("AUTH_REQ",             1 << 2); // I.e. user is authenticated, and on a team.
define("IS_ROLE_TREASURER",    1 << 3);
define("IS_ROLE_TEAMADMIN",    1 << 4);
define("IS_ROLE_SUPER_ADMIN",  1 << 5);

function resolve_routes() {
    $controller = "routes/404.php";
    $resources = array();
    
    foreach ([
        // direct error pages for failed ops
        ["url" => "/^\/error-404*/",                                    "view" => "routes/404.php",                  "flags" => AUTH_REQ],
        
        // Home pages
        ["url" => "/^\/\s*$/",                                          "view" => "routes/upcoming.php",             "flags" => AUTH_REQ], // home page (logged in)
        ["url" => "/^\/logout$/",                                       "view" => "routes/logout.php",               "flags" => NONE],     // logout

        // Klandring pages
        ["url" => "/^\/klandring\/create$/",                            "view" => "routes/klandring-create.php",     "flags" => AUTH_REQ], // create klanring
        ["url" => "/^\/klandring\/(?P<kid>[0-9]+)$/",                   "view" => "routes/klandring-show.php",       "flags" => AUTH_REQ], // create klanring

        // User pages
        ["url" => "/^\/(?P<user>au[0-9]+)$/",                           "view" => "routes/user-index.php",           "flags" => AUTH_REQ], // user page
        ["url" => "/^\/(?P<user>au[0-9]+)\/edit$/",                     "view" => "routes/user-edit.php",            "flags" => AUTH_REQ], // edit user page
        
        // Team pages
        ["url" => "/^\/(?P<team>[a-z0-9\-]{1,35})$/",                  "view" => "routes/team-index.php",           "flags" => AUTH_REQ], // team page
        ["url" => "/^\/(?P<team>[a-z0-9\-]{1,35})\/admin$/",           "view" => "routes/team-admin.php",           "flags" => AUTH_REQ | IS_ROLE_TREASURER], // admin page
        ["url" => "/^\/(?P<team>[a-z0-9\-]{1,35})\/admin-klandring$/", "view" => "routes/team-admin-klandring.php", "flags" => AUTH_REQ | IS_ROLE_TREASURER], // admin klandring page

        // Fallback
        ["url" => "/.*/",                                              "view" => "routes/404.php",                  "flags" => AUTH_REQ], // otherwise case.
        ["url" => "/^.*$/",                                            "view" => "routes/login.php",                "flags" => NONE],     // otherwise case.

        ] as $route) {

        $path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

        $resources = array(2);
        if (preg_match($route["url"], $path, $resources) === 0) {
            continue;
        }

        if (!validate_flags($route, $resources)) {
            continue;
        }

        if (($route["flags"] & NO_TEMPLATE) == NO_TEMPLATE) {
            require_once($route["view"]);
            return;
        }

        $controller = $route["view"];
        break;
    }

    global $db;
    require_once("template/header.php");
    require_once($controller);
    require_once("template/footer.php");
}

function validate_flags($route, $matches) {
    $valid = true;
    $flags = $route["flags"];
    if ($flags & NO_AUTH_REQ) {
        if (isset($_SESSION["oauth-success"])) {
            $valid &= ($_SESSION["oauth-success"] == 0);
        }
    } else if ($flags & AUTH_REQ) {
        if (!isset($_SESSION["oauth-success"])) {
            return false;
        }
        $valid &= ($_SESSION["oauth-success"] == 1);
    }

    if ($flags & IS_ROLE_TREASURER) {
        $valid &= has_role(ROLE_TREASURER, $matches["team"]);
    }
    if ($flags & IS_ROLE_TEAMADMIN) {
        $valid &= has_role(ROLE_TREASURER, $matches["team"]);
    }
    if ($flags & IS_ROLE_SUPER_ADMIN) {
        $valid &= in_array($_SESSION["auid"], $ULTRA_USERS);
    }

    return $valid;
}

function has_role($role, $team_slug) {
    if (!isset($_SESSION["teams"])) {
        return false;
    }

    $user_role = ROLE_APPLICANT;
    foreach ($_SESSION["teams"] as $key => $value) {
        if ($value["slug"] === $team_slug) {
            $user_role |= $value["roleid"];
        }
    }

    return ($user_role & $role) == $role;
}

resolve_routes();
?>