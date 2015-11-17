<?

/**
 * Email template Controller
 * 
 * Manages tasks associated with the Email Templates
 * 
 * @joshcampbell
 * @controller
 */


include_once APPPATH . 'libraries/enumerations.php';

class Emailtemplate extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->helper("url");
 	    $this->load->helper("form");
        $this->load->helper("cookie");
        $this->load->helper("html");
		$this->load->helper("nav_location");
		$this->load->helper("admin_layout");

		
	}
	
	public function index() {
		$this->load->model('emailtemplates/emailtemplatemodel','',true);
		$data["templates"]=  $this->emailtemplatemodel->get_all();
		display('emailtemplates/list',$data,"Email Templates");

	}
	
	public function get($id) {
		$this->load->model('emailtemplates/emailtemplatemodel','',true);
		$template = $this->emailtemplatemodel->init($id);
		if ($template){
			$data["attrs"]=  $template;
			display('emailtemplates/get',$data,"Email Template");
		} else {
			$this->index();
		}
	}
	public function edit($id=NULL) {
		log_message("debug","Trying to edit a message");
		$this->load->model('emailtemplates/emailtemplatemodel','',true);
		$template = $this->emailtemplatemodel->init($id);
		if ($template){
			$data["template"]=  $template;
			display('emailtemplates/edit',$data,"Email Template Editor");
		} else {
			display('emailtemplates/edit','',"Email Template Editor");
		}
	}
	public function create($data=NULL) {
		$this->load->model('emailtemplates/emailtemplatemodel','',true);
		if($_POST){
			$data = $this->input->post();
			$template = $this->emailtemplatemodel->local_init($data);
		} elseif(isset($data)){
			$data = $data;
			$template = $this->emailtemplatemodel->init($data);
		} else{
			// die no data passed
			return false;
		}
		$result = $this->emailtemplatemodel->create();
		if($result){
			log_message("debug","Create Successful redirecting back to view");
			$this->get($template->id);
			} else {
			log_message("debug","Create Failed redirecting back to edit");
			$this->edit($template->id);
			}	
		}
		
	public function update($data=NULL) {
		$this->load->model('emailtemplates/emailtemplatemodel','',true);
		if($_POST){
			$data = $this->input->post();
			$template = $this->emailtemplatemodel->local_init($data);
		} elseif(isset($data)){
			$data = $data;
			$template = $this->emailtemplatemodel->init($data);
		} else{
			// die no data passed
			return false;
		}
		$result = $this->emailtemplatemodel->update();
		
		if($result){
			log_message("debug","Update Successful redirecting back to view");
			$this->get($template->id);
			} else {
			log_message("debug","Update Failed redirecting back to edit");
			$this->edit($template->id);
		}
	}
	
	public function delete($id) {
		$this->load->model('emailtemplates/emailtemplatemodel','',true);
		$this->emailtemplatemodel->init($id);
		$result = $this->emailtemplatemodel->delete();
		
		if($result){
			log_message("debug","Delete Successful redirecting back to list");
			$this->index();
		} else {
			log_message("debug","Delete Failed redirecting back to get");
			$this->get($id);
		};
	}
	
	public function testsend() {
		$this->load->model('emailtemplates/emailtemplatemodel','',true);
		$template = $this->emailtemplatemodel->init(14);
		$result = $template->send('sendtojosh@gmail.com');
		
		if($result){
			echo $result;
		} else {
			echo "message failed";
		};
	}
	
}