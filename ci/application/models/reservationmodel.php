<?php
// Location in repository: member/ci/application/models/reservationmodel.php


/*

CREATE TABLE `reservation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `space_id` int(10) unsigned NOT NULL,
  `date` date NOT NULL,
  `party_of` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `checked_in` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `paid` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `space_id` (`space_id`,`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
*/

include_once APPPATH . 'libraries/utilities.php';
include_once APPPATH . 'libraries/enumerations.php';
include_once APPPATH . 'libraries/constants.php';

class ReservationModel extends CI_Model {

	function getUserByWpId($user_id = null, $type = 'basic') {
		if (empty($user_id) && !empty($_SESSION['wpuser']['wp_users_id'])) {
			$user_id = $_SESSION['wpuser']['wp_users_id'];
		}

		if (empty($user_id)) {
			return false;
		}

		$this->load->model("members/membermodel","",true);
		if ($type === 'full') {
			return $this->membermodel->get_fullMemberData($user_id, UserIdType::WORDPRESSID);
		}

		return $this->membermodel->get_basicMemberData($user_id, UserIdType::WORDPRESSID);
	}

	function wpGetReservations($options = array()) {
		$options = array_merge(array(
			'min_date' => false,
			'max_date' => false,
			'exact_date' => false,
			'location_id' => false,
			'space_id' => false,
			'user_id' => false,
		), (array) $options);

		$sql = array();
		$sql[] = "select space.name as s_name, location.name as l_name, first_name, last_name, party_of, checked_in, paid, date from reservation";
		$sql[] = "join space on reservation.space_id = space.id";
		$sql[] = "join user on reservation.user_id = user.id";
		$sql[] = "join location on space.location_id = location.id";

		// Get find conditions
		$conditions = array();
		$min_date = date('Y-m-d', strtotime($options['min_date']));
		$max_date = date('Y-m-d', strtotime($options['max_date']));
		if (!empty($options['user_id'])) {
			$conditions[] = sprintf("reservation.user_id = %d", (int) $options['user_id']);
		}
		if (!empty($options['space_id'])) {
			$conditions[] = sprintf("reservation.space_id = %d", (int) $options['space_id']);
		}
		if (!empty($options['location_id'])) {
			$conditions[] = sprintf("space.location_id = %d", (int) $options['location_id']);
		}
		if (!empty($options['exact_date'])) {
			$conditions[] = sprintf("reservation.date = '%s'", date('Y-m-d', strtotime($options['exact_date'])));
		}
		if (!empty($options['min_date']) && !empty($options['max_date'])) {
			$conditions[] = sprintf("reservation.date between '%s' and '%s'", $min_date, $max_date);
		} elseif (!empty($options['min_date'])) {
			$conditions[] = sprintf("reservation.date >= '%s'", $min_date);
		} elseif (!empty($options['max_date'])) {
			$conditions[] = sprintf("reservation.date <= '%s'", $max_date);
		}

		$sql[] = "where " . implode(' and ', $conditions);
		$sql = sprintf(implode(' ', $sql), $date);
		$query = $this->db->query($sql);

		$records = array();
		foreach ($query->result_array() as $row) {
			$records[] = $row;
		}
		$query->free_result();
		return $records;
	}

	function reservationsLeft($date, $location_id = null) {
		if (empty($location_id)) {
			return false;
		}

		$capacity = 0;
		try {
			$capacity = $this->getCapacityByLocationId($location_id);
			if ($capacity == 0) {
				return array($capacity, 0);
			}

			// TODO: Verify that there is inventory available

			// Verify that capacity is not already booked
			$booked_capacity = $this->getBookingsOnDateForLocation($location_id, $date);
			if ($booked_capacity == $capacity) {
				return array($capacity, 0);
			}

			return array($capacity, $capacity - $booked_capacity);
		} catch (Exception $e) {
			return array($capacity, 0);
		}
	}

	// TODO: Some sort of error handling?
	function reserve($member_id, $reservation) {
		$this->load->model("members/membermodel");
		$reservation['user_id'] = $member_id;

		try {
			return $this->addReservation($member_id, $reservation);
		} catch (Exception $e) {
			error_log(sprintf("Reservation: %s", $e->getMessage()));
			$this->_reservationError = $e->getMessage();
			return false;
		}
	}

	function check_in($user_id, $location_id, $date = null) {
		$query = $this->getReservation($user_id, $location_id, $date = null);
		if ($query->num_rows() === 0) {
			throw new Exception("There is no available reservation on this date");
		}

		$row = $query->row();
		if ($row->checked_in == 1) {
			// TODO: Do we ignore this?
			throw new Exception("Member has already checked in");
		}

		$id = $row->id;
		$query->free_result();

		$this->db->where('id', $row->id);
		return $this->db->update('reservation', array('checked_in' => 1));
	}

	function hasReservation($user_id, $location_id, $date = null) {
		$query = $this->getReservation($user_id, $location_id, $date);
		$num_rows = $query->num_rows();
		$query->free_result;

		return $num_rows == 1;
	}

	function getReservation($user_id, $location_id, $date = null) {
		if ($date === null) {
			$date = date('Y-m-d');
		}

		$sql = array();
		$sql[] = "select * from reservation";
		$sql[] = "join space on space.id = reservation.space_id";
		$sql[] = "where reservation.user_id = %d  and reservation.date = '%s'";
		$sql[] = "and space.location_id = %d and reservation.checked_in = 0";
		$sql[] = "limit 1";

		$sql = sprintf(implode(' ', $sql), $user_id, $date, $location_id);
		return $this->db->query($sql);
	}

