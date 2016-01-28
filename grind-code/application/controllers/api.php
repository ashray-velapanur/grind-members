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

     function jobs_get() {
          $type = $this->get('type');
          $posted_by = $this->get('posted_by');
          $this->load->model("jobsmodel","jm",true);
          $this->response($this->jm->get($type, $posted_by), 200);
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

     function positions_put(){
        $user_id = $this->get('user_id');
        $access_token = $this->get('access_token');
        $this->load->model("positionsmodel","pm",true);
        $url = "https://api.linkedin.com/v1/people/~:(positions)?format=json&oauth2_access_token=".$access_token;
        $profile = json_decode(file_get_contents($url));
        foreach ($profile->positions->values as $value) {
          $company = $value->company;
          var_dump($value->company);
          $sql = "INSERT INTO company (id, name) VALUES ('$company->id', '$company->name')";
          $this->db->query($sql);
          $this->pm->create($user_id, $company->id);
        }
        var_dump($response);
     }

     function company_tags_get() {
          $company_id = $this->get('company_id');
          $this->load->model("positionsmodel","pm",true);
          $this->load->model("usertagsmodel","utm",true);
          $this->load->model("jobtagsmodel","jtm",true);
          $this->load->model("tagsmodel","tm",true);
          $response = array();
          foreach ($this->pm->get($company_id) as $position) {
            $user_id = $position['user_id'];
            foreach ($this->utm->get($user_id) as $user_tag) {
              $tag_id = $user_tag['tag_id'];
              $name = $this->tm->get($tag_id)['name'];
              $total_count = $this->utm->count($tag_id) + $this->jtm->count($tag_id);
              array_push($response, array('name'=>$name, 'id'=>$user_tag['tag_id'], 'count'=>$total_count));
            }
          }
          $this->response($response, 200);
     }

     function user_tags_get() {
          $user_id = $this->get('user_id');
          $this->load->model("usertagsmodel","utm",true);
          $this->load->model("jobtagsmodel","jtm",true);
          $this->load->model("tagsmodel","tm",true);
          $user_tags = $this->utm->get($user_id);
          $response = array();
          foreach ($user_tags as $user_tag) {
            $tag_id = $user_tag['tag_id'];
            $total_count = $this->utm->count($tag_id) + $this->jtm->count($tag_id);
            $name = $this->tm->get($tag_id)['name'];
            array_push($response, array('name'=>$name, 'id'=>$user_tag['tag_id'], 'count'=>$total_count));
          }
          $this->response($response, 200);
      }
}
?>