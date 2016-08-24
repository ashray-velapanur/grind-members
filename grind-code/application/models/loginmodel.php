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

    function get_and_verify_linkedin_data($access_token, $id){
        $url = "https://api.linkedin.com/v1/people/~:(id,email-address,picture-url,first-name,last-name,positions)?format=json&oauth2_access_token=".$access_token;
        error_log('1. Getting LinkedIn data for: '.$url);
        //TODO: Should use the same curl method. This curl method should be in a UTIL class
        $profile = json_decode(file_get_contents($url));
        error_log('2. Verifying LinkedIn data sent from iPhone');
        $msg = NULL;
        if ($profile == False) {
            $msg = "Invalid LinkedIn access token.";
        } elseif ($id != $profile->id) {
            $msg = "Your LinkedIn ID does not match the access token. Please contact the administrator to login.";
        } 
        if($msg) {
            $this->throw_exp($msg);
        }
        return $profile;
    }

    function handle_wordpress_login($profile){
        error_log('3. LinkedIn verification passed!');
        $this->load->model('/members/membermodel','mm',true);
        //TODO: linkedin string should come from a constants file
        error_log('4. Checking if this is a first time login or a repeat login');
        $userId = $this->mm->isNewUser($profile->id, 'linkedin');
        if(!$userId) {
            error_log("4.a. User is logging in for the first time!");
            $userId = $this->create_user($profile);
            if(!$userId) {
                $this->throw_exp("Could not create a user entry. Please contact the administrator to login.");
            }
        }
        else {
            error_log("4.b. User has logged in before");
        }
        return $userId;
    }

    function handle_cobot_access($userId, $profile) {
        error_log("5. Checking if we have Cobot access token for this user");
        $this->load->model('thirdpartyusermodel', 'tp', true);
        if(!$this->tp->get($userId, 'cobot')){
            error_log("5.a. No Cobot access token found, creating Cobot user");
            $profile = (array)$profile;
            $cobotUserId = $this->create_cobot_user($userId, $profile['emailAddress']);
            if ($cobotUserId) {
                error_log($cobotUserId);
                //TODO: This should throw an exception..
                $this->create_cobot_membership($cobotUserId, $userId, $profile["firstName"].' '.$profile["lastName"].' Virtual Plan');
            }
            else {
                $this->throw_exp("Could not create a Cobot user for you. Please contact administrator to login.");
            }
        }
        else {
            error_log("5.b. Cobot access token found for user!");
            //$this->ensure_cobot_memberships($userId);
        }
    }

    function update_linkedin_third_party($profile, $userId, $access_token) {
        error_log("6. Updating LinkedIn access token");
        $added_tp = $this->add_third_party_user($userId, $profile->id, $profile->pictureUrl, $access_token);
        if(!$added_tp) {
            $this->throw_exp("Could not save LinkedIn Access Token");
        }
    }

    function throw_exp($msg) {
        error_log($msg);
        throw new Exception($msg);
    }

    function ensure_cobot_memberships($userId) {
        $cobot_user = $this->tp->get($userId, 'cobot');
        error_log(json_encode($cobot_user));
        $query = $this->db->get("cobot_spaces");
        $spaces = $query->result();
        foreach ($spaces as $space) {
            $sql = "SELECT cm.id id FROM cobot_memberships cm WHERE cm.space_id='".$space->id."' and cm.user_id='".$userId."' and cm.cobot_user_id='".$cobot_user['network_id']."'";
            error_log($sql);
            $query = $this->db->query($sql);
            $results = $query->result();
            if(!($results && count($results) == 1)) {
                $this->throw_exp("Cobot memberships have not been setup correctly for Grind User Id: ".$userId." for space: ".$space->id);
            }
        }
    }

    function linkedin($access_token, $id) {
        error_log('In linkedin login');
        $response = array("success"=>False, "message"=>"");
        $userId = null;
        try {
            $profile = $this->get_and_verify_linkedin_data($access_token, $id);
            $userId = $this->handle_wordpress_login($profile);
            $this->handle_cobot_access($userId, $profile);
            $this->update_linkedin_third_party($profile, $userId, $access_token);
            $response["success"] = True;
            $response["user_id"] = $userId;
        } catch(Exception $e){
            error_log('Exception during login: '.$e->getMessage());
            $response["message"] = $e->getMessage();
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

    private function make_post_request($url, $data) {
        error_log(json_encode($data));
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        curl_close($curl);
        error_log(json_encode($result));
        return (array)json_decode($result);
    }

    public function create_cobot_membership($id, $user_id, $plan_name) {
        error_log('... creating cobot membership');
        error_log($id);
        $cobot_authorization_token = $this->tp->get_cobot_access_token($user_id);
        if($cobot_authorization_token) {
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
                        'id'=>$space->default_plan_id
                    ),
                    'phone'=>'9999999999'
                );
                $url = 'https://'.$space->id.'.cobot.me/api/membership'; 
                $util = new utilities;
                $response = $util->do_post($url, $params=$d, $cobot_authorization_token);
            }
        }
    }

    function check_cobot_error($cobot_response) {
        if(array_key_exists("error", $cobot_response)) {
            $msg = "Cobot Error: ".$cobot_response['error'].".";
            if(array_key_exists("error_description", $cobot_response)) {
                $msg = $msg." Error Description: ".$cobot_response['error_description'];
            }
            $this->throw_exp($msg);
        }
    }

    function create_cobot_user_for_email($email) {
        error_log("5.a.1. Trying to create Cobot user with email from LinkedIn");
        global $cobot_user_default_password;
        $url = 'https://www.cobot.me/api/users';
        $util = new utilities;
        $data = array(
            'access_token' => $util->get_current_environment_cobot_access_token(),
            'email' => $email,
            'password' => $cobot_user_default_password
        );
        $result = $this->make_post_request($url, $data);
        $this->check_cobot_error($result);
        return $result;
    }

    function fetch_access_token_for_existing_cobot_user($email) {
        error_log("5.a.2.a. Trying to get access token as Cobot user already exists");
        global $cobot_api_key, $cobot_client_secret, $cobot_user_default_password, $cobot_scope;
        $access_token = NULL;
        $url = 'https://www.cobot.me/oauth/access_token';
        $data = array(
            'scope' => $cobot_scope,
            'grant_type' => password,
            'username' => $email,
            'password' => $cobot_user_default_password,
            'client_id' => $cobot_api_key,
            'client_secret' => $cobot_client_secret
        );
        $result = $this->make_post_request($url, $data);
        $this->check_cobot_error($result);
        if(array_key_exists('access_token', $result)) {
            $access_token = $result['access_token'];
        }
        if(!$access_token) {
            $this->throw_exp("Could not get access token for existing Cobot user with email: ".$email);
        }
        return $access_token;
    }

    function fetch_access_token_for_existing_cobot_user_with_custom_password($code) {
        error_log("Trying to get access token as Cobot user already exists but with custom password");
        global $cobot_api_key, $cobot_client_secret;
        $access_token = NULL;
        $url = 'https://www.cobot.me/oauth/access_token';
        $data = array(
            'grant_type' => authorization_code,
            'code' => $code,
            'client_id' => $cobot_api_key,
            'client_secret' => $cobot_client_secret
        );
        $result = $this->make_post_request($url, $data);
        $this->check_cobot_error($result);
        if(array_key_exists('access_token', $result)) {
            $access_token = $result['access_token'];
        }
        if(!$access_token) {
            $this->throw_exp("Could not get access token for existing Cobot user with custom password");
        }
        return $access_token;
    }

    function save_cobot_user($cobot_user_id, $grind_user_id, $access_token) {
        $network = 'cobot';
        $this->load->model('thirdpartyusermodel', 'tp', true);
        $added_cobot_tp = $this->tp->create($grind_user_id, $cobot_user_id, $network, $access_token);
        if(!$added_cobot_tp) {
            $this->throw_exp("Could not save access token for Cobot user with id: ".$cobot_user_id);
        }
    }

    function save_cobot_user_for_access_token($access_token, $grind_user_id) {
        $id = $this->get_cobot_id($access_token);
        if($id) {
            error_log("5.a.2.a.2. Got ID for Cobot user. Trying to save Cobot access token.");
            $this->save_cobot_user($id, $grind_user_id, $access_token);
        } else {
            $this->throw_exp("Could not get ID for existing Cobot user with access_token: ".$access_token);
        }
        return $id;
    }

    function get_cobot_login_url($grind_user_id) {
        global $cobot_scope;
        $base_url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
        $base_url .= '://'. $_SERVER['HTTP_HOST'] .'/grind-members/';
        $login_url = "https://www.cobot.me/oauth/authorize?response_type=code&client_id=a0c2d33b04aa47b0b810e64594c11695&redirect_uri=".$base_url."grind-code/index.php/cobot/login_callback&state=".$grind_user_id."&scope=".$cobot_scope;
        return $login_url;
    }

    function handle_cobot_user_creation_error($result, $user_id, $email) {
        $id = NULL;
        error_log("5.a.2. Error creating Cobot user: ".json_encode($result['errors']));
        if(array_key_exists('email', $result['errors'])) {
            $access_token = $this->fetch_access_token_for_existing_cobot_user($email);
            error_log("5.a.2.a.1. Got access token for Cobot user. Trying to get Cobot ID using the access token.");
            $id = $this->save_cobot_user_for_access_token($access_token, $user_id);
        } else {
            $this->throw_exp("Cannot proceed with Cobot user creation due to error: ".json_encode($result['errors'])." Please contact administrator to login.");
        }
        return $id;
    }

    function handle_new_cobot_user($new_cobot_user, $user_id) {
        error_log("5.a.3. Successfully created Cobot user. Trying to get access token for the new Cobot user.");
        global $cobot_api_key, $cobot_client_secret;
        $id = $new_cobot_user['id'];
        $url = 'https://www.cobot.me/oauth/access_token?';
        $data = array(
            'client_id' => $cobot_api_key,
            'client_secret' => $cobot_client_secret,
            'grant_type' => 'authorization_code',
            'code' => $new_cobot_user['grant_code']
        );
        $result = $this->make_post_request($url, $data);
        $this->check_cobot_error($result);
        if(array_key_exists('access_token', $result)) {
            error_log("5.a.3.a. Got access token for the new Cobot user.");
            $access_token = $result['access_token'];
            $this->save_cobot_user($id, $user_id, $access_token);
        } else {
            $this->throw_exp("Could not get access token for the new Cobot user");
        }
        return $id;
    }

    private function create_cobot_user($user_id, $email) {
        error_log('... creating cobot user');

        $id = NULL;
        $result = $this->create_cobot_user_for_email($email);
        if(array_key_exists('errors', $result)) {
            $id = $this->handle_cobot_user_creation_error($result, $user_id, $email);
        } elseif (array_key_exists("grant_code", $result)) {
            $id = $this->handle_new_cobot_user($result, $user_id);
        }

        if(!$id) {
            $this->throw_exp("Could not create Cobot user with email: ".$email);
        }

        error_log("5.a.5. Successfully created Cobot user with email: ".$email);
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
            error_log($sql);
            $this->db->query($sql);
            if($company_id == 0) {
                $company_id = $this->db->insert_id();
            }
            if($is_current && $set_current_company) {
                $sql = "UPDATE user SET company_id = '".$company_id."' WHERE id = '".$newUserId."'";
                error_log($sql);
                $this->db->query($sql);
                $set_current_company = false;
            }
            $sql = "INSERT INTO positions (user_id, company_id, designation, start_date) VALUES ('$newUserId', '".$company_id."', '".$title."', '".$start_date."') ON DUPLICATE KEY UPDATE designation='".$title."', start_date='".$start_date."'";
            error_log($sql);
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