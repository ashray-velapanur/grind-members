<?
include_once APPPATH . 'libraries/utilities.php';
include_once APPPATH . 'libraries/enumerations.php';
include_once APPPATH . 'libraries/constants.php';
include_once APPPATH . 'libraries/recurlyaccess.php';
include_once APPPATH . 'libraries/CheckinApi.php';

require_once (MEMBERINCLUDEPATH.'/wp-admin/includes/user.php');


/**
 * Member Model
 * 
 * Manages tasks associated with data for the members
 * 
 * @joshcampbell
 * @model
 */
 
class MemberModel extends CI_Model {
    
    public $member;
    public $monthlyInfo;
    public $macAddresses;
    
    function __construct()
       {
           parent::__construct();
           $this->load->helper('date');
		   $this->load->helper("money_helper.php");
       }
    
     /**
     * init 
     *
     * initialize a basic member object and data construct
     *
     * @author magicandmight
     * @param user_id = the user whos info you want
     * @param id_type = the type of userid you are passing
     */  

    function init($user_id,$id_type=NULL){
    error_log("Model: member: intializing:".$user_id."/".$id_type,0);
    	$this->member->id = $user_id;
    	$this->member->rfid = "";
    	$this->member->wp_users_id = "";
    	
    	switch ($id_type) {
    	    case UserIdType::ID:
    	        $this->member->id = $user_id;
    	        break;
    	    case UserIdType::RFID:
    	        $this->member->rfid = $user_id;
    	        break;
    	    case UserIdType::WORDPRESSID:
    	        $this->member->wp_users_id = $user_id;
    	        break;
    	}
    	$this->member->first_name ="";
    	$this->member->last_name = "";
       	$this->member->email = "";
    	$this->member->plan_code = "";
    	$this->member->macaddress = "";
    	$this->member->grind_id = "";
    	$this->member->location_id = "";
    }
    
  	 /**
     * get_fullMemberData 
     *
     * retrieves a fill set of data for a member
     * 
     * tests to see if a mac address exists and if it is associated with the
     * current member defined in the member model ($this)
     *
     * @author magicandmight
     * @param user_id = the user whos info you want
     * @param id_type = the type of userid you are passing
     */  

    function get_fullMemberData($user_id,$id_type=NULL){
    $sql = "   
    	select
        user.id as id, user.first_name, user.last_name, user.rfid, user.wp_users_id,user.twitter,user.behance,user.location_id, user.membership_status_luid,
        wpmember_users.user_login,
        wpmember_users.user_url as website,
    	email.id as 'email_id',
        email.address as 'email',
    	phone.id as phone_id,
    	phone.number as phone,
    	company.id as 'company_id',
        company.name as 'company_name',
    	company.description as 'company_description',
    	subscription_sync.plan_code as 'plan_code',
    	subscription_sync.state as 'subscription_state',
    	subscription_sync.total_amount_in_cents as 'total_amount_in_cents',
    	subscription_sync.current_period_ends_at as 'current_period_ends_at'
    	
    	from 
    	        user 
    	        left outer join wpmember_users on wpmember_users.ID = user.wp_users_id
    	        left outer join email on email.user_id = user.id and email.is_primary = 1
    	        left outer join phone on phone.user_id = user.id and phone.is_primary = 1
    	        left outer join company on company.id = user.company_id
    	        left outer join subscription_sync on subscription_sync.user_id = user.id
    	where ";
    	
    	
    	
    	// if we don't know what kind of we have, we assume it is a grind ID
    	$id_type = (!isset($id_type)) ? UserIdType::ID : $id_type;
    	
    	switch ($id_type) {
    	    case UserIdType::ID:
    	        $sql .= " user.id = ".$user_id;
    	        break;
    	    case UserIdType::RFID:
    	        $sql .= " user.rfid = '".$user_id."'";
    	        break;
    	    case UserIdType::WORDPRESSID:
    	        $sql .= " user.wp_users_id = '".$user_id."'";
    	        break;
    	}
    	
    	$query = $this->db->query($sql);
    	$results = $query->result();
    	$this->member = $results[0];
    	
    	    	
    	// need to trap error here
    	$account = Recurly_Account::get($this->member->id);
    	$billing_info = Recurly_BillingInfo::get($account->account_code);
    	$this->member->billing_info = $billing_info;
    	
    	error_log("member_grind_uid".get_user_meta($this->member->wp_users_id,'grind_uid',true),0);
    	$this->member->grind_uid = get_user_meta($this->member->wp_users_id,'grind_uid',true);
    	
    	// returns a member array
    	return $this->member;
    }
    
     /**
     * get_basicMemberData
     * 
     * retrieves basic profile information about a user
     *
     * @author magicandmight
     * @param user_id = the user you want to get data about
     * @param id_type = the type of userid supplied
     */  

    function get_basicMemberData($user_id,$id_type=NULL){
    error_log("Model: Member: get_basicMemberData:".$user_id."/".$id_type,0);
        $sql = "   
    	select
        user.id as id, user.first_name, user.last_name, user.rfid, user.wp_users_id,
        wpmember_users.user_login,
        email.address as 'email',
    	phone.number as phone,
        company.name as 'company_name'

    	from 
    	        user 
    	        left outer join wpmember_users on wpmember_users.ID = user.wp_users_id
    	        left outer join email on email.user_id = user.id and email.is_primary = 1
    	        left outer join phone on phone.user_id = user.id and phone.is_primary = 1
    	        left outer join company on company.id = user.company_id
    	where ";
    	
    	// if we don't know what kind of we have, we assume it is a grind ID
    	$id_type = (!isset($id_type)) ? UserIdType::ID : $id_type;
    	
    	switch ($id_type) {
    	    case UserIdType::ID:
    	        $sql .= " user.id = ".$user_id;
    	        break;
    	    case UserIdType::RFID:
    	        $sql .= " user.rfid = '".$user_id."'";
    	        break;
    	    case UserIdType::WORDPRESSID:
    	        $sql .= "user.wp_users_id = '".$user_id."'";
    	        break;
    	     case UserIdType::WORDPRESSLOGIN:
    	        $sql .= " wpmember_users.user_login = '".$user_id."'";
   		        break;
    	    
    	}

    	$query = $this->db->query($sql);

    	if($query->num_rows() >0){
    		$row = $query->row(); 
    		$this->member = $row;
			
    		// returns a member array
    		return $this->member;
    	} else {
    		error_log("no member",0);
    		return false;
    	}
    }
    
    
    /**
     * Listing
     * 
     * Retrieves a listing of members
     *
     * @author magicandmight
     */  
    function listing($num,$offset=NULL){
    error_log($num . " :: ".$offset,0);
     $sql = "
		select 
				user.id, user.rfid, user.wp_users_id, user.first_name, user.last_name, 
				company.name as company,
				'[placeholder]' as full_address_w_linebreaks,
				phone.number as phone_number,
				email.address as email_address,
				vadmin.is_admin as is_admin,
				subscription_sync.plan_code as plan_code,
				signin.last_sign_in
		from 
				user 
				left outer join company on company.id = user.company_id
				left outer join subscription_sync on subscription_sync.user_id = user.id
				left outer join address on user.id = address.user_id and address.is_primary = 1
				left outer join phone on phone.user_id = user.id and phone.is_primary = 1
				left outer join email on email.user_id = user.id and email.is_primary = 1
				left outer join (select user_id, max(sign_in) last_sign_in from signin_sheet group by user_id) signin on signin.user_id = user.id
				inner join v_user_adminstatus vadmin on vadmin.id = user.id
		where
				user.membership_status_luid = ".MembershipStatus::ACTIVE_MEMBER."
		order by
				user.first_name, user.last_name";
		if (isset($num)) {
				$sql .= " limit ".$num;
		} 
		if (isset($offset)){
				$sql .= " offset ".$offset;
		}
		
        $query = $this->db->query($sql);
        $users = $query->result();
		
        return $users;
    
    }
    
