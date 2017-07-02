<?php
define("ROLE_USER", 1); // Student is a member of the team.
define("ROLE_ADMIN", 2); // Student is an admin on the team.
define("ROLE_APPLICANT", 3); // Student has required access to the team.

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
?>