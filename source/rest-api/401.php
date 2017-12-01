<?php
require_once("rest-helper.php");
if (isset($db)) {
    $db->close();
}
http_response_code(401);
?>{"code":401,"message":"Authentication Required"}