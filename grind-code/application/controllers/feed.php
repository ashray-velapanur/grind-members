<?
include_once APPPATH . 'libraries/enumerations.php';
class Feed extends CI_Controller {

	function __construct() {
		parent::__construct();
	

	}
	
	public function displayfeed() {
		$this->load->library('simplepie');
		$simplepie = new SimplePie();
		$simplepie->set_cache_location(APPPATH . '/cache');
		$simplepie->set_cache_duration(7200);
		$simplepie->set_feed_url('http://feeds.feedburner.com/the99percent/DPIn');
		$simplepie->init();
	//	echo $simplepie->get_title();
		//$simplepie->handle_content_type();
		$data['feed'] = $simplepie;
		$this->load->view("/feed/listing.php", $data);
		
	}
	public function displayslideshow() {
		$this->load->library('simplepie');
		$simplepie = new SimplePie();
		$simplepie->set_cache_location(APPPATH . '/cache');
		$simplepie->set_feed_url('http://feeds.feedburner.com/the99percent/DPIn');
		$simplepie->init();
		//echo $simplepie->get_title();
		$simplepie->handle_content_type();
		$data['feed'] = $simplepie;
		//$this->load->view("/feed/listing.php", $data);
		$this->load->view("/feed/slideshow.php", $data);
		
	}
}
?>