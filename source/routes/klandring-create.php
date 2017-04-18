<?php if($_SERVER['REQUEST_METHOD'] === "POST") : ?>
	<?php
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$id = $arguements["id"];
		$title = $db->real_escape_string($_POST["title"]);
		$desc = $db->real_escape_string($_POST["desc"]);
		$toID = $db->real_escape_string($_POST["toid"]);


		//CHANGES NAME TO ID!
		$sql = "SELECT id FROM student WHERE name = '$toID' LIMIT 1";
		$result = $db->query($sql);
		if ($result) {
			$row = $result->fetch_assoc();
			$toID = $row['id'];
		} else {
			echo 'fuck';
		}
		$result->free();

		$sql = "INSERT INTO `klandring`(`title`, `description`, `from`, `to`) VALUES ('$title','$desc','$id','$toID');";

		if ($db->query($sql) === TRUE) {
			header("Location: /"  . $arguements["auid"]);
		}
	}
	?>

<?php else: ?>
	<b> Jeg </b>
	<p><b><?php echo $arguements["name"] . "</b>, " . $arguements["auid"]; ?></p>

	<form action="/<?php echo $arguements["auid"]; ?>/create" id="edit-user" method="POST">
		<b>Klandrer hermed</b>

		<div class="form-group">
			<div class="input-group">
			<input type="text" id="toid" class="form-control" name="toid" list="userlist" placeholder="Indtast navn"required>
			<datalist id="userlist">
			  		<?php $sql = "SELECT name FROM student";
                    $result = $db->query($sql);
                    if ($result) {
                        while ($row = $result->fetch_assoc()) {
		                  $name = $row['name'];
		                  echo '<option value="'.$name.'"/>';
						}
                    } else {
                    	echo 'fuck';
                    }
                    $result->free();
				?>
			</datalist>
			</div>
			<b>for</b>
			<div class="input-group">
				<input type="text" class="form-control" name="title" placeholder="Indtast titel" required>
			</div>
			<br>
			<b>uddybning:</b>
			<div class="input-group">
				<input type="textarea" class="form-control" name="desc" placeholder="Indtast beskrivelse" required>
			</div>
			<br>

			<div class="input-group">
				<input type="submit" class="btn btn-default" value="Klandr!" />
			</div>
		</div>
	</form>
<?php endif; ?>