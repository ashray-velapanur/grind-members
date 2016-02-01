<?php

/**
 * API RESTful Controller
 * 
 * Provides an extensible API for programatically
 * interacting with the GRIND subsystem
 * 
 * @author joshcampbell
 * @param 
 */


require(APPPATH.'/libraries/REST_Controller.php');

class Api extends REST_Controller
{
	
	function print_post() {
	

		$data= urldecode($_POST['jobs']);
		
		$xml = new SimpleXMLElement(stripslashes($data));
        				
		$this->load->model("/billing/printmodel","pm",true);
		
		$result = $this->pm->processCharges($xml);
		
        if($result === FALSE)
        {
        	$this->response(array('status' => 'failed','reason'=>'some reason'),400);
        }

        else
        {
        	$this->response(array('status' => 'success'),200);
        }
     	return $this->response;  
	}
	
	
	/**
	 * users_get()
	 * 
	 * retrieves a set of user data and returns
	 * 
	 * @author magicandmight
	 * @params
	 */	
	function users_get() {
		
		$this->load->model('/members/membermodel','mm',true);
				
		$users = $this->mm->export_members();

        if($users)
        {
            $this->response($users, 200);
        }

        else
        {
            $this->response(NULL, 404);
        }
	}
	
	
	 function usernametest_get() {
         
          $this->load->model('/members/membermodel','mm',true);
                   
          $uid = $this->mm->generate_username($this->get('id'));
          error_log('uid: '.$uid,0);

        if($uid)
        {
            $this->response($uid, 200);
        }

        else
        {
            $this->response(NULL, 404);
        }
     }

	// user checkin
	/*
	 function usercheckin_post() {

          $this->load->model('/members/membermodel','mm',true);
          $member = $this->mm->get_basicMemberData($this->post('id'),UserIdType::RFID);
		  $checkin = $this->mm->checkin($this->post('locationid'),$this->post('signinmethod'));

        if($checkin)
        {
            $this->response($checkin, 200);
        }

        else
        {
            $this->response(NULL, 404);
        }
     }
     */
     function testcredits_get($user_id) {
         
          $this->load->model("/billing/printmodel","pm",true);
          $this->pm->test_credits($user_id);
                   
      }

      function company_jobs_get() {
        $id = $this->get('id');
        $this->load->model('members/companymodel','',true);
        $data =  $this->companymodel->get_jobs($id);
        $this->response($data, 200);
      }

      function company_members_get() {
        $id = $this->get('id');
        $this->load->model('members/companymodel','',true);
        $data =  $this->companymodel->get_members($id);
        $this->response($data, 200);
      }

      function profile_post() {
        $id = $this->post('id');
        $company_name = $this->post('company_name');
        $title = $this->post('designation');
        $tags_str = $this->post('tags');
        $tags = explode("##", $tags_str);
        $this->load->model('/members/membermodel','mm',true);
        $success = false;
        $success = $this->mm->update_profile_data($id, $company_name, $title, $tags);
        $this->response(['success' => $success], 200);
      }

      function profile_get() {
        $id = $this->get('id');
        $this->load->model('/members/membermodel','mm',true);
        $profile = $this->mm->get_profile_data($id);
        $this->response($profile, 200);
      }
}
?>