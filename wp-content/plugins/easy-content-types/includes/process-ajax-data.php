<?php

$oldURL = dirname(__FILE__);
$newURL = str_replace(DIRECTORY_SEPARATOR . 'wp-content' . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'easy-content-types' . DIRECTORY_SEPARATOR . 'includes', '', $oldURL);
include($newURL . DIRECTORY_SEPARATOR . 'wp-load.php');

global $wpdb;
$ecpt_db_name = $wpdb->prefix . "ecpt_post_types";
$ecpt_db_tax_name = $wpdb->prefix . "ecpt_taxonomies";
$ecpt_db_meta_name = $wpdb->prefix . "ecpt_meta_boxes";
$ecpt_db_meta_fields_name = $wpdb->prefix . "ecpt_meta_box_fields";

$ecpt_post = (!empty($_POST)) ? true : false;
if($ecpt_post) // if data is being sent
{
	// delete post type
	if(isset($_POST['delete_post_type']))
	{			
		$remove = $wpdb->query("DELETE FROM " . $ecpt_db_name . " WHERE `id`='" . $_POST['delete_post_type'] . "';");			
	}
	// update post type
	if(isset($_POST['update_posttype']))
	{		
		$update = $wpdb->query("UPDATE " . $ecpt_db_name . " SET 
								`name`='" 				. str_replace(' ', '', strtolower($_POST['posttype-name'])) . "', 
								`singular_name`='" 		. $_POST['posttype-singular'] . "', 
								`plural_name`='" 		. $_POST['posttype-plural'] . "', 
								`title`='" 				. $_POST['posttype-title'] . "',
								`editor`='" 			. $_POST['posttype-editor'] . "',
								`author`='" 			. $_POST['posttype-author'] . "',
								`thumbnail`='" 			. $_POST['posttype-thumbnail'] . "',
								`excerpt`='" 			. $_POST['posttype-excerpt'] . "',
								`fields`='" 			. $_POST['posttype-fields'] . "',
								`comments`='" 			. $_POST['posttype-comments'] . "',
								`revisions`='" 			. $_POST['posttype-revisions'] . "',
								`has_archive`='" 		. $_POST['posttype-has_archive'] . "',
								`hierarchical`='" 		. $_POST['posttype-hierarchical'] . "',
								`post_formats`='" 		. $_POST['posttype-post_formats'] . "',
								`show_in_nav_menus`='"	. $_POST['posttype-show_in_nav_menus'] . "',
								`exclude_from_search`='". $_POST['posttype-exclude_from_search'] . "',
								`menu_icon`='"			. $_POST['posttype-menu-icon'] . "',
								`menu_position`='"		. $_POST['posttype-position'] . "',
								`slug`='"				. $_POST['posttype-slug'] . "'
								WHERE `id`='" 			. $_POST['update_posttype'] . "';"
								);			
	}
	
	// delete taxonomy
	if(isset($_POST['delete_taxonomy']))
	{			
		$remove = $wpdb->query("DELETE FROM " . $ecpt_db_tax_name . " WHERE `id`='" . $_POST['delete_taxonomy'] . "';");			
	}
	// update taxonomy
	if(isset($_POST['update_tax']))
	{		
		$update = $wpdb->query("UPDATE " . $ecpt_db_tax_name . " SET 
								`name`='" 				. str_replace(' ', '', $_POST['tax-name']) . "', 
								`page`='" 				. $_POST['tax-page'] . "', 
								`singular_name`='" 		. $_POST['tax-singular'] . "', 
								`plural_name`='" 		. $_POST['tax-plural'] . "', 
								`hierarchical`='" 		. $_POST['tax-hierarchical'] . "',
								`show_tagcloud`='" 		. $_POST['tax-tagcloud'] . "',
								`show_in_nav_menus`='"	. $_POST['tax-show-in-nav'] . "', 
								`slug`='"				. $_POST['tax-slug'] . "' 
								WHERE `id`='" 			. $_POST['update_tax'] . "';"
								);			
	}
	
	// delete metabox
	if(isset($_POST['delete_metabox']))
	{			
		$remove = $wpdb->query("DELETE FROM " . $ecpt_db_meta_name . " WHERE `id`='" . $_POST['delete_metabox'] . "';");			
		$remove_fields = $wpdb->query("DELETE FROM " . $ecpt_db_meta_fields_name . " WHERE `parent`='" . $_POST['delete_meta_fields'] . "';");			
	}
	
	// delete field
	if(isset($_POST['delete_field']))
	{			
		$remove = $wpdb->query("DELETE FROM " . $ecpt_db_meta_fields_name . " WHERE `id`='" . $_POST['delete_field'] . "';");			
	}
	// update field
	if(isset($_POST['update_field']))
	{				
		$update = $wpdb->query("UPDATE " 			. $ecpt_db_meta_fields_name . " SET 
								`name`= '" 			. str_replace(' ', '', strtolower($_POST['field-name'])) . "', 
								`nicename`= '"		. $_POST['field-name'] . "', 
								`description`= '"	. $_POST['field-desc'] . "', 
								`type`='" 			. $_POST['field-type'] . "', 
								`options`='" 		. $_POST['field-options'] . "', 
								`rich_editor`='" 	. $_POST['rich-editor'] . "', 
								`max`='" 			. $_POST['field-max'] . "' 
								WHERE `id`='" 		. $_POST['update_field'] . "';"
								);			
	}
	// update metabox
	if(isset($_POST['update_metabox']))
	{		
		$update = $wpdb->query("UPDATE " 	. $ecpt_db_meta_name . " SET 
								`name`='" 	. str_replace(' ', '', strtolower($_POST['metabox-new-name'])) . "', 
								`nicename`='" . $_POST['metabox-new-name'] . "', 
								`page`='" 	. $_POST['metabox-page'] . "', 
								`context`='" 	. $_POST['metabox-context'] . "',
								`priority`='" . $_POST['metabox-priority'] . "' 
								WHERE `id`='" . $_POST['update_metabox'] . "';"
								);			
	}

}

$action 			= $_POST['action']; 
$updateRecordsArray = $_POST['recordsArray'];

if ($action == "updateRecordsListings"){

	$listingCounter = 1;
	foreach ($updateRecordsArray as $recordIDValue) {

		$wpdb->update($ecpt_db_meta_fields_name, array('list_order' => $listingCounter ), array('id' => $recordIDValue));
		$listingCounter++;
	}
} 

?>