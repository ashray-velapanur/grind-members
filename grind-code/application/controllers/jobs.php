<?
class Jobs extends CI_Controller {
	// id (int), title (varchar), date posted (datetime), company (varchar), dev/design (varchar)
	// create table jobs (id INTEGER(10) UNSIGNED AUTO_INCREMENT primary key, title varchar(255), company varchar(255), created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, type varchar(255));

	public function create(){
		$title = $_GET['title'];
		$company = $_GET['company'];
		$type = $_GET['type'];
		$url = $_GET['url'];
		$sql = "INSERT INTO jobs (title, company, type, url) VALUES ('$title', '$company', '$type', '$url')";
		if ($this->db->query($sql) === TRUE) {
			$response = array('success'=>TRUE);
		} else {
			$response = array('success'=>FALSE);
		}
		var_dump(json_encode($response));
	}
}
?>
