<?php
require_once("rest-helper.php");
if (isset($db)) {
    $db->close();
}
http_response_code(400);
?>{"code":400,"message":"Bad Request"}