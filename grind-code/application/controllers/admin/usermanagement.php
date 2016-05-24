<?
include_once APPPATH . 'libraries/recurlyaccess.php';
include_once APPPATH . 'libraries/enumerations.php';

class UserManagement extends CI_Controller {

	function __construct() {
		parent::__construct();
		
		$this->load->helper("url");
		$this->load->helper("form");
		$this->load->helper("html");
		$this->load->helper("nav_location");
		$this->load->helper("admin_layout");

	}

	
	public function authenticate() {
		error_log("authenticating",0);
		$is_authenticated = 0;
		$associatedUserId = -1;
		
		if (isset($_POST["mac"])) {
			error_log("mac address auth:".$_POST["mac"],0);
			$mac = $_POST["mac"];
			$this->load->model("usermodel");
        	$associatedUserId = $this->usermodel->getUserIdFromMACAddress($mac);
		}
		
        if ($associatedUserId > 0) {
            $is_authenticated = 1;
        } else {
			if (isset($_POST['u']) && isset($_POST['p'])) {
				$password = $_POST['p'];
				$this->db->where('user_login', $_POST['u']);
				$query = $this->db->get('wpmember_users');
				$results = $query->result();
				if (count($results)>0) {
					$hash = $results[0]->user_pass;
					require_once( MEMBERINCLUDEPATH.'/wp-includes/class-phpass.php');
					$wp_hasher = new PasswordHash(8, TRUE);
					$check = $wp_hasher->CheckPassword($password, $hash);
					$is_authenticated = $check;
					error_log("authentication".$check,0);
				}
			}
		}
		
		echo $is_authenticated;
	}	
	
	public function extAuth(){
			$is_authenticated = 0;
			$username = ($username == "" ? $this->input->post("u") : $username);
	        $password = ($password == "" ? $this->input->post("p") : $password);
	        $xml = "";
	        try {
	            $retXML = $this->loginXMLRPC("http://".$_SERVER["HTTP_HOST"]."/xmlrpc.php", $username, $password);
	            $xml = new SimpleXMLElement($retXML);
	            $retval = $xml->params[0]->param[0]->value[0]->boolean;
	        } catch(Exception $e) {
	            $retval = "Exception: " . $xml;
	        }
			if ($retval != 1) {
	            $issueId = $this->issuesmodel->logMemberIssue(0, "Could not Ext Auth \"$username\" to the WP database with supplied password.",  MemberIssueType::SIGNIN);
	            $this->issuesmodel->closeMemberIssue($issueId);
	        } else {
					$this->load->model("usermodel", "", true);
					$this->usermodel->init($username, UserIdType::WORDPRESSID);
					$userData = $this->usermodel->getRFID();
					echo json_encode($userData);
			}
	}
	// deprecated	
	public function searchUsers() {
		$query = $this->load->model("usermodel", "", true);
		$data["users"] = $this->usermodel->getBasicUsers();
		
		$response = '[{"header": {"title": "Members Found","num":'.count($data["users"]).',"limit": "10"},"data":'.json_encode($data[
			"users"]).'}]';
	
		echo $response;
	}
	
	public function search($value=NULL) {
		
		$this->load->helper('array');
		$this->load->model('members/membermodel','',true);
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
		
		$data = "";
		if ($value){  // we have a value to search for
		
			$data["users"] = $this->membermodel->search($value);
		} else {
			$results = false;
		}		
		
		$data["pagination"] = "";
		$data["show_member_subnav"] = true;
		$data["member_subnav_current"] = "Members Listing";
		display('admin/searchresults.php',$data,"Members Search");
	}
	
	
	
