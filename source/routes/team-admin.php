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

$base = "var unchanged={";

function a_checkbox($row, $users, $checked) {
    $name = $users[$row["from"]]["name"];
    if ($checked) {
        return "<input type=\"checkbox\" id=\"va-$row[id]\" checked> $name";
    } else {
        return "<input type=\"checkbox\" id=\"va-$row[id]\"> $name";
    }
}
function b_checkbox($row, $users, $checked) {
    $name = $users[$row["to"]]["name"];
    if ($checked) {
        return "<input type=\"checkbox\" id=\"vb-$row[id]\" checked> $name";
    } else {
        return "<input type=\"checkbox\" id=\"vb-$row[id]\"> $name";
    }
}
?>
<div class="panel panel-default">
    <div class="panel-heading clearfix">
        <div class="pull-left panel-title" style="padding-top:7.5px;">Håndtering af <?= $arguments["name"] ?></div>
        <div class="btn-group pull-right">
        <?php if($arguments["roleid"] == ROLE_TREASURER) {
            echo "<a href=\"admin-klandring\" class=\"btn btn-default\" style=\"\"><i class=\"glyphicon glyphicon-pencil\"> </i> Tilføj klandring</a>";
        }?>
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
<?php switch($row["verdict"]): ?>
<?php default: ?>
<?php case 0: ?>
                <td><?= a_checkbox($row, $users, false) ?></td>
                <td><?= b_checkbox($row, $users, false) ?></td>
<?php break; ?>
<?php case 1: ?>
                <td><?= a_checkbox($row, $users, true) ?></td>
                <td><?= b_checkbox($row, $users, false) ?></td>
<?php break; ?>
<?php case 2: ?>
                <td><?= a_checkbox($row, $users, false) ?></td>
                <td><?= b_checkbox($row, $users, true) ?></td>
<?php break; ?>
<?php case 3: ?>
                <td><?= a_checkbox($row, $users, true) ?></td>
                <td><?= b_checkbox($row, $users, true) ?></td>
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