<?php

/**
 * 
 *
 * @version $Id$
 * @copyright 2003 
 **/
define('PUR_CAPABILITY','manage_options');
 
class plugin_pur {
	var $id;
	var $options_parameters=array();
	var $plugin_page;
	var $load_registration_option = true;
	function plugin_pur($args=array()){
		//------------
		$defaults = array(
			'id'=>'pur',
			'options_parameters'=> array(				
				'id'					=> 'pur',
				'plugin_id'				=> 'pur',
				'capability'			=> 'manage_options',
				'options_varname'		=> 'pur_options',
				'menu_id'				=> 'pur-options',
				'page_title'			=> __('PUR Options','cbg'),
				'menu_text'				=> __('PUR Options','cbg'),
				'option_menu_parent'	=> 'options-general.php',
				'notification'			=> (object)array(
					'plugin_version'=> PUR_VERSION,
					'plugin_code' 	=> 'PUR',
					'message'		=> __('Pages by User Role update %s is available! <a href="%s">Please update now</a>','pur')
				),
				'theme'					=> false,
				'stylesheet'			=> 'pur-options',
				'rangeinput'			=> false,
				'colorpicker'			=> false					
			),
			'load_registration_option'=>true
		);
		foreach($defaults as $property => $default){
			$this->$property = isset($args[$property])?$args[$property]:$default;
		}
		//-----------	
		add_action('plugins_loaded',array(&$this,'plugins_loaded'));
	}
	
	function plugins_loaded(){
		require_once PUR_PATH.'includes/class.FrontendPur.php';
		new FrontendPur();
		require_once PUR_PATH.'includes/class.PurShortcodes.php';
		new PurShortcodes();		
		if(is_admin()):
			require_once PUR_PATH.'includes/class.WP_Pur.php';
			new WP_Pur();	
			
			require_once PUR_PATH.'includes/class.PurCategory.php';
			new PurCategory();
			//---options panel 
			wp_register_style( 'pur-options', PUR_URL.'css/pop.css', array(),'1.0.0');
			require_once PUR_PATH.'includes/class.pur_settings.php';new pur_settings();
			if($this->load_registration_option):
			require_once PUR_PATH.'includes/class.plugin_registration.php';
			new plugin_registration(array('plugin_id'=>$this->id,'tdom'=>'pur','plugin_code'=>'PUR','options_varname'=>'pur_options'));
			endif;
			require_once PUR_PATH.'includes/class.PluginOptionsPanel.php';
			new PluginOptionsPanel($this->options_parameters);			
		endif;
	}
}  

?>