    function count_members(){
    	$this->db->select('id')
    			->from('user')
    			->where('membership_status_luid',MembershipStatus::ACTIVE_MEMBER);
		return $this->db->count_all_results();    
    }
	
	    /**
     * Waitlist
     * 
     * Retrieves a listing of waitlisted members
     *
     * @author magicandmight
     */  
    function waitlist(){
     $sql = "
		select 
				distinct user.id, user.rfid, user.wp_users_id, user.first_name, user.last_name, 
				company.name as company,
				'[placeholder]' as full_address_w_linebreaks,
				phone.number as phone_number,
				email.address as email_address,
				vadmin.is_admin as is_admin,
				signin.last_sign_in
		from 
				user 
				left outer join company on company.id = user.company_id
				left outer join address on user.id = address.user_id and address.is_primary = 1
				left outer join phone on phone.user_id = user.id and phone.is_primary = 1
				left outer join email on email.user_id = user.id and email.is_primary = 1
				left outer join wpmember_users wpu on wpu.id = user.wp_users_id
				left outer join wpmember_usermeta meta on wpu.id = meta.user_id
				left outer join (select user_id, max(sign_in) last_sign_in from signin_sheet group by user_id) signin on signin.user_id = user.id
				inner join v_user_adminstatus vadmin on vadmin.id = user.id
		where
				user.membership_status_luid = ".MembershipStatus::ACTIVE_MEMBER."
		and		meta.meta_key = 'waitlist'
		and		meta.meta_value = '1'
		order by
				user.first_name, user.last_name
		";
				
        $query = $this->db->query($sql);
        $users = $query->result();
		
        return $users;
    
    }
       
     /**
     * viewProfile
     * 
     * Retrieves information related to viewing a member profile
     *
     * @author magicandmight
     * @param user_id = the users profile you want
     * @param id_type = the type of user_id being provided
     */  
    function viewProfile($user_id,$id_type=NULL){
    
    	$this->load->model("billing/planmodel", "", true);
    	$this->load->model("locationmodel", "", true);
    	
    	// MEMBER DATA INFO -------------------------------------
    	$data["member"] = $this->get_fullMemberData($user_id,$id_type);
        
        // FORMAT DATES ------------------------------------------
        $datestring = "%F %j%S";
       	$enddate=mysql_to_unix($data["member"]->current_period_ends_at);
    	$data["end_date"] = mdate($datestring, $enddate);

    	// PRICING INFO -------------------------------------
    	$accesspricing = $this->locationmodel->getAccessPricing();
    	$rate_code = $accesspricing[0]->default_monthly_rate_code;
    	$data["plans"] = $this->planmodel->get_plans(); // fill this with real plans
    	$data["monthly_plan"] = $this->planmodel->local_init($rate_code);
    	foreach($data["plans"] as $plan){
    		$name = $plan->name;
    		if ($plan->plan_code == $data["member"]->plan_code) break;
    	}
		
    	// FORMAT MONEY ------------------------------------------
    	$data["daily_price"] = format_money($accesspricing[0]->daily_rate,false);
    	$data["member"]->total_amount_in_cents=format_money($data["member"]->total_amount_in_cents);
    	$data["monthly_plan"]->unit_amount_in_cents=format_money($data["monthly_plan"]->unit_amount_in_cents); 
    	
    	// MISC VARIABLES ------------------------------------------
    	$data["allow_monthly"]= $accesspricing[0]->allow_monthly_memberships;
    	$data["plan_name"] =$name; // the members current plan name	
    	
		//#213 - Waitlist
		$data["waitlist"]=get_user_meta($data['member']->wp_users_id,'waitlist', true);
		$data["billingData"] = (isset($data["member"]->billing_info)) ? true : false; // the members current plan name				
		
		
    	return $data;
    }
    
