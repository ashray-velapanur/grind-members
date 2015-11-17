<?
include_once APPPATH . 'libraries/utilities.php';
include_once APPPATH . 'libraries/enumerations.php';
include_once APPPATH . 'libraries/constants.php';

require_once (MEMBERINCLUDEPATH.'/wp-admin/includes/user.php');
class ApplicantModel extends CI_Model {


	function getApplicants($status_id = null) {
		
		$sql = "
			select
				user.id, user.date_added as date, user.first_name, user.last_name,applicant_why.why_me,location.name as location,
				company.name as company_name, email.address as email_address,
				membership_status_lu.description as status_name, user.membership_status_luid as status_id,
				user.referrer,
				plans.name as plan,
				user.date_added
			from
				user left outer join company on company.id = user.company_id
				left outer join email on email.user_id = user.id
				left outer join applicant_why on applicant_why.user_id = user.id
				left outer join membership_status_lu on membership_status_lu.id = user.membership_status_luid
				left outer join location on location.id=user.location_id
				left outer join applicant_plan on applicant_plan.user_id= user.id
				left outer join plans on plans.id = applicant_plan.plan_id ";
		if ($status_id != null) {
            $sql .= ' where user.membership_status_luid = ' . $status_id . ' ';
        } else {
			$sql .= ' where user.membership_status_luid in (' .
				MembershipStatus::APPLICANT_AWAITING_APPROVAL . ',' .
				MembershipStatus::APPLICANT_DENIED . ',' .
				MembershipStatus::APPLICANT_APPROVED . ') ';
        }
    	$sql .= 'ORDER BY date DESC';
		
		$query = $this->db->query($sql);
        
        $results = $query->result();

		foreach($results as $result) {
			$sql2 = " select user.id as count from user left outer join email on user.id = email.user_id
					left outer join membership_status_lu on membership_status_lu.id = user.membership_status_luid
					where user.id != " . $result->id . " and email.address = '" . $result->email_address . "'
					and membership_status_lu.id != " . MembershipStatus::APPLICANT_DENIED;
			$query2 = $this->db->query($sql2);
			$results2 = $query2->result();
			if (count($results2) > 0) {
				$result->HasUniqueEmail = false;
			} else {
				$result->HasUniqueEmail = true;
			}
		}
		
		return $results;
	}
	
	function insertApplicant($data) {
		$this->db->trans_start();
		//Handle the company table
		if ($this->input->get_post('company_name')){
			$companydata = array(
				'name'=>$this->input->get_post('company_name')
			);
			$this->db->insert("company", $companydata);
			$companyid = $this->db->insert_id();
		} else { // no company name passed in
			$companyid = null;	
		}
		
		//Handle the user table
		$userdata = array(
			'first_name'=>$this->input->get_post('first_name'),
			'last_name'=>$this->input->get_post('last_name'),
			'is_active'=>0,
			'date_added'=>date(DATE_ISO8601),
			'company_id'=>$companyid,
			'membership_status_luid'=>MembershipStatus::APPLICANT_AWAITING_APPROVAL,
			'referrer'=>$this->input->get_post('referrer'),
			'location_id'=>$this->input->get_post('location_id')
		);
		$this->db->insert("user", $userdata);
		$userid = $this->db->insert_id();
		
		//Handle the applicant_why table
		if ($this->input->get_post('why_me')){
			$whydata = array(
				'why_me'=>$this->input->get_post('why_me'),
				'user_id'=>$userid
			);
			$this->db->insert("applicant_why", $whydata);
			$whyid = $this->db->insert_id();
		} else { // no data passed in
			$whyid = null;	
		}
		//Handle the email table
		$emaildata = array(
			'user_id'=>$userid,
			'is_primary'=>true,
			'address'=>$this->input->get_post('email_address')
		);
		$this->db->insert("email", $emaildata);
		$this->db->trans_complete();
		
		if ($this->db->trans_status() === FALSE)
		{
		    // generate an error... or use the log_message() function to log your error
		}
		return $userid;
	}

	function approveApplicant($id) {
		$data = array('membership_status_luid'=>MembershipStatus::APPLICANT_APPROVED);
		$this->db->where("id", $id);
		$this->db->update("user", $data);
		
		return true;
	}
	
	function delete($id) {
		$where = array('id'=>$id);
		$this->db->delete("user",$where);
		$where = array('user_id'=>$id);
		$this->db->delete("applicant_why",$where);
        $this->db->delete("applicant_plan",$where);
        $this->db->delete("email",$where);
		
		return true;
	}
	
	function denyApplicant($id) {
		
		// next check if they had a wordpress account
		$this->db->select("wp_users_id");
		$this->db->where("id",$id);
		$query = $this->db->get('user');
		if ($query->num_rows() > 0)
		{
		   foreach ($query->result() as $row)
		   {
		    if (isset($row->wp_users_id)) // there is an id
			$this->deleteWordpressAccount($row->wp_users_id);
		   }
		}
		$data = array('membership_status_luid'=>MembershipStatus::APPLICANT_DENIED,'wp_users_id'=>null);
		$this->db->where("id", $id);
		$this->db->update("user", $data);
		
		return true;
	}
	
	function applicantHasUniqueEmail($id) {
		$this->db->where("id", $id);
		$this->db->update("user", $data);
	}
	function createWordpressAccount($email, $first_name, $last_name) {
		$temporaryPassword = $this->get_random_password();
		$registerHash = md5(uniqid('', true));
		
		$result = null;
		
		$user_id = username_exists( $email );
		if ( !$user_id ) {
			$user_id = wp_create_user( $email, $temporaryPassword, $email );
			error_log("testing wp user creation:".$user_id);
		} else {
			return false;
		}
		if ($user_id){
			add_user_meta($user_id, 'registerHash', $registerHash , true );
			return array("wordpressid" => $user_id, "temporarypassword" => $temporaryPassword, "registerHash" => $registerHash);
		}
		
		return array("wordpressid" => $result, "temporarypassword" => $temporaryPassword, "registerHash" => $registerHash);

	}
	function deleteWordpressAccount($wpid) {
		error_log("wpid".$wpid,0);
		try {
  			return wp_delete_user($wpid);
  		} catch (exception $e) {
  			return $e->getMessage();
 		 }

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
}
?>