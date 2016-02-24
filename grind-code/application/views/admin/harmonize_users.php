<!DOCTYPE html>
<html>
<body>

<form action="/grind-members/grind-code/index.php/auth/do_harmonize_users" method="post" enctype="multipart/form-data">
	<p>Users Associated to LinkedinAccounts</p>
    <select name="linkedinuser">
        <?php
        foreach ($linkedinusers as $linkedinuser) { ?>
            <option value="<?php echo $linkedinuser['id'] ?>"><?php echo $linkedinuser['value'] ?></option>
        <?php } ?>
    </select>
    <br><br>
    <p>Users Not Associated to LinkedinAccounts</p>
    <select name="nonlinkedinuser">
        <?php
        foreach ($nonlinkedinusers as $nonlinkedinuser) { ?>
            <option value="<?php echo $nonlinkedinuser['id'] ?>"><?php echo $nonlinkedinuser['value']?></option>
        <?php } ?>
    </select>
    <br><br>
    <input type="submit" value="Harmonize" name="submit">
    <input type="submit" value="Dont Harmonize" name="submit">
</form>

</body>
</html>