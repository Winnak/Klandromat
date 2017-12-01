<?php
require_once("rest-helper.php");

if (validate() == FALSE) {
    require("403.php");
    die();
}

if (isset($_GET["id"])) {
    $ids_str = $_GET["id"];

    if (is_numeric($ids_str)) {
        $ids = $_GET["id"];
    } else {
        $ids = array_map("intval", array_filter(explode(',', $ids_str), "is_numeric"));
        if (count($ids) == 0) {
            require("400.php");
            die();
        }
    }
    
    echo json_encode(get_user_infos_arr($ids));

} else {
    require("400.php");
    die();
}


$db->close();
?>