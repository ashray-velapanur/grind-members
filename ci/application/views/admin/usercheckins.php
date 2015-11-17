<?php  

include_once APPPATH . 'libraries/Date_Difference.phps';

?>


<?php if(isset($user)): ?>
<ul id="tabs" class="clearfix">
  <li><?=g_anchor("/admin/usermanagement/user/" . $user->id, "Edit")?></li>
  <li class="current"><?=g_anchor("/admin/usermanagement/usercheckins/" . $user->id, "Check-Ins")?></li>
  <li><?=g_anchor("/admin/usermanagement/usercharges/" . $user->id, "Charges and Credits")?></li>
</ul>
<?php endif ?>

<section id="checked-in">
  <h2>Check-Ins for <?=$user->first_name . " " . $user->last_name ?></span></h2>
  <table width="100%">
      <tr>
        <th class="date">Date</th>
        <th class="name">Name</th>
        <th class="description">Location</th>
      </tr>
    <? foreach($checkIns as $checkIn): ?>
      <tr>
        <td class="date"><?=Date_Difference::getString(new DateTime($checkin->sign_in)) ?></td>
        <td class="name"><?=g_anchor("/admin/usermanagement/user/" . $checkIn->id, $checkIn->last_name . ", " . $checkIn->first_name) ?><? if($checkIn->company != "") { echo ", <span class=\"company\">" . $checkIn->company . "</span>"; } ?></td>
        <td class="location"><?=$checkIn->location_name ?></td>
      </tr>
    <? endforeach; ?>
  </table>
</section>