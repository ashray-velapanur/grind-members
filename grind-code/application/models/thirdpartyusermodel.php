<?
include_once APPPATH . 'libraries/utilities.php';
include_once APPPATH . 'libraries/enumerations.php';
include_once APPPATH . 'libraries/constants.php';



class ThirdPartyUserModel extends CI_Model {

	public function create($user_id, $network_id, $network, $access_token){
		$sql = "INSERT INTO third_party_user (user_id, network_id, network, access_token) VALUES ('$user_id', '$network_id', '$network', '$access_token')";
		error_log($sql);
		$result = $this->db->query($sql);
		if(!$result) {
			error_log("Could not save ".$network." third party entry for Grind User ID: ".$user_id);
		}
		return $result;
	}

	public function get($user_id, $network) {
		$query = mysql_query("SELECT * FROM third_party_user WHERE user_id='".$user_id."' AND network='".$network."'");
        $response = mysql_fetch_assoc($query);
        return $response;
	}

	public function get_cobot_access_token($user_id) {
		$cobot_access_token = NULL;
	  	$tpu = $this->get($user_id, 'cobot');
	  	error_log(json_encode($tpu));
	  	if($tpu) {
	  		$cobot_access_token = $tpu['access_token'];
	  	}
	  	error_log($cobot_access_token);
	  	return $cobot_access_token;
	}
};
?>