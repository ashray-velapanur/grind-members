<?

/**
 * Member Import/Export Controller
 * 
 * Manages tasks associated with importing(future) and exporting user data
 * 
 * @author joshcampbell
 * @params
 */


class Importexport extends CI_Controller {

	function __construct() {
		parent::__construct();
		
	}

	/**
	 * get_members
	 * 
	 * retrieves a set of user data and returns as JSON
	 * 
	 * @author magicandmight
	 * @params
	 */	
	public function get_members(){
	
		$this->load->model('/members/membermodel','mm',true);
		$result = $this->mm->export_members();
		//echo serialize($result);
		$result = json_encode($result);
		echo $result;
		return $result;
		
	}
}