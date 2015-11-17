<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
	
// ------------------------------------------------------------------------

/**
 * convert timestamps into NY Time
 *
 * passing the abbr flag to true will output an abbr tag around the date   
 *
 * @access	public
 * @param   datestamp,abbr
 * @return	NY time date
 */


if( !function_exists( 'format_date' ) )
{
	function format_date($datestamp,$abbr=NULL)
	{
		$userTimezone = new DateTimeZone('America/New_York');
		$gmtTimezone = new DateTimeZone('America/Chicago');

		$datestamp = new DateTime($datestamp, $gmtTimezone);
    	$datestamp->setTimezone($userTimezone);
    	$datestamp = $datestamp->format(DATE_ISO8601);

	
		if(isset($abbr) && $abbr){
		$datestamp = "<abbr class='localtime timeago' title='".$datestamp."'>".$datestamp."</abbr>";
		}
	
	return $datestamp;

	}
}


           
// ------------------------------------------------------------------------

?>