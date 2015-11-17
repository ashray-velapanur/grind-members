<?
include_once APPPATH . 'libraries/constants.php';
include_once APPPATH . 'libraries/recurlyaccess.php';
include_once APPPATH . 'libraries/Date_Difference.phps';
/**
 * Account Model
 * 
 * Manages tasks associated with member transactions
 * account is a wrapper to many of the functions from securely.
 * if Grind were to move away from recurly, much of the transactional
 * features for getting details about transactions, invoices etc are 
 * encapsulated here. 
 * <?
include_once APPPATH . 'libraries/constants.php';
include_once APPPATH . 'libraries/recurlyaccess.php';
include_once APPPATH . 'libraries/Date_Difference.phps';
/**
 * Account Model
 * 
 * Manages tasks associated with member transactions
 * account is a wrapper to many of the functions from securely.
 * if Grind were to move away from recurly, much of the transactional
 * features for getting details about transactions, invoices etc are 
 * encapsulated here. 
 * 
 * @joshcampbell
 * @model
 */
 
class AccountModel extends CI_Model {
    
    public $account; 
    public $billing_info;
    
    
    function __construct()
       {
           parent::__construct();
           $this->load->helper('date');
           $this->load->helper('money');
           setlocale(LC_MONETARY, 'en_US');
           
       }
    function init($id=NULL)
    {
        error_log("ACCOUNT MODEL INIT",0);
        // initialize basic parameters
        $this->account->id = (isset($id) ? $id : NULL);
        $this->account->account_code = (isset($id) ? $id : NULL);  // account codes are same as user_id
        $this->account->invoices = "";
        $this->account->pendingcharges = "";
        $this->account->credits = "";
        $this->account->balance = "";
        $this->account->transactions = "";
        $this->account->activity = "";
        try{
            $this->recurlyAccount = new Recurly_Account($this->account->account_code);
        }catch(Exception $e){
            $this->recurlyAccount = new Recurly_Account();
        }
        
        $this->billing_info = new Recurly_BillingInfo();        
         
        return $this;
    }

    public function create($username,$first_name,$last_name,$email){
            $account = new Recurly_Account($this->account->id);
            $account->username = $username;
            $account->first_name = $first_name;
            $account->last_name = $last_name;
            $account->email = $email;
            try{
                Recurly_Account::get($this->account->id);
                error_log("*****Account already exists");
                $this->recurlyAccount = $account;
                $result = true;
            }catch(Recurly_NotFoundError $e){
                $account->create();
                $result = isset($account->created_at);
            } 

            if (!$result) {
                error_log("*****Account not created");
                return false;
            } else {
            error_log("*****Account successfully created");
            $this->recurlyAccount = $account;
            return true;
           } 
    }

    public function updateBillingInfo($data) {
        $this->billing_info->first_name = $data['billing_info']['first_name'];
        $this->billing_info->last_name = $data['billing_info']['last_name'];
        $this->billing_info->address1 = $data['billing_info']['address1'];
        $this->billing_info->address2 = $data['billing_info']['address2'];
        $this->billing_info->city = $data['billing_info']['city'];
        $this->billing_info->state = $data['billing_info']['state'];
        $this->billing_info->country = $data['billing_info']['country'];
        $this->billing_info->zip = $data['billing_info']['zip'];
        $this->billing_info->number = $data['credit_card']['number'];
        $this->billing_info->year = intval($data['credit_card']['year']);
        $this->billing_info->month = intval($data['credit_card']['month']);
        $this->billing_info->verification_value = $data['credit_card']['verification_value'];
        $this->billing_info->ip_address = $_SERVER['REMOTE_ADDR'];
        $this->billing_info->account_code = $this->recurlyAccount->account_code;
        
        if (isset($data)){
            try {
                error_log("updating billing info",0);
                $this->billing_info->update();              
                return $this->billing_info;
            } 
            catch (Recurly_ValidationError $e) {
                $error_message = $e->getMessage();
                $response = array(
                "error" => 'INVALID_CC',
                "message" => $error_message
                );
                return $response;
            }
            catch (Recurly_Error $e) {
                $error_message = "An error occurred while communicating with our payment gateway. Please try again or contact support.";
                $response = array(
                    "error" => 'GATEWAY_ERROR',
                    "message" => $e->getMessage().'....'.$error_message
                );
                return $response;
            } catch(Exception $e) {
                $response = array(
                    "error" => 'GRIND_EXCEPTION',
                    "message" => $e
                );
                return $response;
            }
        } else {
            return false;
        }
    }
    
