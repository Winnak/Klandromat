<?php require_once("template/header.php") ?>
<?php

if (isset($_SESSION["oauth-success"]) && $_SESSION["oauth-success"]) { // logged in
    $path = $_SERVER['REQUEST_URI'];

    echo "Logged in! <br>Hello ";
    echo $_SESSION["auid"] . '.<br>';
    echo '<a href="logout.php">Logout</a>';
}
else { // not logged in.
require_once("login.php");
}
?>
<?php require_once("template/footer.php") ?>