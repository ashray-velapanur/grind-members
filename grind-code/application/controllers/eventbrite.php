<?
include_once APPPATH . 'libraries/utilities.php';

class Eventbrite extends CI_Controller {
	public function callback(){
		error_log('... eventbrite callback');
		$request_body = file_get_contents('php://input');
		$data = json_decode($request_body);
		$url = $data->api_url;
		error_log($url);
	  	$data = array(
			"token"=>"EYFPEMS6IJLSNOXNVH56"
	  		);
	    $options = array(
	        'http' => array(
	            'method'  => 'GET',
	            'content' => http_build_query($data),
	        ),
	    );
	    $context  = stream_context_create($options);
	    $result = file_get_contents($url, false, $context);
	    error_log($result);
	    return json_decode($result);
	}
}

?>