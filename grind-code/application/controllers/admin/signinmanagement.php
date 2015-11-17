<?
include_once APPPATH . 'libraries/enumerations.php';

class SignInManagement extends CI_Controller {

    function __construct() {
            parent::__construct();
            
            $this->load->helper("url");
            $this->load->helper("form");

    }
	/* deprecated
    public function signin() {
        $result = "";
        $data = array();

        $data["user_id"] = $this->uri->segment(4);
        $data["sign_in_method"] = $this->uri->segment(5, SignInMethod::RFID);

        $this->load->model("usermodel", "", true);
        $validUser = $this->usermodel->init($data["user_id"], UserIdType::ID);
        
        if ($data["sign_in_method"] != SignInMethod::RFID && $data["sign_in_method"] != SignInMethod::WIFI) {
            $data["result"] = "[Exception 1002]: Sign-in method ID \"". $data["sign_in_method"] ."\" does not exist.";
        } elseif (!$validUser) {
            $data["result"] = "[Exception 1001]: User with ID \"". $data["user_id"] ."\" does not exist.";
        } else {
            $data["result"] = $this->usermodel->checkIn($data["sign_in_method"]);
        }

        $this->load->view("admin/signin.php", $data);
        
    }
    */
    
    public function remotesignin() {
    	$result = 0; // fail by default
		// add multiple locations here in the future
		$location = 1;
		
        $this->load->model("members/membermodel");

        $member = $this->membermodel->authMacAddr($this->input->post('mac'));
        
		if ($member) { // the macaddress was found belonging to a user

			$result = ($this->membermodel->checkin($location,SignInMethod::WIFI) ? 1 : 0);
			error_log("check result" . $result,0);
			
			if(!$result) {
				$result = 4;
				echo $result;
				return $result;
			} else {
				$result = 1;
				echo $result;
				return $result;
			}
			
		} else {

			//do we have a username and password
		 	$username = $this->input->post("u");
		 	// test if the username we have is the dummy username
		 	// we use a dummy username in the transparent authentication
		 	if ($username=="mactest"){ 
				// the mac wasn't recognized and we don't have a real userid
				// fail back to radius
				$result = 2;
				echo $result;
				return $result;
			}
		 
			// now we assume we have a real username
		 
		if (!user_pass_ok($this->input->post('u'),$this->input->post('p'))){
			// user authentication with password failed.
			error_log("Invalid user login:".$this->input->post('u'),0);
			$result = 3;
			echo $result;
			return $result;
		}
		
		// initialize a member object
		$member = $this->membermodel->get_basicMemberData($this->input->post('u'),UserIdType::WORDPRESSLOGIN);

		if($this->membermodel->checkin($location,SignInMethod::WIFI)){
			// we don't add the auto sign in until after we are sure the checkin works
			if(!$this->membermodel->addMacAddr($this->input->post('mac'))){
				// this process isn't dire (they are just going to get asked to log in again
				// we log the issue to the front desk
				issue_log($member->id,"issue adding mac address during checkin");
			}
			$result = $member->id; // success
			error_log("signin checkin success!",0);
			$result = 1;
			echo $result;
			return $result;
		} else {
			// for some reason we could not checkin the user
			error_log("signin checkin failure!",0);
			$result = 4;
			echo $result;
			return $result;
		}
		error_log("checkin success!",0);
		// if we made it this far we have checked in and all good. Give users access to WIFI
		$result = 1;
    }
}

}
?>