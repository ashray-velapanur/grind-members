<?

class Auth extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->output->enable_profiler(TRUE);
	}

	public function harmonize_users() {
		$linkedinusers = array();
		$nonlinkedinusers = array();

		$results = null;
        $sql = "
            select
                user.id id, user.last_name lname, user.first_name fname, wp_user.user_email email
            from
                third_party_user
                left outer join user on third_party_user.user_id = user.id
                left outer join wpmember_users wp_user on wp_user.id = user.wp_users_id
            where
                third_party_user.network = 'linkedin'
                and third_party_user.user_id not in (select user_id from third_party_user where network = 'cobot')
            order by
                fname asc
        ";
        
        $query = $this->db->query($sql);
        $results = $query->result();
        $query->free_result();

		foreach ($results as $user) {
			$linkedinuser = array(
				'id' => $user->id,
				'value' => $user->fname.' '.$user->lname.' : '.$user->email
			);
			array_push($linkedinusers, $linkedinuser);
		}

		$results = null;
        $sql = "
            select
                user.id id, user.last_name lname, user.first_name fname, wp_user.user_email email
            from
            	wpmember_users wp_user
            	inner join user on wp_user.id = user.wp_users_id and user.id not in (select user_id from third_party_user where network = 'linkedin')
            order by
                fname asc
        ";
        
        $query = $this->db->query($sql);
        $results = $query->result();
        $query->free_result();

		foreach ($results as $user) {
			$nonlinkedinuser = array(
				'id' => $user->id,
				'value' => $user->fname.' '.$user->lname.' : '.$user->email
			);
			array_push($nonlinkedinusers, $nonlinkedinuser);
		}

		$data = array('linkedinusers'=>$linkedinusers, 'nonlinkedinusers'=>$nonlinkedinusers);
		$this->load->view('/admin/harmonize_users.php', $data);
	}

	public function do_harmonize_users() {
		$submit = $_POST['submit'];
		$linkedinuserid = $_POST['linkedinuser'];
		$nonlinkedinuserid = $_POST['nonlinkedinuser'];
		if ($submit == "Dont Harmonize") {
			$this->load->model("members/membermodel","",true);
			$member = $this->membermodel->get_basicMemberData($linkedinuserid);
			// $this->create_cobot_user($linkedinuserid, $member->user_login);
		} elseif ($submit == "Harmonize") {
			//Update third party table with nonlinkedinuserid instead of linkedinuserid
			$sql = "UPDATE third_party_user SET user_id = '".$nonlinkedinuserid."' WHERE user_id = '".$linkedinuserid."'";
	        $this->db->query($sql);
			//Update positions with nonlinkedinuserid instead of linkedinuserid
			$sql = "UPDATE positions SET user_id = '".$nonlinkedinuserid."' WHERE user_id = '".$linkedinuserid."'";
	        $this->db->query($sql);
	        //Update jobs with nonlinkedinuserid instead of linkedinuserid
	        $sql = "UPDATE jobs SET posted_by = '".$nonlinkedinuserid."' WHERE posted_by = '".$linkedinuserid."'";
	        $this->db->query($sql);
	        //Update user tags with nonlinkedinuserid instead of linkedinuserid
	        $sql = "UPDATE user_tags SET user_id = '".$nonlinkedinuserid."' WHERE user_id = '".$linkedinuserid."'";
	        $this->db->query($sql);
			//Update nonlinkedinuser with current company of linkedinuser(optional)
			$sql = "UPDATE user SET company_id = (SELECT company_id FROM user where id='".$linkedinuserid."') WHERE id = '".$nonlinkedinuserid."'";
	        $this->db->query($sql);
			//Delete linkedinuser's wp_user_meta and wp_user
			$sql = "DELETE FROM WPMEMBER_USERMETA WHERE user_id = (SELECT WP_USERS_ID FROM USER WHERE ID='".$linkedinuserid."')";
	        $this->db->query($sql);
			//Delete linkedinuser
			$sql = "DELETE FROM wpmember_users WHERE id = (SELECT WP_USERS_ID FROM USER WHERE ID='".$linkedinuserid."')";
	        $this->db->query($sql);
	        //Delete linkedinuser
			$sql = "DELETE FROM USER WHERE id = '".$linkedinuserid."'";
	        $this->db->query($sql);
		}
	}

	public function cobot() {
		$client_id = 'f07c161f33dc2d90450d931176a6aea1';
		$client_secret = 'c692bd368b45e18ba93d2d051ca5deaae480a73cd6f4612568290d172ada2a11';
		$scope = 'read read_memberships write write_memberships read_check_ins';
		$redirect_uri = 'http://oscarosl-test.appspot.com/~aiyappaganesh/grind-members/grind-code/index.php/auth/cobot';
		$access_token = '79af7d71ab964cf5e34f8eec64d175533bf5c924bf4d1133ff01aed76c6017d8';

		$code = $_GET['code'];

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_POST, 1);

		$data = array(
			'client_id' => $client_id,
			'client_secret' => $client_secret,
			'grant_type' => 'authorization_code',
			'code' => $code
		);

		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

		$url = "https://www.cobot.me/oauth/access_token?";

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		$result = curl_exec($curl);

		curl_close($curl);

		$result = (array)json_decode($result);

		foreach ($result as $key => $value) {
			error_log($key.' '.$value);
		}

		return $result['access_token'];
	}

	function populate_bubbles() {
		$file = fopen(__DIR__."/../../../bubbles.csv","r");
		if ($file) {
			$sql = "TRUNCATE TABLE bubbles";
			if ($this->db->query($sql) === TRUE) {
				echo "Bubbles table cleared";
			} else {
				echo "Error: " . $sql . "<br>" . $this->db->error;
			}
			while(! feof($file))
			{
				$arr = fgetcsv($file);
				$title = mysql_real_escape_string($arr[0]);
				$image = mysql_real_escape_string($arr[1]);
				$sql = "INSERT INTO bubbles (title, image, rank) VALUES ('$title', '$image', $arr[2])";
				if ($this->db->query($sql) === TRUE) {
					echo "New record created successfully";
				} else {
					echo "Error: " . $sql . "<br>" . $this->db->error;
				}
			}
			fclose($file);
		}
	}

	function bubbles_get() {
		$bubbles = array();
		$query = $this->db->get('bubbles');
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
		return $bubbles;
	}

	public function admin_get() {
		$query = $this->db->query("select id, wp_users_id from user left join wpmember_usermeta on user.wp_users_id = wpmember_usermeta.user_id where wpmember_usermeta.meta_key = 'wpmember_user_level' and wpmember_usermeta.meta_value = '10'");
		if ($query->num_rows() > 0){
			$user = $query->row();
			$user_id = $user->id;
			$wp_user_id = $user->wp_users_id;
			$query->free_result();
			if($user_id) {
				$query = $this->db->query("select network_id from third_party_user where user_id = ".$user_id." and network='linkedin'");
				if ($query->num_rows() > 0) {
					$tpu = $query->row();
					$id = $tpu->network_id;
				}
				$query->free_result();
				$query = $this->db->query("select meta_value from wpmember_usermeta where user_id=".$wp_user_id." and meta_key='first_name'");
				if ($query->num_rows() > 0) {
					$um = $query->row();
					$first_name = $um->meta_value;
				}
				$query->free_result();
				$query = $this->db->query("select meta_value from wpmember_usermeta where user_id=".$wp_user_id." and meta_key = 'last_name'");
				if ($query->num_rows() > 0) {
					$um = $query->row();
					$last_name = $um->meta_value;
				}
				$query->free_result();
				$query = $this->db->query("select meta_value from wpmember_usermeta where user_id=".$wp_user_id." and meta_key = 'email'");
				if ($query->num_rows() > 0) {
					$um = $query->row();
					$email = $um->meta_value;
				}
			}
			error_log('PRINT: returning user: '.json_encode(compact('id','email','first_name','last_name')));
			return json_encode(compact('id','email','first_name','last_name'));
		} else {
			error_log('PRINT: could not find admin user');
			return false;
		}
	}

	public function users_get() {
		$users_array = array();
		$query = $this->db->query("select id, wp_users_id from user");
		if ($query->num_rows() > 0){
			foreach ($query->result() as $user) {
				$id = '';
				$first_name = '';
				$last_name = '';
				$email = '';
				$user_id = $user->id;
				$wp_user_id = $user->wp_users_id;
				$query->free_result();
				if($user_id) {
					$query = $this->db->query("select network_id from third_party_user where user_id = ".$user_id." and network='linkedin'");
					if ($query->num_rows() > 0) {
						$tpu = $query->row();
						$id = $tpu->network_id;
					}
					$query->free_result();
					$query = $this->db->query("select meta_value from wpmember_usermeta where user_id=".$wp_user_id." and meta_key='first_name'");
					if ($query->num_rows() > 0) {
						$um = $query->row();
						$first_name = $um->meta_value;
					}
					$query->free_result();
					$query = $this->db->query("select meta_value from wpmember_usermeta where user_id=".$wp_user_id." and meta_key = 'last_name'");
					if ($query->num_rows() > 0) {
						$um = $query->row();
						$last_name = $um->meta_value;
					}
					$query->free_result();
					$query = $this->db->query("select meta_value from wpmember_usermeta where user_id=".$wp_user_id." and meta_key = 'email'");
					if ($query->num_rows() > 0) {
						$um = $query->row();
						$email = $um->meta_value;
					}
				}
				array_push($users_array, compact('id','email','first_name','last_name'));
			}
			error_log('PRINT: returning users: '.json_encode($users_array));
			return json_encode($users_array);
		} else {
			error_log('PRINT: could not find any users');
			return false;
		}
	}
}
?>