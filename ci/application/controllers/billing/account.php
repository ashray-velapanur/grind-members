<?

/**
 * Account Controller
 * 
 * Manages tasks associated with the member accounts (billing activities)
 * 
 * @joshcampbell Magic+Might
 * @controller
 */


include_once APPPATH . 'libraries/enumerations.php';

class Account extends CI_Controller {

	function __construct() {
		parent::__construct();
		
	}
	

	public function activity($user_id) {
		$this->load->model('billing/accountmodel','',true);
		$am = $this->accountmodel->init($user_id);
		
		//$data["transactions"]=  $this->account_activity_model->listTransactions($user_id);
		
		$data["activity"] =  $am->get_activity();
		$data["invoices"] =  $am->listInvoices();
		$data["balance"] =  $am->account->balance;


		$this->load->view('billing/account_activity.php',$data,"All Transactions");
	
	}
	
	public function getInvoice($account_code,$id) {
		$this->load->model('billing/invoicemodel','',true);
		$this->invoicemodel->init($account_code,$id);
		$invoice = $this->invoicemodel->getInvoice($account_code,$id);
		echo var_dump($invoice);

	}
	
	public function plancheck($account_code) {
		$this->load->model('billing/accountmodel','',true);
		$this->accountmodel->init($account_code);
		$details = $this->accountmodel->getDetails($account_code);
		if($details){
			echo json_encode($details);
			exit;
		} else {
			echo json_encode(false);
			exit;
		}

	}
	
	// returns JSON
	public function updateBilling($user_id){

			$this->load->model('billing/accountmodel','',true);
			$this->accountmodel->init($user_id);
			$updatedBillingInfo = $this->accountmodel->updateBillingInfo($_POST);
			echo json_encode($updatedBillingInfo);	
	}
	
	public function changeMembership($user_id){
	    error_log("current plan -> ".$_POST['current_plan'],0);
        error_log("change plan to -> ".$_POST['plan_code'],0);
	    error_log("changing membership",0);
		$now = true;
		if($_POST['plan_code'] == $_POST['current_plan']){
		    $result = array("nochange" => 1);
		} elseif ($_POST['plan_code']=="daily" && $_POST['current_plan'] != "daily"){
		    error_log("update subscriber to daily. addsub is false",0);
		   $this->update($user_id,false,true);
		} else {
		    error_log("change the plan");
			if ($now==false){
			  $this->update($user_id,true,false); // change subscription at renewal
			} else {
			  $this->update($user_id,true,false); // change subscription immediately
			}
		 
		}
	}
	
	public function update($user_id,$addsub,$now=NULL){
			error_log("user:".$user_id.", add a sub:". $addsub.", now:".$now,0 );
			$this->load->model('billing/accountmodel','',true);
			$this->accountmodel->init($user_id);			
			$result = $this->accountmodel->updateMembership($addsub,$now,$_POST);
			echo json_encode($result);	
	}
	
	private function display($viewtemplate,$data,$title=NULL){
		$vars["title"] = $title;
		$page["header"]=$this->load->view('template/_header',$vars,true);
		$page["footer"]=$this->load->view('template/_footer','',true);
		$page["content"]=$this->load->view($viewtemplate,$data,true);
		$this->load->view('template/_frame',$page);
		
	}
	
	
}