<?
class Jobs extends CI_Controller {
	// id (int), title (varchar), date posted (datetime), company (varchar), dev/design (varchar)
	// ALTER TABLE company ENGINE = InnoDB;
	// ALTER TABLE user ENGINE = InnoDB;
	// SHOW ENGINE INNODB STATUS\G 
	// create table jobs (id INTEGER(10) UNSIGNED AUTO_INCREMENT primary key, title varchar(255), company_id INTEGER(10) UNSIGNED, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, type varchar(255), url varchar(255), posted_by INTEGER(10) UNSIGNED, FOREIGN KEY (company_id) REFERENCES company (id), FOREIGN KEY (posted_by) REFERENCES user (id));

	public function create(){
		$title = $_GET['title'];
		$company_id = $_GET['company_id'];
		$type = $_GET['type'];
		$url = $_GET['url'];
		$posted_by = $_GET['posted_by'];
		$sql = "INSERT INTO jobs (title, company_id, type, url, posted_by) VALUES ('$title', '$company_id', '$type', '$url', '$posted_by')";
		if ($this->db->query($sql) === TRUE) {
			$response = array('success'=>TRUE);
		} else {
			$response = array('success'=>FALSE);
		}
		var_dump(json_encode($response));
	}

	public function get(){
		$posted_by = $_GET['posted_by'];
		$type = $_GET['type'];
		$sql = "SELECT * FROM jobs WHERE posted_by='".$posted_by."' AND type='".$type."'";
		$result = mysql_query($sql);
		$response = array();
		while($row = mysql_fetch_assoc($result))
			{
				array_push($response, $row);
		}
		var_dump(json_encode($response));
	}
}
?>
