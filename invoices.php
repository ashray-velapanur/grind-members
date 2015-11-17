<?php
/**
 * Invoicing System
 * 
 * This PHP files runs as a cronjob to invoice members charges for the day
 * There are a few configuration parameters set at the top of the file.
 * The first is the invoice schedule for monthlies. By default, we invoice everyone
 * at the end of each day. If you want to invoice monthly members at the end of (their)
 * billing cycle set "include_monthly" to false.
 *
 * this system does require that you define database configuration here
 *
 * This runs outside of CI and Wordpress to mitigate conflicts with the cron on Rackspace Cloud
 * this utility is meant to be run from the OS php cron not from an HTTP cron.
 *
 * @magicandmight
 */


if ( !defined('__DIR__') ) define('__DIR__', dirname(__FILE__));

include_once __DIR__.'/_siteconfig.php';
include_once(__DIR__.'/lib/recurly.php');
Recurly_Client::$subdomain = RECURLY_SUBDOMAIN;
Recurly_Client::$apiKey = RECURLY_API_PASSWORD;


// database configuration
$con = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }
mysql_select_db(DB_NAME, $con);


// invoicing configuration
$include_monthly = TRUE;
$no_charges = "";
$charges = "";
$msg = "";

// begin routine


error_log("Starting Daily Invoice Routine",0);

$sql = "select * from subscription_sync;";
$results = mysql_query($sql);// insert db query here
try {
        while ($row = mysql_fetch_assoc($results)) {

                if ($include_monthly==TRUE || $row["plan_code"] == "daily"){

            try{
                $account = Recurly_Account::get($row["user_id"]);
                $invoice = Recurly_Invoice::invoicePendingCharges($account->account_code);
                if (isset($invoice)) {
                    $m = "Invoice created for member id: ".$row["user_id"]."\n";
                    $charges .= $m;
                   // error_log($m,0);
                } else {
                    $m = "no charges for member id: ".$row["user_id"]."\n";
                    $no_charges .= $m;
                   // error_log($m,0);
                } // end invoice
            }catch(Recurly_Error $e){
                error_log("exception: ".$e->getMessage()."\n",0);
                //skip monthly user or account not found
            }
		} else {
			// a monthly user we are skipping
		} // end monthly check
	} // end while
	
} catch (Exception $e) {
	$msg .= 'Error occurred: Reason: ' + $e->getMessage() . '<br/>';
	error_log('Daily Invoices: Failed. Reason: ' + $e->getMessage(),0);
	return false;
} // end try catch

if ($msg){
echo "\n**************************\n";
echo "CORE RESULTS\n";
echo $msg;
echo "\n**************************\n";
}


echo "**************************\n";
echo "CHARGES\n";
if ($charges){
	echo $charges;
} else {
	echo "No charges were found.";
}
echo "\n**************************\n";


echo "\n**************************\n";
echo "NO CHARGES\n";
echo $no_charges;
echo "\n**************************\n";

error_log("Completing Daily Invoice Routine",0);
?>