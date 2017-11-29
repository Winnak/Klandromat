<?php
if (isset($db)) {
    $db->close();
}
header("status: 400");
header("Content-Type: text/json; charset=UTF-8");
?>{"code":400,"message":"Bad Request"}