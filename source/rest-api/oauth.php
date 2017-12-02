<?php
require_once("rest-helper.php");

if (isset($_GET["code"]) && isset($_GET["username"])) {
    $query_params = http_build_query(
        array(
            "client_id"     => OAUTH_CLIENT_ID,
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
            $row = get_user_from_auid($_GET["username"]);
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

                $sql = "UPDATE `student` SET `apitoken`=X'$_GET[code]' WHERE id=$row[id]";
                $db->query($sql);

                // temp fix.
                $row["apitoken"] = "";

                $response = array(
                    "auid" => "$row[auid]",
                    "token" => "$_GET[code]",

                    // NOTE: this is probably TMI, we should probably just return the above.
                    "userinfo" => $row,
                    "teams" => $teams,
                );

                echo json_encode($response);
            } else {
                raise_error(401);
            }
        }
    }
} else {
    raise_error(400);
}
$db->close();
?>