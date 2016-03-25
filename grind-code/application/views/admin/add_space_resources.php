<!DOCTYPE html>
<html>
<body>
<?php get_header(); ?>
<form action="<?=ROOTMEMBERPATH?>grind-code/index.php/admin/locationmanagement/add_space_resources" method="post" enctype="multipart/form-data">
    <h1>Space: <?= $space_id ?></h1>
    <input type="hidden" name="space_id" value="<?= $space_id ?>">
    <table>
        <tbody>
            <?php foreach ($resources as $resource) {?>
                <tr>
                    <td><span>Resource Name: <?= $resource->name ?></span></td>
                    <td>Select image to upload: <input type="file" name="image<?= $resource->id ?>" id="image<?= $resource->id ?>"></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <input type="submit" value="Add Resources" name="submit">
</form>
<?php get_footer(); ?>
</body>
</html>