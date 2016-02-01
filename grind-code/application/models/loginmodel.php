<?
include_once APPPATH . 'libraries/utilities.php';
include_once APPPATH . 'libraries/enumerations.php';
include_once APPPATH . 'libraries/constants.php';

class LoginModel extends CI_Model {

	function linkedin($access_token, $id) {
		$response = array("success"=>False, "message"=>"");
		$userId = null;
		$url = "https://api.linkedin.com/v1/people/~:(id,email-address,picture-url,first-name,last-name,positions)?format=json&oauth2_access_token=".$access_token;
		$profile = json_decode(file_get_contents($url));
		if ($profile == False) {
			$response = array("success"=>False, "message"=>"Invalid access token.");
		} elseif ($id != $profile->id) {
			$response = array("success"=>False, "message"=>"Invalid ID.");
		} 
		else {
			$this->load->model('/members/membermodel','mm',true);
			$userId = $this->mm->isNewUser($id, 'linkedin');
			if(!$userId) {
				$userId = $this->create_user($profile);
			}
			if($userId) {
				$this->add_third_party_user($userId, $profile, $access_token);
				$response = ["success"=>True, "user_id"=>$userId];
			}
		}
		return $response;
	}

	private function create_user($profile) {
		$this->mm->load->model("billing/accountmodel","am",true);
		$this->mm->load->model("members/emailmodel","em",true);
		$profile = (array)$profile;
		$userdata = array();
		$membershipdata = array();
		$companydata = array();
		$phonedata = array();
		$emaildata = array();
		$billingdata = null;
		$wpdata = array();

		$userdata["first_name"] = $profile["firstName"];
		$userdata["last_name"] = $profile["lastName"];
		$emaildata["user_id"] = -1;
		$emaildata["address"] = $profile['emailAddress'];
		$emaildata["is_primary"] = 1;
		$wpdata["user_login"] = $profile['emailAddress'];
		$wpdata["user_email"] = $profile['emailAddress'];
		$this->mm->member->email = $profile['emailAddress'];
		$newUserId = $this->mm->doAddMember($userdata, $membershipdata, $companydata, $phonedata, $emaildata, $billingdata, $wpdata);
		if($newUserId) {
			$this->add_companies($newUserId, $profile);
		}
		return $newUserId;
	}

	private function add_companies($newUserId, $profile) {
		$positions = (array)$profile["positions"];
		$values = (array)$positions["values"];
		foreach ($values as $value) {
			$value = (array)$value;
			$company = (array)$value["company"];
			$is_current = $value["isCurrent"];
			$title = $value["title"];
			$start_date_arr = (array)$value["startDate"];
			$start_date = $start_date_arr["year"].'-'.$start_date_arr["month"].'-01';
			$company_id = $company["id"];
			if(!$company_id) {
				$company_id = 0;
			}
			$company_name = $company["name"];
			$sql = "INSERT INTO company (id, name) VALUES ('".$company_id."', '".$company_name."') ON DUPLICATE KEY UPDATE name='".$company_name."'";
			$this->db->query($sql);
			if($company_id == 0) {
				$company_id = $this->db->insert_id();
			}
			if($is_current) {
				$sql = "UPDATE user SET company_id = '".$company_id."' WHERE id = '".$newUserId."'";
				$this->db->query($sql);
			}
			$sql = "INSERT INTO positions (user_id, company_id, designation, start_date) VALUES ('$newUserId', '".$company_id."', '".$title."', '".$start_date."') ON DUPLICATE KEY UPDATE designation='".$title."', start_date='".$start_date."'";
			$this->db->query($sql);
		}
	}

	private function add_third_party_user($userId, $profile, $access_token) {
		$sql = "INSERT INTO third_party_user (network_id, user_id, network, access_token, profile_picture) VALUES ('$profile->id', $userId, 'linkedin', '$access_token', '$profile->pictureUrl') ON DUPLICATE KEY UPDATE access_token='".$access_token."' , profile_picture='".$profile->pictureUrl."'";
		$this->db->query($sql);
	}

};
?>