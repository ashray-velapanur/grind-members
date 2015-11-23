<?
class Users extends CI_Controller {
	public function login(){
		wp_signon('', '');
		foreach ($_SESSION['wpuser'] as $key => $value) {
			error_log($key);
			error_log($value);
		}
	}
}
?>