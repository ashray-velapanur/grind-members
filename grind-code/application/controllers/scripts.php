<?
include_once APPPATH . 'libraries/utilities.php';
include_once APPPATH . 'libraries/enumerations.php';
include_once APPPATH . 'libraries/constants.php';

require(APPPATH.'/config/linkedin.php');
require(APPPATH.'/config/cobot.php');
require(APPPATH.'/controllers/admin/spaces_dict.php');

$max_tags_per_entity = 10;

class Scripts extends CI_Controller {

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
                $this->create_cobot_membership($cobotUserId, $userId, $profile["firstName"].' '.$profile["lastName"].' Daily Plan');
            }
            else {
                $this->throw_exp("Could not create a Cobot user for you. Please contact administrator to login.");
            }
        }
        else {
            error_log("5.b. Cobot access token found for user!");
        }
    }

    function throw_exp($msg) {
        echo $msg;
        error_log($msg);
        throw new Exception($msg);
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
                        'id'=>$space->daily_plan_id
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
        error_log("5.a.1. Trying to create Cobot user with email: ".$email);
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
        global $cobot_api_key, $cobot_client_secret, $cobot_user_default_password;
        $access_token = NULL;
        $url = 'https://www.cobot.me/oauth/access_token';
        $data = array(
            'scope' => 'read_resources read_plans read_memberships write write_memberships write_user',
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

    function handle_cobot_user_creation_error($result, $email) {
        $id = NULL;
        error_log("5.a.2. Error creating Cobot user: ".json_encode($result['errors']));
        if(array_key_exists('email', $result['errors'])) {
            $access_token = $this->fetch_access_token_for_existing_cobot_user($email);
            error_log("5.a.2.a.1. Got access token for Cobot user: ".$access_token." Trying to get Cobot ID using the access token.");
            $id = $this->get_cobot_id($access_token);
            if($id) {
                error_log("5.a.2.a.2. Got ID for Cobot user: ".$id);
            } else {
                $this->throw_exp("Could not get ID for existing Cobot user with access_token: ".$access_token);
            }
        } else {
            $this->throw_exp("Cannot proceed with Cobot user creation due to error: ".json_encode($result['errors'])." Please contact administrator to login.");
        }
        return $id;
    }

    function handle_new_cobot_user($new_cobot_user) {
        global $cobot_api_key, $cobot_client_secret;
        $id = $new_cobot_user['id'];
        error_log("5.a.3. Successfully created Cobot user with id: ".$id." Trying to get access token for the new Cobot user.");
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
            $access_token = $result['access_token'];
            error_log("5.a.3.a. Got access token for the new Cobot user: ".$access_token);
        } else {
            $this->throw_exp("Could not get access token for the new Cobot user");
        }
        return $id;
    }

    public function create_cobot_user() {
        $email = $_POST['email'];
        if(!$email) {
            $this->throw_exp("Email required to create Cobot user. Email sent: ".$email);
        }
        $id = NULL;
        $result = $this->create_cobot_user_for_email($email);
        if(array_key_exists('errors', $result)) {
            $id = $this->handle_cobot_user_creation_error($result, $email);
        } elseif (array_key_exists("grant_code", $result)) {
            $id = $this->handle_new_cobot_user($result);
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

    function generateRandomString($type = 'alphanumeric', $length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $alpha_characters = 'abcdefghijklmnopqrstuvwxyz';
        $numeric_characters = '0123456789';
        if($type == 'alpha') {
            $characters = $alpha_characters;
        } elseif ($type == 'numeric') {
            $characters = $numeric_characters;
        }
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function generate_cobot($user_id, $profile) {
        error_log("Generating Cobot details for user: ".$user_id." ::::::");
        $cobot_access_token = $this->generateRandomString('alphanumeric', 64);
        $cobot_id = $this->generateRandomString('alphanumeric', 32);
        $network = 'cobot';
        $added_cobot_tp = $this->tp->create($user_id, $cobot_id, $network, $cobot_access_token);
        $this->create_cobot_memberships($cobot_id, $profile['firstName'].' '.$profile['lastName'], $user_id);
        return $cobot_id;
    }

    public function create_cobot_memberships($cobot_id, $membership_name, $user_id) {
        error_log('Creating Cobot Daily memberships for Cobot Id: '.$cobot_id." ::::::");
        $query = $this->db->get("cobot_spaces");
        $spaces = $query->result();
        foreach ($spaces as $space) {
            $membership_id = $this->generateRandomString('alphanumeric', 32);
            $plan_id = $this->generateRandomString('alphanumeric', 32);
            $sql = "INSERT INTO `cobot_memberships` (`space_id`, `id`, `user_id`, `cobot_user_id`, `name`, `plan_name`, `starts_at`, `canceled_to`, `plan_id`)
VALUES ('".$space->id."', '".$membership_id."', '".$user_id."', '".$cobot_id."', '".$membership_name."', 'Daily', NULL, NULL, '".$plan_id."')";
            error_log($sql);
            $this->db->query($sql);
        }
    }

    function generate_companies($count = 3) {
        error_log("Generating ".$count." companies ::::::");
        $companies = array();
        for ($i=0; $i < $count; $i++) {
            $company = array(
                'company' => array(
                    'id' => $this->generateRandomString('numeric',rand(6,9)),
                    'name' => ucwords($this->generateRandomString('alpha',rand(5, 10)))
                ),
                'isCurrent' => ($i === 0 ? true : false),
                'title' => ucwords($this->generateRandomString('alpha', rand(8,10))),
                'startDate' => array(
                    'year' => '20'.rand(0,1).rand(0,6),
                    'month' => rand(1,12)
                )
            );
            array_push($companies, $company);
        }
        return $companies;
    }

    function generate_linkedin_profile() {
        error_log("Generating new Linkedin Profile ::::::");
        $firstName = ucwords($this->generateRandomString('alpha',rand(5, 10)));
        $lastName = ucwords($this->generateRandomString('alpha',rand(5, 10)));
        $companies = $this->generate_companies();
        $current_company = current($companies);
        $company = $current_company['company'];
        $company_name = strtolower($company['name']);
        $emailAddress = $firstName.'.'.$lastName.'@'.$company_name.'.com';
        $profile = array(
            'id' => $this->generateRandomString(),
            'firstName' => $firstName,
            'lastName' => $lastName,
            'emailAddress' => $emailAddress,
            'current_company' => $company,
            'pictureUrl' => '',
            'positions' => array(
                'values' => $companies
            )
        );
        return $profile;
    }

    function generate_jobs($user_id, $company_id) {
        error_log("Generating jobs for user: ".$user_id." ::::::");
        $job_types = array('Development','Design','Management','Analyst');
        $job_count = rand(0,5);
        error_log('Job Count: '.$job_count);
        for ($i=0; $i < $job_count; $i++) { 
            $job_title = ucwords($this->generateRandomString('alpha',rand(5, 10)));
            $this->jm->create($job_title, $company_id, $job_types[rand(0,count($job_types)-1)], 'http://www.'.$this->generateRandomString('alpha',rand(6,10)).'.com', $user_id);
        }
    }

    function tag_jobs($jobs, $tags_array) {
        error_log('Tagging jobs ::::::');
        global $max_tags_per_entity;
        foreach ($jobs as $job) {
            $tag_count = rand(1, $max_tags_per_entity);
            for ($i=0; $i < $tag_count; $i++) { 
                $tag = $tags_array[rand(0, (count($tags_array)-1))];
                $this->jtm->create($job->id, $tag->id);
            }
        }
    }

    function generate_jobs_tags($user_id, $tags_array, $company_id) {
        $this->generate_jobs($user_id, $company_id);
        $jobs = $this->fetch_jobs($user_id);
        $this->tag_jobs($jobs, $tags_array);
    }

    function generate_user_tags($user_id, $tags_array) {
        error_log("Generating tags for user: ".$user_id." ::::::");
        global $max_tags_per_entity;
        $tag_count = rand(1, $max_tags_per_entity);
        for ($i=0; $i < $tag_count; $i++) { 
            $tag = $tags_array[rand(0, (count($tags_array)-1))];
            $this->utm->create($user_id, $tag->id);
        }
    }

    function generate_user($tags) {
        error_log("Generating user ::::::");
        $profile = $this->generate_linkedin_profile();
        $user = array();
        $user['profile'] = $profile;
        $user_id = $this->lm->handle_wordpress_login($profile);
        echo "User ID: ".$user_id;
        error_log("User ID: ".$user_id);
        if($user_id) {
            $user['grind_user_id'] = $user_id;
            $cobot_id = $this->generate_cobot($user_id, $profile);
            $user['cobot_id'] = $cobot_id;
            $linkedin_access_token = $this->generateRandomString('alphanumeric', 179);
            $profile_object = json_decode(json_encode($profile), FALSE);
            $this->lm->update_linkedin_third_party($profile_object, $user_id, $linkedin_access_token);
            $tags_array = (array)$tags;
            $total_tag_count = count($tags_array);
            error_log('Tag count: '.$total_tag_count);
            if($total_tag_count > 0) {
                $this->generate_user_tags($user_id, $tags_array);
                $user_current_company = $profile['current_company'];
                error_log(json_encode($user_current_company));
                error_log($user_current_company['id']);
                if($user_current_company) {
                    $this->generate_jobs_tags($user_id, $tags_array, $user_current_company['id']);
                }
            }
        }
        return $user;
    }

    function generate_users() {
        error_log("In Generating users ::::::");
        $this->load->model("loginmodel","lm",true);
        $this->load->model('thirdpartyusermodel', 'tp', true);
        $this->load->model('usertagsmodel', 'utm', true);
        $this->load->model('jobsmodel', 'jm', true);
        $this->load->model('jobtagsmodel', 'jtm', true);
        $count = intval($_GET['count']);
        if(!$count) {
            $count = 1;
        }
        $users = array();
        error_log($count);

        $tags = $this->fetch_tags();

        for($loop = 0; $loop < $count; $loop++) {
            error_log('Loop: '.$loop);
            $user = $this->generate_user($tags);
            array_push($users, $user);
            error_log("***************************************************************************************************************");
            //sleep(5);
        }
        echo json_encode($users);
        error_log(json_encode($users));
    }

    function fetch_tags() {
        error_log("Fetching all tags ::::::");
        $query = $this->db->get("tags");
        $tags = $query->result();
        return $tags;
    }

    function fetch_jobs($user_id) {
        error_log("Fetching jobs for user: ".$user_id." ::::::");
        if($user_id) {
            $this->db->where('posted_by',$user_id);
        }
        $query = $this->db->get("jobs");
        $jobs = $query->result();
        error_log('Jobs: '.json_encode($jobs));
        return $jobs;
    }

    function generate_tags() {
        error_log("Generating tags ::::::");
        $count = intval($_GET['count']);
        if(!$count) {
            $count = 25;
        }
        $tags = array();
        for ($i=0; $i < $count; $i++) {
            $tag = $this->generateRandomString('alpha', rand(4, 10));
            $sql = "INSERT INTO `tags` (`name`) VALUES ('".$tag."')";
            error_log($sql);
            $this->db->query($sql);
            array_push($tags, $tag);
        }
        echo json_encode($tags);
        error_log(json_encode($tags));
    }

};

?>