<!DOCTYPE html>
<html>
<body>

<form action="/grind-members/grind-code/index.php/admin/locationmanagement/add_update_resource" method="post" enctype="multipart/form-data">
    <select name="space_id">
    	<?php foreach ($spacedata as $space) {?>
    		<option value="<?php echo $space ?>"><?php echo $space ?></option>
    	<?php } ?>
    </select>
    <input type="hidden" name="resource_id">
    Cobot Resource ID:
    <input type="text" name="cobot_resource_id">
    Select image to upload:
    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="submit" value="Add Resource" name="submit">
</form>

</body>
</html>