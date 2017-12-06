<?php
require_once("rest-helper.php");
if (validate() == FALSE) {
    raise_error(401);
}

$auth = split(" ", $_SERVER["HTTP_AUTHORIZATION"]);

$sql = "UPDATE `student` SET `apitoken`=NULL WHERE `apitoken`=$auth";
$db->query($sql);

$db->close();

?>{"code":200,"message":"Token has been removed."}