<?php

require_once(MEMBERINCLUDEPATH.'/_siteconfig.php');
require_once(MEMBERINCLUDEPATH.'ci/application/libraries/recurlyaccess.php');
require_once(MEMBERINCLUDEPATH.'ci/application/libraries/enumerations.php');

//ini_set("session.cookie_domain", ".grindspaces.com");

if ( function_exists('register_sidebar') )
    register_sidebar();

//Add user's info to session
if ( is_user_logged_in() ) {
  
  if (!isset($_COOKIE['wpauth'])) {
	get_currentuserinfo();
	setcookie("wpauth", $current_user->user_firstname, time()+60*60*24*14, "", substr($_SERVER['SERVER_NAME'], strpos($_SERVER['SERVER_NAME'], '.')));
  }
  
  if(!isset($_SESSION['wpuser'])) {
	get_currentuserinfo();
	session_start();
	$role = "subscriber";
	if (current_user_can( 'administrator' ))
	  $role = "admin";

	$result = $wpdb->get_row("SELECT id, first_name, last_name, rfid FROM user where wp_users_id = " . $current_user->ID );
	
	$_SESSION["wpuser"] = array(
				"id"=>$result->id,
				"wp_users_id"=>$current_user->ID,
				"user_login"=>$current_user->user_login,
				"wp_role"=>$role,
				"first_name"=>$result->first_name,
				"last_name"=>$result->last_name,
				"rfid"=>$result->rfid
	);
	$cookiedata = array(
				"id"=>$result->id,
				"wp_users_id"=>$current_user->ID,
				"user_login"=>$current_user->user_login,
				"wp_role"=>$role,
				"first_name"=>$result->first_name,
				"last_name"=>$result->last_name,
				"rfid"=>$result->rfid
	);
	setGalleryCookie($cookiedata);
	
  }
}

function expireAuthCookieForPublicSite() {
  setcookie("wpauth", "", time()-3600, "/", substr($_SERVER['SERVER_NAME'], strpos($_SERVER['SERVER_NAME'], '.')));
  setcookie("gauth", "", time()-3600, "/", substr($_SERVER['SERVER_NAME'], strpos($_SERVER['SERVER_NAME'], '.')));
}
function setGalleryCookie($data){
//	echo var_export($data);
	$data = json_encode($data);
$data = trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, ENCRYPTKEY, $data, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
	setcookie("gauth", $data, time()+60*60*24*14, "", substr($_SERVER['SERVER_NAME'], strpos($_SERVER['SERVER_NAME'], '.')));
}

function fnEncrypt($sValue, $sSecretKey)
{
   return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $sSecretKey, $sValue, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
	
}

function grind_login($login) {
	error_log("grind_login called:".$login,0);
	global $user_ID;
    $user = get_userdatabylogin($login);

	global $wpdb;
    update_usermeta( $user->ID, 'my_last_login', time() );
	if (!isset($_SESSION['wpuser'])){
		session_start();
	}
	$role = "subscriber";
	if (current_user_can( 'administrator' ))
	  $role = "admin"; 

	$result = $wpdb->get_row("SELECT id, first_name, last_name,rfid FROM user where wp_users_id = " . $user->ID );
	$_SESSION['wpuser'] = array(
				"id"=>$result->id,
				"wp_users_id"=>$user->ID,
				"user_login"=>$user->user_login,
				"wp_role"=>$role,
				"first_name"=>$result->first_name,
				"last_name"=>$result->last_name,
				"rfid"=>$result->rfid
				
	);
	$cookiedata = array(
				"id"=>$result->id,
				"wp_users_id"=>$user->ID,
				"user_login"=>$user->user_login,
				"wp_role"=>$role,
				"first_name"=>$result->first_name,
				"last_name"=>$result->last_name,
				"rfid"=>$result->rfid
	);
	
	setGalleryCookie($cookiedata);
	

}

add_action('wp_login','grind_login',20);

function superSaasLogin($login){
    global $wpdb, $current_user, $user_ID;
    $loginUser = get_userdatabylogin($login);
    error_log('begin supersaas login:'.$user->ID);
    $account = 'grindspaces';
    $password = 'grindforever';
    $after = 'http://www.supersaas.com/schedule/grindspaces/TEST_Daily_reservation';
    $user = $wpdb->get_row("select * from user where wp_users_id={$loginUser->ID}");
    $rfid = (isset($user) && isset($user->rfid)) ? $user->rfid : '';
    $url = "https://supersaas.com/api/users";
    $cheksum =  md5("$account$password$loginUser->user_login");
    $data = array(
        'account' => 'grindspaces',
        'id'      => $loginUser->ID.'fk',
        'checksum'=> $cheksum,
        'after'   => $after,
    );
    $userData = array(
        'name'      => htmlspecialchars($loginUser->user_login),
        'full_name' => htmlspecialchars($loginUser->user_firstname . ' ' . $loginUser->user_lastname),
        'email'     => htmlspecialchars($loginUser->user_email),
        'field_1'   => htmlspecialchars($rfid)
    );
    
    $dataString = "";
    foreach($data as $key => $value){
        $dataString .= $key.'='.$value.'&';
    }
    foreach($userData as $key => $value){
        $dataString .= "user[$key]=$value&";
    }
    $dataString = rtrim($dataString,"&");
    error_log($dataString);
    
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_POST, true);
    curl_setopt($ch,CURLOPT_POSTFIELDS, $dataString);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    $result = curl_exec($ch);
    error_log($result);
    curl_close($ch);
}
add_action('wp_login','superSaasLogin',30);

