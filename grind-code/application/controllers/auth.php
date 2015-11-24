<?
class Auth extends CI_Controller {
	public function linkedin(){
		$access_token = $_GET['access_token'];
		$id = $_GET['id'];
        $url = "https://api.linkedin.com/v1/people/~?format=json&oauth2_access_token=".$access_token;
		$profile = json_decode(file_get_contents($url));
		if ($profile == False) {
			$response = array("success"=>False, "message"=>"Invalid access token.");
		} elseif ($id != $profile->id) {
			$response = array("success"=>False, "message"=>"Invalid ID.");
		} else {
			$response = array("success"=>True);
			// start session here
		}
		print(json_encode($response));
	}
}
?>