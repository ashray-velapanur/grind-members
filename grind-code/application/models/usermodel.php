<?
include_once APPPATH . 'libraries/utilities.php';
include_once APPPATH . 'libraries/enumerations.php';
include_once APPPATH . 'libraries/constants.php';
include_once APPPATH . 'libraries/recurlyaccess.php';


class UserModel extends CI_Model {
    
    private $userInfo;
	
    function init($id, $type = UserIdType::ID) {
        $retval = false;

        // just get the basic information for the user
        //based on the ID passed in.
        
        $sql = "
            select 
                    user.id as id, user.first_name, user.last_name, user.rfid, user.referrer,user.wp_users_id,
                    wpmember_users.user_login,
                    wpmember_users.user_url,
					email.id as 'email_id',
                    email.address as 'email',
					phone.id as phone_id,
					phone.number as phone,
					company.id as 'company_id',
                    company.name as 'company_name',
					company.description as 'company_description',
					wpmember_users.user_url as website,
					user.twitter,
					user.behance 
            from 
                    user 
                    left outer join wpmember_users on wpmember_users.ID = user.wp_users_id
                    left outer join email on email.user_id = user.id and email.is_primary = 1
                    left outer join phone on phone.user_id = user.id and phone.is_primary = 1
                    left outer join company on company.id = user.company_id
            where
                    
        ";

        // determine which ID we are going to use for the lookup
        //echo "type = $type<br />";
        switch ($type) {
            case UserIdType::ID:
                $sql .= " user.id = $id";
                break;
            case UserIdType::RFID:
                $sql .= " user.rfid = '$id'";
                break;
            case UserIdType::WORDPRESSID:
                $sql .= " user.wp_users_id = '$id'";
                break;
        }
        $query = $this->db->query($sql);
        
        $results = $query->result();
        
        if (count($results) > 0) {
            $this->userInfo = $results[0];
            $retval = $this->userInfo;
            
            //$this->userInfo->recurlydata = "something";
        }
		
		// now get the recurly subscription plan information
		$retval->membership_plan_code = "daily";
		$retval->membership_plan = "Daily";
		$retval->membership_canceled = false;
		$retval->membership_expires_on = "";
		$subscription = RecurlySubscription::getSubscription($retval->id);
		if ($subscription) {
			$retval->membership_plan_code = $subscription->plan->plan_code;
			
			//echo $retval->membership_plan_code;
			
			$retval->membership_plan = $subscription->plan->name;
			$retval->membership_canceled = ($subscription->state == "canceled" ? true : false);
			if ($subscription->expires_at != "") $retval->membership_expires_on = date("m/d/Y", $subscription->expires_at);
			
			/*
			echo "<pre>";
			print_r($subscription);
			echo "</pre>";
			*/
		}

		// and get the recurly billing information
		//$retval->billingInfo = new RecurlyBillingInfo();
		$retval->billingInfo = RecurlyBillingInfo::getBillingInfo($retval->id);
		if (!$retval->billingInfo) {
			$retval->billingInfo = new RecurlyBillingInfo($retval->id);
		}
		
		/*
		echo "user id = " . $retval->id;
		echo "<pre>";
		print_r($retval->billingInfo);
		echo "</pre>";
		*/
		
		//$this->load->model("issuesmodel");
		//$this->issuesmodel->logMemberIssue($id, "just set the membership plan to " . $retval->membership_plan_code);
        
        return $retval;
    }
    
	public function updateBillingFromWordpress($data) {
		$acct = $this->getRecurlyAccount();
		$billing_info = new RecurlyBillingInfo($acct->account_code);
		$billing_info->first_name = $data['billing_info']['first_name'];
		$billing_info->last_name = $data['billing_info']['last_name'];
		$billing_info->address1 = $data['billing_info']['address1'];
        $billing_info->address2 = $data['billing_info']['address2'];
        $billing_info->city = $data['billing_info']['city'];
        $billing_info->state = $data['billing_info']['state'];
        $billing_info->country = $data['billing_info']['country'];
        $billing_info->zip = $data['billing_info']['zip'];
        $billing_info->credit_card->number = $data['credit_card']['number'];
        $billing_info->credit_card->year = intval($data['credit_card']['year']);
        $billing_info->credit_card->month = intval($data['credit_card']['month']);
        $billing_info->credit_card->verification_value = $data['credit_card']['verification_value'];
        $billing_info->ip_address = $_SERVER['REMOTE_ADDR'];
		
		$updated_billing = $billing_info->update();
		return $updated_billing;
	}
	
	public function wpUpdateName($data) {
		$userTableData = array('first_name'=>$data['first_name'],
							   'last_name'=>$data['last_name']);
		$this->db->where("id", $this->userInfo->id);
		$this->db->update("user", $userTableData);
	}
	
	public function wpUpdateWebsite($data) {
		$wpUsersTableData = array('user_url'=>$data['website']);
		$this->db->where("id", $this->userInfo->wp_users_id);
		$this->db->update("wpmember_users", $wpUsersTableData);
	}
	
	public function wpUpdateCompanyName($data) {
		$wpUsersTableData = array('name'=>$data['company_name']);
		$this->db->where("id", $this->userInfo->company_id);
		$this->db->update("company", $wpUsersTableData);
	}
	
	public function wpUpdateCompanyDescription($data) {
		$wpUsersTableData = array('description'=>$data['company_description']);
		$this->db->where("id", $this->userInfo->company_id);
		$this->db->update("company", $wpUsersTableData);
	}
	