//Override logout to clear session
function grind_logout() {
	session_start();
	session_unset();
	session_destroy();
    wp_clear_auth_cookie();
	expireAuthCookieForPublicSite();
	header( 'Location: '.GRINDPUBLICSITEROOT) ;
	exit;
}
add_action('wp_logout','grind_logout');

//Custom CSS for login page
function custom_login() { 
  echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('template_directory').'/grind-login.css" />';
  echo '<script type="text/javascript" src="'.get_bloginfo('template_directory').'/grind-login.js" ></script>';
}
add_action('login_head', 'custom_login');

//Get location checkins
function grind_get_location_checkins()
{
  global $wpdb;
  $result = $wpdb->get_results("select l.id, l.name as location_name, l.capacity, (select count(*) from v_checkins_today where location_id = l.id) as checkins from location l");
  
  return $result;
}


function grind_get_who_has_checkedin()
{
  global $wpdb;
  $result = $wpdb->get_results("SELECT u.wp_users_id, u.id, cit.id, cit.location_id, l.id FROM user u, v_checkins_today cit, location l WHERE cit.location_id = l.id AND u.id = cit.id");
  return $result;
}

function grind_get_users($ids){
	global $wpdb;
	
	$query = "SELECT * from $wpdb->users u WHERE ";
	for($i = 0; $i< count($ids); $i++){
		if($i > 0){
			$query.= " OR ";
		}
		$query.= " u.ID = ". $ids[$i];
	}	
	
	echo $query;
	exit();
		
//	$r	= $wpdb->get_results
	

}


//Hide Admin Bar
add_filter( 'show_admin_bar', '__return_false' );

// Sign-in from Public Site
add_filter( 'xmlrpc_methods', 'add_grind_xmlrpc_methods' );
function add_grind_xmlrpc_methods( $methods ) {
	$methods['grind.loginRemotely'] = 'loginRemotely';
	$methods['grind.createWordpressUser'] = 'createWordpressUser';
	$methods['grind.setWpUserSession'] = 'setWpUserSession';
	$methods['grind.deleteWordpressUser'] = 'deleteWordpressUser';
	//$methods['grind.isGrindUserLoggedIn'] = 'isGrindUserLoggedIn';
	return $methods;
}


function setWpUserSession($args){
		$email=$args[0];
		$passwordhash=$args[1];
		$result = $wpdb->get_row("SELECT id, user_pass FROM wpmember_users where user_login = '" . $email . "'");
	
		if ($result->user_pass == $passwordhash) {
			$userId = $result->id;
	
			wp_set_current_user($result->id, $email);
			wp_set_auth_cookie($result->id);
			do_action('wp_login', $email);
		}
		
		return true;
}

function loginRemotely($args){
		$username=$args[0];
		$password=$args[1];
		if (!user_pass_ok($username, $password)) {
			//an error occurred, the username and password supplied were not valid
			return false;
		}
		
		$user = get_userdatabylogin($username);
		wp_set_current_user($user->ID, $username);
		wp_set_auth_cookie($user->ID);
		do_action('wp_login', $username);
		
		// no errors occurred, the U&P are good, return true
		return true;
}

function createWordpressUser($args){
try {
  $newWordpressUser = array( 
		    'user_login' => $args[0],
		    'user_pass' => $args[1],
		    'user_nicename' =>  $args[2],
		    'nickname' => $args[3],
		    'first_name' => $args[4],
		    'last_name' => $args[5],
		    'display_name' => $args[6],
		    'user_registered' => $args[7],
		    'user_email' => $args[8]
		);
	// Call wordpress function to create a new user
	$id = wp_insert_user($newWordpressUser);
  
	// test success of the action
	if(is_wp_error($id)){ // insert user failed
		throw new Exception($id->get_error_message());
	} else { 			// insert was successfull
		add_user_meta( $id, 'registerHash', $args[9] , true );
		return $id;
	} // end test
  } catch (exception $e) {
  	return $e->getMessage();
  } // end try
}

