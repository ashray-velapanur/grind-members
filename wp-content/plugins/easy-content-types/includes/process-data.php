<?php

global $ecpt_db_name;
global $ecpt_db_tax_name;
global $ecpt_db_meta_name;
global $ecpt_db_meta_fields_name;

$ecpt_post = (!empty($_POST)) ? true : false;
if($ecpt_post) // if data is being sent
{
	if(isset($_POST['post-type-name'])) 
	{	
		if($_POST['label-single'] != '') { $single = $_POST['label-single']; } else { $single = $_POST['post-type-name']; }
		if($_POST['label-plural'] != '') { $plural = $_POST['label-plural']; } else { $plural = $_POST['post-type-name']; }

		// check for checked options
		if($_POST['options-hierarchial']) 		{ $hierarchical = 1; }
		if($_POST['options-post-formats']) 		{ $post_formats = 1; }
		if($_POST['options-archives']) 			{ $archives = 1; }
		if($_POST['options-nav']) 				{ $nav = 1; }
		
		// check for supports options
		if($_POST['options-title']) 		{ $title = 1; }
		if($_POST['options-editor']) 		{ $editor = 1; }
		if($_POST['options-author']) 		{ $author = 1; }
		if($_POST['options-thumbnail']) 	{ $thumbnail = 1; }
		if($_POST['options-excerpt']) 		{ $excerpt = 1; }
		if($_POST['options-custom-fields']) { $fields = 1; }
		if($_POST['options-comments']) 		{ $comments = 1; }
		if($_POST['options-revisions']) 	{ $revisions = 1; }
		
		// check for advanced options
		if(!$_POST['advanced-position']) 	{ $position = 0; } else { $position = $_POST['advanced-position']; }
		if(!$_POST['advanced-slug']) 	{ $slug = str_replace(' ', '_', strtolower($_POST['post-type-name'])); } else { $slug = $_POST['advanced-slug']; }
			
		$add = $wpdb->query("INSERT INTO " . $ecpt_db_name . " SET 
			`name`='" . str_replace(' ', '', strtolower($_POST['post-type-name'])) . "',			
			`singular_name`='"		. 	$single . "',	
			`plural_name`='"		. 	$plural . "',	
			`hierarchical`='"		. 	$hierarchical . "',	
			`post_formats`='"		. 	$post_formats . "',	
			`has_archive`='"		. 	$archives . "',		
			`title`='"				. 	$title . "',
			`editor`='"				. 	$editor . "',
			`author`='"				. 	$author . "',
			`thumbnail`='"			. 	$thumbnail . "',
			`excerpt`='"			. 	$excerpt . "',
			`fields`='"				. 	$fields . "',
			`comments`='"			. 	$comments . "',
			`revisions`='"			. 	$revisions . "',
			`menu_icon`='"			. 	$_POST['options-icon'] . "',
			`menu_position`='"		. 	$position . "',
			`slug`='"				. 	$slug . "'

		;");	
		
		$url = get_bloginfo('wpurl') . '/wp-admin/admin.php?page=easy-content-types/easy-content-types.php?posttypes&post-type-added=1';
		header ("Location: $url");
	}
	
	if(isset($_POST['taxonomy-name'])) 
	{	
		if($_POST['label-single'] != '') { $single = $_POST['label-single']; } else { $single = $_POST['taxonomy-name']; }
		if($_POST['label-plural'] != '') { $plural = $_POST['label-plural']; } else { $plural = $_POST['taxonomy-name']; }
		if($_POST['options-slug'] != '' ) { $slug = strtolower(str_replace(' ', '-', $_POST['options-slug'])); } 
			else { $slug = str_replace(' ', '_', strtolower($_POST['taxonomy-name'])); }
		
		
		// check for checked options
		if($_POST['options-hierarchial']) 	{ $hierarchical = 1; }
		if($_POST['options-tagcloud']) { $show_tagcloud = 1; }
		if($_POST['options-nav']) 			{ $nav = 1; }
		
		$pages = array();
		foreach($_POST['taxonomy-object'] as $page) { $pages[] = $page; };
		$pages_final = implode(',', $pages);
		
		$add = $wpdb->query("INSERT INTO " . $ecpt_db_tax_name . " SET 
			`name`='" 					. 	str_replace(' ', '', $_POST['taxonomy-name']) . "',
			`singular_name`='"			. 	$single . "',
			`plural_name`='"			. 	$plural . "',	
			`hierarchical`='"			. 	$hierarchical . "',
			`show_tagcloud`='"			. 	$show_tagcloud . "',
			`show_in_nav_menus`='"		. 	$nav . "',
			`page`='"					. 	$pages_final . "',
			`slug`='"					. 	$slug . "'

		;");	
		
		$url = get_bloginfo('wpurl') . '/wp-admin/admin.php?page=easy-content-types/easy-content-types.php?taxonomies&taxonomy-added=1';
		header ("Location: $url");
	}
	
	// add custom meta boxes
	if(isset($_POST['metabox-name'])) 
	{	
					
		$add = $wpdb->query("INSERT INTO " . $ecpt_db_meta_name . " SET 
			`name`='" 	.   str_replace(' ', '', strtolower($_POST['metabox-name'])) . "',
			`nicename`='" .   $_POST['metabox-name'] . "',
			`page`='"		. 	$_POST['metabox-page'] . "',
			`context`='"	. 	$_POST['metabox-context'] . "',
			`priority`='"	. 	$_POST['metabox-priority'] . "'

		;");	
		
		$url = get_bloginfo('wpurl') . '/wp-admin/admin.php?page=easy-content-types/easy-content-types.php?metaboxes&metabox-added=1';
		header ("Location: $url");
	}
	
	// add fields to meta boxes
	if(isset($_POST['field-name'])) 
	{	
		$list_id = $_POST['field-order'] + 1;
		
		if($_POST['rich-editor']) 	{ $rich_editor = 1; }
		
		$add = $wpdb->query("INSERT INTO " . $ecpt_db_meta_fields_name . " SET 
			`name`= '" 			. preg_replace('/[^a-zA-Z0-9!@#$"\'\/()\.,]/', '', str_replace(' ', '', strtolower($_POST['field-name']))) . "',
			`nicename`='"		. $_POST['field-name'] . "',
			`parent`='"			. $_POST['field-parent'] . "',
			`type`='"			. $_POST['field-type'] . "',
			`description`='"	. $_POST['field-desc'] . "',
			`options`='"		. $_POST['field-options'] . "',
			`rich_editor`='"	. $rich_editor . "',
			`list_order`='"		. $_POST['field-order'] . "',
			`max`='"			. $_POST['field-max'] . "'

		;");	
		
		$url = get_bloginfo('wpurl') . '/wp-admin/admin.php?page=easy-content-types/easy-content-types.php?metaboxes&fields-edit=' . $_POST['current-field'];
		header ("Location: $url");
	}	
} 
?>