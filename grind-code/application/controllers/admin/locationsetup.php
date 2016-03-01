<?
require(APPPATH.'/config/cobot.php');

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
		global $cobot_admin_access_token;
		$this->delete_locations();
		$spaces_dict = $this->get_spaces_dict();
		$email = $_GET["email"];
		$spaces = $spaces_dict[$email];
		foreach ($spaces as $space) {
			error_log(json_encode($space));
			$space_id = $space['id'];
			error_log($space_id);
			$this->add_update_space($space_id, $space['capacity'], $space['imgName'], $space['lat'], $space['long'], $space['address'], $space['rate'], $space['name'], $space['description']);

			$resource_data = array();
			$curl = curl_init();
			$url = 'https://'.$space_id.'.cobot.me/api/resources';
			$rdata = array(
			  'access_token' => $cobot_admin_access_token
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
			    $this->add_update_resource($resource['id'], $space_id, 'parkave-think.jpg');
			  }
			}
		}
	}

	public function get_spaces_dict() {
		$spaces_dict = array();
		$spaces_dict["ranju@b-eagles.com"] = array(
			array(
				'id' => 'grind-park-avenue',
				'capacity' => 100,
				'imgName' => 'parkavenue.png',
				'lat' => '40.618',
				'long' => '-78.116',
				'address' => '419 Park Avenue South New York NY 10016',
				'rate' => 55,
				'name' => 'PARK AVE',
				'description' => 'Description Here...'
			),
			array(
				'id' => 'pirates-lasalle',
				'capacity' => 100,
				'imgName' => 'parkavenue.png',
				'lat' => '40.618',
				'long' => '-78.116',
				'address' => '419 Park Avenue South New York NY 10016',
				'rate' => 55,
				'name' => 'PARK AVE',
				'description' => 'Description Here...'
			),
			array(
				'id' => 'pirates-downtown',
				'capacity' => 100,
				'imgName' => 'parkavenue.png',
				'lat' => '40.618',
				'long' => '-78.116',
				'address' => '419 Park Avenue South New York NY 10016',
				'rate' => 55,
				'name' => 'PARK AVE',
				'description' => 'Description Here...'
			),
			array(
				'id' => 'pirates-broadway-30',
				'capacity' => 100,
				'imgName' => 'parkavenue.png',
				'lat' => '40.618',
				'long' => '-78.116',
				'address' => '419 Park Avenue South New York NY 10016',
				'rate' => 55,
				'name' => 'PARK AVE',
				'description' => 'Description Here...'
			),
			array(
				'id' => 'pirates-broadway',
				'capacity' => 100,
				'imgName' => 'parkavenue.png',
				'lat' => '40.618',
				'long' => '-78.116',
				'address' => '419 Park Avenue South New York NY 10016',
				'rate' => 55,
				'name' => 'PARK AVE',
				'description' => 'Description Here...'
			)
		);
		$spaces_dict["rajiv@latticefund.com"] = array(
			array(
				'id' => 'grind-park-ave',
				'capacity' => 100,
				'imgName' => 'parkavenue.png',
				'lat' => '40.618',
				'long' => '-78.116',
				'address' => '419 Park Avenue South New York NY 10016',
				'rate' => 55,
				'name' => 'Grind Park Ave/29th',
				'description' => "Grind Park is Grind's first location, built in 2011. It is predominantly open, non-dedicated space, but contains Team Rooms and Conference Rooms in addition, making it a home for almost all of Grind's varied memberships."
			),
			array(
				'id' => 'grind-lasalle-washington',
				'capacity' => 50,
				'imgName' => 'lasalle.jpg',
				'lat' => '41.882',
				'long' => '-87.634',
				'address' => '2 North LaSalle St, 14th Fl. Chicago IL 60602',
				'rate' => 45,
				'name' => 'Grind LaSalle/Washington',
				'description' => "Grind's first location in Chicago. Located right in The Loop, it boasts 22 foot high ceilings, double windows, and a variety of workspaces and memberships."
			),
			array(
				'id' => 'grind-broadway-39th',
				'capacity' => 85,
				'imgName' => 'broadway.jpg',
				'lat' => '40.262',
				'long' => '-78.321',
				'address' => '1412 Broadway, 22nd Fl. New York NY 10018',
				'rate' => 50,
				'name' => 'Grind Broadway/39th',
				'description' => "A short jaunt from 12 different subway lines as well as Penn Station and The Port Authority, Grind Broadway is one of the more convenient destinations in NYC."
			)
		);
		return $spaces_dict;
	}

	public function add_update_space($space_id, $capacity, $imgName, $lat, $long, $address, $rate, $name, $description) {
		error_log("In add_update_space");
		if($space_id) {
			if(!$capacity) {
				$capacity = 0;
			}
			$address = trim($address);
			if(!$rate) {
				$rate = 0.0;
			}
			$sql = "INSERT INTO cobot_spaces";
			$sql .= "(id, image, capacity, lat, lon, address, rate, name, description) VALUES ('$space_id', '$imgName', $capacity, '$lat', '$long', '$address', $rate, '$name', \"$description\") ";
			if($imgName || $capacity || $lat || $long || $address || $rate || $name || $description) {
				$sql .= " ON DUPLICATE KEY UPDATE ";
				$comma = " ";
				if($imgName) {
					$sql = $sql.$comma." image = '".$imgName."'";
					$comma = " , ";
				}
				if($capacity) {
					$sql = $sql.$comma." capacity = ".$capacity;
					$comma = " , ";
				}
				if($lat) {
					$sql = $sql.$comma." lat = '".$lat."'";
					$comma = " , ";
				}
				if($long) {
					$sql = $sql.$comma." lon = '".$long."'";
					$comma = " , ";
				}
				if($address) {
					$sql = $sql.$comma." address = '".$address."'";
					$comma = " , ";
				}
				if($rate) {
					$sql = $sql.$comma." rate = ".$rate;
					$comma = " , ";
				}
				if($name) {
					$sql = $sql.$comma." name = '".$name."'";
					$comma = " , ";
				}
				if($description) {
					$sql = $sql.$comma." description = \"".$description."\"";
					$comma = " , ";
				}
			}
			try {
				error_log($sql);
				if ($this->db->query($sql) === TRUE) {
					echo "Record created/updated successfully";
					$this->setup_webhook_subscriptions($space_id);
				} else {
					echo "Error: " . $sql . "<br>" . $this->db->error;
				}
			} catch (Exception $e) {
			    error_log('Caught exception: ',  $e->getMessage(), "\n");
			}
		} else {
			echo "Specify a cobot ID for the space";
		}
	}

	public function setup_webhook_subscriptions($space_id) {
		$host = $_SERVER['SERVER_NAME'];
		error_log($host);
		if($host != 'localhost' && $host != '127.0.0.1') {
			$is_https = $_SERVER['HTTPS'];
			$callback_url = 'http://';
			if($is_https) {
				$callback_url = 'https://';
			}
			// Create created_booking webhook
			$callback_url = $callback_url.$host.'/grind-members/grind-code/index.php/cobot/booking_created';
			error_log($callback_url);
			$event = 'created_booking';
			$subdomain = $space_id;
			$this->load->model("subscriptionmodel","sm",true);
			$subscription_url = $this->sm->create_webhook_subscription($event, $callback_url, $subdomain);
			// Create updated_booking webhook
			$callback_url = $callback_url.$host.'/grind-members/grind-code/index.php/cobot/booking_updated';
			error_log($callback_url);
			$event = 'updated_booking';
			$subdomain = $space_id;
			$this->load->model("subscriptionmodel","sm",true);
			$subscription_url = $this->sm->create_webhook_subscription($event, $callback_url, $subdomain);
			// Create deleted_booking webhook
			$callback_url = $callback_url.$host.'/grind-members/grind-code/index.php/cobot/booking_deleted';
			error_log($callback_url);
			$event = 'deleted_booking';
			$subdomain = $space_id;
			$this->load->model("subscriptionmodel","sm",true);
			$subscription_url = $this->sm->create_webhook_subscription($event, $callback_url, $subdomain);
			// Create created_membership webhook
			$callback_url = $callback_url.$host.'/grind-members/grind-code/index.php/cobot/membership_created';
			error_log($callback_url);
			$event = 'created_membership';
			$subdomain = $space_id;
			$this->load->model("subscriptionmodel","sm",true);
			$subscription_url = $this->sm->create_webhook_subscription($event, $callback_url, $subdomain);
			// Create confirmed_membership webhook
			$callback_url = $callback_url.$host.'/grind-members/grind-code/index.php/cobot/membership_created';
			error_log($callback_url);
			$event = 'confirmed_membership';
			$subdomain = $space_id;
			$this->load->model("subscriptionmodel","sm",true);
			$subscription_url = $this->sm->create_webhook_subscription($event, $callback_url, $subdomain);
			// Create connected_user webhook
			$callback_url = $callback_url.$host.'/grind-members/grind-code/index.php/cobot/membership_created';
			error_log($callback_url);
			$event = 'connected_user';
			$subdomain = $space_id;
			$this->load->model("subscriptionmodel","sm",true);
			$subscription_url = $this->sm->create_webhook_subscription($event, $callback_url, $subdomain);
			// Create canceled_membership webhook
			$callback_url = $callback_url.$host.'/grind-members/grind-code/index.php/cobot/membership_canceled';
			error_log($callback_url);
			$event = 'canceled_membership';
			$subdomain = $space_id;
			$this->load->model("subscriptionmodel","sm",true);
			$subscription_url = $this->sm->create_webhook_subscription($event, $callback_url, $subdomain);
		}
	}

	public function add_update_resource($cobot_resource_id, $space_id, $imgName) {
		error_log("In add_update_resource");
		$sql = "INSERT INTO cobot_resources";
		$sql .= "(id, space_id, image) VALUES ('$cobot_resource_id', '$space_id', '$imgName')";
		if($imgName) {
			$sql .= " ON DUPLICATE KEY UPDATE ";
			$sql .= " image = '".$imgName."'";
		}
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