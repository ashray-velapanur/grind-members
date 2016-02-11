<?
include_once APPPATH . 'libraries/utilities.php';
include_once APPPATH . 'libraries/enumerations.php';
include_once APPPATH . 'libraries/constants.php';



class EventsModel extends CI_Model {

	function create($id, $name){
		$sql = "INSERT INTO events (id, name) VALUES ('$id', '$name')";
		return $this->db->query($sql);
	}
};
?>