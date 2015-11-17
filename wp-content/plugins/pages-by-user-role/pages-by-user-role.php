<?php

/**
Plugin Name: Pages by User Role for WordPress
Plugin URI: http://plugins.righthere.com/pages-by-user-role/
Description: Restrict access to different types of content like Pages, Posts, Custom Post Types or Categories depending on which Role the user has. It removes the Pages, Posts or Custom Post Types from search results and blog roll. You can hide Pages and Categories from the menu when the user is not logged in. You can also set a specific redirect URL for users that don't have the required User Role. 
Version: 1.1.5 rev7652
Author: Alberto Lau (RightHere LLC)
Author URI: http://plugins.righthere.com
 **/

define('PUR_VERSION','1.1.5'); 
define('PUR_PATH', plugin_dir_path(__FILE__) ); 
define("PUR_URL", plugin_dir_url(__FILE__) );

load_plugin_textdomain('pur', null, PUR_PATH.'languages' );

require_once(PUR_PATH.'includes/class.plugin_pur.php');
new plugin_pur();
?>