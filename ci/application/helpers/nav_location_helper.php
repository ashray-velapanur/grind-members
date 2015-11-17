<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
	
// ------------------------------------------------------------------------

/**
 * Nav Locations Helpers
 *
 * helpers to simplify and DRY up the locations switcher in the nav
 *
 * @access	public
 * @param   data (array used to hold the nav location variables
 * @return	none
 */



if( !function_exists( 'set_nav_switcher' ) )
{
	function set_nav_switcher($data)
	{
		$CI =& get_instance();
    	$CI->load->model("lookuptablemodel");
		$CI->load->model("locationmodel");
        $data["navlocations"] = $CI->lookuptablemodel->getPseudoLookupTableEntries("location");
        $data["navlocation_current"] = $CI->locationmodel->getCurrentLocation();
		return $data;
	}	
}


// ------------------------------------------------------------------------

?>