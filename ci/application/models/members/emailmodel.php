<?
include_once APPPATH . 'libraries/recurlyaccess.php';
include_once APPPATH . 'libraries/campaign_monitor.php';
include_once(MEMBERINCLUDEPATH.'/cm/csrest_subscribers.php');

/**
 * email Model
 * 
 * Manages tasks associated with data for the email address table
 * 
 * @joshcampbell
 * @model
 */
 
class EmailModel extends CI_Model {
    
    private $email;
    
    function __construct()
       {
           parent::__construct();
       }
	function init($id)
	{
		$retval = false;
        $sql = "
            select 
                    email.id as id, user_id, is_primary, email_type_luid, address, email_type_lu.short_description
                    from 
                    email
                    left outer join email_type_lu on email_type_lu.id = email_type_luid
            where
            user_id = ".$id;    


        $query = $this->db->query($sql);
        
        $results = $query->result();
        
        if (count($results) > 0) {
            $this->email = $results[0];
            $retval = $this->email;
            
        }	        
        return $retval;
	}
	
	public function update($id=null)
	{
			error_log("are we even updating?",0);
			$this->db->where('user_login', $this->email->address);
			$this->db->from('wpmember_users');
			$count = $this->db->count_all_results();

				if ($count < 1) { //New email is unique
				
					if ($this->email->is_primary){
						//because it is the primary email address
						// we need to change it in GRIND DB, BILLING DB, AND WPDB
							$this->db->trans_begin();
							
							//first update wordpress
							$sql = "update wpmember_users wp JOIN user u ON wp.id = u.wp_users_id SET wp.user_login = '".$this->email->address."', wp.user_email = '".$this->email->address."' WHERE u.id=".$this->email->user_id;
			
							$result = $this->db->query($sql);
							if ($this->db->trans_status() === FALSE)
							{
								error_log("Couldn't update wordpress",0);
							    $this->db->trans_rollback();
							    throw new exception("Couldn't update wordpress");
							}
							
							//now update grind
							$this->db->set("user_id",$this->email->user_id);
							$this->db->set("is_primary",$this->email->is_primary);
							$this->db->set("email_type_luid",$this->email->email_type_luid);
							$this->db->set("address",$this->email->address);
							$this->db->where("id", $this->email->id);
							
							$result = $this->db->update('email');
							error_log("grind failed",0);
					
							if ($this->db->trans_status() === FALSE)
							{
							    $this->db->trans_rollback();
							    throw new exception("Couldn't update grind"); 
							}
							
							//now update recurly
							$account = Recurly_Account::get($this->email->user_id);
							$account->email = $this->email->address;
							$account->username = $this->email->address;
							$result = $account->update();
							if (!$result) {
								$this->db->trans_rollback();
								error_log("recurly failed",0);
								throw new exception("Couldn't update recurly");
								
							}
	
							if ($this->db->trans_status() === FALSE)
							{
							    $this->db->trans_rollback();
							    error_log("something went wrong with email update",0);			
							}
							else
							{
								error_log("email shoud be updated logging email update",0);
							    $this->db->trans_commit();
							}
						
						
					} else {
					
							// not the primary email address so just update it in Grind
							error_log("thinks not primary address",0);	
							$this->db->set("user_id",$this->email->user_id);
							$this->db->set("is_primary",$this->email->is_primary);
							$this->db->set("email_type_luid",$this->email->email_type_luid);
							$this->db->set("address",$this->email->address);
							$this->db->where("id", $this->email->id);
							$result = $this->db->update('email');
					}
									
				} else {
				error_log("thinks email already taken",0);
				throw new exception("Email Address is already taken");
				}
				
	}
	
	public function create($data)
	{
		error_log("EMAILMODEL: create email address:".serialize($data),0);
		$retval = false;
			foreach($data as $key => $value){
				$this->db->set($key,$value);
			}
			$result = $this->db->insert('email');
			if (!$result){
				$msg= "email address creation failed";
				error_log($msg,0);
				throw new exception($msg);
			}
		$retval = $this->init($data["user_id"]);
		//set the return value to a newly minted email object
		
		if(!$retval){
			$msg= "email address creation failed (email init failed with query:".$this->db->last_query();
				error_log($msg,0);
				throw new exception($msg);
		}
		return $retval;
	}
	public function delete()
	{
		log_message("debug","emailMODEL: delete email address");
		if(!$this->email->is_primary){
			$this->db->delete('email', array('id' => $this->email->id));
		} else {
			throw new exception("Sorry you can't delete your primary email address");
		}
		
	}
	
	// we might need to retrieve their old email…not sure we will have it at this point
	public function email_list_update($old_email,$email,$name=NULL){
		$wrap = new CS_REST_Subscribers(CM_MEMBERLIST, CM_APIKEY);
		error_log($old_email,0);
		if(isset($name)){ // assume we are updating name
			$result = $wrap->update($old_email, array(
		    'Name' => $name,
		 	'Resubscribe' => false
			));
		} else { // assume we are updating email address
			$result = $wrap->update($old_email, array(
		    'EmailAddress' => $email,
		 	'Resubscribe' => false
			));

		
		}
		
		error_log("EMAIL LIST UPDATE: Result of POST /api/v3/subscribers/{list id}.{format}\n<br />",0);
		if($result->was_successful()) {
		    error_log("Subscribed with code ".$result->http_status_code,0);
		} else {
		    error_log('Failed with code '.$result->http_status_code."\n<br /><pre>",0);
		    error_log(serialize($result->response),0);
		}

	
	
	
	}// end email list update
	
	public function email_list_add($name){
		error_log("EM: email_list_add",0);
		$wrap = new CS_REST_Subscribers(CM_MEMBERLIST, CM_APIKEY);
		// future (if grind supports multiple emails per person you will need
		// to ensure this is a primary email that you are adding
			$result = $wrap->add(array(		
		    'EmailAddress' => $this->email->address,
		    'Name' => $name,
		    'CustomFields' => array(
		        array(
		            'Key' => 'grind_id',
		            'Value' => $this->email->user_id
		  	      )
		 	   ),
		  	  'Resubscribe' => true
			));	
								
		if($result->was_successful()) {
		    error_log("Subscribed with code ".$result->http_status_code,0);
		    return true;
		} else {
		    error_log('Failed with code '.$result->http_status_code."\n<br /><pre>",0);
		    return false;
		}

	
	
	}// end email list add
	
	public function email_list_remove($uid=NULL){
		$wrap = new CS_REST_Subscribers(CM_MEMBERLIST, CM_APIKEY);
		error_log("email list uid = ".$uid,0);
		// future (if grind supports multiple emails per person you will need
		// to ensure this is a primary email that you are adding
	
		// if we are passing in a userid init their email address and unsub that.
		// otherwise assume we have an email
		if (isset($uid)){
			$this->email = $this->init($uid); 
			error_log("This->email:".serialize($this->email));
		} else if(!isset($this->email->address)){
			// bail we have no email address
			return false;
			exit;
		}
			
		$result = $wrap->delete($this->email->address);
		
		if($result->was_successful()) {
		    error_log("Subscribed with code ".$result->http_status_code,0);
		} else {
		    error_log('Failed with code '.$result->http_status_code."\n<br /><pre>",0);
		}
	
	
	
	}// end email list add

}
?>