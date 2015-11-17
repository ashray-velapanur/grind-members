<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
	
// ------------------------------------------------------------------------

/**
 * message helper displays a message to the screen
 *
 * it easy for us to do from anywhere in the app
 *
 * @access	public
 * @param   message
 * @return	none
 */


if( !function_exists( 'display_msg' ) )
{
	function display_msg($msg,$type=NULL)
	{
		echo "<div id='msg' class='error'>".$msg."</div>";		
	}	
}


// ------------------------------------------------------------------------

?>