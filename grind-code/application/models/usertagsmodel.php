<?
include_once APPPATH . 'libraries/utilities.php';
include_once APPPATH . 'libraries/enumerations.php';
include_once APPPATH . 'libraries/constants.php';

class UserTagsModel extends CI_Model {

	function create($user_id, $tag_id){
		$sql = "INSERT INTO user_tags (user_id, tag_id) VALUES ('$user_id', '$tag_id')";
		return $this->db->query($sql);
	}

	function get($user_id){
		$query = mysql_query("SELECT * FROM user_tags WHERE user_id='".$user_id."'");
		$response = array();
        while($row = mysql_fetch_assoc($query)) {
	      	array_push($response, $row);
        }
        return $response;
	}

	function count($tag_id){
		$query = mysql_query("SELECT COUNT(*) as count FROM user_tags WHERE tag_id='".$tag_id."'");
		$query_result = mysql_fetch_assoc($query);
		$query_result_count = $query_result['count'];
		$count = intval($query_result_count);
		return $count;
	}

	function get_tags_with_count($user_id) {
		$this->load->model("jobtagsmodel","jtm",true);
		$this->load->model("tagsmodel","tm",true);
		$user_tags = $this->get($user_id);
        $response_data = array();
        foreach ($user_tags as $user_tag) {
          $tag_id = $user_tag['tag_id'];
          $total_count = $this->count($tag_id) + $this->jtm->count($tag_id);
          $tag = $this->tm->get($tag_id);
          $name = $tag['name'];
          array_push($response_data, array('name'=>$name, 'id'=>$user_tag['tag_id'], 'count'=>$total_count));
        }
        return $response_data;
	}
};
?>