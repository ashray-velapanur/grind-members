<?
require(APPPATH.'/controllers/admin/spaces_dict.php');

class LocationSetup extends CI_Controller {
	function __construct() {
		parent::__construct();
		$this->output->enable_profiler(TRUE);
		$this->load->helper("url");
		$this->load->helper("form");
		$this->load->helper("html");
		$this->load->helper("nav_location");
		$this->load->helper("admin_layout");
	}

	public function add_locations() {
		global $environmentsToSpaces;
		$this->delete_locations();
		$environment = $_GET["environment"];
		$spaces = $environmentsToSpaces[$environment];
		$this->load->model("locationmodel","lm",true);
		foreach ($spaces as $space_id) {
			$this->lm->add_space_and_resources($space_id, $environment);
		}
	}

	public function delete_locations() {
		$sql = "TRUNCATE TABLE cobot_bookings";
		error_log($sql);
		$this->db->query($sql);
		$sql = "TRUNCATE TABLE cobot_memberships";
		error_log($sql);
		$this->db->query($sql);
		$sql = "TRUNCATE TABLE cobot_webhook_subscriptions";
		error_log($sql);
		$this->db->query($sql);
		$sql = "TRUNCATE TABLE cobot_resources";
		error_log($sql);
		$this->db->query($sql);
		$sql = "TRUNCATE TABLE cobot_spaces";
		error_log($sql);
		$this->db->query($sql);
	}
}
?>