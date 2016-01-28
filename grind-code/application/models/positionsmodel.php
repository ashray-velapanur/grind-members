<?
include_once APPPATH . 'libraries/utilities.php';
include_once APPPATH . 'libraries/enumerations.php';
include_once APPPATH . 'libraries/constants.php';



class PositionsModel extends CI_Model {

	function create($user_id, $company_id) {
		$sql = "INSERT INTO positions (user_id, company_id) VALUES ('$user_id', '$company_id')";
		return $this->db->query($sql);
	}

	function get($company_id){
		$query = mysql_query("SELECT * FROM positions WHERE company_id='".$company_id."'");
		$response = array();
		while($row = mysql_fetch_assoc($query)) {
			array_push($response, $row);
		}
        return $response;
	}

};
?>