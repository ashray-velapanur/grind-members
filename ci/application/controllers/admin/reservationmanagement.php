<?
// Location in repository: member/ci/application/controllers/admin/reservationmanagement.php

class ReservationManagement extends CI_Controller {

	function __construct() {
		parent::__construct();

		$this->load->helper("url");
		$this->load->helper("form");
		$this->load->helper("html");
		$this->load->helper("nav_location");
		$this->load->helper("admin_layout");

		$this->load->model("spacemodel");
		$this->load->model("locationmodel");
		$this->load->model("reservationmodel");
  }

  function index() {
		$space_id = false;
		$location_id = false;
		$exact_date = date('Y-m-d');

		$spaces = $this->spacemodel->wpGetSpacesList();
		$locations = $this->locationmodel->wpGetLocationsList();

		// Process a user reservation
		if (!empty($_REQUEST['data'])) {
			$data = $_REQUEST['data'];
			if (!empty($data['space_id'])) {
				$space_id = (int) $data['space_id'];
			}
			if (!empty($data['location_id'])) {
				$location_id = (int) $data['location_id'];
			}

			if (!isset($spaces[(int) $space_id])) {
				$space_id = false;
			}

			if (!isset($locations[(int) $location_id])) {
				$location_id = false;
			}

			if (!empty($data['exact_date'])) {
				$exact_date = date('Y-m-d', strtotime((string) $data['exact_date']));
			}
		}

		// Get data for the page
		$reservations = $this->reservationmodel->wpGetReservations(compact('space_id', 'location_id', 'exact_date'));
		list($capacity, $spacesLeft) = $this->reservationmodel->reservationsLeft($exact_date, $location_id);

		$data = compact('spaces', 'locations', 'reservations', 'location_id', 'space_id', 'exact_date', 'capacity', 'spacesLeft');
		display('admin/reservations', $data, "Reservations");
	}

}