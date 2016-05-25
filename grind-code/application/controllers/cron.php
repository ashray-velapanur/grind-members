<?php

include_once APPPATH . 'libraries/utilities.php';

class Cron extends CI_Controller {

    public function message($to = 'World')
    {
        echo "Hello {$to}!".PHP_EOL;
    }

    public function reset_checkins() {
        error_log('cron job: resetting checkin count');
        $sql = "update cobot_spaces set checkins=0";
        $this->db->query($sql);
    }

    public function invoice_cobot_bookings() {
    	global $environmentsToAccessToken;
    	$util = new utilities;
    	$access_token = $environmentsToAccessToken[$util->get_current_environment()];
    	if(!$from){
			$to = date_create();
			$from = date_add(date_create(), date_interval_create_from_date_string("-24 hours"));
		}
		if(!$to){
			$to = date_create();
		}
		$from = date_format($from, 'Y-m-d H:i O');
		$to = date_format($to, 'Y-m-d H:i O');

		$query = $this->db->get("cobot_spaces");
	    $spaces = $query->result();
	    foreach ($spaces as $space) {
	    	$bookings_url = 'https://'.$space->id.'.cobot.me/api/bookings';
			$bookings = $util->do_get($bookings_url, $params=array(
				'access_token' => $access_token,
				'from' => rawurlencode($from),
				'to' => rawurlencode($to)
			));
			if(count($bookings) < 1) {
				echo " *** No bookings in the last 24 hours for space: ".$space->id;
			}
			foreach ($bookings as $booking) {
				$booking = (array)$booking;
				$booking_id = $booking['id'];
				echo " *** Trying to generate invoice for booking id: ".$booking_id;
				$membership = $booking['membership'];
				$resource = $booking['resource'];
				$resource_name = '';
				if($resource) {
					$resource_name = $resource->name;
				}
				$title = $booking['title'];
				$price = $booking['price'];
				if($membership) {
					$membership_id = $membership->id;
					$invoice_url = 'https://'.$space->id.'.cobot.me/api/memberships/'.$membership_id.'/invoices';
					$params = array("access_token" => "$access_token","items" => array(array("amount" => "$price","description" => "$title".' - '."$resource_name","quantity" => "1")));
					$result = $util->do_post($invoice_url, $params);
					if($result && count($result) > 0) {
						error_log('Invoice created with id: '.$result['id'].' and url: '.$result['url'].' for booking id: '.$booking_id);
						echo ' *** Invoice created with id: '.$result['id'].' and url: '.$result['url'].' for booking id: '.$booking_id;
					}
				}
			}
	    }
    }
}
?>