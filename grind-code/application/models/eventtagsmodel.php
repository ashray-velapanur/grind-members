<?
include_once APPPATH . 'libraries/utilities.php';
include_once APPPATH . 'libraries/enumerations.php';
include_once APPPATH . 'libraries/constants.php';

class EventTagsModel extends CI_Model {

	function create($event_id, $tag_id){
		$sql = "INSERT INTO event_tags (event_id, tag_id) VALUES ('$event_id', '$tag_id')";
		return $this->db->query($sql);
	}

	function get($event_id){
		$this->load->model("tagsmodel","tm",true);
		$query = mysql_query("SELECT * FROM event_tags WHERE event_id='".$event_id."'");
		$response = array();
        while($row = mysql_fetch_assoc($query)) {
              $tag_id = $row['tag_id'];
	          $tag = $this->tm->get($tag_id);
	          $name = $tag['name'];
	          array_push($response, array('name'=>$name, 'id'=>$row['tag_id']));
        }
        return $response;
	}

	function count($tag_id){
		$query = mysql_query("SELECT COUNT(*) as count FROM event_tags WHERE tag_id='".$tag_id."'");
		$query_result = mysql_fetch_assoc($query);
		$query_result_count = $query_result['count'];
		$count = intval($query_result_count);
		return $count;
	}

	function get_tags_with_count($event_id) {
		$this->load->model("jobtagsmodel","jtm",true);
		$this->load->model("usertagsmodel","utm",true);
		$this->load->model("tagsmodel","tm",true);
		$event_tags = $this->get($event_id);
        $response_data = array();
        foreach ($event_tags as $event_tag) {
          $tag_id = $event_tag['id'];
          $total_count = $this->count($tag_id) + $this->jtm->count($tag_id) + $this->utm->count($tag_id);
          $tag = $this->tm->get($tag_id);
          $name = $tag['name'];
          array_push($response_data, array('name'=>$name, 'id'=>$event_tag['id'], 'count'=>$total_count));
        }
        return $response_data;
	}

};
?>