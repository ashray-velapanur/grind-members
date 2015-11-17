<?php
/*
Plugin Name: Easy Content Types
Plugin URI: http://pippinsplugins.com/easy-content-types/
Description: The easiest way to create unlimited custom post types, taxonomies, and meta boxes
Author: Pippin Williamson
Author URI: http://pippinsplugins.com
Version: 2.1.2
*/

/*****************************************
plugin shortname = ECPT
*****************************************/


/*****************************************
global variables
*****************************************/

global $wpdb;

// plugin root folder
global $ecpt_base_dir;
$ecpt_base_dir = WP_PLUGIN_URL . '/' . str_replace(basename( __FILE__), "" ,plugin_basename(__FILE__));

// plugin root file
global $ecpt_base_filse;
$ecpt_base_filse = WP_PLUGIN_URL . '/' . plugin_basename(__FILE__);

// plugin prefix
global $ecpt_prefix;
$ecpt_prefix = 'ecpt_';

// ECPT DB version
global $ecpt_db_version;
$ecpt_db_version = 1.1;

// ECPT DB taxonomy version
global $ecpt_db_tax_version;
$ecpt_db_tax_version = 1.1;

// ECPT DB meta box version
global $ecpt_db_meta_version;
$ecpt_db_meta_version = 1.0;

// ECPT DB meta box fields version
global $ecpt_db_meta_fields_version;
$ecpt_db_meta_fields_version = 1.7;

// name of the ECPT post type database
global $ecpt_db_name;
$ecpt_db_name = $wpdb->prefix . "ecpt_post_types";

// name of the ECPT post type database
global $ecpt_db_tax_name;
$ecpt_db_tax_name = $wpdb->prefix . "ecpt_taxonomies";

// name of the ECPT metabox database
global $ecpt_db_meta_name;
$ecpt_db_meta_name = $wpdb->prefix . "ecpt_meta_boxes";

// name of the ECPT metabox fields database
global $ecpt_db_meta_fields_name;
$ecpt_db_meta_fields_name = $wpdb->prefix . "ecpt_meta_box_fields";

// field types
$field_types = array('text', 'textarea', 'select', 'checkbox', 'radio', 'date', 'upload', 'slider');

// metabox page
$metabox_pages = get_post_types('', 'objects');

// metabox context
$metabox_contexts = array('normal', 'advanced', 'side');

// metabox priority
$metabox_priorities = array('default', 'high', 'core', 'low');

// taxonomy objects
$tax_objects = get_post_types('', 'objects');


// taxonomy attributes
$tax_atts = array('hierarchical', 'show_tagcloud', 'show_in_nav_menus');

// user levels
$user_levels = array('Admin', 'Editor', 'Author');

// load the plugin options
$ecpt_options = get_option( 'ecpt_settings' );


/*****************************************
includes
*****************************************/
include('includes/page-home.php');
include('includes/process-data.php');
include('includes/post-types-admin.php');
include('includes/taxonomies-admin.php');
include('includes/metabox-admin.php');
include('includes/scripts.php');
include('includes/misc-functions.php');
include('includes/register-post-types.php');
include('includes/register-taxonomies.php');
include('includes/register-meta-boxes.php');
include('includes/shortcodes.php');
include('includes/settings.php');
include('includes/export-admin.php');
include('includes/help-page.php');

/*****************************************
Install
*****************************************/

