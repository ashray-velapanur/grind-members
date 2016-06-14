<?
require(APPPATH.'/config/cobot.php');
require(APPPATH.'/controllers/admin/spaces_dict.php');
include_once APPPATH . 'libraries/utilities.php';
//require('./admin/spaces_dict.php');

class Cobot extends CI_Controller {
	public function test(){
		error_log('cobot testing');
	}

	public function populate_users(){
		$file = fopen('cobot.csv', 'r');
		while (!feof($file)) {
			$lines[] = fgetcsv($file, 1024);
		}
		fclose($file);
		foreach ($lines as $line) {
			$user = explode(',', $line);
			$sql = "INSERT INTO third_party_user (user_id, network, access_token) VALUES ('$user[0]', 'cobot', '$user[1]')";
			$this->db->query($sql);
		}
	}

	function booking_created() {
		$_json = file_get_contents("php://input");
		$_POST = json_decode($_json, true);
		$booking_url = $_POST['url'];
		error_log('Booking URL: '.$booking_url);
		$subdomain_start = strpos($booking_url, '://') + 3;
		$subdomain_end = strpos($booking_url, '.cobot.me/api/bookings/');
		$subdomain = substr($booking_url, $subdomain_start, $subdomain_end-$subdomain_start);
		$id_start = strpos($booking_url, '.cobot.me/api/bookings/') + 23;
		$id = substr($booking_url, $id_start);
		$booking = json_decode($this->get_booking_details($booking_url, $subdomain));
		$sql = "SELECT * FROM cobot_bookings where space_id = '".$subdomain."' AND id='".$id."'";
		error_log($sql);
		$query = $this->db->query($sql);
		$bookings = $query->result();
		error_log(count($bookings));
		if(count($bookings) <= 0) {
			$sql = "INSERT INTO cobot_bookings (space_id, id, from_datetime, to_datetime, title, resource_id, resource_name, membership_id, membership_name, price, tax_rate, cancellation_period, comments) VALUES ('$subdomain', '$id', '$booking->from_datetime', '$booking->to_datetime', '$booking->title', '$booking->resource_id', '$booking->resource_name', '$booking->membership_id', '$booking->membership_name', $booking->price, $booking->tax_rate, $booking->cancellation_period, '$booking->comments')";
			error_log($sql);
			$this->db->query($sql);
			if(strtolower($booking->resource_name) == 'main area') {
				$sql = "UPDATE cobot_spaces SET checkins = checkins + 1 where id = '".$subdomain."'";
				error_log($sql);
				$this->db->query($sql);
			}
		}
		return $booking_url;
	}

	function booking_updated() {
		$_json = file_get_contents("php://input");
		$_POST = json_decode($_json, true);
		$booking_url = $_POST['url'];
		error_log('Booking URL: '.$booking_url);
		$subdomain_start = strpos($booking_url, '://') + 3;
		$subdomain_end = strpos($booking_url, '.cobot.me/api/bookings/');
		$subdomain = substr($booking_url, $subdomain_start, $subdomain_end-$subdomain_start);
		$id_start = strpos($booking_url, '.cobot.me/api/bookings/') + 23;
		$id = substr($booking_url, $id_start);
		$booking = json_decode($this->get_booking_details($booking_url, $subdomain));
		$sql = "UPDATE cobot_bookings SET from_datetime = '$booking->from_datetime', to_datetime = '$booking->to_datetime', title = '$booking->title', resource_id = '$booking->resource_id', resource_name = '$booking->resource_name', membership_id = '$booking->membership_id', membership_name = '$booking->membership_name', price = $booking->price, tax_rate = $booking->tax_rate, cancellation_period = $booking->cancellation_period, comments = '$booking->comments' WHERE space_id = '$subdomain' and id = '$id' ";
		error_log($sql);
		$this->db->query($sql);
		return $booking_url;
	}

