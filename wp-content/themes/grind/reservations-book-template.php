<?php

/**
 * Template Name: Reservations Book
 */

// Location in repository: member/wp-content/themes/grind/reservations-book-template.php

// Get initial CI information
$CI =& get_instance();
$CI->load->model("spacemodel");
$CI->load->model("reservationmodel");
$CI->load->model("members/membermodel", "membermodel");

// Process a user reservation
$reserved = false;
$error = false;
if (!empty($_REQUEST['data'])) {
	$ciUser = $CI->reservationmodel->getUserByWpId();

	$data = $_REQUEST['data'];
	$data['user_id'] = $ciUser->id;
	$reserved = $CI->reservationmodel->reserve($ciUser->id, $data);
	if (!$reserved) {
		$error = $CI->reservationmodel->_reservationError;
	}
}

// Get data for the page
$spaces = $CI->spacemodel->wpGetSpacesList();

// Render the page

get_header();

if (have_posts()) {
	while (have_posts()) {
		the_post(); ?>
		<h1><?php the_title(); ?></h1>
		<div id="instructions"><?php the_content(); ?></div>
		<hr class="pagehead" />

		<?php if ($reserved) : ?>
			<div id="conferenceRoomSuccess" style="display:block">
				<p class="msg msg1">You have successfully reserved the room!</p>
			</div>
		<?php endif; ?>

		<?php if (!empty($error)) : ?>
			<div id="conferenceRoomSuccess" style="display:block">
				<p class="msg msg1" style="display:block"><?php echo $error ?></p>
			</div>
		<?php endif; ?>

		<form id="requestRoom" class="grey-fields" method="post" style="padding-top:0">
			<legend>Create a new reservation:</legend>
			<div class="request-dates clearfix">
				<div class="col">
					<label for="dataSpaceId">Space ID</label>
					<select name="data[space_id]" id="dataSpaceId">
						<?php foreach ($spaces as $space_id => $space_name) : ?>
						  <option value="<?php echo $space_id ?>"><?php echo $space_name; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
			<div class="request-dates clearfix">
				<div class="col">
					<label for="dataDate">Date</label>
					<input type="date" name="data[date]" id="dataDate">
				</div>
				<div class="col">
					<label for="dataPartyOf">Number of Spaces</label>
					<input type="text" name="data[party_of]" id="dataPartyOf">
				</div>
			</div>
			<input type="submit" class="btn" value="Submit" id="formSubmit"/>
			<div class="loader" style="display: none;"></div>
		</form>

		<?php
	}
}
get_footer(); ?>