    public function updateMembership($addsub,$now,$data=NULL){
        error_log("Updating membership.  add a sub:". $addsub.", now:".$now.", ".$data,0 );
        if ($addsub==true){
            try{
                
            if(isset($_POST["current_plan"]) && $_POST["current_plan"] != "daily"){
                $result = $this->changeSubscription($data['plan_code'],$now);
            } else {
                error_log("this should be happening");
                $result = $this->createSubscription($data['plan_code'],true);
            }
            
            $this->load->helper("money_helper.php");
            
            if (isset($result->total_amount_in_cents)){
                $cost = format_money($result->total_amount_in_cents);
            } else {
                $cost = 0;
            }
                $response = array(
                "success" => 1,
                "subscription"=>$result,
                "cost"=>$cost
            );
            } catch (Exception $e){
                error_log($e->getMessage(),0);
                $response = array(
                "success" => 0
            );
                return $response;
            }
        } else {
            // assume we are removing a subscription
            if($subscription = self::getSubscription($this->account->id)){
                if ($now) { //Cancel immediately, give refund
                    $subscription->terminateAndPartialRefund();
                } else { // Cancel at the end of the current period
                    $subscription->terminateWithoutRefund();
                }
                $response = array(
                    "success" => 1
                );
                
            }else{
                $responsen = array(
                    "success" => 0
                );
            }
                    
        }
        return $response;

    }
    
    public function createSubscription($plan_code,$billing_on_file=NULL) {
        error_log("Createsub am called",0);
        if($plan_code != "daily"){
            try {
                error_log("*****attempting to create a subscription",0);
                $subscription = new Recurly_Subscription();
                
                $subscription->plan_code = $plan_code;
                $subscription->currency = "USD";
                $subscription->account = $this->recurlyAccount;
                
                if($billing_on_file){
                    // use the billing info on file…don't change any billing info
                    } else {
                    $subscription->account->billing_info = $this->billing_info;
                    }

                    $data["plan_code"] = $plan_code;
                    $data["user_id"] = $this->account->id;
                    $data["state"] = "active";
                    
                    $query = $this->db->query("select plan_code from subscription_sync where user_id='".$this->account->id."';");
                    if ($query->num_rows() > 0) {
                        $this->db->where('user_id', $this->account->id);
                        error_log("updating subscription_sync",0);
                        $queryresult = $this->db->update('subscription_sync', $data); 
                    } else {
                        error_log("inserting subscription_sync",0);
                        $queryresult = $this->db->insert("subscription_sync",$data);
                    }

                    $subscription->create();
                    $result = isset($subscription->uuid);
                    error_log("*****Recurly subscription successfully created");
                    }
            catch (Recurly_ValidationError $e) {
                error_log("*****".$e->getMessage());
                $this->db->where('user_id', $this->account->id);
                $this->db->delete('subscription_sync');
                
                if ($e->getMessage() === "Subscriptions is invalid. Account Cannot create multiple subscriptions for same account.") {
                    $response = array(
                        "error" => 'ALREADY_HAS_SUB',
                        "message" => $e->getMessage()
                    );
                    return $response;
                
                } else {
                    $response = array(
                        "error" => 'INVALID_CC',
                        "message" => $e->getMessage()
                    );
                    return $response;
                
                }
            }
            catch (Recurly_Error $e) {
                error_log("*****".$e->getMessage());
                $this->db->where('user_id', $this->account->id);
                $this->db->delete('subscription_sync');
                
                $response = array(
                    "error" => 'GATEWAY_ERROR',
                    "message" => $e->getMessage()
                );
                return $response;
        
            }
            catch(Exception $e){
                error_log("*****".$e->getMessage());
                $this->db->where('user_id', $this->account->id);
                $this->db->delete('subscription_sync');
            
                $response = array(
                    "error" => 'GRIND_EXCEPTION',
                    "message" => $e->getMessage()
                );  
                return $response;
            }
        }else { // daily member
            //add billingdata to recurly!!!
            error_log("*****adding a daily member",0);
            $this->recurlyAccount->billing_info = $this->billing_info;
            $this->recurlyAccount->update();
            $data["plan_code"] = $plan_code;
            $data["user_id"] = $this->account->id;
            $data["state"] = "active";
            $result = $this->db->insert("subscription_sync",$data);
        }   
        return $result;
    }

