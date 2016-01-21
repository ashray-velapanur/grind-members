<?
class Tags extends CI_Controller {
	// create table tags (id INTEGER(10) UNSIGNED AUTO_INCREMENT primary key, name varchar(255));
	// create table user_tags (user_id INTEGER(10) UNSIGNED, tag_id INTEGER(10) UNSIGNED, PRIMARY KEY (user_id, tag_id), FOREIGN KEY (user_id) REFERENCES user (id), FOREIGN KEY (tag_id) REFERENCES tags (id));
	// create table job_tags (job_id INTEGER(10) UNSIGNED, tag_id INTEGER(10) UNSIGNED, PRIMARY KEY (job_id, tag_id), FOREIGN KEY (job_id) REFERENCES jobs (id), FOREIGN KEY (tag_id) REFERENCES tags (id));

	public function create(){
		$name = $_GET['name'];
		$sql = "INSERT INTO tags (name) VALUES ('$name')";
		if ($this->db->query($sql) === TRUE) {
			$response = array('success'=>TRUE);
		} else {
			$response = array('success'=>FALSE);
		}
		var_dump($response);
	}

	public function get_all(){
		$sql = "SELECT * FROM tags";
		$result = mysql_query($sql);
		$response = array();
		while($row = mysql_fetch_assoc($result)) {
			array_push($response, $row);
		}
		var_dump($response);
	}

	private function get_tag_count($tag_id){
		$user_tags_count_query = mysql_query("SELECT COUNT(*) as count FROM user_tags WHERE tag_id='".$tag_id."'");
		$job_tags_count_query = mysql_query("SELECT COUNT(*) as count FROM job_tags WHERE tag_id='".$tag_id."'");
		$user_count = intval(mysql_fetch_assoc($user_tags_count_query)['count']);
		$job_count = intval(mysql_fetch_assoc($job_tags_count_query)['count']);
		return $user_count + $job_count;
	}

	public function get_for(){
		$type = $_GET['type'];
		$entity_id = $_GET['entity_id'];
		switch ($type) {
			case "user":
				$query = mysql_query("SELECT * FROM user_tags WHERE user_id='".$entity_id."'");
				break;
			case "job":
				$query = mysql_query("SELECT * FROM job_tags WHERE job_id='".$entity_id."'");
				break;
			case "company":
				$employees = mysql_query("SELECT * FROM positions WHERE company_id='".$entity_id."'");
				break;
		}
		$response = array();
		while($row = mysql_fetch_assoc($query)) {
			$tag_sql = "SELECT * FROM tags WHERE id='".$row['tag_id']."'";
			$count = $this->get_tag_count($row['tag_id']);
			$tag_result = mysql_query($tag_sql);
			$tag_row = mysql_fetch_assoc($tag_result);
			array_push($response, array('id'=>$tag_row['id'], 'name'=>$tag_row['name'], 'count'=>$count));

		}
		var_dump(json_encode($response));
	}

	public function for_company(){
		$company_id = $_GET['company_id'];
		$response = array();
		$query = mysql_query("SELECT * FROM positions WHERE company_id='".$company_id."'");
		while($row = mysql_fetch_assoc($query)) {
			$user_query = mysql_query("SELECT * FROM user_tags WHERE user_id='".$row['user_id']."'");
			while($user_row = mysql_fetch_assoc($user_query)) {
				$tag_sql = "SELECT * FROM tags WHERE id='".$user_row['tag_id']."'";
				$count = $this->get_tag_count($user_row['tag_id']);
				$tag_result = mysql_query($tag_sql);
				$tag_row = mysql_fetch_assoc($tag_result);
				array_push($response, array('id'=>$tag_row['id'], 'name'=>$tag_row['name'], 'count'=>$count));

			}
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
		var_dump($response);
	}
}
?>
