<?
include_once APPPATH . 'libraries/utilities.php';
include_once APPPATH . 'libraries/enumerations.php';
include_once APPPATH . 'libraries/constants.php';

require(APPPATH.'/config/linkedin.php');
require(APPPATH.'/config/cobot.php');
require(APPPATH.'/controllers/admin/spaces_dict.php');

class LoginModel extends CI_Model {

    public function linkedin_authorization_url() {
        global $linkedin_client_id, $linkedin_state, $linkedin_scope, $linkedin_authorization_url, $linkedin_callback_url, $cobot_user_default_password;
        error_log('The authorization url template: '.$linkedin_authorization_url);
        $url = sprintf($linkedin_authorization_url, $linkedin_client_id, $linkedin_callback_url, $linkedin_state, $linkedin_scope);
        error_log('The authorization url formatted: '.$url);
        return $url;
    }

    public function exchange_linkedin_code_for_access_token($code, $state) {
        global $linkedin_callback_url, $linkedin_client_id, $linkedin_client_secret, $linkedin_state, $linkedin_access_token_fetch_url;
        $access_token = NULL;
        if($state == $linkedin_state) {
            $url = $linkedin_access_token_fetch_url;
            $params = array(
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $linkedin_callback_url,
                'client_id' => $linkedin_client_id,
                'client_secret' => $linkedin_client_secret
            );
            $util = new utilities;
            $response = $util->do_get($url, $params);
            $access_token = $response['access_token'];
        }
        error_log('AT: '.$access_token);
        return $access_token;
    }

    function linkedin($access_token, $id) {
        error_log('In linkedin login');
        $response = array("success"=>False, "message"=>"");
        $userId = null;
        $url = "https://api.linkedin.com/v1/people/~:(id,email-address,picture-url,first-name,last-name,positions)?format=json&oauth2_access_token=".$access_token;
        error_log('1. Getting LinkedIn data for: '.$url);
        //TODO: Should use the same curl method. This curl method should be in a UTIL class
        $profile = json_decode(file_get_contents($url));
        error_log('2. Verifying LinkedIn data sent from iPhone');
        if ($profile == False) {
            $msg = "Invalid LinkedIn access token.";
            $response = array("success"=>False, "message"=>$msg);
            error_log($msg);
        } elseif ($id != $profile->id) {
            $msg = "Your LinkedIn ID does not match the access token. Please contact the administrator to login.";
            $response = array("success"=>False, "message"=>$msg);
            error_log($msg);
        } 
        else {
            error_log('3. LinkedIn verification passed!');
            $network_id = $profile->id;
            $pictureUrl = $profile->pictureUrl;
            $this->load->model('/members/membermodel','mm',true);
            $this->load->model('thirdpartyusermodel', 'tp', true);
            //TODO: linkedin string should come from a constants file
            error_log('4. Checking if this is a first time login or a repeat login');
            $userId = $this->mm->isNewUser($id, 'linkedin');
            if(!$userId) {
                error_log("4.a. User is logging in for the first time!");
                $userId = $this->create_user($profile);
            }
            else {
                error_log("4.b. User has logged in before");
            }
            error_log("5. Checking if we have Cobot access token for this user");
            if(!$this->tp->get($userId, 'cobot')){
                error_log("5.a. No Cobot access token found, creating Cobot user");
                $profile = (array)$profile;
                $cobotUserId = $this->create_cobot_user($userId, $profile['emailAddress']);
                error_log($cobotUserId);
                $this->create_cobot_membership($cobotUserId, $profile["firstName"].' '.$profile["lastName"].' Daily Plan');
            }
            else {
                error_log("5.b. Cobot access token found for user!");
            }
            error_log("6. Updating LinkedIn access token");
            $added_tp = $this->add_third_party_user($userId, $network_id, $pictureUrl, $access_token);
            if($added_tp) {
                $response = array("success"=>True, "user_id"=>$userId);
            } else {
                $msg = "Could not save LinkedIn Access Token";
                $response = array("success"=>False, "message"=>$msg);
            }
        }
        error_log(json_encode($response));
        return $response;
    }

    //TODO: Rename to create_user_if_necessary
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
        error_log('... creating cobot membership');
        error_log($id);
        $util = new utilities;
        $cobot_authorization_token = $util->get_current_environment_cobot_access_token();
        $query = $this->db->get("cobot_spaces");
        $spaces = $query->result();
        foreach ($spaces as $space) {
            $d = array(
                'address' => array(
                    'name' => $plan_name,
                    'full_address'=>'broadway 12345 New York',
                    'country'=>'USA'
                ),
                'plan'=>array(
                    'id'=>$space->daily_plan_id
                ),
                'phone'=>'9999999999',
                'user'=>array(
                    'id'=>$id
                )
            );
            $url = 'https://'.$space->id.'.cobot.me/api/memberships'; 
            $options = array(
                'http' => array(
                    'header'  => "Authorization: Bearer ".$cobot_authorization_token."\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($d),
                ),
            );
            error_log(json_encode($options));
            $context  = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
            error_log(json_encode($result));
        }
    }

    private function create_cobot_user($user_id, $email){
      error_log('... creating cobot user');
      global $cobot_api_key, $cobot_client_secret, $cobot_user_default_password;
      $url = 'https://www.cobot.me/api/users';
      $util = new utilities;
      $environment = $util->get_current_environment();
      $data = array(
        'access_token' => $util->get_current_environment_cobot_access_token(),
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
        $set_current_company = true;
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
            if($is_current && $set_current_company) {
                $sql = "UPDATE user SET company_id = '".$company_id."' WHERE id = '".$newUserId."'";
                $this->db->query($sql);
                $set_current_company = false;
            }
            $sql = "INSERT INTO positions (user_id, company_id, designation, start_date) VALUES ('$newUserId', '".$company_id."', '".$title."', '".$start_date."') ON DUPLICATE KEY UPDATE designation='".$title."', start_date='".$start_date."'";
            $this->db->query($sql);
        }
    }

    private function add_third_party_user($userId, $network_id, $pictureUrl, $access_token) {
        $sql = "INSERT INTO third_party_user (network_id, user_id, network, access_token, profile_picture) VALUES ('$network_id', $userId, 'linkedin', '$access_token', '$pictureUrl') ON DUPLICATE KEY UPDATE access_token='".$access_token."' , profile_picture='".$pictureUrl."'";
        error_log($sql);
        return $this->db->query($sql);
    }

};

?>