    public function changeSubscription($plan_code,$now) {
        error_log("Change am called",0);
        if($plan_code != "daily"){
            try {
                error_log("attempting to change a subscription plan_code:".$plan_code,0);               
                $timeframe = ($now == 1) ? "now" : "renewal";
                $subscription = self::getSubscription($this->recurlyAccount->account_code);
                $subscription->plan_code = $plan_code;
                if($now == 1){
                    $subscription->updateImmediately();
                }else{
                    $subscription->updateAtRenewal();
                }
                $result = $subscription;
            }
            catch (Recurly_ValidationError $e) {
                error_log("*****".$e->getMessage());
                if ($e->getMessage() === "Subscriptions is invalid. Account Cannot create multiple subscriptions for same account.") {
                    $response = array(
                        "error" => 'ALREADY_HAS_SUB',
                        "message" => $e->getMessage()
                    );
                    return $response;
                
                } else {
                    $response = array(
                        "error" => 'INVALID_CC',
                        "message" => $e->getMessage()
                    );
                    return $response;
                
                }
            }
            catch (Recurly_Error $e) {
                error_log("*****".$e->getMessage());
                $response = array(
                    "error" => 'GATEWAY_ERROR',
                    "message" => $e->getMessage()
                );
                return $response;

            }
            catch(Exception $e)
            {
                error_log("*****".$e->getMessage());
                $response = array(
                    "error" => 'GRIND_EXCEPTION',
                    "message" => $e->getMessage()
                );  
                return $response;
            }
        }
        return $result;
    }
    
    
    public function get_activity()
    {

        $activity = array();
        $this->account->pendingcharges = $this->listPendingCharges();

        $this->account->credits = $this->listCredits();

        if($this->account->pendingcharges){
            foreach ($this->account->pendingcharges as $id => $item) {
                    $this->account->balance = $this->account->balance + $item->total_in_cents;
                    $item_id=$item->created_at->format('Y-m-d H:i:s') . "D"; //D for debit
                    $activity[$item_id] = $item;
              }
        }
        
        if ($this->account->credits) {
            foreach ($this->account->credits as $id => $item) {
                if(!isset($item->invoice)){
                    $this->account->balance = $this->account->balance + $item->total_in_cents;
                    $item_id=$item->created_at->format('Y-m-d H:i:s') . "D"; //D for debit
                    $activity[$item_id] = $item;
                } else{
                    // this charge has been invoiced
                }   
            }
        }
        ksort($activity);
        $activity = array_reverse($activity);
        
        $this->account->activity = $activity;
        return $this->account->activity;        
    }
    
    private function _RecurlyRequest($type){
        // because recurly doesn't expose through their api some of the functions
        // that we would like to utilize…like pending charges, we have simple wrapper here
        // to help us get that data out of securely.
        $user_id = $this->account->id;
        switch ($type) {
        case "transactions":
            $path = Recurly_Client::PATH_TRANSACTIONS;
            $object = 'transaction';
            $exception = "Could not list transactions for account {$user_id}:";
        break;
        case "pendingcharges":
            $path = RecurlyClient::PATH_ACCOUNT_CHARGES . "?show=pending";
            $object = 'charge';
            $exception = "Could not list charges for account {$user_id}:";  
        break;
        case "invoices":
            $path = RecurlyClient::PATH_ACCOUNT_INVOICES;
            $object = 'invoice';
            $exception = "Could not list invoices for account {$user_id}:";
        break;
        case "charges":
            $path = RecurlyClient::PATH_ACCOUNT_CHARGES;
            $object = 'charge';
            $exception = "Could not list charges for account {$user_id}:";  
        break;
        case "credits":
            $path = RecurlyClient::PATH_ACCOUNT_CREDITS;
            $object = 'credit';
            $exception = "Could not list charges for account {$user_id}:";  
        break;
        
        }
        
        $uri = RecurlyClient::PATH_ACCOUNTS . urlencode($this->account->account_code) . $path;
        $result = RecurlyClient::__sendRequest($uri);
        if (preg_match("/^2..$/", $result->code) || RECURLY_ENVIRONMENT=='sandbox') {
            $results = RecurlyClient::__parse_xml($result->response, $object);
            return ($results != null && !is_array($results)) ? array($results) : $results;
        } else if (strpos($result->response, '<errors>') > 0 && $result->code == 422) {
            throw new RecurlyValidationException($result->code, $result->response);
        } else {
            throw new RecurlyException($exception." {$result->response} ({$result->code})");
        }
    }
    