	public function wpUpdateEmail($data) {
		$wpUsersTableData = array('address'=>$data['email']);
		$this->db->where("id", $this->userInfo->email_id);
		$this->db->update("email", $wpUsersTableData);
	}
	
    public function id() {
        return $this->userInfo->id;
    }
    
    public function wp() {
        return $this->userInfo->id;
    }
    
    public function addMembership($data) {
    	log_message('debug', "addMembership:".$data);
		$this->db->where("user_id", $this->userInfo->id);
		$this->db->from("membership");
		if ($this->db->count_all_results()>0) {
			$this->updateMembership($data);
		} else {
			log_message('debug', "insertMembership:".$data);
			$this->db->insert("membership", $data);
		}
	}
	
	public function updateMembership($data) {
		log_message('debug', "updateMembership:".$data);
		$this->db->where("user_id", $this->userInfo->id);
		$this->db->update("membership", $data);
	}
	
	public function deleteMembership($id) {
		$this->db->delete('membership', array('user_id' => $id)); 
	}
    
    public function getUsers() {
        $sql = "
		select 
				user.id, user.rfid, user.wp_users_id, user.first_name, user.last_name, user.is_primary_contact,
				company.name as company,
				'[placeholder]' as full_address_w_linebreaks,
				phone_type_lu.description as phone_type, phone.number as phone_number,
				email_type_lu.description as email_type, email.address as email_address,
				vadmin.is_admin as is_admin,
				signin.last_sign_in
		from 
				user 
				left outer join company on company.id = user.company_id
				left outer join address on user.id = address.user_id and address.is_primary = 1
				left outer join phone on phone.user_id = user.id and phone.is_primary = 1
				left outer join email on email.user_id = user.id and email.is_primary = 1
				left outer join state_prov_lu on state_prov_lu.id = address.state_prov_luid
				left outer join country_lu on country_lu.id = address.country_luid
				left outer join phone_type_lu on phone_type_lu.id = phone.phone_type_luid
				left outer join email_type_lu on email_type_lu.id = email.email_type_luid
				left outer join (select user_id, max(sign_in) last_sign_in from signin_sheet group by user_id) signin on signin.user_id = user.id
				inner join v_user_adminstatus vadmin on vadmin.id = user.id
		where
				user.membership_status_luid = 4
				and 1=1
		order by
				user.last_name, user.first_name
		";
		
		$addtlWhereClause = "";
		if(isset($_POST["q"])) {
			$fragments = explode(" ", $_POST["q"]);
				foreach($fragments as $token) {
					$addtlWhereClause .= "user.first_name like '" . $token . "%' or user.last_name like '" . $token . "%' or ";
				}
			$sql = str_replace("1=1", "(" . $addtlWhereClause . " 1=0)", $sql);
		}
		
        $query = $this->db->query($sql);
        $users = $query->result();
		
		foreach ($users as $user) {
			// now get the recurly subscription plan information for each user
			//echo "User ID = " . $user->id . "<br/>";
			$subscription = RecurlySubscription::getSubscription($user->id);
			$planCode = "daily";
			$planName = "Daily";
			if($subscription) {
				$planCode = $subscription->plan->plan_code;
				$planName = $subscription->plan->name;
				/*
				echo "<pre>Subscription info";
				print_r($subscription);
				echo "</pre>";
				*/
			}
			$user->membership_plan_code = $planCode;
			$user->membership_plan = $planName;
		}
		
        return $users;
    }
 
	public function getBasicUsers() {
        $sql = "
		select 
				user.id, user.first_name as 'primary', user.last_name as 'secondary'
		from 
				user 
		where
				user.membership_status_luid = 4
				and 1=1
		order by
				user.last_name, user.first_name
		";

		$addtlWhereClause = "";
		if(isset($_REQUEST["q"])) {
			$fragments = explode(" ", $_REQUEST["q"]);
				foreach($fragments as $token) {
					$addtlWhereClause .= "user.first_name like '" . $token . "%' or user.last_name like '" . $token . "%' or ";
				}
			$sql = str_replace("1=1", "(" . $addtlWhereClause . " 1=0)", $sql);
		}

        $query = $this->db->query($sql);
        $users = $query->result();

        return $users;
    }
    function getRFID() {
        return $this->userInfo->rfid;
    }
    function getBasicInformation() {
        return $this->userInfo;
    }

	//-------------------------------------------------------
	// Data Provider for a users profile data
	// requires: $id = user id
	function getProfileData($id) {
		// Access Database for Member Profile Data
		 $sql = "
	            select 
	                    user.id as id, user.first_name, user.last_name, user.rfid, user.wp_users_id,
	                    wpmember_users.user_login,
	                    wpmember_users.user_url,
						email.id as 'email_id',
	                    email.address as 'email',
						phone.id as phone_id,
						phone.number as phone,
						company.id as 'company_id',
	                    company.name as 'company_name',
						company.description as 'company_description',
						wpmember_users.user_url as website,
						user.twitter,
						user.behance 
	            from 
	                    user 
	                    left outer join wpmember_users on wpmember_users.ID = user.wp_users_id
	                    left outer join email on email.user_id = user.id and email.is_primary = 1
	                    left outer join phone on phone.user_id = user.id and phone.is_primary = 1
	                    left outer join company on company.id = user.company_id
	            where
				
	        ";
		$sql .= " user.id = $id";
		$query = $this->db->query($sql);
		$results = $query->result();
		
		if (count($results) > 0) { 
			return $results[0];  // Return data back
		} else { // no user found with that user id
			return false;
		}
        
    }
	//-------------------------------------------------------
	// Data Provider for a users membership plan
	// requires: $id = user id
	function getPlanData($id) {
		// By default we assume everyone is a daily member
		$data->membership_plan_code = "daily";
		$data->membership_plan = "Daily";
		$data->membership_canceled = false;
		$data->membership_expires_on = "";
		
		// next we use RecurlyAPI to gather information about their subscription
		$subscription = RecurlySubscription::getSubscription($id);
		if ($subscription) { // if subscription data is provided back by Recurly
			$data->membership_plan_code = $subscription->plan->plan_code;	// set the plan code
			$data->membership_plan = $subscription->plan->name;				// set the plan name
			$data->membership_canceled = ($subscription->state == "canceled" ? true : false); // set expiration
			if ($subscription->expires_at != "") $data->membership_expires_on = date("m/d/Y", $subscription->expires_at);
			
		}
		// return data back
		return $data;
	}