	public function users($row=NULL) {
		$this->load->library('pagination');
		$query = $this->load->model("members/membermodel", "", true);
		
		$config['base_url'] = site_url('/grind-code/admin/usermanagement/users/');
		
		$config['total_rows'] = $this->membermodel->count_members();
		$config['per_page'] = 200; 
		$config['full_tag_open'] = '<div style="display:inline-block" class="navigation">';
		$config['full_tag_close'] = '</div>';
		$config['uri_segment'] = 4;
		$data["users"] = $this->membermodel->listing($config['per_page'],$row);		
		$this->pagination->initialize($config);
		
		$data["pagination"] = $this->pagination->create_links();
		
		$data["show_member_subnav"] = true;
		$data["member_subnav_current"] = "Members Listing";
		
		display('admin/users.php',$data,"Members");
	}
	
	
	
	
	public function waitlist() {
		$query = $this->load->model("members/membermodel", "", true);
		$data["users"] = $this->membermodel->waitlist();
		
		$data["show_member_subnav"] = true;
		$data["member_subnav_current"] = "Waitlist";

		display('admin/users.php',$data,"Waitlist");
	}

	public function user($error=NULL) {
		$this->load->model("locationmodel");
		$this->load->model("usermodel");

		if ($this->uri->segment(4) != "") {
			$data["user"] = $this->usermodel->init($this->uri->segment(4));
			$data["billing_info"] = new RecurlyBillingInfo($this->uri->segment(4));
		}

		$title = (isset($data["user"]) ? "Profile of " . $data["user"]->first_name . " " . $data["user"]->last_name : "Create New Profile");
		
		$data["admin_only"] = true;
		$data["show_member_subnav"] = true;
		$data["member_subnav_current"] = "Member Registration";
		$data["accesspricing"] = $this->locationmodel->getAccessPricing();

		$data["error"]=$error;
		
		display('admin/profile.php',$data,$title);
		
	}
	
	public function register() {
		

		$this->load->model("usermodel");
	
		if ($this->uri->segment(4) != "") {
			$data["user"] = $this->usermodel->init($this->uri->segment(4));
			$data["billing_info"] = new RecurlyBillingInfo($this->uri->segment(4));
		}
		
		$data["admin_only"] = true;
		$data["show_member_subnav"] = true;
		$data["member_subnav_current"] = "Member Registration";
		
		display('admin/profile.php',$data,"Create New Profile");
		
	}

	public function invite() {
		
		$this->load->model("usermodel");
		
		if ($this->uri->segment(4) != "") {
			$data["user"] = $this->usermodel->init($this->uri->segment(4));
			$data["billing_info"] = new RecurlyBillingInfo($this->uri->segment(4));
		}
		

		$data["admin_only"] = true;
		$data["show_member_subnav"] = true;
		$data["member_subnav_current"] = "Member Invite";

		display('admin/invite.php',$data,"Send a Membership Invitation");
		
	}

	public function userupdate() {

		$this->load->model("usermodel");
		
		if ($this->uri->segment(4) != "") {
			$data["user"] = $this->usermodel->init($this->uri->segment(4));
		}
    
		$title = (isset($data["user"]) ? "Profile of " . $data["user"]->first_name . " " . $data["user"]->last_name : "Create New Profile");

		$data["admin_only"] = true;
		$data["show_member_subnav"] = true;
		$data["member_subnav_current"] = "Member Registration";
	
		display('admin/profileupdate.php',$data,$title);
		
	}


public function viewprofile($user_id,$id_type=NULL) {
		$this->load->model("locationmodel");
		$this->load->model("members/membermodel", "", true);
		$this->load->model("billing/planmodel","",true);
		$this->load->helper('form'); 
		
		if ($user_id != "") {
			$data = $this->membermodel->viewProfile($user_id,$id_type);
		}

        $name = $data["member"]->first_name . " ". $data["member"]->last_name;
		$title = (isset($data["member"]) ? ("Profile of ".$name) : "Create New Profile");
				
		$chooserdata["plans"] = $data["plans"];
		$chooserdata["selectName"] = "plan_code";
		
		$chooserdata["allplans"] = true;
		$chooserdata["accesspricing"] = $this->locationmodel->getAccessPricing();

		$data["membershipChooser"]=$this->load->view('billing/membership_chooser',$chooserdata,true);
	
		$billing["billing_info"]=$data["member"]->billing_info;
		$billing["member"]=$data["member"];
		if (isset($data["member"]->billing_info)){
			$billing["countries_dropdown"] = form_countries( "billing_info_country",$billing["billing_info"]->country,array("name"=>"billing_info[country]"));
		} else {
			$billing["countries_dropdown"] = form_countries( "billing_info_country",'US',array("name"=>"billing_info[country]"));
	}
		$data["billing_form"]= $this->load->view("members/billing_form",
		$billing,true);	

	// -----------------ACCOUNT -----------------------------------------------
		$this->load->model('billing/accountmodel','',true);
		$am = $this->accountmodel->init($data["member"]->id);	
		$data["activity"] =  $am->get_activity();
		$data["invoices"] =  $am->listInvoices();
		$data["balance"] =  $am->account->balance;	
		$data["acct_activity"]= $this->load->view('billing/account_activity.php',$data,true);
	
	// -----------------CHECKINS -----------------------------------------------
		$checkins["checkins"] = $this->membermodel->getCheckins();
		$data["checkins"] = $this->load->view('members/checkin_activity.php',$checkins,true);

		
		$data["admin_only"] = true;
		$data["show_member_subnav"] = true;
		$data["member_subnav_current"] = "Member Listing";
		display('members/admin-profile',$data,$title);
	
	}	