// function to create the DB / Options / Defaults					
function ecpt_options_install() {
   	global $wpdb;
  	global $ecpt_db_name;
  	global $ecpt_db_version;
  	global $ecpt_db_tax_name;
  	global $ecpt_db_tax_version;
  	global $ecpt_db_meta_name;
  	global $ecpt_db_meta_version;
  	global $ecpt_db_meta_fields_name;
  	global $ecpt_db_meta_fields_version;

	// create the ECPT post type database table
	if($wpdb->get_var("show tables like '$ecpt_db_name'") != $ecpt_db_name) 
	{
		$sql = "CREATE TABLE " . $ecpt_db_name . " (
		`id` mediumint(9) NOT NULL AUTO_INCREMENT,
		`name` tinytext NOT NULL,
		`singular_name` tinytext NOT NULL,
		`plural_name` tinytext NOT NULL,
		`hierarchical` tinyint NOT NULL,
		`title` tinyint NOT NULL,
		`editor` tinyint NOT NULL,
		`author` tinyint NOT NULL,
		`thumbnail` tinyint NOT NULL,
		`excerpt` tinyint NOT NULL,
		`fields` tinyint NOT NULL,
		`comments` tinyint NOT NULL,
		`revisions` tinyint NOT NULL,
		`has_archive` tinyint NOT NULL,
		`post_formats` tinyint NOT NULL,
		`page_attributes` tinyint NOT NULL,
		`show_in_nav_menus` tinyint NOT NULL,
		`menu_position` tinyint NOT NULL,
		`menu_icon` tinytext NOT NULL,
		`exclude_from_search` TINYINT NOT NULL,		
		`slug` TINYTEXT NOT NULL,		
		UNIQUE KEY id (id)
		);";
		
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
				
		add_option("ecpt_db_version", $ecpt_db_version);	
	}
	// check to see if the slug column needs added for post types
	if(!$wpdb->query("SELECT `slug` FROM `" . $ecpt_db_name . "`")) 
	{
		$wpdb->query("ALTER TABLE `" . $ecpt_db_name . "` ADD `slug` tinytext");
		update_option('ecpt_db_version', 1.1 );	
	}
	
	// create the ECPT taxonomy database table
	if($wpdb->get_var("show tables like '$ecpt_db_tax_name'") != $ecpt_db_tax_name) 
	{
		$sql = "CREATE TABLE " . $ecpt_db_tax_name . " (
		`id` mediumint(9) NOT NULL AUTO_INCREMENT,
		`name` tinytext NOT NULL,
		`page` tinytext NOT NULL,
		`singular_name` tinytext NOT NULL,
		`plural_name` tinytext NOT NULL,
		`hierarchical` tinyint NOT NULL,
		`show_tagcloud` tinyint NOT NULL,
		`show_in_nav_menus` tinyint NOT NULL,
		`menu_position` tinyint NOT NULL,
		`slug` TINYTEXT NOT NULL,
		UNIQUE KEY id (id)
		);";
		
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
				
		add_option("ecpt_db_tax_version", $ecpt_db_tax_version);	
	}
	// check to see if the slug column needs added for taxonomies
	if(!$wpdb->query("SELECT `slug` FROM `" . $ecpt_db_tax_name . "`")) 
	{
		$wpdb->query("ALTER TABLE `" . $ecpt_db_tax_name . "` ADD `slug` tinytext");
		update_option('ecpt_db_tax_version', 1.1 );	
	}

	// create the ECPT metabox database table
	if($wpdb->get_var("show tables like '$ecpt_db_meta_name'") != $ecpt_db_meta_name) 
	{
		$sql = "CREATE TABLE " . $ecpt_db_meta_name . " (
		`id` mediumint(9) NOT NULL AUTO_INCREMENT,
		`name` tinytext NOT NULL,
		`nicename` tinytext NOT NULL,
		`page` tinytext NOT NULL,
		`context` tinytext NOT NULL,
		`priority` tinytext NOT NULL,
		UNIQUE KEY id (id)
		);";
		
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
				
		add_option("ecpt_db_meta_version", $ecpt_db_meta_version);	
	}
	
	// create the ECPT metabox fields database table
	if($wpdb->get_var("show tables like '$ecpt_db_meta_fields_name'") != $ecpt_db_meta_fields_name) 
	{
		$sql = "CREATE TABLE " . $ecpt_db_meta_fields_name . " (
		`id` mediumint(9) NOT NULL AUTO_INCREMENT,
		`name` tinytext NOT NULL,
		`nicename` tinytext NOT NULL,
		`parent` tinytext NOT NULL,
		`type` tinytext NOT NULL,
		`options` tinytext NOT NULL,
		`description` tinytext NOT NULL,
		`list_order` tinyint NOT NULL,
		`rich_editor` tinyint NOT NULL,
		`max` tinyint NOT NULL,
		UNIQUE KEY id (id)
		);";
		
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
				
		add_option("ecpt_db_meta_fields_version", $ecpt_db_meta_fields_version);	
	}
	// check if the meatbox fields table needs to be upgraded
	if(get_option('ecpt_db_meta_fields_version') < 1.3)
	{
		$wpdb->query("ALTER TABLE " . $ecpt_db_meta_fields_name . " MODIFY `list_order` tinyint");
		update_option('ecpt_db_meta_fields_version', 1.3 );	
	} 
	if(get_option('ecpt_db_meta_fields_version') < 1.5) 
	{
		$wpdb->query("ALTER TABLE " . $ecpt_db_meta_fields_name . " MODIFY `options` mediumtext");
	}
	
	// check if the rich_editor column exists
	if(!$wpdb->query("SELECT `description` FROM `" . $ecpt_db_meta_fields_name . "`")) 
	{
		$wpdb->query("ALTER TABLE `" . $ecpt_db_meta_fields_name . "` ADD `description` tinytext");
	}
	
	// check if the rich_editor column exists
	if(!$wpdb->query("SELECT `rich_editor` FROM `" . $ecpt_db_meta_fields_name . "`")) 
	{
		$wpdb->query("ALTER TABLE `" . $ecpt_db_meta_fields_name . "` ADD `rich_editor` tinyint");
	}
	// check if the max column exists
	if(!$wpdb->query("SELECT `max` FROM `" . $ecpt_db_meta_fields_name . "`")) 
	{
		$wpdb->query("ALTER TABLE `" . $ecpt_db_meta_fields_name . "` ADD `max` tinyint");
	}
	
	update_option('ecpt_db_meta_fields_version', $ecpt_db_meta_fields_version );
}
// run the install scripts upon plugin activation
register_activation_hook(__FILE__,'ecpt_options_install');