	//-------------------------------------------------------
	// Data Provider for a users Billing Account Information
	// requires: $id = user id
	function getRecurlyAccountById($id) {
		// use RecurlyAPI to gather billing information return the data back
		$data = RecurlyAccount::getAccount($id);
        return $data;
    } 

	
	//-------------------------------------------------------
	// Data Provider for a users Billing Information
	// requires: $id = user id
	function getBillingData($id) {
		// use RecurlyAPI to gather billing information return the data back
		$data = RecurlyBillingInfo::getBillingInfo($id);
        return $data;
    }

	//-------------------------------------------------------
	public function createRecurlySubscription($data) {

        $account = $this->getRecurlyAccount();
        if ($account===null)
        {
            $account = new RecurlyAccount($this->userInfo->id);
            $account->username = $this->getPrimaryEmailAddress();
            $account->first_name = $this->userInfo->first_name;
            $account->last_name = $this->userInfo->last_name;
            $account->email = $this->getPrimaryEmailAddress();
            $account->create();
        }

		//Only create subscription if the user is monthly
		if ($data['plan_code'] !== 'daily') {
			$subscription = new RecurlySubscription();
			$subscription->plan_code = $data['plan_code'];
			$subscription->account = $account;
			$subscription->billing_info = new RecurlyBillingInfo($subscription->account->account_code);
			$billing_info = $subscription->billing_info;
			$billing_info->first_name = $account->first_name;
			$billing_info->last_name = $account->last_name;
			$billing_info->address1 = $data['billing_info']['address1'];
			$billing_info->address2 = $data['billing_info']['address2'];
			$billing_info->city = $data['billing_info']['city'];
			$billing_info->state = $data['billing_info']['state'];
			$billing_info->country = $data['billing_info']['country'];
			$billing_info->zip = $data['billing_info']['zip'];
			$billing_info->credit_card->number = $data['credit_card']['number'];
			$billing_info->credit_card->year = intval($data['credit_card']['year']);
			$billing_info->credit_card->month = intval($data['credit_card']['month']);
			$billing_info->credit_card->verification_value = $data['credit_card']['verification_value'];
			$billing_info->ip_address = $_SERVER['REMOTE_ADDR'];
			//Create Recurly Subscription
			$account_info = $subscription->create();
		} else {
		// Still add CC information if a daily user
			$billing_info = new RecurlyBillingInfo($account->account_code);
			$billing_info->first_name = $account->first_name;
			$billing_info->last_name = $account->last_name;
			$billing_info->address1 = $data['billing_info']['address1'];
			$billing_info->address2 = $data['billing_info']['address2'];
			$billing_info->city = $data['billing_info']['city'];
			$billing_info->state = $data['billing_info']['state'];
			$billing_info->country = $data['billing_info']['country'];
			$billing_info->zip = $data['billing_info']['zip'];
			$billing_info->credit_card->number = $data['credit_card']['number'];
			$billing_info->credit_card->year = intval($data['credit_card']['year']);
			$billing_info->credit_card->month = intval($data['credit_card']['month']);
			$billing_info->credit_card->verification_value = $data['credit_card']['verification_value'];
			$billing_info->ip_address = $_SERVER['REMOTE_ADDR'];
			//Create Recurly billing info
			try {
					$account_info = $billing_info->update();
			} catch(Exception $e) {
				$this->load->model("issuesmodel");
				$this->issuesmodel->logMemberIssue(0, "Exception attempting to create billing info: " . $e->getMessage(), MemberIssueType::GENERAL);
				throw new Exception('Exception:'.$e);
			}
		
		}
        return $account;
    }

   	//-------------------------------------------------------
    function getAllUserData() {
        //$this->userInfo;
        
        //return 
    }
    
    function getRecurlyAccount() {
        return RecurlyAccount::getAccount($this->userInfo->id);
    }
    
   
    
    function getRecurlySubscription($id) {
        if ($id == null)
        {
            return  RecurlySubscription::getSubscription($this->userInfo->id);
        } else {
            return  RecurlySubscription::getSubscription($id);
        }
    }
    
    function getAddresses($onlyPrimary = false) {
        return $this->getContactInformation("address", $onlyPrimary);
    }
    
    function getPhones($onlyPrimary = false) {
        return $this->getContactInformation("phone", $onlyPrimary);
    }
    
    function getEmails($onlyPrimary = false) {
        return $this->getContactInformation("email", $onlyPrimary);
    }
    
