<?php 
function ecpt_field_shortcode( $atts, $content = null )
{
	global $ecpt_prefix;
	global $post;
	
	extract( shortcode_atts( array(
			'id' => '',
			'image' => false,
			'url' => ''
		), $atts )
	);
	 
	if($image == true) {
		
		if($url) { $field .= '<a href="' . $url . '">'; }
		$field .= '<img src="' . get_post_meta($post->ID, $ecpt_prefix . $id, true) . '">';
		if($url) { $field .= '</a>'; }
		
	} else {
		if($url) { $field .= '<a href="' . $url . '">'; }
		$field .= get_post_meta($post->ID, $ecpt_prefix . $id, true);
		if($url) {  $field .= '</a>'; }
	}
	
	return $field;
}
add_shortcode('ecpt_field', 'ecpt_field_shortcode');

// enable short codes within meta box fields
add_filter('the_content', 'do_shortcode');
?>