<?php
$VALID_LETTERS = array();

if (!defined("DATABASE_CONSTS")) {
    define("DATABASE_CONSTS", true);

    // roles
    define("ROLE_APPLICANT", 0); // Student has required access to the team.
    define("ROLE_USER", 1);      // Student is a member of the team.
    define("ROLE_TREASURER", 2); // Student is a treasurer on the team.
    define("ROLE_TEAMADMIN", 3); // Student is an admin on the team.

    // klandring
    define("WINNER_NONE", 0); // Klandring undetermained.
    define("WINNER_KLANDRER", 1); // Klandring was won by klandrer.
    define("WINNER_KLANDRET", 2); // Klandring was won by klandret.
    define("WINNER_BOTH", WINNER_KLANDRER | WINNER_KLANDRET); // Klandring was a draw.

    // meta
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
  * Fetches the user info from the database 
  *
  * @var string $auid corresponding to the user's auid in the database.
  *
  * @return mixed the row in the database, if it exists.
  */
function get_user_from_auid($auid) {
    global $db;
    assert($db !== null, "DB is not ready for get_user_from_auid.");
    assert(substr($auid, 0, 2) === "au", "get_user_info_auid needs to be a auid");

    $auid = $db->real_escape_string($auid);
    $sql = "SELECT * FROM student WHERE `auid` = '$auid' LIMIT 1";
    $result = $db->query($sql);
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $result->free();

    return $row;
}

 /**
  * Fetches the user info from the database 
  *
  * @var mixed (string or int) $some_id corresponding to the user's auid or year in the database.
  *
  * @return mixed the row in the database, if it exists.
  */
  function get_user_from_auid_or_year($some_id) {
    global $db;
    assert($db !== null, "DB is not ready for get_user_from_auid.");
    
    $some_id = $db->real_escape_string($some_id);
    $sql = "SELECT * FROM student WHERE `auid` = '$some_id' OR `year` = '$some_id' LIMIT 1";
    $result = $db->query($sql);
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $result->free();

    return $row;
}

 /**
  * Fetches a klandring from the database.
  *
  * @var int $id corresponding to the klandring's id in the database.
  *
  * @throws InvalidArgumentException if the input was not strictly integers.
  *
  * @return mixed the row in the database, if it exists.
  */
function get_klandring_from_id($id) {
    global $db;
    assert($db !== null, "DB is not ready for get_klandring_from_id.");

    if (!is_numeric($id)) {
        throw new InvalidArgumentException("get_klandring_from_id function only accepts integers. Input was: $id (".gettype($id).")");
    }

    $sql = "SELECT * FROM klandring WHERE id = $id LIMIT 1";
    $result = $db->query($sql);
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $result->free();

    return $row;
}

 /**
  * Fetches a list of unresolved klandringer relating to the user.
  *
  * @var int $studentid corresponding to the user's id.
  *
  * @throws InvalidArgumentException if the input was not strictly integers.
  *
  * @return mixed the row in the database, if it exists.
  */
  function get_overview_klandringer($studentid, $team_id) {
    global $db;
    assert($db !== null, "DB is not ready for get_overview_klandringer.");

    if (!is_numeric($studentid) || !is_numeric($team_id)) {
        throw new InvalidArgumentException("get_overview_klandringer function only accepts integers. Input was: $studentid (".gettype($studentid).") and $team_id (".gettype($team_id).")");
    }

    $your_klandringer = [];
    $their_klandringer = [];
    $ids = [];

    // get all of your klandringer
    $sql = "SELECT * FROM `klandring` 
            WHERE ((team = $team_id) AND (`from` = $studentid AND verdict = 0))";

    $result = $db->query($sql);

    while ($row = $result->fetch_assoc()) {
        $your_klandringer[] = $row;
    }
    foreach ($your_klandringer as $klandring) {
        $ids[] = intval($klandring["from"]);
        $ids[] = intval($klandring["to"]);
    }

    // get all of their klandringer
    $sql = "SELECT `from` FROM `klandring` 
            WHERE ((team = $team_id) AND ((`from` != $studentid) AND (verdict = 0)))";

    $result = $db->query($sql);

    while ($row = $result->fetch_assoc()) {
        $their_klandringer[] = $row;
    }

    foreach ($their_klandringer as $klandring) {
        $ids[] = intval($klandring["from"]);
    }

    $users = [];
    if (count($ids)) {
        // todo: solve in one go, instead of 3.
        $ids = array_values(array_unique($ids));
        sort($ids);

        $users = array_combine($ids, get_user_infos_arr($ids));
    }

    return array("yours"  => $your_klandringer, 
                 "theirs" => $their_klandringer,
                 "users"  => $users);
}

 /**
  * Fetches a list of unresolved klandringer relating to the user.
  *
  * @var int $studentid corresponding to the user's id.
  *
  * @throws InvalidArgumentException if the input was not strictly integers.
  *
  * @return mixed the row in the database, if it exists.
  */
function get_user_klandringer($studentid) {
    global $db;
    assert($db !== null, "DB is not ready for get_overview_klandringer.");

    if (!is_numeric($studentid)) {
        throw new InvalidArgumentException("get_overview_klandringer function only accepts integers. Input was: $studentid (".gettype($studentid).")");
    }

    $sql = "SELECT * FROM `klandring` 
    WHERE ((`to` = $studentid AND verdict != 0) 
        OR (`from` = $studentid))";

    $result = $db->query($sql);

    $klandringer = [];
    while ($row = $result->fetch_assoc()) {
        $klandringer[] = $row;
    }

    return $klandringer;
}

 /**
  * Fetches a team from the database.
  *
  * @var string $slug corresponding to the team's slug in the database.
  *
  * @return mixed the row in the database, if it exists.
  */
function get_team_from_slug($slug) {
    global $db;
    assert($db !== null, "DB is not ready for get_team_from_slug.");

    $slug = $db->real_escape_string($slug);
    $sql = "SELECT * FROM team WHERE `slug` = '$slug' LIMIT 1";
    $result = $db->query($sql);
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $result->free();

    return $row;
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
            throw new InvalidArgumentException("get_user_infos function only accepts integers. Input was: $id (".gettype($id).")");
        }
        $manifest .= $id.",";
    }
    $manifest = substr($manifest, 0, -1); // remove trailing comma

    $sql = "SELECT id,auid,`name`,year,email,phone FROM student WHERE id IN ($manifest) LIMIT " . count($ids);
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

function get_user_role($student_id, $team_id) {
    global $db;
    assert($db !== null, "DB is not ready for get_students_of_team.");

    if (!is_numeric($student_id) || !is_numeric($team_id)) {
        throw new InvalidArgumentException("get_user_role function only accepts integers. Input was: $student_id (".gettype($student_id)."), $team_id (".gettype($team_id).").");
    }
    $applicant = ROLE_APPLICANT;
    $sql = "SELECT `roleid` FROM `teamstudent` WHERE `teamid` = $team_id AND `studentid` = $student_id";
    
    $result = $db->query($sql);
    if (!$result) {
        return ROLE_APPLICANT;
    }
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $result->free();

    return $row["roleid"];
}

function get_current_user_role($team_id) {
    foreach ($_SESSION["teams"] as $key => $value) {
        if ($value["id"] === $team_id) {
            return $value["roleid"];
        }
    }
    return ROLE_APPLICANT;
}
?>