<?

/**
 * Companies Controller
 * 
 * Manages tasks associated with the Company Profile
 * 
 * @joshcampbell
 * @controller
 */


include_once APPPATH . 'libraries/enumerations.php';
include_once APPPATH . 'libraries/recurlyaccess.php';

class Companies extends CI_Controller {

	function __construct() {
		parent::__construct();
	}
	
	public function get($id) {
		$this->load->model('members/companymodel','',true);
		$data["attrs"]=  $this->companymodel->init($id);
		$this->load->view('members/companies/get.php',$data);
	}
	public function lists() {
		$this->load->model('members/companymodel','',true);
		$data["companies"]=  $this->companymodel->get_all();
		
		$this->load->view('members/companies/list.php',$data);
	}
	
}