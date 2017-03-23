<?php require_once("template/header.php") ?>
<?php
require("config.php");

if (isset($_SESSION["oauth-success"])) {
    echo "Logged in! <br>Hello ";
    echo $_SESSION["auid"] . '.<br>';
    echo '<a href="logout.php">Logout</a>';
}
else {

echo '<a href="';

echo OAUTH_PROVIDER . "authorize?client_id=" . OAUTH_CLIENT_ID 
                    . "&amp;response_type=code"
                    . "&amp;redirect_uri=http://$_SERVER[HTTP_HOST]/oauth-callback.php";

echo '"><h3>Login</h3></a>';
}
?>
<?php require_once("template/footer.php") ?>