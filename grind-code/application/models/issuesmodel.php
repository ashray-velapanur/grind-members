<?
include_once APPPATH . 'libraries/utilities.php';
include_once APPPATH . 'libraries/enumerations.php';
include_once APPPATH . 'libraries/constants.php';

class IssuesModel extends CI_Model {

    public function getMemberIssues($onlyOpen = true, $offset = 0, $limit = 0, $userId = 0) {
		$sql = "
			select
				issues.id,
				case when user.id is null or user.id = 0 then '--' else concat(user.first_name, ' ', user.last_name) end user_name,
				case type_code when 0 then 'General' when 1 then 'Sign-In' when 2 then 'Billing' else 'General' end as type,
				issues.message,
				issues.date
			from 
				issues
				left outer join user on user.id = issues.user_id
			where 
				1=1
				and issues.is_closed = 0
		";
		if ($onlyOpen) $sql .= " and issues.is_closed = 0";
		if ($userId) $sql .= " and user.id = $userId";
		
		//set the order
		$sql .= " order by issues.date desc";
		
		if ($limit > 0) $sql .= " limit $offset, $limit";

        $query = $this->db->query($sql);

		//echo $this->db->last_query();	
        return $query->result();
    }

    public function logMemberIssue($userId, $message, $issueType=NULL) {
    	$issueType = (isset($issueType)) ? $issueType : MemberIssueType::GENERAL;
    	error_log($issueType,0);
        $this->db->insert("issues", array("user_id" => $userId, "type_code" => $issueType, "message" => $message, "date" => date(DATE_ISO8601)));
		return $this->db->insert_id();
    }

    public function closeMemberIssue($issueId) {
        $this->db->where("id", $issueId);
        $this->db->update("issues", array("is_closed" => 1));
    }

}

?>