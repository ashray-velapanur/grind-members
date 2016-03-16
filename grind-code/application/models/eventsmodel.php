<?
include_once APPPATH . 'libraries/utilities.php';
include_once APPPATH . 'libraries/enumerations.php';
include_once APPPATH . 'libraries/constants.php';



class EventsModel extends CI_Model {

	function create($id, $name){
		$sql = "INSERT INTO events (id, name) VALUES ('$id', '$name')";
		return $this->db->query($sql);
	}

	function get_events($tag_id=NULL) {
        $response_data = array();
        $sql = "select ".
        			"events.id, events.name ".
        		"from ".
        			"events ";
        if($tag_id) {
        	$sql .= "join event_tags on event_tags.event_id = events.id ".
        			"where event_tags.tag_id = ".$tag_id;
        }
        		
        error_log($sql);
        $query = $this->db->query($sql);
        $response_data = $query->result();
        return $response_data;
	}
};
?>