<?php
require_once("rest-helper.php");
if (isset($db)) {
    $db->close();
}
http_response_code(403);
?>{"code":403,"message":"User unauthorized."}