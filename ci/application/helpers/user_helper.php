<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
	
// ------------------------------------------------------------------------

/**
 * is_this_user
 *
 * validates if a user id matches current session user id
 *
 * @access	public
 * @param   SESSION,$user_id (GRIND)
 * @return	boolean
 */


if( !function_exists( 'is_this_user' ) )
{
	function is_this_user($user_id)
	{
		// if the users id matches the users id that is stored in session
		// then they are the same user
		if(isset($_SESSION['wpuser']['id'])){
			$result = ($_SESSION['wpuser']['id'] == $user_id) ? true : false;
			return $result;
		} else {
			// We can't determine their id
			log_message("debug:","trying to match user id's but something is not working");
			return false;
		}
	}	
}

/**
 * session_user_id
 *
 * just outputs the current session user id
 *
 * @access	public
 * @param   SESSION
 * @return	int, A grind User ID
 */


if( !function_exists( 'session_user_id' ) )
{
	function session_user_id()
	{
		// if the users id matches the users id that is stored in session
		// then they are the same user
		if(isset($_SESSION['wpuser']['id'])){
			$result = $_SESSION['wpuser']['id'];
			return $result;
		} else {
			// We can't determine their id
			log_message("debug:","there doesn't seem to be a user id in session");
			return false;
		}
	}	
}


// ------------------------------------------------------------------------

?>