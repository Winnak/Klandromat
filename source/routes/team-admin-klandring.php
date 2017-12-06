<?php $team = get_team_from_slug($resources["team"]); ?>
<?php if($_SERVER['REQUEST_METHOD'] === "POST") : ?>
<?php
$title = $db->real_escape_string($_POST["title"]);
$desc = $db->real_escape_string($_POST["desc"]);
$to = intval($_POST["toid"]);
$from = intval($_POST["fromid"]);

if ($to < 0 || $from < 0
	|| !is_numeric($_POST["toid"])
	|| !is_numeric($_POST["fromid"]))
{
	header("Location: /$team[slug]/admin-klandring");
	die();
}

$sql = "INSERT INTO klandring (`title`, `description`, `from`, `to`, `team`, `verdictdate`)
		VALUES ('$title', '$desc', $from, $to, $team[id], CURDATE());";

$result = $db->query($sql);
if ($result) {
	header("Location: /$team[slug]/admin");
}
else {
	header("Location: /$team[slug]/admin-klandring");
}
?>
<?php else: /* GET */ ?>
<?php
$students = get_students_of_team($team["id"]);
$options = "";
foreach ($students as $student) {
	$options .= "<option value='$student[id]'>$student[name]</option>";
}
?>
<form class="col col-md-4" action="/<?= $team["slug"] ?>/admin-klandring" id="edit-user" method="POST">
	<div class="form-group">
		<label for="select-team">På holdet:</label>
		<?= $team["name"]; ?>
	</div>
	<div class="form-group">
		<label for="select-klandret">Klandrer</label>
		<select name="fromid" class="form-control" id="select-klandrer">
			<?= $options ?>
		</select>
	</div>
	<div class="form-group">
		<label for="select-klandret">Klandret</label>
		<select name="toid" class="form-control" id="select-klandret">
			<?= $options ?>
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
window.onload = function(e) {
	var klandrer = document.getElementById("select-klandrer");
	var klandret = document.getElementById("select-klandret");
    klandret.innerHTML = teams[team.value];
};
</script>
<?php endif; ?>