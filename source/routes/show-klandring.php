<?php if($arguements["auid"] == $_SESSION["auid"]) : ?>
<?php endif; ?>
<?php
$paths = preg_split('/\//', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), -1, PREG_SPLIT_NO_EMPTY);
$id = $arguements["id"];
$sql = "SELECT * FROM `klandring` WHERE (`id` = ". $paths[2] .") LIMIT 1";

$result = $db->query($sql);
    if ($result) {
        $row = $result->fetch_assoc();
        $toID = $row['to'];
        $fromID = $row['from'];
        $title = $row['title'];
        $desc = $row['description'];


        switch ($row['verdict']) {
        case 0:
            $score = 'VS';
            break;
        case 1:
            $score = '1-0';
            break;
        case 2:
            $score = '0-1';
            break;
        case 3:
            $score = '1-1';
            break;
        }

    } else {
        echo 'fuck';
    }
echo 
'<br/><div class="jumbotron jumbotron-fluid text-center">
  <p> Klandring:</p>
  <h1>'. $title .'</h1>
  <div class="container">
  <h3 class="col col-md-12 col-sm-12"><span class="col col-md-4 col-sm-12 col-xs-12">' . idToName($fromID, $db) . '</span><span class="col col-md-4 col-sm-12 col-xs-12"> ' . $score . '</span><span class="col col-md-4 col-sm-12 col-xs-12">'  . idToName($toID, $db) . '</span></h3>
  </div>
  <br/>
  <div class="well">
  <p>' . $desc . '</p>
  </div>
</div>';
$result->free();
?>


<br>
