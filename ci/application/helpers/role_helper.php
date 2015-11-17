<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
	
// ------------------------------------------------------------------------

/**
 * is_admin
 *
 * validates if a user is an admin
 *
 * @access	public
 * @param   SESSION
 * @return	boolean
 */


if( !function_exists( 'is_administrator' ) )
{
	function is_administrator()
	{
		// if the users role is subscriber, then they are not an admin
		// we consider anyone above a subscriber to be an admin
		// in the future if GRIND wants to make this more restrictive 
		// this helper can be expanded.
		// right now this matches the wordpress function is_admin but leverages it for CI
		if(isset($_SESSION['wpuser']['wp_role'])){
			$role = ($_SESSION['wpuser']['wp_role'] == "Subscriber") ? false : true;
			return $role;
		} else {
			// we can't determine their role
			log_message("debug:","the user has no visible role in session variables");
			return false;
		}
	}	
}


// ------------------------------------------------------------------------

?>