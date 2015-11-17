<?php
@require_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
@require_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/pluggable.php');
@require_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/registration.php');

$output = "email = " . $_GET['email'] . " | ";
$output .= "hash = " . $_GET['pwhash'] . " | ";

if (isset($_GET['email']) && isset($_GET['pwhash'])) {
	$result = $wpdb->get_row("SELECT user_pass, id FROM wpmember_users where user_login = '" . $_GET['email'] . "'");
	
	$output .= "password from query = ". $result->user_pass . " | ";
	
	if ($result->user_pass == $_GET['pwhash']) {
		
		$output .= "inside if, id = " . $result->id . "| ";
		
		$userId = $result->id;
		wp_set_current_user($result->id, $_GET['email']);
		wp_set_auth_cookie($result->id);
		do_action('wp_login', $_GET['email']);
	}
	$output .= "after first if | ";
}
//echo $output;

?>