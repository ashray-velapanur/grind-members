<?
include_once APPPATH . 'libraries/utilities.php';

class Eventbrite extends CI_Controller {
	public function callback(){
		error_log('... eventbrite callback');
		$request_body = file_get_contents('php://input');
		$data = json_decode($request_body);
		$url = $data->api_url;
		$url = $url."?token=EYFPEMS6IJLSNOXNVH56";
		error_log($url);
		$response = json_decode(file_get_contents($url));
		$name = $response->name;
		$id = $response->id;
		$sql = "INSERT INTO events (id, name) VALUES ('$id', '$name->text')";
		$this->db->query($sql);
	}
}

?>