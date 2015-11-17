<?php

/**
 * 
 *
 * @version $Id$
 * @copyright 2003 
 **/
class PurShortcodes {
	function PurShortcodes(){
		add_shortcode('pur_restricted', array(&$this,'pur_restricted'));
	}
	
	function pur_restricted($atts,$content=null,$code=""){
		extract(shortcode_atts(array(
			'capability' 	=> 'view_restricted_content',
			'alt'			=> ''
		), $atts));
		
		if(current_user_can($capability)){
			return $content;
		}else{
			return $alt;
		}
	}
}

?>