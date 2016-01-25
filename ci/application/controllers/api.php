<?php

/**
 * API RESTful Controller
 * 
 * Provides an extensible API for programatically
 * interacting with the GRIND subsystem
 * 
 * @author joshcampbell
 * @param 
 */


require(APPPATH.'/libraries/REST_Controller.php');

class Api extends REST_Controller
{
	
	function print_post() {
	

		$data= urldecode($_POST['jobs']);
		
		$xml = new SimpleXMLElement(stripslashes($data));
        				
		$this->load->model("/billing/printmodel","pm",true);
		
		$result = $this->pm->processCharges($xml);
		
        if($result === FALSE)
        {
        	$this->response(array('status' => 'failed','reason'=>'some reason'),400);
        }

        else
        {
        	$this->response(array('status' => 'success'),200);
        }
     	return $this->response;  
	}
	
	
	/**
	 * users_get()
	 * 
	 * retrieves a set of user data and returns
	 * 
	 * @author magicandmight
	 * @params
	 */	
	function users_get() {
		
		$this->load->model('/members/membermodel','mm',true);
				
		$users = $this->mm->export_members();

        if($users)
        {
            $this->response($users, 200);
        }

        else
        {
            $this->response(NULL, 404);
        }
	}
	
	
	 function usernametest_get() {
         
          $this->load->model('/members/membermodel','mm',true);
                   
          $uid = $this->mm->generate_username($this->get('id'));
          error_log('uid: '.$uid,0);

        if($uid)
        {
            $this->response($uid, 200);
        }

        else
        {
            $this->response(NULL, 404);
        }
     }

	// user checkin
	/*
	 function usercheckin_post() {

          $this->load->model('/members/membermodel','mm',true);
          $member = $this->mm->get_basicMemberData($this->post('id'),UserIdType::RFID);
		  $checkin = $this->mm->checkin($this->post('locationid'),$this->post('signinmethod'));

        if($checkin)
        {
            $this->response($checkin, 200);
        }

        else
        {
            $this->response(NULL, 404);
        }
     }
     */
     function testcredits_get($user_id) {
         
          $this->load->model("/billing/printmodel","pm",true);
          $this->pm->test_credits($user_id);
                   
      }

      function login_post() {
        error_log('reached login');
        $access_token = $this->post('access_token');
        $id = $this->post('id');
        error_log('access_token: '.$access_token);
        error_log('id: '.$id);
        $data = $this->linkedin($access_token, $id);
        error_log(json_encode($data));
        $this->response($data, 200);
      }

      function bubbles_post() {
        $bubbles = array();
        $query = $this->rest->db->get('bubbles');
        $results = $query->result();
        if (count($results)>0) {
          foreach ($results as $result) {
            $arr = array();
            $arr['title'] = $result->title;
            $arr['image'] = $result->image;
            $arr['rank'] = $result->rank;
            array_push($bubbles, $arr);
          }
        }
        $this->response($bubbles, 200);
      }

      private function linkedin($access_token, $id){
        $userId = null;
        error_log('In linkedin: '.$access_token.' '.$id);
        $url = "https://api.linkedin.com/v1/people/~:(id,email-address,picture-url,first-name,last-name,positions)?format=json&oauth2_access_token=".$access_token;
        $profile = json_decode(file_get_contents($url));
        error_log(json_encode($profile));
        if ($profile == False) {
          error_log('Profile is false');
          $response = array("success"=>False, "message"=>"Invalid access token.");
        } elseif ($id != $profile->id) {
          error_log('Profile id not matching');
          $response = array("success"=>False, "message"=>"Invalid ID.");
        } 
        else {
          error_log('Profile is ok');
          error_log('initialised response');
          $this->load->model('/members/membermodel','mm',true);
          error_log('Loaded member model');
          $userId = $this->mm->isNewUser($id, 'linkedin');
          if(!$userId) {
            error_log('create user');
            $userId = $this->create_user($profile);
          }
          $this->add_third_party_user($userId, $profile, $access_token);
          $response = ["success"=>True, "user_id"=>$userId];
        }
        error_log(json_encode($response));
        return $response;
      }

      private function create_user($profile){
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

        error_log('Collecting data from profile');
        error_log($profile["firstName"]);
        $userdata["first_name"] = $profile["firstName"];
        $userdata["last_name"] = $profile["lastName"];
        $emaildata["user_id"] = -1;
        $emaildata["address"] = $profile['emailAddress'];
        $emaildata["is_primary"] = 1;
        $wpdata["user_login"] = $profile['emailAddress'];
        $wpdata["user_email"] = $profile['emailAddress'];
        error_log('Trying to get membermodel');
        $this->mm->member->email = $profile['emailAddress'];
        error_log('Trying to add member');
        $newUserId = $this->mm->doAddMember($userdata, $membershipdata, $companydata, $phonedata, $emaildata, $billingdata, $wpdata);
        error_log('Done adding member: '.$newUserId);
        if($newUserId) {
          $this->add_companies($newUserId, $profile);
        }
        return $newUserId;
      }

      private function add_companies($newUserId, $profile) {
        $positions = (array)$profile["positions"];
        error_log(json_encode($positions));
        $values = (array)$positions["values"];
        error_log(json_encode($values));
        foreach ($values as $value) {
          $value = (array)$value;
          error_log(json_encode($value));
          $company = (array)$value["company"];
          error_log(json_encode($company));
          error_log($company["id"]);
          $sql = "INSERT INTO company (id, name) VALUES ('".$company["id"]."', '".$company["name"]."') ON DUPLICATE KEY UPDATE name='".$company["name"]."'";
          error_log($sql);
          error_log($this->rest->db->query($sql));
          $sql = "INSERT INTO positions (user_id, company_id) VALUES ('$newUserId', '".$company["id"]."')";
          error_log($sql);
          $this->rest->db->query($sql);
        }
      }

      private function add_third_party_user($userId, $profile, $access_token) {
        $sql = "INSERT INTO third_party_user (network_id, user_id, network, access_token, profile_picture) VALUES ('$profile->id', $userId, 'linkedin', '$access_token', '$profile->pictureUrl') ON DUPLICATE KEY UPDATE access_token='".$access_token."' , profile_picture='".$profile->pictureUrl."'";
        error_log($sql);
        error_log($this->rest->db->query($sql));
      }
}
?>