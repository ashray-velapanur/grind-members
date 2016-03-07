<?
include_once APPPATH . 'libraries/utilities.php';

class Eventbrite extends CI_Controller {
	public function callback(){
		error_log('... eventbrite callback');
		$request_body = file_get_contents('php://input');
		$data = json_decode($request_body);
		error_log($data);		
	}
}

?>