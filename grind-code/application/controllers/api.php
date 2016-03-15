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
require(APPPATH.'/config/cobot.php');
require(APPPATH.'/controllers/admin/spaces_dict.php');

class Api extends REST_Controller
{
	function __construct() {
    parent::__construct();
    $this->output->enable_profiler(TRUE);
    if($this->uri->uri_string != 'api/login') {
      $id = $this->_args['user_id'];
      if(!$id) {
        $this->response(array('message'=>'User ID not provided', 'success'=>FALSE), 404);
      }
      $this->load->model('/members/membermodel', 'mm', true);
      if (!$this->mm->get_basicMemberData($id)) {
        $this->response(array('message'=>'Invalid User ID.', 'success'=>FALSE), 404);
      }
    }
  }

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

     function tags_put() {
          $name = $this->put('name');
          if (!$name) {
            $response = array('success'=> FALSE, 'message'=>'Invalid parameters.');
          } else {
            $this->load->model("tagsmodel","tm",true);
            if ($this->tm->create($name)){
              $response = array('success'=> TRUE);
            } else {
              $response = array('success'=> FALSE);
            }
          }
          $this->response($response, 200);
      }

     function jobs_put() {
          $title = $this->put('title');
          $company_id = $this->put('company_id');
          $type = $this->put('type');
          $posted_by = $this->put('user_id');
          $url = $this->put('url');
          if (!$title or !$company_id or !$type or !$posted_by or !$url) {
            $response = array('success'=> FALSE, 'message'=>'Invalid parameters.');
          } else {
            $this->load->model("jobsmodel","tm",true);
            if ($this->tm->create($title, $company_id, $type, $url, $posted_by)) {
              $response = array('success'=> TRUE);
            } else {
              $response = array('success'=> FALSE);
            }
          }
          $this->response($response, 200);
      }

     function jobs_get() {
          $type = $this->get('type');
          $posted_by = $this->get('posted_by');
          $company_id = $this->get('company_id');
          $id = $this->get('id');
          $this->load->model("jobsmodel","jm",true);
          $response_data = $this->jm->get($type, $posted_by, $company_id, $id);
          if ($response_data) {
            $response = array('success'=> TRUE, 'data'=>$response_data);
          } else {
            $response = array('success'=> FALSE);
          }
          $this->response($response, 200);
      }

     function tags_get() {
          $this->load->model("tagsmodel","tm",true);
          $company_id = $this->get('company_id');
          if($company_id) {
            $response_data = $this->tm->company_tags($company_id);  
          } else {
            $response_data = $this->tm->all();  
          }
          if ($response_data) {
              $response = array('success'=> TRUE, 'data'=>$response_data);
          } else {
            $response = array('success'=> FALSE);
          }
          $this->response($response, 200);
      }

     function user_tag_put() {
          $user_id = $this->put('user_id');
          $tag_id = $this->put('tag_id');
          error_log($user_id);
          error_log($tag_id);
          if (!$user_id or !$tag_id) {
            error_log('No user_id or tag_id');
            $response = array('success'=> FALSE, 'message'=>'Invalid parameters.');
          } else {
            $this->load->model("usertagsmodel","utm",true);
            error_log('Loaded usertagsmodel');
            if ($this->utm->create($user_id, $tag_id)) {
              error_log('Creation success');
              $response = array('success'=> TRUE);
            } else {
              error_log('Creation failure');
              $response = array('success'=> FALSE);
            }
          }
          error_log(json_encode($response));
          $this->response($response, 200);
      }

