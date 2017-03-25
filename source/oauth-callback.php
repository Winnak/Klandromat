<?php
session_start();

require("config.php");

if (isset($_GET["code"]) && isset($_GET["username"])) {

    $query_params = http_build_query(
        array(
            "client_id"     => OAUTH_CLIENT_ID,
            // "client_secret" => OAUTH_CLEINT_SECRET,
            "redirect_uri"  => "http://$_SERVER[HTTP_HOST]/" . SITE_ROOT . "/"  . $_GET["username"] . "/",
            "grant_type"    => "authorization_code",
            "username"      => $_GET["username"],
            "code"          => $_GET["code"]
        )
    );

    $url = OAUTH_PROVIDER . "token?" . $query_params;

    $result = file_get_contents($url);

    if ($result !== FALSE) { 
        if(strpos($http_response_header[0], "200"))
        {
            $_SESSION["auid"]          = $_GET["username"];
            $_SESSION["oauth-code"]    = $_GET["code"];
            $_SESSION["oauth-success"] = true;
            header("Location: /" . SITE_ROOT . "/"  . $_GET["username"]);
        }
    }
}
?>