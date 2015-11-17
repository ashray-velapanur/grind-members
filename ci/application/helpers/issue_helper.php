<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
	
// ------------------------------------------------------------------------

/**
 * issue_log to front desk
 *
 * notes an issue to the front desk helper function makes
 * it easy for us to do from anywhere in the app
 *
 * @access	public
 * @param   message, type
 * @return	boolean
 */


if( !function_exists( 'issue_log' ) )
{
	function issue_log($user_id,$msg,$type=NULL)
	{
		$CI =& get_instance();
    	$CI->load->model('issuesmodel');
    	$issue_id = $CI->issuesmodel->logMemberIssue($user_id,$msg,$type);
    //	$CI->issuesmodel->closeMemberIssue($issue_id);
		
		// log the message to the php error console as well
		error_log($msg,0);
	}	
}


// ------------------------------------------------------------------------

?>