<?
class Auth extends CI_Controller {
	public function linkedin(){
		global $wpdb;
		$access_token = $_GET['access_token'];
		$id = $_GET['id'];
		$url = "https://api.linkedin.com/v1/people/~:(id,email-address)?format=json&oauth2_access_token=".$access_token;
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
			}
		}
		print(json_encode($response));
	}
}
?>