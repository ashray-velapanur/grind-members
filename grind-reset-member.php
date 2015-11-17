		<?
		@require_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');

		error_log("attempting to set current user",0);
		$member = $_SESSION['member'];
		//session_destroy();
		
		wp_clear_auth_cookie();
		wp_set_auth_cookie($member->wp_users_id);
		do_action('wp_login', $member->email);
		
		wp_redirect('your-account');
		
		?>