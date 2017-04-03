<a href="<?php
echo OAUTH_PROVIDER . "authorize?client_id=" . OAUTH_CLIENT_ID 
                    . "&amp;response_type=code"
                    . "&amp;redirect_uri=http://$_SERVER[HTTP_HOST]/oauth-callback.php";
?>"><h3>Login</h3></a>