function deleteWordpressUser($id){
try {
  	return wp_delete_user($id);
  } catch (exception $e) {
  	return $e->getMessage();
  }
}

/* THESE NEED TO BE OPTIMIZED */
function GetDefaultMonthlyRateCode() {
  global $wpdb;
  $result = $wpdb->get_row('select default_monthly_rate_code from configuration limit 1');
  return $result->default_monthly_rate_code;
}

function GetMonthlyPlan() {
  return Recurly_Plan::get(GetDefaultMonthlyRateCode());
}

function GetDailyRate() {
  global $wpdb;
  $result = $wpdb->get_row('select daily_rate from configuration limit 1');
  return $result->daily_rate;
}

function GetUserRecurlyAccount() {
  session_start();
  return Recurly_Account::get($_SESSION['wpuser']['id']);
}

function GetUserRecurlySubscription() {
  $account = GetUserRecurlyAccount();
  return Recurly_Subscription::get($account->account_code);
}

function AllowMonthlyMemberships() {
  global $wpdb;
  $result = $wpdb->get_row('select allow_monthly_memberships from configuration limit 1');
  return $result->allow_monthly_memberships;
}

function GetLatestsPostsFromPublicSite($num = 3) {
	global $wpdb;
	$results = $wpdb->get_results("
		SELECT p.guid, wp_postmeta.meta_key, DATE_FORMAT(p.post_date, '%m/%e/%Y') as post_date, p.post_title, wp_postmeta.meta_value as display_name
FROM wp_posts p
inner join wp_postmeta on (p.ID = wp_postmeta.post_id and wp_postmeta.meta_key = 'ptm_guest_author_name' )
		AND post_type = 'post' 
		AND post_status = 'publish'
		ORDER BY post_date DESC
		LIMIT ".$num.";" 
	);
	return $results;
}

// shortcode for displaying user name in posts [memberfirstname]
function membername_shortcode( ) {
	
	return $_SESSION['wpuser']['first_name'];
}
add_shortcode( 'memberfirstname', 'membername_shortcode' );



/**
 * Post Sorting For the admin section
 * 
 * Mark / t31os
 *
 * @http://wordpress.stackexchange.com/
 * @http://wordpress.org/support/topic/sort-posts-by-custom-field-in-backend
 */
if(defined('SHOW_META_VALUES') || defined('META_ALT_QUERY') || defined('SHOW_META_MULTIPLE')) die('Sorry, we need these defines, you can\'t have them.');
// Whether to show a posts meta values when filtering by meta key
define('SHOW_META_VALUES',true); // Might possibly slow down load times if viewing lots of posts when active
// Option to use alternate query - ymmv, so use whichever works best
define('META_ALT_QUERY',false); // Set to true if you have problems with the standard query, this may help, it may not..
// When filtering by meta key, this sets whether to show all a posts meta values (for that key) or just a single one.
// NOTE: SHOW_META_VALUES must be set to true in order for this to do anything
define('SHOW_META_MULTIPLE',true); // Set to true for multiple or false for single

function add_meta_column_head($defaults) {
	$defaults['meta'] = ( isset($_GET['meta_key']) && $_GET['meta_key'] != 'All') ? esc_attr($_GET['meta_key']) : '';
	return $defaults;
}

function add_meta_column($column_name,$post_id) {
	$metakey = ( isset($_GET['meta_key']) && $_GET['meta_key'] != 'All') ? esc_attr($_GET['meta_key']) : '';
	if( $column_name == 'meta' && '' != $metakey )
	$val_num = 1;
	echo "\n \t";
	if( SHOW_META_MULTIPLE ) {
		$meta = '<p>' . $val_num++ . ': '. implode( '</p>' . "\n \t" .'<p>' . $val_num++ . ': ',  get_post_meta( $post_id , $metakey , false ) ) . '</p>' . "\n";
	}
	else {
		$meta = '<p>' . $val_num++ . ': '. get_post_meta( $post_id , $metakey , true ) . '</p>' . "\n";
	}
	echo $meta;
	return;
}

