<?php
@require_once('wp-config.php');
@require_once('wp-includes/pluggable.php');
@require_once('wp-includes/registration.php');

if (!empty($_POST)) {
	global $wpdb, $user_ID, $user_email;
	get_currentuserinfo();
	$user = get_userdata($user_ID);
	
	if ($_POST['password'] != $_POST['password_confirm']) {
		$response = array(
			"success" => 0,
			"reason" => "PASSWORDMISMATCH"
		);
		echo json_encode($response);
		exit;
	}
	
	if (isset($_POST['current_password']) && isset($_POST['password']) && isset($_POST['password_confirm']) && !empty($_POST['password']) && $_POST['password'] == $_POST['password_confirm']) {
		
		$user = wp_authenticate($user->user_login, $_POST['current_password']);
		if (is_wp_error($user) ) {
			$response = array(
				"success" => 0,
				"reason" => "BADCURRENTPASSWORD"
			);
			echo json_encode($response);
			exit;
		}

		
		$update = $wpdb->query("UPDATE wpmember_users SET user_pass = '".wp_hash_password($_POST['password'])."' WHERE id = '".$user_ID."'");

		wp_cache_delete($user_ID, 'users');
		wp_cache_delete($user->user_login, 'userlogins');
		
		session_start();
		session_unset();
		session_destroy();
		wp_clear_auth_cookie();
		
		
		wp_signon(array('user_login' => $user->user_login,
					   'user_password' => $_POST['password']));
		
		$response = array(
			"success" => 1
		);
		echo json_encode($response);
		exit;
	}

}
?>