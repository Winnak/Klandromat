<?php
if($_SERVER['REQUEST_METHOD'] === "POST") {
    // just to be sure, we verify that they really are admins of the team.
    if ($arguments["roleid"] != ROLE_TREASURER) {
        header("Status: 300", true, 300);
        die();
    }

    $data = json_decode(file_get_contents('php://input'), true);

    $db->autocommit(FALSE); // ensure all changes go through.

    foreach ($data as $id => $change) {
        $db->query("UPDATE klandring
                    SET verdict=$change[verdict], paid=$change[paid]
                    WHERE id=$id");
    }

    if (!$db->commit()) {
        header("Status: 500", true, 500);
    }
    echo "all is well";
    die();
}

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

function get_checkbox($klandring_row, $prefix, $winner) {
    return "<input type=\"checkbox\" id=\"" . $prefix . "-$klandring_row[id]\" " . (($klandring_row["verdict"] & $winner) == $winner ? "checked>" : ">");
}

$base = "var unchanged={";
?>
<div class="panel panel-default">
    <div class="panel-heading clearfix">
        <div class="pull-left panel-title" style="padding-top:7.5px;">Håndtering af <?= $arguments["name"] ?></div>
        <div class="btn-group pull-right">
        <?php if($arguments["roleid"] == ROLE_TREASURER): ?>
            <a href="admin-klandring" class="btn btn-default"><i class="glyphicon glyphicon-pencil"> </i> Tilføj klandring</a>
            <a href="admin-snaps" class="btn btn-primary"><i class="glyphicon glyphicon-check"> </i> Afgør SNAPS</a>
        <?php endif; ?>
        </div>
    </div>
<?php if(count($klandringer) > 0) : ?>
    <table class='table table-hover'>
        <thead>
            <tr>
                <th>ID</th>
                <th>Dato</th>
                <th>Title</th>
                <th>Klandrer</th>
                <th>Klandret</th>
                <th>Betalt</th>
            </tr>
        </thead>
        <tbody id="klandringer">
<?php foreach ($klandringer as $row) : ?>
            <tr id="r-<?= $row["id"] ?>">
                <td><a href="/klandring/<?= $row["id"]?>"><?= $row["id"]?></a></td>
                <td><div id="d-<?= $row["id"] /* TODO: edit options */?>"><?= $row["verdictdate"] ?></div></td>
                <td><?= $row["title"] ?></td>
                <td><?= get_checkbox($row, "va", WINNER_KLANDRER) . " " . $users[$row["from"]]["name"] ?></td>
                <td><?= get_checkbox($row, "vb", WINNER_KLANDRET) . " " . $users[$row["to"]]["name"] ?></td>
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
    <script>
<?php 
echo substr($base,0,-1)."};\n";
echo "var slug=\"$arguments[slug]\";"; 
?>

    </script>
    <script src="/static/team-admin.js"></script>
<?php else: ?>
    <p style="padding: 20px">Ingen klandringer. <a href="/klandring/create">Så se at få oprettet nogle</a></p>
<?php endif ?>
</div>
<br>
<br>