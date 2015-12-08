<?
class Events extends CI_Controller {
	public function test(){
		error_log('asdasdas');
	}

	public function save_event(){
		$event_id = $_GET['event_id'];
		$name = $_GET['name'];
		$sql = "INSERT INTO events (event_id, name) VALUES ('$event_id', '$name')";
		if ($this->db->query($sql) === TRUE) {
			error_log('done!');
		} else {
			error_log('nope...');
		}
	}

	public function get_event(){
		$event_id = $_GET['id'];
		$sql = "SELECT * FROM events WHERE id=('$event_id')";
		if ($this->db->query($sql) === TRUE) {
			error_log('done!');
		} else {
			error_log('nope...');
		}
	}


}
?>