     /**
     * editProfile
     * 
     * allows modifications to profile data
     * provide a user_id and an array of data, the first string in the area should always
     * be the name of the profile item you want to edit
     *
     * @author magicandmight
     * @param user_id = the user you are editing
     * @param data = a multidimensional array (item = what you are editing, data is an array of things to change)
     *
     * @note:   on 11/10/11 by @mattkosoy updated this method to store this user profile information on the wordpress side of the app as well as the grind side. 
     */  
    function editProfile($user_id,$data){
    		log_message("debug","editProfileHandler in MemberModel");
    		$retval = false;
    		$this->load->helper('array');
    		$item = $this->input->post("item");
    		$data = $this->input->post("data");
    	
    		switch ($item) {
       		    case "companyNameBlock":
       		    case "companyDescriptionBlock":
       		    	log_message("debug","name/desc handler".$data);
       		    	$name = element('name',$data[0]);
       		    	$value = element('value',$data[0]);
       		    	
       		    	// company table column names are different than what we receive from the form
       		    	$name = ($name == "company_name") ? "name" : "description";
       		    	
       		    	//Company may not be setup yet for a given user
       		    	//if they are…update it, otherwise set it up
       		    	$this->db->select("company_id");
       		    	$result = $this->db->get_where('user',array('id' => $user_id));
       		    	if ($result->num_rows() > 0) // the company has been setup already
       		    	{
       		    		log_message("debug","1");
       		    	   $row = $result->row();
       		    	   if(isset($row->company_id)){ 
       		    	   	 log_message("debug","updating company".$n);
       		    	  	 $sql = "update company c JOIN user u ON c.id = u.company_id SET c.".$name." = '".$value."' WHERE u.id=".$user_id;
       		    	  	 $result = $this->db->query($sql);
       		    	  	} else { 		
       		    	  	log_message("debug","trying to create company");
       		   			// the company has not been setup, create it now
       		    	  	 $this->db->trans_start();
       		    	  	 	$this->db->set($name,$value);
       		    	  	 	$this->db->insert('company');
       		    	  	 	$company_id = $this->db->insert_id();
       		    	   	 	$this->update_profile_field($user_id,'user','company_id',$company_id);
         		    	   $this->db->trans_complete();
       		    	  }
       		    	  
						/* update wp user meta */
						if($item == 'companyNameBlock'){
							update_user_meta($row->wp_users_id, 'company_name', $value);
						} else {
							update_user_meta($row->wp_users_id, 'company_desc', $value);
						}
       		    	  
					}
					
					
       		    	break;
       		    	
    		    case "websiteBlock":
    		    	$name = element('name',$data[0]);
    		    	$value = element('value',$data[0]);
    		    	$sql = "update wpmember_users wp JOIN user u ON wp.id = u.wp_users_id SET wp.user_url = '".$value."' WHERE u.id=".$user_id;
    		        $result = $this->db->query($sql);
    		        /* wp user meta */
					$r = $this->db->get_where('user',array('id' => $user_id));
       		    	if ($r->num_rows() > 0) {
						$row = $r->row();
						update_user_meta($row->wp_users_id, 'URL', $value);
					}
    		    	break;
                case "location_idBlock":
                    $name = element('name',$data[0]);
                    $value = element('value',$data[0]);
                    $sql = "update user SET location_id=$value WHERE id=$user_id";
                    $result = $this->db->query($sql);
                    break;
    		    case "phoneBlock":
    		   		log_message("debug","phone handler");
    		    	// all we care about is phone number
    		    	// future you can expand this to insert additional data points for phone numbers
    		    	// like phone number types, multiple numbers per person etc
    		    	
    		    	$value = element('value',$data[0]);
    		    	
    		    	$this->load->model("/members/phonemodel","",true);
    		    	$phone = $this->phonemodel->init($user_id);
    		    	if($phone){
    		    		if($value == ""){ // if we are sent a blank phone value, we delete the number
    		    			$this->phonemodel->delete();
    		    		} else {
    		    			$phone->number = $value;
    		    			$this->phonemodel->update();
    		    		}
    		    	} else {
    		    		// there is no phone number for the user
    		    		log_message("debug","adding phone number");
    		    		// is_primary,phone_type_luid is always true because we are dealing with 1 number. when more numbers
    		    		// this code will have to change slightly
    		    		$phone_info = array("number"=>$value,"user_id"=>$user_id,"is_primary"=>1,"phone_type_luid"=>0);
    		    		try{
    		    			$phone = $this->phonemodel->create($phone_info);
    		    		} catch (Exception $e) {
    		    			log_message("error",$e);
    		    		}
    		    	}

    		        /* wp user meta */
					$r = $this->db->get_where('user',array('id' => $user_id));
       		    	if ($r->num_rows() > 0) {
						$row = $r->row();
						update_user_meta($row->wp_users_id, 'phone', $value);
					}



    		    break;
    		    case "twitterBlock":
    		    case "behanceBlock":
	    		    $name = element('name',$data[0]);
	    		    $value = element('value',$data[0]);
	    		    $this->update_profile_field($user_id,'user',$name,$value);
	    	 	
	    	 		
						/* wp user meta */
						$r = $this->db->get_where('user',array('id' => $user_id));
						if ($r->num_rows() > 0) {
							$row = $r->row();
							if($item == 'twitterBlock'){ 
								update_user_meta($row->wp_users_id, 'twitter', $value);
							} 
							
							if($item == 'behanceBlock'){
								update_user_meta($row->wp_users_id, 'behance', $value);
							}
						}
	    	 		
	    	 	
	    	 	
	    	 	break;
	    	 	case "rfidBlock":
	    	 		$r = $this->db->get_where('user',array('id' => $user_id));
					if ($r->num_rows() > 0) {
						$row = $r->row();
					
	    	 			$grind_uid = get_user_meta($row->wp_users_id,'grind_uid',true);
		    		    if($grind_uid == "") {
		    		    	$grind_uid = $this->generate_username($user_id);
		    		    	update_user_meta($this->member->wp_users_id, 'grind_uid',$grind_uid );
		    		    }
		    		    		    	 		
		    		    $name = element('name',$data[0]);
		    		    $value = element('value',$data[0]);
		    		    $this->update_profile_field($user_id,'user',$name,$value);
	    	 		}

	    	 	
	    	 	break;
    		    case "fullNameBlock":
					
    		    	foreach($data as $key => $row){
    		    		$field = element('name',$row);
    		    		$value = element('value',$row);
    		    		$this->db->set($field,$value);
    		    		log_message("debug",$field.":".$value);
    		    		$this->member->{$field} = $value;
    		    	}
    		    	$this->db->where('id',$user_id);
    		    	$this->db->update('user');
    		    	
    		    	// update the mailing list with their new details
    		    	$this->load->model("/members/emailmodel","",true);
    		    	$email = $this->emailmodel->init($user_id);
    		    	$this->emailmodel->email_list_update($email->address,$email->address,$this->member->first_name." ".$this->member->last_name);
    		    	
    		    	/* user meta data on the wp side */
						$r = $this->db->get_where('user',array('id' => $user_id));
						if ($r->num_rows() > 0) {
								$row = $r->row();
								update_user_meta($row->wp_users_id, 'first_name', $this->member->first_name);
								update_user_meta($row->wp_users_id, 'last_name', $this->member->last_name);    		    	
						}    		    	
    		    	
    		    	
    		    	break;  
    		    case "emailBlock":
    		    	
    		    	log_message("debug","email address handler");
    		    	// all we care about is email address
    		    	// future you can expand this to insert additional data points for email addresses
    		    	// like email address types, multiple address per person etc

    		    	foreach($data as $key => $row){
    		    		$field = element('name',$row);
    		    		$value = element('value',$row);
    		    		if ($field == "email") break;
    		    	}
    		    	
    		    	$this->load->model("/members/emailmodel","",true);
    		    	$email = $this->emailmodel->init($user_id);
    		    	$old_email = $email->address;
    		    	if($email){
    		    	
    		    		if($value == ""){ // if we are sent a blank email value, we error out the email
    		    			$e = "Can't update email";
    		    			log_message("error",$e);
    		    			return false;
    		    		} else {
    		    			$email->address = $value;
    		    			$this->emailmodel->update();
    		    			$this->emailmodel->email_list_update($old_email,$value);
    		    		}
    		    	} else {
    		    		// there is no email address for the user
    		    		log_message("debug","adding email address");
    		    		// is_primary,email_type_luid is always true because we are dealing with 1 number. when more numbers
    		    		// this code will have to change slightly
    		    		$email_info = array("number"=>$value,"user_id"=>$user_id,"is_primary"=>1,"email_type_luid"=>0);
    		    		try{
    		    			$email = $this->emailmodel->create($email_info);
    		    		} catch (Exception $e) {
    		    			log_message("error",$e);
    		    		}
    		    	}
    		    	
    		    	$r = $this->db->get_where('user',array('id' => $user_id));
					if ($r->num_rows() > 0) {
						$row = $r->row();
						update_user_meta($row->wp_users_id, 'email', $value);
					}
    		    	
    		    	break;  
				case "waitlistBlock" : //#213 - Waitlist
					log_message("debug","inside waitlistBlock");
					$result = $this->db->get_where('user',array('id' => $user_id));
       		    	if ($result->num_rows() > 0)
       		    	{
						$row = $result->row();
						if (element('value',$data[0]) == "1") {
							update_user_meta($row->wp_users_id, 'waitlist', element('value',$data[0]));
							
							$this->member = $this->get_basicMemberData($user_id);
							$this->load->model("emailtemplates/emailtemplatemodel", "", true);
							$email = $this->emailtemplatemodel->init(23);
							$email->message = str_replace("%%first_name%%",$this->member->first_name, $email->message);
							$email->message = str_replace("%%last_name%%",$this->member->last_name, $email->message);
							$this->emailtemplatemodel->send($this->member->email); 
						
						} else {
							delete_user_meta($row->wp_users_id, 'waitlist');
						}
					}
					break;
    		    case "photo":
       		    	//future
      		    	break;  
    		    case "passwordBlock":
    		    	// we actually don't handle passwords here
    		    	// that is managed by a php file in the wp directory
    		    	// grind-update-pw.php
    		    break;
    			}		
   
    }
    
