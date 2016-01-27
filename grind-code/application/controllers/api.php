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

include_once APPPATH . 'libraries/enumerations.php';
require(APPPATH.'/libraries/REST_Controller.php');

class Api extends REST_Controller
{
	
	function print_post() {
	

		$data= urldecode($_POST['jobs']);
		
		$xml = new SimpleXMLElement($data);
				
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
	
	// user checkin
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
	
	// remove test harness
	// move this to a utility function when registering for an account
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
     
     
		
     function testcredits_get($user_id) {
         
          $this->load->model("/billing/printmodel","pm",true);
          $this->pm->test_credits($user_id);
                   
      }
		// end remove

     function create_tag_get() {
          $name = $this->get('name');
          $this->load->model("tagsmodel","tm",true);
          $this->tm->create($name);
      }

     function create_job_get() {
          $title = $this->get('title');
          $company_id = $this->get('company_id');
          $type = $this->get('type');
          $posted_by = $this->get('posted_by');
          $url = $this->get('url');
          $this->load->model("jobsmodel","tm",true);
          $this->tm->create($title, $company_id, $type, $url, $posted_by);
      }

     function tags_get() {
          $this->load->model("tagsmodel","tm",true);
          $this->response($this->tm->all(), 200);
      }

     function create_user_tag_get() {
          $user_id = $this->get('user_id');
          $tag_id = $this->get('tag_id');
          $this->load->model("usertagsmodel","utm",true);
          $this->response($this->utm->create($user_id, $tag_id));
      }

     function user_tags_get() {
          $user_id = $this->get('user_id');
          $this->load->model("usertagsmodel","utm",true);
          $this->response($this->utm->get($user_id), 200);
      }

}
?>