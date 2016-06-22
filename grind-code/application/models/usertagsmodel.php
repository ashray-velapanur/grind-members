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

	function get_users($tag_id, $limit=NULL, $offset=NULL) {
        $response_data = array();
        $sql = "select ".
        			"user.id, user.first_name, user.last_name, ".
        			"third_party_user.profile_picture as profile_picture, positions.designation as designation, company.name as company ".
        		"from ".
        			"user ".
        			"join user_tags on user_tags.user_id = user.id ".
        			"join third_party_user on third_party_user.user_id = user.id and third_party_user.network='linkedin'".
        			"join positions on positions.company_id = user.company_id and positions.user_id = user.id ".
        			"join company on user.company_id = company.id ".
        		"where user_tags.tag_id = ".$tag_id;
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

	function get_companies($tag_id, $limit=NULL, $offset=NULL) {
        $response_data = array();
        $sql = "select ".
        			"company.id, company.name, company.logo_url, company.header, company.description ".
        		"from ".
        			"company ".
        			"left outer join positions on positions.company_id = company.id ".
        			"left outer join user_tags on user_tags.user_id = positions.user_id ".
        		"where user_tags.tag_id = ".$tag_id;
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
};
?>