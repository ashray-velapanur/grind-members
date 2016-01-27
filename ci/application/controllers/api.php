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

      function spaces_post() {
        $space_data = [];
        $query = $this->rest->db->get("cobot_spaces");
        $spaces = $query->result();
        foreach ($spaces as $space_arr) {
          $space = (array)$space_arr;
          $space_id = $space['id'];
          $space_img_src = 'data:image/jpeg;base64,'.base64_encode( $space['image'] );
          $capacity = $space['capacity'];

          $curl = curl_init();
          $url = 'https://www.cobot.me/api/spaces/'.$space_id;
          $data = [];
          if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
          curl_setopt($curl, CURLOPT_URL, $url);
          curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
          $result = (array)json_decode(curl_exec($curl));
          curl_close($curl);
          $description = $result['description'];
          $name = $result['name'];
          $resources = $this->resources($space_id);
          $spacedata = array(
            'id' => $space_id,
            'img_src' => 'data:image/jpeg;base64,'.base64_encode( $space['image'] ),
            'description' => $description,
            'name' => $name,
            'capacity' => $capacity,
            'resources' => $resources
          );
          array_push($space_data, $spacedata);
        }
        $data = array('space_data' => $space_data);
        $this->response($data, 200);
      }

      function members_post() {
        $this->load->library('pagination');
        $query = $this->load->model("members/membermodel", "", true);
        $config['base_url'] = site_url('/ci/api/members/');
        $config['total_rows'] = $this->membermodel->count_members();
        $config['per_page'] = 200;
        $config['full_tag_open'] = '<div style="display:inline-block" class="navigation">';
        $config['full_tag_close'] = '</div>';
        $config['uri_segment'] = 4;
        $data["users"] = $this->membermodel->new_listing($config['per_page'],$row);
        $this->pagination->initialize($config);
        $data["pagination"] = $this->pagination->create_links();
        $this->response($data, 200);
      }

      function companies_post() {
        $this->load->model('members/companymodel','',true);
        $companies = $this->companymodel->get_all();
        $companies_data = [];
        foreach ($companies as $company) {
          $company = (array)$company;
          $company_data = [
            'name' => $company['name'],
            'logo' => 'data:image/jpeg;base64,'.base64_encode( $company['logo'] ),
            'description' => $company['description'],
            'id' => $company['id']
          ];
          array_push($companies_data, $company_data);
        }
        $data = [
          "companies" => $companies_data
        ];
        $this->response($data, 200);
      }

      function company_jobs_post() {
        $id = $this->post('id');
        $this->load->model('members/companymodel','',true);
        $data =  $this->companymodel->get_jobs($id);
        $this->response($data, 200);
      }

      private function resources($space_id) {
        $resource_data = [];
        $curl = curl_init();
        $url = 'https://'.$space_id.'.cobot.me/api/resources';
        $rdata = [
          'access_token' => '79af7d71ab964cf5e34f8eec64d175533bf5c924bf4d1133ff01aed76c6017d8'
        ];
        if ($rdata)
              $url = sprintf("%s?%s", $url, http_build_query($rdata));
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        $result_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $cobot_resources = [];
        if($result_code == 200) {
          $result = (array)json_decode($result);
          foreach ($result as $resource) {
            $resource = (array)$resource;
            $cobot_resources[$resource['id']] = $resource;
          }
        }

        $this->rest->db->where("space_id", $space_id);
        $query = $this->rest->db->get('cobot_resources');
        $resources = $query->result();
        foreach ($resources as $resource_arr) {
          $resource = (array)$resource_arr;
          $resource_id = $resource['id'];
          $resource_img_src = 'data:image/jpeg;base64,'.base64_encode( $resource['image'] );
          $cobot_resource = $cobot_resources[$resource_id];
          $description = $cobot_resource['description'];
          $capacity = $cobot_resource['capacity'];
          $rate = $cobot_resource['price_per_hour'];
          $resourcedata = array(
            'id' => $resource_id,
                'img_src' => $resource_img_src,
                'description' => $description,
                'capacity' => $capacity,
                'rate' => $rate
              );
              array_push($resource_data, $resourcedata);
        }
        return $resource_data;
      }

      private function linkedin($access_token, $id){
        $response = array("success"=>False, "message"=>"");
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
          if($userId) {
            $this->add_third_party_user($userId, $profile, $access_token);
            $response = ["success"=>True, "user_id"=>$userId];
          }
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
          $is_current = $value["isCurrent"];
          error_log(json_encode($company));
          error_log($company["id"]);
          $sql = "INSERT INTO company (id, name) VALUES ('".$company["id"]."', '".$company["name"]."') ON DUPLICATE KEY UPDATE name='".$company["name"]."'";
          error_log($sql);
          $this->rest->db->query($sql);
          if($is_current) {
            $sql = "UPDATE user SET company_id = '".$company["id"]."' WHERE id = '".$newUserId."'";
            error_log($sql);
            $this->rest->db->query($sql);
          }
          $sql = "INSERT INTO positions (user_id, company_id) VALUES ('$newUserId', '".$company["id"]."')";
          error_log($sql);
          $this->rest->db->query($sql);
        }
      }

      private function add_third_party_user($userId, $profile, $access_token) {
        $sql = "INSERT INTO third_party_user (network_id, user_id, network, access_token, profile_picture) VALUES ('$profile->id', $userId, 'linkedin', '$access_token', '$profile->pictureUrl') ON DUPLICATE KEY UPDATE access_token='".$access_token."' , profile_picture='".$profile->pictureUrl."'";
        error_log($sql);
        $this->rest->db->query($sql);
      }
}
?>