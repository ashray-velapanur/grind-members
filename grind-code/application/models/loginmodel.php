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
			error_log("Invalid access_token");
		} elseif ($id != $profile->id) {
			$response = array("success"=>False, "message"=>"Invalid ID.");
			error_log("Invalid linkedin ID");
		} 
		else {
			$this->load->model('/members/membermodel','mm',true);
			error_log("Loaded member model");
			$userId = $this->mm->isNewUser($id, 'linkedin');
			error_log("New User: ".$userId);
			if(!$userId) {
				error_log("Creating new user");
				$userId = $this->create_user($profile);
			}
			if($userId) {
				error_log("Updating third party");
				$this->add_third_party_user($userId, $profile, $access_token);
				$response = array("success"=>True, "user_id"=>$userId);
			}
		}
		error_log('Returning response');
		error_log(json_encode($response));
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
		$newUserId = $this->mm->doAddMember($userdata, $membershipdata, $companydata, $phonedata, $emaildata, $billingdata, $wpdata, $appuser=true);
		if($newUserId) {
			$this->add_companies($newUserId, $profile);
			//$this->create_cobot_user($newUserId, $profile['emailAddress']);
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

	private function create_cobot_user($user_id, $email){
      $app_token = '061cb2b829ece8b489e9310a474df0848adbe47024b7749a2090bf4917fe543a';
      $url = 'https://www.cobot.me/api/users';

      $data = array(
        'access_token' => $app_token,
        'email' => $email
      );

      $curl = curl_init();
      curl_setopt($curl, CURLOPT_POST, 1);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

      $result = curl_exec($curl);

      curl_close($curl);
      $result = (array)json_decode($result);
      $id = $result['id'];

      $url = 'https://www.cobot.me/oauth/access_token?';
      $data = array(
        'client_id' => '26a81206b5b2b7c9a510ca0935b0febd',
        'client_secret' => '15a365f477efa39842a493c1ac885ebea374482240c2755d882b8d41dc293532',
        'grant_type' => 'authorization_code',
        'code' => $result['grant_code']
      );

      $curl = curl_init();
      curl_setopt($curl, CURLOPT_POST, 1);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

      $result = curl_exec($curl);

      curl_close($curl);

      $result = (array)json_decode($result);
      $access_token = $result['access_token'];
      $network = 'cobot';

      $this->load->model("thirdpartyusermodel","tpum",true);
      $this->tpum->create($user_id, $id, $network, $access_token);
    }

	private function add_third_party_user($userId, $profile, $access_token) {
		$sql = "INSERT INTO third_party_user (network_id, user_id, network, access_token, profile_picture) VALUES ('$profile->id', $userId, 'linkedin', '$access_token', '$profile->pictureUrl') ON DUPLICATE KEY UPDATE access_token='".$access_token."' , profile_picture='".$profile->pictureUrl."'";
		error_log($sql);
		$this->db->query($sql);
	}

};
?>