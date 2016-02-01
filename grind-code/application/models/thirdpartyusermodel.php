<?
include_once APPPATH . 'libraries/utilities.php';
include_once APPPATH . 'libraries/enumerations.php';
include_once APPPATH . 'libraries/constants.php';



class ThirdPartyUserModel extends CI_Model {

	public function create($user_id, $network_id, $network, $access_token){
		$sql = "INSERT INTO third_party_user (user_id, network_id, network, access_token) VALUES ('$user_id', '$network_id', '$network', '$access_token')";
		return $this->db->query($sql)
	}
};
?>