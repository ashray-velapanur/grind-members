<?

/**
 * Profile Controller
 * 
 * Manages tasks associated with the Member Profile from the member profile screen
 * 
 * @author magicandmight
 * @controller
 */


class Profile extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->helper('date');
		setlocale(LC_MONETARY, 'en_US');
		$this->load->helper("url");
		$this->load->helper("form");
		$this->load->helper("html");
	}

	public function view($user_id,$id_type=NULL,$membershipsuccess=FALSE){
	
		$this->load->helper('form'); 
		$this->load->model("members/membermodel", "", true);
		$data = $this->membermodel->viewProfile($user_id,$id_type);
		
		$billing["billing_info"]=$data["member"]->billing_info;
		$billing["member"]=$data["member"];
		if (isset($data["member"]->billing_info)){
			$billing["countries_dropdown"] = form_countries( "billing_info[country]",$billing["billing_info"]->country,array("id"=>"billing_info_country"));
			} else {
			$billing["countries_dropdown"] = form_countries( "billing_info[country]",'US',array("id"=>"billing_info_country"));
		}
		
		$data["membershipsuccess"] = $membershipsuccess;
		$data["billing_form"]= $this->load->view("members/billing_form",
		$billing,true);		
		
		$this->load->model("locationmodel", "", true);
		$accesspricing = $this->locationmodel->getAccessPricing();
		$data["allow_monthly"]=$accesspricing->allow_monthly_memberships;
		
		//#213 - Waitlist
		$data["waitlist"]=get_user_meta($data['member']->wp_users_id,'waitlist', true);
		
		$this->load->view("members/profile", $data);
	}
	

	public function dump($user_id,$id_type){
		$this->load->model("members/membermodel", "", true);
		$member = $this->membermodel->get_fullMemberData($user_id,$id_type);
		var_dump( $member);
	}
	
	public function edit($user_id,$id_type = null){
		log_message("debug:","Edit profile -> Controller");
		//session_start();
		
		$this->load->model("members/membermodel", "", true);

		$allowed = is_this_user($user_id) ? true : false; 	// make sure the user is the same as the user in session
		if(!$allowed){
			 $allowed = is_administrator() ? true : false; 		// if it isn't the same user…ensure the user is of ADMIN role
		}
		if (!$_POST) {
			echo json_encode(array("success"=>0,"reason"=>"Nothing Changed"));
			return false;
		}		

		if ($allowed) 
		{
			try {
				log_message("debug:",$_POST);
				$this->membermodel->editProfile($user_id,$_POST);
			} catch (Exception $e) {
				echo json_encode(array("success"=>0,"reason"=>"SYSTEMERROR"));
				return false;
			}
			echo json_encode(array("success"=>1));
			return true;
		} else { // not allowed
			echo json_encode(array("success"=>0,"reason"=>"Invalid User Credentials"));
			return false;
		}
	}
	
	public function reset_pw($user_id){
		log_message("debug","trying to reset pw");
		$this->load->model("members/membermodel", "", true);
		//$member = $this->membermodel->get_fullMemberData($user_id,$id_type);
		$result = $this->membermodel->reset_pw($user_id);
		if($result){
			echo json_encode(array("success"=>1));
			
			}else{
			echo json_encode(array("success"=>0,"reason"=>"Unknown Error"));

		}
		log_message("debug","result".$result);
		
	}
	
	public function delete($user_id=NULL){
		$this->load->helper("nav_location");
		$this->load->helper("admin_layout");
		
		$allowed = is_administrator() ? true : false; 		// if it isn't the same user…ensure the user is of ADMIN role
		// ensure that the user is an adminstrator when issuing a delete command
		$data["show_member_subnav"] = true;
		$data["member_subnav_current"] = "Remove member";
		
		if ($allowed){
			$data["allowed"]=true;
			
			error_log("deleting a user",0);
			
			
			$this->load->model("members/membermodel", "", true);
			$user_id = isset($_POST["user_id"]) ? $_POST["user_id"] : $user_id;
			error_log("userid:".$user_id,0);
			if ($user_id) {
				$member_result = $this->membermodel->get_basicmemberdata($user_id);
				if($member_result){
					$data["member"]=$member_result;
					$result = $this->membermodel->delete($user_id);	
				} else {
					$result = false;
					$data["member"]=false;
					$data["error"]="Unknown member id";
				}
					
			} else {
				$data["member"]=false;
				$result = false;
			}
			
			if($this->input->is_ajax_request()){
				if ($result){
					$response = array("success"=>"1","type"=>"delete");
					} else {
						$response = array("success"=>"0","type"=>"delete");
					}
				echo json_encode($response);
			} else {
				$data["response"] = $result;
				display('members/delete',$data,"Delete member");
			}
		
		} else {
			$data["response"] = false;
			$data["allowed"]=false;
			
			display('members/delete',$data,"Delete member");
		}
		
		
		
	}
	
	//registers a new user
	//handles post from new_member_registration
	public function register($user_id,$id_type=NULL){
		error_log("Controller: Register:".$user_id."/".$id_type,0);
		$this->load->model('members/membermodel','',true);
		
		$this->membermodel->init($user_id,$id_type);

		$this->membermodel->get_basicMemberData($user_id,$id_type);
		$results = $this->membermodel->register();
		
		if($this->input->is_ajax_request()){
			// output should be json encoded
			error_log("what is coming back".$results,0);
			if((!$results["error"]) && $results){
				error_log("we should be telling javascript good news",0);
				$response = array(
				"success" => 1
				);
				$results = json_encode($response);
				error_log($results,0);
				echo $results;
				return;
			} else {
				error_log("but it is getting bad",0);
				if (is_array($results)){
					$results = json_encode($results);
				}
				issue_log($user_id,"error when registering member");
				echo $results;
			}
			
		} else {
			echo $results;
		}
	}

	
}


?>