    function getPrimaryEmailAddress() {
        $emails = $this->getContactInformation("email", true);
        return $emails[0]->address;
    }

    private function getContactInformation($tableName, $onlyPrimary = false) {
        if (isset($this->userInfo)) {
            if ($onlyPrimary) $this->db->where("is_primary", 1);
            $this->db->where("user_id", $this->userInfo->id);
            $query = $this->db->get($tableName);
            return $query->result();
        }
    }
    
    function addAddress($data) {
        $this->db->insert("address", $data);        
    }
    
    function addEmail($data) {
        $this->db->insert("email", $data);        
    }
    
    function addPhone($data) {
        $this->db->insert("phone", $data);        
    }
    
    function updateAddress($data) {
        $this->db->where("id", $data["id"]);
        $this->db->update("address", $data);        
    }
    
    function updateEmail($data) {
        $this->db->where("id", $data["id"]);
        $this->db->update("email", $data);
    }
    
    function updatePhone($data) {
        $this->db->where("id", $data["id"]);
        $this->db->update("phone", $data);
    }
    
    function updateWordpressId($data) {
        $this->db->where("id", $data["id"]);
        $this->db->update("user", $data);
    }
    
    function authenticate() {
        $retval = 0;

        // first check to see if the mac address is already stored
        $mac = $_POST["mac"];
      //  $this->load->model("usermodel");
        $this->load->model("membermodel");
        $this->load->model("issuesmodel");
        $user_id = $this->usermodel->getUserIdFromMACAddress($mac);
        

        if ($user_id) {
            // have the userId, so simply sign them in
            // this piece is useless
            //$retval = $user_id;
        
        } else {
            // mac not cached, so attempt to authenticate
        	
        	//if the username is the mac-test, we know that this is the first run
        	//authentication attempt, if it was second run, it would be their actual login info
        	if($this->input->post("u")!="mac-test"){
        		$username = $this->input->post("u");
           		$password = $this->input->post("p");
           		$retval = user_pass_ok($username, $password);
           		if (!$retval){
        		// LOG an issue to the dashboard 
                	$issueId = $this->issuesmodel->logMemberIssue(0, "Could not authorize \"$username\" with the supplied password.",  MemberIssueType::SIGNIN);
               		$this->issuesmodel->closeMemberIssue($issueId);
               		return false;
                }
           
				
				try {
					$associatedUserId = $this->usermodel->getUserIdFromWPLogin($username);   
					if ($mac != "") {
						$this->usermodel->addMACAddress($associatedUserId, $mac);
					}
					
				} catch(Exception $e) {
					$issueId = $this->issuesmodel->logMemberIssue($associatedUserId, "Exception attempting to store the mac address \"$mac\": " . $e->getMessage(),  MemberIssueType::SIGNIN);
					$this->issuesmodel->closeMemberIssue($issueId);
				}
				
            }
        }
        return $retval;        
    }

    function loginXMLRPC($rpcurl,$username,$password) {
        $params = array($username,$password);
        $request = xmlrpc_encode_request('grind.loginCheck',$params);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_URL, $rpcurl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        $results = curl_exec($ch);
        curl_close($ch);
        return $results;
    }

    function wp_hash_password($password) {
		require_once('../wp-includes/class-phpass.php');
		// By default, use the portable hash from phpass
		$wp_hasher = new PasswordHash(8, true);
    	return $wp_hasher->HashPassword($password);
    }

    function wp_check_password($password, $hash, $user_id = '') {
        global $wp_hasher;
    
        // If the hash is still md5...
        /*
        if ( strlen($hash) <= 32 ) {
            $check = ( $hash == md5($password) );
            if ( $check && $user_id ) {
                // Rehash using new hash.
                wp_set_password($password, $user_id);
                $hash = wp_hash_password($password);
            }
    
            return apply_filters('check_password', $check, $password, $hash, $user_id);
        }
        */
        
        // If the stored hash is longer than an MD5, presume the
        // new style phpass portable hash.
        require_once( '../wp-includes/class-phpass.php');
        // By default, use the portable hash from phpass
        $wp_hasher = new PasswordHash(8, TRUE);
    
        $check = $wp_hasher->CheckPassword($password, $hash);
    
        return apply_filters('check_password', $check, $password, $hash, $user_id);
    }

    public function checkIn($sign_in_method, $currentTime = "") {
        /*
         Logic: If the user is a monthly user, then leave payment to the scheduled
         agreement already set in Recurly.
         If the user is a daily user, then auto charge for the current day.
         If there is an error with the processing, return an error code, otherwise return
         empty string.
        */
        
        $result = "";
        if($currentTime == "") $currentTime = date("Y-m-d H:i:s");

        if($this->userInfo->membership_type == MembershipType::DAILY) {
            // charge for today if they haven't been charged

            // TODO: find the user's current reservation and get the cost from there.
            // If there is not a reservation, check availability.  If availability is
            // good, then get going rate for space.  Otherwise, throw NoAvailableSpace
            // exception.
            $this->makeSinglePayment(19.95);
        }
        
        //echo $this->userInfo->id;
        //echo "The date/time is $currentTime<br />";
        
        $this->db->insert("signin_sheet",
         array(
            "user_id" => $this->userInfo->id, "location_id" => $this->userInfo->location_id,
            "sign_in_method" => $sign_in_method, "sign_in" => $currentTime       
            )
         );
        
        $result = $this->db->insert_id();
        
        return $result;
    }
	
