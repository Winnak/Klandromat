<?php
if (isset($db)) {
    $db->close();
}
header("status: 403");
header("Content-Type: text/json; charset=UTF-8");
?>{"code":403,"message":"User unauthorized."}