<!DOCTYPE html>
<html>
<body>
<?php get_header(); ?>
<h1>Eventbrite Tokens</h1>
<table>
    <thead>
        <tr>
            <td>Eventbrite User ID</td>
            <td>Eventbrite Token</td>
            <td>Action</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($results as $result) {?>
            <tr>
                <td><?= $result->eb_user_id ?></td>
                <td><?= $result->token ?></td>
                <td><a href="<?=ROOTMEMBERPATH?>grind-code/index.php/eventbrite/delete_token?eb_user_id=<?= $result->eb_user_id ?>">Delete</a></td>
            </tr>
        <?php } ?>
    </tbody>
</table>
<hr>
<form action="<?=ROOTMEMBERPATH?>grind-code/index.php/eventbrite/add_token" method="post" enctype="multipart/form-data">
    <h2>Add Eventbrite Token</h2>
    Eventbrite User ID: <input type="text" name="eb_user_id"><br><br>
    Eventbrite Access Token: <input type="text" name="eb_token"><br><br>
    <input type="submit" value="Add Token" name="submit">
</form>
<?php get_footer(); ?>
</body>
</html>