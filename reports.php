<?php
/**
 * Reporting System

 * 
 * This PHP file runs as a cronjob to export members information for the day
 * There are a few configuration parameters set at the top of the file.
 * The first is the email address for the output. By default, we email the results
 * to grindtechoutput@gmail.com.
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
require(__DIR__.'/postmark-php/Postmark.php');

define('POSTMARKAPP_API_KEY', '78939af4-c306-471b-9caa-53b8e05ce60e');
define('POSTMARKAPP_MAIL_FROM_ADDRESS', 'admin@grindspaces.com');


// database configuration
$con = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }
mysql_select_db(DB_NAME, $con);


// reporting configuration
$output_mail = 'grindtechoutput@gmail.com';
$destination_mail = 'admin@grindspaces.com';
$msg = "";
$filename1 = "daily_report_users.csv";
$mimeType1 = "text/csv";
$filename2 = "daily_report_checkins.csv";
$mimeType2 = "text/csv";
$filename3 = "lifetime_report_checkins.csv";
$mimeType3 = "text/csv";
$debug=false;

// begin routine


error_log("Starting Daily Reporting Routine",0);

try {	
	$sql = "select * from v_user_data_dump";
	$results = mysql_query($sql); 
	$numberFields = mysql_num_fields($results); 

	for($i=0; $i<$numberFields; $i++) {
		
		$keys[] = mysql_field_name($results, $i); // Create array of the names for the loop of data below
		$head[] = escape_csv_value(mysql_field_name($results, $i)); // Create and escape the headers for each column, this is the field name in the database
	}
	
	$contents_users = "id,company_id,name,rfid,wp_users_id,date_added,membership_status,referrer,twitter,behance,location_id,location_name,membership_id,plan_code,plan_state,quantity,activated_at,expires_at,canceled_at,current_period_started_at,current_period_ends_at,address\n";
	if (mysql_num_rows($results) > 0){
		
		//while ($row = mysql_fetch_assoc($results)) {
		while ($info = mysql_fetch_object($results)) {

		
			foreach($keys as $fieldName) { // Loop through the array of headers as we fetch the data
			
				$item[] = escape_csv_value($info->$fieldName);
			} // End loop
			$contents_users .= join(',', $item)."\n"; // Create a new row of data and append it to the last row
			$item = ''; // Clear the contents of the $row variable to start a new row
			
			//$contents_users .= implode(",",$row);
			//$contents_users .="\n";
			
			// write line of csv file
			
		} // end while
	} else {
		
		$contents_checkins .= "User data empty,";
		$errormsg = "User dump unsuccessful (No Results)";
		$msg .= $errormsg."\n";
		throw new Exception ($errormsg);
	}
	$msg .= "User dump successful\n";
	$results = NULL; // clear results
	
	$sql = "select * from v_checkins_today";
	$results = mysql_query($sql); 
	$numberFields = mysql_num_fields($results); 
	$keys = "";
	for($i=0; $i<$numberFields; $i++) {
		
		$keys[] = mysql_field_name($results, $i); // Create array of the names for the loop of data below
		$head[] = escape_csv_value(mysql_field_name($results, $i)); // Create and escape the headers for each column, this is the field name in the database
	}
	$contents_checkins = "id,last_name,first_name,company,sign_in_method,sign_in,location_id,plan_code\n";
	if (mysql_num_rows($results) > 0){

		//while ($row = mysql_fetch_assoc($results)) {
while ($info = mysql_fetch_object($results))	{
			foreach($keys as $fieldName) { // Loop through the array of headers as we fetch the data
			
				$item[] = escape_csv_value($info->$fieldName);
			} // End loop
			$contents_checkins .= join(',', $item)."\n"; // Create a new row of data and append it to the last row
			$item = ''; // Clear the contents of the $row variable to start a new row
			
			//$contents_checkins .= implode(",",$row);
			//$contents_checkins .="\n";
			// write line of csv file
			
		} // end while
		$msg .= "Checkins Today successful\n";

	} else {
		$contents_checkins .= "no checkins today,";
		$msg .= "Checkins Today successful (No Results)\n";
	}
	
	$sql = "select * from v_checkins_lifetime";
	$results = mysql_query($sql); 
	$numberFields = mysql_num_fields($results); 
	$keys = "";
	for($i=0; $i<$numberFields; $i++) {
		
		$keys[] = mysql_field_name($results, $i); // Create array of the names for the loop of data below
		$head[] = escape_csv_value(mysql_field_name($results, $i)); // Create and escape the headers for each column, this is the field name in the database
	}
	$contents_checkins_lifetime = "id,last_name,first_name,company,sign_in_method,sign_in,time,location_id,plan_code\n";
	if (mysql_num_rows($results) > 0){

		//while ($row = mysql_fetch_assoc($results)) {
while ($info = mysql_fetch_object($results))	{
			foreach($keys as $fieldName) { // Loop through the array of headers as we fetch the data
			
				$item[] = escape_csv_value($info->$fieldName);
			} // End loop
			$contents_checkins_lifetime .= join(',', $item)."\n"; // Create a new row of data and append it to the last row
			$item = ''; // Clear the contents of the $row variable to start a new row
			
			//$contents_checkins .= implode(",",$row);
			//$contents_checkins .="\n";
			// write line of csv file
			
		} // end while
		$msg .= "Checkins Lifetime successful\n";

	} else {
		$contents_checkins_lifetime .= "no checkins Lifetime,";
		$msg .= "Checkins Today Lifetime (No Results)\n";
	}
	
	error_log("message".$msg,0);
	
	$email = new Mail_Postmark();
	$email->addTo($output_mail, 'Grind Tech Output');
	$email->addCc($destination_mail);
    $email->subject('Daily Reports');
    $email->messagePlain('Attached are the Daily Grind Reports' . "\n" . "User report:\n" . 'https://members.grindspaces.com/reports/' . $filename1 . "\nLifetime check-ins report:\n" . 'https://members.grindspaces.com/reports/' . $filename3);
	file_put_contents('/var/www/vhosts/members.grindspaces.com/content/reports/'.$filename1, $contents_users);
	file_put_contents('/var/www/vhosts/members.grindspaces.com/content/reports/'.$filename3, $contents_checkins_lifetime);
    //$email->addCustomAttachment($filename1, $contents_users, $mimeType1);
echo "Total Size " . strlen($content_users . $contents_checkins . $contents_checkins_lifetime);
    $email->addCustomAttachment($filename2, $contents_checkins, $mimeType2);
     //$email->addCustomAttachment($filename3, $contents_checkins_lifetime, $mimeType3);
    $email->debug($debug);
    $email->send();
	
} catch (Exception $e) {
	$msg .= 'Error occurred: Reason: ' + $e->getMessage() . '<br/>';
	error_log('Daily Reporting: Failed. Reason: ' . $e->getMessage(),0);
	return false;
} // end try catch

if ($msg){
echo "\n**************************\n";
echo "REPORTING RESULTS\n";
echo $msg;
echo "\n**************************\n";
}

error_log("Completing Daily Reporting Routine",0);

/*
Created by: Daniel Kassner
Website: http://www.danielkassner.com
*/
function escape_csv_value($value) {
	$value = str_replace('"', '""', $value); // First off escape all " and make them ""
	if(preg_match('/,/', $value) or preg_match("/\n/", $value) or preg_match('/"/', $value)) { // Check if I have any commas or new lines
		return '"'.$value.'"'; // If I have new lines or commas escape them
	} else {
		return $value; // If no new lines or commas just return the value
	}
}
?>
