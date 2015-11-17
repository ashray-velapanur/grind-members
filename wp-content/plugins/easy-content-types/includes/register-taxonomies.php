<?php 

function ecpt_register_taxonomies() {
	global $wpdb;
	global $ecpt_db_tax_name;
	global $ecpt_options;
	global $wp_rewrite;
	
	foreach( $wpdb->get_results("SELECT * FROM " . $ecpt_db_tax_name . ";") as $key => $tax) {
	
		
		$labels = array(
			'name' 					=> _x( $tax->plural_name, 'taxonomy general name' ),
			'singular_name' 		=> _x( $tax->singular_name, 'taxonomy singular name' ),
			'add_new' 				=> _x( 'Add New ' . $tax->singular_name, $tax->singular_name),
			'add_new_item' 			=> __( 'Add New ' . $tax->singular_name ),
			'edit_item' 			=> __( 'Edit ' . $tax->singular_name ),
			'new_item' 				=> __( 'New ' . $tax->singular_name ),
			'view_item' 			=> __( 'View ' . $tax->singular_name ),
			'search_items' 			=> __( 'Search ' . $tax->plural_name ),
			'not_found' 			=> __( 'No ' . $tax->singular_name . ' found' ),
			'not_found_in_trash' 	=> __( 'No ' . $tax->singular_name . ' found in Trash' ),
		);

		if($tax->hierarchical == 1) 		{ $hierarchical = true; } else { $hierarchical = false; } 
		if($tax->show_tagcloud == 1) 		{ $tagcloud = true; } else { $tagcloud = false; } 
		if($tax->show_in_nav_menus == 1)	{ $nav = true; } else { $nav = false; } 
		
		$pages = array();
		$pages = explode(',', $tax->page);
		
		$args = array(
			'labels' 			=> $labels,
			'singular_label' 	=> __($tax->singular_name),
			'public' 			=> true,
			'show_ui' 			=> true,
			'hierarchical' 		=> $hierarchical,
			'show_tagcloud' 	=> $tagcloud,
			'show_in_nav_menus' => $nav,
			'rewrite' 			=> array('slug' => $tax->slug),
		 );
		 
		if($ecpt_options['create_tax_templates'] == true) {
			 // create a template file for the taxonomy archives if it doesn't exist
			 if(!file_exists(get_stylesheet_directory() . '/taxonomy-' . str_replace(' ', '_', strtolower($tax->name)) . '.php')) {
				if(file_exists(get_stylesheet_directory() . '/taxonomy.php')) {
					copy(get_stylesheet_directory() . '/taxonomy.php', get_stylesheet_directory() . '/taxonomy-' . str_replace(' ', '_', strtolower($tax->name)) . '.php');
				} elseif (file_exists(get_stylesheet_directory() . '/archive.php')) {
					copy(get_stylesheet_directory() . '/archive.php', get_stylesheet_directory() . '/taxonomy-' . str_replace(' ', '_', strtolower($tax->name)) . '.php');
				} else {
					copy(get_stylesheet_directory() . '/index.php', get_stylesheet_directory() . '/taxonomy-' . str_replace(' ', '_', strtolower($tax->name)) . '.php');
				}
			 }
		}		 
		 
		register_taxonomy(str_replace(' ', '_', strtolower($tax->name)), $pages, $args);		
	}
	if($ecpt_options['auto_flush_permalinks'] == true) {
		// update the permalink structure
		$wp_rewrite->flush_rules();
	}
}
add_action('init', 'ecpt_register_taxonomies');

?>