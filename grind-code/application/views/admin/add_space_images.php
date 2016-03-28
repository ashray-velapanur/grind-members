<!DOCTYPE html>
<html>
<body>
<?php get_header(); ?>
<form action="<?=ROOTMEMBERPATH?>grind-code/index.php/admin/locationmanagement/add_space_images" method="post" enctype="multipart/form-data">
    <h1>Spaces</h1>
    <table>
        <thead>
            <tr>
                <td><b>Space Name</b></td>
                <td><b>Current Image</b></td>
                <td><b>Select image to upload</b></td>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($spaces as $space) {?>
                <tr>
                    <td><?= $space->name ?></td>
                    <td><img height="100" width="150" src="<?=ROOTMEMBERPATH?>grind-code/index.php/image/get?id=<?= $space->image ?>"/></td>
                    <td><input type="file" name="image<?= $space->id ?>" id="image<?= $space->id ?>"></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <input type="submit" value="Add Images" name="submit">
</form>
<?php get_footer(); ?>
</body>
</html>