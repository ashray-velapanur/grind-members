<?
class Tags extends CI_Controller {
	// create table tags (id INTEGER(10) UNSIGNED AUTO_INCREMENT primary key, name varchar(255));

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

	public function create(){
		$name = $_GET['name'];
		$sql = "INSERT INTO tags (name) VALUES ('$name')";
		if ($this->db->query($sql) === TRUE) {
			$response = array('success'=>TRUE);
		} else {
			$response = array('success'=>FALSE);
		}
		var_dump(json_encode($response));
	}

	public function get(){
		$sql = "SELECT * FROM tags";
		$result = mysql_query($sql);
		$response = array();
		while($row = mysql_fetch_assoc($result)) {
			array_push($response, $row);
		}
		var_dump(json_encode($response));
	}
}
?>
