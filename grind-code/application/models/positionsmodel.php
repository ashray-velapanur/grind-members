<?
include_once APPPATH . 'libraries/utilities.php';
include_once APPPATH . 'libraries/enumerations.php';
include_once APPPATH . 'libraries/constants.php';



class PositionsModel extends CI_Model {

	function create($user_id, $company_id) {
		$sql = "INSERT INTO positions (user_id, company_id) VALUES ('$user_id', '$company_id')";
		return $this->db->query($sql);
	}
};
?>