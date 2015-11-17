<?

class Index extends CI_Controller {

    function __construct() {
            parent::__construct();
            
            $this->load->helper("url");
            $this->load->helper("form");
            $this->load->helper("cookie");
    		$this->load->helper("html");
			$this->load->helper("nav_location");
			$this->load->helper("admin_layout");
    }
    
    function index() {
        $this->dashboard();        
    }
    
    function dashboard() {

		$this->load->model("locationmodel");
		$this->load->model("billing/planmodel",'',true);
        $this->load->model("issuesmodel");
		$this->load->model("lookuptablemodel");

        /*****************************************
         * CHECKIN MODULE
         ****************************************/
        $locationId = $this->locationmodel->getCurrentLocation();
        //echo "locationId = $locationId";
        $data["location"] = $this->locationmodel->getLocation($locationId);
        $data["signedInMembers"] = $this->locationmodel->whosHere($locationId, 12);

        /*****************************************
         * ISSUES MODULE
         ****************************************/
        $data["issues"] = $this->issuesmodel->getMemberIssues(true, 0, 5);

        /*****************************************
         * CONFERENCE ROOMS MODULE
         ****************************************/
		$data["location"]->conferencerooms = $this->locationmodel->getConferenceRooms(true, $locationId);

        /*****************************************
         * PRICING MODULE
         ****************************************/
        $data["accesspricing"] = $this->locationmodel->getAccessPricing();
		$data["plans"] = $this->planmodel->get_plans();

        
        $data["admin_only"] = true;
		display('admin/dashboard.php',$data,"Front Desk Dashboard");
    }

    public function updatenavlocation() {
        $locationId = $this->input->post("locationnav");
        //echo "\$locationId = $locationId<br />";
        $this->input->set_cookie("adminlocationid", $locationId, 1*(365*24*60*60));
        redirect("admin/index/dashboard");
    }

	private function subSpaces($space) {
		//echo "Attempting to get subspaces for space '$space->name'<br />\n";
		$this->load->model("locationmodel", "", true);
		$subspaces = $this->locationmodel->getSpaces($space->location_id, $space->id);
		foreach ($subspaces as $subspace) {
			$subspace->level = $space->level + 1;
			$subspace->spaces = $this->subSpaces($subspace); 
		}
		return $subspaces;
	}

}