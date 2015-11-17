<?
include_once APPPATH . 'libraries/enumerations.php';
include_once APPPATH . 'libraries/recurlyaccess.php';

class Sync extends CI_Controller {

	var $notification;
	function __construct() {
		parent::__construct();
		$this->load->helper('date');
	}
	
		
/**
 * inbound
 * 
 * handle inbound sync requests from securely billing and 
 * subscription management system
 *
 * Each notification that we handle has a method attached 
 * to it which we invoke when the inbound request is handled
 * 
 * @joshcampbell
 * 
 */
	public function inbound() {
		log_message("debug","Inbound Recurly notification");
		
		$post_xml = file_get_contents ("php://input");
		$this->notification = new RecurlyPushNotification($post_xml);
		
		log_message("debug","Notification:".$this->notification->type);
		
		// Determine if this is a notification that we handle.
		if(method_exists($this, $this->notification->type)){
			$this->{$this->notification->type}();
			} else {
			log_message("debug","We don't handle ".$this->notification->type);
			// do nothing we don't handle that type of notification
		}
		
	}
	
	/*-------------------------------------------------------------
	* NOTIFICATION HANDLERS - Subscriptions
	* ------------------------------------------------------------*/
	private function new_subscription_notification() {
		log_message("debug","Handling: new subscription notification");
		if ($this->check_sub_exists($this->notification->account->account_code)) {
			log_message("debug","Subscription Exists for user, passing to update");
			$this->updated_subscription_notification();
		}
		else { // this is a brand new record
			$this->db->trans_start();
				  $data = $this->subscription_to_array();
				  
				  $result = $this->db->insert("subscription_sync",$data);
				  // if issues were raised when inserting into the DB Log the issue to the issue list
			      if ($this->db->_error_message()) {
			   		  $this->load->model("issuesmodel");
			   		  $this->issuesmodel->logMemberIssue($data["user_id"], "Error when adding new subscription info to the db from recurly " . $this->db->_error_message(),  MemberIssueType::SYNC);
			   	  } // else no issues	
			   	  log_message("debug","Handling:Complete");	      		      
			$this->db->trans_complete();
		}
			
	} // end new_subscription_notification
	
	private function updated_subscription_notification() {
			log_message("debug","Handling: updated subscription notification");

			$data = $this->subscription_to_array();

			$this->simple_sub_update($data);	
								
			log_message("debug","Handling:Complete");			
		
	}
	private function expired_subscription_notification() {
		log_message("debug","Handling: expired subscription notification");
		
			$data = $this->subscription_to_array();
			
			//set specific data points
			$data["plan_code"]="daily";
			$data["total_amount_in_cents"]=null;
			$data["state"]="active";
			
			$this->simple_sub_update($data);	
			
		log_message("debug","Handling:Complete");
			
	}
	private function canceled_subscription_notification() {
		log_message("debug","Handling: canceling subscription notification");
			
			$data = $this->subscription_to_array();
			$this->simple_sub_update($data);	
							
		log_message("debug","Handling:Complete");
				
	}
	private function renewed_subscription_notification() {
		log_message("debug","Handling: renewed subscription notification");
		
			$data = $this->subscription_to_array();
			$this->simple_sub_update($data);	
							
		log_message("debug","Handling:Complete");
			}
	private function reactivated_account_notification() {
		log_message("debug","Handling: reactivated subscription notification");
		
			$data = $this->subscription_to_array();
			$this->simple_sub_update($data);	
							
		log_message("debug","Handling:Complete");
		
	}
	
/**
 * simple_sub_update() 
 * 
 * helper to perform a simple subscription update
 * many of the functions perform the same action
 * returns boolean
 * input parameter is a data array
 *
 * @joshcampbell
 * @helper
 */	
	private function simple_sub_update($data) {
		log_message("debug","simple update");
		$this->db->trans_start();
			
		if($data)
		{ // we did convert to an array
			$this->db->where('user_id',$data["user_id"]);
			$result=$this->db->update("subscription_sync",$data);
			$this->db->trans_complete();
			
		} else {
			// that isn't working we should log an error
			log_message("debug","the array doesn't exist");
			return false;
		} 	
	}

/**
 * check_sub_exists() 
 * 
 * helper to check if the subscription already exists (could be a daily)
 * returns boolean
 * input parameter is a user id (int)
 *
 * @joshcampbell
 * @helper
 */	
	private function check_sub_exists($id) {
		log_message("debug","check exists");
		$query = $this->db->get_where("subscription_sync",array('user_id' => $id));
		// if there are any rows with this user_id
		$exists = ($query->num_rows() > 0) ? true : false;
		return $exists;	
		}
		
/**
 * subscription_to_array() 
 * 
 * Simple Php function to help convert the variables found in a
 * notification record to our database columns (as an array)
 *
 *
 * @joshcampbell
 * @helper
 */	
	private function subscription_to_array() {
		log_message("debug","setting up data");
		 $data = array();
		 $data["user_id"] = $this->notification->account->account_code; // our user ids match recurly account codes 
		 
		 $subscription = $this->notification->subscription;		      
		 $plan = $subscription->plan;
		
		 $data["plan_code"] = (string) $plan->plan_code;
		 $data["state"] = (string) $subscription->state;
		 $data["quantity"] = (int) $subscription->quantity;
		 $data["total_amount_in_cents"] = $subscription->total_amount_in_cents;
		 $data["activated_at"] = $subscription->activated_at!="" ?  date('Y-m-d H:i:s',$subscription->activated_at) : null;   
		 $data["canceled_at"] = $subscription->canceled_at!="" ?  date('Y-m-d H:i:s',$subscription->canceled_at) : null;
		 $data["expires_at"] = $subscription->expires_at!="" ?  $subscription->expires_at->format('Y-m-d H:i:s') : null;
		 $data["current_period_started_at"] = $subscription->current_period_started_at!="" ?  date('Y-m-d H:i:s',$subscription->current_period_started_at) : null;
		 $data["current_period_ends_at"] = $subscription->current_period_ends_at!="" ? date('Y-m-d H:i:s',$subscription->current_period_ends_at): null;
		 $data["trial_started_at"] = $subscription->trial_started_at!="" ? date('Y-m-d H:i:s',$subscription->trial_started_at) : null;
		 $data["trial_ends_at"] = $subscription->trial_ends_at!="" ? date('Y-m-d H:i:s',$subscription->trial_ends_at) : null;
		 log_message("debug","returning data".$data);
		return $data;
	}

/**
 * ingest
 * 
 * This method provides the ability to grab and insert all of the current recurly data
 * and insert it into the grind database
 * it loops through all of the active users and gets their subscription information
 * it adds it to the table subscription_sync
 *
 * ** WARNING ** 
 * This function is only meant to be used on an empty database. Clear the table first
 * then run this method.
 *
 * @joshcampbell
 * 
 */
 