	// TODO: Can users reserve more than one spot in a space?
	function addReservation($member_id, $data) {
		$this->load->model("members/membermodel");

		if (empty($member_id)) {
			throw new Exception("Reservation missing member id");
		}

		$data['user_id'] = $member_id;

		$requiredFields = array('user_id', 'space_id', 'date', 'party_of');
		$requiredFields = array_combine($requiredFields, $requiredFields);

		if (empty($data)) {
			throw new Exception("Missing reservation data");
		}

		$data = array_intersect_key($data, $requiredFields);
		if (count(array_keys($data)) !== count(array_keys($requiredFields))) {
			throw new Exception("Missing reservation data");
		}

		$data['date'] = date('Y-m-d', strtotime($data['date']));
		if (empty($data['date'])) {
			throw new Exception("Invalid date format for reservation");
		}

		// Validate that there are spots available
		$capacity = $this->getCapacityBySpace($data['space_id']);
		if ($capacity == 0) {
			throw new Exception("There is no capacity at this space");
		}

		// TODO: Verify that there is inventory available

		// Verify that capacity is not already booked
		$booked_capacity = $this->getBookingsOnDateForSpace($data['space_id'], $data['date']);
		if ($booked_capacity == $capacity) {
			throw new Exception("There is no more capacity in this space");
		}

		$available_capacity = $capacity - $booked_capacity;
		if ($available_capacity < (int) $data['party_of']) {
			throw new Exception("There is not enough capacity for this booking");
		}

		if (!$this->db->insert("reservation", $data)) {
			throw new Exception("Unable to create the reservation in the database");
		}

		// Mark reservation as paid
		$reservation_id = $this->db->insert_id();
		if (!$this->membermodel->charge_daily($member_id, (int) $data['party_of'])) {
			$this->db->delete('delete', array('id' => $reservation_id));
			throw new Exception("Unable to charge member the daily rate");
		}

		$this->db->where('id', $reservation_id);
		if (!$this->db->update('reservation', array('paid' => 1))) {
			$this->db->delete('delete', array('id' => $reservation_id));
			throw new Exception("Reservation made, but we were unable to mark the reservation as paid");
		}


		$space_id = $data['space_id'];
		$party_of = $data['party_of'];
		$date     = date('Y-m-d', strtotime($data['date']));

		$user = $this->membermodel->get_basicMemberData($user_id, UserIdType::ID);

		$to = EMAIL_G_ADMIN;
		$subject = "[GRIND RESERVATION]";
		$body = <<<BODY
Your space reservation has been placed:

- Space ID: {$space_id}
- Party of: {$party_of}
- Date: {$date}

Let us know if you have any issues.

- Grind

BODY;

		$headers = "From: ".EMAIL_G_ADMIN."\r\n" .
			"Reply-To: ".EMAIL_G_ADMIN."\r\n" .
			"X-Mailer: PHP/" . phpversion() . "\r\n" .
			"MIME-Version: 1.0\r\n" .
			"Content-Type: text/html; charset=ISO-8859-1\r\n";
		mail($to, $subject, $body, $headers);
		mail($user->email, $subject, $body, $headers);

		return true;
	}

	function getCapacityBySpace($space_id) {
		// Validate that there are spots available
		$sql = array();
		$sql[] = "select capacity from space";
		$sql[] = "where is_bookable = 1";
		$sql[] = "and capacity > 0";
		$sql[] = "and is_inactive = 0";
		$sql[] = "and id = %d";
		$sql = sprintf(implode(' ', $sql), $space_id);
		$query = $this->db->query($sql);
		$num_rows = $query->num_rows();
		if ($num_rows == 0) {
			throw new Exception("This space cannot be booked");
		}

		$row = $query->row();
		$capacity = $row->capacity;
		$query->free_result;

		return $capacity;
	}

	function getCapacityByLocationId($location_id) {
		// Validate that there are spots available
		$sql = array();
		$sql[] = "select sum(capacity) as capacity from space";
		$sql[] = "where is_bookable = 1";
		$sql[] = "and capacity > 0";
		$sql[] = "and is_inactive = 0";
		$sql[] = "and location_id = %d";
		$sql = sprintf(implode(' ', $sql), $location_id);
		$query = $this->db->query($sql);
		$num_rows = $query->num_rows();
		if ($num_rows == 0) {
			throw new Exception("This location cannot be booked");
		}

		$row = $query->row();
		$capacity = $row->capacity;
		$query->free_result;

		return $capacity;
	}

	function getBookingsOnDateForSpace($space_id, $date, $paid = null) {
		$sql = array();
		$sql[] = "select sum(party_of) as booked_capacity from reservation";
		$sql[] = "where space_id = %d";
		$sql[] = "and date = '%s'";
		if ($paid === true) {
			$sql[] = "and paid = 1";
		}

		$sql = sprintf(implode(' ', $sql), $space_id, $date);
		$query = $this->db->query($sql);
		$num_rows = $query->num_rows();
		if ($num_rows == 0) {
			throw new Exception("There was an error getting the booked capacity");
		}

		$row = $query->row();
		$booked_capacity = $row->booked_capacity;
		$query->free_result;

		return $booked_capacity;
	}

	function getBookingsOnDateForLocation($location_id, $date, $paid = null) {
		$sql = array();
		$sql[] = "select sum(party_of) as booked_capacity from reservation";
		$sql[] = "join space on reservation.space_id = space.id";
		$sql[] = "where location_id = %d";
		$sql[] = "and date = '%s'";
		if ($paid === true) {
			$sql[] = "and paid = 1";
		}

		$sql = sprintf(implode(' ', $sql), $location_id, $date);
		$query = $this->db->query($sql);
		$num_rows = $query->num_rows();
		if ($num_rows == 0) {
			throw new Exception("There was an error getting the booked capacity");
		}

		$row = $query->row();
		$booked_capacity = $row->booked_capacity;
		$query->free_result;

		return $booked_capacity;
	}

}
