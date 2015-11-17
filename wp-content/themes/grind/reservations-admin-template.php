<?php

/**
 * Template Name: Reservations Admin
 */

// Location in repository: member/wp-content/themes/grind/reservations-admin-template.php

// Get initial CI information
$CI =& get_instance();
$CI->load->model("spacemodel");
$CI->load->model("locationmodel");
$CI->load->model("reservationmodel");

$space_id = false;
$location_id = false;
$exact_date = date('Y-m-d');

$spaces = $CI->spacemodel->wpGetSpacesList();
$locations = $CI->locationmodel->wpGetLocationsList();

// Process a user reservation
if (!empty($_REQUEST['data'])) {
	$data = $_REQUEST['data'];
	if (!empty($data['space_id'])) {
		$space_id = (int) $data['space_id'];
	}
	if (!empty($data['location_id'])) {
		$location_id = (int) $data['location_id'];
	}
	
	if (!isset($spaces[(int) $space_id])) {
		$space_id = false;
	}
	
	if (!isset($locations[(int) $location_id])) {
		$location_id = false;
	}
	
	if (!empty($data['exact_date'])) {
		$exact_date = date('Y-m-d', strtotime((string) $data['exact_date']));
	}
}

// Get data for the page
$reservations = $CI->reservationmodel->wpGetReservations(compact('space_id', 'location_id', 'exact_date'));
list($capacity, $spacesLeft) = $CI->reservationmodel->reservationsLeft($exact_date, $location_id);

// Render the page

get_header();

if (have_posts()) {
	while (have_posts()) {
		the_post(); ?>
		<h1><?php the_title(); ?></h1>
		<div id="instructions"><?php the_content(); ?></div>
		<hr class="pagehead" />

		<form id="requestRoom" class="grey-fields" method="post" style="padding-top:0">
			<legend>Show all listings for the following filter:</legend>
			<div class="request-dates clearfix">
				<div class="col">
					<label for="dataLocationId">Location</label>
					<select name="data[location_id]" id="dataLocationId">
						  <option value="">Choose a location</option>
						<?php foreach ($locations as $l_id => $location_name) : ?>
						  <option value="<?php echo $l_id ?>" <?php echo $location_id == $l_id ? 'selected="selected"' : ''?>><?php echo $location_name; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
			<div class="request-dates clearfix">
				<div class="col">
					<label for="dataSpaceId">Location/Space</label>
					<select name="data[space_id]" id="dataSpaceId">
						  <option value="">Choose a space</option>
						<?php foreach ($spaces as $s_id => $space_name) : ?>
						  <option value="<?php echo $s_id ?>" <?php echo $space_id == $s_id ? 'selected="selected"' : ''?>><?php echo $space_name; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
			<div class="request-dates clearfix">
				<div class="col">
					<label for="dataExactDate">Date</label>
					<input type="date" name="data[exact_date]" id="dataExactDate">
				</div>
			</div>
			<input type="submit" class="btn" value="Search" id="formSubmit"/>
			<div class="loader" style="display: none;"></div>
		</form>

		<hr class="pagehead" />

		<h3>Reservations For the following filter</h3>

		<ul>
			<li><strong>Space:</strong> <?php echo $space_id ? $spaces[$space_id] : 'No space specified' ?></li>
			<li><strong>Space:</strong> <?php echo $location_id ? $locations[$location_id] : 'No location specified' ?></li>
			<li><strong>Date:</strong> <?php echo $exact_date ? $exact_date : 'No date specified' ?></li>
		</ul>
		<br />

		<?php if ($spacesLeft !== false) : ?>
		<ul>
				<li><strong>Capacity at location for date:</strong> <?php echo number_format($capacity) ?></li>
				<li><strong>Spaces Left at location:</strong> <?php echo number_format($spacesLeft) ?></li>
		</ul>
		<br />
		<?php endif; ?>

		<table>
			<tr>
				<td>Location</td>
				<td>Space</td>
				<td>Date</td>
				<td>Member</td>
				<td>Party Of</td>
				<td>Checked In</td>
				<td>Paid</td>
			</tr>
			<?php foreach ($reservations as $reservation) : ?>
				<tr>
					<td><?php echo $reservation['l_name']; ?></td>
					<td><?php echo $reservation['s_name']; ?></td>
					<td><?php echo $reservation['date']; ?></td>
					<td><?php echo "{$reservation['first_name']} {$reservation['last_name']}"; ?></td>
					<td><?php echo $reservation['party_of']; ?></td>
					<td><?php echo $reservation['checked_in'] ? 'Yes' : 'No'; ?></td>
					<td><?php echo $reservation['paid'] ? 'Yes' : 'No'; ?></td>
				</tr>
			<?php endforeach; ?>
		</table>

		<?php
	}
}
get_footer(); ?>