     /**
     * checkin
     * 
     * checks a user into GRIND for the day
     *
     * @author magicandmight
     * @param the member is implied ($this->member) assumes the model has been initiated already with
     *        member data
     * @param location = the physical location we are signing the member into
     * @param signInMethod = the type of sign in, RFID, WIFI, ADMIN
     */      
    function checkin($location_id,$sign_in_method){
    	error_log("check in:".$location_id." ".$sign_in_method,0);
        /*
         Logic: If the user is a monthly user, then leave payment to the scheduled
         agreement already set in Recurly.
         If the user is a daily user, then auto charge for the current day.
         If there is an error with the processing, return an error code, otherwise return
         empty string.
        */
    	$currentTime = date(DATE_ISO8601);
    	error_log("member trying to check in:".$this->member->id);
    	// simplified this query
    	//$sql = "select * from signin_sheet where user_id = ".$this->member->id." AND ".$currentTime." = ".DATE(NOW())." order by sign_in DESC LIMIT 1";
    	$sql = "select * from v_checkins_today where id = ".$this->member->id." order by sign_in DESC LIMIT 1";    	
    	error_log($sql,0);
    	$query = $this->db->query($sql);
    	if ($query->num_rows > 0){
    		
			// in the future if people are abusing the checkin and checking in lots of different people
			// additional logic could be included here to track the number of sign in attempts (because
			// we don't know how the wifi network will behave, it is better to leave this functionality out
			// for the initial release. We also have to worry about the table filling up with entries
			
			// here we know that users have checked into today so we don't need to check them in again
			//$this->db->free_result();
			return true; // user is already checked in today
    	} else {
    	    try{
    	        $account = Recurly_Account::get($this->member->id);
                //check if account has a "chargeable" subscription
                if($subscription = self::getSubscription($this->member->id)){
                    //no charge, signin
                    $result = $this->signin($this->member->id, $location_id, $sign_in_method, $currentTime);
                    error_log("monthly checkin insert id". $result,0);
                    if (!$result){
                        issue_log($this->member->id,"Unable to add user checkin to database",MemberIssueType::SIGNIN);
                    }
                    return $result;
                }else{
                    //no active subscription, so charge daily
                    if ($this->charge_daily($this->member->id,1,$location_id)) {
                        return $this->signin($this->member->id, $location_id, $sign_in_method, $currentTime);
                    }
    
                    error_log("checkin failed",0);
                    return false; // checkin failed
                }
    	    }catch(Recurly_Error $e){
    	        issue_log($this->member->id,"Unable to retrieve the account for user while checking in:",MemberIssueType::SIGNIN);
    	        return false;
    	    }
            
		}
  	}    	
    
    function charge_daily($member_id = null, $member_count = 1,$location_id=false) {
        if ($member_id === null) {
            $member_id = $this->member->id;
        }

        // get the daily rate from the database
        $query = $this->db->get("configuration");
        $row = $query->row();
        $dailyRate = $row->daily_rate;

        // this can be changed to have a dynamic location/space info
        if($location_id){
            $sql = "SELECT * FROM location WHERE id=".$location_id;
            $query = $this->db->query($sql);
            $result = $query->row();
            $chargeMessage = $result->charge_message;
        }else{
            $chargeMessage = "CHK-PA";
        }
        
        $msg = "[{$chargeMessage}_DAILYCHECKIN]: Charging ${$dailyRate} * {$member_count} members  for " . date("m-d-Y");

        $rate = $dailyRate * 100 * $member_count;

        $this->load->model("billing/accountmodel",'account');
        $this->account->init($member_id);
        return $this->account->charge($rate,$msg);
    }


    function signin($user_id, $location_id, $sign_in_method, $sign_in) {
        $this->db->insert("signin_sheet", compact("user_id", "location_id", "sign_in_method", "sign_in"));
        $result = $this->db->insert_id();

        if ($result) {
            $apiResult = CheckinApi::checkin($user_id, $location_id);
            error_log("checkin success:".$result,0);
            return $result;
        }

        error_log("why is failing:".$result,0);
        issue_log($this->member->id,"Unable to add user checkin to database",MemberIssueType::SIGNIN);
        return false;
    }
	
