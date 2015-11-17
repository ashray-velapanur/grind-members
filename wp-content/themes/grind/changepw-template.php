<?php
/**
 * Template Name: New Member: Change Password
 * 
 */
global $current_user, $user_ID, $wp_roles;
get_currentuserinfo();
$message = "";
$currentUserId = $user_ID;
$currentUserLogin = $current_user->user_login;


// If user doesn't have a registerHash metadata field in the database, then
// they already already went through this process. Redirect to account screen.
if (!get_user_meta($currentUserId, 'registerHash', true)) {
	header( 'Location: '.site_url('your-account'));
	exit;
} else {
	add_user_meta($user_ID,'registerStep',1,true);
}

if (isset($_POST['password']) && isset($_POST['password_confirm'])) {
	
	$update = $wpdb->query("UPDATE {$wpdb->users} SET `user_pass` = '".wp_hash_password($_POST['password'])."' WHERE `ID` = '".$currentUserId."'");
	
	if (!is_wp_error($update)) {
		wp_cache_delete($currentUserId, 'users');
		wp_cache_delete($currentUserLogin, 'userlogins');
		//wp_logout();
		
		wp_signon(array('user_login' => $currentUserLogin,
					   'user_password' => $_POST['password']));
		
		wp_set_current_user($currentUserId, $currentUserLogin);
		wp_set_auth_cookie($currentUserId);
		do_action('wp_login', $currentUserLogin);
		update_user_meta($user_ID,'registerStep',2,true);
		
		header( 'Location: '.site_url('member-registration' )) ;
		exit;
	} else {
		$message = "We were unable to set your password at this time. Please try again.";
	}
}

$newregistration=true;
include("header.php");
 ?>

		<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

			<h1><?php the_title(); ?></h1>
			<div>
				<?php the_content(); ?>
				<div id="error-message" class="error-message" <?php echo ''.(!empty($message) ? 'style="display:inline-block;"' : '');?> >
					<?php echo $message; ?>
				</div>
				<form id="passwordFormNewMember" method="post" class="newMemberPasswordForm">
					<div class="form-row">
						<div class="inputLabel">Password</div>
						<input class="text-input required" name="password" type="password" id="password" value="" />
					</div>
					<div class="form-row">
						<div class="inputLabel">Confirm Password</div>
						<input class="text-input required" name="password_confirm" type="password" id="password_confirm" value="" />
					</div>
					<div class="form-row">
						<input type="submit" value="Submit" class="btn" />
					</div>
				</form>
			</div>
		<?php endwhile; ?>

<?php get_footer(); ?>
<script type="text/javascript">
 $(document).ready(function(){
    $("#passwordFormNewMember").validate({
  rules: {
    password: {
    required:true,
    minlength:8
    },
    password_confirm: {
      equalTo: "#password"
    }
  }
});
  });
</script>
