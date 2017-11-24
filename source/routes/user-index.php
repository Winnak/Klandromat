<?php
$user = get_user_from_auid($resources["user"]);
?>

<h3><?= $user["name"] ?></h3>
<i class="glyphicon glyphicon-barcode"></i> AU-ID: <?= $user["auid"] ?>.<br>
<i class="glyphicon glyphicon-envelope"></i> E-mail: <?= $user["email"] ?>.<br>
<i class="glyphicon glyphicon-earphone"></i> Telefon: <?= ((sizeof($user["phone"]) !== 0) ? sprintf('+45 %04d %04d', $user["phone"] / 10000, $user["phone"] % 10000) : "Telefon nummer ikke sat")?>.<br>
<i class="glyphicon glyphicon-credit-card"></i> Årskort: <?= $user["year"]; ?>.<br>

<?php if($user["auid"] == $_SESSION["auid"]) : ?>
<p>
    <a href="/<?= $user["auid"]; ?>/edit"><i class="glyphicon glyphicon-pencil"></i> Ret brugeroplysninger.</a><br>
    <a href="/logout"><i class="glyphicon glyphicon-log-out"></i> Log ud.</a>
</p>
<?php endif; ?>

<div class="panel panel-default">
    <div class="panel-heading">Dine klandringer</div>
    <div class="panel-body">
        <div id="debt"></div>
    </div>
<?php

$sql = "SELECT * FROM `klandring` 
        WHERE ((`to` = $user[id] AND verdict != 0) 
            OR (`from` = $user[id]))";

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

$losings = array();
foreach ($_SESSION["teams"] as $key => $value) {
    $losings[$value["id"]] = 0;
}
?>
<?php if($result->num_rows > 0) : ?>
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
<?php
/* 
 * Calculate the total debt.
 */
if ($row["verdict"] == 3) {
    $losings[$row["team"]] += 5 * (1 - $row["paid"]);
} elseif ($row["to"] == $user["id"]) {
    if ($row["verdict"] == 1) {
        $losings[$row["team"]] += 5 * (1 - $row["paid"]);
    }
} elseif ($row["from"] == $user["id"]) {
    if ($row["verdict"] == 2) {
        $losings[$row["team"]] += 5 * (1 - $row["paid"]);
    }
}
?>
<?php endforeach ?>
        </tbody>
    </table>
    <script>document.getElementById("debt").innerHTML = "<?php
    // TODO: Could this be cleaner?
    foreach ($losings as $teamid => $amount) {
        $team = "";
        foreach ($_SESSION["teams"] as $key => $value) {
            if ($teamid == $value["id"]) {
                $team = $value["name"];
                break;
            }
        }

        echo "Du skylder $team: <i>$amount</i> kr.<br>\\n";
    }
    ?>";</script>
<?php endif ?>
</div>