	public function userview() {

		$this->load->model("usermodel");
	
		if ($this->uri->segment(4) != "") {
			$data["user"] = $this->usermodel->init($this->uri->segment(4));
		}

		$title = (isset($data["user"]) ? "Profile of " . $data["user"]->first_name . " " . $data["user"]->last_name : "Create New Profile");

		$data["admin_only"] = true;
		$data["show_member_subnav"] = true;
		$data["member_subnav_current"] = "Member Listing";

		display('admin/profile',$data,$title);
		
	}	
	public function wpGetUserInfo() {
		session_start();
		$this->load->model("usermodel", "", true);
		$this->usermodel->init($_SESSION['wpuser']['wp_users_id'], UserIdType::WORDPRESSID);
		
		$userData = $this->usermodel->getBasicInformation();
		echo json_encode($userData);
	}
	
	public function wpUpdateprofile() { 
		session_start();
		if ($_POST['wp_users_id'] == $_SESSION['wpuser']['wp_users_id'])
		{
			try {
				$this->load->model("usermodel", "", true);
				$this->usermodel->init($_SESSION['wpuser']['wp_users_id'] , UserIdType::WORDPRESSID);
				$this->usermodel->wpUpdateProfile($_POST);
				echo json_encode(array("success"=>1));
			} catch (Exception $e) {
				echo json_encode(array("success"=>0,"reason"=>"SYSTEMERROR"));
			}
			
		}
	}
	
	
	public function wpUpdateEmail() {
		session_start();
		try {
			if (isset($_POST['email'])) {
				//Check for unique email address/username in Wordpress
				$this->db->where('user_login', $_POST['email']);
				$this->db->from('wpmember_users');
				$count = $this->db->count_all_results();
	
				if ($count < 1) { //New email is unique
					//Change username and email in WP db
					$data = array(
						'user_login' => $_POST['email'],
						'user_email' => $_POST['email']
					 );
					$this->db->where('ID', $_SESSION['wpuser']['wp_users_id']);
					$this->db->update('wpmember_users', $data);
					
					//Change primary email address in Grind db
					$this->load->model("usermodel", "", true);
					$this->usermodel->init($_SESSION['wpuser']['wp_users_id'] , UserIdType::WORDPRESSID);
					$this->usermodel->wpUpdateEmail($_POST);
					
					//Update recurly email address
					$account = RecurlyAccount::getAccount($_SESSION['wpuser']['id']);
					$account->email = $_POST['email'];
					$account->username = $_POST['email'];
					$account->update();
					
					//Return success
					echo json_encode(array("success"=>1));
				} else { //new email is not unique
					echo json_encode(array("success"=>0,"reason"=>"NOTUNIQUE"));
				}
			} else {
				echo json_encode(array("success"=>0,"reason"=>"EMAILEMPTY"));
			}
		} catch (Exception $e) {
			echo json_encode(array("success"=>0,"reason"=>"SYSTEMERROR"));
		}

	}
	
