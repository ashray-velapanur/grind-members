<?

/**
 * Search Controller
 * 
 * Manages tasks associated with the Searching (for members)
 * 
 * @joshcampbell
 * @magic+might
 * @controller
 */
 

class Search extends CI_Controller {

	function __construct() {
		parent::__construct();
	}
	
	/**
	 * Default controller action
	 * takes a search value and returns results
	 * 
	 * if an AJAX request, it will return data JSON encoded
	 *
	 * @author your name
	 * @tag value
	 */
	public function go($value=NULL) {
		$this->load->helper('array');
		$this->load->model('members/membermodel','',true);
		echo var_dump($this->input->post());
			if (!isset($value)){
				if (isset($_POST)){
					if($this->input->post('term')){
						$value = $this->input->post('term');
					} elseif($this->input->post('q')) {
						$value = $this->input->post('q');
					} else{
						$value = NULL;
					}
				}
			}
		
		$results = "";
		if ($value){  // we have a value to search for
		
			$results["members"] = $this->membermodel->search($value);
		} else {
			$results = false;
		}
		
		if($this->input->is_ajax_request()){
			// output should be json encoded
			echo json_encode($results);
		} else {
			$results = (!$results) ? array('error'=>'no results') : $results;
			$this->load->view('members/searchlisting.php',$results);
		}
	} // end index
	
	
	public function smartsuggest($value=NULL) {
		$this->load->helper('array');
		$this->load->model('members/membermodel','',true);

		if (!isset($value)){
			$value = isset($_POST) ? $this->input->post('q') : NULL;
		}
		
		$results = "";
		if ($value){  // we have a value to search for
		
			$results["members"] = $this->membermodel->search($value);
		} else {
			$results = false;
		}
		
		if($this->input->is_ajax_request()){
			// output should be json encoded
			echo json_encode($results);
		} else {
			$results = (!$results) ? array('error'=>'no results') : $results;
			$this->load->view('members/searchlisting.php',$results);
		}
	} // end index
	
		
	
}