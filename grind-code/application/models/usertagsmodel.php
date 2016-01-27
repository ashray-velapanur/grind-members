<?
include_once APPPATH . 'libraries/utilities.php';
include_once APPPATH . 'libraries/enumerations.php';
include_once APPPATH . 'libraries/constants.php';

class UserTagsModel extends CI_Model {

	function create($user_id, $tag_id){
		$sql = "INSERT INTO user_tags (user_id, tag_id) VALUES ('$user_id', '$tag_id')";
		if ($this->db->query($sql) === TRUE) {
			$response = array('success'=>TRUE);
		} else {
			$response = array('success'=>FALSE);
		}
		return $response;
	}

	function get($user_id){
		$query = mysql_query("SELECT * FROM user_tags WHERE user_id='".$user_id."'");
		$response = array();
        while($row = mysql_fetch_assoc($query)) {
	      	array_push($response, $row);
        }
        return $response;
	}
};
?>