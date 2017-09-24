<?php
$VALID_LETTERS = array();

if (!defined("DATABASE_CONSTS")) {
    define("DATABASE_CONSTS", true);
    define("ROLE_APPLICANT", 0); // Student has required access to the team.
    define("ROLE_USER", 1);      // Student is a member of the team.
    define("ROLE_TREASURER", 2); // Student is a treasurer on the team.
    define("ROLE_TEAMADMIN", 3); // Student is an admin on the team.
    define("DATA_PATH", "/static/data/");
    $VALID_LETTERS = str_split("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_");
    define("VALID_PATH_LETTERS_LENGTH", count($VALID_LETTERS));
}
    
function get_random_filename($length) {
    global $VALID_LETTERS;
    $path = "";
    for ($i=0; $i < $length; $i++) { 
        $path .= $VALID_LETTERS[rand(0, VALID_PATH_LETTERS_LENGTH - 1)];
    }
    return $path;
}

 /**
  * Fetches the infos from a 1... users from the database. 
  *
  * @var int $ids,... ids corresponding to the user id in the database.
  *
  * @throws InvalidArgumentException if the input was not strictly integers.
  *
  * @return mixed array of user infos (including id, auid, year, name, email, phone).
  *           note: result will be in sorted order, and not id order.
  */
function get_user_infos(... $ids) {
    return get_user_infos_arr($ids);
}
/**
 * @see get_user_infos
 */
function get_user_infos_arr($ids) {
    global $db;
    assert($db !== null, "DB is not ready for get_user_infos.");
    assert(count($ids) > 0, "get_user_infos needs at least 1 id.");
    
    $manifest = "";
    for ($i=0; $i < count($ids); $i++) { 
        $id = $ids[$i];
        if (!is_int($id) || ($id < 0)) {
            throw new InvalidArgumentException("get_user_infos function only accepts integers. Input was: ".$id." (".gettype($id).")");
        }
        $manifest .= $id.",";
    }
    $manifest = substr($manifest, 0, -1); // remove trailing comma

    $sql = "SELECT * FROM student WHERE id IN ($manifest) LIMIT " . count($ids);
    $result = $db->query($sql);
    
    $rows = [];
    while($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $rows[] = $row;
    }
    return $rows;
}

function get_students_of_team($team_id) {
    global $db;
    assert($db !== null, "DB is not ready for get_students_of_team.");

    $sql = "SELECT A.* FROM student A
        INNER JOIN teamstudent B ON A.id = B.studentid
        WHERE B.teamid = $team_id";

    $result = $db->query($sql);

    return $result;
}
?>