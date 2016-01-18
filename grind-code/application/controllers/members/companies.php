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

	public function all() {
		$this->load->model('members/companymodel','',true);
		$companies = $this->companymodel->get_all();
		$companies_data = [];
		foreach ($companies as $company) {
			$company = (array)$company;
			$company_data = [
				'name' => $company['name'],
				'logo' => 'data:image/jpeg;base64,'.base64_encode( $company['logo'] ),
				'description' => $company['description']
			];
			array_push($companies_data, $company_data);
		}
		$data = [
			"companies" => $companies_data
		];
		return json_encode($data);
		//$this->load->view('members/companies/all.php',$data);
	}

	public function get_members() {
		$id = $_GET['id'];
		$this->load->model('members/companymodel','',true);
		$data =  $this->companymodel->get_members($id);
		return $data;
		//$this->load->view('members/companies/members.php',$data);
	}
	
}