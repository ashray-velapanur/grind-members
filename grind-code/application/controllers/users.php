<?
class Users extends CI_Controller {
	public function login(){
		wp_signon('', '');
		foreach ($_SESSION['wpuser'] as $key => $value) {
			error_log($key);
			error_log($value);
		}
	}

	public function all_users($row=NULL) {
		$this->load->library('pagination');
		$query = $this->load->model("members/membermodel", "", true);
		
		$config['base_url'] = site_url('/grind-code/admin/usermanagement/all_users/');
		
		$config['total_rows'] = $this->membermodel->count_members();
		$config['per_page'] = 2; 
		$config['full_tag_open'] = '<div style="display:inline-block" class="navigation">';
		$config['full_tag_close'] = '</div>';
		$config['uri_segment'] = 4;
		$data["users"] = $this->membermodel->new_listing($config['per_page'],$row);		
		$this->pagination->initialize($config);
		
		$data["pagination"] = $this->pagination->create_links();
		
		$data["show_member_subnav"] = true;
		$data["member_subnav_current"] = "Members Listing";
		return json_encode($data);
		//$this->load->view("/admin/all_users.php",$data);
	}
}
?>