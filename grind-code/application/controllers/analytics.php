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

	function write_to_google_drive($file_name, $content=NULL, $header=NULL, $overwrite=true) {
		$fileMetadata = new Google_Service_Drive_DriveFile(array(
				'name' => $file_name,
				'mimeType' => 'text/csv'));
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
			if($overwrite) {
				$content = $header . $content;
			} else {
				$file_contents = $service->files->get($result->id, array( 'alt' => 'media' ));
				error_log('File Contents: ');
				error_log($file_contents);
				$content = $file_contents . $content;
			}
			error_log('Updating google drive file: '.$file_name);
			$file = $service->files->update($result->id, $fileMetadata, array(
				'data' => $content,
				'mimeType' => 'text/csv',
				'uploadType' => 'multipart'));
		} else {
			$content = $header . $content;
			error_log('Creating google drive file: '.$file_name);
			$file = $service->files->create($fileMetadata, array(
				'data' => $content,
				'mimeType' => 'text/csv',
				'uploadType' => 'multipart'));
		}
		echo nl2br($content);
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
		$header = "id,last_name,first_name,company,sign_in_method,sign_in,time,checkin_count,location,plan_code,day_of_week,membership_id,plan_name\n";
		try {
			$checkinsfile = "";
			$util = new utilities;
			$date_today = '';
			if(!$from_datetime || !$to_datetime) {
				date_default_timezone_set('America/New_York');
				$date_today = date('Y-m-d', time());
				$time_tomorrow = strtotime("+1 day", strtotime($date_today));
				$date_tomorrow = date("Y-m-d", $time_tomorrow);
				$params = array('from' => urlencode($date_today." 00:00:00 -0400"), 'to' => urlencode($date_tomorrow." 00:00:00 -0400"));
			} else {
				$params = array('from' => urlencode($from_datetime), 'to' => urlencode($to_datetime));
			}
			$day_of_week = '';
			if($date_today) {
				$day_of_week = date( "w", $date_today) + 1;
			}
			
			$query = $this->db->get("cobot_spaces");
		    $spaces = $query->result();
		    foreach ($spaces as $space) {
		    	$space_checkins = array();
		    	$spacecheckinsfile = "";
		    	$url = "https://".$space->id.".cobot.me/api/work_sessions";
		    	$checkins = $this->do_get($url, NULL, $params);
		    	error_log(json_encode($checkins));
		    	$member_checkins = array();
		    	foreach ($checkins as $checkin) {
		    		error_log(json_encode($checkin));
		    		$membership_id = $checkin->membership->id;
		    		$from = $checkin->valid_from;
		    		$from_date = '';
		    		$from_time = '';
		    		if($from) {
		    			$from_date = substr($from, 0, strpos($from, " "));
		    			$from_time = substr($from, strpos($from, " ")+1, strpos($from, " ", strpos($from, " ")+1));
		    		}
		    		// Assuming only one day's checkins being handled; ignoring sorting by date; sorting only by time;
		    		// Update this to handle sorting by date
		    		if(!array_key_exists($membership_id, $member_checkins)) {
		    			$member_checkins[$membership_id] = array();
		    		}
		    		array_push($member_checkins[$membership_id], $from_time);
		    	}

		    	foreach ($member_checkins as $membership_id => $checkin_times) {
		    		error_log(json_encode($checkin_times));
		    		$checkin_count = count($checkin_times);
		    		sort($checkin_times);
		    		$first_checkin = current($checkin_times);
		    		$sql = "SELECT u.id as id, u.last_name as last_name, u.first_name as first_name, c.name as company, '".$date_today."' as sign_in, '".$first_checkin."' as time, '".$checkin_count."' as checkin_count, '".$space->id."' as location_id, cm.plan_id as plan_code, ".$day_of_week." as day_of_week, cm.id as membership_id, cm.plan_name as plan_name FROM cobot_memberships cm left join user u on cm.user_id = u.id left join company c on u.company_id = c.id where cm.space_id = '".$space->id."' AND cm.id='".$membership_id."'";
					error_log($sql);
					$query = $this->db->query($sql);
					$result = current($query->result());
					if($result) {
						$record = $result->id.','.$result->last_name.','.$result->first_name.','.$result->company.','.$result->sign_in_method.','.$result->sign_in.','.$result->time.','.$result->checkin_count.','.$result->location_id.','.$result->plan_code.','.$result->day_of_week.','.$result->membership_id.','.$result->plan_name."\n";
						//echo nl2br($record);
						$spacecheckinsfile .= $record;
						array_push($space_checkins, $result);
					}
		    	}

		    	error_log(json_encode($space_checkins));
		    	$this->write_to_google_drive('checkins-'.$space->id.'.csv', $spacecheckinsfile, $header, false);
		    }
		} catch(Exception $e){
			$error_msg = 'Exception getting all checkins : '.$e->getMessage();
			echo nl2br($error_msg);
			error_log($error_msg);
		}
	}

	function get_users() {
		echo nl2br("Fetching users ");
		$header = "id,company_id,name,rfid,wp_users_id,date_added,membership_status,referrer,twitter,behance,membership_id,plan_state,plan_code,quantity,activated_at,expires_at,canceled_at,current_period_started_at,current_period_ends_at,email_address,last_name,first_name,company,sign_in_method,sign_in,time,location_id,location_name,reason_for_unsubscribing,activated_date,date_activate_at,date_expires_at,date_date_added,cobot_id,last_invoice_date,next_invoice_date\n";
		try {
			$query = $this->db->get("cobot_spaces");
			$spaces = $query->result();
			foreach ($spaces as $space) {
				$space_users = array();
				$spacefile = "";

				$custom_fields_dict = array();
				$url = "https://".$space->id.".cobot.me/api/custom_fields";
				$membership_custom_fields = $this->do_get($url, NULL);
			    if(count($membership_custom_fields) > 0) {
			    	foreach ($membership_custom_fields as $membership_custom_field) {
			    		$membership_id = $membership_custom_field->membership_id;
			    		$custom_fields_dict[$membership_id] = array();
						$custom_fields = $membership_custom_field->fields;
						foreach ($custom_fields as $custom_field) {
							$label = $custom_field['label'];
							$value = $custom_field['value'];
							$custom_fields_dict[$membership_id][$label] = $value;
						}
			    	}
			    }
			    error_log(json_encode($custom_fields_dict));

				$url = "https://".$space->id.".cobot.me/api/memberships";
				$params = array("all" => true);
				$memberships = $this->do_get($url, NULL, $params);
			    error_log(json_encode($memberships));
			    if($memberships) {
			    	foreach ($memberships as $membership) {
			    		echo nl2br(".");
			    		$user = $membership->user;
			    		if($user) {
			    			$email_address = $membership->email;
			    			$membership_id = $membership->id;
				    		$plan_state = 'active';
				    		if($membership->canceled_to) {
				    			$plan_state = 'canceled';
				    		}
				    		$plan = $membership->plan;
				    		$plan_code = '';
				    		if($plan) {
				    			$parent_plan = $plan->parent_plan;
				    			if($parent_plan) {
				    				$plan_code = $parent_plan->id;
				    			}
				    		}
				    		$activated_at = $membership->confirmed_at;
				    		$canceled_at = $membership->canceled_to;
				    		$current_period_ends_at = $membership->next_invoice_at;
				    		$next_invoice_date = $membership->next_invoice_at;

			    			$cobot_id = $user->id;

							$sql = "SELECT u.id as id, u.company_id as company_id, concat(u.first_name,' ',u.last_name) as name, u.rfid as rfid, u.wp_users_id as wp_users_id, u.date_added as date_added, u.referrer as referrer, u.twitter as twitter, u.behance as behance, wp_user.user_email as email_address, u.last_name as last_name, u.first_name as first_name, c.name as company, tpu.network_id as cobot_id FROM user u join third_party_user tpu on tpu.user_id = u.id and tpu.network = 'cobot' left join wpmember_users wp_user on u.wp_users_id = wp_user.id left join company c on u.company_id = c.id where tpu.network_id = '".$cobot_id."'";
							error_log($sql);
							$query = $this->db->query($sql);
							$results = $query->result();
							if(count($results)>0) {
								$result = current($results);

								$location_name = array_key_exists($membership_id, $custom_fields_dict) ? ( array_key_exists('Home Space', $custom_fields_dict[$membership_id]) ? $custom_fields_dict[$membership_id]['Home Space'] : '' ) : '';
								error_log($location_name);

								$record = $result->id.','.$result->company_id.','.str_replace(',', ' ', $result->name).','.$result->rfid.','.$result->wp_users_id.','.$result->date_added.',,'.str_replace(',', ' ', $result->referrer).','.str_replace(',', ' ', $result->twitter).','.str_replace(',', ' ', $result->behance).','.$membership_id.','.$plan_state.','.$plan_code.',,'.$activated_at.',,'.$canceled_at.',,,'.$result->email_address.','.str_replace(',', ' ', $result->last_name).','.str_replace(',', ' ', $result->first_name).','.str_replace(',', ' ', $result->company).',,,,,'.str_replace(',', ' ', $location_name).',,,,,,'.$result->cobot_id.',,'.$next_invoice_date."\n";
								//echo nl2br($record);
								$spacefile .= $record;
				    			array_push($space_users, $record);
							}
			    		}
			    	}
			    }
			    error_log(json_encode($space_users));
			    echo nl2br("\n");
				$this->write_to_google_drive('users-'.$space->id.'.csv', $spacefile, $header);
			}
		} catch(Exception $e){
			$error_msg = 'Exception getting space users : '.$e->getMessage();
			echo nl2br($error_msg);
			error_log($error_msg);
		}
	}

	function get_invoices() {
		$header = "id,account_code,account_name,invoice_number,subscription_id,plan_code,coupon_code,total_subtotal,vat_amount,currency,date,status,closed_at,purchase_country,vat_number,line_item_total,line_item_description,line_item_origin,line_item_product_code,line_item_accounting_code,line_item_start_date,line_item_end_date,net_terms,po_number,collection_method,date_created,short_description,tax_amount,tax_type,tax_region,tax_rate,discount,line_item_uuid,date_closed_at,original_adjustment_uuid,modified_at,invoice_type,original_invoice_number,cobot_id,membership_id\n";
		try {
			$query = $this->db->get("cobot_spaces");
			$spaces = $query->result();
			foreach ($spaces as $space) {
				$space_users = array();
				$spacefile = "";
				$url = "https://".$space->id.".cobot.me/api/invoices";
				$invoices = $this->do_get($url, NULL);
			    error_log(json_encode($invoices));
			    if($invoices) {
			    	foreach ($invoices as $invoice) {
			    		$invoice_id = $invoice->id;
			    		$membership_id = $invoice->membership_id;
			    		$sql = "SELECT u.id as user_id, concat(u.first_name,' ',u.last_name) as name, cm.plan_id as plan_code, cm.cobot_user_id as cobot_id from cobot_memberships cm join user u on cm.user_id = u.id where cm.id = '".$membership_id."' and cm.space_id = '".$space->id."'";
						error_log($sql);
						$query = $this->db->query($sql);
						$results = $query->result();
						$account_code = '';
						$account_name = '';
						$plan_code = '';
						$cobot_id = '';
						if($results) {
							$result = current($results);
							$account_code = $result->user_id;
							$account_name = $result->name;
							$plan_code = $result->plan_code;
							$cobot_id = $result->cobot_id;
						}
						$invoice_number = $invoice->invoice_number;
						$total_subtotal = $invoice->total_amount_without_taxes;
						$vat_amount = $invoice->tax_amount;
						$currency = $invoice->currency;
						$date = $invoice->created_at;
						$status = $invoice->paid_status;
						$closed_at = $invoice->paid_at;
						$space_address = $invoice->space_address;
						$purchase_country = '';
						if($space_address) {
							$purchase_country = $space_address->country;
						}
						$vat_number = $invoice->tax_id;
						$date_created = $invoice->created_at;
						$tax_amount = $invoice->tax_amount;
						$tax_type = $invoice->tax_name;
						$tax_rate = $invoice->tax_rate;
						$items = $invoice->items;
						$short_description = '';
						$discount = 0.0;
						if($items) {
							foreach ($items as $item) {
								$short_description = $item->description."; ".$short_description;
								$item_val = floatval($item->amount);
								if($item_val < 0) {
									$discount = $discount + abs($item_val);
								}
							}
						}
						$collection_method = '';
						if(strtolower($status) == 'paid') {
							$url = "https://".$space->id.".cobot.me/api/invoices/payment_records";
							$params = array("invoice_ids" => $invoice_id);
							$payment_records = $this->do_get($url, NULL, $params);
							error_log(json_encode($payment_records));
							if(count($payment_records) > 0) {
								$payment_record = current($payment_records);
								$collection_method = $payment_record->note;
							}
						}

			    		$record = $invoice_id.','.$account_code.','.$account_name.','.$invoice_number.',,'.$plan_code.',,'.$total_subtotal.','.$vat_amount.','.$currency.','.$date.','.$status.','.$closed_at.','.$purchase_country.','.$vat_number.',,,,,,,,,,'.$collection_method.','.$date_created.','.$short_description.','.$tax_amount.','.$tax_type.',,'.$tax_rate.",".$discount.",,,,,,,".$cobot_id.",".$membership_id."\n";
			    		$spacefile .= $record;
			    		array_push($space_users, $record);
			    	}
			    }
			    error_log(json_encode($space_users));
				$this->write_to_google_drive('invoices-'.$space->id.'.csv', $spacefile, $header);
			}
		} catch(Exception $e){
			$error_msg = 'Exception getting space invoices : '.$e->getMessage();
			echo nl2br($error_msg);
			error_log($error_msg);
		}
	}

	function get_accounts() {
		$space_id = $_GET["space_id"];
		$email = $_GET["email"];
		$all_accounts = array();
		$header = "cobot_user_id,grind_user_id,email,currency,quantity,unit_amount,add_on_amount,total_recurring_amount,next_invoice_at,activated_at,canceled_to,collection_method,plan_name\n";
		try {
			$accountsfile = "";
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
						$accountsfile .= $record;
						array_push($all_accounts, $record);
			    	}
				}
			}
			error_log(json_encode($all_accounts));
			$this->write_to_google_drive('accounts.csv', $accountsfile, $header);
		} catch(Exception $e){
			$error_msg = 'Exception getting all accounts : '.$e->getMessage();
			echo nl2br($error_msg);
			error_log($error_msg);
		}
	}
}

?>