<!DOCTYPE html>
<html>
<body>
<?php get_header(); ?>
<h1>Drop-In Plans</h1>
<table>
    <thead>
        <tr>
            <td>Space ID</td>
            <td>Plan ID</td>
            <td>Plan Name</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($results as $result) {?>
            <tr>
                <td><?= $result->space_id ?></td>
                <td><?= $result->plan_id ?></td>
                <td><?= $result->plan_name ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>
<hr>
<form action="<?=ROOTMEMBERPATH?>grind-code/index.php/admin/locationmanagement/add_drop_in_plan" method="post" enctype="multipart/form-data">
    <h2>Add Drop-In Plan</h2>
    Space:
    <select id="spaces" name="space_id" class="type-values">
        <option value="">Select Space</option>
        <? foreach ($spaces as $space) { ?>
            <option value="<?= $space->id ?>"><?= $space->name ?></option>
        <? } ?>
    </select>
    <br><br>
    Plan ID: <input type="text" name="plan_id"><br><br>
    Plan Name (optional): <input type="text" name="plan_name"><br><br>
    <input type="submit" value="Add Drop-In Plan" name="submit">
</form>
<?php get_footer(); ?>
</body>
</html>