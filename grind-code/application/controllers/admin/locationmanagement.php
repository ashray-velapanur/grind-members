<?

class LocationManagement extends CI_Controller {

	function __construct() {
		parent::__construct();
		
		$this->load->helper("url");
		$this->load->helper("form");
		$this->load->helper("html");
		$this->load->helper("nav_location");
		$this->load->helper("admin_layout");
	
	}
	
	public function getLocationsAndSpaces() {
		
		$this->load->model("locationmodel", "", true);
		$confrooms = $this->locationmodel->getConferenceRooms(1);
		
		if ($this->input->is_ajax_request()){
		
			echo json_encode($confrooms);
		} else { // call a view
		
			// we are assuming that for this function we are always routing to a chooser, but that could change in the future.
			$data["locationdata"] = $confrooms;
			$data["jsDataSet"] = json_encode($confrooms);
	
			$this->load->view("/conf_room/choose_conf_room.php",$data);
		}
	}
	public function roomchooser() {
		
		$this->load->model("locationmodel", "", true);
		$confrooms = $this->locationmodel->getConferenceRoomsByLocation(1);
		
			// we are assuming that for this function we are always routing to a chooser, but that could change in the future.
			$data["locations"] = $confrooms;
	
			// set the full javascript array as well
			$data["jsDataSet"] = json_encode($confrooms);
			
			$this->load->view("/conf_room/choose_conf_room.php",$data);

	}
	
	public function locations($id=NULL) {
	
		$this->load->model("locationmodel", "", true);
		$data["locations"] = $this->locationmodel->getLocations(0);

		$data["admin_only"] = true;

		display('admin/locations',$data,"Locations");
		
	}
	
	public function location() {
	
		$this->load->model("locationmodel", "", true);
		$this->load->model("lookuptablemodel", "", true);
		
		if ($this->uri->segment(4) != "") {
			$data["location"] = $this->locationmodel->getLocation($this->uri->segment(4));
		}
		
		$data["state_provs"] = $this->lookuptablemodel->getTableEntries("state_prov");
		$data["countries"] = $this->lookuptablemodel->getTableEntries("country");

		$title = (isset($data["location"]) ? "Location, " . $data["location"]->name : "New Location"); 
		$data["admin_only"] = true;

		display('admin/location',$data,$title);
		
	}

	public function addupdatelocation() {
		$this->load->model("locationmodel", "", true);
		if (ISSET($_POST["id"])) {
			$this->locationmodel->updateLocation($_POST);
		} else {
			$this->locationmodel->addLocation($_POST);
		}
		$this->locations();		
	}
	
	public function deletelocation() {
		$this->load->model("locationmodel", "", true);
		$this->locationmodel->deleteLocation($_POST);				
		$this->locations();	
	}
	
	public function locationspaces($id) {

		$this->load->model("locationmodel", "", true);		
		$data["location"] = $this->locationmodel->getLocation($id);

		$data["location"]->spaces = $this->locationmodel->getSpaces($data["location"]->id);
		foreach ($data["location"]->spaces as $space) {
			$space->level = 1;
			$space->spaces = $this->subSpaces($space);
		}
	
		$title = "Spaces at " . $data["location"]->name;
		$data["admin_only"] = true;
		
		display('admin/spaces',$data,$title);
	}
	
	public function locationspace() {
	
		$this->load->model("locationmodel", "", true);
		$this->load->model("lookuptablemodel", "", true);
		$locationId = $this->uri->segment(4);
		$spaceId = $this->uri->segment(5);
		if ($spaceId != "") {
			$data["space"] = $this->locationmodel->getSpace($spaceId);
		}
		
		$data["space_types"] = $this->lookuptablemodel->getTableEntries("space_type");
		$data["existing_spaces"] = $this->lookuptablemodel->getPseudoLookupTableEntries("space", "id", "name", "location_id = " . $locationId, "", true);
	
		$data["location"] = array("id" => $locationId, "name" => $locations[$locationId]);
			
		$title = (isset($data["space"]) ? "Space, " . $data["space"]->name . " in " . $data["location"]["name"] : "New Space in " . $data["location"]["name"]);

		$data["admin_only"] = true;
		
		display('admin/space',$data,$title);

		
	}
	
	public function addupdatespace() {
		$this->load->model("locationmodel", "", true);
		if (ISSET($_POST["id"])) {
			$this->locationmodel->updateSpace($_POST);
		} else {
			$this->locationmodel->addSpace($_POST);
		}
				
		$this->locationspaces($_POST["location_id"]);
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

	public function deletespace() {
		$this->load->model("locationmodel", "", true);
		$this->locationmodel->deleteSpace($_POST);				
		$this->locationsspaces($_POST["location_id"]);
	}

	
    public function whoshere() {
        $locationId = $this->uri->segment(4);
        $this->db->where("id", $locationId);
        $query = $this->db->get("location");
        $locations = $query->result();
        $locationName = count($locations) ? $locations[0]->name : "";
        $query->free_result();
        $this->load->model("locationmodel", "", true);
        $data["signedInMembers"] = $this->locationmodel->whosHere($locationId, 200);
		$title = "Members checked in today at " . $locationName;
		$data["admin_only"] = true;
		
		display('admin/whoshere',$data,$title);

    }
    
	public function updateaccesspricing() {
		$this->load->model("locationmodel", "", true);
		$this->locationmodel->updateAccessPricing($_POST);
				
		redirect(g_url("/admin/index/dashboard"));
	}

	public function cobot_space() {
		$this->load->view("/admin/add_space.php");
	}

	public function add_update_space() {
		error_log("In add_update_space");
		if(isset($_POST["submit"])) {
			$cobot_id = $_POST["cobot_id"];
			$tmpName = $_FILES['fileToUpload']['tmp_name'];
			$fp = fopen($tmpName, 'r');
			$data = fread($fp, filesize($tmpName));
			$data = addslashes($data);
			fclose($fp);
			$sql = "INSERT INTO cobot_spaces";
			$sql .= "(id, image) VALUES ('$cobot_id', '$data')";
			try {
				if ($this->db->query($sql) === TRUE) {
					echo "Record created/updated successfully";
				} else {
					echo "Error: " . $sql . "<br>" . $this->db->error;
				}
			} catch (Exception $e) {
			    error_log('Caught exception: ',  $e->getMessage(), "\n");
			}
		}
	}
}

?>