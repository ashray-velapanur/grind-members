<?
/**
 * listing of Transactions view template
 * 
 * returns a list of transactions for a given user
 *
 *
 * @joshcampbell 
 * @view template
 */
 include_once APPPATH . 'libraries/Date_Difference.phps';

 ?>
<h3>Checkin Activity</h3>
<table>
<?
 if ($checkins){
 foreach($checkins as $checkin){
?>
	 <tr>
        <td class="date"><?=format_date($checkin->sign_in,true) ?></td>
        <td class="description">&nbsp;</td>
        <td class="location"><?=$checkin->location_id ?></td>
      </tr>
<?php
	} //end foreach
} else {
	echo "No recent activity";
}
?>
</table>
