<?php
$sql = "SELECT * FROM `klandring` WHERE (verdict != 0)";
$result = $db->query($sql);

$klandringer = [];
while ($row = $result->fetch_assoc()) {
    $klandringer[] = $row;
}

$ids = [];
foreach ($klandringer as $klandring) {
    $ids[] = intval($klandring["from"]);
    $ids[] = intval($klandring["to"]);
}

// todo: solve in one go, instead of 3.
$ids = array_values(array_unique($ids));
sort($ids);

$users = array_combine($ids, get_user_infos_arr($ids));

$losings = 0;
?>
<?php if($result->num_rows > 0) : ?>
<div class="panel panel-default">
    <div class="panel-heading">Klandringshistorik for "LAV HOLD"</div>
    <table class='table table-hover'>
        <thead>
            <tr>
                <th>Dato</th>
                <th>Titel</th>
                <th>Klandrer</th>
                <th>Klandret</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
<?php foreach ($klandringer as $row) : ?>
            <tr onclick="window.document.location='/klandring/<?= $row["id"]?>'">
                <td><?= $row["verdictdate"]?></td>
                <td><?= $row["title"]?></td>
<?php switch($row["verdict"]): ?>
<?php default: ?>
<?php case 0: ?>
                <td><i><?= $users[$row["from"]]["name"] ?></i></td>
                <td><i><?= $users[$row["to"]]["name"] ?></i></td>
<?php break; ?>
<?php case 1: ?>
                <td><div class="win"><?= $users[$row["from"]]["name"] ?></div></td>
                <td><div class="loss"><?= $users[$row["to"]]["name"] ?></div></td>
<?php break; ?>
<?php case 2: ?>
                <td><div class="loss"><?= $users[$row["from"]]["name"] ?></div></td>
                <td><div class="win"><?= $users[$row["to"]]["name"] ?></div></td>
<?php break; ?>
<?php case 3: ?>
                <td><div class="tie"><?= $users[$row["from"]]["name"] ?></div></td>
                <td><div class="tie"><?= $users[$row["to"]]["name"] ?></div></td>
<?php break; ?>
<?php endswitch; ?>
                <td><?= $row["verdict"] == 0 ? "ikke afgjort" : 
                        ($row["paid"] ? "betalt" : "ikke betalt") ?></td>
            </tr>
<?php endforeach ?>
        </tbody>
    </table>
<?php else: ?>
    <a href="/klandring/create">Ingen klandringer, det må du gøre noget ved!</a>
<?php endif; ?>
</div>