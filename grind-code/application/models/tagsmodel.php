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
        $this->load->model("jobtagsmodel","jtm",true);
		$this->load->model("usertagsmodel","utm",true);
        while($row = mysql_fetch_assoc($query)) {
        	$total_count = $this->utm->count($row['id']) + $this->jtm->count($row['id']);
        	$row["count"] = $total_count;
	      	array_push($response, $row);
        }
        return $response;
	}

    function company_tags($company_id) {
        $this->load->model("positionsmodel","pm",true);
        $this->load->model("usertagsmodel","utm",true);
        $response_data = array();
        foreach ($this->pm->get($company_id) as $position) {
            $user_id = $position['user_id'];
            $resp = $this->utm->get_tags_with_count($user_id);
            $response_data = array_merge($response_data, $resp);
        }
        return $response_data;
    }
};
?>