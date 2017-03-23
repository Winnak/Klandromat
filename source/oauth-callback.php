<?php require_once("template/header.php");
require("config.php");

if (isset($_GET["code"]) && isset($_GET["username"])) {

    $query_params = http_build_query(
        array(
            "client_id"     => OAUTH_CLIENT_ID,
            // "client_secret" => OAUTH_CLEINT_SECRET,
            "redirect_uri"  => "http://$_SERVER[HTTP_HOST]/" . $_GET["username"] . "/",
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
            header("Location: /" . $_GET["username"] . "/");
            die();
        }
    }
}
else{
    failure:
    echo '<div class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span><span class="sr-only">Error:</span> Invalid login.</div>';

    header("refresh:60;url=index.php");
}


require_once("template/footer.php"); ?>