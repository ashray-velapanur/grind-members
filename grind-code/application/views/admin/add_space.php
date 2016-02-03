<!DOCTYPE html>
<html>
<body>

<form action="/grind-members/grind-code/index.php/admin/locationmanagement/add_update_space" method="post" enctype="multipart/form-data">
	<p><b>Cobot Space</b></p>
    <input type="hidden" name="space_id">
    Cobot ID: <input type="text" name="cobot_id"><br><br>
    Capacity: <input type="number" name="capacity" min="0"><br><br>
    Select image to upload: <input type="file" name="fileToUpload" id="fileToUpload"><br><br>
    Latitude: <input type="text" name="latitude"><br><br>
    Longitude: <input type="text" name="longitude"><br><br>
    Address Street: <input type="text" name="address-street"><br><br>
    Address City: <input type="text" name="address-city"><br><br>
    Address State: <input type="text" name="address-state"><br><br>
    Address Country: <input type="text" name="address-country"><br><br>
    Address ZIP: <input type="text" name="address-zip"><br><br>
    Rate: <input type="number" name="rate" min="0" step="0.01"><br><br>
    <input type="submit" value="Add Space" name="submit">
</form>

</body>
</html>