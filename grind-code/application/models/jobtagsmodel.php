<?
include_once APPPATH . 'libraries/utilities.php';
include_once APPPATH . 'libraries/enumerations.php';
include_once APPPATH . 'libraries/constants.php';

class JobTagsModel extends CI_Model {

	function create($job_id, $tag_id){
		$sql = "INSERT INTO job_tags (job_id, tag_id) VALUES ('$job_id', '$tag_id')";
		return $this->db->query($sql);
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
		$query_results = mysql_fetch_assoc($query)['count'];
		$query_results_count = $query_results['count'];
		$count = intval($query_results_count);
		return $count;
	}
};
?>