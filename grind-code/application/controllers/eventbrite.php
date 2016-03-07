<?
include_once APPPATH . 'libraries/utilities.php';

class Eventbrite extends CI_Controller {
	public function callback(){
		error_log('... eventbrite callback');
		$api_url = $_GET['api_url'];
		error_log($api_url);
	}
}

?>