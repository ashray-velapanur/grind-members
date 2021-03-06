<?php
/**
 * WordPress User Page
 *
 * Handles authentication, registering, resetting passwords, forgot password,
 * and other user handling.
 *
 * @package WordPress
 */

/** Make sure that the WordPress bootstrap has run before continuing. */
require( dirname(__FILE__) . '/wp-load.php' );
require( dirname(__FILE__) . '/_siteconfig.php' );
global $firstrun;
$firstrun = false;
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

/**
 * Outputs the header for the login page.
 *
 * @uses do_action() Calls the 'login_head' for outputting HTML in the Log In
 *		header.
 * @uses apply_filters() Calls 'login_headerurl' for the top login link.
 * @uses apply_filters() Calls 'login_headertitle' for the top login title.
 * @uses apply_filters() Calls 'login_message' on the message to display in the
 *		header.
 * @uses $error The error global, which is checked for displaying errors.
 *
 * @param string $title Optional. WordPress Log In Page title to display in
 *		<title/> element.
 * @param string $message Optional. Message to display in header.
 * @param WP_Error $wp_error Optional. WordPress Error Object
 */
function login_header($title = 'Log In', $message = '', $wp_error = '') {
	global $error, $is_iphone, $interim_login, $current_site;

	// Don't index any of these forms
	add_filter( 'pre_option_blog_public', '__return_zero' );
	add_action( 'login_head', 'noindex' );

	if ( empty($wp_error) )
		$wp_error = new WP_Error();

	// Shake it!
	$shake_error_codes = array( 'empty_password', 'empty_email', 'invalid_email', 'invalidcombo', 'empty_username', 'invalid_username', 'incorrect_password' );
	$shake_error_codes = apply_filters( 'shake_error_codes', $shake_error_codes );

	if ( $shake_error_codes && $wp_error->get_error_code() && in_array( $wp_error->get_error_code(), $shake_error_codes ) )
		add_action( 'login_head', 'wp_shake_js', 12 );

	?>
<!doctype html> 
<html lang="en"> 
<head> 
  <meta charset="utf-8"> 
  <title><?php bloginfo('name'); ?> | <?php echo $title; ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src='<?= get_bloginfo('stylesheet_directory'); ?>/js/libs/jquery-1.6.2.min.js'></script>
<?php
	do_action( 'login_enqueue_scripts' );
	do_action( 'login_head' ); 
?>
<script type="text/javascript">


<?php
error_log("attempting wifi login",0);


// uses the err_flag sent from the controller in the query string rather than a cookie value
// if the error_flag is set other than 0 we know that the mactest failed.
global $firstrun;

if (isset($_GET['statusCode'])){
	if ($_GET['statusCode']=5){
		$firstrun = false;
		echo "testval=true;";
	} else {
		// could add additional status codes
		/*
		Status Code 1: "You are already logged in. No further action is required on your part."

		Status Code 2: "You are not configured to authenticate against web portal. No further action is required on your part."
		
		Status Code 3: "The username specified cannot be used at this time. Perhaps the username is already logged into the system?"
		
		Status Code 4: "You have been excluded."
		
		Status Code 5: "The User Name and Password combination you have entered is invalid. Please try again."
		*/

		$firstrun = true;
		echo "testval=false;";
	}
	
} else {
	$firstrun = true;
	echo "testval=false;";
};

/* Disabling for test with stuart 
global $firstrun;
if ($_COOKIE["grindwifi"]=="secondrun"){
// we should be able to test if error flag err_flag is set on the get (need someone in the space to test this)
// if it is set to a value other than 0 then there is an error, that means it is a true second run.
// other wise it could just be a timeout

	$firstrun = true;
	echo "testval=true;";
} else {
	$firstrun = false;
	echo "testval=false;";
	//setcookie("grindwifi", 'secondrun',time()+120);  // use this to set a 2 minute timeout. Helps if the browser window is still open in another tab, they might get asked to login again.  However if we are testing the err flag and it is clean yet the cookie is set, that means that it is actually first run and testval should be true!!
	setcookie("grindwifi", 'secondrun');
	//echo "setCookie('grindwifi','secondrun');";
};
*/
?>
</script>

</head>
<body class="login">
  <div id="main" role="main">
    <h1><img src="<?= get_bloginfo('stylesheet_directory'); ?>/img/grind-login-logo.png" width="155" height="64" alt="grind"></h1>
    <h2>Digital check in</h2>

<?
	$message = apply_filters('login_message', $message);
	if ( !empty( $message ) ) echo $message . "\n";

	// Incase a plugin uses $error rather than the $errors object
	if ( !empty( $error ) ) {
		$wp_error->add('error', $error);
		unset($error);
	}

	if ( $wp_error->get_error_code() ) {
		$errors = '';
		$messages = '';
		foreach ( $wp_error->get_error_codes() as $code ) {
			$severity = $wp_error->get_error_data($code);
			foreach ( $wp_error->get_error_messages($code) as $error ) {
				if ( 'message' == $severity )
					$messages .= '	' . $error . "<br />\n";
				else
					$errors .= '	' . $error . "<br />\n";
			}
		}
		if ( !empty($errors) )
			echo '<div id="login_error">' . apply_filters('login_errors', $errors) . "</div>\n";
		if ( !empty($messages) )
			echo '<p class="message">' . apply_filters('login_messages', $messages) . "</p>\n";
	}
} // End of login_header()


