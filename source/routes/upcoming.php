<h3>Upkommende Klandringer</h3>
<h4 style="line-height:2"><a href="/klandring/create"><i class="glyphicon glyphicon-plus"></i> Ny klandring</a></h4>

<?php foreach($_SESSION["teams"] as $team) : ?>

<div class="panel panel-default">
    <div class="panel-heading"><?= $team["name"] ?></div>
<?php
global $db;

$id = $_SESSION["student-id"];

$result = get_overview_klandringer($id, $team["id"]);
$your_klandringer = $result["yours"];
$their_klandringer = $result["theirs"];
$users = $result["users"];


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
