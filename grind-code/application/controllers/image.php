<?

class Image extends CI_Controller {

	public function get() {
		error_log('In image get');
		$id = (isset($_GET['id']) && is_numeric($_GET['id'])) ? intval($_GET['id']) : 0;
		$this->db->where("id", $id);
	    $query = $this->db->get('image');
	    $image = current($query->result());
	    if($image) {
	    	error_log('Got image');
	    	error_log($image->type);
	    	error_log($image->content);
	    	header('Content-Type: '.$image->type);
			echo $image->content;
	    }
	}
}
?>