    public function listTransactions()
    {
        $this->account->transactions = $this->_RecurlyRequest('transactions');
        return $this->account->transactions;
    }
    public function listCredits()
    {
        $adjustments = Recurly_AdjustmentList::get($this->account->account_code,array('type'=>'credit'));
        $credits = array();
        foreach($adjustments as $credit){
            $credits[] = $credit;
        }
        $this->account->credits = $credits;
        return $this->account->credits;
    }
    
    public function listCharges()
    {
        $this->account->charges = $this->_RecurlyRequest('charges');
        return $this->account->charges;
    }
    
    public function listPendingCharges()
    {
        $adjustments = Recurly_AdjustmentList::get($this->account->account_code,array('type'=>'charge','state'=>'pending'));
        $pendingcharges = array();
        foreach($adjustments as $pendingcharge){
            $pendingcharges[] = $pendingcharge;
        }
        $this->account->pendingcharges = $pendingcharges;
        return $this->account->pendingcharges;
    }

    
    public function listInvoices()
    {
        $_invoices = Recurly_InvoiceList::getForAccount($this->account->account_code);
        $invoices = array();
        foreach($_invoices as $invoice){
            $invoices[] = $invoice;
        }
        $this->account->invoices = $invoices;
        return $this->account->invoices;
    }
    
    // --------------------------------------------------------------------------
    public function delete($account_code=NULL){
        if (isset($account_code)){ // set the account code without initializing the model
            $this->recurlyAccount = Recurly_Account::get($account_code);
        }
        try {
            $this->recurlyAccount->close();
        } catch (RecurlyException $e){
            error_log("could not delete recurly account: ".$this->account->account_code,0);
        }
    }
    

        // --------------------------------------------------------------------------       
    public function charge($amount, $details = ""){ 
        $dollars = format_money($amount,true);
        try {       
            error_log("About to charge user $dollars for $details.",0);
            
            $charge = new Recurly_Adjustment();
            $charge->account_code = $this->recurlyAccount->account_code;
            $charge->currency = "USD";
            $charge->unit_amount_in_cents = $amount;
            $charge->description = $details;
            $charge->create();
            
            if  ($charge->uuid) {
                return true;
            } else {
                throw new Exception('transaction failed');
            }

        } catch (Exception $e) {
            issue_log($this->account->account_code,"Issue charging user [$dollars for $details]. " . $e->getMessage(), MemberIssueType::BILLING);
        }
    } // end charge
    
    // --------------------------------------------------------------------------

    public function credit($amount, $details = ""){
        $dollars = format_money($amount,true);      
        try {       
            error_log("About to credit user $dollars for $details.",0);
            $credit = new Recurly_Adjustment();
            $credit->account_code = $this->recurlyAccount->account_code;
            $credit->currency = "USD";
            $credit->unit_amount_in_cents = -$amount;
            $credit->description = $details;
            $credit->create();
            
            if  ($credit->uuid) {
                return true;
            } else {
                throw new Exception('transaction failed');
            }

        } catch (Exception $e) {
            issue_log($this->account->account_code,"Issue crediting user [$dollars for $details]. " . $e->getMessage(), MemberIssueType::BILLING);
        }
    } // end charge

