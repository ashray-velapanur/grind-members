<?
include_once APPPATH . 'libraries/utilities.php';
include_once APPPATH . 'libraries/enumerations.php';
include_once APPPATH . 'libraries/constants.php';

class JobsModel extends CI_Model {

	function create($title, $company_id, $type, $url, $posted_by){
		$sql = "INSERT INTO jobs (title, company_id, type, url, posted_by) VALUES ('$title', '$company_id', '$type', '$url', '$posted_by')";
		if ($this->db->query($sql) === TRUE) {
			$response = array('success'=>TRUE);
		} else {
			$response = array('success'=>FALSE);
		}
		return $response;
	}

	function get($type=null, $posted_by=null){
		if ($type AND $posted_by){
			$query = mysql_query("SELECT * FROM jobs WHERE posted_by='".$posted_by."' AND type='".$type."'");
		} elseif ($type) {
			$query = mysql_query("SELECT * FROM jobs WHERE type='".$type."'");
		} elseif ($posted_by) {
			$query = mysql_query("SELECT * FROM jobs WHERE posted_by='".$posted_by."'");
		}
		$response = array();
        while($row = mysql_fetch_assoc($query)) {
	      	array_push($response, $row);
        }
        return $response;
	}
};
?>