<?php $klandring = get_klandring_from_id($matches["kid"]); ?>
<?php if($_SERVER['REQUEST_METHOD'] === "POST") : ?>
<?php
if (get_current_user_role($klandring["team"]) != ROLE_TREASURER) {
    die();
}

$verdict = intval($_POST["verdict"]);

if ($verdict < 0|| !is_numeric($_POST["verdict"]))
{
	die();
}

$sql = "UPDATE klandring SET `verdict`=$verdict, `verdictdate`=CURDATE() WHERE `id`=$klandring[id]";

$result = $db->query($sql);
?>
<?php else: ?>
<?php
$involved_parties = get_user_infos(intval($klandring["from"]), intval($klandring["to"]));
$from = $to = null;

if (count($involved_parties) === 1) {
    $from = $involved_parties[0];
    $to = $involved_parties[0];
} else if ($klandring["from"] == $involved_parties[0]["id"]) {
    $from = $involved_parties[0];
    $to = $involved_parties[1];
} else {
    $from = $involved_parties[1];
    $to = $involved_parties[0];
}

switch ($klandring['verdict']) {
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

// <input type=\"checkbox\" id=\"" . $prefix . "-$klandring_row[id]\" " . (($klandring_row["verdict"] & $winner) == $winner ? "checked>" : ">");

$is_treasurer = get_current_user_role($klandring["team"]) == ROLE_TREASURER;
?>
<div class="jumbotron jumbotron-fluid text-center">
    <p>Klandring:</p>
    <h1><?= $klandring["title"] ?></h1>
    <div class="container">
        <h3 class="col col-md-12 col-sm-12">
            <span class="col col-md-4 col-sm-12 col-xs-12"><?= $from["name"] ?>
            <?php if ($is_treasurer): ?>
            <br><input type="checkbox" id="va" <?php if (($klandring["verdict"] & WINNER_KLANDRER) == WINNER_KLANDRER) { echo "checked"; } ?>></input>
            <?php endif ?>
            </span>
            <span class="col col-md-4 col-sm-12 col-xs-12"><?= $score ?></span>
            <span class="col col-md-4 col-sm-12 col-xs-12"><?= $to["name"] ?>
            <?php if ($is_treasurer): ?>
            <br><input type="checkbox" id="vb" <?php if (($klandring["verdict"] & WINNER_KLANDRET) == WINNER_KLANDRET) { echo "checked"; } ?>></input>
            <?php endif ?>
            </span>
        </h3>
    </div>
    <br/>
    <div class="well">
        <p><?= $klandring["description"] ?></p>
    </div>
<?php if($is_treasurer): ?>
<script src="/static/klandring-admin.js"></script>
<?php
$sql = "SELECT COALESCE(
            (SELECT id FROM klandring
                WHERE (verdict = 0)
                  AND (creationdate > TIMESTAMP('$klandring[creationdate]'))
                  AND (team = $klandring[team])
                ORDER BY creationdate
                LIMIT 1),
            (SELECT id FROM klandring
                WHERE (verdict = 0)
                  AND (team = $klandring[team])
                ORDER BY creationdate
                LIMIT 1)) next_id";
$result = $db->query($sql);
$row = $result->fetch_array(MYSQLI_ASSOC);
$result->free();
?>
<a href="<?= $row["next_id"]?>" class="btn btn-default">Gå til næste uafgjorte klandring <i class="glyphicon glyphicon-forward"> </i></a>
<?php endif ?>
</div>
<br>
<?php endif ?>