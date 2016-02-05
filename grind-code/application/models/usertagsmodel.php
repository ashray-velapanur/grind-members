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


};
?>