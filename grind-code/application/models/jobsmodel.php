<?
include_once APPPATH . 'libraries/utilities.php';
include_once APPPATH . 'libraries/enumerations.php';
include_once APPPATH . 'libraries/constants.php';

class JobsModel extends CI_Model {

	function create($title, $company_id, $type, $url, $posted_by){
		$sql = "INSERT INTO jobs (title, company_id, type, url, posted_by) VALUES ('$title', '$company_id', '$type', '$url', '$posted_by')";
		return $this->db->query($sql);
	}

	function get($type=null, $posted_by=null, $company_id=null){
		$base_query = "select jobs.title, jobs.company_id,  date_format(jobs.created_at, '%e-%M') created_at, jobs.type, jobs.url, jobs.posted_by, company.name as company_name, user.first_name, user.last_name from jobs left join company on jobs.company_id = company.id left join user on jobs.posted_by = user.id";
		if ($company_id) {
			$query = mysql_query($base_query." where company_id='".$company_id."'");
		} elseif ($type) {
			$query = mysql_query($base_query." where type='".$type."'");
		} elseif ($posted_by) {
			$query = mysql_query($base_query." where posted_by='".$posted_by."'");
		} else {
			$query = mysql_query($base_query);
		}
		error_log($query);
		$response = array();
        while($row = mysql_fetch_assoc($query)) {
	      	array_push($response, $row);
        }
        return $response;
	}
};
?>