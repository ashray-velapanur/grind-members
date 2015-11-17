<?php

include_once APPPATH . 'libraries/utilities.php';
include_once APPPATH . 'libraries/enumerations.php';
include_once APPPATH . 'libraries/constants.php';
include_once APPPATH . 'libraries/recurlyaccess.php';

/**
 * Membership Model
 * 
 * Manages specific tasks associated with creating, and canceling memberships
 * 
 * @magicandmight
 * @model
 */
 
class MembershipModel extends CI_Model {
    
    public $member;
    public $membership;
     
    function __construct()
       {
           parent::__construct();
           $this->load->helper('date');
		   $this->load->helper("money_helper.php");
       }
    


    /**
     * create
     * 
     * Create a new membership
     *
     * @author magicandmight
     * @param form data = the form data for the member we are creating
     */
     
 
     
    public function create($data?) {
   
   //log_message('debug', 'wpCreateMembership');
		// assume we have user id in session
		session_start();
		try
		{
			$this->load->model("user model", "", true);
			
			$this->usermodel->init($_SESSION['wpuser']['id'], UserIdType::ID);
						
			log_message('debug', 'CREATE START');
			
			// this should move to update membership
			if (isset($_POST['hasBillingInfo']) && $_POST['hasBillingInfo'] === "true") { //Going from daily to monthly, already has billing info
				$account = Recurly_Account::get($_SESSION['wpuser']['id']);
				$billing = Recurly_BillingInfo::get($account->account_code);
				$subscription = new RecurlySubscription();
				$subscription->plan_code = $_POST['plan_code'];
				$subscription->account = $account;
				$subscription->billing_info = $billing;
				$subscription->create();
			} else {
				$this->load->model("billing/account","account",true)
				$this->account->init(//give id here);
		
				$ra = $this->account->RecurlyAccount;
				$ra->username = //email address
				$ra->first_name = //first name
				$ra->last_name = //last name
				$ra->email = //email address
				$ra->create(); // this actually creates the recurly account for the user 
						
			
			
				$recurlyAccount = $this->usermodel->createRecurlySubscription($_POST);
			}
			
			if ($_POST['plan_code'] === "daily") {
				$membership_type = MembershipType::DAILY;
			} else {
				$membership_type = MembershipType::MONTHLY;
			}
			log_message('debug', "membership_type:".$membership_type);
			$membershipData = array(
				"user_id" => $_SESSION['wpuser']['id'],
				"membership_type_code" => $membership_type,
				"start_date" => date('Y-m-d H:i:s')	 
			);
			
			//$this->usermodel->addMembership($membershipData);
			
			// not sure this is needed$this->usermodel->updateMembershipStatus($_SESSION['wpuser']['id'],MembershipStatus::ACTIVE_MEMBER);
			
			$response = array(
				"success" => 1
			);
			
			$this->db->set('meta_value',null);
			$this->db->where('user_id', $_SESSION['wpuser']['id']);
			$this->db->where('meta_key', 'registerHash');
			$this->db->update('wpmember_usermeta');
			//delete_user_meta( $currentUserId, 'registerHash');
			
		    // different responses for json and non json?
			echo json_encode($response);
   
   	}
	
	
	/**
     * update
     * 
     * update a membership
     *
     * @author magicandmight
     * @param data = an array of elements we are updating for a membership
     */
	public function updateMembership($data) {
		log_message('debug', "updateMembership:".$data);
		$this->db->where("user_id", $this->userInfo->id);
		$this->db->update("membership", $data);
	}

	 /**
     * delete
     * 
     * delete/cancel a membership
     *
     * @author magicandmight
     * @param id = the account_code we are deleting the membership for
     */
	public function deleteMembership($id) {
		$this->db->delete('membership', array('user_id' => $id)); 
	}
    
     
?>