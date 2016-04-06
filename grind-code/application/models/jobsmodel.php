<?
include_once APPPATH . 'libraries/utilities.php';
include_once APPPATH . 'libraries/enumerations.php';
include_once APPPATH . 'libraries/constants.php';

class JobsModel extends CI_Model {

	function create($title, $company_id, $type, $url, $posted_by){
		$sql = "INSERT INTO jobs (title, company_id, type, url, posted_by) VALUES ('$title', '$company_id', '$type', '$url', '$posted_by')";
		return $this->db->query($sql);
	}

	function get($type=null, $posted_by=null, $company_id=null, $id=null){
		$base_query = "select jobs.title, jobs.company_id,  date_format(jobs.created_at, '%e-%M') created_at, jobs.type, jobs.url, jobs.posted_by, company.name as company_name, user.first_name, user.last_name from jobs left join company on jobs.company_id = company.id left join user on jobs.posted_by = user.id";
		if ($company_id) {
			$query = $base_query." where jobs.company_id='".$company_id."'";
		} elseif ($type) {
			$query = $base_query." where jobs.type='".$type."'";
		} elseif ($posted_by) {
			$query = $base_query." where jobs.posted_by='".$posted_by."'";
		} elseif ($id) {
			$query = $base_query." where jobs.id='".$id."'";
		} else {
			$query = $base_query;
		}
		$query = $query." order by created_at desc";
		error_log(json_encode($query));
		$response = array();
		$mysql_query = mysql_query($query);
        while($row = mysql_fetch_assoc($mysql_query)) {
	      	array_push($response, $row);
        }
        return $response;
	}
};
?>