     /**
     * getCheckins
     * 
     * return all (recent) checkins by a user
     *
     * @author magicandmight
     * @param user-id you want data for
     */  
     function getCheckins($user_id=NULL){
     $user_id = (!isset($user_id)) ? $this->member->id : $user_id; 
   	 $results = null;
        $sql = "
            select
                * from v_checkins where
                id = '".$user_id."';";
        
        $query = $this->db->query($sql);
        $results = $query->result();
        $query->free_result();

		return $results;
    }
    
	 /**
     * addMacAddr
     * 
     * adds the mac address to the database. If it already exists, we update
     * the user defined with it
     *
     * @author magicandmight
     * @param macAddr = the mac address you want to add
     */  
    function addMacAddr($macAddr){
    	$this->load->model('members/macaddressmodel','macmodel');
    	$this->macmodel->init($macAddr);
    	if($this->macmodel->exists){

    		// if the macaddress exists already, we are going to update it
    		// to the current members user_id so they "claim" this piece
    		// of hardware as belonging to their account
    		if($this->macmodel->validate($this->member->id)){
    		 // break out, the mac already exists, and it belongs to this member
    		 return true;
    		}
    		$this->macmodel->macaddress->user_id = $this->member->id;
    		$this->macmodel->update();
    	} else {

    		// we've never seen that mac before
    		// we don't pass descriptions (because don't support entering desc in the interface);
    		$this->macmodel->create($macAddr,$this->member->id);
    	}   	
    }
    
     /**
     * checkMacAddr
     * 
     * tests to see if a mac address exists and if it is associated with the
     * current member defined in the member model ($this)
     *
     * @author magicandmight
     * @param macAddr = the mac address you want to check
     */  
 	function checkMacAddr($macAddr){
    	$this->load->model('members/macaddressmodel','macmodel');
    	$this->macmodel->init($macAddr);
    	if ($this->macmodel->exists){
    		if($this->macmodel->validate($this->member->id)){
    		 // break out, the mac already exists, and it belongs to this member
    		 return true;
    		}
    		// the mac exists, but it doesn't belong to this user
    		return false;
    	} else {
    		// the mac doesn't exist
    		return false;
    	}
    }
    
