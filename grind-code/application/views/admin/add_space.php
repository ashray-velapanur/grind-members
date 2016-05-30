<!DOCTYPE html>
<html>
<body>
<?php get_header(); ?>
<form action="<?=ROOTMEMBERPATH?>grind-code/index.php/admin/locationmanagement/add_update_space" method="post" enctype="multipart/form-data">
    <p><b>Cobot Space</b></p><br><br>
    <p><b>It is assumed that the Space has already been setup on Cobot with at least a plan named 'Daily'</b></p><br><br>
    <input type="hidden" name="space_id">
    <table>
        <thead>
            <tr>
                <td>Field</td>
                <td>Value</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Cobot ID:</td>
                <td><input type="text" name="cobot_id"></td>
            </tr>
            <tr>
                <td>Main Area Resource ID:</td>
                <td><input type="text" name="main_area_id"></td>
            </tr>
            <tr>
                <td>Display Name:</td>
                <td><input type="text" name="name"></td>
            </tr>
            <tr>
                <td>Description:</td>
                <td><textarea name="description"></textarea></td>
            </tr>
            <tr>
                <td>Capacity:</td>
                <td><input type="number" name="capacity" min="0"></td>
            </tr>
            <tr>
                <td>Select image to upload:</td>
                <td><input type="file" name="fileToUpload" id="fileToUpload"></td>
            </tr>
            <tr>
                <td>Latitude:</td>
                <td><input type="text" name="latitude"></td>
            </tr>
            <tr>
                <td>Longitude:</td>
                <td><input type="text" name="longitude"></td>
            </tr>
            <tr>
                <td>Address Street:</td>
                <td><input type="text" name="address-street"></td>
            </tr>
            <tr>
                <td>Address City:</td>
                <td><input type="text" name="address-city"></td>
            </tr>
            <tr>
                <td>Address State:</td>
                <td><input type="text" name="address-state"></td>
            </tr>
            <tr>
                <td>Address Country:</td>
                <td><input type="text" name="address-country"></td>
            </tr>
            <tr>
                <td>Address ZIP:</td>
                <td><input type="text" name="address-zip"></td>
            </tr>
            <tr>
                <td>Rate:</td>
                <td><input type="number" name="rate" min="0" step="0.01"></td>
            </tr>
        </tbody>
    </table>

    <input type="submit" value="Add Space" name="submit">
</form>
<?php get_footer(); ?>
</body>
</html>