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
    	$util = new utilities;
    	$access_token = $util->get_current_environment_cobot_access_token();
    	$to = date_create();
		$from = date_add(date_create(), date_interval_create_from_date_string("-24 hours"));
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
				echo " *** No bookings in the last 24 hours for space: ".$space->id."\r\n";
			}
			$memberships = array();
			foreach ($bookings as $booking) {
				$booking = (array)$booking;
				$membership = $booking['membership'];
				if($membership) {
					if(!isset($memberships[$membership->id])) {
						$memberships[$membership->id] = 1;
					}
				}
			}

			$membership_ids = array_keys($memberships);
			error_log(json_encode($membership_ids));

			foreach ($membership_ids as $membership_id) {
				echo " *** Trying to generate invoice for membership id: ".$membership_id." for space: ".$space->id."\r\n";
				$invoice_url = 'https://'.$space->id.'.cobot.me/api/memberships/'.$membership_id.'/charges_based_invoices';
				$params = array();
				$result = $util->do_post($invoice_url, $params, $access_token);
				if($result && count($result) > 0 && !array_key_exists('error', $result)) {
					error_log('Invoice created with id: '.$result['id'].' and number: '.$result['invoice_number'].' and url: '.$result['url'].' for membership id: '.$membership_id);
					echo ' *** Invoice created with id: '.$result['id'].' and number: '.$result['invoice_number'].' and url: '.$result['url'].' for membership id: '.$membership_id."\r\n";
					$charge_url = 'https://'.$space->id.'.cobot.me/api/invoices/'.$result['invoice_number'].'/charges';
					$charge_result = $util->do_post($charge_url, array(), $access_token);
					echo " *** Charge made for invoice number: ".$result['invoice_number']."\r\n";
				}
			}
	    }
    }
}
?>