    public function checkIn2($userId, $locationId, $sign_in_method, $currentTime = "") {
        /*
         Logic: If the user is a monthly user, then leave payment to the scheduled
         agreement already set in Recurly.
         If the user is a daily user, then auto charge for the current day.
         If there is an error with the processing, return an error code, otherwise return
         empty string.
        */
        
		$this->load->model("issuesmodel");
        $result = "";
        if($currentTime == "") $currentTime = date(DATE_ISO8601);;

		$um = new UserModel();
		$user = $um->init($userId);

        $daysSinceLastCharge = 1000;
        
		try {
			$account = new RecurlyAccount($userId);
			$charges = $account->listCharges();
			if(count($charges) > 0) {
				//$then = new DateTime(date("Y-m-d H:i:s O", $charges[0]->start_date));
				//$now = new DateTime(date("Y-m-d H:i:s O"));
				//$interval = $now->diff($then);  // doesn't work with php 5.2
				//$daysSinceLastCharge = (int) $interval->format("%a");

				$then = date("Y-m-d H:i:s O", $charges[0]->start_date);
				$now = date("Y-m-d H:i:s O");
				$interval = utilities::date_diff($then, $now);
				$daysSinceLastCharge = $interval;
			}
			//echo "daysSinceLastCharge = $daysSinceLastCharge<br />";
			//$this->issuesmodel->logMemberIssue($userId, "daysSinceLastCharge = $daysSinceLastCharge");
	
			$requirePayment = false;
			//$this->issuesmodel->logMemberIssue($userId, "\$requirePayment = " . $requirePayment);
			//$this->issuesmodel->logMemberIssue($userId, "\$user->membership_plan_code = " . $user->membership_plan_code);
			if($user->membership_plan_code == "daily") {
				// charge for today if they haven't been charged
				$requirePayment = ($daysSinceLastCharge > 1);
			}
			
			//$this->issuesmodel->logMemberIssue($userId, "\$requirePayment = " . $requirePayment);
			if ($requirePayment) {
				// charge for today if they haven't been charged
	
				// TODO: find the user's current reservation and get the cost from there.
				// If there is not a reservation, check availability.  If availability is
				// good, then get going rate for space.  Otherwise, throw NoAvailableSpace
				// exception.
				$query = $this->db->get("configuration");
				$row = $query->row();
				$dailyRate = $row->daily_rate;
				$details = "[CHK-PARK01-1D]: Charging \$dailyRate for " . date("m-d-Y");
				$this->makeSinglePayment($userId, $dailyRate, $details);
			}

		} catch (Exception $e) {
			$issueId = $this->issuesmodel->logMemberIssue($userId, "About to check-in user, but failed attempting to charge them. " . $e->getMessage(), MemberIssueType::BILLING);
			//$this->issuesmodel->closeMemberIssue($issueId);
		}
        //echo $this->userInfo->id;
        //echo "The date/time is $currentTime<br />";
		
		//$this->issuesmodel->logMemberIssue($userId, "About to insert into the signin_sheet");
        $this->db->insert("signin_sheet",
         array(
            "user_id" => $userId, "location_id" => $locationId,
            "sign_in_method" => $sign_in_method, "sign_in" => $currentTime       
            )
         );
		
        $result = $this->db->insert_id();
        
        return $result;
    }
    
    public function makeSinglePayment($userId, $amount, $details = "", $isCredit = false) {
		
		try {		
			//log_message('debug', "About to charge user $userId \$$amount for $details.");
			$account = new RecurlyAccount($userId);
			if (!$isCredit) {
				$charge = $account->chargeAccount($amount, $details);
				if (trim($charge) != "") {
					$issueId = $this->issuesmodel->logMemberIssue($userId, "Charging user [\$$amount for $details]. " . $charge, MemberIssueType::BILLING);
				}
			} else {
				$credit = $account->creditAccount($amount, $details);
				if (trim($credit) != "") {
					$issueId = $this->issuesmodel->logMemberIssue($userId, "Crediting user [\$$amount for $details]. " . $credit, MemberIssueType::BILLING);
				}
			}
			
		} catch (Exception $e) {
			$issueId = $this->issuesmodel->logMemberIssue($userId, "Issue charging user [\$$amount for $details]. " . $e->getMessage(), MemberIssueType::BILLING);
			$this->issuesmodel->closeMemberIssue($issueId);
		}
		//echo "charge = ";
		//print_r($charge);
		//echo "<br />";
    }
    
    public function getUserIdFromMACAddress($mac) {
    	error_log($mac,0);
        $this->db->where("address", strtoupper($mac));
        $query = $this->db->get("macaddress");
        $addresses = $query->result();
        $userId = 0;
        if(count($addresses) > 0) {
            $userId = $addresses[0]->user_id;
        }
        error_log(count($addresses),0);
        error_log($userId,0);
        //echo "(user id from mac address: $userId)\n";
        return $userId;
    }
    
    public function getUserIdFromWPLogin($wpLogin) {
        $sql = "
            select
                user.id
            from
                wpmember_users wpuser
                inner join user on wpuser.id = user.wp_users_id
            where
                wpuser.user_login = ?
        ";
        $query = $this->db->query($sql, array($wpLogin));
        $logins = $query->result();
        $userId = 0;
        if(count($logins) > 0) {
            $userId = $logins[0]->id;
        }
        return $userId;
    }
    
    public function addMACAddress($userId, $mac, $description = "") {
        if ($userId > 0 and $mac != "") {
            $this->db->insert("macaddress", array("user_id" => $userId, "address" => strtoupper($mac), "description" => $description));
        }
    }

