<?php
$title = $arguments['title'];
$desc = $arguments['description'];

$involved_parties = get_user_infos(intval($arguments['from']), intval($arguments['to']));
$from = $to = null;

if (count($involved_parties) === 1) {
    $from = $involved_parties[0];
    $to = $involved_parties[0];
} else if ($arguments["from"] == $involved_parties[0]["id"]) {
    $from = $involved_parties[0];
    $to = $involved_parties[1];
} else {
    $from = $involved_parties[1];
    $to = $involved_parties[0];
}

switch ($arguments['verdict']) {
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
?>
<div class="jumbotron jumbotron-fluid text-center">
    <p>Klandring:</p>
    <h1><?= $title ?></h1>
    <div class="container">
        <h3 class="col col-md-12 col-sm-12">
            <span class="col col-md-4 col-sm-12 col-xs-12"><?= $from["name"] ?></span>
            <span class="col col-md-4 col-sm-12 col-xs-12"><?= $score ?></span>
            <span class="col col-md-4 col-sm-12 col-xs-12"><?= $to["name"] ?></span>
        </h3>
    </div>
    <br/>
    <div class="well">
        <p><?= $desc ?></p>
    </div>
</div>
<br>