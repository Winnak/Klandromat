<?php 
define("OAUTH_PROVIDER", "https://services.brics.dk/java/dovs-auth/"); // Use AU-OAUTH provider.
define("OAUTH_CLIENT_ID","dovs");
define("OAUTH_TMP_DIR", function_exists("sys_get_temp_dir") ? sys_get_temp_dir() : realpath($_ENV["TMP"]));

// Load personal settings.
require_once("config.personal.php");
?>