    // --------------------------------------------------------------------------
    
/**
 * getDetails
 * 
 * provides a lookup function for details on a members plan. Currently it
 * pulls out and sends information to start and end date, cost and state 
 * 
 * @author magicandmight
 * @param none
 */     
    public function getDetails(){
        $this->load->helper('date');
        $this->load->helper("money_helper.php");
        if($subscription = self::getSubscription($this->account->account_code)){
            $response = array();
            $response["state"] = $subscription->state;
            $response["quantity"] = $subscription->quantity;
            $response["cost"] = format_money($subscription->total_amount_in_cents);     
            $datestring = "%F %j%S";
            $response["trial_end"] = mdate($datestring, $subscription->current_period_started_at);
            $response["end_date"] = mdate($datestring, $subscription->current_period_ends_at);
            $response["start_date"] = mdate($datestring, $subscription->current_period_started_at);
        }else{
            $response = array();
        }
        
        return $response;
    }
    
    public static function getSubscription($accountId){
        try{
            $subscriptions = Recurly_SubscriptionList::getForAccount($accountId);
            foreach($subscriptions as $subscription){
                if($subscription->state == 'active'){
                    return $subscription;
                }
            }
        }catch(Recurly_NotFoundError $e){
            return false;
        }
        
        return false;
    }
/**
 * getPlanType
 * 
 * provides a lookup function for type of members. Currently it
 * determines monthly, daily, collaborator, etc
 * 
 * @author magicandmight
 * @param user_id
 */     
    function getPlanType($user_id){
        $query = $this->db->get_where("subscription_sync",array("user_id"=>$user_id),1);
        $result = $query->row();
        error_log("ACCOUNT_MODEL: GET PLAN TYPE: plan code: ".$result->plan_code,0);
        return $result->plan_code;
    }
}


?>
 * @joshcampbell
 * @model
 */
 
class AccountModel extends CI_Model {
    
    public $account; 
    public $billing_info;
    
    
    function __construct()
       {
           parent::__construct();
           $this->load->helper('date');
		   setlocale(LC_MONETARY, 'en_US');
           
       }
	function init($id=NULL)
	{
		error_log("ACCOUNT MODEL INIT",0);
		// initialize basic parameters
		$this->account->id = (isset($id) ? $id : NULL);
		$this->account->account_code = (isset($id) ? $id : NULL);  // account codes are same as user_id
		$this->account->invoices = "";
		$this->account->pendingcharges = "";
		$this->account->credits = "";
		$this->account->balance = "";
		$this->account->transactions = "";
		$this->account->activity = "";
		$this->recurlyAccount = new RecurlyAccount($this->account->account_code);
		$this->billing_info = new RecurlyBillingInfo($this->account->account_code);		
		 
		return $this;
	}

	public function create($username,$first_name,$last_name,$email){
			$account = new RecurlyAccount($this->account->id);
            $account->username = $username;
            $account->first_name = $first_name;
            $account->last_name = $last_name;
            $account->email = $email;
            $checkExists =$account->getAccount($this->account->id);
            error_log(serialize($checkExists),0);
	            if ($checkExists) {
	            	$this->recurlyAccount = $account;
	            	$result = true;
	            } else {
            		$result = $account->create();
            	} 
            if (!$result) {
     			return false;
            } else {
            
           	$this->recurlyAccount = $account;
           	return true;
           } 
	}

	public function updateBillingInfo($data) {
	
		$this->billing_info->first_name = $data['billing_info']['first_name'];
		$this->billing_info->last_name = $data['billing_info']['last_name'];
		$this->billing_info->address1 = $data['billing_info']['address1'];
        $this->billing_info->address2 = $data['billing_info']['address2'];
        $this->billing_info->city = $data['billing_info']['city'];
        $this->billing_info->state = $data['billing_info']['state'];
        $this->billing_info->country = $data['billing_info']['country'];
        $this->billing_info->zip = $data['billing_info']['zip'];
        $this->billing_info->credit_card->number = $data['credit_card']['number'];
        $this->billing_info->credit_card->year = intval($data['credit_card']['year']);
        $this->billing_info->credit_card->month = intval($data['credit_card']['month']);
        $this->billing_info->credit_card->verification_value = $data['credit_card']['verification_value'];
        $this->billing_info->ip_address = $_SERVER['REMOTE_ADDR'];
	
		if (isset($data)){
			try {
				error_log("updating billing info",0);
				$result = $this->billing_info->update();
				    switch ($result->credit_card->type) {
				    	case "visa":
				    		$cc_name = CreditCardNames::CC_VISA;
				    	break;
				    	case "discover":
				    		$cc_name = CreditCardNames::CC_DISCOVER;
				    	break;
				    	case "american_express":
				    		$cc_name = CreditCardNames::CC_AMEX;
				    	break;
				    	case "mastercard":
				    		$cc_name = CreditCardNames::CC_MC;
				    	break;
					}
				$result->cc_name = $cc_name;				
				return $result;
			} 
			catch (RecurlyValidationException $e) {
				$error_message = $e->getMessage();
				$response = array(
				"error" => 'INVALID_CC',
				"message" => $error_message
				);
				return $response;
       		}
       		catch (RecurlyException $e) {
	   		   	$error_message = "An error occurred while communicating with our payment gateway. Please try again or contact support.";
	   		   	$response = array(
	   		   		"error" => 'GATEWAY_ERROR',
					"message" => $error_message
	   		   	);
	   		   	return $response;
       		} catch(Exception $e) {
	   		   	$response = array(
	   		   		"error" => 'GRIND_EXCEPTION',
					"message" => $e
	   		   	);
	   		   	return $response;
	   		}
		} else {
			return false;
		}
	}
	
