<?php
session_start();

require("config.php");

if (isset($_GET["code"]) && isset($_GET["username"])) {

    $query_params = http_build_query(
        array(
            "client_id"     => OAUTH_CLIENT_ID,
            // "client_secret" => OAUTH_CLEINT_SECRET,
            "redirect_uri"  => (isset($_SERVER["HTTPS"]) ? "https" : "http")."://$_SERVER[HTTP_HOST]/$_GET[username]/",
            "grant_type"    => "authorization_code",
            "username"      => $_GET["username"],
            "code"          => $_GET["code"]
        )
    );

    $url = OAUTH_PROVIDER . "token?$query_params";

    $result = file_get_contents($url);

    if ($result !== FALSE) { 
        if(strpos($http_response_header[0], "200"))
        {
            $db = new mysqli(MYSQL_PROVIDER, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
            $db->set_charset("utf8");

            $auid = $db->real_escape_string($_GET["username"]);
            $sql = "SELECT id, auid, name FROM student WHERE `auid` = '$auid' LIMIT 1";
            $result = $db->query($sql);
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $result->free();

            if($row) { // A person that is in the database.

                // Get the teams they are involved in.
                $sql = "SELECT A.*, B.roleid FROM team A
                        INNER JOIN teamstudent B ON A.id = B.teamid
                        WHERE B.studentid = $row[id]";
                $result = $db->query($sql);
                $teams = [];
                while($row2 = $result->fetch_array(MYSQLI_ASSOC)) {
                    $teams[] = $row2;
                }
                $result->free();

                // save it to the session so we don't need to look it up later.
                $_SESSION["student-id"]    = $row["id"];
                $_SESSION["student-name"]  = $row["name"];
                $_SESSION["auid"]          = $_GET["username"];
                $_SESSION["oauth-code"]    = $_GET["code"];
                $_SESSION["oauth-success"] = $_GET["username"] === $row["auid"];
                $_SESSION["teams"]         = $teams;
                header("Location: /$_GET[username]");
            } else {
                header("Location: /");
            }

            $db->close();
        }
    }
}
?>