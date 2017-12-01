<?php
require_once("rest-helper.php");
if (isset($db)) {
    $db->close();
}
http_response_code(405);
?>{"code":404,"message":"Method Not Allowed"}