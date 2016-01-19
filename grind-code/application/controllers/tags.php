<?
class Tags extends CI_Controller {
	// create table tags (id INTEGER(10) UNSIGNED AUTO_INCREMENT primary key, name varchar(255));
	// create table user_tags (user_id INTEGER(10) UNSIGNED, tag_id INTEGER(10) UNSIGNED, PRIMARY KEY (user_id, tag_id));
	// create table job_tags (job_id INTEGER(10) UNSIGNED, tag_id INTEGER(10) UNSIGNED, PRIMARY KEY (job_id, tag_id));

	public function new(){
		$name = $_GET['name'];
		$sql = "INSERT INTO tags (name) VALUES ('$name')";
		if ($this->db->query($sql) === TRUE) {
			$response = array('success'=>TRUE);
		} else {
			$response = array('success'=>FALSE);
		}
		var_dump(json_encode($response));
	}

	public function get_all(){
		$sql = "SELECT * FROM tags";
		$result = mysql_query($sql);
		$response = array();
		while($row = mysql_fetch_assoc($result)) {
			array_push($response, $row);
		}
		var_dump(json_encode($response));
	}

	public function get_for(){
		$type = $_GET['type'];
		$entity_id = $_GET['entity_id'];
		switch ($type) {
			case "user":
				$sql = "SELECT * FROM user_tags WHERE user_id='".$entity_id."'";
				break;
			case "job":
				$sql = "SELECT * FROM job_tags WHERE job_id='".$entity_id."'";
				break;
		}
		$result = mysql_query($sql);
		$response = array();
		while($row = mysql_fetch_assoc($result)) {
			$tag_sql = "SELECT * FROM tags WHERE id='".$row['tag_id']."'";
			$tag_result = mysql_query($tag_sql);
			$tag_row = mysql_fetch_assoc($tag_result);
			array_push($response, $tag_row);
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