function wp_admin_filters($query) {
	global $pagenow;
	if( $query->is_admin && ( 'edit.php' == $pagenow ) ) { 

		$metakey =
			( isset($_GET['meta_key']) && $_GET['meta_key'] != 'All' )
				? esc_attr( $_GET['meta_key'] )
				: '';
		$sortorder =
			( isset($_GET['order']) && $_GET['order'] == 'asc' )
				? 'asc'
				: 'desc';

		if( '' != $metakey ) {
			if( SHOW_META_VALUES ) {
				add_filter( 'manage_posts_columns' , 'add_meta_column_head' );
				add_action( 'manage_posts_custom_column' , 'add_meta_column' , 2, 2);
			}
			$query->set( 'orderby' , 'meta_value' );
			$query->set( 'meta_key' , $metakey );
		}
		if( $sortorder != 'desc' ) {
			$query->set( 'order' , 'asc' );
		}
		else {
			$query->set( 'order' , 'desc' );
		}
	}
	return $query;
}
function wp_admin_filters_dropdowns() {
	global $wpdb;

	$select_meta = '';
	if( !META_ALT_QUERY ) {
		$meta_keys = $wpdb->get_col("SELECT DISTINCT meta_key FROM $wpdb->postmeta WHERE SUBSTRING(meta_key,1,1) != '_'" );
	}
	else {
		$meta_keys = $wpdb->get_col("SELECT meta_key FROM wp_postmeta WHERE meta_key NOT LIKE '\_%' GROUP BY meta_key" );
	}

	if( !empty( $meta_keys ) ) {
		$metakey = ( isset($_GET['meta_key']) && $_GET['meta_key'] != 'All') ? esc_attr($_GET['meta_key']) : '';
		$select_meta .= '<select name="meta_key" id="meta" class="postform">';
		$select_meta .= ( $metakey == ( 'All' || '' ) )
			? "\n \t".'<option selected="selected" value="All">' . __('View all meta') . ' &nbsp;&nbsp;</option>'
			: "\n \t".'<option value="All">' . __('View all meta') . ' &nbsp;&nbsp;</option>';
		foreach($meta_keys as $key => $meta_option) {
			$select_meta .= ( $metakey == $meta_option )
				? "\n \t".'<option selected="selected" value="'.$meta_option.'">'.$meta_option.'</option>'
				: "\n \t".'<option value="'.$meta_option.'">'.$meta_option.'</option>';
		}
		$select_meta .= "\n".'</select>'."\n";
	}
	echo $select_meta;

	$select_order = '<select name="order">';
	$select_order .= "\n \t".'<option value="">&nbsp; --- &nbsp;</option>';
	$select_order .= ( isset($_GET['order']) && $_GET['order'] == 'asc' )
		? "\n \t".'<option selected="selected" value="asc">'.__('Ascending').'</option>'
		: "\n \t".'<option value="asc">'.__('Ascending').'</option>';
	$select_order .= ( isset($_GET['order']) && $_GET['order'] == 'desc' )
		? "\n \t".'<option selected="selected" value="desc">'.__('Descending').'</option>'
		: "\n \t".'<option value="desc">'.__('Descending').'</option>';
	$select_order .= "\n".'</select>'."\n";

	echo $select_order;

	return;
}


add_filter('pre_get_posts', 'wp_admin_filters');
add_action('restrict_manage_posts', 'wp_admin_filters_dropdowns',2);


function register_my_menus() {
  register_nav_menus(
    array('header-menu' => __( 'Global Nav Menu' ),'footer-menu' => __( 'Footer Menu' ),'registration-menu' => __( 'Registration Menu' ) )
  );
}
add_action( 'init', 'register_my_menus' );


/* add a new default avatar */
if ( !function_exists('_addavatar') ) {
	function _addavatar( $avatar_defaults ) {
		$myavatar = get_bloginfo('template_directory') . '/img/avatar_grind.png';
		$avatar_defaults[$myavatar] = 'Grind';
		return $avatar_defaults;
	}
	add_filter( 'avatar_defaults', '_addavatar' );
}

function member_template_redirect()
   {
      global $wp_query;

      if( array_key_exists('author_name', $wp_query->query_vars) && !empty($wp_query->query_vars['author_name']) )
      {
         global $member;
         $member = new WP_User( $wp_query->query_vars["author_name"] );
         if( $member )
         {
            include( TEMPLATEPATH . "/author.php" );
            exit;
         }
      }
   }

add_action( 'template_redirect', 'member_template_redirect' );

function curl_contents($url){
 // from: http://www.jonasjohn.de/snippets/php/curl-example.htm
 
    // is cURL installed yet?
    if (!function_exists('curl_init')){
        die('Sorry cURL is not installed!');
    }
 
    // OK cool - then let's create a new cURL resource handle
    $ch = curl_init();
 
    // Now set some options (most are optional)
 
    // Set URL to download
    curl_setopt($ch, CURLOPT_URL, $url);
 
    // Set a referer
    curl_setopt($ch, CURLOPT_REFERER, "http://members.grindspaces.com");
 
    // User agent
   //  curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
 
    // Include header in result? (0 = yes, 1 = no)
    curl_setopt($ch, CURLOPT_HEADER, 0);
 
    // Should cURL return or print out the data? (true = return, false = print)
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
    // Timeout in seconds
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
 
    // Download the given URL, and return output
    $output = curl_exec($ch);
 
    // Close the cURL resource, and free system resources
    curl_close($ch);
 
    return $output;
}


?>