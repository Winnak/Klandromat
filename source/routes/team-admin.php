<?php
$sql = "SELECT * FROM `klandring` WHERE team = $arguments[id]";
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

$base = "var unchanged={";

function a_checkbox($id, $checked) {
    if ($checked) {
        return "<input type=\"checkbox\" id=\"va-$id\" checked>";
    } else {
        return "<input type=\"checkbox\" id=\"va-$id\">";
    }
}
function b_checkbox($id, $checked) {
    if ($checked) {
        return "<input type=\"checkbox\" id=\"vb-$id\" checked>";
    } else {
        return "<input type=\"checkbox\" id=\"vb-$id\">";
    }
}
?>
<div class="panel panel-default">
    <div class="panel-heading">Håndtering af <?= $arguments["name"] ?></div>
<?php if(count($klandringer) > 0) : ?>
    <table class='table table-hover'>
        <thead>
            <tr>
                <th>ID</th>
                <th>Dato</th>
                <th>Klandrer</th>
                <th>Klandret</th>
                <th>Betalt</th>
            </tr>
        </thead>
        <tbody id="klandringer">
<?php foreach ($klandringer as $row) : ?>
            <tr id="r-<?= $row["id"] ?>">
                <td><?= $row["id"]?></td>
                <td><div id="d-<?= $row["id"] /* TODO: edit options */?>"><?= $row["verdictdate"] ?></div></td>
<?php switch($row["verdict"]): ?>
<?php default: ?>
<?php case 0: ?>
                <td><i><?= $users[$row["from"]]["name"] ?></i><?= a_checkbox($row["id"], false) ?></td>
                <td><i><?= $users[$row["to"]]["name"] ?></i><?= b_checkbox($row["id"], false) ?></td>
<?php break; ?>
<?php case 1: ?>
                <td><div class="win"><?= $users[$row["from"]]["name"] ?></div><?= a_checkbox($row["id"], true) ?></td>
                <td><div class="loss"><?= $users[$row["to"]]["name"] ?></div><?= b_checkbox($row["id"], false) ?></td>
<?php break; ?>
<?php case 2: ?>
                <td><div class="loss"><?= $users[$row["from"]]["name"] ?></div><?= a_checkbox($row["id"], false) ?></td>
                <td><div class="win"><?= $users[$row["to"]]["name"] ?></div><?= b_checkbox($row["id"], true) ?></td>
<?php break; ?>
<?php case 3: ?>
                <td><div class="tie"><?= $users[$row["from"]]["name"] ?></div><?= a_checkbox($row["id"], true) ?></td>
                <td><div class="tie"><?= $users[$row["to"]]["name"] ?></div><?= b_checkbox($row["id"], true) ?></td>
<?php break; ?>
<?php endswitch; ?>
                <td><input type="checkbox" id="p-<?= $row["id"] ?>" <?= $row["paid"] ? "checked" : "" ?>></input></td>
            </tr>
<?php
$base .= "$row[id]:{verdict:$row[verdict],verdictdate:\"$row[verdictdate]\",paid:$row[paid]},"
?>
<?php endforeach ?>
        </tbody>
    </table>
    <div class="panel-body">
        <b>Ændringer:</b>
        <ul id="summary"></ul>
        <div class="btn btn-primary" id="btn-update" disabled>Tjek ændringer</div>
        <div class="btn btn-success" id="btn-submit" disabled>Submit ændringer</div>
    </div>
    <script><?= substr($base,0,-1)."};" ?></script>
    <script src="/static/team-admin.js"></script>
<?php else: ?>
    <p style="padding: 20px">Ingen klandringer. <a href="/klandring/create">Så se at få oprettet nogle</a></p>
<?php endif ?>
</div>