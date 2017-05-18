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
	<form class="col col-md-4" action="/<?php echo $arguements["auid"]; ?>/create" id="edit-user" method="POST">
		<div class="form-group">
			<label> <?php echo $arguements["name"] . "</b> (" . $arguements["auid"] . ")"; ?></label>
		</div>
		<div class="form-group">
			<label for="selectKlandret">Klandrer hermed</label>
			<select name="toid" class="form-control" id="selectKlandret">
			<?php $sql = "SELECT name FROM student";
                    $result = $db->query($sql);
                    if ($result) {
                        while ($row = $result->fetch_assoc()) {
		                  $name = $row['name'];
		                  echo '<option>'.$name.'</option>';
						}
                    } else {
                    	echo 'fuck';
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
			<textarea class="form-control" id="selectElab" name="desc" placeholder="Indtast beskrivelse" rows="3"required></textarea>
		</div>

			<div class="input-group">
				<input type="submit" class="btn btn-default" value="Klandr!" />
			</div>
		</div>
	</form>
<?php endif; ?>