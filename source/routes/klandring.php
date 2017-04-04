<?php
function can_edit($args) {
    return ($_SESSION["student-id"] === $args["from"])
        && ($args["verdict"] == 0);
}
function can_see($args) {
    return can_edit($args) // If we can edit it
        || $args["verdict"] > 0; // or it has been decided.
}
?>
<?php if($_SERVER['REQUEST_METHOD'] === "POST") : // User edits this klandring ?>
<?php 
if(!can_edit($arguements)) {
    echo "WHAT THE FUCK DID YOU DO!";
    die();
}
$db = new mysqli(MYSQL_PROVIDER, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
$db->set_charset("utf8");

$title       = $db->real_escape_string($_POST["title"]);
$to          = $db->real_escape_string($_POST["klandret"]);
$description = $db->real_escape_string($_POST["description"]);
$sql = "UPDATE klandring SET title = '$title', description = '$description', `to` = '$to' WHERE id = " . $arguements["id"] . ";";

$db->query($sql);
$db->close();
header("Location: /klandring/" .  $arguements["id"]);
?>
<?php else: // User is just view this klanndring ?>
<div class="panel panel-default">
    <div class="panel-heading">Klandring</div>
    <div class="panel-body">
<?php if(can_edit($arguements)) : ?>
<form action="/klandring/<?php echo $arguements["id"]; ?>" id="edit-klandring" method="POST">
    <div class="form-group">
        <div class="input-group">
            <span class="input-group-addon">Titel</span>
            <input type="text" class="form-control" name="title" value="<?php echo $arguements["title"]; ?>" required>
        </div>
        <br>
        <div class="input-group">
            <select name="klandret" required>
                <option value="">Vælg person</option>
<?php
$db = new mysqli(MYSQL_PROVIDER, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
$db->set_charset("utf8");
$sql = "SELECT id, name FROM student WHERE 1";
$result = $db->query($sql);
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "<option value='" . $row["id"] . ($row["id"] === $arguements["to"] ? "' selected" : "'") . ">" . $row["name"] . "</option>";
    }
}
$result->free();
$db->close();
?>
            </select>
        </div>
        <br>
        <div class="input-group">
            <textarea class="form-control" rows="3" name="description"><?php echo $arguements["description"]; ?></textarea>
        </div>
        <br>
        <div class="input-group">
            <input type="submit" class="btn btn-default" value="Edit" />
        </div>
    </div>
</form>
<?php else : ?>
<?php if(can_see($arguements)) : ?>
<p>Titel: <?php echo $arguements["title"]; ?></p>
<p>Afgørelse: <?php 
switch ($arguements["verdict"]) {
    case '1':
        echo "Klandrer vandt (" . $arguements["verdictdate"] . ")";
        break;
    case '2':
        echo "Klandret vandt (" . $arguements["verdictdate"] . ")";
        break;
    case '3':
        echo "uafgjort (" . $arguements["verdictdate"] . ")";
        break;
    default:
    case '0':
        echo "Endnu ikke afgjort";
        break;
}
?></p>
<p>Klandrer: <?php
$db = new mysqli(MYSQL_PROVIDER, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
$db->set_charset("utf8");
$sql = "SELECT name, auid FROM student WHERE id = " . $arguements["from"];
$result = $db->query($sql);
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "<a href='/" . $row["auid"] . "'>" . $row["name"] . "</a>";
    }
}
?></p>
<p>Klandret: <?php 
$sql = "SELECT name, auid FROM student WHERE id = " . $arguements["to"];
$result = $db->query($sql);
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "<a href='/" . $row["auid"] . "'>" . $row["name"] . "</a>";
    }
}
$result->free();
$db->close();
?></p>
<p>Beskrivelse: <?php echo $arguements["description"]; ?></p>
<?php else: ?>
Hemmelig klandring, endnu ikke afgjort.
<br>
<br>
<a href="/<?php echo $_SESSION["auid"]; ?>">Walk and piss</a>
<?php endif; ?>
<?php endif; ?>
    </div>
</div>
<?php endif; ?>