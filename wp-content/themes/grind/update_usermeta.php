<?php
/**
 * Template Name: Update Usermeta
 */

function endsWith($haystack, $needle) {
    return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}

if(!empty($_FILES['simple-local-avatar']['name'])){
    $filename = $_FILES["simple-local-avatar"]["name"];
    $valid = endsWith($filename, ".jpg") || endsWith($filename, ".jpeg") || endsWith($filename, ".png");
    if ($valid != true) {
        wp_redirect( get_bloginfo('home').'/your-account/' );
    }
}

// the fields we're using.
$fields = array(
	'first_name'	=>	'Jerry',
	'last_name'		=>	'Grinder',
	'email'			=>	'Agora Email',
	'company_name'	=>	'Company Name',
	'company_desc'	=>	'Company Description',
	'phone'			=>	'Phone ',
	'URL'			=>	'URL',
	'twitter'		=>  'Twitter URL...',
	'behance'		=>  'Behance URL...',
	'foursquare'	=>	'FourSquare URL...',
	'linkedin'		=>	'LinkedIn URL...',
	'facebook'		=>	'Facebook URL...',
	'dribbble'		=>	'Dribbble URL...',
	'i_need'		=>	'One walrus.'
);
for($i=1; $i<7; $i++){
	$fields['skill_'.$i] = '';
}




/* If profile was saved, update profile. */
if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['gs_action'] ) && $_POST['gs_action'] == 'update-usermeta' ) {

	global $wpdb;
	
	foreach($fields as $k=>$v){
			// we have data to update.

			if($_POST[$k] != $v){ 
				// if we are working with a non-default value

			switch($k){
				case 'first_name':
				case 'last_name':
				case 'twitter':
				case 'behance':
				
				
				
				    update_user_meta( $_POST['wp_id'], $k, esc_attr($_POST[$k]) );
				    
				    $sql = 'UPDATE user SET '.$k.' = "'.$_POST[$k].'" WHERE id = '.$_POST['user_id'];
				    $res = $wpdb->query($sql);

				break;
				
				case 'company_name':
					update_user_meta( $_POST['wp_id'], $k, esc_attr($_POST[$k]));
					
					$tmp = $wpdb->get_results("SELECT company_id FROM user WHERE id = ".$_POST['user_id']);
					$co_id = $tmp[0]->company_id;
					
					if($co_id){ 
					    $sql = 'UPDATE company SET name = "'.$_POST[$k].'" WHERE id = '.$co_id;
					    $res = $wpdb->query($sql);
					} 
					
				break;
				case 'company_desc':

					update_user_meta( $_POST['wp_id'], $k, esc_attr($_POST[$k]) );
					
					$tmp = $wpdb->get_results("SELECT company_id FROM user WHERE id = ".$_POST['user_id']);
					$co_id = $tmp[0]->company_id;
					
					if($co_id){ 
					    $sql = 'UPDATE company SET description = "'.$_POST[$k].'" WHERE id = '.$co_id;
					    $res = $wpdb->query($sql);
					} 				
				break;
								
				case 'email':
					update_user_meta( $_POST['wp_id'], $k, esc_attr($_POST[$k]));
					$sql = 'UPDATE email SET address = "'.$_POST[$k].'" WHERE user_id = '.$_POST['user_id'];
					$res = $wpdb->query($sql);
				break;
				
				case 'phone':
					update_user_meta( $_POST['wp_id'], $k, esc_attr($_POST[$k]));
					$sql = 'UPDATE phone SET number = "'.$_POST[$k].'" WHERE user_id = '.$_POST['user_id'];
					$res = $wpdb->query($sql);
				break;
				
				case 'URL':
					update_user_meta( $_POST['wp_id'], $k, esc_url($_POST[$k]) );
					update_user_meta( $_POST['wp_id'], 'user_url', esc_url($_POST[$k] ) );

				break;
				
				default:
					update_user_meta( $_POST['wp_id'], $k, esc_attr($_POST[$k]) );
				break;
				
			
			}	// end switch
			
			} // end check on default value
	}
	
	$simple_local_avatars->edit_user_profile_update($_POST['wp_id']);		
		
	do_action('personal_options_update');
  /* Redirect so the page will show updated info. */
    if ( !$error ) {
        wp_redirect( get_bloginfo('home').'/your-account/' );
        exit();
    } else{ 
    	#print_r($error);
    }

}	else {
	// no direct access to this script.
	wp_redirect( home_url(), 302 ); exit;
	exit();
}


 ?>