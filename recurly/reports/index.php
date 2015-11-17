<?php 


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

ini_set('max_execution_time', 300);

// require necessary libraries, etc.
require_once('../../_siteconfig.php');
require_once('../library/recurly.php');
// instantiate connectivity to recurly
RecurlyClient::SetAuth(RECURLY_API_USERNAME, RECURLY_API_PASSWORD, RECURLY_SUBDOMAIN, RECURLY_ENVIRONMENT,RECURLY_PRIVATEKEY);

// database configuration
$con = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }
mysql_select_db(DB_NAME, $con);



// gather list of all accounts in the system

try {	
	
	if(isset($_GET['start'])){
		$start = $_GET['start'];
	} else { 
		$start = 0;
	}
	$end = $start + 25;
	
	$sql = "select * from v_user_data_dump LIMIT $start, $end";	
	$results = mysql_query($sql); 
	$numberFields = mysql_num_fields($results); 

	for($i=0; $i<$numberFields; $i++) {
		
		$keys[] = mysql_field_name($results, $i); // Create array of the names for the loop of data below
		$head[] = escape_csv_value(mysql_field_name($results, $i)); // Create and escape the headers for each column, this is the field name in the database
	}
	
	$contents_users = "id,company_id,name,rfid,wp_users_id,date_added,membership_status,referrer,twitter,behance,membership_id,plan_code,plan_state,quantity,activated_at,expires_at,canceled_at,current_period_started_at,current_period_ends_at,address\n";
	if (mysql_num_rows($results) > 0){
		echo'<style type="text/css"> td { text-align:center; }</style>';
		echo '<div style="margin:0 auto; width:940px; padding:0 20px 20px 20px; margin-top:100px;">';
		echo '<table style="width:100%;">'."\n";
		
		echo '<tr>
				<th>Subscriber name</th>
				<th>Membership Plan</th>
				<th>Recurly Balance</th>
			 </tr>';
		
		//while ($row = mysql_fetch_assoc($results)) {
		while ($info = mysql_fetch_object($results)) {
			#print_r($info);
			
			
			echo '<tr>';
				echo '<td>'.$info->name.'</td>';
				echo '<td>'.$info->plan_code.'</td>';
				$account = RecurlyAccount::getAccount($info->wp_users_id);		
				echo '<td>$'.number_format($account->balance_in_cents).'</td>';
			echo '</tr>';
		} // end while
		
		echo '</table>';
		echo '</div>';
	} else {
		
		$contents_checkins .= "User data empty,";
		$errormsg = "User dump unsuccessful (No Results)";
		$msg .= $errormsg."\n";
		throw new Exception ($errormsg);
	}
} catch(Exception $e) {
	$msg .= 'Error occurred: Reason: ' + $e->getMessage() . '<br/>';
	error_log('Daily Reporting: Failed. Reason: ' + $e->getMessage(),0);
	return false;
} // end try catch




// foreach plan type

// list the accounts associated with a type

// output the 'balance' that is stored in recurly with each record.




?>