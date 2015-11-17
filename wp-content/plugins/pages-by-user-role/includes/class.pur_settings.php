<?php

/**
 * 
 *
 * @version $Id$
 * @copyright 2003 
 **/
class pur_settings {
	function pur_settings(){
		add_filter('pop-options_pur',array(&$this,'options'),10,1);
	}
	
	function options($t){
		$i = count($t);

		//-------------------------	
		$i++;
		$t[$i]->id 			= 'pur-defaults'; 
		$t[$i]->label 		= __('Settings','pur');
		$t[$i]->right_label	= __('General Settings (default redir url)','pur');
		$t[$i]->page_title	= __('Settings','pur');
		$t[$i]->theme_option = true;
		$t[$i]->plugin_option = true;
		$t[$i]->options = array(
			(object)array(
				'id'	=> 'redir_url',
				'type'	=> 'textarea',
				'label'	=> __('Default Redirect URL','pur'),
				'description' => __('If a Page, Post or Custom Post Type does not have a redirect URL, users with no access to the Page, Post or Custom Post Type will get redirected to the default redirect URL.','pur'),
				'el_properties' => array(),
				'save_option'=>true,
				'load_option'=>true
			),
			(object)array(
				'id'	=> 'comment_filtering',
				'type'	=> 'checkbox',
				'label'	=> __('Check to enable comment filtering. ','pur'),
				'description' => __('Check to enable comment filtering.','pur'),
				'el_properties' => array(),
				'save_option'=>true,
				'load_option'=>true
			),	
			(object)array(
				'id'	=> 'login_redir',
				'type'	=> 'checkbox',
				'label'	=> __('Redirect to login','pur'),
				'description' => __('If a visitor(not logged user) is trying to access a restricted page, check this option to redirect to login, or leave it unchecked to redirect to the defined redirect url.','pur'),
				'value'=>1,
				'default'=>1,
				'el_properties' => array(),
				'save_option'=>true,
				'load_option'=>true
			),			
			(object)array(
				'type'=>'clear'
			),
			(object)array(
				'type'	=> 'submit',
				'label'	=> __('Save','sws'),
				'class' => 'button-primary',
				'save_option'=>false,
				'load_option'=>false
			)
		);
		
		//-------------------------	
		//--------------
		$post_types=array();
		foreach(get_post_types(array('_builtin' => false),'objects','and') as $post_type => $pt){
			$post_types[$post_type]=$pt;
		} 
		//--------------
		if(count($post_types)>0){
			$i++;
			$t[$i]->id 			= 'pur-post-types'; 
			$t[$i]->label 		= __('PUR for Custom Post Types','pur');
			$t[$i]->right_label	= __('Enable Page Access Options for custom post types','pur');
			$t[$i]->page_title	= __('PUR for Custom Post Types','pur');
			$t[$i]->theme_option = true;
			$t[$i]->plugin_option = true;
			$t[$i]->options = array();
			
			$j=0;
			foreach($post_types as $post_type => $pt){
				$tmp=(object)array(
					'id'	=> 'post_types_'.$post_type,
					'name'	=> 'post_types[]',
					'type'	=> 'checkbox',
					'option_value'=>$post_type,
					'label'	=> (@$pt->labels->name?$pt->labels->name:$post_type),
					'el_properties' => array(),
					'save_option'=>true,
					'load_option'=>true
				);
				if($j==0){
					$tmp->description = __("Page Access by User Role can be enabled for plugins using WordPress 3.0 Custom Post Types.",'pur');
					$tmp->description_rowspan = count($post_types);
				}
				$t[$i]->options[]=$tmp;
				$j++;
			}
			
			$t[$i]->options[]=(object)array(
					'type'=>'clear'
				);
			$t[$i]->options[]=(object)array(
					'type'	=> 'submit',
					'label'	=> __('Save','sws'),
					'class' => 'button-primary',
					'save_option'=>false,
					'load_option'=>false
				);		
		}		
		
		//----- Custom post types by User Role
		$i++;
		$t[$i]->id 			= 'cpur'; 
		$t[$i]->label 		= __('Custom Post Types by User Role','pur');
		$t[$i]->right_label	= __('Restrict post types by user role','pur');
		$t[$i]->page_title	= __('Custom Post Types by User Role','pur');
		$t[$i]->theme_option = true;
		$t[$i]->plugin_option = true;
		$t[$i]->options = array(
			(object)array(
				'id'	=> 'disable_cpur',
				'type'	=> 'yesno',
				'label'	=> __('Disable CPUR','pur'),
				'description' => __('Oops! If you have left out yourself from accessing a custom post type, choose this option so the post type is visible again.','pur'),
				'el_properties' => array(),
				'save_option'=>true,
				'load_option'=>true
			)			
		);		
		
		global $wp_roles;
		$roles = $wp_roles->get_names();
		
		if(is_array($roles)&&count($roles)>0){
			$j=0;
			foreach($post_types as $post_type => $pt){
				$tmp=(object)array(
					'type'	=> 'label',
					'label'	=> (@$pt->labels->name?$pt->labels->name:$post_type),
					'save_option'=>false,
					'load_option'=>false
				);
				if($j==0){
					$tmp->description = __("You can restrict access to certain custom post types by checking the user roles that should have access to it.  Please observe that unchecking a role does not mean that it will enable the post type for that role.",'pur');
					$tmp->description_rowspan = count($post_types);
				}
				$t[$i]->options[]=$tmp;
				$j++;		
				//-----			
				foreach($roles as $role => $role_name){
					$t[$i]->options[]=(object)array(
						'id'	=> sprintf("cpur_%s_%s",$post_type,$role),
						'name'	=> sprintf("cpur_%s[]",$post_type),
						'type'	=> 'checkbox',
						'option_value'=>$role,
						'label'	=> $role_name,
						'el_properties' => array(),
						'save_option'=>true,
						'load_option'=>true
					);
				}	
			}
		}
		
		$t[$i]->options[]=(object)array(
				'type'=>'clear'
			);
		$t[$i]->options[]=(object)array(
				'type'	=> 'submit',
				'label'	=> __('Save','sws'),
				'class' => 'button-primary',
				'save_option'=>false,
				'load_option'=>false
			);									
		
		

		return $t;
	}
}
?>