	public function ingest(){
		$this->load->helper('date');
		$this->db->select('id');
		$query = $this->db->get_where('user',array('membership_status_luid' => 4));
        
		$this->db->trans_start();
		foreach ($query->result() as $row)
		   {
		      try{
		          $account = Recurly_Account::get($row->id);
		      }catch(Exception $e){
		          continue;
		      }
		      
		      $subscriptions = Recurly_SubscriptionList::getForAccount($account->account_code,array('state'=>'active'));
              $subscription = $subscriptions->current();
		      $data = array();
		      $data["user_id"] = $row->id;
		    		      
		      $plan = ($subscription != null && isset($subscription->plan) ? $subscription->plan : null);
		      if(isset($plan)){
			      $data["plan_code"] = $plan->plan_code;
			  
			      $data["state"] = $subscription->state;
			      $data["quantity"] = $subscription->quantity;
			      $data["total_amount_in_cents"] = $subscription->unit_amount_in_cents;
			      $data["activated_at"] = $subscription->activated_at!="" ? $subscription->activated_at->format('Y-m-d H:i:s') : null;   
			      $data["canceled_at"] = $subscription->canceled_at!="" ? $subscription->canceled_at->format('Y-m-d H:i:s') : null;
			      $data["expires_at"] = $subscription->expires_at!="" ? $subscription->expires_at->format('Y-m-d H:i:s') : null;
			      $data["current_period_started_at"] = $subscription->current_period_started_at!="" ? $subscription->current_period_started_at->format('Y-m-d H:i:s') : null;
			      $data["current_period_ends_at"] = $subscription->current_period_ends_at!="" ? $subscription->current_period_ends_at->format('Y-m-d H:i:s') : null;
			      $data["trial_started_at"] = $subscription->trial_started_at!="" ? $subscription->trial_started_at->format('Y-m-d H:i:s') : null;
			      $data["trial_ends_at"] = $subscription->trial_ends_at!="" ? $subscription->trial_ends_at->format('Y-m-d H:i:s') : null;
			  
			  } else {
			  	  $data["plan_code"] = "daily";
			  	  $data["state"] = "active";
			  }
		     	
		      $this->db->insert("subscription_sync",$data);
		   }
		 $this->db->trans_complete();
		
		echo "import complete";	
	}
	
/**
 * sync_plans
 * 
 * This method provides the ability to grab and insert all of the plans from recurly data
 * and insert it into the grind database
 *
 * ** WARNING ** 
 * This function is only meant to be as a cron job to ensure our DB matches recurly plans
 * Remember that the daily plan is managed through the DB specifically
 *
 * @joshcampbell
 * 
 */	
	public function sync_plans(){
		 $this->load->helper('date');
		 $plans = Recurly_PlanList::get();
		
		 $results = array();
		 $i = 0;
		 foreach ($plans as $plan=>$plandata){
		 	unset($plandata->currencies);		// work around for recurly curriences issue
		 	 	
		 	$this->load->model("billing/planmodel","",true);
		 	$plan_code = $plandata->plan_code;
		 	$this->planmodel->local_init($plan_code,$plandata);
			$result = $this->planmodel->create();
			$results[] = array("name" => $plandata->plan_code,"response"=>$result);
			$i=$i++;
		 }
		 $data["results"] = $results;
		 $this->load->view('billing/plan_sync_results',$data);
	}
}
?>