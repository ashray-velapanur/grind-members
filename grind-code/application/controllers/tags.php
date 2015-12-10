<?
class Tags extends CI_Controller {
	public function test(){
		error_log('asdasdas');
	}

	public function save_tag(){
		$name = $_GET['name'];
		$entity_id = $_GET['entity_id'];
		$type = $_GET['type'];
		$sql = "INSERT INTO tags (name, type, entity_id) VALUES ('$name', '$type', '$entity_id')";
		if ($this->db->query($sql) === TRUE) {
			error_log('done!');
		} else {
			error_log('nope...');
		}
	}
}
?>