     /**
     * authMacAddr
     * 
     * Authenticates a user by mac address. You pass in a mac address
     * we give you a user back if it is found in the database
     *
     * @author magicandmight 
     * @param macAddr = the mac address you want to authenticate
     */
    function authMacAddr($macAddr){
    	$sql = "select
				user.id as id, user.first_name, user.last_name, user.wp_users_id,
				wpmember_users.user_login from 
				user 
				left outer join macaddress on macaddress.user_id = user.id
				left outer join wpmember_users on wpmember_users.ID = user.wp_users_id  	
				where macaddress.address='".$macAddr."';";
				
    	$query = $this->db->query($sql);
    	if($query->num_rows > 0){
    		$this->member = $query->row();
    		return $this->member;
    	} else {
    		return false;
    	}
    }

    
     /**
     * Reset Password
     * 
     * Integrates the wordpress reset password into the grind member model
     * allows for resetting from the admin console or other locations in the app
     * it only sends the user an auth code in an email, they then have to change 
     * their password. Follows standard wp behavior
     *
     * @author magicandmight
     * @param user_id = the user we are resetting the password for
     */  
     function reset_pw($user_id){
     		global $wpdb;
     		$this->member = $this->get_basicMemberData($user_id);
       		
    		// make sure the person is allowed to reset the pw    	
       		$key = $wpdb->get_var($wpdb->prepare("SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $this->member->email));
       		       		
    		if ( empty($key) ) {
    		//	 Generate something random for a key...
    			$key = wp_generate_password(20, false);
    			do_action('retrieve_password_key', $this->member->email, $key);
    		//	 Now insert the new md5 key into the db
    			$wpdb->update($wpdb->users, array('user_activation_key' => $key), array('user_login' => $this->member->email));
    		}
    		
    		$this->load->model("emailtemplates/emailtemplatemodel", "", true);
    		$resetemail = $this->emailtemplatemodel->init(19);
    		$link = network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($this->member->email), 'login');
    		$resetemail->message = str_replace("%%RESET_LINK%%",$link, $resetemail->message);
    		$resetemail->message = str_replace("%%USER_LOGIN%%",$this->member->email, $resetemail->message);
    		$result = $this->emailtemplatemodel->send($this->member->email);	
    		if($result) {
    			log_message("debug","Password reset sent for: ".$this->member->email);
    			return true;
    		} else {
    			log_message("error","could not reset the password for: ".$this->member->email);
    			return false;
    		}    				
    } 
    
    /**
     * Search
     * 
     * Search method for members, searches on name by default
     *
     * @author magicandmight
     * @param $value = the search term
     */
    function search($value){
     		
     		
     		$sql = "select user.id as 'user_id', user.id as 'id', concat( user.first_name,' ',user.last_name  )as 'name', user.first_name as 'primary', user.last_name as 'secondary', user.rfid as 'rfid',subscription_sync.plan_code as plan_code, email.address as email_address, signin.last_sign_in,company.name as company,phone.number as phone_number,vadmin.is_admin as is_admin
     				from user 
     				left outer join company on company.id = user.company_id
					left outer join subscription_sync on subscription_sync.user_id = user.id
					left outer join address on user.id = address.user_id and address.is_primary = 1
					left outer join phone on phone.user_id = user.id and phone.is_primary = 1
					left outer join email on email.user_id = user.id and email.is_primary = 1
					left outer join (select user_id, max(sign_in) last_sign_in from signin_sheet group by user_id) signin on signin.user_id = user.id
				inner join v_user_adminstatus vadmin on vadmin.id = user.id     				where user.membership_status_luid = ".MembershipStatus::ACTIVE_MEMBER." 
     				AND 1=1 
     				order by user.last_name, user.first_name";
     		
     				$addtlWhereClause = "";
     				
     				if(isset($value)) {
     					$fragments = explode(" ", $value);
     						foreach($fragments as $token) {
								if (strlen($addtlWhereClause)>0) {
									$addtlWhereClause.= " or ";
								}
     							$addtlWhereClause .= "user.first_name like '" . $token . "%' or user.last_name like '" . $token . "%' or user.rfid like '" . $token . "%'";
     						}
     					$sql = str_replace("1=1", "(" . $addtlWhereClause . ")", $sql);
     				}
     		
     		        $query = $this->db->query($sql);

     		        $members = $query->result();
   
    		if($members) {
    			
    			return $members;
    		} else {
 
    			return false;
    		}    				
    } 
    
     /**
     * addMember
     * 
     * Admin function that creates a new member & emails them a hash to setup a pw
     *
     * @author magicandmight
     * @param passed as post variables
     */  
    public function addMember() {
    	$this->load->model("billing/accountmodel","am",true);
    	$this->load->model("members/emailmodel","em",true);
		$userId = $this->input->post("id");
		$userdata = array();
		$membershipdata = array();
		$companydata = array();
		$phonedata = array();
		$emaildata = array();
		$billingdata = new Recurly_BillingInfo();
		$wpdata = array();
		
		foreach($_POST as $name => $value) {
						
			switch ($name) {
				case "first_name":
				case "last_name":
					$userdata[$name] = $value;
					$this->member->{$name} = $value;
					break;
				case "twitter":
				case "behance":
				case "rfid":
                case "location_id":
					$userdata[$name] = $value;
					break;
				case "company":
					$companydata["name"] =
					 $value == "" || $value == null ? $this->input->post("first_name") : $value;
					break;
				case "company_description":
					$companydata["description"] = $value;
					break;
				case "website":
					$wpdata["user_url"] = $value;
					break;
				case "membership_plan_code":
					$membershipdata["membership_plan_code"] = $value;
					$membershipdata["start_date"] = date("Y-m-d");
					break;
				case "billing_first_name":
					$billingdata->first_name = $value;
					break;
				case "billing_last_name":
					$billingdata->last_name = $value;
					break;
				case "billing_address_1":
					$billingdata->address1 = $value;
					break;
				case "billing_address_2":
					$billingdata->address2 = $value;
					break;
				case "billing_city":
					$billingdata->city = $value;
					break;
				case "billing_state":
					$billingdata->state = $value;
					break;
				case "billing_zip_code":
					$billingdata->zip = $value;
					break;
				case "billing_info_country":				
					$billingdata->country = $value;
					break;
				case "card_number":
					$billingdata->number = $value;
					break;
				case "card_exp_month":
					$billingdata->month =  intval($value);
					break;
				case "card_exp_year":
					$billingdata->year = intval($value);
					break;
				case "security_code":
					if (trim($value) != "") $billingdata->verification_value = $value;
					break;
				case "primary_email":
					$emaildata["user_id"] = -1;
					$emaildata["address"] = $value;
					$emaildata["is_primary"] = 1;
					$wpdata["user_login"] = $value;
					$wpdata["user_email"] = $value;
					$this->member->email = $value;
					break;
				case "primary_phone":
					$phonedata["user_id"] = -1;
					$phonedata["number"] = $value;
					$phonedata["is_primary"] = 1;
					break;
			}
		}

        $newUserId = doAddMember($userdata, $membershipdata, $companydata, $phonedata, $emaildata, $billingdata, $wpdata);
        if($newUserId) {
            return true;
        } else {
            return false;
        }
	}

    public function doAddMember($userdata = array(), $membershipdata = array(), $companydata = array(), $phonedata = array(), $emaildata = array(), $billingdata, $wpdata = array()){
        $newUserId = null;
        $userdata["date_added"] = date("Y-m-d H:i:s");
        $userdata["membership_status_luid"] = MembershipStatus::ACTIVE_MEMBER;
        $userdata['terms_agree'] = 1;

        try {
            
            //$this->load->library('utilities');
            $temporaryPassword = $this->get_random_password();
            $registerHash = md5(uniqid('', true));
        
            $result = null;         

            // need to run the transactions manually since this also
            // takes into account the web service insert into recurly
            $this->db->trans_begin();
            
            //first we create the wordpress account
            $wpid = username_exists( $this->member->email );
            if ( !$wpid ) {
                
                $wpid = wp_create_user( $this->member->email, $temporaryPassword,$this->member->email);
                error_log("Wordpress User Create Result=".$wpid);
                
            } else {
                error_log("unable to create wordpress user",0);
                return null;
            }
            if ($wpid){
                error_log("wordpress user exists:".$wpid,0);
                // sets the hash for accessing the registration from an email
                add_user_meta($wpid, 'registerHash', $registerHash , true );
                
                // sets the flag for an admin created user (so they skip member registration)
                add_user_meta($wpid, 'admin_init',true,true);
                $userdata["wp_users_id"] = $wpid;
                
                /* update the new wp user's meta data with the info that's been posted. */
                
                // first name
                update_user_meta($wpid, 'first_name', $this->member->first_name);
                // last name
                update_user_meta($wpid, 'last_name', $this->member->last_name);

                // nice name                
                $noice_name =  str_replace('@', '', $this->member->email);
                $noice_name =  str_replace('.', '-', $noice_name); 
                log_message("debug", 'sanitized nicename = '.$noice_name);
                
                
                $sql = "UPDATE wpmember_users SET user_nicename = '".$noice_name."' WHERE ID = ".$wpid;
                $query = $this->db->query($sql);
               
                log_message("debug","query = ".$sql);
                if($query){
                    log_message("debug", "query to update nicename has been executed successfully");
                }

                // company name
                update_user_meta($wpid, 'company_name', $companydata["name"]);

                // company descrip
                update_user_meta($wpid, 'company_desc', $companydata["description"]);

                // phone
                update_user_meta($wpid, 'phone', $phonedata["number"]);

                // email
                update_user_meta($wpid, 'email', $this->member->email);
                
                // twitter
                update_user_meta($wpid, 'twitter', $userdata['twitter']);
                
                // behance
                update_user_meta($wpid, 'behance', $userdata['behance']);
                
                /* end wp user meta update */
                
            }
            
            error_log('Done wp user creation');
            error_log(count($companydata));
            if(count($companydata)>0) {
                $companyId = 0;
                $this->db->insert("company", $companydata);     
                $companyId = $this->db->insert_id();

                if ($companyId > 0) $userdata["company_id"] = $companyId;
            }

            error_log('Done company creation');

            // now we create the real grind account
            $this->db->insert("user", $userdata);
            $this->member->id = $this->db->insert_id();
            $newUserId = $this->member->id;
            error_log('Done user creation: '.$newUserId);
            
            if(count($phonedata)>0) {
                $phonedata["user_id"] = $this->member->id;
                $this->db->insert("phone", $phonedata);
            }

            error_log('Done phone creation');
            
            //skipping email for now
            // $emaildata["user_id"] = $this->member->id;
            // error_log('Creating email data');
            // $this->em->create($emaildata);
            // error_log('Done email data');
            // $result = $this->em->email_list_add($this->member->first_name . " " . $this->member->last_name);
            // if (!$result){
            //     throw new Exception("Couldn't create the new email list subscription");
            // }

            error_log('Done email creation');
            
            if($billingdata) {
                // leverage the account model to manage the account type and subscription
                $this->am->init($this->member->id);
                $result = $this->am->create($this->member->email,$this->member->first_name,$this->member->last_name,$this->member->email); // username,first,last,email
                if (!$result){
                    throw new Exception("Couldn't create the new member account");
                }
                $billingdata->account_code = $this->member->id;
                $this->am->billing_info = $billingdata;
                $result = $this->am->createSubscription($_POST["membership_plan_code"]);
                if (!$result){
                    throw new Exception("Couldn't create the new recurly subscription for some reason");
                }
            }

            error_log('Done billing creation');

            // skipping email for now
            // $this->load->model("emailtemplates/emailtemplatemodel", "", true);
  
            // $email = $this->emailtemplatemodel->init(20);
            // //Replace variables with our data
        
            // $email->message = str_replace("%%first_name%%",$this->member->first_name, $email->message);
            // $email->message = str_replace("%%last_name%%",$this->member->last_name, $email->message);
            // $email->message = str_replace("%%ID%%",$this->member->email,$email->message);
            // $email->message = str_replace("%%ID2%%",$this->member->email,$email->message);
            // $email->message = str_replace("%%HASH%%",$registerHash,$email->message);
            // $result = $this->emailtemplatemodel->send($this->member->email);                
            // if (!$result){
            //     throw new Exception("Couldn't send email to new member");
            // }
            error_log('Done email template creation');
            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                throw new Exception("Database error, Couldn't Create User");
            }
        
        } catch(Exception $e) {

            error_log('Exception: ');
            error_log($e);
            
            $this->db->trans_rollback(); // transactions don't seem to be working so we are manually deleting the member
            $this->db->delete("user",array('id' => $this->member->id));
            $this->db->delete("phone",array('user_id' => $this->member->id));
            $this->db->delete("company",array('id' => $companyId));
            $this->db->delete('email', array('user_id' => $this->member->id));
                            
            
            wp_delete_user($wpid);
            $this->am->delete();
            $this->em->email_list_remove($this->member->id);
            
            issue_log("0","exception attempting to create account:". $e->getMessage(), MemberIssueType::GENERAL);
            return null;
        }

         $this->db->trans_commit();
         return $newUserId;
    }
    
