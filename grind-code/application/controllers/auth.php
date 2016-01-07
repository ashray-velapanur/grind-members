<?
class Auth extends CI_Controller {
	public function linkedin(){
		global $wpdb;
		$access_token = $_GET['access_token'];
		$id = $_GET['id'];
		$url = "https://api.linkedin.com/v1/people/~:(id,email-address,picture-url)?format=json&oauth2_access_token=".$access_token;
		$profile = json_decode(file_get_contents($url));
		if ($profile == False) {
			$response = array("success"=>False, "message"=>"Invalid access token.");
		} elseif ($id != $profile->id) {
			$response = array("success"=>False, "message"=>"Invalid ID.");
		} else {
			$response = array("success"=>True);
			// start session here
			if (!isset($_COOKIE['grindauth'])) {
				setcookie("grindauth", $id, time()+60*60*24*14, "", substr($_SERVER['SERVER_NAME'], strpos($_SERVER['SERVER_NAME'], '.')));
			}
			if(!isset($_SESSION['grinduser'])) {
				session_start();
				$role = "subscriber";
				$user = '';
				$this->db->where('user_login', $profile->emailAddress);
				$query = $this->db->get('wpmember_users');
				$results = $query->result();
				if (count($results)>0) {
					$user = $results[0];
				}

				$result = $wpdb->get_row("SELECT id, first_name, last_name, rfid FROM user where wp_users_id = " . $user->ID );

				$_SESSION["wpuser"] = array(
							"id"=>$result->id,
							"wp_users_id"=>$user->ID,
							"user_login"=>$user->user_login,
							"wp_role"=>$role,
							"first_name"=>$result->first_name,
							"last_name"=>$result->last_name,
							"rfid"=>$result->rfid
				);
				$cookiedata = array(
							"id"=>$result->id,
							"wp_users_id"=>$user->ID,
							"user_login"=>$user->user_login,
							"wp_role"=>$role,
							"first_name"=>$result->first_name,
							"last_name"=>$result->last_name,
							"rfid"=>$result->rfid
				);
				setGalleryCookie($cookiedata);

				$sql = "INSERT INTO third_party_user (network_id, user_id, network, access_token, profile_picture) VALUES ('$profile->id', $result->id, 'linkedin', '$access_token', '$profile->pictureUrl') ON DUPLICATE KEY UPDATE access_token='".$access_token."' , profile_picture='".$profile->pictureUrl."'";
				try {
					if ($this->db->query($sql) === TRUE) {
						echo "Record created/updated successfully";
					} else {
						echo "Error: " . $sql . "<br>" . $this->db->error;
					}
				} catch (Exception $e) {
				    error_log('Caught exception: ',  $e->getMessage(), "\n");
				}
			}
		}
		print(json_encode($response));
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
		error_log('In bubbles_get');
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
		error_log('Printing Bubbles');
		foreach ($bubbles as $bubble) {
			foreach ($bubble as $key => $value) {
				error_log($key.' '.$value);
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
		$users_array = [];
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