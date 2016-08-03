<?

error_log('analytics.php');
require(APPPATH.'/config/cobot.php');
require(APPPATH.'/controllers/admin/spaces_dict.php');
include_once APPPATH . 'libraries/utilities.php';

session_start();

class Analytics extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$sql = "SELECT gd.access_token FROM google_drive gd";
		error_log($sql);
		$query = $this->db->query($sql);
		$result = current($query->result());
		if($result) {
			error_log($result->access_token);
			$_SESSION['access_token'] = $result->access_token;
		}
		if (!(isset($_SESSION['access_token']) && $_SESSION['access_token'])) {
			error_log('Access token not set');
			$_SESSION['not_authorized'] = 1;
		}
		$request_uri = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		error_log($request_uri);
		$_SESSION['app_request_uri'] = $request_uri;

		include_once APPPATH . '../../google_drive.php';
		if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
			$sql = "TRUNCATE TABLE google_drive";
			error_log($sql);
			$query = $this->db->query($sql);
			$sql = "INSERT INTO google_drive (access_token) VALUES ('".$_SESSION['access_token']."')";
			error_log($sql);
			$query = $this->db->query($sql);
		}
	}

	function do_get($url, $environment, $params=array()) {
		$util = new utilities;
		$get_result = array();
		$curl = curl_init();
		$url = $url.'?access_token='.$util->get_current_environment_cobot_access_token();
		foreach ($params as $key => $value) {
			$url.="&".$key."=".$value;
		}
		error_log($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($curl);
		$result_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		if($result_code >= 200 && $result_code < 300) {
			$get_result = (array)json_decode($result);
		}
		//error_log(json_encode($get_result));
		return $get_result;
	}

	function write_to_google_drive($file_name) {
		$fileMetadata = new Google_Service_Drive_DriveFile(array(
				'name' => $file_name,
				'mimeType' => 'text/csv'));
		$content = file_get_contents(__DIR__."/../../../".$file_name);
		$client = new Google_Client();
		$client->setAuthConfigFile(CLIENT_SECRET_PATH);
		$client->addScope(SCOPES);
		$client->setAccessType("offline");
		$client->setAccessToken($_SESSION['access_token']);
		$service = new Google_Service_Drive($client);
		$sql = "SELECT gdf.id FROM google_drive_files gdf where gdf.name = '".$file_name."'";
		error_log($sql);
		$query = $this->db->query($sql);
		$result = current($query->result());
		$file = NULL;
		error_log(json_encode($result));
		if($result && $result->id) {
			error_log('Updating google drive file: '.$file_name);
			$file = $service->files->update($result->id, $fileMetadata, array(
				'data' => $content,
				'mimeType' => 'text/csv',
				'uploadType' => 'multipart'));
		} else {
			error_log('Creating google drive file: '.$file_name);
			$file = $service->files->create($fileMetadata, array(
				'data' => $content,
				'mimeType' => 'text/csv',
				'uploadType' => 'multipart'));
		}
		if($file && $file->id) {
			$sql = "INSERT INTO google_drive_files (id, name) VALUES ('".$file->id."', '".$file_name."')";
			error_log($sql);
			$query = $this->db->query($sql);
		}
		return $file;
	}

	function get_checkins() {
		$all_checkins = array();
		$from_datetime = $_GET["from"];
		$to_datetime = $_GET["to"];
		$header = "id,last_name,first_name,company,sign_in,time,location_id,plan_code\n";
		echo nl2br($header);
		try {
			$file_path = __DIR__."/../../../checkins.csv";
			if(! $checkinsfile =  fopen($file_path,"a+")) {
	        	throw new Exception("Unable to open file checkins.csv !");
			}
			$file_contents = fread($checkinsfile, filesize($file_path));
			if(empty($file_contents)) {
				fwrite($checkinsfile, $header);	
			}
			$util = new utilities;
			if(!$from_datetime || !$to_datetime) {
				date_default_timezone_set('America/New_York');
				$date_today = date('Y-m-d', time());
				$time_tomorrow = strtotime("+1 day", strtotime($date_today));
				$date_tomorrow = date("Y-m-d", $time_tomorrow);
				$params = array('from' => urlencode($date_today." 00:00:00 -0400"), 'to' => urlencode($date_tomorrow." 00:00:00 -0400"));
			} else {
				$params = array('from' => urlencode($from_datetime), 'to' => urlencode($to_datetime));
			}
			
			$query = $this->db->get("cobot_spaces");
		    $spaces = $query->result();
		    foreach ($spaces as $space) {
		    	$url = "https://".$space->id.".cobot.me/api/work_sessions";
		    	$checkins = $this->do_get($url, NULL, $params);
		    	error_log(json_encode($checkins));
		    	foreach ($checkins as $checkin) {
		    		error_log(json_encode($checkin));
		    		$membership_id = $checkin->membership->id;
		    		$from = $checkin->valid_from;
		    		if($from) {
		    			$from_date = substr($from, 0, strpos($from, " "));
		    			$from_time = substr($from, strpos($from, " ")+1);
		    		}
		    		$sql = "SELECT u.id as id, u.last_name as last_name, u.first_name as first_name, c.name as company, '".$from_date."' as sign_in, '".$from_time."' as time, '".$space->id."' as location_id, cm.plan_name as plan_code FROM cobot_memberships cm join user u on cm.user_id = u.id join company c on u.company_id = c.id where cm.space_id = '".$space->id."' AND cm.id='".$membership_id."'";
					error_log($sql);
					$query = $this->db->query($sql);
					$result = current($query->result());
					if($result) {
						$record = $result->id.','.$result->last_name.','.$result->first_name.','.$result->company.','.$result->sign_in.','.$result->time.','.$result->location_id.','.$result->plan_code."\n";
						echo nl2br($record);
						fwrite($checkinsfile, $record);
						array_push($all_checkins, $result);
					}
		    	}
		    }
		    error_log(json_encode($all_checkins));
		    fclose($checkinsfile);
		    $this->write_to_google_drive('checkins.csv');
		} catch(Exception $e){
			if($checkinsfile) {
				fclose($checkinsfile);
			}
			$error_msg = 'Exception getting all checkins : '.$e->getMessage();
			echo nl2br($error_msg);
			error_log($error_msg);
		}
	}

	function get_users() {
		$all_users = array();
		$header = "id,company_id,name,rfid,wp_users_id,date_added,referrer,twitter,behance,email_address\n";
		echo nl2br($header);

		try {
			if(! $usersfile =  fopen(__DIR__."/../../../users.csv","w+")) {
	        	throw new Exception("Unable to open file users.csv !");
			}
			fwrite($usersfile, $header);
			$sql = "SELECT u.id as id, u.company_id as company_id, concat(u.first_name,' ',u.last_name) as name, u.rfid as rfid, u.wp_users_id as wp_users_id, u.date_added as date_added, u.referrer as referrer, u.twitter as twitter, u.behance as behance, wp_user.user_email as email_address FROM user u join wpmember_users wp_user on u.wp_users_id = wp_user.id";
			error_log($sql);
			$query = $this->db->query($sql);
			$results = $query->result();
			if($results) {
				foreach ($results as $result) {
					$record = $result->id.','.$result->company_id.','.$result->name.','.$result->rfid.','.$result->wp_users_id.','.$result->date_added.','.$result->referrer.','.$result->twitter.','.$result->behance.','.$result->email_address."\n";
					echo nl2br($record);
					fwrite($usersfile, $record);
					array_push($all_users, $result);
				}
			}
			error_log(json_encode($all_users));
			fclose($usersfile);
			$this->write_to_google_drive('users.csv');
		} catch(Exception $e){
			if($usersfile) {
				fclose($usersfile);
			}
			$error_msg = 'Exception getting all users : '.$e->getMessage();
			echo nl2br($error_msg);
			error_log($error_msg);
		}
	}

	function get_accounts() {
		$space_id = $_GET["space_id"];
		$email = $_GET["email"];
		$all_accounts = array();
		$header = "cobot_user_id,grind_user_id,email,currency,quantity,unit_amount,add_on_amount,total_recurring_amount,next_invoice_at,activated_at,canceled_to,collection_method,plan_name\n";
		echo nl2br($header);
		try {
			if(! $accountsfile =  fopen(__DIR__."/../../../accounts.csv","w+")) {
	        	throw new Exception("Unable to open file accounts.csv !");
			}
			fwrite($accountsfile, $header);
			$sql = "SELECT cm.id, cm.space_id, cm.cobot_user_id, cm.user_id FROM cobot_memberships cm";
			if($email) {
				$sql = $sql . " join user u on cm.user_id = u.id join wpmember_users wp_user on u.wp_users_id = wp_user.id and wp_user.user_email = '".$email."'";
			}
			if($space_id) {
				$sql = $sql . " where cm.space_id = '".$space_id."'";
			}
			error_log($sql);
			$query = $this->db->query($sql);
			$results = $query->result();
			if($results) {
				foreach ($results as $result) {
					$url = "https://".$result->space_id.".cobot.me/api/memberships/".$result->id;
			    	$membership = $this->do_get($url, NULL);
			    	error_log(json_encode($membership));
			    	if($membership) {
			    		$plan = $membership['plan'];
				    	$payment_method = $membership['payment_method'];
				    	$add_on_amount = 0.0;
				    	if($plan->extras) {
							foreach ($plan->extras as $extra) {
								$add_on_amount = $add_on_amount + $extra->price;
							}
				    	}
				    	$record = $result->cobot_user_id.','.$result->user_id.','.$membership['email'].','.$plan->currency.','.'1'.','.$plan->price_per_cycle.','.$add_on_amount.','.$plan->total_price_per_cycle.','.$membership['next_invoice_at'].','.$membership['confirmed_at'].','.$membership['canceled_to'].','.$payment_method->name.','.$plan->name."\n";
						echo nl2br($record);
						fwrite($accountsfile, $record);
						array_push($all_accounts, $record);
			    	}
				}
			}
			error_log(json_encode($all_accounts));
			fclose($accountsfile);
			$this->write_to_google_drive('accounts.csv');
		} catch(Exception $e){
			if($accountsfile) {
				fclose($accountsfile);
			}
			$error_msg = 'Exception getting all accounts : '.$e->getMessage();
			echo nl2br($error_msg);
			error_log($error_msg);
		}
	}
}

?>