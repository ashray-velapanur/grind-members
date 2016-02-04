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

	// CREATE TABLE `cobot_spaces` (`id` varchar(300) NOT NULL, `image` varchar(300) NOT NULL, `capacity` int(11) NOT NULL, `lat` varchar(50) NOT NULL, `lon` varchar(50) NOT NULL, `address` varchar(300) NOT NULL, `rate` float NOT NULL, PRIMARY KEY (`id`))
	public function add_update_space() {
		error_log("In add_update_space");
		if(isset($_POST["submit"])) {
			$cobot_id = $_POST["cobot_id"];
			$capacity = $_POST["capacity"];
			if(!$capacity) {
				$capacity = 0;
			}
			$imgName = $_FILES['fileToUpload']['name'];
			$lat = $_POST["latitude"];
			$long = $_POST["longitude"];
			$address_street = $_POST["address-street"];
			$address_city = $_POST["address-city"];
			$address_state = $_POST["address-state"];
			$address_country = $_POST["address-country"];
			$address_zip = $_POST["address-zip"];
			$address = $address_street.' '.$address_city.' '.$address_state.' '.$address_zip;
			$address = trim($address);
			$rate = $_POST["rate"];
			if(!$rate) {
				$rate = 0.0;
			}
			$sql = "INSERT INTO cobot_spaces";
			$sql .= "(id, image, capacity, lat, lon, address, rate) VALUES ('$cobot_id', '$imgName', $capacity, '$lat', '$long', '$address', $rate) ";
			$sql .= " ON DUPLICATE KEY UPDATE ";
			$comma = " ";
			if($imgName) {
				$sql = $sql.$comma." image = '".$imgName."'";
				$comma = " , ";
			}
			if($capacity) {
				$sql = $sql.$comma." capacity = ".$capacity;
				$comma = " , ";
			}
			if($lat) {
				$sql = $sql.$comma." lat = '".$lat."'";
				$comma = " , ";
			}
			if($long) {
				$sql = $sql.$comma." lon = '".$long."'";
				$comma = " , ";
			}
			if($address) {
				$sql = $sql.$comma." address = '".$address."'";
				$comma = " , ";
			}
			if($rate) {
				$sql = $sql.$comma." rate = ".$rate;
				$comma = " , ";
			}
			try {
				error_log($sql);
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

	public function spaces_get() {
		$space_data = array();
		$query = $this->db->get("cobot_spaces");
        $spaces = $query->result();
        foreach ($spaces as $space_arr) {
        	$space = (array)$space_arr;
        	$space_id = $space['id'];
        	$space_img_src = 'data:image/jpeg;base64,'.base64_encode( $space['image'] );
        	$capacity = $space['capacity'];

        	$curl = curl_init();
        	$url = 'https://www.cobot.me/api/spaces/'.$space_id;
			$data = array();
        	if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			$result = (array)json_decode(curl_exec($curl));
			curl_close($curl);
			$description = $result['description'];
			$name = $result['name'];

			$spacedata = array(
				'id' => $space_id,
        		'img_src' => 'data:image/jpeg;base64,'.base64_encode( $space['image'] ),
        		'description' => $description,
        		'name' => $name,
        		'capacity' => $capacity
        	);
        	array_push($space_data, $spacedata);
        }
        $data = array('space_data' => $space_data);
        return $data;
        // $this->load->view('/admin/show_spaces.php', $data);
	}

	public function cobot_resource() {
		$query = $this->db->get("cobot_spaces");
        $spaces = $query->result();
        $data = array();
        $spacedata = array();
        foreach ($spaces as $space_arr) {
        	$space = (array)$space_arr;
        	$space_id = $space['id'];
        	array_push($spacedata, $space_id);
        }
        $data['spacedata'] = $spacedata;
		$this->load->view("/admin/add_resource.php", $data);
	}

	public function add_update_resource() {
		error_log("In add_update_resource");
		if(isset($_POST["submit"])) {
			$cobot_resource_id = $_POST["cobot_resource_id"];
			$space_id = $_POST["space_id"];
			$tmpName = $_FILES['fileToUpload']['tmp_name'];
			$fp = fopen($tmpName, 'r');
			$data = fread($fp, filesize($tmpName));
			$data = addslashes($data);
			fclose($fp);
			$sql = "INSERT INTO cobot_resources";
			$sql .= "(id, space_id, image) VALUES ('$cobot_resource_id', '$space_id', '$data')";
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

	public function resources_get() {
		$space_id = $_GET['space_id'];
		$resource_data = array();

		$curl = curl_init();
		$url = 'https://'.$space_id.'.cobot.me/api/resources';
		$data = array(
			'access_token' => '79af7d71ab964cf5e34f8eec64d175533bf5c924bf4d1133ff01aed76c6017d8'
		);
    	if ($data)
            $url = sprintf("%s?%s", $url, http_build_query($data));
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($curl);
		$result_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		$cobot_resources = array();
		if($result_code == 200) {
			$result = (array)json_decode($result);
			foreach ($result as $resource) {
				$resource = (array)$resource;
				$cobot_resources[$resource['id']] = $resource;
			}
		}

		$this->db->where("space_id", $space_id);
		$query = $this->db->get('cobot_resources');
		$resources = $query->result();
		foreach ($resources as $resource_arr) {
			$resource = (array)$resource_arr;
			$resource_id = $resource['id'];
			$resource_img_src = 'data:image/jpeg;base64,'.base64_encode( $resource['image'] );
			$cobot_resource = $cobot_resources[$resource_id];
			$description = $cobot_resource['description'];
			$capacity = $cobot_resource['capacity'];
			$rate = $cobot_resource['price_per_hour'];
			$resourcedata = array(
				'id' => $resource_id,
        		'img_src' => $resource_img_src,
        		'description' => $description,
        		'capacity' => $capacity,
        		'rate' => $rate
        	);
        	error_log(json_encode($resourcedata));
        	array_push($resource_data, $resourcedata);
		}
		$data = array('resource_data' => $resource_data);
		return $data;
        //$this->load->view('/admin/show_resources.php', $data);
	}
}

?>