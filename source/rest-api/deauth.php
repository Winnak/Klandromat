<?php
require("rest-helper.php");
if (validate() == FALSE) {
    require("403.php");
    die();
}

header("Content-Type: application/json; charset=UTF-8");

$auth = split(" ", $_SERVER["HTTP_AUTHORIZATION"]);

$sql = "UPDATE `student` SET `apitoken`=NULL WHERE `apitoken`=$auth";
$db->query($sql);

$db->close();

header("status: 200");
?>{"code":200,"message":"Token has been removed."}