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
<p><h4>Bubbles</h4></p>
<table>
<thead>
<tr><td>Rank</td><td>Type</td><td>Title</td><td>Action</td></tr>
</thead>
<tbody>
<? foreach($bubbles as $bubble) { ?>
<tr><td><?= $bubble->rank ?></td><td><?= $bubble->type ?></td><td><?= $bubble->title ?></td><td><a href="<?=ROOTMEMBERPATH?>grind-code/index.php/admin/locationmanagement/delete_bubble?id=<?= $bubble->id ?>&type=<?= $bubble->type ?>">Delete</a></td></tr>
<? }; ?>
</tbody>
</table>

<hr>
<div>
<p><h4>Add Bubble</h4></p>
<form action="<?=ROOTMEMBERPATH?>grind-code/index.php/admin/locationmanagement/add_bubble" method="post" enctype="multipart/form-data">
	<select name="type" onchange="showTypeValues(this)">
		<option value="">Select Type</option>
		<? foreach ($types as $type) { ?>
			<option value="<?= $type ?>"><?= $type ?></option>
		<? } ?>
	</select>
	<select id="event" name="event" class="type-values">
		<option value="">Select Event</option>
		<? foreach ($events as $event) { ?>
			<option value="<?= $event->id ?>"><?= $event->name ?></option>
		<? } ?>
	</select>
	<select id="user" name="user" class="type-values">
		<option value="">Select User</option>
		<? foreach ($users as $user) { ?>
			<option value="<?= $user->id ?>"><?= $user->first_name.' '.$user->last_name ?></option>
		<? } ?>
	</select>
	<select id="company" name="company" class="type-values">
		<option value="">Select Company</option>
		<? foreach ($companies as $company) { ?>
			<option value="<?= $company->id ?>"><?= $company->name ?></option>
		<? } ?>
	</select>
    Title: <input type="text" name="title"><br><br>
    Select image to upload: <input type="file" name="fileToUpload" id="fileToUpload"><br><br>
    Rank: <input type="number" name="rank" min="1" step="1"><br><br>
    <input type="submit" value="Add Bubble" name="submit">
</form>
</div>
<script type="text/javascript">
	function showTypeValues(e) {
		$('.type-values').hide();
		$('#'+$(e).val()).show();
	}
</script>
<?php get_footer(); ?>
</body>
</html>