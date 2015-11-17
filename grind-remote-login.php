<?php

require( dirname(__FILE__) . '/wp-load.php' );

// Redirect to https login if forced to use SSL
if ( force_ssl_admin() && !is_ssl() ) {
	if ( 0 === strpos($_SERVER['REQUEST_URI'], 'http') ) {
		wp_redirect(preg_replace('|^http://|', 'https://', $_SERVER['REQUEST_URI']));
		exit();
	} else {
		wp_redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		exit();
	}
}

require('_siteconfig.php');

$username = $_POST['Username'];
$pw = $_POST['Password'];
if (isset($username) && isset($pw)) {
	//echo "variables set";
	$user = wp_authenticate($username, $pw);
	if ( is_wp_error($user) ){
	    header('Location: ' . $_SERVER['HTTP_REFERER'] . '?login=err');
	//	echo "failed";
	}else {
	//	echo "authentication succeeded";
		wp_set_current_user($user->ID, $username);
		wp_set_auth_cookie($user->ID);
		do_action('wp_login', $username);
		header( 'Location: https://'.$_SERVER["SERVER_NAME"].ROOTMEMBERPATH);
	}
}

?>