	function booking_deleted() {
		$_json = file_get_contents("php://input");
		$_POST = json_decode($_json, true);
		$booking_url = $_POST['url'];
		error_log('Booking URL: '.$booking_url);
		$subdomain_start = strpos($booking_url, '://') + 3;
		$subdomain_end = strpos($booking_url, '.cobot.me/api/bookings/');
		$subdomain = substr($booking_url, $subdomain_start, $subdomain_end-$subdomain_start);
		$id_start = strpos($booking_url, '.cobot.me/api/bookings/') + 23;
		$id = substr($booking_url, $id_start);
		$sql = "SELECT * FROM cobot_bookings where space_id = '".$subdomain."' AND id='".$id."'";
		error_log($sql);
		$query = $this->db->query($sql);
		$bookings = $query->result();
		error_log(count($bookings));
		if(count($bookings) > 0) {
			$sql = "DELETE FROM cobot_bookings WHERE space_id='".$subdomain."' and id='".$id."'";
			error_log($sql);
			$this->db->query($sql);
			if(strtolower($booking->resource_name) == 'main area') {
				$sql = "UPDATE cobot_spaces SET checkins = checkins - 1 where id = '".$subdomain."'";
				error_log($sql);
				$this->db->query($sql);
			}
		}
		return $booking_url;
	}

	function get_booking_details($booking_url, $space_id) {
		$booking_details = array();
		$util = new utilities;
		$admin_access_token = $util->get_current_environment_cobot_access_token();
		$curl = curl_init();
		$url = $booking_url.'?access_token='.$admin_access_token;
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($curl);
		$result_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		if($result_code == 200) {
			$booking = (array)json_decode($result);
			$from = date_create($booking['from'])->format('Y-m-d H:i:s');
			$to = date_create($booking['to'])->format('Y-m-d H:i:s');
			$title = $booking['title'];
			$resource_name = $booking['resource_name'];
			$price = $booking['price'];
			$tax_rate = $booking['tax_rate'];
			$membership_id = $booking['membership_id'];
			$membership_name = $booking['membership']->name;
			$resource_id = $booking['resource']->id;
			$cancellation_period = $booking['cancellation_period'];
			$comments = $booking['comments'];
			$booking_details = array(
				'from_datetime' => $from,
				'to_datetime' => $to,
				'title' => $title,
				'resource_id' => $resource_id,
				'resource_name' => $resource_name,
				'membership_id' => $membership_id,
				'membership_name' => $membership_name,
				'price' => $price,
				'tax_rate' => $tax_rate,
				'cancellation_period' => $cancellation_period,
				'comments' => $comments
			);
	    }
		return json_encode($booking_details);
	}

	function membership_created() {
		error_log('Handling membership created webhook');
		$_json = file_get_contents("php://input");
		$_POST = json_decode($_json, true);
		$membership_url = $_POST['url'];
		error_log($membership_url);
		$id_subdomain = $this->get_membership_id_and_subdomain($membership_url);
		$subdomain = $id_subdomain['subdomain'];
		$id = $id_subdomain['id'];
		$membership = json_decode($this->get_membership_details($membership_url, $subdomain));
		error_log(json_encode($membership));
		if($membership->cobot_user_id) {
			$sql = "INSERT INTO cobot_memberships (space_id, id, user_id, cobot_user_id, name, plan_name, plan_id";
			$values = " VALUES ('$subdomain', '$id', '$membership->user_id', '$membership->cobot_user_id', '$membership->name', '$membership->plan_name', '$membership->plan_id'";
			if($membership->starts_at) {
				$sql = $sql.", starts_at";
				$values = $values.", '$membership->starts_at'";
			}
			if($membership->canceled_to) {
				$sql = $sql.", canceled_to";
				$values = $values.", '$membership->canceled_to'";
			}
			$sql = $sql.")";
			$values = $values.")";
			$sql = $sql.$values;
			error_log($sql);
			$this->db->query($sql);
		}
		return $membership_url;
	}

	function membership_canceled() {
		error_log('Handling membership canceled webhook');
		$_json = file_get_contents("php://input");
		$_POST = json_decode($_json, true);
		$membership_url = $_POST['url'];
		$id_subdomain = $this->get_membership_id_and_subdomain($membership_url);
		$subdomain = $id_subdomain['subdomain'];
		$id = $id_subdomain['id'];
		$membership = json_decode($this->get_membership_details($membership_url, $subdomain));
		$sql = "UPDATE cobot_memberships SET canceled_to = '$membership->canceled_to' where space_id = '$subdomain' and id = '$id'";
		error_log($sql);
		$this->db->query($sql);
		return $membership_url;
	}

	function membership_plan_changed() {
		error_log('Handling membership plan changed webhook');
		$_json = file_get_contents("php://input");
		$_POST = json_decode($_json, true);
		$membership_url = $_POST['url'];
		$id_subdomain = $this->get_membership_id_and_subdomain($membership_url);
		$subdomain = $id_subdomain['subdomain'];
		$id = $id_subdomain['id'];
		$membership = json_decode($this->get_membership_details($membership_url, $subdomain));
		$sql = "UPDATE cobot_memberships SET plan_name = '$membership->plan_name' where space_id = '$subdomain' and id = '$id'";
		error_log($sql);
		$this->db->query($sql);
		return $membership_url;
	}

