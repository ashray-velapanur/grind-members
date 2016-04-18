<?
include_once APPPATH . 'libraries/utilities.php';
include_once APPPATH . 'libraries/enumerations.php';
include_once APPPATH . 'libraries/constants.php';



class EventsModel extends CI_Model {

	function create($id, $name){
		$sql = "INSERT INTO events (id, name) VALUES ('$id', '$name')";
                error_log($sql);
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

        function add_eventbrite_token($eb_token, $eb_user_id=NULL) {
                $sql = "INSERT INTO eventbrite (";
                if($eb_user_id) {
                        $sql .= "eb_user_id, ";
                }
                $sql .= "token) VALUES (";
                if($eb_user_id) {
                        $sql .= "'$eb_user_id', ";
                }
                $sql .= "'$eb_token')";
                error_log($sql);
                return $this->db->query($sql);
        }

        function delete_eventbrite_token($eb_user_id=NULL) {
                if($eb_user_id) {
                        $sql = "DELETE FROM eventbrite WHERE eb_user_id='".$eb_user_id."'";
                } else {
                        $sql = "TRUNCATE TABLE eventbrite";
                }
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