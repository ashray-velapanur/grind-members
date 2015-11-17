<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
	
// ------------------------------------------------------------------------

/**
 * Admin Layout Helper
 *
 * helpers to simplify and DRY up the Admin view templates
 *
 * @access	public
 * @viewtemplate (The template you want to display)
 * @data = the information you are passing into the template
 * @title=NULL, The title of the page
 * @return	none
 */


if( !function_exists( 'display' ) )
{
	function display($viewtemplate,$data,$title=NULL)
	{
		$CI =& get_instance();
    	$vars = set_nav_switcher($data);
		$vars["title"] = $title;
		$page["header"]=$CI ->load->view('template/_header',$vars,true);
		$page["footer"]=$CI->load->view('template/_footer','',true);
		$data["admin_only"] = true;
		$page["content"]=$CI->load->view($viewtemplate,$data,true);
		$CI ->load->view('template/_frame',$page);
	}	
}


// ------------------------------------------------------------------------

?>