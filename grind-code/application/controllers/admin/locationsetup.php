<?
require(APPPATH.'/controllers/admin/spaces_dict.php');

class LocationSetup extends CI_Controller {
	function __construct() {
		parent::__construct();
		$this->output->enable_profiler(TRUE);
		$this->load->helper("url");
		$this->load->helper("form");
		$this->load->helper("html");
		$this->load->helper("nav_location");
		$this->load->helper("admin_layout");
	}

	public function add_locations() {
		global $environmentsToSpaces, $environmentSpaces, $environmentsToAccessToken, $spaceToMainArea;
		$this->delete_locations();
		$environment = $_GET["environment"];
		$spaces = $environmentsToSpaces[$environment];
		foreach ($spaces as $space_id) {
			$this->add_update_space($environmentSpaces[$space_id], $environment);

			$resource_data = array();
			$curl = curl_init();
			$url = 'https://'.$space_id.'.cobot.me/api/resources';
			$rdata = array(
			  'access_token' => $environmentsToAccessToken[$environment]
			);
			if ($rdata)
			      $url = sprintf("%s?%s", $url, http_build_query($rdata));
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			$result = curl_exec($curl);
			$result_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);
			$cobot_resources = array();
			if($result_code == 200) {
			  $result = (array)json_decode($result);
			  foreach ($result as $resource) {
			    $resource = (array)$resource;
			    $resource_id = $resource['id'];
			    if($resource_id == $spaceToMainArea[$space_id]) {
			    	$sql = "UPDATE cobot_spaces SET capacity = ".$resource['capacity'].", rate = ".$resource['price_per_hour']." WHERE id = '".$space_id."'";
					$this->db->query($sql);
			    }
			    else {
			    	$this->add_update_resource($resource, $space_id);
			    }
			  }
			}
		}
	}

	public function add_update_space($space, $environment) {
		$sql = "INSERT INTO cobot_spaces";
		$sql .= "(id, image, capacity, lat, lon, address, rate, name, description) ".
				"VALUES ".
				"(\"".$space['id']."\", \"".$space['imgName']."\", ".$space['capacity'].", \"".$space['lat']."\", \"".$space['long']."\", \"".$space['address']."\", ".$space['rate'].", \"".$space['name']."\", \"".$space['description']."\") ";
		try {
			error_log($sql);
			if ($this->db->query($sql) === TRUE) {
				echo "Record created/updated successfully";
				$this->setup_webhook_subscriptions($space['id'], $environment);
			} else {
				echo "Error: " . $sql . "<br>" . $this->db->error;
			}
		} catch (Exception $e) {
		    error_log('Caught exception: ',  $e->getMessage(), "\n");
		}
	}

	public function setup_webhook_subscriptions($space_id, $environment) {
		global $environmentsToAccessToken;
		$host = $_SERVER['SERVER_NAME'];
		if($host != 'localhost' && $host != '127.0.0.1') {
			$is_https = $_SERVER['HTTPS'];
			$callback_url = 'http://';
			if($is_https) {
				$callback_url = 'https://';
			}
			$callbacks = array("created_booking" => $callback_url.$host.'/grind-members/grind-code/index.php/cobot/booking_created',
							   "updated_booking" => $callback_url.$host.'/grind-members/grind-code/index.php/cobot/booking_updated',
							   "deleted_booking" => $callback_url.$host.'/grind-members/grind-code/index.php/cobot/booking_deleted',
							   "created_membership" => $callback_url.$host.'/grind-members/grind-code/index.php/cobot/membership_created',
							   "confirmed_membership" => $callback_url.$host.'/grind-members/grind-code/index.php/cobot/membership_created',
							   "connected_user" => $callback_url.$host.'/grind-members/grind-code/index.php/cobot/membership_created',
							   "canceled_membership" => $callback_url.$host.'/grind-members/grind-code/index.php/cobot/membership_canceled',
							   "created_checkin" => $callback_url.$host.'/grind-members/grind-code/index.php/cobot/update_space_capacity',
							   "created_checkout" => $callback_url.$host.'/grind-members/grind-code/index.php/cobot/update_space_capacity',
							   "changed_membership_plan" => $callback_url.$host.'/grind-members/grind-code/index.php/cobot/membership_plan_changed'
							   );
			foreach ($callbacks as $event => $url) {
				$subdomain = $space_id;
				$this->load->model("subscriptionmodel","sm",true);
				$this->sm->create_webhook_subscription($event, $url, $subdomain, $environmentsToAccessToken[$environment]);
			}
		}
	}

	public function add_update_resource($resource, $space_id) {
		$imgName = $resource['id'].'.png';
		$sql = "INSERT INTO cobot_resources";
		$rate = $resource['rate'] ? $resource['rate'] : 0.0;
		$sql .= "(id, space_id, image, name, capacity, rate, description) VALUES ('".$resource['id']."', '$space_id', '$imgName', '".$resource['name']."', 10, ".$rate.", \"".$resource['description']."\")";
		try {
			error_log($sql);
			if ($this->db->query($sql) === TRUE) {
				echo "Record created/updated successfully";
			} else {
				echo "Error: " . $sql . "<br>" . $this->db->error;
			}
		} catch (Exception $e) {
		    error_log('Caught exception: ',  $e->getMessage(), "\n");
		}
	}

	public function delete_locations() {
		$sql = "TRUNCATE TABLE cobot_bookings";
		error_log($sql);
		$this->db->query($sql);
		$sql = "TRUNCATE TABLE cobot_memberships";
		error_log($sql);
		$this->db->query($sql);
		$sql = "TRUNCATE TABLE cobot_webhook_subscriptions";
		error_log($sql);
		$this->db->query($sql);
		$sql = "TRUNCATE TABLE cobot_resources";
		error_log($sql);
		$this->db->query($sql);
		$sql = "TRUNCATE TABLE cobot_spaces";
		error_log($sql);
		$this->db->query($sql);
	}
}
?>