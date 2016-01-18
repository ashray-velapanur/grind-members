<?
class Jobs extends CI_Controller {
	// id (int), title (varchar), date posted (datetime), company (varchar), dev/design (varchar)
	// create table jobs (id INTEGER(10) UNSIGNED AUTO_INCREMENT primary key, title varchar(255), company varchar(255), created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, type varchar(255), url varchar(255), posted_by varchar(255));

	public function create(){
		$title = $_GET['title'];
		$company = $_GET['company'];
		$type = $_GET['type'];
		$url = $_GET['url'];
		$posted_by = $_GET['posted_by'];
		$sql = "INSERT INTO jobs (title, company, type, url, posted_by) VALUES ('$title', '$company', '$type', '$url', '$posted_by')";
		if ($this->db->query($sql) === TRUE) {
			$response = array('success'=>TRUE);
		} else {
			$response = array('success'=>FALSE);
		}
		var_dump(json_encode($response));
	}

	public function get(){
		$posted_by = $_GET['posted_by'];
		$sql = "SELECT * FROM jobs WHERE posted_by='".$posted_by."'";
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