    public function updateRecurlySubscription($data) {
		$response = RecurlySubscription::changeSubscription($this->userInfo->id, 'now', $data['plan_code'] );
		var_dump($response);
		exit;
    }
	function updateMembershipStatus($id, $status) {
		$data = array('membership_status_luid'=>$status);
		$this->db->where("id", $id);
		$this->db->update("user", $data);
	}

	
	// addProfile also handles the adding of profile info during
	// a membership invitation
	public function addProfile() {
		$userId = $this->input->post("id");
		$userdata = array();
		$membershipdata = array();
		$companydata = array();
		$phonedata = array();
		$emaildata = array();
		$billingdata = array();
		$wpdata = array();
		
		foreach($_POST as $name => $value) {
			//echo $name . ": " . $value . "<br />";
			
			switch ($name) {
				case "first_name":
				case "last_name":
				case "twitter":
				case "behance":
				case "rfid":
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
				case "billing_last_name":
				case "card_number":
				case "card_type":
				case "card_exp_month":
				case "card_exp_year":
				case "security_code":
				case "billing_address_1":
				case "billing_address_2":
				case "billing_city":
				case "billing_state":
				case "billing_zip_code":					
					$billingdata[$name] = $value;
					break;
				case "primary_email":
					$emaildata["user_id"] = -1;
					$emaildata["address"] = $value;
					$emaildata["is_primary"] = 1;
					$wpdata["user_login"] = $value;
					$wpdata["user_email"] = $value;
					break;
				case "primary_phone":
					$phonedata["user_id"] = -1;
					$phonedata["number"] = $value;
					$phonedata["is_primary"] = 1;
					break;
			}
		}

		$wpdata["user_nicename"] = $this->input->post("first_name") . " " . $this->input->post("last_name");
		$wpdata["display_name"] = $wpdata["user_nicename"];
		$wpdata["user_registered"] = date("Y-m-d H:i:s");

		$userdata["date_added"] = date("Y-m-d H:i:s");
		
		$userdata["membership_status_luid"] = ($this->input->post("invite") == "true" ? 3 : 4);
		
		try {
			// need to run the transactions manually since this also
			// takes into account the web service insert into recurly
			$this->db->trans_begin();
			
            $companyId = 0;
			if ($this->input->post("invite") != "true") {
                $this->db->insert("company", $companydata);		
                $companyId = $this->db->insert_id();
            }

			//$this->db->insert("wpmember_users", $wpdata);
			//$wpId = $this->db->insert_id();
			
			if ($companyId > 0) $userdata["company_id"] = $companyId;
			//$userdata["wp_users_id"] = $wpId;
			$this->db->insert("user", $userdata);
			$userId = $this->db->insert_id();
	
			//$membershipdata["user_id"] = $userId;
			//$this->db->insert("membership", $membershipdata);		
	
			if ($this->input->post("invite") != "true") {
                $phonedata["user_id"] = $userId;
                $this->db->insert("phone", $phonedata);		
            }
            
			$emaildata["user_id"] = $userId;
			$this->db->insert("email", $emaildata);
			
			$account = $this->createRecurlyAccount($userId, $userdata["first_name"], $userdata["last_name"], $emaildata["address"]);

			if ($this->input->post("invite") != "true") {
                if(
                    $billingdata["card_number"] != "" && $billingdata["card_exp_month"] != "" &&
                    $billingdata["card_exp_year"] != "" & $billingdata["security_code"] != ""
                ) {
                    $subscription = $this->createRecurlySubscription2($account, $_POST["membership_plan_code"], $billingdata["billing_first_name"], $billingdata["billing_last_name"], $billingdata["billing_address_1"],
                     $billingdata["billing_address_2"], $billingdata["billing_city"], $billingdata["billing_state"], $billingdata["billing_zip_code"],
                     1, $billingdata["card_number"], $billingdata["card_exp_month"], $billingdata["card_exp_year"], $billingdata["security_code"]);
                }
            }
            
    		$this->load->model("applicantmodel", "", true);
            $idPassAndHash = $this->applicantmodel->createWordpressAccount($emaildata["address"], $userdata["first_name"], $userdata["last_name"]);
            //$wpid = $idPassAndHash["wordpressid"];
            //$tempPassword = $idPassAndHash["temporarypassword"];
            $registerHash = $idPassAndHash["registerHash"];
			//print_r($idPassAndHash);
			
			// zdc 2011/07/05: now update the user table with the wpid
			$this->db->update("user", array("wp_users_id" => $idPassAndHash["wordpressid"]), "id = $userId");
            
            $this->load->model("emailtemplates/emailtemplatemodel", "", true);
            $um = new UserModel();
            $um->init($userId);
			
            $this->emailtemplatemodel->sendApplicationApproval($um, $registerHash, ($this->input->post("invite") == "true"));

			$this->db->trans_commit();

			//echo "inserted user as id $userId<br />";
			
		} catch(Exception $e) {
			$this->load->model("issuesmodel");
			$this->issuesmodel->logMemberIssue(0, "Exception attempting to create account: " . $e->getMessage(), MemberIssueType::GENERAL);
			$this->db->trans_rollback();
		}
	}

