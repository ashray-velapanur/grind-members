<?
include_once APPPATH . 'libraries/utilities.php';
include_once APPPATH . 'libraries/enumerations.php';
include_once APPPATH . 'libraries/constants.php';

class JobTagsModel extends CI_Model {

	function create($job_id, $tag_id){
		$sql = "INSERT INTO job_tags (job_id, tag_id) VALUES ('$job_id', '$tag_id')";
		if ($this->db->query($sql) === TRUE) {
			$response = array('success'=>TRUE);
		} else {
			$response = array('success'=>FALSE);
		}
		return $response;
	}

	function get($job_id){
		$query = mysql_query("SELECT * FROM job_tags WHERE job_id='".$job_id."'");
		$response = array();
        while($row = mysql_fetch_assoc($query)) {
	      	array_push($response, $row);
        }
        return $response;
	}

	function count($tag_id){
		$query = mysql_query("SELECT COUNT(*) as count FROM job_tags WHERE tag_id='".$tag_id."'");
		$count = intval(mysql_fetch_assoc($query)['count']);
		return $count;
	}
};
?>