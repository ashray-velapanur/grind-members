<!DOCTYPE html>
<html>
<body>
<?php get_header(); ?>
<h1>Eventbrite Tokens</h1>
<table>
    <thead>
        <tr>
            <td>Eventbrite Token</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($results as $result) {?>
            <tr>
                <td><?= $result->token ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>
<hr>
<form action="<?=ROOTMEMBERPATH?>grind-code/index.php/eventbrite/add_token" method="post" enctype="multipart/form-data">
    <h2>Add Eventbrite Token</h2>
    Eventbrite Access Token: <input type="text" name="eb_token"><br><br>
    <input type="submit" value="Add Token" name="submit">
</form>
<?php get_footer(); ?>
</body>
</html>