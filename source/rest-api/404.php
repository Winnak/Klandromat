<?php
require_once("rest-helper.php");
if (isset($db)) {
    $db->close();
}
http_response_code(404);
?>{"code":404,"message":"Resource not found"}