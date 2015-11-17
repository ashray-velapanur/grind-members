<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
	
// ------------------------------------------------------------------------

/**
 * convert money and format it appropriately
 *
 * the billing system (securely) often gives us amounts in USD cents
 * which we convert to dollar, the format  
 *
 * @access	public
 * @param   amount, to_dollars=NULL (assume yes)
 * @return	formatted moeny
 */
 setlocale(LC_MONETARY, 'en_US');

if( !function_exists( 'format_money' ) )
{
	function format_money($amount, $to_dollars=true)
	{
		if($to_dollars){
			$amount = $amount/100;
		}
	$retval =  money_format('%(#1n', $amount);
	return $retval;
	}
}


           
// ------------------------------------------------------------------------

?>