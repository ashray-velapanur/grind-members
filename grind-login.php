<?php

require('wp-blog-header.php');
require('_siteconfig.php');

if (isset($_GET['h']) && isset($_GET['id'])) {
	$user = get_userdatabylogin($_GET['id']);
	
	if ($_GET['h'] === get_user_meta($user->ID, 'registerHash', true)) {
		wp_set_current_user($user->ID, $_GET['id']);
		wp_set_auth_cookie($user->ID);
		do_action('wp_login', $_GET['id']);
		if (get_user_meta($user->ID, 'registerStep',true)==2){
			// if you ever want to skip the password creation screen, just swap to this other
			// redirect
			//header( 'Location: '.site_url('member-registration','login')) ;
			header( 'Location: '.site_url('new-member-set-password/','login'));
			exit;
		}
			header( 'Location: '.site_url('new-member-set-password/','login'));
			exit;
	} else {
		header( 'Location: '.site_url('wp-login.php','login'));
	}
} else {

	header( 'Location: '.site_url('wp-login.php','login'));
	exit;
}

?>