	public function updateProfile() {
		$userId = $this->input->post("id");
		$userdata = array();
		$membershipdata = array();
		$companydata = array();
		$phonedata = array();
		$emaildata = array();
		$billingdata = array();

		foreach($_POST as $name => $value) {
			echo $name . ": " . $value . "<br />";
		
			switch ($name) {
				case "first_name":
				case "last_name":
				case "twitter":
				case "behance":
				case "rfid":
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
				case "billing_last_name":
				case "card_number":
				case "card_type":
				case "card_exp_month":
				case "card_exp_year":
				case "security_code":
				case "billing_address_1":
				case "billing_address_2":
				case "billing_city":
				case "billing_state":
				case "billing_zip_code":					
					$billingdata[$name] = $value;
					break;
				case "primary_email":
					$emaildata["address"] = $value;
					$emaildata["is_primary"] = 1;
					$wpdata["user_login"] = $value;
					$wpdata["user_email"] = $value;
					break;
				case "primary_phone":
					$phonedata["number"] = $value;
					$phonedata["is_primary"] = 1;
					break;
			}
		}

		$wpdata["user_nicename"] = $this->input->post("first_name") . " " . $this->input->post("last_name");
		$wpdata["display_name"] = $wpdata["user_nicename"];

		try {
			// need to run the transactions manually since this also
			// takes into account the web service insert into recurly
			$this->db->trans_begin();
			
			// get the wpid and company id from the user table
			$query = $this->db->query("select wp_users_id, company_id from user where id = $userId");
			$data = $query->row();
			//echo "company: " . $data->company_id . ", wp: " . $data->wp_users_id;

			if (isset($data->company_id)) {
				$this->db->update("company", $companydata, "id = " . $data->company_id);		
			} else {
				$this->db->insert("company", $companydata);
				$userdata["company_id"] = $this->db->insert_id();
			}
			
			if (isset($data->wp_users_id)) {
				$this->db->update("wpmember_users", $wpdata, "id = " . $data->wp_users_id);
			} else {
				$this->db->insert("wpmember_users", $wpdata);
				$userdata["wp_users_id"] = $this->db->insert_id();
			}
			
	
			$this->db->update("user", $userdata, "id = $userId");
	
			//$membershipdata["user_id"] = $userId;
			//$this->db->insert("membership", $membershipdata);		
	
			$this->db->update("phone", $phonedata, "user_id = $userId");		
	
			$this->db->update("email", $emaildata, "user_id = $userId");
			
			$account = $this->getRecurlyAccountById($userId);
			
			$subscription = $this->createRecurlySubscription2($account, $_POST["membership_plan_code"], $billingdata["billing_first_name"], $billingdata["billing_last_name"], $billingdata["billing_address_1"],
			 $billingdata["billing_address_2"], $billingdata["billing_city"], $billingdata["billing_state"], $billingdata["billing_zip_code"],
			 1, $billingdata["card_number"], $billingdata["card_exp_month"], $billingdata["card_exp_year"], $billingdata["security_code"]);

			$this->db->trans_commit();

			//echo "updated user id $userId<br />";
		} catch(Exception $e) {
			$this->load->model("issuesmodel");
			$this->issuesmodel->logMemberIssue($userId, "Exception attempting to update account: " . $e->getMessage(), MemberIssueType::GENERAL);
			$this->db->trans_rollback();
		}

	}
	
	public function wpUpdateProfile($data) {
		
		$userTableData = array();
		$companyTableData = array();
		$wpUsersTableData = array();
		
		foreach($_POST as $name => $value) {
			switch ($name) {
				case "first_name":
					$userTableData['first_name'] = $data['first_name'];
					$wpUsersTableData['user_nicename'] = $data['first_name'] . ' ' . $data['last_name'];
					$wpUsersTableData['display_name'] = $data['first_name'] . ' ' . $data['last_name'];
					break;
				case "last_name":
					$userTableData['last_name'] = $data['last_name'];
					$wpUsersTableData['user_nicename'] = $data['first_name'] . ' ' . $data['last_name'];
					$wpUsersTableData['display_name'] = $data['first_name'] . ' ' . $data['last_name'];
					break;
				case "website":
					$wpUsersTableData['user_url'] = $data['website'];
					break;
				/*case "email":
					$emailTableData['address'] => $data['email'];
					$wpUsersTableData['user_email'] => $data['email'];
					//TODO: Save recurly email
					break;
				*/
				case "company_name":
					log_message('error', 'in case statement ' . $data['company_name']);
					$companyTableData['name'] = $data['company_name'];
					break;
				case "company_description":
					$companyTableData['description'] = $data['company_description'];
					break;
				case "twitter":
					$userTableData['twitter'] = $data['twitter'];
					break;
				case "behance":
					$userTableData['behance'] = $data['behance'];
					break;
				default:
					break;
			}
		}
		
		try {
			$this->db->trans_begin();
			if (!empty($userTableData)) {
				$this->db->where("id", $this->userInfo->id);
				$this->db->update("user", $userTableData);
			}
			
			if (!empty($companyTableData)) {
				log_message('error', 'in empty company id = ' . $this->userInfo->company_id);
				$this->db->where("id", $this->userInfo->company_id);
				$this->db->update("company", $companyTableData);
			}
			
			/*
			 if (!empty($emailTableData)) {
				$this->db->where("id", $data["id"]);
				$this->db->update("email", $data);
			}
			*/
			
			if (!empty($wpUsersTableData)) {
				$this->db->where("id", $this->userInfo->wp_users_id);
				$this->db->update("wpmember_users", $wpUsersTableData);
			}
			
			$this->db->trans_commit();
		} catch(Exception $e) {
			//echo "Exception attempting to save profile data from member account screen: " & $e->getMessage();
			log_message('error', 'Exception attempting to save profile data from member account screen: ' . $e->getMessage());
			$this->db->trans_rollback();
			throw new Exception('Exception attempting to save profile data from member account screen: ' . $e->getMessage());
		}
	}
	