	function get_membership_id_and_subdomain($membership_url) {
		$subdomain_start = strpos($membership_url, '://') + strlen('://');
		$subdomain_end = strpos($membership_url, '.cobot.me/api/memberships/');
		$subdomain = substr($membership_url, $subdomain_start, $subdomain_end-$subdomain_start);
		$id_start = strpos($membership_url, '.cobot.me/api/memberships/') + strlen('.cobot.me/api/memberships/');
		$id = substr($membership_url, $id_start);
		return array('subdomain'=>$subdomain, 'id'=>$id);
	}

	function update_space_capacity() {
		$_json = file_get_contents("php://input");
		$_POST = json_decode($_json, true);
		$checkin_url = $_POST['url'];
		$subdomain_start = strpos($checkin_url, '://') + strlen('://');
		$subdomain_end = strpos($checkin_url, '.cobot.me/api/check_ins/');
		$subdomain = substr($checkin_url, $subdomain_start, $subdomain_end-$subdomain_start);
		$url = 'https://'.$subdomain.'.cobot.me/api/check_ins';
		$util = new utilities;
		$result = $this->do_get($url, $util->get_environment_for($subdomain));
		$checkin_count = count($result);
		$sql = "UPDATE cobot_spaces SET checkins = $checkin_count where id = '$subdomain'";
		error_log($sql);
		$this->db->query($sql);

		// Get checkin id
		$checkinid_start = strpos($checkin_url, '.cobot.me/api/check_ins/') + strlen('.cobot.me/api/check_ins/');
		$checkinid = substr($checkin_url, $checkinid_start);
		error_log('Checkin Id: '.$checkinid);
		// Get membership id from checkin id
		$result = $this->do_get($checkin_url, $util->get_environment_for($subdomain));
		$membership_id = $result['membership_id'];
		error_log('Membership Id: '.$membership_id);
		if($membership_id) {
			// Check if membership id daily plan
			$sql = "SELECT cm.plan_name plan_name, cs.rate rate FROM cobot_memberships cm join cobot_spaces cs on cm.space_id = cs.id WHERE cs.id='".$subdomain."'";
			error_log($sql);
			$query = $this->db->query($sql);
			$results = $query->result();
			if($results) {
				$result = current($results);
				error_log(json_encode($result));
				$plan_name = $result->plan_name;
				$price = $result->rate;
				if(strtolower($plan_name) == 'daily') {
					$invoice_url = 'https://'.$subdomain.'.cobot.me/api/memberships/'.$membership_id.'/invoices';
					$params = array("items" => array(array("amount" => "$price","description" => "Checkin: ".$checkinid." at space: ".$subdomain,"quantity" => "1")));
					echo "Will create invoice for checkin_id: ".$checkinid." for price: $".$price;
					error_log("Will create invoice for checkin_id: ".$checkinid." for price: $".$price);
					$access_token = $util->get_current_environment_cobot_access_token();
					$result = $util->do_post($invoice_url, $params, $access_token);
					if($result && count($result) > 0) {
						error_log('Invoice created with id: '.$result['id'].' and number: '.$result['invoice_number'].' and url: '.$result['url'].' for checkin id: '.$checkinid);
						echo ' *** Invoice created with id: '.$result['id'].' and number: '.$result['invoice_number'].' and url: '.$result['url'].' for checkin id: '.$checkinid."\r\n";
						$charge_url = 'https://'.$subdomain.'.cobot.me/api/invoices/'.$result['invoice_number'].'/charges';
						//$charge_result = $util->do_post($charge_url, array(), $access_token);
						echo " *** Charge made for invoice number: ".$result['invoice_number']."\r\n";
						error_log(" *** Charge made for invoice number: ".$result['invoice_number']);
					}
				}
			}
		}
	}

