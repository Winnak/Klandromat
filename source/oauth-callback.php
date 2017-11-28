<?php
session_start();

require("config.php");
require("database-helper.php");

if (isset($_GET["code"]) && isset($_GET["username"])) {

    $redirect = (isset($_SERVER["HTTPS"]) ? "https" : "http")."://$_SERVER[HTTP_HOST]";

    if (isset($_GET["from"])) {
        // FIXME: why do we get the 'from' query param twice when debugging?
        $redirect .= $_GET["from"];
    }

    $query_params = http_build_query(
        array(
            "client_id"     => OAUTH_CLIENT_ID,
            // "client_secret" => OAUTH_CLEINT_SECRET,
            "redirect_uri"  => $redirect,
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

            $row = get_user_from_auid_or_year($_GET["username"]);

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
                $_SESSION["auid"]          = $row["auid"];
                $_SESSION["oauth-code"]    = $_GET["code"];
                $_SESSION["oauth-success"] = 1;
                $_SESSION["teams"]         = $teams;
            } else {
                $_SESSION["auid"]          = $_GET["username"];
                $_SESSION["oauth-code"]    = $_GET["code"];
                $_SESSION["oauth-success"] = 2;
                $_SESSION["teams"]         = [];
            }
            $db->close();
            header("Location: $redirect");
        }
    }
}
?>