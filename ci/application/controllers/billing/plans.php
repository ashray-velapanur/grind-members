<?

class Plans extends CI_Controller {

	function __construct() {
		parent::__construct();
		
	}
	
	public function get() {
		$this->load->model('billing/planmodel','',true);
		$data["plans"]=  $this->planmodel->get_plans();
		$this->load->view('billing/get.php',$data);
	}
	public function list_plans() {
		$this->load->model('billing/planmodel','',true);
		$data["plans"]=  $this->planmodel->get_plans();
		
		$this->load->view('billing/plan_list.php',$data);
	}
	
	
	public function choosemembership(){
		$this->load->model('billing/planmodel','',true);
		$data = $this->planmodel->get_membership_options();
		$this->load->view('billing/membership_chooser',$data);
	}	
	
};

?>