    public function register(){
    	// assume we have initiated the member object already if not…bail
    	error_log("*****registering a user:".$this->member->id.", ".$this->member->wp_users_id);
    	if(!$this->member->id){
    	    error_log("*****member id not set");
    		return false;
    	}
    	$data = $this->input->post();
  	
  		$this->load->model("members/emailmodel","em",true);
  		$this->em->init($this->member->id);
  		
    	$this->load->model("billing/accountmodel","am",true);
    	$this->am->init($this->member->id);
    	error_log("*****Account model create");
    	$this->am->create($this->member->email,$this->member->first_name,$this->member->last_name,$this->member->email);
    
		$this->am->billing_info->first_name = $data["billing_info"]["first_name"];
		$this->am->billing_info->last_name = $data["billing_info"]["last_name"];	
		$this->am->billing_info->address1 = $data["billing_info"]["address1"];			
		$this->am->billing_info->address2 = $data["billing_info"]["address2"];			
		$this->am->billing_info->city = $data["billing_info"]["city"];
		$this->am->billing_info->state = $data["billing_info"]["state"];
		$this->am->billing_info->zip = $data["billing_info"]["zip"];
		$this->am->billing_info->country = $data["billing_info"]["country"];
		$this->am->billing_info->number = $data["credit_card"]["number"];
		$this->am->billing_info->month =  intval($data["credit_card"]["month"]);
		$this->am->billing_info->year = intval($data["credit_card"]["year"]);
		$this->am->billing_info->verification_value = $data["credit_card"]["verification_value"];
		$data = array('membership_status_luid'=>MembershipStatus::ACTIVE_MEMBER,'terms_agree'=>1);
		$where= array("id" => $this->member->id);
		
		$_SESSION['member'] = $this->member;
	
		
		try {
		    error_log("*****Creating Subscription");
			$result = $this->am->createSubscription("daily");
			if (is_array($result) && $result["error"]){
				//return $result;
				throw new Exception($result["error"].";".$result['message']);
			}
			$result = $this->db->update("user", $data,$where);
			if (!$result){
				throw new Exception("error update database with new member status");
			}
			$_SESSION['membershipsuccess']=true;
			
			delete_user_meta($this->member->wp_users_id,'registerHash');
			delete_user_meta($this->member->wp_users_id,'registerStep');		
			delete_user_meta($this->member->wp_users_id,'admin_init');	
			
			$this->em->email_list_add($this->member->first_name . " " . $this->member->last_name);
			
					
			$user_id_role = new WP_User($this->member->wp_users_id);
			$change_role_result = $user_id_role->set_role('subscriber');
			error_log("changing role:".$change_role_result,0);
						
			
			wp_clear_auth_cookie();
			wp_set_auth_cookie($this->member->wp_users_id);
		
			$wp_user = wp_set_current_user($this->member->wp_users_id, $this->member->email);
			
			
			
			
			/* update wp user meta w/ the info that's stored in our model */
			/* added by @mattkosoy */
			// the fields we're using.
			$fields = array(
				'first_name'	=>	$this->member->first_name,
				'last_name'		=>	$this->member->last_name,
				'email'			=>	$this->member->email,
				'company_name'	=>	$this->member->company_name,
				'company_desc'	=>	'Company Description',
			//	'phone'			=>	'Phone ',
				'URL'			=>	'URL',
				'twitter'		=>  'Twitter URL...',
				'behance'		=>  'Behance URL...',
				'foursquare'	=>	'FourSquare URL...',
				'linkedin'		=>	'LinkedIn URL...',
				'facebook'		=>	'Facebook URL...',
				'dribbble'		=>	'Dribbble URL...',
				'i_need'		=>	''
			);
			
				
			foreach($fields as $k=>$v){
				if(update_user_meta($this->member->wp_users_id,$k, $v)){
			 		log_message("debug","Wordpress update user meta ".$k." for user: ".$this->member->first_name." ".$this->member->last_name." is successful");
			 	}
			}
			
			
			
			
			$notification_message = "Registering new member ".$this->member->first_name . " " . $this->member->last_name;
			issue_log($this->member->id,$notification_message, MemberIssueType::GENERAL);
			$this->load->model("emailtemplates/emailtemplatemodel", "", true);
    		$notification = $this->emailtemplatemodel->init(22);
       		$notification->message = $notification->message." \n" .$notification_message;
    		$result = $this->emailtemplatemodel->send(EMAIL_G_ADMIN);
			return true;
						
		} catch (Exception $e){
			error_log("error creating new subscription for userid:".$this->member->id." message: \n".$e,0);
			
			return $result;
		}   		
    }
    
