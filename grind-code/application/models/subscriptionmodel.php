<?
include_once APPPATH . 'libraries/utilities.php';
include_once APPPATH . 'libraries/enumerations.php';
include_once APPPATH . 'libraries/constants.php';
require(APPPATH.'/config/cobot.php');

class SubscriptionModel extends CI_Model {

	function create_webhook_subscription($event, $callback_url, $subdomain) {
		global $cobot_admin_access_token;
		$url = 'https://'.$subdomain.'.cobot.me/api/subscriptions';
		$data = [
		  'access_token' => $cobot_admin_access_token,
		  'event' => $event,
		  'callback_url' => $callback_url
		];

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		$result = curl_exec($curl);

		curl_close($curl);
		$result = (array)json_decode($result);
		error_log(json_encode($result));

		$subscription_url = $result['url'];
		$subdomain_start = strpos($subscription_url, '://') + 3;
		$subdomain_end = strpos($subscription_url, '.cobot.me/api/subscriptions/');
		$subdomain = substr($subscription_url, $subdomain_start, $subdomain_end-$subdomain_start);
		$id_start = strpos($subscription_url, '.cobot.me/api/subscriptions/') + 28;
		$id = substr($subscription_url, $id_start);
		$sql = "INSERT INTO cobot_webhook_subscriptions (space_id, id, url, event) VALUES ('$subdomain', '$id', '$subscription_url', '$event')";
		error_log($sql);
		$this->db->query($sql);
		return $subscription_url;
	}

	function delete_webhook_subscription($subscription_url) {
		global $cobot_admin_access_token;
		$data = [
	      'access_token' => $cobot_admin_access_token
	    ];

	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	    curl_setopt($curl, CURLOPT_URL, $subscription_url);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

	    $result = curl_exec($curl);

	    curl_close($curl);
	    $result = (array)json_decode($result);
	    error_log(json_encode($result));

	    $subdomain_start = strpos($subscription_url, '://') + 3;
	    $subdomain_end = strpos($subscription_url, '.cobot.me/api/subscriptions/');
	    $subdomain = substr($subscription_url, $subdomain_start, $subdomain_end-$subdomain_start);
	    $id_start = strpos($subscription_url, '.cobot.me/api/subscriptions/') + 28;
	    $id = substr($subscription_url, $id_start);
	    $sql = "DELETE FROM cobot_webhook_subscriptions WHERE space_id='".$subdomain."' and id='".$id."'";
	    error_log($sql);
	    $this->db->query($sql);
	    return $subscription_url;
	}
};
?>