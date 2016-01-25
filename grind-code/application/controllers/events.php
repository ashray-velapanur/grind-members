<?
class Events extends CI_Controller {
	public function save_event(){
		$event_id = $_GET['event_id'];
		$name = $_GET['name'];
		$sql = "INSERT INTO events (id, name) VALUES ('$event_id', '$name')";
		$response = array();
		if ($this->db->query($sql) === TRUE) {
			$response = array('success'=>TRUE);
		} else {
			$response = array('success'=>FALSE);
		}
		var_dump($response);
	}
}
?>
