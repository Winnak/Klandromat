<a href="<?= OAUTH_PROVIDER . "authorize?client_id=" . OAUTH_CLIENT_ID 
                    . "&amp;response_type=code"
                    . "&amp;redirect_uri=".(isset($_SERVER["HTTPS"]) ? "https://" : "http://")."$_SERVER[HTTP_HOST]/oauth-callback.php"
?>"><h3>Login</h3></a>

Velkommen til klandromaten, snak med en af teknikkerne for at sætte dit hold op.