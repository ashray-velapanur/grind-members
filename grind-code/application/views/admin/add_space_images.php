<!DOCTYPE html>
<html>
<body>
<?php get_header(); ?>
<form action="<?=ROOTMEMBERPATH?>grind-code/index.php/admin/locationmanagement/add_space_images" method="post" enctype="multipart/form-data">
    <h1>Spaces</h1>
    <table>
        <tbody>
            <?php foreach ($spaces as $space) {?>
                <tr>
                    <td><span>Space Name: <?= $space->name ?></span></td>
                    <td><img height="100" width="150" src="<?=ROOTMEMBERPATH?>grind-code/index.php/image/get?id=<?= $space->image ?>"/></td>
                    <td>Select image to upload: <input type="file" name="image<?= $space->id ?>" id="image<?= $space->id ?>"></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <input type="submit" value="Add Images" name="submit">
</form>
<?php get_footer(); ?>
</body>
</html>