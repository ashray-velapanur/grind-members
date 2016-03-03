<?
/*
include_once APPPATH . 'libraries/utilities.php';
include_once APPPATH . 'libraries/enumerations.php';
include_once APPPATH . 'libraries/constants.php';

require(APPPATH.'/config/cobot.php');
require(APPPATH.'/controllers/admin/spaces_dict.php');
*/
require('../controllers/admin/spaces_dict.php');

class LoginModel /*extends CI_Model*/ {

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
            $id = $this->create_cobot_user($newUserId, $profile['emailAddress']);
            $this->create_cobot_membership($id, $profile["firstName"].' '.$profile["lastName"].' Daily Plan');
        }
        return $newUserId;
    }

    private function make_request($url, $data) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);
        curl_close($curl);
        return (array)json_decode($result);
    }

    public function create_cobot_membership($id, $plan_name) {
        global $environmentsToSpaces, $environmentsToAccessToken, $spacePlansMap;
        $environment = 'dev';
        $spaces = $environmentsToSpaces[$environment];
        foreach ($spaces as $space) {
            $plan_id = $spacePlansMap[$space];
            $d = array(
                'address' => array(
                    'name' => $plan_name,
                    'full_address'=>'broadway 12345 New York',
                    'country'=>'USA'
                ),
                'plan'=>array(
                    'id'=>$plan_id
                ),
                'phone'=>'9999999999',
                'user'=>array(
                    'id'=>$id
                )
            );
            $url = 'https://'.$space.'.cobot.me/api/memberships'; 
            $options = array(
                'http' => array(
                    'header'  => "Authorization: Bearer ".$environmentsToAccessToken[$environment]."\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($d),
                ),
            );
            $context  = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
        }
    }

    private function create_cobot_user($user_id, $email){
      global $cobot_admin_access_token, $cobot_api_key, $cobot_client_secret, $cobot_user_default_password;
      $url = 'https://www.cobot.me/api/users';

      $data = array(
        'access_token' => $cobot_admin_access_token,
        'email' => $email,
        'password' => $cobot_user_default_password
      );
      error_log(json_encode($data));
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_POST, 1);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

      $result = curl_exec($curl);
      error_log(json_encode($result));
      curl_close($curl);
      $result = (array)json_decode($result);

      if($result['errors']->email) {
        $url = 'https://www.cobot.me/oauth/access_token';
        $data = array(
            'scope' => 'read_resources read_plans read_memberships write write_memberships write_user',
            'grant_type' => password,
            'username' => $email,
            'password' => $cobot_user_default_password,
            'client_id' => $cobot_api_key,
            'client_secret' => $cobot_client_secret
        );
        error_log(json_encode($data));
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);
        $result = (array)json_decode($result);
        error_log(json_encode($result));
        curl_close($curl);
        $access_token = $result['access_token'];

        $id = $this->get_cobot_id($access_token);
        $network = 'cobot';

        $this->load->model("thirdpartyusermodel","tpum",true);
        $this->tpum->create($user_id, $id, $network, $access_token);
      }
      else if ($result['grant_code']) {
          $id = $result['id'];
          $url = 'https://www.cobot.me/oauth/access_token?';
          $data = array(
            'client_id' => $cobot_api_key,
            'client_secret' => $cobot_client_secret,
            'grant_type' => 'authorization_code',
            'code' => $result['grant_code']
          );
          error_log(json_encode($data));
          $curl = curl_init();
          curl_setopt($curl, CURLOPT_POST, 1);
          curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
          curl_setopt($curl, CURLOPT_URL, $url);
          curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

          $result = curl_exec($curl);
          error_log(json_encode($result));
          curl_close($curl);

          $result = (array)json_decode($result);

        $access_token = $result['access_token'];
        $network = 'cobot';

        $this->load->model("thirdpartyusermodel","tpum",true);
        $this->tpum->create($user_id, $id, $network, $access_token);
      }
      return $id;
    }

    function get_cobot_id($access_token) {
        $id = NULL;
        $url = 'https://www.cobot.me/api/user';
        $curl = curl_init();
        $url = $url.'?access_token='.$access_token;
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        $result_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if($result_code == 200) {
            $user = (array)json_decode($result);
            $id = $user['id'];
        }
        return $id;
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
        error_log($sql);
        $this->db->query($sql);
    }

};

$temp = new LoginModel;
$temp->create_cobot_membership();
?>