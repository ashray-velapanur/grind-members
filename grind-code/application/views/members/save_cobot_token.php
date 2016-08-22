<!DOCTYPE html>
<html>
<head>
<style type="text/css">
	.type-values {
		display: none;
	}
</style>
<script type="text/javascript" src="<?=ROOTMEMBERPATH?>grind-code/js/libs/jquery-1.6.2.min.js"></script>
</head>
<body>
<?php get_header(); ?>
<p><h4>Save Cobot Access Token for Grind Users</h4></p>

<hr>
<div>
<form action="<?=ROOTMEMBERPATH?>grind-code/index.php/admin/usermanagement/fetch_cobot_user" method="post" enctype="multipart/form-data">
	<select id="user" name="user">
		<option value="">Select User</option>
		<? foreach ($users as $user) { ?>
			<option value="<?= $user->id ?>"><?= $user->first_name.' '.$user->last_name ?></option>
		<? } ?>
	</select><br><br>
    <input type="submit" value="Login to Cobot" name="submit">
</form>
</div>
<?php get_footer(); ?>
</body>
</html>