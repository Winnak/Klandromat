<?php
require_once("rest-helper.php");

if (validate() == FALSE) {
    raise_error(401);
}

if (isset($_GET["id"])) {
    $ids_str = $_GET["id"];

    if (is_numeric($ids_str)) {
        $ids = $_GET["id"];
    } else {
        $ids = array_map("intval", array_filter(explode(',', $ids_str), "is_numeric"));
        if (count($ids) == 0) {
            raise_error(400);
        }
    }

    if (isset($_GET["students"])) {
        $students = [];
        foreach ($ids as $teamid) {
            $students[$teamid] = get_students_of_team($teamid);
        }

        echo json_encode($students);
    } else {
        echo json_encode(get_team_infos_arr($ids));
    }

} else {
    raise_error(400);
}


$db->close();
?>