	function get_membership_details($membership_url, $space_id) {
		global $cobot_network_name;
		$membership_details = array();
		$util = new utilities;
		$admin_access_token = $util->get_current_environment_cobot_access_token();
		$curl = curl_init();
		$url = $membership_url.'?access_token='.$admin_access_token;
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($curl);
		$result_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		if($result_code == 200) {
			$membership = (array)json_decode($result);
			$name = $membership['name'];
			$plan_name = $membership['plan']->name;
			$plan = $membership['plan']->parent_plan;
			$plan_id = $plan->id;
			error_log('plan_id');
			error_log($plan_id);			
			$cobot_user_id = $membership['user']->id;
			error_log('cobot_user_id');
			error_log($result);
			error_log($cobot_user_id);
		    $sql = "SELECT user_id from third_party_user where network = '".$cobot_network_name."' and network_id = '".$cobot_user_id."'";
			error_log($sql);
			$query = $this->db->query($sql);
			$third_party_results = $query->result();
		    $cobot_user = current($third_party_results);
		    $user_id = $cobot_user->user_id;
		    $canceled_to = NULL;
		    if($membership['canceled_to']) {
		    	$canceled_to = date_create($membership['canceled_to'])->format('Y-m-d H:i:s');
		    }
		    $starts_at = NULL;
		    if($membership['starts_at']) {
		    	$starts_at = date_create($membership['starts_at'])->format('Y-m-d H:i:s');
		    }
			$membership_details = array(
				'name' => $name,
				'plan_name' => $plan_name,
				'plan_id' => $plan_id,
				'cobot_user_id' => $cobot_user_id,
				'user_id' => $user_id,
				'canceled_to' => $canceled_to,
				'starts_at' => $starts_at
			);
	    }
		return json_encode($membership_details);
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
		if($result_code == 200) {
			$get_result = (array)json_decode($result);
		}
		//error_log(json_encode($get_result));
		return $get_result;
	}

	function add_rfid_tokens() {
		$util = new utilities;
    	$access_token = $util->get_current_environment_cobot_access_token();
		$query = $this->db->get("cobot_spaces");
	    $spaces = $query->result();
	    foreach ($spaces as $space) {
	    	$token_url = 'https://'.$space->id.'.cobot.me/api/check_in_tokens';
			$sql = "SELECT u.id as user_id, u.rfid as user_rfid, cm.id as membership_id FROM cobot_memberships cm join user u on cm.user_id = u.id where cm.space_id = '".$space->id."'";
			error_log($sql);
			$query = $this->db->query($sql);
			$members = $query->result();
			error_log(json_encode($members));
			foreach ($members as $member) {
				$token = "test-token-"."$member->membership_id";
				if($member->user_rfid) {
					$token = "$member->user_rfid";
				}
				$params = array(
					"membership_id" => "$member->membership_id",
					"token" => $token
				);
				$util->do_post($token_url, $params, $access_token);
			}
		}
	}

	function create_cobot_memberships() {
		$cobot_user_id = $_GET["cobot_user_id"];
		$user_name = $_GET["user_name"];
		if($cobot_user_id && $user_name) {
			$this->load->model("loginmodel","lgnm",true);
        	$this->lgnm->create_cobot_membership($cobot_user_id, $user_name." Daily Plan");
		}
	}

	function clear_unwanted_subscriptions() {
		$this->load->model("subscriptionmodel","sm",true);
		$query = $this->db->get("cobot_spaces");
	    $spaces = $query->result();
	    foreach ($spaces as $space) {
	    	try {
	    		$sql = "SELECT url FROM cobot_webhook_subscriptions where space_id = '".$space->id."'";
				error_log($sql);
				$query = $this->db->query($sql);
				$results = $query->result();
				$valid_urls = [];
				foreach ($results as $result) {
					array_push($valid_urls, $result->url);
				}
				error_log('Valid urls: '.json_encode($valid_urls));
		    	$subscriptions_listing_url = "https://".$space->id.".cobot.me/api/subscriptions";
				$result = $this->do_get($subscriptions_listing_url, NULL);
				foreach ($result as $subscription) {
					$callback_url = $subscription->callback_url;
					$subscription_url = $subscription->url;
					if(!in_array($subscription_url, $valid_urls)) {
						$expected_callback = ROOTMEMBERPATH."grind-code/index.php/cobot/";
						error_log('expected_callback: '.$expected_callback);
						if($callback_url) {
							$pos = strpos($callback_url, $expected_callback);
							if (!($pos === false)) {
								error_log('expected_callback found... removing');
								error_log('callback_url: '.$callback_url);
								error_log('subscription_url: '.$subscription_url);
								//$this->sm->delete_webhook_subscription($subscription_url);
							}
						}
					}
				}
	    	} catch(Exception $e){
				error_log('Exception during clearing unwanted subscriptions : '.$e->getMessage());
			}
	    }
	}
}

//$temp = new Cobot;
//print_r($temp->get_environment_for("grind-park-ave"));
?>