<?php
/*
Plugin Name: WP Block Admin
Plugin URI: http://www.callum-macdonald.com/code/wp-block-admin/
Description: Block access to the admin interface based on user capability. The default is only editors and admins are allowed access, but the required capability can be set by editing the plugin file.
Version: 0.2.2
Author: Callum Macdonald
Author URI: http://www.callum-macdonald.com/
*/

// This plugin is released under the GNU GPL licence version 3.0 or later as at:
// http://www.gnu.org/licenses/gpl.txt

// Config
// If you want to change the level of users that are blocked from the admin
// interface you can change this level. For details of the capabilities and what
// they mean see http://codex.wordpress.org/Roles_and_Capabilities#Capabilities
$wpba_required_capability = 'edit_others_posts';
// Here you can change the url to which users are redirected. If it's left blank
// the plugin will redirect to the blog homepage.
$wpba_redirect_to = '';

// To make upgrades easier, you can set these in wp-config.php like:
/*
define('WPBA_REQUIRED_CAPABILITY', 'edit_others_posts');
define('WPBA_REDIRECT_TO' , 'http://enter-url-here');
*/

// End of Config - Edit below here at your own risk

/**
 * CHANGE LOG
 * 0.2.2 * Fixes a bug introduced in 0.2.1 using back ticks instead of quotes. Excludes admin-ajax.php as well as async-upload.php.
 * 0.2.1 Bugfix contributed by Sam hermans http://www.greenfudge.org/
 * 0.2 Added option to set config vars as constants in wp-config.php
 * 0.1.3 Bugfix contributed by Jonah Korbes http://neveradudelikethisone.com/
 */

// Override these values from the constants if they are defined and not empty
if (defined('WPBA_REQUIRED_CAPABILITY'))
	$wpba_required_capability = WPBA_REQUIRED_CAPABILITY;
if (defined('WPBA_REDIRECT_TO'))
	$wpba_redirect_to = WPBA_REDIRECT_TO;

if (!function_exists('wpba_init')) {
	
	function wpba_init() {
		
		// We need the config vars inside the function
		global $wpba_required_capability, $wpba_redirect_to;
		
		// Is this the admin interface?
		if (
			// Look for the presence of /wp-admin/ in the url
			stripos($_SERVER['REQUEST_URI'],'/wp-admin/') !== false
			&&
			// Allow calls to async-upload.php
			stripos($_SERVER['REQUEST_URI'],'async-upload.php') == false
			&&
			// Allow calls to admin-ajax.php
			stripos($_SERVER['REQUEST_URI'],'admin-ajax.php') == false
		) {
			
			// Does the current user fail the required capability level?
			if (!current_user_can($wpba_required_capability)) {
			// If you want to use this plugin on WPMU to stop all users accessing the admin interface, comment out the line above, uncomment the line below.
//			if (!is_site_admin()) {
				
				// Do we need to default to the site homepage?
				if ($wpba_redirect_to == '') { $wpba_redirect_to = get_option('siteurl'); }
				
				// Send a temporary redirect
				wp_redirect($wpba_redirect_to,302);
				
			}
			
		}
		
	}
	
}

// Add the action with maximum priority
add_action('init','wpba_init',0);

?>
