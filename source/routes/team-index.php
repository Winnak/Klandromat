<?php
$sql = "SELECT * FROM `klandring` WHERE ((team = $arguments[id]) AND (verdict != 0))";
$result = $db->query($sql);

$klandringer = [];
$ids = [];
$users = [];

while ($row = $result->fetch_assoc()) {
    $klandringer[] = $row;
}
$result->free();

if (count($klandringer)) {
    foreach ($klandringer as $klandring) {
        $ids[] = intval($klandring["from"]);
        $ids[] = intval($klandring["to"]);
    }

    // todo: solve in one go, instead of 3.
    $ids = array_values(array_unique($ids));
    sort($ids);

    $users = array_combine($ids, get_user_infos_arr($ids));
}

$losings = 0;
$debt = 0;
?>
<div class="panel panel-default">
    <div class="panel-heading clearfix">
        <div class="pull-left panel-title" style="padding-top:7.5px;">Klandringshistorik for <?= $arguments["name"] ?></div>
        <div class="btn-group pull-right">
        <?php if($arguments["roleid"] == ROLE_ADMIN) {
            echo "<a href=\"$arguments[slug]/admin\" class=\"btn btn-default\" style=\"\"><i class=\"glyphicon glyphicon-pencil\"> </i> Administrerer</a>";
        }?>
        </div>
    </div>
    <div class="panel-body">
        <p>Holdet har indtjent <i id="total">0</i> kr.</p>
        <p>Holdet skylder <i id="debt">0</i> kr.</p>
        <p>Holdet har brugt for <i id="spent">TODO</i> kr.</p>
    </div>
<?php if(count($klandringer) > 0) : ?>
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
                <td><?= $row["paid"] ? "betalt" : "ikke betalt" ?></td>
            </tr>
<?php
/* 
 * Calculate the total debt.
 */
switch ($row["verdict"]) {
    case 3:
        $losings += $team["value"];
        $debt += $team["value"] * (1 - $row["paid"]);
    case 1:
    case 2:
        $losings += $team["value"];
        $debt += $team["value"] * (1 - $row["paid"]);
        break;
}
?>
<?php endforeach ?>
        </tbody>
    </table>
    <script>document.getElementById("debt").innerHTML = <?= $debt ?>;</script>
    <script>document.getElementById("total").innerHTML = <?= $losings ?>;</script>
<?php else: ?>
    <p style="padding: 20px">Ingen klandringer. <a href="/klandring/create">Så se at få oprettet nogle</a></p>
<?php endif ?>
</div>