     function event_tag_put() {
          $event_id = $this->put('event_id');
          $tag_id = $this->put('tag_id');
          error_log($event_id);
          error_log($tag_id);
          if (!$event_id or !$tag_id) {
            error_log('No event_id or tag_id');
            $response = array('success'=> FALSE, 'message'=>'Invalid parameters.');
          } else {
            $this->load->model("eventtagsmodel","utm",true);
            error_log('Loaded eventtagsmodel');
            if ($this->utm->create($event_id, $tag_id)) {
              error_log('Creation success');
              $response = array('success'=> TRUE);
            } else {
              error_log('Creation failure');
              $response = array('success'=> FALSE);
            }
          }
          error_log(json_encode($response));
          $this->response($response, 200);
      }

// clean this up
     function positions_put(){
        $user_id = $this->get('user_id');
        $access_token = $this->get('access_token');
        $this->load->model("positionsmodel","pm",true);
        $url = "https://api.linkedin.com/v1/people/~:(positions)?format=json&oauth2_access_token=".$access_token;
        $profile = json_decode(file_get_contents($url));
        foreach ($profile->positions->values as $value) {
          $company = $value->company;
          $sql = "INSERT INTO company (id, name) VALUES ('$company->id', '$company->name')";
          $this->db->query($sql);
          $this->pm->create($user_id, $company->id);
        }
     }

     function company_tags_get() {
          $company_id = $this->get('company_id');
          if (!$company_id) {
            $response = array('success'=> FALSE, 'message'=>'Invalid parameters.');
          } else {
            $this->load->model("positionsmodel","pm",true);
            $this->load->model("usertagsmodel","utm",true);
            $response_data = array();
            foreach ($this->pm->get($company_id) as $position) {
              $user_id = $position['user_id'];
              $resp = $this->utm->get_tags_with_count($user_id);
              array_push($response_data, $resp);
            }
            $response = array('success'=>TRUE, 'data'=>$response_data);
          }
          $this->response($response, 200);
     }

     function event_tags_get() {
          $event_id = $this->get('event_id');
          if (!$event_id) {
            $response = array('success'=> FALSE, 'message'=>'Invalid parameters.');
          } else {
            $this->load->model("eventtagsmodel","etm",true);
            $response_data = $this->etm->get($event_id);
            $response = array('success'=>TRUE, 'data'=>$response_data);
          }
          $this->response($response, 200);
     }

     function user_tags_get() {
          $user_id = $this->get('user_id');
          if (!$user_id) {
            $response = array('success'=> FALSE, 'message'=>'Invalid parameters.');
          } else {
            $this->load->model("usertagsmodel","utm",true);
            $response_data = $this->utm->get_tags_with_count($user_id);
            $response = array('success'=>TRUE, 'data'=>$response_data);
          }
          $this->response($response, 200);
      }

      function tag_companies_get() {
          $tag_id = $this->get('tag_id');
          if (!$tag_id) {
            $response = array('success'=> FALSE, 'message'=>'Invalid parameters.');
          } else {
            $this->load->model("usertagsmodel","utm",true);
            $response_data = $this->utm->get_companies($tag_id);
            $response = array('success'=>TRUE, 'data'=>$response_data);
          }
          $this->response($response, 200);
      }

