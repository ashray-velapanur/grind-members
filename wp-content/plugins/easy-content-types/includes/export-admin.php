<?php
function ecpt_export_page() {

	global $wpdb;
	global $ecpt_prefix;
  	global $ecpt_db_name;
  	global $ecpt_db_tax_name;
  	global $ecpt_db_meta_name;
  	global $ecpt_db_meta_fields_name;

	if ( ! isset( $_REQUEST['updated'] ) )
		$_REQUEST['updated'] = false;
	?>
	<div class="wrap">
	<div id="ecpt-wrap">
		<h2>Export Post Types, Taxonomies, and Metaboxes</h2>
		<p>The code provided on this page will allow you to "export" your created post types, taxonomies, and metaboxes to other WordPress installs very easily. Simply copy the given code and paste it into your theme's functions.php.
		
		<div class="ecpt_export_section">
			<h3>Your Post Types:</h3>
			<pre>
			<?php
			foreach( $wpdb->get_results("SELECT * FROM " . $ecpt_db_name . ";") as $key => $type)
			{
				echo '<div class="export-code">
				';
				echo "// registration code for $type->name post type
				";
				echo "function register_" . str_replace('-', '_', $type->name) . "_posttype() {";
					echo "
					&#36labels = array(
						'name' 				=> _x( '" . $type->plural_name . "', 'post type general name' ),
						'singular_name'		=> _x( '" . $type->singular_name . "', 'post type singular name' ),
						'add_new' 			=> _x( 'Add New', '" . $type->singular_name . "'),
						'add_new_item' 		=> __( 'Add New " . $type->singular_name ." '),
						'edit_item' 		=> __( 'Edit " . $type->singular_name ." '),
						'new_item' 			=> __( 'New " . $type->singular_name . " '),
						'view_item' 		=> __( 'View " . $type->singular_name ." '),
						'search_items' 		=> __( 'Search " . $type->plural_name ." '),
						'not_found' 		=>  __( 'No " . $type->singular_name ." found' ),
						'not_found_in_trash'=> __( 'No " . $type->plural_name . " found in Trash' ),
						'parent_item_colon' => ''
					);
					";

					// hierarchical is disabled at this time due to a bug in WP core
					if($type->hierarchical == 1) { $hierarchical = 'true'; } else { $hierarchical = 'false'; } 
					if($type->has_archive == 1) { $archive = 'true'; } else { $archive = 'false'; } 
					
					// check for supports options
					$supports = array();
					if($type->title == 1) 			{ $supports[] = 'title'; }
					if($type->editor == 1) 			{ $supports[] = 'editor'; }
					if($type->author == 1) 			{ $supports[] = 'author'; }
					if($type->thumbnail == 1) 		{ $supports[] = 'thumbnail'; }
					if($type->excerpt == 1) 		{ $supports[] = 'excerpt'; }
					if($type->fields == 1) 			{ $supports[] = 'custom-fields'; }
					if($type->comments == 1) 		{ $supports[] = 'comments'; }
					if($type->revisions == 1) 		{ $supports[] = 'revisions'; }
					if($type->post_formats == 1) 	{ $supports[] = 'post-formats'; }
					if($type->hierarchical == 1) 	{ $supports[] = 'page-attributes'; }
					
					echo "
					&#36supports = array(";
					foreach ($supports as $support) { $supports_string .= "'" . $support . "',"; }
					echo substr($supports_string, 0, strlen($supports_string)-1) . ");
					";
								
					echo "
					&#36post_type_args = array(
						'labels' 			=> &#36labels,
						'singular_label' 	=> __('$type->singular_name'),
						'public' 			=> true,
						'show_ui' 			=> true,
						'publicly_queryable'=> true,
						'query_var'			=> true,
						'capability_type' 	=> 'post',
						'has_archive' 		=> $archive,
						'hierarchical' 		=> $hierarchical,
						'rewrite' 			=> array('slug' => '$type->name'),
						'supports' 			=> &#36supports,
						'menu_position' 	=> 5,
						'menu_icon' 		=> '$icon'
					 );
					 ";
					echo "register_post_type('$type->name',&#36post_type_args);
				}
				";
				echo "add_action('init', 'register_" . str_replace('-', '_', $type->name) . "_posttype');";
				echo '</div>';
			}
			?>
			</pre>
		</div>
		
		<div class="ecpt_export_section">
			<h3>Your Taxonomies:</h3>
			<pre>
			<?php
			foreach( $wpdb->get_results("SELECT * FROM " . $ecpt_db_tax_name . ";") as $key => $tax)
			{
				echo '<div class="export-code">
				';
				echo "// registration code for $tax->name taxonomy
				";
				echo "function register_" . str_replace('-', '_', strtolower($tax->name)) . "_tax() {";
					echo "
					&#36labels = array(
						'name' 					=> _x( '$tax->plural_name', 'taxonomy general name' ),
						'singular_name' 		=> _x( '$tax->singular_name', 'taxonomy singular name' ),
						'add_new' 				=> _x( 'Add New $tax->singular_name', '$tax->singular_name'),
						'add_new_item' 			=> __( 'Add New $tax->singular_name' ),
						'edit_item' 			=> __( 'Edit $tax->singular_name' ),
						'new_item' 				=> __( 'New $tax->singular_name' ),
						'view_item' 			=> __( 'View $tax->singular_name' ),
						'search_items' 			=> __( 'Search $tax->plural_name' ),
						'not_found' 			=> __( 'No $tax->singular_name found' ),
						'not_found_in_trash' 	=> __( 'No $tax->singular_name found in Trash' ),
					);
					";
					if($tax->hierarchical == 1) 		{ $hierarchical = 'true'; } else { $hierarchical = 'false'; } 
					if($tax->show_tagcloud == 1) 		{ $tagcloud = 'true'; } else { $tagcloud = 'false'; } 
					if($tax->show_in_nav_menus == 1)	{ $nav = 'true'; } else { $nav = 'false'; }
					
					$pages = explode(',', $tax->page);
					
					echo "
					&#36pages = array(";
					foreach ($pages as $page) { $pages_str .= "'" . $page . "',"; }
					echo substr($pages_str, 0, strlen($pages_str)-1) . ");
					";
				
					
					echo "			
					&#36args = array(
						'labels' 			=> &#36labels,
						'singular_label' 	=> __('$tax->singular_name'),
						'public' 			=> true,
						'show_ui' 			=> true,
						'hierarchical' 		=> $hierarchical,
						'show_tagcloud' 	=> $tagcloud,
						'show_in_nav_menus' => $nav,
						'rewrite' 			=> array('slug' => $tax->name),
					 );
					";
					echo "register_taxonomy('" . strtolower($tax->name) . "', &#36pages, &#36args);
				}
				";
				echo "add_action('init', 'register_" . str_replace('-', '_', strtolower($tax->name)) . "_tax');";
				echo '</div>';
			}
			?>
			</pre>			
		</div>
		
		<div class="ecpt_export_section">
			<h3>Your Metaboxes:</h3>
			<p>Coming soon!</p>
			<pre>
			<?php 
			/*
			foreach( $wpdb->get_results("SELECT * FROM " . $ecpt_db_meta_name . " LIMIT 1;") as $key => $metabox)
			{
				echo "
				add_action('admin_menu', 'ecpt_add_" . $metabox->name . "_metabox');
				function ecpt_add_" . $metabox->name . "_metabox() {
				";
				
				echo "&#36" . $metabox->name . "_fields = array(";				
					foreach($wpdb->get_results("SELECT * FROM " . $ecpt_db_meta_fields_name . " WHERE parent = '" . $metabox->name ."';") as $key => $meta_field) 
					{
						$options = array();
						$options = explode(',', $meta_field->options);
						$options_str = '';
						foreach ($options as $option) { $options_str .= "'" . $option . "',"; }
						$options_str = substr($options_str, 0, strlen($options_str)-1);
					
						
						echo " 
						array(
							'name' 		=> '$meta_field->nicename',
							'desc' 		=> '$meta_field->description',
							'id' 		=> '" . $ecpt_prefix ."$meta_field->name',
							'class' 	=> '" . $ecpt_prefix . "$meta_field->name',
							'type' 		=> '$meta_field->type',
							'options' 	=> array($options_str)
						),
						";
					}
				echo ");";
				echo "				
				add_meta_box('ecpt_metabox_$metabox->id, '$metabox->nicename', 'ecpt_drop_in_show_box', '$metabox->page', '$metabox->context', '$metabox->priority', &#36" . $metabox->name . "_fields);
				";
			}*/
			?>
			</pre>
			
		</div>
		
	</div>
</div>
<?php 
}
?>