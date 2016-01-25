<?
class Search extends CI_Controller {
	public function search(){
		$q = $_GET['q'];
		$query = mysql_query(sprintf("
									(select id, first_name as name, 'user' as type from user where first_name like '%%%s%%')
									union
									(select id, name, 'company' as type from company where name like '%%%s%%')
									union
									(select id, name, 'event' as type from event where name like '%%%s%%')
									union
									(select id, title as name, 'job' as type from jobs where name title '%%%s%%')
									", $q, $q, $q));
		$response = array();
		while($row = mysql_fetch_assoc($query)) {
			array_push($response, $row);
		}
		var_dump($response);
	}
}
?>