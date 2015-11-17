<?php

/**
 * 
 *
 * @version $Id$
 * @copyright 2003 
 **/
class WP_Pur {
	var $options;
	function WP_Pur(){
		add_action('admin_menu', array(&$this, 'post_meta_box') );
		add_action('save_post', array(&$this,'save_post'), 10, 1 );
		//------------
		$this->options = get_option('pur_options');
		add_filter('manage_posts_columns', array(&$this,'manage_posts_columns'), 10, 1);
		add_filter('manage_pages_columns', array(&$this,'manage_posts_columns'), 10, 1);
		add_action('manage_posts_custom_column', array(&$this,'custom_column'),10,2);	
		add_action('manage_pages_custom_column', array(&$this,'custom_column'),10,2);	
	}
	
	function post_type_enabled($post_type){
		return in_array( $post_type, array_merge((is_array($this->options['post_types'])?$this->options['post_types']:array()),array('page','post')) );
	}
	
	function custom_column($field, $post_id=null){
		global $post;
		$post_id = $post_id==null?$post->ID:$post_id;
		if($field=='pur'){		
			global $wp_roles;
			$pur_roles = get_post_meta($post->ID,'pur-available-roles');
			$pur_roles = is_array($pur_roles)?$pur_roles:array();
			$roles = $wp_roles->get_names();
			$tmp = array();
			foreach($pur_roles as $role){
				$tmp[]=isset($roles[$role])?$roles[$role]:$role;
			}
			if(count($tmp)==0){
				echo '';
			}else{
				echo implode(", ",$tmp);
			}
		}
	}
	
	function manage_posts_columns($posts_columns){
		global $post;
		if(!$this->post_type_enabled($post->post_type)) 
			return $posts_columns;		
		$posts_columns['pur']=__('Access Control','cbw');
		return $posts_columns; 
	}
	
	function post_meta_box(){
		add_meta_box( 'pur-postmeta', __('Access Control','pur'),	array( &$this, 'form_template' ), 'page', 'side', 'low');
		add_meta_box( 'pur-postmeta', __('Access Control','pur'),	array( &$this, 'form_template' ), 'post', 'side', 'low');
		if(!empty($this->options['post_types'])&&count($this->options['post_types'])>0){
			foreach($this->options['post_types'] as $post_type){
				add_meta_box( 'pur-postmeta', __('Access Control','pur'),	array( &$this, 'form_template' ), $post_type, 'side', 'low');
			}
		}
	}	
	
	function save_post($post_id){
		if ( !wp_verify_nonce( @$_POST['pur-nonce'], 'pur-nonce' )) {
			return $post_id;
		}
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
			return $post_id;
		// Check permissions
		if ( 'page' == $_POST['post_type'] ) {
		  if ( !current_user_can( 'edit_page', $post_id ) )
		    return $post_id;
		} else {
		  if ( !current_user_can( 'edit_post', $post_id ) )
		    return $post_id;
		}

		$pur_roles = isset($_POST['pur_roles'])&&is_array($_POST['pur_roles'])?$_POST['pur_roles']:array();
		delete_post_meta($post_id,'pur-available-roles');
		if(!empty($pur_roles)){
			foreach($pur_roles as $role){
				add_post_meta($post_id,'pur-available-roles',$role);
			}
		}
		
		
		foreach( array('pur_redir_url') as $field){
			if(isset($_POST[$field])){
				update_post_meta($post_id,$field,$_POST[$field]);		
			}
		}
	}	

		
	function form_template($post){
		global $wp_roles;
		
		echo '<input type="hidden" name="pur-nonce" id="pur-nonce" value="' . wp_create_nonce( 'pur-nonce' ) . '" />';
		//----------------------
		$pur_roles = get_post_meta($post->ID,'pur-available-roles');
		$pur_roles = is_array($pur_roles)?$pur_roles:array();
		
		$roles = $wp_roles->get_names();
		
		if(is_array($roles)&&count($roles)>0){
?>
<div style="padding:10px;"><ul class="pur-roles">
<p>Leave blank in order <strong>not</strong> to restrict access to this part.<br />
By checking one or more User Roles only the checked will have access to this part.</p>
<?php
			foreach($roles as $value => $label){
				$checked = in_array($value,$pur_roles)?'checked="checked"':'';
?>
<li><span><input type="checkbox" <?php echo $checked ?> name="pur_roles[]" value="<?php echo $value ?>" />&nbsp;<?php echo $label ?></span></li>
<?php
			}
			echo "</ul>";
?>
<br />
<label>No Access URL:</label><br />
<input type="text" size="37" name="pur_redir_url" value="<?php echo get_post_meta($post->ID,'pur_redir_url',true)?>" />
</div>			
<?php			
		}else{
			echo __('Settings error, we could not identify any User Roles in the system.','pur');
		}
	}		
	
	
}

?>