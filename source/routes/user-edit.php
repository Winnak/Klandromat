<?php
$user = get_user_from_auid($resources["user"]);
?>

<?php if($_SERVER['REQUEST_METHOD'] === "POST") : ?>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $db->real_escape_string($_POST["email"]);
    $phone = $db->real_escape_string($_POST["phone"]);

    $sql = "UPDATE student SET email = '$email', phone = $phone WHERE student.id = $user[id]";

    if ($db->query($sql) === TRUE) {
        header("Location: /$user[auid]");
    }
}
?>

<?php else: ?>
<?php /** TODO INLINE */ ?>

<p><b><?= $user["name"] ?></b>, <?= $user["auid"] ?></p>

<form action="/<?= $resources["user"] ?>/edit" id="edit-user" method="POST">
    <div class="form-group">
        <div class="input-group">
            <span class="input-group-addon">@</span>
            <input type="email" class="form-control" name="email" value="<?= $user["email"] ?>" required>
        </div>
        <br>
        <div class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-earphone"></i> +45</span>
            <input type="tel" id="tel" class="form-control" name="phone" maxlength="8" minlength="8" value="<?= $user["phone"] ?>">
        </div>
        <br>
        <div class="input-group">
            <input type="submit" class="btn btn-default" value="Edit" />
        </div>
    </div>
</form>
<script>$("#edit-user").validate({
rules: {
    phone: { regex: "[0-9]{8}" }
}
});</script>
<?php endif; ?>