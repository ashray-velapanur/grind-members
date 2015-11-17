<?php
@require_once('wp-config.php');
@require_once('wp-includes/pluggable.php');
@require_once('wp-includes/registration.php');
get_currentuserinfo();
	
if ( is_user_logged_in() ) {
	session_start();
	if (!isset($_SESSION['wpuser'])) {
		$_SESSION['wpuser'] = $current_user;
	}
	echo "1";
} else {
	session_start();
	session_unset();
	session_destroy();
	echo "0";
}
?>
