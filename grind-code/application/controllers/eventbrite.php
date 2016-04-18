<?
include_once APPPATH . 'libraries/utilities.php';

class Eventbrite extends CI_Controller {
	public function callback(){
		error_log('... eventbrite callback');
		$response = $this->get_event_from_callback();
		$name = $response->name;
		$id = $response->id;
		$sql = "INSERT INTO events (id, name) VALUES ('$id', '$name->text')";
		$this->db->query($sql);
	}

	public function event_unpublished(){
		error_log('... eventbrite event unpublished');
		$response = $this->get_event_from_callback();
		$id = $response->id;
		$sql = "DELETE FROM event_tags WHERE event_id='$id'";
		error_log($sql);
		$this->db->query($sql);
		$sql = "DELETE FROM events WHERE id='$id'";
		error_log($sql);
		$this->db->query($sql);
	}

	public function add_token() {
		$util = new utilities;
		if(isset($_POST["submit"])) {
			$eb_user_id = $_POST["eb_user_id"];
			$eb_token = $_POST["eb_token"];
			$this->load->model("eventsmodel","em",true);
			if ($this->em->add_eventbrite_token($eb_user_id, $eb_token) === TRUE) {
				error_log("Added Eventbrite access token successfully");
				//Delete events fetched with older token and user ID
				$sql = "TRUNCATE TABLE events";
				error_log($sql);
				$this->db->query($sql);
				//Pull events with newer token and user ID
				$url = 'https://www.eventbriteapi.com/v3/users/'.$eb_user_id.'/owned_events/';
				$params = array(
					'token' => $eb_token,
					'status' => 'live,started,ended'
				);
				$results = $util->do_get($url, $params);
				$events = $results['events'];
				foreach ($events as $event) {
					$event = (array)$event;
					$event_name = $event['name'];
					$this->em->create($event['id'], $event_name->text);
				}
			} else {
				error_log("Error adding Eventbrite access token");
			}
		}
		$util->redirect(ROOTMEMBERPATH.'grind-code/index.php/eventbrite/tokens');
	}

	public function delete_token() {
		$eb_user_id = $_GET["eb_user_id"];
		$this->load->model("eventsmodel","em",true);
		if ($this->em->delete_eventbrite_token($eb_user_id) === TRUE) {
			error_log("Deleted Eventbrite access token successfully");
		} else {
			error_log("Error deleting Eventbrite access token");
		}
		$util = new utilities;
		$util->redirect(ROOTMEMBERPATH.'grind-code/index.php/eventbrite/tokens');
	}

	public function tokens() {
		error_log("In Eventbrite Tokens");
		$query = $this->db->get('eventbrite');
    	$results = $query->result();
    	$data = array('results'=>$results);
    	$this->load->view("/admin/show_eventbrite.php", $data);
	}

	private function get_event_from_callback() {
		$request_body = file_get_contents('php://input');
		$data = json_decode($request_body);
		$url = $data->api_url;
		$url = $url."?token=EYFPEMS6IJLSNOXNVH56";
		error_log($url);
		$response = json_decode(file_get_contents($url));
		error_log(json_encode($response));
		return $response;
	}
}

?>