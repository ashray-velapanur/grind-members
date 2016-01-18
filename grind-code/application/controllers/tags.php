<?
class Tags extends CI_Controller {
	// create table tags (id INTEGER(10) UNSIGNED AUTO_INCREMENT primary key, name varchar(255));
	// create table user_tags (user_id INTEGER(10) UNSIGNED primary key, tag_id INTEGER(10) UNSIGNED);
	// create table job_tags (job_id INTEGER(10) UNSIGNED primary key, tag_id INTEGER(10) UNSIGNED);

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

	public function add(){
		$type = $_GET['type'];
		$entity_id = $_GET['entity_id'];
		$tag_id = $_GET['tag_id'];
		switch ($type) {
			case "user":
				$sql = "INSERT INTO user_tags (user_id, tag_id) VALUES ('$entity_id', '$tag_id')";
				break;
			case "job":
				$sql = "INSERT INTO job_tags (job_id, tag_id) VALUES ('$entity_id', '$tag_id')";
				break;				
		}
		if ($this->db->query($sql) === TRUE) {
			$response = array('success'=>TRUE);
		} else {
			$response = array('success'=>FALSE);
		}
		var_dump(json_encode($response));
	}
}
?>
