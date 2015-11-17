<?php
include_once $_SERVER['DOCUMENT_ROOT'] .ROOTMEMBERPATH. '/_siteconfig.php';
include_once APPPATH . 'libraries/enumerations.php';

status_header( 200 );

class Utility extends CI_Controller {

	function __construct() {
		parent::__construct();
		
		$this->load->helper("url");
		$this->load->helper("form");
	}

	public function uniqueEmail() {
		// we are passed a email id in the request object
		    if (isset($_REQUEST['primary_email'])) { // some forms use a different email field label
				$email = $_REQUEST['primary_email'];
			} elseif(isset($_REQUEST['email_address'])) {
				$email = $_REQUEST['email_address'];
			} elseif(isset($_REQUEST['email'])) {
				$email = $_REQUEST['email'];
			} else {
				echo json_encode(false); //no email is present
				return;
			};
		// test if the request contains a user id (determine if users are changing an existing useremail address)
		if(isset($_REQUEST['uid'])) // we do have a user ID
		{ 
			$user_id = $_REQUEST['uid'];
			$this->db->select('wpmember_users.user_login as "user_login", user.id as "id"');
			$this->db->from('wpmember_users');
			$this->db->join('user','user.wp_users_id = wpmember_users.id');
			$this->db->where('user_login', $email);
			$query = $this->db->get();			
			if ($query->num_rows() > 0)  // email not unique
			{
				$data = $query->row();
				if($user_id==$data->id) //Email is not unique but it is the same as the current users? 
				{ 
					echo json_encode(true);	
				} 
				else // email not unique not users
				{ 
					echo json_encode(false);
				}
			} 
			else //Email is unique
			{ 
				echo json_encode(true);
			} // end
			
		} else { // we do not have a user id. We assume that this is just checking if the ID is already in use
			$this->db->where('address', $email);
			$this->db->from('email');
			$count = $this->db->count_all_results();
	
			 if ($count < 1) { //Email is unique
				echo json_encode(true);
			} else { //Email is not unique
				echo json_encode(false);
			}
		} // end 
	}
	
	public function check_current_pass($user_login,$user_password) {
			if (!user_pass_ok($user_login, $user_password)) {
			return json_encode(false);
			}
			return json_encode(true);
	}
	
	public function SendRequestConfRoomMail() {
		if($_SESSION['wpuser']['wp_users_id']){
			$user_id = $_SESSION['wpuser']['wp_users_id'];
		} else {
			echo "2"."unknown user";
			return; // exit we don't' know who this user is
		}
		$this->load->library('parser');
		//isset($_POST['form-location'])
		if (1==1) {
			$this->load->model("locationmodel", "", true);
			$location = $this->locationmodel->getLocation($_POST['form-location']);
			$space_name = "None selected";
			if (isset($_POST['space']) && $_POST['space'] !== "-1") {
				$space = $this->locationmodel->getSpace($_POST['space']);
				$space_name = $space->name;
			} 
			
			$this->load->model("members/membermodel","",true);
			$userData = $this->membermodel->get_basicMemberData($user_id, UserIdType::WORDPRESSID);
			
			if(!$userData){
				echo "2";
				return; // exit we don't' know who this user is
			}
			$submitterEmail = $userData->email;
			$data["first_name"] =$userData->first_name;
			$data["name"] =$userData->first_name . " " . $userData->last_name;
			$data["member_email"] = $submitterEmail;
			$data["location"] = $location->name;
			$data["conf_room"] = $space_name;
			$data["from_date"] = $_POST['fromDate'];
			$data["to_date"] = $_POST['toDate']; 
			$data["from_time"] = $_POST['time1'];
			$data["to_time"] = $_POST['time2'];
			$data["message"] = $_POST['message'];
			
			$msg = $this->parser->parse('emailtemplates/conf_room.php', $data, true);
			
			$this->load->model("emailtemplates/emailtemplatemodel", "", true);
			$adminemail = $this->emailtemplatemodel->init(15);
			$adminemail->message = $adminemail->message . $msg;	
			$result = $this->emailtemplatemodel->send(EMAIL_G_MEMBERREQ);				
			if ($result){
				$memberemail = $this->emailtemplatemodel->init(16);
				$memberemail->message = $memberemail->message . $msg;
				$memberemail->message = str_replace("%%first_name%%",$userData->first_name, $memberemail->message);
				$result = $this->emailtemplatemodel->send($submitterEmail);
				echo 1;
			} else {
				echo 0;
			}
		} else {
			echo "2"."unknown anything";
		}
	}
	
	public function SendHelpMail($user_id,$id_type = null) {
			if (isset($_POST['message'])) {
			
			$this->load->model("members/membermodel","",true);
			$member = $this->membermodel->get_basicMemberData($user_id,$id_type);
			$submitterEmail = $member->email;
			
			$msg = "Name: " . $member->first_name . " " . $member->last_name . "\n";
			$msg .= "Email: " . $submitterEmail . "\n";
			if (isset($_POST["message"])) {
			$msg .= "Question: " . $_POST["message"];
			}

			$this->load->model("emailtemplates/emailtemplatemodel", "", true);
			$adminemail = $this->emailtemplatemodel->init(17);
			$adminemail->message = $adminemail->message . $msg;	
			$result = $this->emailtemplatemodel->send(EMAIL_G_MEMBERREQ);				
			if ($result){
				$memberemail = $this->emailtemplatemodel->init(18);
				$memberemail->message = $memberemail->message . $msg;
				$result = $this->emailtemplatemodel->send($submitterEmail);
				echo 1;
			} else {
				echo 0;
			}
		} else {
			echo 2;
		}
	}
	
}

?>