/**
 * Outputs the footer for the login page.
 *
 * @param string $input_id Which input to auto-focus
 */
function login_footer($input_id = '') {
	echo "</div>\n";

	if ( !empty($input_id) ) {
?>
<script type="text/javascript">
try{document.getElementById('<?php echo $input_id; ?>').focus();}catch(e){}
if(typeof wpOnload=='function')wpOnload();
</script>
<?php
	}

do_action('login_footer'); ?>
    <div id="push"></div> 
  </div><!--#main-->
  <div id="footer"> 
    <ul class="clearfix"> 
      <li>A collaboration by</li> 
      <li><a class="ir p1" href="http://www.cocollective.com" target="_blank">CO:</a></li> 
      <li><a class="ir p2" href="http://www.behance.net" target="_blank">Behance</a></li> 
      <li><a class="ir p3" href="http://www.coolhunting.com" target="_blank">Cool Hunting</a></li> 
      <li><a class="ir p4" href="http://breakfastny.com" target="_blank">Breakfast</a></li> 
      <li><a class="ir p5" href="http://magicandmight.com" target="_blank">Magic+Might</a></li> 
      <!--<li><a class="ir p6" href="http://vincentbandy.com" target="_blank">VB</a></li>-->
    </ul> 
  </div>
</body>
</html>
<?php
}

function wp_shake_js() {
	global $is_iphone;
	if ( $is_iphone )
		return;
?>
<script type="text/javascript">
addLoadEvent = function(func){if(typeof jQuery!="undefined")jQuery(document).ready(func);else if(typeof wpOnload!='function'){wpOnload=func;}else{var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}};
function s(id,pos){g(id).left=pos+'px';}
function g(id){return document.getElementById(id).style;}
function shake(id,a,d){c=a.shift();s(id,c);if(a.length>0){setTimeout(function(){shake(id,a,d);},d);}else{try{g(id).position='static';wp_attempt_focus();}catch(e){}}}
addLoadEvent(function(){ var p=new Array(15,30,15,0,-15,-30,-15,0);p=p.concat(p.concat(p));var i=document.forms[0].id;g(i).position='relative';shake(i,p,20);});
</script>
<?php
}

/**
 * Handles sending password retrieval email to user.
 *
 * @uses $wpdb WordPress Database object
 *
 * @return bool|WP_Error True: when finish. WP_Error on error
 */
