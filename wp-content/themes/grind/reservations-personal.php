<?php

/**
 * Template Name: Reservations Personal
 */

// Location in repository: member/wp-content/themes/grind/reservations-personal-template.php

$CI =& get_instance();
$CI->load->model("reservationmodel");
$ciUser = $CI->reservationmodel->getUserByWpId();
$reservations = $CI->reservationmodel->wpGetReservations(array(
	'user_id' => $ciUser->id,
	'min_date' => date('Y-m-d'),
));
?>

<h3>Your Upcoming Reservations</h3>
<table>
	<tr>
		<td>Location</td>
		<td>Space</td>
		<td>Date</td>
		<td>Party Of</td>
		<td>Checked In</td>
		<td>Paid</td>
	</tr>
	<?php foreach ($reservations as $reservation) : ?>
		<tr>
			<td><?php echo $reservation['l_name']; ?></td>
			<td><?php echo $reservation['s_name']; ?></td>
			<td><?php echo $reservation['date']; ?></td>
			<td><?php echo $reservation['party_of']; ?></td>
			<td><?php echo $reservation['checked_in'] ? 'Yes' : 'No'; ?></td>
			<td><?php echo $reservation['paid'] ? 'Yes' : 'No'; ?></td>
		</tr>
	<?php endforeach; ?>
</table>