	public function updateMembership($addsub,$now,$data=NULL){
		error_log("add a sub:". $addsub.", now:".$now.", ".$data,0 );
		if ($addsub==true){
			try{
				
			if(isset($_POST["current_plan"]) && $_POST["current_plan"] != "daily"){
				$result = $this->changeSubscription($data['plan_code'],$now);
			} else {
				$result = $this->createSubscription($data['plan_code'],true);
			}
			
			$this->load->helper("money_helper.php");
			
			if (isset($result->total_amount_in_cents)){
				$cost = format_money($result->total_amount_in_cents);
			} else {
				$cost = 0;
			}
				$response = array(
				"success" => 1,
				"subscription"=>$result,
				"cost"=>$cost
			);
			} catch (exception $e){
				error_log($e->getMessage(),0);
				$response = array(
				"success" => 0
			);
				return $response;
			}
		} else {
			// assume we are removing a subscription
			if ($now) { //Cancel immediately, give refund
				RecurlySubscription::refundSubscription($this->account->id, 'partial');
			} else { // Cancel at the end of the current period
				RecurlySubscription::cancelSubscription($this->account->id);
			}
			$response = array(
				"success" => 1
			);
						
		}
		return $response;

	}
	
	public function createSubscription($plan_code,$billing_on_file=NULL) {
		error_log("Createsub am called",0);
		if($plan_code != "daily"){
			try {
				error_log("attempting to create a subscription",0);
				$subscription = new RecurlySubscription();
				
				$subscription->plan_code = $plan_code;
				$subscription->account = $this->recurlyAccount;
				
				if($billing_on_file){
					// use the billing info on file…don't change any billing info
					} else {
					$subscription->billing_info = $this->billing_info;
					}

					$data["plan_code"] = $plan_code;
					$data["user_id"] = $this->account->id;
					$data["state"] = "active";
		   			
		   			$query = $this->db->query("select plan_code from subscription_sync where user_id='".$this->account->id."';");
					if ($query->num_rows() > 0) {
						$this->db->where('user_id', $this->account->id);
						$queryresult = $this->db->update('subscription_sync', $data); 
					} else {
						$queryresult = $this->db->insert("subscription_sync",$data);
					}

					$result = $subscription->create();
					
					}
			catch (RecurlyValidationException $e) {
				$this->db->where('user_id', $this->account->id);
				$this->db->delete('subscription_sync');
				
				if ($e->getMessage() === "Subscriptions is invalid. Account Cannot create multiple subscriptions for same account.") {
					$response = array(
						"error" => 'ALREADY_HAS_SUB',
						"message" => $e->getMessage()
					);
					return $response;
				
				} else {
					$response = array(
						"error" => 'INVALID_CC',
						"message" => $e->getMessage()
					);
					return $response;
				
				}
			}
			catch (RecurlyException $e) {
				$this->db->where('user_id', $this->account->id);
				$this->db->delete('subscription_sync');
				
				$response = array(
					"error" => 'GATEWAY_ERROR',
					"message" => $e->getMessage()
				);
				return $response;
		
			}
			catch(Exception $e){
				$this->db->where('user_id', $this->account->id);
				$this->db->delete('subscription_sync');
			
				$response = array(
					"error" => 'GRIND_EXCEPTION',
					"message" => $e->getMessage()
				);	
				return $response;
			}
		}else { // daily member
			//add billingdata to recurly!!!
			error_log("adding a daily member",0);
			$this->billing_info->update();
			$data["plan_code"] = $plan_code;
			$data["user_id"] = $this->account->id;
			$data["state"] = "active";
		    $result = $this->db->insert("subscription_sync",$data);
		}	
		return $result;
	}

