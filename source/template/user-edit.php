<form action="/<?php echo SITE_ROOT . "/" . $arguements["auid"]; ?>/edit" id="edit-user">
    <div class="form-group">
        <div class="input-group">
            <span class="input-group-addon">@</span>
            <input type="email" class="form-control" name="email" value="<?php echo $arguements["email"]; ?>" required>
        </div>
        <div class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-earphone"></i> +45</span>
            <input type="tel" id="tel" class="form-control" name="phone" maxlength="8" minlength="8" value="<?php echo $arguements["phone"]; ?>">
        </div>
        <div class="input-group">
            <input type="submit" class="form-control" value="Edit" />
        </div>
    </div>
</form>
<script>$("#edit-user").validate({
rules: {
    phone: { regex: "[0-9]{8}" }
}
});</script>