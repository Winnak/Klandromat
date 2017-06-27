<?php if($_SERVER['REQUEST_METHOD'] === "POST") : ?>
<?php
$title = $db->real_escape_string($_POST["title"]);
$desc = $db->real_escape_string($_POST["desc"]);
$to = intval($_POST["toid"]);

if ($to < 0) {
	header("Location: /klandring/create");
}

$sql = "INSERT INTO klandring (`title`, `description`, `from`, `to`) 
		VALUES ('$title', '$desc', $arguments[id], $to);";

$result = $db->query($sql);
if ($result) {
	header("Location: /$arguments[auid]");
}
else {
	header("Location: /klandring/create");
}
?>
<?php else: /* GET */ ?>
<form class="col col-md-4" action="/klandring/create" id="edit-user" method="POST">
	<div class="form-group">
		<label><?= $arguments["name"] ?></b> (<?= $arguments["auid"] ?>)</label>
	</div>
	<div class="form-group">
		<label for="selectKlandret">Klandrer hermed</label>
		<select name="toid" class="form-control" id="selectKlandret">
		<?php 
			$sql = "SELECT id, name FROM student";
			$result = $db->query($sql);
			if ($result) {
				while ($row = $result->fetch_assoc()) {
					echo "<option value='$row[id]'>$row[name]</option>";
				}
			}
			$result->free();
		?>
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
<?php endif; ?>