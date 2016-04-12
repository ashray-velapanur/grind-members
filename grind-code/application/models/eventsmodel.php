<?
include_once APPPATH . 'libraries/utilities.php';
include_once APPPATH . 'libraries/enumerations.php';
include_once APPPATH . 'libraries/constants.php';



class EventsModel extends CI_Model {

	function create($id, $name){
		$sql = "INSERT INTO events (id, name) VALUES ('$id', '$name')";
		return $this->db->query($sql);
	}

	function get_events($tag_id=NULL, $limit=NULL, $offset=NULL) {
        $response_data = array();
        $sql = "select ".
        			"events.id, events.name ".
        		"from ".
        			"events ";
        if($tag_id) {
        	$sql .= "join event_tags on event_tags.event_id = events.id ".
        			"where event_tags.tag_id = ".$tag_id;
        }
        if (isset($limit)) {
                $sql .= " limit ".$limit;
        } 
        if (isset($offset)){
                $sql .= " offset ".$offset;
        }
        error_log($sql);
        $query = $this->db->query($sql);
        $response_data = $query->result();
        return $response_data;
	}

        function add_eventbrite_token($eb_user_id, $eb_token) {
                $sql = "INSERT INTO eventbrite (eb_user_id, token) VALUES ('$eb_user_id', '$eb_token')";
                error_log($sql);
                return $this->db->query($sql);
        }

        function delete_eventbrite_token($eb_user_id) {
                $sql = "DELETE FROM eventbrite WHERE eb_user_id='".$eb_user_id."'";
                error_log($sql);
                return $this->db->query($sql);
        }

        function get_eventbrite_token() {
                $query = $this->db->get('eventbrite');
                $results = $query->result();
                $eventbrite = current($results);
                return $eventbrite;
        }
};
?>