    public function activate($uid){
    	
    	try{
    		if (isset($uid)){
    			// if uid is passed update the model with for that user
    			$this->get_basicMemberData($uid);
    		
    		} elseif (!$this->member->first_name || !$this->member->last_name) {
    			throw new Exception("No member data supplied, and member not intialized");
    		}
    		$this->db->set(membership_status_luid,MembershipStatus::ACTIVE_MEMBER);
    		$this->db->where('id',$uid);
    		$this->db->update('user');
    		// currently we are not opening or closing the account in recurly
    			
			$this->load->model("members/emailmodel","em",TRUE);
            $this->em->init($uid);
            $this->em->email_list_add($this->member->first_name . " " . $this->member->last_name);
    	
			return true;    		
    		
    	} catch (Exception $e) {
    		error_log('Caught exception Activating member: '. $e->getMessage(), 0);
    		return false;
    	}

    }
    
    
    public function deactivate($uid=NULL){

    	try{
    		if (isset($uid)){
    		// if uid is passed update the model with for that user
    		$this->get_basicMemberData($uid);
    		} elseif (!$this->member->id) {
    			throw new Exception("No member data supplied, and member not intialized");
    		}
    		
    		$this->db->set(membership_status_luid,MembershipStatus::INACTIVE_MEMBER);
    		$this->db->where('id',$this->member->id);
    		$this->db->update('user');
    		// $this->load->model("billing/accountmodel","am",TRUE);  // currently closing an account is done through recurly
    		// $this->am->close($uid); 

    		$this->load->model("members/emailmodel","em",TRUE);
    		$this->em->email_list_remove($this->member->id);
    	
			return true;    		
    		
    	} catch (Exception $e) {
    		error_log('Caught exception deactivating member: '. $e->getMessage(), 0);
    		return false;
    	}

    }


    
    public function delete($uid){
    	try{
    		$this->db->select('wp_users_id');
    		$where = array('id'=>$uid);
    		$query = $this->db->get_where('user',$where);
    		$result = $this->db->last_query();
    		if ($query->num_rows() >0){
    			$row = $query->row(); 
    			$wpid = $row->wp_users_id;
    			wp_delete_user($wpid);
    		}
    		$this->load->model("billing/accountmodel","am",TRUE);
    		$this->load->model("members/emailmodel","em",TRUE);
    		$result .=  "<br />email list: ". $this->em->email_list_remove($uid);
    		$result .= "<br />reculry: ". $this->am->delete($uid);
    		$where = array('user_id'=>$uid);
    		$result .=  "<br />address: ". $this->db->delete("address",$where);
    		$result .=  "<br />email: ". $this->db->delete("email",$where);
    		$result .=  "<br />phone: ". $this->db->delete("phone",$where);
    		$result .=  "<br />signin: ". $this->db->delete("signin_sheet",$where);
    		$result .=  "<br />issues: ". $this->db->delete("issues",$where);
    		$result .=  "<br />macaddress: ". $this->db->delete("macaddress",$where);	
    		$where = array('id'=>$uid);
    		$result .=  "<br />user: ". $this->db->delete("user",$where);
			return true;    		
    		
    	} catch (Exception $e) {
    		error_log('Caught exception: '. $e->getMessage(), 0);
    		return false;
    	}

    }
    
    public function generate_username($user_id){
     	$this->member = $this->get_basicMemberData($user_id);
     	$grind_uid = strtolower(substr($this->member->first_name,0,1) . str_replace(" ","",$this->member->last_name));
    	
     	$testing = false;
     	$i = 1;
     	while (! $testing){
     		if($this->test_username($grind_uid)){
     			//name is not found
				update_user_meta($this->member->wp_users_id, 'grind_uid',$grind_uid);
				$testing = true;
     		} else {
     			$grind_uid = $grind_uid . $i;
     			$i++;
     		} // endif
     	}//endwhile; 
     	return $grind_uid;    	
    }
    
   
    
   private function test_username($str){
    	try{
       		$this->db->select('meta_key');
    		$where = array('meta_key'=>'grind_uid','meta_value'=>$str);
    		$query = $this->db->get_where('wpmember_usermeta',$where);
    		error_log('testing sql'.$this->db->last_query(),0);
	    	if ($query->num_rows() > 0){
	    		return false;
	    	} else {
	    		return true;
	    	}
    	} catch (Exception $e){
    		error_log('Caught exception: '. $e->getMessage(), 0);
    		return false;
    	}
    }
    
    
    
   public function export_members(){
   		$query = $this->db->query('select * from v_export_userdata');
   		return $query->result();	
       }
    
    
    private function update_profile_field($user_id,$table,$field,$value)
    {
    	log_message("debug","userid:".$user_id."/".$field."/".$value);
    	$this->db->set($field,$value);
    	$this->db->where('id',$user_id);
    	$this->db->update($table);
    }
    
    public function get_random_password($chars_min=8, $chars_max=10, $use_upper_case=true, $include_numbers=true, $include_special_chars=true)
    {
        $length = rand($chars_min, $chars_max);
        $selection = 'aeuoyibcdfghjklmnpqrstvwxz';
        if($include_numbers) {
            $selection .= "1234567890";
        }
                                
        $password = "";
        for($i=0; $i<$length; $i++) {
            $current_letter = $use_upper_case ? (rand(0,1) ? strtoupper($selection[(rand() % strlen($selection))]) : $selection[(rand() % strlen($selection))]) : $selection[(rand() % strlen($selection))];            
            $password .=  $current_letter;
        }                
        
        return $password;
    }
    
    public static function getSubscription($userId){
        try{
            $subscriptions = Recurly_SubscriptionList::getForAccount($userId);
            foreach($subscriptions as $subscription){
                if($subscription->state == 'active' || $subscription->state == 'canceled'){
                    return $subscription;
                }
            }
        }catch(Recurly_NotFoundError $e){
            return false;
        }
        
        return false;
    }
}