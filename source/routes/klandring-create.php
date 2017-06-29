<?php $id = $_SESSION["student-id"] ?>
<?php if($_SERVER['REQUEST_METHOD'] === "POST") : ?>
<?php
$title = $db->real_escape_string($_POST["title"]);
$desc = $db->real_escape_string($_POST["desc"]);
$to = intval($_POST["toid"]);
$team = intval($_POST["team"]);

if ($to < 0 || !is_numeric($_POST["toid"]) || !is_numeric($_POST["team"])) {
	header("Location: /klandring/create");
	die();
}

$sql = "INSERT INTO klandring (`title`, `description`, `from`, `to`, `team`) 
		VALUES ('$title', '$desc', $id, $to, $team);";

$result = $db->query($sql);
if ($result) {
	header("Location: /");
}
else {
	header("Location: /klandring/create");
}
?>
<?php else: /* GET */ ?>
<form class="col col-md-4" action="/klandring/create" id="edit-user" method="POST">
	<div class="form-group">
		<label><?= $_SESSION["student-name"] ?></b> (<?= $_SESSION["auid"] ?>)</label>
	</div>
	<div class="form-group">
		<label for="select-team">På holdet</label>
		<select name="team" class="form-control" id="select-team">
<?php
// FIXME why did <? foreach ( ...) : ?'> not work?
foreach ($_SESSION["teams"] as $team) {
	echo("<option value='$team[id]'>$team[name]</option>");
}
?>
		</select>
	</div>
	<div class="form-group">
		<label for="select-klandret">Klandrer hermed</label>
		<select name="toid" class="form-control" id="select-klandret">
		</select>
	</div>
	<div class="form-group">
		<label for="selectTitle">for</label>
		<input type="text" class="form-control" name="title" placeholder="Indtast titel" id="selectTitle" required>
	</div>
	<div class="form-group">
		<label for="selectElab">med uddybning</label>
		<textarea class="form-control" id="selectElab" name="desc" placeholder="Indtast beskrivelse" rows="3" required></textarea>
	</div>

	<div class="input-group">
		<input type="submit" class="btn btn-default" value="Klandr!" />
	</div>
	</div>
</form>
<script>

<?php
$manifest = "var teams = {";
foreach ($_SESSION["teams"] as $team) {
	$sql = "SELECT DISTINCT A.* FROM student A
		INNER JOIN teamstudent B ON A.id = B.studentid
		WHERE B.teamid = $team[id]";

	$result = $db->query($sql);

	$manifest .= "$team[id]: \"";
	while ($row = $result->fetch_assoc()) {
		$manifest .= "<option value='$row[id]'>$row[name]</option>";
	}
	$manifest .= "\",";

	$result->free();
}
$manifest = substr($manifest, 0, -1);
$manifest .= "};\n";
echo $manifest;
?>
var team = document.getElementById("select-team");
var klandret = document.getElementById("select-klandret");

team.addEventListener("change", function(e) {
    klandret.innerHTML = teams[team.value];
});

window.onload = function(e) {
    klandret.innerHTML = teams[team.value];    
};
</script>
<?php endif; ?>