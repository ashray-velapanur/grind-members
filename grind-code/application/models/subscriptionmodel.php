<?
include_once APPPATH . 'libraries/utilities.php';
include_once APPPATH . 'libraries/enumerations.php';
include_once APPPATH . 'libraries/constants.php';
require(APPPATH.'/config/cobot.php');

class SubscriptionModel extends CI_Model {

	function create_webhook_subscription($event, $callback_url, $subdomain) {
		$util = new utilities;
		$url = 'https://'.$subdomain.'.cobot.me/api/subscriptions';
		$data = array(
		  'access_token' => $util->get_current_environment_cobot_access_token(),
		  'event' => $event,
		  'callback_url' => $callback_url
		);

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		$result = curl_exec($curl);
		print($result);

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
		$util = new utilities;
		$data = array(
	      'access_token' => $util->get_current_environment_cobot_access_token()
	    );

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
/*
$temp = new SubscriptionModel;
$temp->create_webhook_subscription('created_booking', 'http://percolate.grindspaces.com/grind-members/grind-code/index.php/cobot/booking_created', 'grind-park-avenue', '3f0064f3d5f81c5100a4c62b070053f0badf3bf1c8d3f0bea32e59152b7d56d6');
*/
?>