	public function changeSubscription($plan_code,$now) {
		error_log("Change am called",0);
		if($plan_code != "daily"){
			try {
				error_log("attempting to change a subscription",0);				
				$timeframe = ($now == 1) ? "now" : "renewal";
				$subscription = new RecurlySubscription();
				$subscription->plan_code = $plan_code;
				$subscription->account = $this->recurlyAccount;
				$result = $subscription->changeSubscription($this->account->id,$timeframe,$plan_code);
			}
			catch (RecurlyValidationException $e) {
				if ($e->getMessage() === "Subscriptions is invalid. Account Cannot create multiple subscriptions for same account.") {
					$response = array(
						"error" => 'ALREADY_HAS_SUB',
						"message" => $e->getMessage()
					);
					return $response;
				
				} else {
					$response = array(
						"error" => 'INVALID_CC',
						"message" => $e->getMessage()
					);
					return $response;
				
				}
			}
			catch (RecurlyException $e) {
				$response = array(
					"error" => 'GATEWAY_ERROR',
					"message" => $e->getMessage()
				);
				return $response;

			}
			catch(Exception $e)
			{
				$response = array(
					"error" => 'GRIND_EXCEPTION',
					"message" => $e->getMessage()
				);	
				return $response;
			}
		}
		return $result;
	}
	
	
	public function get_activity()
	{

		$activity = array();
	    $this->account->pendingcharges = $this->listPendingCharges();

	    $this->account->credits = $this->listCredits();

	    if($this->account->pendingcharges){
	    	foreach ($this->account->pendingcharges as $id => $item) {
	    			$this->account->balance = $this->account->balance + $item->amount_in_cents;
        	    	$item_id=$item->created_at . "D"; //D for debit
            		$activity[$item_id] = $item;
              }
	    }
	    
        if ($this->account->credits) {
            foreach ($this->account->credits as $id => $item) {
            	if(!$item->invoice_number){
	    			$this->account->balance = $this->account->balance + $item->amount_in_cents;
        	    	$item_id=$item->created_at . "D"; //D for debit
            		$activity[$item_id] = $item;
            	} else{
            		// this charge has been invoiced
            	}	
            }
        }
   	    ksort($activity);
	    $activity = array_reverse($activity);
	    
		$this->account->activity = $activity;
		return $this->account->activity;		
	}
	
	private function _RecurlyRequest($type){
		// because recurly doesn't expose through their api some of the functions
		// that we would like to utilize…like pending charges, we have simple wrapper here
		// to help us get that data out of securely.
		$user_id = $this->account->id;
		switch ($type) {
		case "transactions":
			$path = RecurlyClient::PATH_TRANSACTIONS;
			$object = 'transaction';
			$exception = "Could not list transactions for account {$user_id}:";
		break;
		case "pendingcharges":
			$path = RecurlyClient::PATH_ACCOUNT_CHARGES . "?show=pending";
			$object = 'charge';
			$exception = "Could not list charges for account {$user_id}:";	
		break;
		case "invoices":
			$path = RecurlyClient::PATH_ACCOUNT_INVOICES;
			$object = 'invoice';
			$exception = "Could not list invoices for account {$user_id}:";
		break;
		case "charges":
			$path = RecurlyClient::PATH_ACCOUNT_CHARGES;
			$object = 'charge';
			$exception = "Could not list charges for account {$user_id}:";	
		break;
		case "credits":
			$path = RecurlyClient::PATH_ACCOUNT_CREDITS;
			$object = 'credit';
			$exception = "Could not list charges for account {$user_id}:";	
		break;
		
		}
		
		$uri = RecurlyClient::PATH_ACCOUNTS . urlencode($this->account->account_code) . $path;
		$result = RecurlyClient::__sendRequest($uri);
		if (preg_match("/^2..$/", $result->code)) {
			$results = RecurlyClient::__parse_xml($result->response, $object);
			return ($results != null && !is_array($results)) ? array($results) : $results;
		} else if (strpos($result->response, '<errors>') > 0 && $result->code == 422) {
			throw new RecurlyValidationException($result->code, $result->response);
		} else {
			throw new RecurlyException($exception." {$result->response} ({$result->code})");
		}
	}
	
