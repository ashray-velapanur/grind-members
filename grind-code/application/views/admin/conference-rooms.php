<!DOCTYPE html>
<html>
<body>
<?php get_header(); ?>
<nav id="primary">
  <ul class="clearfix">
    <li><?=g_anchor("../conferencerooms", "My Account")?></li>
    <li><?=g_anchor("../conferencerooms", "Conference Rooms")?></li>
    <li><?=g_anchor("../conferencerooms", "Help")?></li>
    <li><?=g_anchor("../conferencerooms", "Agora")?></li>
  </ul>
</nav>
<table>
    <thead>
        <tr><td><h1>Book a tank</h1></td></tr>
        <tr>
            <?php foreach ($spaces as $space) {?>
                <td>
                    <a onclick="$('.resources').hide();$('#<?= $space->id ?>').show();">
                        <img height="100" width="150" src="<?=ROOTMEMBERPATH?>grind-code/index.php/image/get?id=<?= $space->image ?>"/>
                        <p>
                            <b><?= $space->name ?></b>
                        </p>
                    </a>
                </td>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
        <tr><td><h1>Select a Room</h1></td></tr>
        <?php foreach ($spaces as $space) {?>
        <tr class="resources" id="<?= $space->id ?>" style="display:none;">
            <?php foreach ($resources[$space->id] as $resource) {?>
            <td>
                <a onclick="loadCalendar('b-eagles.com_j6ed1i4hc1981lqgovb2rk3c8o%40group.calendar.google.com')">
                    <img height="100" width="150" src="<?=ROOTMEMBERPATH?>grind-code/index.php/image/get?id=<?= $resource->image ?>"/>
                    <p>
                        <b><?= $resource->name ?></b>
                    </p>
                    <p>
                        <?= $resource->description ?>
                    </p>
                    <p>
                        <b>Monthlies:</b> $<?= $resource->rate ?>/hour
                    </p>
                </a>
            </td>
            <?php } ?>
        </tr>
        <?php } ?>
        <tr>
            <td>
                <iframe id="gcal" src="" style="border: 0;display:none;" width="800" height="600" frameborder="0" scrolling="no"></iframe>
            </td>
        </tr>
    </tbody>
</table>
<?php get_footer(); ?>
<script type="text/javascript">
    function loadCalendar(cal_name) {
        var cal_src = 'https://calendar.google.com/calendar/embed?src='+cal_name+'&ctz=America/New_York';
        $("#gcal").attr('src',cal_src);
        $("#gcal").show();
    }
</script>
</body>
</html>