	public function setWpPassword() {
		$sql = "UPDATE wpmember_users SET user_pass = MD5('password2') WHERE ID = 33;";
		$query = $this->db->query($sql);
			exit;
	}
		
	
	public function wpGetRecurlyBillingInfo() {
		session_start();
		echo json_encode(RecurlyBillingInfo::getBillingInfo($_SESSION['wpuser']['id']));
	}
	public function getRecurlySubscription() {
		$wpid = $this->uri->segment(4);
		$this->load->model("usermodel", "", true);
		$this->usermodel->init($wpid , UserIdType::WORDPRESSID);
		$userData = $this->usermodel->getBasicInformation();
		echo json_encode($this->usermodel->getRecurlySubscription($userData->id)); 
	}
	
	
	
	
   	public function updateprofile() {
		$this->load->model("members/membermodel","",true);
		$result = $this->membermodel->addMember();
		if (!$result) {
			$msg = "Sorry, an error occurred when creating the member. Please check the issue log";
			$this->user($msg);
		} else {
			$this->users();	
		}
		
	}

    function usercheckins() {

		$this->load->model("usermodel");
        $this->load->model("issuesmodel");

		$userId = $this->uri->segment(4);
		$data["checkIns"] = $this->usermodel->getCheckins($userId);
		$data["user"] = $this->usermodel->init($userId);

		$data["show_member_subnav"] = true;
		$data["member_subnav_current"] = "Member Registration";	

        $data["admin_only"] = true;
		display('admin/usercheckins',$data,"User Check-Ins");

    }

    function usercharges() {

		$this->load->model("usermodel");
        $this->load->model("issuesmodel");
		
		$userId = $this->uri->segment(4);
		$data["charges"] = $this->usermodel->getChargesAndCredits($userId);
		$data["user"] = $this->usermodel->init($userId);

		$data["show_member_subnav"] = true;
		$data["member_subnav_current"] = "Member Registration";
        $data["admin_only"] = true;

		display('admin/usercharges',$data,"User Charges and Credits");
    }

	public function checkin() {
		error_log("checking in",0);
		$userId = $this->input->post("user_id");
		$locationId = $this->input->post("location_id");
		$sign_in_method = $this->input->post("sign_in_method");
		$this->load->model("members/membermodel","",true);
		$member = $this->membermodel->init($userId,UserIdType::ID);
		$signin_sheet_id = $this->membermodel->checkin($locationId, $sign_in_method);
		return $signin_sheet_id;
	}

	public function charge() {

		$this->load->model("issuesmodel");

		$userId = $this->input->post("user_id");
		$amount = $this->input->post("amount");
		$details = $this->input->post("details");
		$isCredit = ($this->input->post("dc") == "C");

		$this->load->model("usermodel");
		$this->load->model("billing/accountmodel",'am',true);
		$this->am->init($userId);
		
		if ($isCredit) {
			$result = $this->am->credit($amount,$details);
		} else{
			$result = $this->am->charge($amount,$details);
		}
		return $result;
		
	}

	public function conferencerooms() {
		$query = $this->db->get("cobot_spaces");
		$current_user = wp_get_current_user();
		$spaces = $query->result();
		$data = array();
		$data['spaces'] = $spaces;
		$data['resources'] = array();
		foreach ($spaces as $space) {
			$space_id = $space->id;
			$this->db->where("space_id", $space_id);
			$query = $this->db->get("cobot_resources");
			$resources = $query->result();
			$data['resources'][$space_id] = $resources;
			$sql = "SELECT resource_name as name, from_datetime as from_time, to_datetime as to_time FROM cobot_bookings cb JOIN cobot_memberships cm on cb.membership_id = cm.id join user u on cm.user_id = u.id  WHERE u.wp_users_id = ".$current_user->ID;
			error_log($sql);
			$query = $this->db->query($sql);
			$bookings = $query->result();
			$data['bookings'][$space_id] = $bookings;
		}
		
		$this->load->view("/admin/conference-rooms.php", $data);
	}

}

?>
