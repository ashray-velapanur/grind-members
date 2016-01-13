<?
class Cobot extends CI_Controller {
	public function test(){
		error_log('cobot testing');
	}

	public function create_user(){
		$app_token = '061cb2b829ece8b489e9310a474df0848adbe47024b7749a2090bf4917fe543a';
		$url = 'https://www.cobot.me/api/users';
		$email = $_GET['email'];
		$data = array('access_token' => $app_token, 'email' => $email);

		$data = [
			'access_token' => $app_token,
			'email' => $email
		];

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		$result = curl_exec($curl);

		curl_close($curl);
		$result = (array)json_decode($result);

		var_dump($result);
	}
}
?>