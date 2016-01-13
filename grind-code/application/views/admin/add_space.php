<!DOCTYPE html>
<html>
<body>

<form action="/~aiyappaganesh/grind-members/grind-code/index.php/admin/locationmanagement/add_update_space" method="post" enctype="multipart/form-data">
	<p><b>Cobot Space</b></p>
    <input type="hidden" name="space_id">
    Cobot ID: <input type="text" name="cobot_id"><br><br>
    Capacity: <input type="number" name="capacity" min="0"><br><br>
    Select image to upload: <input type="file" name="fileToUpload" id="fileToUpload"><br><br>
    <input type="submit" value="Add Space" name="submit">
</form>

</body>
</html>