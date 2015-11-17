<?
include_once APPPATH . 'libraries/enumerations.php';
class ApplicantManagement extends CI_Controller {

	function __construct() {
		parent::__construct();
		
		$this->load->helper("url");
		$this->load->helper("form");
		$this->load->helper("nav_location");
		$this->load->helper("admin_layout");

	}
	
	public function applicants() {
		$this->load->model("applicantmodel", "", true);
		$this->load->model("lookuptablemodel", "", true);
		
        $status_id = $this->uri->segment(4);
        if ($status_id == null)
        {
            $status_id = MembershipStatus::APPLICANT_AWAITING_APPROVAL;
        }
		
		$data["applicants"] = $this->applicantmodel->getApplicants($status_id);

		$data["admin_only"] = true;
		$data["show_member_subnav"] = true;
		$data["member_subnav_current"] = "Applicants";
        $data["status_chosen"] = $status_id;     
        $data["statuses"] = $this->lookuptablemodel->getTableEntries("membership_status");

		display('admin/applicants',$data,"Applicants");
		
	}
	
    public function insertApplicantJsonP() {
		$return = "";
		error_log("new applicant",0);
		try
		{
			if (isset($_REQUEST['email_address'])) {
				$this->db->where('address', $_REQUEST['email_address']);
				$this->db->from('email');
				$count = $this->db->count_all_results();
	
				if ($count < 1) { //New email is unique
					//Save applicant data
					$this->load->model("applicantmodel", "", true);
					$this->applicantmodel->insertApplicant($_REQUEST);
					
					//Send confirmation email to applicant
					$this->load->model("emailtemplates/emailtemplatemodel", "", true);
					$email = $this->emailtemplatemodel->init(1);
					$result = $this->emailtemplatemodel->send($_REQUEST['email_address']);
					
					
					$return =  '{"success" : "1"}';
				} else {
					$return =  '{"success" : "0", "reason" : "EMAILNOTUNIQUE"}';
				}
			} else {
				$return =  '{"success" : "0", "reason" : "INVALIDPOSTDATA"}';
			}
		}
		catch(Exception $e)
		{
			$return =  '{"success" : "0", "reason" : "SYSTEMERROR"}';
		}
		
		if (isset($_GET['callback'])) {
			echo $_GET['callback'] . '(' . $return . ')';
		} else {
			echo $return;
		}
		
	}
	
	public function approveApplicant($id) {

		$this->load->model("applicantmodel", "", true);
		$this->applicantmodel->approveApplicant($id);
		
		//Create Wordpress user
		$this->load->model("members/membermodel","",true);
		$member = $this->membermodel->get_basicMemberData($id);

		
		$idPassAndHash = $this->applicantmodel->createWordpressAccount($member->email, $member->first_name, $member->last_name );
		$wpid = $idPassAndHash["wordpressid"];
		$tempPassword = $idPassAndHash["temporarypassword"];
		$registerHash = $idPassAndHash["registerHash"];
		$data = array('wp_users_id' => $wpid);
		$this->db->where('id', $id);
		$this->db->update('user',$data);
		error_log("applicantmgmt: adding wpid to table: ".$this->db->last_query(),0);
        
		
    
		//Send confirmation email to applicant
		$this->load->model("emailtemplates/emailtemplatemodel", "", true);
		$email = $this->emailtemplatemodel->init(2);
		//Replace variables with our data
		
		$email->message = str_replace("%%first_name%%",$member->first_name, $email->message);
		$email->message = str_replace("%%last_name%%",$member->last_name, $email->message);
		$email->message = str_replace("%%ID%%",$member->email,$email->message);
		$email->message = str_replace("%%ID2%%",$member->email,$email->message);
		$email->message = str_replace("%%HASH%%",$registerHash,$email->message);
		$result = $this->emailtemplatemodel->send($member->email);
		
		if($this->input->is_ajax_request()){
			if ($result){
				$response = array("success"=>"1","type"=>"approve");
			} else {
				$response = array("success"=>"0","type"=>"approve");
			}
			echo json_encode($response);
		} else {
			$this->applicants();
		}	
		
	}
	
	public function delete($id) {
		$this->load->model("applicantmodel", "", true);
		$result = $this->applicantmodel->delete($id);
		
		if($this->input->is_ajax_request()){
			if ($result){
				$response = array("success"=>"1","type"=>"delete");
			} else {
				$response = array("success"=>"0","type"=>"delete");
			}
			echo json_encode($response);
		} else {
			$this->applicants();
		}

	}
	
	
	public function denyApplicant() {
		$this->load->model("applicantmodel", "", true);
		$result = $this->applicantmodel->denyApplicant($this->uri->segment(4));
		
		if($this->input->is_ajax_request()){
			if ($result){
				$response = array("success"=>"1","type"=>"deny");
			} else {
				$response = array("success"=>"0","type"=>"deny");
			}
			echo json_encode($response);
		} else {
			$this->applicants(2);
		}

	}
	/* Invite users to become a member
	  1) has to be done by an Administrator
	  2) moves "applicants" directly into the approved/waiting registration mode
	*/
	public function inviteuser() {
		
		//Save applicant data
		$this->load->model("applicantmodel", "", true);
		$userid = $this->applicantmodel->insertApplicant($_REQUEST);
		$this->applicantmodel->approveApplicant($userid);
		
		//Create Wordpress user
		$this->load->model("usermodel", "", true);
		$this->usermodel->init($userid, UserIdType::ID);
		$userInfo = $this->usermodel->getBasicInformation();
		$emails = $this->usermodel->getEmails(false);
		
		$idPassAndHash = $this->applicantmodel->createWordpressAccount($emails[0]->address, $userInfo->first_name, $userInfo->last_name );
		$wpid = $idPassAndHash["wordpressid"];
		$tempPassword = $idPassAndHash["temporarypassword"];
		$registerHash = $idPassAndHash["registerHash"];
		
		if (!$userInfo->wp_users_id) {
			log_message('debug',"debug:no wp id exists".$userid."/".$wpid);
			$this->usermodel->updateWordpressId(array("id" => $userid, "wp_users_id" => $wpid));
		}
		
		// send confirmation email (the last parameter identifies it as an invite)
		$this->load->model("emailtemplates/emailtemplatemodel", "", true);
		$email = $this->emailtemplatemodel->init(3);
		//Replace variables with our data
		
		if(!isset($userInfo->referrer)){
			$userInfo->referrer = "a friend of yours";
		}
		$email->subject = str_replace("%%referrer%%",$userInfo->referrer, $email->subject);
		$email->message = str_replace("%%referrer%%", $userInfo->referrer,$email->message);
		$email->message = str_replace("%%first_name%%",$userInfo->first_name, $email->message);
		$email->message = str_replace("%%last_name%%",$userInfo->last_name, $email->message);
		$email->message = str_replace("%%ID%%",$emails[0]->address,$email->message);
		$email->message = str_replace("%%ID2%%",$emails[0]->address,$email->message);
		$email->message = str_replace("%%HASH%%",$registerHash,$email->message);
		$result = $this->emailtemplatemodel->send($emails[0]->address);
		
		
		$this->applicants(3);
	}
	
	
}

?>
