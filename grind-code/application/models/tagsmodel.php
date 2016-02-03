<?
include_once APPPATH . 'libraries/utilities.php';
include_once APPPATH . 'libraries/enumerations.php';
include_once APPPATH . 'libraries/constants.php';



class TagsModel extends CI_Model {

	function create($name){
		$sql = "INSERT INTO tags (name) VALUES ('$name')";
		return $this->db->query($sql);
	}

	function get($tag_id){
        $query = mysql_query("SELECT * FROM tags WHERE id='".$tag_id."'");
        $response = mysql_fetch_assoc($query);
        return $response;
	}

	function all(){
        $query = mysql_query("SELECT * FROM tags");
        $response = array();
        while($row = mysql_fetch_assoc($query)) {
	      	array_push($response, $row);
        }
        return $response;
	}
};
?>