// create custom plugin settings menu
add_action('admin_menu', 'ecpt_menu');
function ecpt_menu() {
	global $ecpt_options;
	
	// check the user levels needed to access each page
	
	if($ecpt_options['menu_user_level'] == 'Author') { 
		$menu_level = 'edit_posts'; $posts_level = 'edit_posts'; $tax_level = 'edit_posts'; $meta_level = 'edit_posts';
	} else if ($ecpt_options['menu_user_level'] == 'Editor') { 
		$menu_level = 'edit_pages'; $posts_level = 'edit_pages'; $tax_level = 'edit_pages'; $meta_level = 'edit_pages';
	} else { 
		$menu_level = 'manage_options'; $posts_level = 'manage_options'; $tax_level = 'manage_options'; $meta_level = 'manage_options'; 
	}	
	
	if($ecpt_options['posttype_user_level'] == 'Author' && (($ecpt_options['menu_user_level'] != 'Editor') && ($ecpt_options['menu_user_level'] != 'Admin'))) { 
		$posts_level = 'edit_posts'; 
	} else if ($ecpt_options['posttype_user_level'] == 'Editor' && ($ecpt_options['menu_user_level'] != 'Admin')) { 
		$posts_level = 'edit_pages'; 
	} else { 
		$posts_level = 'manage_options'; 
	}
	
	if($ecpt_options['tax_user_level'] == 'Author' && (($ecpt_options['menu_user_level'] != 'Editor') && ($ecpt_options['menu_user_level'] != 'Admin'))) { 
		$tax_level = 'edit_posts'; 
	} else if ($ecpt_options['tax_user_level'] == 'Editor' && ($ecpt_options['menu_user_level'] != 'Admin')) { 
		$tax_level = 'edit_pages'; 
	} else { 
		$tax_level = 'manage_options'; 
	}
	//echo $tax_level; exit;
	
	if($ecpt_options['metabox_user_level'] == 'Author' && (($ecpt_options['menu_user_level'] != 'Editor') && ($ecpt_options['menu_user_level'] != 'Admin'))) { 
		$meta_level = 'edit_posts'; 
	} else if ($ecpt_options['metabox_user_level'] == 'Editor' && ($ecpt_options['menu_user_level'] != 'Admin')) { 
		$meta_level = 'edit_pages'; 
	} else { 
		$meta_level = 'manage_options'; 
	}
	
	//create new top-level menu
	add_menu_page('Custom Content Types', 'Content Types', $menu_level, __FILE__, 'ecpt_home_page', plugins_url('/includes/images/icon.png', __FILE__));
	
	// add about page -- top level page links here
	add_submenu_page(__FILE__, 'About', 'About',$menu_level, __FILE__, 'ecpt_home_page');	
	
	
	// add custom post types page
	add_submenu_page(__FILE__, 'Post Types', 'Post Types',$posts_level, __FILE__ . '?posttypes', 'ecpt_posttype_manager');	
	
	// add custom taxonomies page
	add_submenu_page(__FILE__, 'Taxonomies', 'Taxonomies',$tax_level, __FILE__ . '?taxonomies', 'ecpt_tax_manager');	

	// add custom metaboxes page
	add_submenu_page(__FILE__, 'MetaBoxes', 'Meta Boxes',$meta_level, __FILE__ . '?metaboxes', 'ecpt_metabox_manager');	
	
	// add settings page
	add_submenu_page(__FILE__, 'Settings', 'Settings','manage_options', __FILE__ . '?settings', 'ecpt_settings_page');		
	
	// add export page
	add_submenu_page(__FILE__, 'Export', 'Export','manage_options', __FILE__ . '?export', 'ecpt_export_page');		
	
	// add help page
	add_submenu_page(__FILE__, 'Help', 'Help',$menu_level, __FILE__ . '?help', 'ecpt_help_page');	
	
}

// add menu links to the plugin entry in the plugins menu
function ecpt_action_links($links, $file) {
    static $this_plugin;
 
    if (!$this_plugin) {
        $this_plugin = plugin_basename(__FILE__);
    }
 
    // check to make sure we are on the correct plugin
    if ($file == $this_plugin) {
	
        $ecpt_links[] = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=easy-content-types/easy-content-types.php?settings">Settings</a>';
		
        $ecpt_links[] = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=easy-content-types/easy-content-types.php?metaboxes">Meta Boxes</a>';
		
        $ecpt_links[] = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=easy-content-types/easy-content-types.php?taxonomies">Taxonomies</a>';
		
        $ecpt_links[] = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=easy-content-types/easy-content-types.php?posttypes">Post Types</a>';
		
		
		
        // add the links to the list of links alread there
		foreach($ecpt_links as $ecpt_link) {
			array_unshift($links, $ecpt_link);
		}
    }
 
    return $links;
}
add_filter('plugin_action_links', 'ecpt_action_links', 10, 2);


?>