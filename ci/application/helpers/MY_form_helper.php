<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
	
// ------------------------------------------------------------------------

/**
 * form_countries
 *
 * Generates a select list of countries
 *
 * @access	public
 * @param	string, boolean, array
 * @return	string
 */

if( !function_exists( 'form_countries' ) )
{
	function form_countries( $name, $selected = FALSE, $attributes=NULL )
	{
		$CI =& get_instance();
		
		$CI->config->load( 'countries' );
		
		$country_list = $CI->config->item( 'country_list' );
		
		$form = '<select name="'.$name.'"';
		
		if(isset($attributes)){
			foreach( $attributes as $key => $value )
			{
				$form .= " ".$key.'="'.$value.'"';
			}
		}
		$form .= ">";
		
		foreach( $country_list as $key => $value )
		{
			$form .= "\n".'<option value="'.$key.'"';
			
			if( $key == $selected )
			{
				$form .= ' selected>';
			}
			else
			{
				$form .= '>';
			}
			
			$form .= ucwords( strtolower( $value ) ).'</option>';
		}
		
		$form .= "\n</select>";
		
		return $form;
	}
}



?>