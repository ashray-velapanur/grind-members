<?
class Cobot extends CI_Controller {
	public function test(){
		error_log('cobot testing');
	}

	public function populate_users(){
		$file = fopen('cobot.csv', 'r');
		while (!feof($file)) {
			$lines[] = fgetcsv($file, 1024);
		}
		fclose($file);
		foreach ($lines as $line) {
			$user = explode(',', $line);
			$sql = "INSERT INTO third_party_user (user_id, network, access_token) VALUES ('$user[0]', 'cobot', '$user[1]')";
			$this->db->query($sql);
		}
	}

	public function create_user(){
		$app_token = '061cb2b829ece8b489e9310a474df0848adbe47024b7749a2090bf4917fe543a';
		$url = 'https://www.cobot.me/api/users';
		$email = $_GET['email'];

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
		$id = $result['id'];

		$url = 'https://www.cobot.me/oauth/access_token?';
		$data = [
			'client_id' => '26a81206b5b2b7c9a510ca0935b0febd',
			'client_secret' => '15a365f477efa39842a493c1ac885ebea374482240c2755d882b8d41dc293532',
			'grant_type' => 'authorization_code',
			'code' => $result['grant_code']
		];

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		$result = curl_exec($curl);

		curl_close($curl);

		$result = (array)json_decode($result);
		$access_token = $result['access_token'];
		$network = 'cobot';

		$sql = "INSERT INTO third_party_user (user_id, network, access_token) VALUES ('$id', '$network', '$access_token')";
		if ($this->db->query($sql) === TRUE) {
			error_log('done!');
		} else {
			error_log('nope...');
		}
	}

	function booking_created() {
		$booking_url = $_POST['url'];
		error_log('Booking URL: '.$booking_url);
		$subdomain_start = strpos($booking_url, '://') + 3;
		$subdomain_end = strpos($booking_url, '.cobot.me/api/bookings/');
		$subdomain = substr($booking_url, $subdomain_start, $subdomain_end-$subdomain_start);
		$id_start = strpos($booking_url, '.cobot.me/api/bookings/') + 23;
		$id = substr($booking_url, $id_start);
		$sql = "INSERT INTO cobot_bookings (space_id, id, url) VALUES ('$subdomain', '$id', '$booking_url')";
		error_log($sql);
		$this->db->query($sql);
		return $booking_url;
	}

	function booking_updated() {
		$booking_url = $_POST['url'];
		error_log('Booking URL: '.$booking_url);
		return $booking_url;
	}

	function booking_deleted() {
		$booking_url = $_POST['url'];
		error_log('Booking URL: '.$booking_url);
		$subdomain_start = strpos($booking_url, '://') + 3;
		$subdomain_end = strpos($booking_url, '.cobot.me/api/bookings/');
		$subdomain = substr($booking_url, $subdomain_start, $subdomain_end-$subdomain_start);
		$id_start = strpos($booking_url, '.cobot.me/api/bookings/') + 23;
		$id = substr($booking_url, $id_start);
		$sql = "DELETE FROM cobot_bookings WHERE space_id='".$subdomain."' and id='".$id."'";
		error_log($sql);
		$this->db->query($sql);
		return $booking_url;
	}
}
?>