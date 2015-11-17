<?php


/**
 * Print Model
 * 
 * Manages tasks associated with data from print, fax, and copy charges
 * 
 * @author magicandmight
 */
 
 class PrintModel extends CI_Model {
    
    
    function __construct()
       {
           parent::__construct();
           $this->load->model('/billing/accountmodel','am',true);
       }
	
	
	function processCharges($xml){
		error_log('PRINT: printing start',0);
		$users = $xml->xpath('//USERNAME');
		$users = array_unique($users);
		
		foreach ($users as $user){
			
			
			$account_code = $this->lookup_user($user);
			if ($account_code) {
			error_log('PRINT: printing user: '.$user.",id: ".$account_code,0);
				//am section moved from process_job
				$this->am->init($account_code);
					
				$type = $this->am->getPlanType($account_code);
				$type = ($type=="daily") ? "daily" : "monthly";
					if ($type == "monthly"){
						$credits = $this->fetch_credits($account_code);
						$credit_balance = $credits->credits;
					} else {
						$credit_balance = 0.00;
					}
					
				//end am section			
				$user_charges = $xml->xpath("JOB[USERNAME='".$user."']");
				
				if ($user_charges){
					error_log('PRINT: in user charges',0);

					foreach($user_charges as $job) // loop through our books
					{
						if($this->previous_charge($job[0]["id"])){
							error_log("PRINT: DUPLICATE ENTRY:". $job[0]["id"],0);
					 	} else {
					 		// the if statement below means print charges will only be processed if this is a daily user
					 	    if ($type == "daily"){
					 			$this->process_job($account_code,$job,$credit_balance,$type); 
					 		}
					 	}
					}    	
					if ($type == "monthly"){
						$credits->credits = $credit_balance;
						$this->update_credits($account_code,$credits);
					} 				
				} 	  
			} // end if account code (if we don't have the account code we don't know the user)
		}
	}

	 function process_job($account_code,$job,&$credit_balance,$type){

			if ($job->AMOUNTPAID != 0) {

				$name = ($job->JOBNAME != "") ? $job->JOBNAME.", " : "";
				$service = $job->SERVICE->NAME;
				$quantity = $job->SERVICE->CARDINALITY;
				$quantity = ($quantity>1) ? $quantity . " pages" : $quantity . " page";
				// moved account model out of the job up to the user
				$charge_amount = substr($job->AMOUNTPAID,0,-1);
				error_log("PRINT CHARGE AMOUNT" . $charge_amount,0);
				$charge = $this->deduct_credit($credit_balance,$charge_amount);
				$credited = ($charge == $charge_amount) ? " " :" A credit was applied";
				$this->am->charge($charge,$name.$service.', '.$quantity.$credited); 
				$this->record_charge($job[0]["id"]);
				
			}
		
		}
	
		//remove test harness
		function test_credits($user_id){
			$available_credits = $this->fetch_credits($user_id);
			
			$this->update_credits($user_id,$available_credits);
			$ac = var_dump($available_credits);
			return $ac;
		}
		// end remove
	
		private function fetch_credits($user_id){
			error_log('PRINT: Fetching Credits',0);
			// get available credits for $user_id
			$query = $this->db->get_where('print_credits', array('user_id' => $user_id), 1);
			$available_credits = $query->row();

				
			if ($available_credits == null){
				error_log('it thinks its null',0);
				$data = array(
					'expiry_date' => $this->set_credit_expiration(),
					'credits' =>PRINT_CREDIT_AMOUNT,
					'user_id' => $user_id
				);
				
				$this->db->insert('print_credits',$data);
				
				$available_credits = new stdClass();
				foreach ($data as $key => $value){
					$available_credits->$key = $value;
				}
			}
				
			if (($available_credits->expiry_date <= date('Y-m-d'))){
				error_log('it thinks its less date than today',0);
				$available_credits->credits = PRINT_CREDIT_AMOUNT;
				$available_credits->expiry_date = $this->set_credit_expiration();

			} 
			return $available_credits;
		}
		private function update_credits($user_id,&$credits){
			error_log('PRINT: Updating Credits',0);
			// get available credits for $user_id
			$data = array(
               'credits' => $credits->credits,
               'expiry_date' => $credits->expiry_date,
              );

			$this->db->where('user_id', $user_id);
			$this->db->update('print_credits', $data); 
		}
		private function adding_credits($user_id,&$credits){
			error_log('PRINT: Adding Credits',0);
			// get available credits for $user_id
			$data = array(
               'credits' => $credits->credits,
               'expiry_date' => $credits->expiry_date,
              );

			
			$this->db->update('print_credits', $data); 
		}
		
		private function set_credit_expiration(){
			error_log('PRINT: Setting credit expiration',0);

			$curMonth = date('m');
			$curYear  = date('Y');
			if ($curMonth == 12) {
				$timeStamp = mktime(0, 0, 0,1,1, $curYear+1);
			} else {
				$timeStamp = mktime(0, 0, 0, $curMonth+1,1,$curYear);

			}
			$firstDay = date('Y-m-d',$timeStamp);
			error_log('PRINT: Setting credit expiration to: '.$firstDay,0);
			return $firstDay;
			return false;
		}
		
		private function deduct_credit(&$credit_balance,&$charge_amount){
			error_log('PRINT: deduct balance: '.$credit_balance.' charge:'.$charge_amount,0);
			$balance = $credit_balance - $charge_amount;
			error_log('PRINT: BALANCE:'.$balance,0);
			if ($balance > 0){
				$charge_amount = 0;
				$credit_balance = $balance;
			} else {
				$charge_amount = abs($balance);
				$credit_balance = 0;
			}
			return $charge_amount;
		}
		
		private function lookup_user($grind_id){
			$query = $this->db->query("select user.id from user left join `wpmember_usermeta` on user.wp_users_id = `wpmember_usermeta`.`user_id` where wpmember_usermeta.meta_key = 'grind_uid' and wpmember_usermeta.meta_value = '".$grind_id."'");	
			if ($query->num_rows() > 0){
				$user = $query->row();
				$user_id = $user->id;
				error_log('PRINT: returning user:'.$user_id,0);
				return $user_id;
			} else {
				error_log('PRINT: could not find user:'.$grind_id,0);
				return false;
			}
			
		}
		
		private function previous_charge($guid){
			error_log("PRINT: in previous charge",0);
			$query = $this->db->query("select guid from print_charges where guid = '".$guid."'");	
			if ($query->num_rows() > 0){
				error_log("prev charge found",0);
				return true;
			} else {
				error_log("prev charge not found",0);
				return false;
			}
			
		}
		
		private function record_charge($guid){
		error_log("PRINT: in recording charge:".$guid,0);
			$data = array(
               'guid' => $guid
              );
              $this->db->query("INSERT INTO print_charges (guid) VALUES ('".$guid."')");
			error_log($this->db->last_query(),0);
			
		}
		

		
	}

?>