     function search_get(){
      $this->benchmark->mark('search_start');
      $q = $this->get('q');
      if (!$q) {
        $response = array('success'=> FALSE, 'message'=>'Invalid parameters.');
      } else {
        $query = mysql_query(sprintf("
                      (select id, first_name as name, 'user' as type from user where first_name like '%%%s%%')
                      union
                      (select id, name, 'company' as type from company where name like '%%%s%%')
                      union
                      (select id, name, 'event' as type from events where name like '%%%s%%')
                      union
                      (select id, title as name, 'job' as type from jobs where title like '%%%s%%')
                      ", $q, $q, $q, $q));
        $response_data = array();
        while($row = mysql_fetch_assoc($query)) {
          array_push($response_data, $row);
        }
        $response = array('success'=>TRUE, 'data'=>$response_data);
      }
      $this->benchmark->mark('search_end');
      error_log('Search Time: '.$this->benchmark->elapsed_time('search_start', 'search_end'));
      $this->response($response, 200);
     }

  function login_post() {
    $this->benchmark->mark('login_start');
    $access_token = $this->post('access_token');
    $id = $this->post('id');
    if (!$access_token or !$id) {
      $response = array('success'=> FALSE, 'message'=>'Invalid parameters.');
    } else {
      $this->load->model("loginmodel","lm",true);
      $response = $this->lm->linkedin($access_token, $id);
    }
    error_log(json_encode($response));
    $this->benchmark->mark('login_end');
    error_log('Login Time: '.$this->benchmark->elapsed_time('login_start', 'login_end'));
    $this->response($response, 200);
  }

// error handling
  function bubbles_get() {
    $this->benchmark->mark('bubbles_start');
    $bubbles = array();
    $query = $this->rest->db->get('bubbles');
    $results = $query->result();
    if (count($results)>0) {
      foreach ($results as $result) {
        $arr = array();
        $arr['name'] = $result->title;
        $arr['image'] = '/grind-members/grind-code/images/bubbles/'.$result->image;
        $arr['rank'] = $result->rank;
        $arr['type'] = $result->type;
        $arr['type_id'] = $result->id;
        array_push($bubbles, $arr);
      }
    }
    $this->benchmark->mark('bubbles_end');
    error_log('Bubbles Time: '.$this->benchmark->elapsed_time('bubbles_start', 'bubbles_end'));
    $this->response($bubbles, 200);
  }

// error handling
  function spaces_get() {
    $this->benchmark->mark('spaces_start');
    $user_id = $this->get('user_id');
    $this->load->model("locationmodel","lm",true);
    $space_data = $this->lm->spaces($user_id);
    $this->benchmark->mark('spaces_end');
    error_log('Spaces Time: '.$this->benchmark->elapsed_time('spaces_start', 'spaces_end'));
    $this->response($space_data, 200);
  }

// error handling
  function members_get() {
    $this->benchmark->mark('members_start');
    $this->load->library('pagination');
    $query = $this->load->model("members/membermodel", "", true);
    $config['base_url'] = site_url('/grind-code/api/members/');
    $config['total_rows'] = $this->membermodel->count_members();
    $config['per_page'] = 200;
    $config['full_tag_open'] = '<div style="display:inline-block" class="navigation">';
    $config['full_tag_close'] = '</div>';
    $config['uri_segment'] = 4;
    $filters = NULL;
    $tag_id = $this->get('tag_id');
    $company_id = $this->get('company_id');
    $user_id = $this->get('user_id');
    $data["users"] = $this->membermodel->new_listing($config['per_page'], $row, $company_id, $user_id, $tag_id);
    $this->pagination->initialize($config);
    $data["pagination"] = $this->pagination->create_links();
    $this->benchmark->mark('members_end');
    error_log('Members Time: '.$this->benchmark->elapsed_time('members_start', 'members_end'));
    $this->response($data, 200);
  }

// error handling
  function companies_get() {
    $this->benchmark->mark('companies_start');
    $this->load->model('members/companymodel','',true);
    $tag_id = $this->get('tag_id');
    $companies = $this->companymodel->listing($tag_id);
    $companies_data = array();
    foreach ($companies as $company) {
      $company = (array)$company;
      $company_data = array(
        'name' => $company['name'],
        'logo_url' => $company['logo_url'],
        'header' => $company['header'],
        'description' => $company['description'],
        'id' => $company['id']
      );
      array_push($companies_data, $company_data);
    }
    $data = array(
      "companies" => $companies_data
    );
    $this->benchmark->mark('companies_end');
    error_log('Companies Time: '.$this->benchmark->elapsed_time('companies_start', 'companies_end'));
    $this->response($data, 200);
  }

  //duplicate
  function company_jobs_get() {
    $id = $this->get('id');
    if (!$id) {
      $response = array('success'=> FALSE, 'message'=>'Invalid parameters.');
    } else {
      $this->load->model('members/companymodel','',true);
      $response = $this->companymodel->get_jobs($id);
    }
    $this->response($response, 200);
  }

  //duplicate
  function company_members_get() {
    $id = $this->get('id');
    if (!$id) {
      $response = array('success'=> FALSE, 'message'=>'Invalid parameters.');
    } else {
      $this->load->model('members/companymodel','',true);
      $response = $this->companymodel->get_members($id);
    }
    $this->response($response, 200);
  }

// error handling
  function profile_put() {
    $id = $this->put('user_id');
    $company_name = $this->put('company_name');
    $title = $this->put('designation');
    $tags_str = $this->put('tags');
    $tags = explode("##", $tags_str);
    $this->load->model('/members/membermodel','mm',true);
    $success = false;
    $success = $this->mm->update_profile_data($id, $company_name, $title, $tags);
    $this->response(array('success' => $success), 200);
  }

// error handling
  function profile_get() {
    $this->benchmark->mark('profile_start');
    $id = $this->get('user_id');
    $this->load->model('/members/membermodel','mm',true);
    $profile = $this->mm->get_profile_data($id);
    $this->benchmark->mark('profile_end');
    error_log('Profile Time: '.$this->benchmark->elapsed_time('profile_start', 'profile_end'));
    $this->response($profile, 200);
  }

  function user_profile_get() {
    $this->benchmark->mark('user_profile_start');
    $id = $this->get('id');
    if (!$id) {
      $response = array('success'=> FALSE, 'message'=>'Invalid parameters.');
    } else {
      $this->load->model('/members/membermodel','mm',true);
      $response = $this->mm->get_profile_data($id);
    }
    $this->benchmark->mark('user_profile_end');
    error_log('User Profile Time: '.$this->benchmark->elapsed_time('user_profile_start', 'user_profile_end'));
    $this->response($response, 200);
  }

  function create_company_post() {
    $name = $this->post('name');
    $description = $this->post('description');

    $config['upload_path'] = './images/companies';
    $config['allowed_types'] = 'gif|jpg|png';
    $config['max_size'] = '100';
    $config['max_width']  = '1024';
    $config['max_height']  = '768';

    $this->load->library('upload', $config);
    $this->upload->do_upload();
    $response = $this->upload->data();
    $logo_url = $response['full_path'];

    $data = array("name"=>$name, "description"=>$description, "logo_url"=>$logo_url);
    $this->db->insert("company", $data);

  }

  function create_main_area_booking_post() {
    error_log('ceating booking');
    $space_id = $this->post('space_id');
    $user_id = $this->post('user_id');
    $resource_id = $this->post('resource_id');
    $from = $this->post('from');
    $to = $this->post('to');
    $this->load->model('locationmodel', 'lm', true);
    $response = $this->lm->book_space($space_id, $user_id, $resource_id, $from, $to);
    if ($response) {
      $this->response(array('success' => true, 'data' => $response), 200);
    } else {
      $this->response(array('success' => false, 'message' => 'Booking error'), 200);
    }
  }

  function create_event_post() {
    $token = "EYFPEMS6IJLSNOXNVH56";
    $url = "https://www.eventbriteapi.com/v3/events/";

    $name = $this->post('name');
    $start_time = $this->post('start_time');
    $end_time = $this->post('end_time');

    // $start_time = "2016-03-13T03:00:00Z";
    // $end_time = "2016-03-13T06:00:00Z";

    $data = array(
          "event.name.html" => $name,
          "event.start.utc" => $start_time,
          "event.start.timezone" => "America/New_York",
          "event.end.utc" => $end_time,
          "event.end.timezone" => "America/New_York",
          "event.currency" => "USD"
        );

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Bearer EYFPEMS6IJLSNOXNVH56"));

    $result = curl_exec($curl);
    curl_close($curl);
    $result = (array)json_decode($result);
    $id = $result['id'];

    $this->load->model('eventsmodel','em',true);
    $this->em->create($id, $name);
    $this->response($result['url'], 200);
  }

  function create_webhook_subscription_post() {
    global $environmentsToAccessToken;
    $event = $this->post('event');
    $callback_url = $this->post('callback_url');
    $subdomain = $this->post('subdomain');
    $environment = $this->post('environment');
    $this->load->model("subscriptionmodel","sm",true);
    $subscription_url = $this->sm->create_webhook_subscription($event, $callback_url, $subdomain, $environmentsToAccessToken[$environment]);
    $this->response($subscription_url, 200);
  }

  function delete_webhook_subscription_post() {
    $subscription_url = $this->post('subscription_url');
    $this->load->model("subscriptionmodel","sm",true);
    $subscription_url = $this->sm->delete_webhook_subscription($subscription_url);
    $this->response($subscription_url, 200);
  }

  function eventbrite_token_get() {
    $response = array("token" => "EYFPEMS6IJLSNOXNVH56", "user.id" => "152621267415");
    $this->response($response, 200);
  }
}
?>