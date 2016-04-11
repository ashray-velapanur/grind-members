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
			$sql = "UPDATE third_party_user SET user_id = '".$nonlinkedinuserid."' WHERE user_id = '".$linkedinuserid."' AND network = 'linkedin'";
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
			$sql = "DELETE FROM third_party_user WHERE user_id = '".$linkedinuserid."' AND network = 'cobot'";
	        $this->db->query($sql);
			$sql = "DELETE FROM user WHERE id = '".$linkedinuserid."'";
	        $this->db->query($sql);
		}
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
}
?>