    public function createRecurlyAccount($id, $fname, $lname, $email) {

		$account = new RecurlyAccount($id);
		$account->username = $email;
		$account->first_name = $fname;
		$account->last_name = $lname;
		$account->email = $email;
		$account->create();
		
		return $account;
	}
	
	public function createRecurlySubscription2(RecurlyAccount $account, $planCode, $billingFirstName, $billingLastName, $billingAddr1, $billingAddr2 = "",
	 $billingCity, $billingState, $billingZIP, $billingCountry = "US", $ccNumber, $ccExpMonth, $ccExpYear,
	 $ccVerificationValue) {

		$billing_info = new RecurlyBillingInfo($account->account_code);		
        $billing_info->first_name = $billingFirstName;
        $billing_info->last_name = $billingLastName;
        $billing_info->address1 = $billingAddr1;
        $billing_info->address2 = $billingAddr2;
        $billing_info->city = $billingCity;
        $billing_info->state = $billingState;
        $billing_info->country = $billingCountry;
        $billing_info->zip = $billingZIP;
        $billing_info->credit_card->year = intval($ccExpYear);
        $billing_info->credit_card->month = intval($ccExpMonth);
        if (trim($ccVerificationValue) != "") $billing_info->credit_card->verification_value = $ccVerificationValue;
		if (substr($ccNumber, 0, 5) != "*****") $billing_info->credit_card->number = $ccNumber;
		$billing_info->ip_address = $_SERVER['REMOTE_ADDR'];

		$account_info = $billing_info->update();

		$account_info = null;
		
		$currentSubscription = RecurlySubscription::getSubscription($account->account_code);
		$currentPlanCode = "";
		$currentPlanCost = 0;
		$newPlanCost = 0;
		
		if ($planCode != "" && $planCode != "daily") {
			$newPlan = RecurlyPlan::getPlan($planCode);
			$newPlanCost = $newPlan->unit_amount_in_cents;
		}
		
		if ($currentSubscription) {
			$currentPlanCode = $currentSubscription->plan_code;
			$currentPlan = RecurlyPlan::getPlan($currentPlanCode);
			$currentPlanCost = $currentSubscription->unit_amount_in_cents;
		}
		
		if ($planCode == "" || $planCode == "daily") {
			// if the plan is being moved from a monthly plan to daily, then we have to cancel subscription.
			if ($currentSubscription) {
				RecurlySubscription::cancelSubscription($account->account_code);
			}
		} else {
			// if the plan is being moved from one monthly plan to another, then we have to
			// upgrade or downgrade.
			
			if ($currentSubscription) {
				// get the current plan's amount and compare to the new one
				if ($newPlanCost > $currentPlanCost) {
					// upgrade
					RecurlySubscription::changeSubscription($account->account_code, 'now', $planCode, 1);
				} elseif ($newPlanCost < $currentPlanCost) {
					RecurlySubscription::changeSubscription($account->account_code, 'renewal', $planCode, 1);
				}
			} else {
				// no current subscription and we want a monthly, so just add a new one
				$subscription = new RecurlySubscription();
				$subscription->plan_code = $planCode;
				$subscription->account = $account;
				$subscription->billing_info = $billing_info;
				$subscription->create();
			}
		}

        return $account_info;
    }
	
	function getCheckins($userId) {
		$results = null;
        $sql = "
            select
                user.id, user.last_name, user.first_name, company.name as company, signin_sheet.sign_in, location.name as location_name,
                case signin_sheet.sign_in_method
                    when 0 then 'RFID'
                    when 1 then 'Wireless Router'
                    when 2 then 'Admin'
                end as signin_method
            from
                signin_sheet
                left outer join user on signin_sheet.user_id = user.id
                left outer join company on company.id = user.company_id
				left outer join location on location.id = signin_sheet.location_id
            where
                user.id = $userId
            order by
                sign_in desc, sign_out desc
        ";
        
        $query = $this->db->query($sql);
        $results = $query->result();
        $query->free_result();

		return $results;
	}

	function getChargesAndCredits($userId) {
		$retval = array();
		$acct = RecurlyAccount::getAccount($userId);
		if ($acct) {
			$charges = $acct->listCharges();
			$credits = $acct->listCredits();
			/*
			 echo "<pre>";
			print_r($charges);
			echo "</pre>";
			*/
            if ($charges) {
                foreach ($charges as $id => $charge) {
                    $retval[$charge->created_at . "D"]->amount = $charge->amount_in_cents / 100;
                    $retval[$charge->created_at . "D"]->description = $charge->description;
                    $retval[$charge->created_at . "D"]->date = date("m/d/Y H:i:s", $charge->created_at);
                    $retval[$charge->created_at . "D"]->invoice = $charge->invoice_number;
                }
            }
            if ($credits) {
                foreach ($credits as $id => $credit) {
                    $retval[$credit->created_at . "C"]->amount = $credit->amount_in_cents / 100;
                    $retval[$credit->created_at . "C"]->description = $credit->description;
                    $retval[$credit->created_at . "C"]->date = date("m/d/Y H:i:s", $credit->created_at);
                    $retval[$credit->created_at . "C"]->invoice = $credit->invoice_number;
                }
            }
			ksort($retval);
			$revRetval = array_reverse($retval);

		}
		return $revRetval;
	}
	
}


?>