<?
require(APPPATH.'/config/cobot.php');
require(APPPATH.'/libraries/utilities.php');
require(APPPATH.'/controllers/admin/locationsetup.php');

class LocationManagement extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->output->enable_profiler(TRUE);
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
			if($cobot_id) {
				$image = $_FILES['fileToUpload'];
				$this->load->model("imagesmodel","im",true);
				$image_id = $this->im->save_image($image);
				$main_area_id = $_POST["main_area_id"];
				$capacity = $_POST["capacity"];
				if(!$capacity) {
					$capacity = 0;
				}
				$lat = $_POST["latitude"];
				$long = $_POST["longitude"];
				$address_street = $_POST["address-street"];
				$address_city = $_POST["address-city"];
				$address_state = $_POST["address-state"];
				$address_country = $_POST["address-country"];
				$address_zip = $_POST["address-zip"];
				$address_1 = trim($address_street);
				$address_2 = trim($address_city.' '.$address_state.' '.$address_zip);
				$rate = $_POST["rate"];
				if(!$rate) {
					$rate = 0.0;
				}
				$name = $_POST["name"];
				$description = $_POST["description"];
				$space = array(
					'id' => $cobot_id,
					'imgName' => $image_id,
					'capacity' => $capacity,
					'lat' => $lat,
					'long' => $long,
					'address_1' => $address_1,
					'address_2' => $address_2,
					'rate' => $rate,
					'name' => $name,
					'description' => $description,
					'main_area_id' => $main_area_id
				);
				$util = new utilities;
				$this->load->model("locationmodel","lm",true);
				$this->lm->add_space_and_resources($cobot_id, $space);
				$this->db->where("space_id", $cobot_id);
				$query = $this->db->get("cobot_resources");
				$resources = $query->result();
				$data = array();
				$data['resources'] = $resources;
				$data['space_id'] = $cobot_id;
				$this->load->view("/admin/add_space_resources.php", $data);
			} else {
				echo "Specify a cobot ID for the space";
			}
		}
	}

	public function update_space_images() {
		$query = $this->db->get("cobot_spaces");
		$spaces = $query->result();
		$data = array();
		$data['spaces'] = $spaces;
		$this->load->view("/admin/add_space_images.php", $data);
	}

	public function add_space_images() {
		error_log("In add_space_images");
		if(isset($_POST["submit"])) {
			$this->load->model("imagesmodel","im",true);
			
			$query = $this->db->get("cobot_spaces");
			$spaces = $query->result();

			foreach ($spaces as $space) {
				$space_id = $space->id;
				$image = $_FILES['image'.$space_id];
				if(!$image['error']) {
					$image_id = $this->im->save_image($image);
					if(!$image_id || $image_id == NULL) {
						error_log('Error uploading image');
					} else {
						$this->im->delete_image($space->image);
						$sql = "UPDATE cobot_spaces SET image = '".$image_id."' WHERE id = '".$space_id."'";
						error_log($sql);
						try {
							if ($this->db->query($sql) === TRUE) {
								echo "Space updated successfully";
							} else {
								echo "Error: " . $sql . "<br>" . $this->db->error;
							}
						} catch (Exception $e) {
						    error_log('Caught exception: ',  $e->getMessage(), "\n");
						}
					}
				}
			}
		}
		$util = new utilities;
		$util->redirect(ROOTMEMBERPATH.'grind-code/index.php/admin/locationmanagement/update_space_images');
	}

	public function update_space_resources() {
		$space_id = $_GET["space_id"];
		$this->db->where("space_id", $space_id);
		$query = $this->db->get("cobot_resources");
		$resources = $query->result();
		$data = array();
		$data['resources'] = $resources;
		$data['space_id'] = $space_id;
		$this->load->view("/admin/add_space_resources.php", $data);
	}

	public function add_space_resources() {
		error_log("In add_space_resources");
		if(isset($_POST["submit"])) {
			$space_id = $_POST["space_id"];
			$this->load->model("imagesmodel","im",true);
			
			$this->db->where("space_id", $space_id);
			$query = $this->db->get("cobot_resources");
			$resources = $query->result();

			foreach ($resources as $resource) {
				$resource_id = $resource->id;
				$image_id = $resource->image;
				$capacity = $_POST['capacity'.$resource_id];
				if(!$capacity) {
					$capacity = 10;
				}
				$image = $_FILES['image'.$resource_id];
				if(!$image['error']) {
					$image_id = $this->im->save_image($image);
					if(!$image_id || $image_id == NULL) {
						error_log('Error uploading image');
					} else {
						$this->im->delete_image($resource->image);
						$sql = "UPDATE cobot_resources SET capacity = ".$capacity.", image = '".$image_id."' WHERE space_id = '".$space_id."' AND id='".$resource_id."'";
						error_log($sql);
						try {
							if ($this->db->query($sql) === TRUE) {
								echo "Resource updated successfully";
							} else {
								echo "Error: " . $sql . "<br>" . $this->db->error;
							}
						} catch (Exception $e) {
						    error_log('Caught exception: ',  $e->getMessage(), "\n");
						}
					}
				}
			}
			$util = new utilities;
			$util->redirect(ROOTMEMBERPATH.'grind-code/index.php/admin/locationmanagement/update_space_resources?space_id='.$space_id);
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
			$imgName = $_FILES['fileToUpload']['name'];
			$sql = "INSERT INTO cobot_resources";
			$sql .= "(id, space_id, image) VALUES ('$cobot_resource_id', '$space_id', '$imgName')";
			if($imgName) {
				$sql .= " ON DUPLICATE KEY UPDATE ";
				$sql .= " image = '".$imgName."'";
			}
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
	}

	public function add_bubble() {
		error_log("In add_bubble");
		if(isset($_POST["submit"])) {
			$type = $_POST["type"];
			$id = $_POST[$type];
			error_log($type.' '.$id);
			if($id && $type) {
				$title = $_POST["title"];
				$rank = $_POST["rank"];
				$image = $_FILES['fileToUpload'];
				$this->load->model("imagesmodel","im",true);
    			$image_id = $this->im->save_image($image);
				$sql = "INSERT INTO bubbles (title, image_id, rank, type, id) VALUES ('$title', '$image_id', $rank, '$type', '$id')";
				error_log($sql);
				if ($this->db->query($sql) === TRUE) {
					error_log("New bubble created successfully");
				} else {
					error_log("Error: " . $sql . "<br>" . $this->db->error);
				}
			}
		}
		$util = new utilities;
		$environment = $util->redirect(ROOTMEMBERPATH.'grind-code/index.php/admin/locationmanagement/show_bubbles');
	}

	public function delete_bubble() {
		error_log("In delete_bubble");
		$type = $_GET["type"];
		$id = $_GET["id"];
		error_log($type.' '.$id);
		if($id && $type) {
			$sql = "SELECT image_id FROM bubbles WHERE type='".$type."' and id='".$id."'";
			error_log($sql);
			$query = $this->db->query($sql);
			$bubbles = $query->result();
			error_log(json_encode($bubbles));
			$bubble = current($bubbles);
			if($bubble) {
				$this->load->model("imagesmodel","im",true);
    			$this->im->delete_image($bubble->image_id);
			}
			$sql = "DELETE FROM bubbles WHERE type='".$type."' and id='".$id."'";
			error_log($sql);
			if ($this->db->query($sql) === TRUE) {
				error_log("Deleted bubble successfully");
			} else {
				error_log("Error: " . $sql . "<br>" . $this->db->error);
			}
		}
		$util = new utilities;
		$environment = $util->redirect(ROOTMEMBERPATH.'grind-code/index.php/admin/locationmanagement/show_bubbles');
	}

	public function show_bubbles() {
		error_log("In show_bubbles");
		$this->db->order_by('rank', 'asc');
		$query = $this->db->get('bubbles');
    	$results = $query->result();
    	$query = $this->db->get('events');
    	$events = $query->result();
    	$query = $this->db->get('user');
    	$users = $query->result();
    	$query = $this->db->get('company');
    	$companies = $query->result();
    	$data = array('bubbles'=>$results, 'types'=>array('user','event', 'company'), 'events'=>$events, 'users'=>$users, 'companies'=>$companies);
    	$this->load->view("/admin/show_bubbles.php", $data);
	}
}

?>