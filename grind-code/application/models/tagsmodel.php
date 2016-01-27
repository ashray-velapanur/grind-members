<?
include_once APPPATH . 'libraries/utilities.php';
include_once APPPATH . 'libraries/enumerations.php';
include_once APPPATH . 'libraries/constants.php';



class TagsModel extends CI_Model {

	public function create($name){
		$sql = "INSERT INTO tags (name) VALUES ('$name')";
		if ($this->db->query($sql) === TRUE) {
			$response = array('success'=>TRUE);
		} else {
			$response = array('success'=>FALSE);
		}
		return $response;
	}

	public function all(){
        $query = mysql_query("SELECT * FROM tags");
        $response = array();
        while($row = mysql_fetch_assoc($query)) {
	      	array_push($response, $row);
        }
        return $response;
	}
};
?>