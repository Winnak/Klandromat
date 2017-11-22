<h3>Upkommende Klandringer</h3>
<h4 style="line-height:2"><a href="/klandring/create"><i class="glyphicon glyphicon-plus"></i> Ny klandring</a></h4>

<?php foreach($_SESSION["teams"] as $team) : ?>

<div class="panel panel-default">
    <div class="panel-heading"><?= $team["name"] ?></div>
<?php
global $db;

$your_klandringer = [];
$their_klandringer = [];
$ids = [];

$id = $_SESSION["student-id"];

// get all of your klandringer
$sql = "SELECT * FROM `klandring` 
        WHERE ((team = $team[id]) AND (`from` = $id AND verdict = 0))";

$result = $db->query($sql);

while ($row = $result->fetch_assoc()) {
    $your_klandringer[] = $row;
}
$result->free();

foreach ($your_klandringer as $klandring) {
    $ids[] = intval($klandring["from"]);
    $ids[] = intval($klandring["to"]);
}

// get all of their klandringer
$sql = "SELECT `from` FROM `klandring` 
        WHERE ((team = $team[id]) AND ((`from` != $id) AND (verdict = 0)))";

$result = $db->query($sql);

while ($row = $result->fetch_assoc()) {
    $their_klandringer[] = $row;
}
$result->free();

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

$losings = 0;
?>
<?php if(count($your_klandringer) + count($their_klandringer) > 0) : ?>
    <table class='table table-hover'>
        <thead>
            <tr>
                <th>Titel</th>
                <th>Klandrer</th>
                <th>Klandret</th>
            </tr>
        </thead>
        <tbody>
<?php foreach ($your_klandringer as $row) : ?>
            <tr onclick="window.document.location='/klandring/<?= $row["id"]?>'">
                <td><?= $row["title"]?></td>
                <td><i><?= $users[$row["from"]]["name"] ?></i></td>
                <td><?= $users[$row["to"]]["name"] ?></td>
            </tr>
<?php endforeach ?>
<?php foreach ($their_klandringer as $row) : ?>
            <tr>
                <td></td>
                <td><?= $users[$row["from"]]["name"] ?></td>
                <td>XXXXX HEMMELIGT XXXXX</td>
            </tr>
<?php endforeach ?>
        </tbody>
    </table>
<?php else: ?>
    <p style="padding: 20px">Ingen klandringer. <a href="/klandring/create">Så se at få oprettet nogle</a></p>
<?php endif ?>
</div>
<?php endforeach ?>
