<?
include_once APPPATH . 'libraries/utilities.php';
include_once APPPATH . 'libraries/enumerations.php';
include_once APPPATH . 'libraries/constants.php';



class UsersModel extends CI_Model {
    
    
	       
    public function getUsers() {
        $sql = "
		select 
				user.id, user.rfid, user.wp_users_id, user.first_name, user.last_name, 
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
    
};
?>