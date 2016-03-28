<!DOCTYPE html>
<html>
<body>
<?php get_header(); ?>
<form action="<?=ROOTMEMBERPATH?>grind-code/index.php/admin/locationmanagement/add_space_resources" method="post" enctype="multipart/form-data">
    <h1>Space: <?= $space_id ?></h1>
    <input type="hidden" name="space_id" value="<?= $space_id ?>">
    <table>
        <thead>
            <tr>
                <td><b>Resource Name</b></td>
                <td><b>Current Image</b></td>
                <td><b>Select image to upload</b></td>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($resources as $resource) {?>
                <tr>
                    <td><?= $resource->name ?></td>
                    <td><img height="100" width="150" src="<?=ROOTMEMBERPATH?>grind-code/index.php/image/get?id=<?= $resource->image ?>"/></td>
                    <td><input type="file" name="image<?= $resource->id ?>" id="image<?= $resource->id ?>"></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <input type="submit" value="Add Resources" name="submit">
</form>
<?php get_footer(); ?>
</body>
</html>