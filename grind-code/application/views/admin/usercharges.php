<?php if(isset($user)): ?>
<ul id="tabs" class="clearfix">
  <li><?=g_anchor("/admin/usermanagement/user/" . $user->id, "Edit")?></li>
  <li><?=g_anchor("/admin/usermanagement/usercheckins/" . $user->id, "Check-Ins")?></li>
  <li class="current"><?=g_anchor("/admin/usermanagement/usercharges/" . $user->id, "Charges and Credits")?></li>
</ul>
<?php endif ?>

<section id="checked-in">
  <h2>Charges and Credits for <?=$user->first_name . " " . $user->last_name ?></span></h2>
  <table width="100%">
      <tr>
        <th class="date">Date</th>
        <th class="description">Description</th>
        <th class="amount">Amount</th>
        <th class="invoice">Invoice Number</th>
      </tr>
    <? foreach($charges as $charge): ?>
      <tr>
        <td class="date"><?=utilities::grind_date_format($charge->date) ?></td>
        <td class="description"><?=$charge->description ?></td>
        <td class="amount"><?=number_format($charge->amount, 2) ?></td>
        <td class="invoice"><?=($charge->invoice == "" ? "Not yet invoiced" : $charge->invoice) ?></td>
      </tr>
    <? endforeach; ?>
  </table>
</section>