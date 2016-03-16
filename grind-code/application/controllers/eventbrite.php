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