function retrieve_password() {
	global $wpdb, $current_site;

	$errors = new WP_Error();

	if ( empty( $_POST['user_login'] ) && empty( $_POST['user_email'] ) )
		$errors->add('empty_username', __('<strong>ERROR</strong>: Enter a username or e-mail address.'));

	if ( strpos($_POST['user_login'], '@') ) {
		$user_data = get_user_by_email(trim($_POST['user_login']));
		if ( empty($user_data) )
			$errors->add('invalid_email', __('<strong>ERROR</strong>: There is no user registered with that email address.'));
	} else {
		$login = trim($_POST['user_login']);
		$user_data = get_userdatabylogin($login);
	}

	do_action('lostpassword_post');

	if ( $errors->get_error_code() )
		return $errors;

	if ( !$user_data ) {
		$errors->add('invalidcombo', __('<strong>ERROR</strong>: Invalid username or e-mail.'));
		return $errors;
	}

	// redefining user_login ensures we return the right case in the email
	$user_login = $user_data->user_login;
	$user_email = $user_data->user_email;

	do_action('retreive_password', $user_login);  // Misspelled and deprecated
	do_action('retrieve_password', $user_login);

	$allow = apply_filters('allow_password_reset', true, $user_data->ID);

	if ( ! $allow )
		return new WP_Error('no_password_reset', __('Password reset is not allowed for this user'));
	else if ( is_wp_error($allow) )
		return $allow;

	$key = $wpdb->get_var($wpdb->prepare("SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $user_login));
	if ( empty($key) ) {
		// Generate something random for a key...
		$key = wp_generate_password(20, false);
		do_action('retrieve_password_key', $user_login, $key);
		// Now insert the new md5 key into the db
		$wpdb->update($wpdb->users, array('user_activation_key' => $key), array('user_login' => $user_login));
	}
	$message = __('Someone requested that the password be reset for the following account:') . "\r\n\r\n";
	$message .= network_site_url() . "\r\n\r\n";
	$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
	$message .= __('If this was a mistake, just ignore this email and nothing will happen.') . "\r\n\r\n";
	$message .= __('To reset your password, visit the following address:') . "\r\n\r\n";
	$message .= '<' . network_site_url("wp-login-wifi.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . ">\r\n";

	if ( is_multisite() )
		$blogname = $GLOBALS['current_site']->site_name;
	else
		// The blogname option is escaped with esc_html on the way into the database in sanitize_option
		// we want to reverse this for the plain text arena of emails.
		$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	$title = sprintf( __('[%s] Password Reset'), $blogname );

	$title = apply_filters('retrieve_password_title', $title);
	$message = apply_filters('retrieve_password_message', $message, $key);

	if ( $message && !wp_mail($user_email, $title, $message) )
		wp_die( __('The e-mail could not be sent.') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function...') );

	return true;
}

/**
 * Retrieves a user row based on password reset key and login
 *
 * @uses $wpdb WordPress Database object
 *
 * @param string $key Hash to validate sending user's password
 * @param string $login The user login
 *
 * @return object|WP_Error
 */
function check_password_reset_key($key, $login) {
	global $wpdb;

	$key = preg_replace('/[^a-z0-9]/i', '', $key);

	if ( empty( $key ) || !is_string( $key ) )
		return new WP_Error('invalid_key', __('Invalid key'));

	if ( empty($login) || !is_string($login) )
		return new WP_Error('invalid_key', __('Invalid key'));

	$user = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->users WHERE user_activation_key = %s AND user_login = %s", $key, $login));

	if ( empty( $user ) )
		return new WP_Error('invalid_key', __('Invalid key'));

	return $user;
}

/**
 * Handles resetting the user's password.
 *
 * @uses $wpdb WordPress Database object
 *
 * @param string $key Hash to validate sending user's password
 */
function reset_password($user, $new_pass) {
	do_action('password_reset', $user, $new_pass);

	wp_set_password($new_pass, $user->ID);

	wp_password_change_notification($user);
}

/**
 * Handles registering a new user.
 *
 * @param string $user_login User's username for logging in
 * @param string $user_email User's email address to send password and add
 * @return int|WP_Error Either user's ID or error on failure.
 */
function register_new_user( $user_login, $user_email ) {
	$errors = new WP_Error();

	$sanitized_user_login = sanitize_user( $user_login );
	$user_email = apply_filters( 'user_registration_email', $user_email );

	// Check the username
	if ( $sanitized_user_login == '' ) {
		$errors->add( 'empty_username', __( '<strong>ERROR</strong>: Please enter a username.' ) );
	} elseif ( ! validate_username( $user_login ) ) {
		$errors->add( 'invalid_username', __( '<strong>ERROR</strong>: This username is invalid because it uses illegal characters. Please enter a valid username.' ) );
		$sanitized_user_login = '';
	} elseif ( username_exists( $sanitized_user_login ) ) {
		$errors->add( 'username_exists', __( '<strong>ERROR</strong>: This username is already registered, please choose another one.' ) );
	}

	// Check the e-mail address
	if ( $user_email == '' ) {
		$errors->add( 'empty_email', __( '<strong>ERROR</strong>: Please type your e-mail address.' ) );
	} elseif ( ! is_email( $user_email ) ) {
		$errors->add( 'invalid_email', __( '<strong>ERROR</strong>: The email address isn&#8217;t correct.' ) );
		$user_email = '';
	} elseif ( email_exists( $user_email ) ) {
		$errors->add( 'email_exists', __( '<strong>ERROR</strong>: This email is already registered, please choose another one.' ) );
	}

	do_action( 'register_post', $sanitized_user_login, $user_email, $errors );

	$errors = apply_filters( 'registration_errors', $errors, $sanitized_user_login, $user_email );

	if ( $errors->get_error_code() )
		return $errors;

	$user_pass = wp_generate_password( 12, false);
	$user_id = wp_create_user( $sanitized_user_login, $user_pass, $user_email );
	if ( ! $user_id ) {
		$errors->add( 'registerfail', sprintf( __( '<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !' ), get_option( 'admin_email' ) ) );
		return $errors;
	}

	update_user_option( $user_id, 'default_password_nag', true, true ); //Set up the Password change nag.

	wp_new_user_notification( $user_id, $user_pass );

	return $user_id;
}

//
// Main
//

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'login';
$errors = new WP_Error();

if ( isset($_GET['key']) )
	$action = 'resetpass';

// validate action so as to default to the login screen
if ( !in_array($action, array('logout', 'lostpassword', 'retrievepassword', 'resetpass', 'rp', 'register', 'login'), true) && false === has_filter('login_form_' . $action) )
	$action = 'login';

nocache_headers();

header('Content-Type: '.get_bloginfo('html_type').'; charset='.get_bloginfo('charset'));

if ( defined('RELOCATE') ) { // Move flag is set
	if ( isset( $_SERVER['PATH_INFO'] ) && ($_SERVER['PATH_INFO'] != $_SERVER['PHP_SELF']) )
		$_SERVER['PHP_SELF'] = str_replace( $_SERVER['PATH_INFO'], '', $_SERVER['PHP_SELF'] );

	$schema = is_ssl() ? 'https://' : 'http://';
	if ( dirname($schema . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']) != get_option('siteurl') )
		update_option('siteurl', dirname($schema . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']) );
}

//Set a cookie now to see if they are supported by the browser.
setcookie(TEST_COOKIE, 'WP Cookie check', 0, COOKIEPATH, COOKIE_DOMAIN);
if ( SITECOOKIEPATH != COOKIEPATH )
	setcookie(TEST_COOKIE, 'WP Cookie check', 0, SITECOOKIEPATH, COOKIE_DOMAIN);

// allow plugins to override the default actions, and to add extra actions if they want
do_action('login_form_' . $action);

$http_post = ('POST' == $_SERVER['REQUEST_METHOD']);
switch ($action) {

case 'logout' :
	check_admin_referer('log-out');
	wp_logout();

	$redirect_to = !empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : 'wp-login-wifi.php?loggedout=true';
	wp_safe_redirect( $redirect_to );
	exit();

break;

case 'lostpassword' :
case 'retrievepassword' :

	if ( $http_post ) {
		$errors = retrieve_password();
		if ( !is_wp_error($errors) ) {
			$redirect_to = !empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : 'wp-login-wifi.php?checkemail=confirm';
			wp_safe_redirect( $redirect_to );
			exit();
		}
	}

	if ( isset($_GET['error']) && 'invalidkey' == $_GET['error'] ) $errors->add('invalidkey', __('Sorry, that key does not appear to be valid.'));
	$redirect_to = apply_filters( 'lostpassword_redirect', !empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '' );

	do_action('lost_password');
	login_header(__('Lost Password'), '<p class="message">' . __('Please enter your username or email address. You will receive a link to create a new password via email.') . '</p>', $errors);

	$user_login = isset($_POST['user_login']) ? stripslashes($_POST['user_login']) : '';

?>

<form name="lostpasswordform" id="lostpasswordform" action="<?php echo site_url('wp-login-wifi.php?action=lostpassword', 'login_post') ?>" method="post">
	<p>
		<label><?php _e('Username or E-mail:') ?><br />
		<input type="text" name="user_login" id="user_login" class="input" value="<?php echo esc_attr($user_login); ?>" size="20" tabindex="10" /></label>
	</p>
<?php do_action('lostpassword_form'); ?>
	<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>" />
	<p class="submit"><button type="submit" name="wp-submit" id="wp-submit" class="btn" tabindex="100"><?php esc_attr_e('Get New Password'); ?></button></p>
</form>

<p id="nav">
<a href="<?php echo site_url('wp-login-wifi.php', 'login') ?>"><?php _e('Log in') ?></a>
<?php if (get_option('users_can_register')) : ?>
 | <a href="<?php echo site_url('wp-login-wifi.php?action=register', 'login') ?>"><?php _e('Register') ?></a>
<?php endif; ?>
</p>

<?php
login_footer('user_login');
break;

case 'resetpass' :
case 'rp' :
	$user = check_password_reset_key($_GET['key'], $_GET['login']);

	if ( is_wp_error($user) ) {
		wp_redirect( site_url('wp-login-wifi.php?action=lostpassword&error=invalidkey') );
		exit;
	}

	$errors = '';

	if ( isset($_POST['pass1']) && $_POST['pass1'] != $_POST['pass2'] ) {
		$errors = new WP_Error('password_reset_mismatch', __('The passwords do not match.'));
	} elseif ( isset($_POST['pass1']) && !empty($_POST['pass1']) ) {
		reset_password($user, $_POST['pass1']);
		login_header(__('Password Reset'), '<p class="message reset-pass">' . __('Your password has been reset.') . ' <a href="' . site_url('wp-login-wifi.php', 'login') . '">' . __('Log in') . '</a></p>');
		login_footer();
		exit;
	}

	wp_enqueue_script('utils');
	wp_enqueue_script('user-profile');

	login_header(__('Reset Password'), '<p class="message reset-pass">' . __('Enter your new password below.') . '</p>', $errors );

?>
<form name="resetpassform" id="resetpassform" action="<?php echo site_url('wp-login-wifi.php?action=resetpass&key=' . urlencode($_GET['key']) . '&login=' . urlencode($_GET['login']), 'login_post') ?>" method="post">
	<input type="hidden" id="user_login" value="<?php echo esc_attr( $_GET['login'] ); ?>" autocomplete="off" />

	<p>
		<label><?php _e('New password') ?><br />
		<input type="password" name="pass1" id="pass1" class="input" size="20" value="" autocomplete="off" /></label>
	</p>
	<p>
		<label><?php _e('Confirm new password') ?><br />
		<input type="password" name="pass2" id="pass2" class="input" size="20" value="" autocomplete="off" /></label>
	</p>

	<div id="pass-strength-result" class="hide-if-no-js"><?php _e('Strength indicator'); ?></div>
	<p class="description indicator-hint"><?php _e('Hint: The password should be at least seven characters long. To make it stronger, use upper and lower case letters, numbers and symbols like ! " ? $ % ^ &amp; ).'); ?></p>

	<br class="clear" />
	<p class="submit"><input type="submit" name="wp-submit" id="wp-submit" class="button-primary" value="<?php esc_attr_e('Reset Password'); ?>" tabindex="100" /></p>
</form>

<p id="nav">
<a href="<?php echo site_url('wp-login-wifi.php', 'login') ?>"><?php _e('Log in') ?></a>
<?php if (get_option('users_can_register')) : ?>
 | <a href="<?php echo site_url('wp-login-wifi.php?action=register', 'login') ?>"><?php _e('Register') ?></a>
<?php endif; ?>
</p>

<?php
login_footer('user_pass');
break;

case 'register' :
	if ( is_multisite() ) {
		// Multisite uses wp-signup.php
		wp_redirect( apply_filters( 'wp_signup_location', site_url('wp-signup.php') ) );
		exit;
	}

	if ( !get_option('users_can_register') ) {
		wp_redirect( site_url('wp-login-wifi.php?registration=disabled') );
		exit();
	}

	$user_login = '';
	$user_email = '';
	if ( $http_post ) {
		$user_login = $_POST['user_login'];
		$user_email = $_POST['user_email'];
		$errors = register_new_user($user_login, $user_email);
		if ( !is_wp_error($errors) ) {
			$redirect_to = !empty( $_POST['redirect_to'] ) ? $_POST['redirect_to'] : 'wp-login-wifi.php?checkemail=registered';
			wp_safe_redirect( $redirect_to );
			exit();
		}
	}

	$redirect_to = apply_filters( 'registration_redirect', !empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '' );
	login_header(__('Registration Form'), '<p class="message register">' . __('Register For This Site') . '</p>', $errors);
?>

<form name="registerform" id="registerform" action="<?php echo site_url('wp-login-wifi.php?action=register', 'login_post') ?>" method="post">
	<p>
		<label><?php _e('Username') ?><br />
		<input type="text" name="user_login" id="user_login" class="input" value="<?php echo esc_attr(stripslashes($user_login)); ?>" size="20" tabindex="10" /></label>
	</p>
	<p>
		<label><?php _e('E-mail') ?><br />
		<input type="text" name="user_email" id="user_email" class="input" value="<?php echo esc_attr(stripslashes($user_email)); ?>" size="25" tabindex="20" /></label>
	</p>
<?php do_action('register_form'); ?>
	<p id="reg_passmail"><?php _e('A password will be e-mailed to you.') ?></p>
	<br class="clear" />
	<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>" />
	<p class="submit"><input type="submit" name="wp-submit" id="wp-submit" class="button-primary" value="<?php esc_attr_e('Register'); ?>" tabindex="100" /></p>
</form>

<p id="nav">
<a href="<?php echo site_url('wp-login-wifi.php', 'login') ?>"><?php _e('Log in') ?></a> |
<a href="<?php echo site_url('wp-login-wifi.php?action=lostpassword', 'login') ?>" title="<?php _e('Password Lost and Found') ?>"><?php _e('Lost your password?') ?></a>
</p>

<?php
login_footer('user_login');
break;

case 'login' :
default:
	$secure_cookie = '';
	$interim_login = isset($_REQUEST['interim-login']);

	// If the user wants ssl but the session is not ssl, force a secure cookie.
	if ( !empty($_POST['log']) && !force_ssl_admin() ) {
		$user_name = sanitize_user($_POST['log']);
		if ( $user = get_userdatabylogin($user_name) ) {
			if ( get_user_option('use_ssl', $user->ID) ) {
				$secure_cookie = true;
				force_ssl_admin(true);
			}
		}
	}

	if ( isset( $_REQUEST['redirect_to'] ) ) {
		$redirect_to = $_REQUEST['redirect_to'];
		// Redirect to https if user wants ssl
		if ( $secure_cookie && false !== strpos($redirect_to, 'wp-admin') )
			$redirect_to = preg_replace('|^http://|', 'https://', $redirect_to);
	} else {
		$redirect_to = admin_url();
	}

	$reauth = empty($_REQUEST['reauth']) ? false : true;

	// If the user was redirected to a secure login form from a non-secure admin page, and secure login is required but secure admin is not, then don't use a secure
	// cookie and redirect back to the referring non-secure admin page.  This allows logins to always be POSTed over SSL while allowing the user to choose visiting
	// the admin via http or https.
	if ( !$secure_cookie && is_ssl() && force_ssl_login() && !force_ssl_admin() && ( 0 !== strpos($redirect_to, 'https') ) && ( 0 === strpos($redirect_to, 'http') ) )
		$secure_cookie = false;

	$user = wp_signon('', $secure_cookie);

	$redirect_to = apply_filters('login_redirect', $redirect_to, isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '', $user);

	if ( !is_wp_error($user) && !$reauth ) {
		if ( $interim_login ) {
			$message = '<p class="message">' . __('You have logged in successfully.') . '</p>';
			login_header( '', $message ); ?>
			<script type="text/javascript">setTimeout( function(){window.close()}, 8000);</script>
			<p class="alignright">
			<input type="button" class="button-primary" value="<?php esc_attr_e('Close'); ?>" onclick="window.close()" /></p>
			</div></body></html>
<?php		exit;
		}

		if ( ( empty( $redirect_to ) || $redirect_to == 'wp-admin/' || $redirect_to == admin_url() ) ) {
			// If the user doesn't belong to a blog, send them to user admin. If the user can't edit posts, send them to their profile.
			if ( is_multisite() && !get_active_blog_for_user($user->id) )
				$redirect_to = user_admin_url();
			elseif ( is_multisite() && !$user->has_cap('read') )
				$redirect_to = get_dashboard_url( $user->id );
			elseif ( !$user->has_cap('edit_posts') )
				$redirect_to = admin_url('profile.php');
		}
		wp_safe_redirect($redirect_to);
		exit();
	}

	$errors = $user;
	// Clear errors if loggedout is set.
	if ( !empty($_GET['loggedout']) || $reauth )
		$errors = new WP_Error();

	// If cookies are disabled we can't log in even with a valid user+pass
	if ( isset($_POST['testcookie']) && empty($_COOKIE[TEST_COOKIE]) )
		$errors->add('test_cookie', __("<strong>ERROR</strong>: Cookies are blocked or not supported by your browser. You must <a href='http://www.google.com/cookies.html'>enable cookies</a> to use WordPress."));

	// Some parts of this script use the main login form to display a message
	if		( isset($_GET['loggedout']) && TRUE == $_GET['loggedout'] )
		$errors->add('loggedout', __('You are now logged out.'), 'message');
	elseif	( isset($_GET['registration']) && 'disabled' == $_GET['registration'] )
		$errors->add('registerdisabled', __('User registration is currently not allowed.'));
	elseif	( isset($_GET['checkemail']) && 'confirm' == $_GET['checkemail'] )
		$errors->add('confirm', __('Check your e-mail for the confirmation link.'), 'message');
	elseif	( isset($_GET['checkemail']) && 'newpass' == $_GET['checkemail'] )
		$errors->add('newpass', __('Check your e-mail for your new password.'), 'message');
	elseif	( isset($_GET['checkemail']) && 'registered' == $_GET['checkemail'] )
		$errors->add('registered', __('Registration complete. Please check your e-mail.'), 'message');
	elseif	( $interim_login )
		$errors->add('expired', __('Your session has expired. Please log-in again.'), 'message');

	// Clear any stale cookies.
	if ( $reauth )
		wp_clear_auth_cookie();

	login_header(__('Log In'), '', $errors);

	if ( isset($_POST['log']) )
		$user_login = ( 'incorrect_password' == $errors->get_error_code() || 'empty_password' == $errors->get_error_code() ) ? esc_attr(stripslashes($_POST['log'])) : '';
	$rememberme = ! empty( $_POST['rememberme'] );
?>


<?  
global $firstrun;

if ($firstrun == true){ ?>
		<div id="block">
			<div id="largeloader" style="display:block;width:200px;height:200px">
				
			</div>
		</div>
<?
	} else {
?>  
<form name="loginform" id="loginform" method="post" action="<?=site_url('ci/admin/usermanagement/authenticate')?>">
	<p>
	 <label for="user_login">Email address</label>
	 <input type="text" name="u" id="username" class="input" value="<?php echo esc_attr($user_login); ?>" size="20" tabindex="10" />
  </p>
	<p>
	 <label for="password">Password</label>
	 <input type="password" name="p" id="user_pass" class="input" value="" size="20" tabindex="20" />
  </p>
<?php do_action('login_form'); ?>
  <p><input type="checkbox" id="terms" name="terms"><label for="terms" class="label-checkbox" tabindex="90">I Agree to the <a href="#" class="pop-terms">Terms &amp; Conditions</a></label></p>
	<!--<p class="forgetmenot"><input name="rememberme" type="checkbox" id="rememberme" value="forever" tabindex="90"<?php checked( $rememberme ); ?> /> <?php esc_attr_e('Remember Me'); ?></label></p>-->
	<p>We will check you into Grind now.<br>Daily members will be charged a Daily Rate: $35.<br>Includes WiFi, and access to Grind for the day.</p>
	<p class="submit">
		<button type="submit" name="submit" id="submit" class="btn" tabindex="100" />Sign in &amp; conquer</button>
<?php	if ( $interim_login ) { ?>
		<input type="hidden" name="interim-login" value="1" />
<?php	} else { ?>
		<input type="hidden" name="redirect_to" value="<?php echo esc_attr($redirect_to); ?>" />
<?php 	} ?>
		<input type="hidden" name="testcookie" value="1" />
	</p>
</form>



<?php if ( !$interim_login ) { ?>
<p><a href="<?php echo site_url('wp-login-wifi.php?action=lostpassword', 'login') ?>" title="<?php _e('Password Lost and Found') ?>"><?php _e('Forgot your password?') ?></a></p>
<p>Not a Member, <a href="http://grindspaces.com/#apply-form">Apply Now</a></p>
<?php } 

}// end firstrunif
?>
<form name="wifilogin" id="wifilogin" action="<?=$_GET['switch_url'] ; ?>" method="post">
<input type="hidden" name="buttonClicked" value="4" />
<input type="hidden" name="redirect_url" value="<?=$_GET['redirect'] ; ?>" />
<input type="hidden" name="err_flag" value="0" />
<input type="hidden" id="wifi_user" name="username" value="mactest" />
<input type="password" id="wifi_pass"  style="display:none" name="password" value="mactest" />
</form>
    <div id="push"></div> 
  </div><!--#main-->
  <div id="footer"> 
    <ul class="clearfix"> 
      <li>A collaboration by</li> 
      <li><a class="ir p1" href="http://www.cocollective.com" target="_blank">CO:</a></li> 
      <li><a class="ir p2" href="http://www.behance.net" target="_blank">Behance</a></li> 
      <li><a class="ir p3" href="http://www.coolhunting.com" target="_blank">Cool Hunting</a></li> 
      <li><a class="ir p4" href="http://breakfastny.com" target="_blank">Breakfast</a></li> 
      <li><a class="ir p5" href="http://magicandmight.com" target="_blank">Magic+Might</a></li> 
      <!--<li><a class="ir p6" href="http://vincentbandy.com" target="_blank">VB</a></li>-->
    </ul> 
  </div>

<?php // TERMS AND CONDITIONS POP UP
$page_id = "399"; // 123 should be replaced with a specific Page's id from your site, which you can find by mousing over the link to edit that Page on the Manage Pages admin page. The id will be embedded in the query string of the URL, e.g. page.php?action=edit&post=123.
$page_data = get_page( $page_id ); // You must pass in a variable to the get_page function. If you pass in a value (e.g. get_page ( 123 ); ), WordPress will generate an error. 

$content = apply_filters('the_content', $page_data->post_content); // Get Content and retain Wordpress filters such as paragraph tags. Origin from: http://wordpress.org/support/topic/get_pagepost-and-no-paragraphs-problem
echo $content;
?>

<script type="text/javascript">
function wp_attempt_focus(){
setTimeout( function(){ try{
<?php if ( $user_login || $interim_login ) { ?>
d = document.getElementById('user_pass');
d.value = '';
<?php } else { ?>
d = document.getElementById('user_login');
<?php if ( 'invalid_username' == $errors->get_error_code() ) { ?>
if( d.value != '' )
d.value = '';
<?php
}
}?>
d.focus();
d.select();
} catch(e){}
}, 200);
}

<?php if ( !$error ) { ?>
wp_attempt_focus();
<?php } ?>
if(typeof wpOnload=='function')wpOnload();
</script>
<?php do_action( 'login_footer' ); ?>

</body>
</html>
<?php

break;
} // end action switch
?>