	public function listTransactions()
	{
		$this->account->transactions = $this->_RecurlyRequest('transactions');
		return $this->account->transactions;
	}
	public function listCredits()
	{
		$this->account->credits = $this->_RecurlyRequest('credits');
		return $this->account->credits;
	}
	
	public function listCharges()
	{
		$this->account->charges = $this->_RecurlyRequest('charges');
		return $this->account->charges;
	}
	
	public function listPendingCharges()
	{
		$this->account->pendingcharges = $this->_RecurlyRequest('pendingcharges');
		return $this->account->pendingcharges;
	}

	
	public function listInvoices()
	{
		
		$this->account->invoices = $this->_RecurlyRequest('invoices');
		return $this->account->invoices;
	}
	
	// --------------------------------------------------------------------------
	public function delete($account_code=NULL){
		if (isset($account_code)){ // set the account code without initializing the model
			$this->account->account_code = $account_code;
		}
		try {
			RecurlyAccount::closeAccount($this->account->account_code);
			//$this->recurlyAccount->closeAccount();
		} catch (RecurlyException $e){
			error_log("could not delete recurly account: ".$this->account->account_code,0);
		}
	}
	

		// --------------------------------------------------------------------------		
	public function charge($amount, $details = ""){	

		try {		
			error_log("About to charge user  \$$amount for $details.",0);
			
			$transaction = $this->recurlyAccount->chargeAccount($amount,$details);
				
			if  ($transaction->id) {
				return true;
			} else {
				throw new Exception('transaction failed');
			}

		} catch (Exception $e) {
			issue_log($this->account->account_code,"Issue charging user [\$$amount for $details]. " . $e->getMessage(), MemberIssueType::BILLING);
		}
	} // end charge
	
	// --------------------------------------------------------------------------

	public function credit($amount, $details = ""){		
		try {		
			error_log("About to credit user \$$amount for $details.",0);
			$transaction = $this->recurlyAccount->creditAccount($amount, $details);
			
			if  ($transaction->id) {
				return true;
			} else {
				throw new Exception('transaction failed');
			}

		} catch (Exception $e) {
			issue_log($this->account->account_code,"Issue crediting user [\$$amount for $details]. " . $e->getMessage(), MemberIssueType::BILLING);
		}
	} // end charge

	// --------------------------------------------------------------------------
	
/**
 * getDetails
 * 
 * provides a lookup function for details on a members plan. Currently it
 * pulls out and sends information to start and end date, cost and state 
 * 
 * @author magicandmight
 * @param none
 */		
	public function getDetails(){
        $this->load->helper('date');
		$this->load->helper("money_helper.php");
		$subscription = new RecurlySubscription();
		$subscription = $subscription->getSubscription($this->account->account_code);	
		$response = array();
		$response["state"] = $subscription->state;
		$response["quantity"] = $subscription->quantity;
		$response["cost"] = format_money($subscription->total_amount_in_cents);		
		$datestring = "%F %j%S";
		$response["trial_end"] = mdate($datestring, $subscription->current_period_started_at);
    	$response["end_date"] = mdate($datestring, $subscription->current_period_ends_at);
    	$response["start_date"] = mdate($datestring, $subscription->current_period_started_at);
		return $response;
	}
/**
 * getPlanType
 * 
 * provides a lookup function for type of members. Currently it
 * determines monthly, daily, collaborator, etc
 * 
 * @author magicandmight
 * @param user_id
 */		
	function getPlanType($user_id){
   		$query = $this->db->get_where("subscription_sync",array("user_id"=>$user_id),1);
   		$result = $query->row();
   		error_log("ACCOUNT_MODEL: GET PLAN TYPE: plan code: ".$result->plan_code,0);
   		return $result->plan_code;
	}
}


?>