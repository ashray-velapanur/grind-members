<?
class Events extends CI_Controller {
	public function test(){
		error_log('asdasdas');
	}

	public function save_event(){
		$event_id = $_GET['event_id'];
		$sql = "INSERT INTO events (event_id) VALUES ('$event_id')";
		if ($this->db->query($sql) === TRUE) {
			error_log('done!');
		} else {
			error_log('nope...');
		}
	}
}
?>
