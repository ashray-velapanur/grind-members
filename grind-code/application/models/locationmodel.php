<?
include_once APPPATH . 'libraries/utilities.php';
include_once APPPATH . 'libraries/enumerations.php';
include_once APPPATH . 'libraries/constants.php';
require(APPPATH.'/config/cobot.php');
require(APPPATH.'/controllers/admin/spaces_dict.php');

class LocationModel extends CI_Model {


	function getLocations($returnOnlyOpen = 1) {
	
		$sql = "
			select
				location.*, state_prov_lu.description as state_prov, country_lu.description as country
			from 
				location 
				left outer join state_prov_lu on state_prov_lu.id = location.state_prov_luid
				left outer join country_lu on country_lu.id = location.country_luid
		";
		if ($returnOnlyOpen == 1) $this->db->where("location.is_closed", 0);
		$query = $this->db->query($sql);
		$locations = $query->result();
		foreach($locations as $location) {

			$location->full_address_w_linebreaks = $this->formatAddress(
											$location->addr_1,
											$location->addr_2,
											$location->cross_street,
											$location->city,
											$location->state_prov,
											$location->post_code,
											$location->country,
											true
										);

			$location->full_address = $this->formatAddress(
											$location->addr_1,
											$location->addr_2,
											$location->cross_street,
											$location->city,
											$location->state_prov,
											$location->post_code,
											$location->country,
											false
										);

		}

		return $locations;
	}

	function getLocation($id) {
		
		$sql = "
			select
				location.*, state_prov_lu.description as state_prov, country_lu.description as country
			from 
				location 
				left outer join state_prov_lu on state_prov_lu.id = location.state_prov_luid
				left outer join country_lu on country_lu.id = location.country_luid
			where location.id = $id
		";
		$query = $this->db->query($sql);
		$locations = $query->result();

		foreach($locations as $location) {
			$location->full_address_w_linebreaks = $this->formatAddress(
											$location->addr_1,
											$location->addr_2,
											$location->cross_street,
											$location->city,
											$location->state_prov,
											$location->post_code,
											$location->country,
											true
										);
	
			$location->full_address = $this->formatAddress(
											$location->addr_1,
											$location->addr_2,
											$location->cross_street,
											$location->city,
											$location->state_prov,
											$location->post_code,
											$location->country,
											false
										);
		}
		
		$retval = null;		
		if (count($locations) > 0) {
			$retval = $locations[0];
		}
		
		return $retval;
	}
	function getLocationId($wlc_ip) {
		$id = 0;
			$sql = "SELECT id FROM location WHERE wlc_ip='".$wlc_ip."';";
			$query = $this->db->query($sql);
		$results = $query->result();
		if (count($results) > 0) {
			$id = $results[0]->id;
		}
		return $id;
	}
	
	function getDefaultLocationId() {
		$id = 0;
		$this->db->where("name like '%Park Ave%'");
		$this->db->where("is_closed", "0");
		$query = $this->db->get("location");
		$results = $query->result();
		if (count($results) > 0) {
			$id = $results[0]->id;
		}
		return $id;
	}

	function getCurrentLocation($locationId = 0) {
		// first check if we have being passed in
		// then check the admin cookie for "adminlocationid"
		// if still no location, then use the default location
		$retval = 0;
		$cookieLocationId = $this->input->cookie("adminlocationid", 0);
		//echo "cookieLocationId = $cookieLocationId";
		if($locationId > 0) {
			if($locationId != $cookieLocationId) {
				$this->input->set_cookie("adminlocationid", $locationId, 1*(365*24*60*60));
			}
			$retval = $locationId;
		} elseif($cookieLocationId > 0) {
			$retval = $cookieLocationId;
		} else {
			$retval = $this->getDefaultLocationId();
		}
		
		return $retval;
	}
	
	function whosHere($locationId) {
		$results = null;
        $sql = "select * from v_checkins_today where location_id = '".$locationId."';";
        
        $query = $this->db->query($sql);
        $results = $query->result();
        $query->free_result();

		return $results;
	}
	
	function addLocation($data) {
		$this->db->insert("location", $data);
		//echo $this->db->last_query() . "<br />";
	}

	function updateLocation($data) {
		$this->db->where("id", $data["id"]);
		$this->db->update("location", $data);
		//echo $this->db->last_query() . "<br />";
	}
	
	function deleteLocation($data) {
		$this->db->delete("location", array("id" => $data["id"]));
		$this->db->delete("space", array("location_id" => $data["id"]));
		$this->db->delete("signin_sheet", array("location_id" => $data["id"]));
	}
	
	protected function formatAddress($addr1, $addr2, $crossStreet, $city, $state, $postCode, $country, $withHTMLLineBreaks = true) {
		$fullAddr = $addr1;
		if ($addr2 != "") $fullAddr .= (", " . $addr2); 
		if ($crossStreet != "" && $withHTMLLineBreaks) $fullAddr .= (" (" . $crossStreet . ")");
		$fullAddr .= ($withHTMLLineBreaks ? "<br />" : ", ");
		$fullAddr .= ($city . ", " . $state);
		$fullAddr .= ($withHTMLLineBreaks ? "<br />" : " ");
		$fullAddr .= $postCode;
		$fullAddr .= ($withHTMLLineBreaks ? "<br />" : " ");
		$fullAddr .= $country;
		
		return $fullAddr;
	}
	
	function getConferenceRooms($onlyForOpenLocations = 0, $specificLocation = 0) {
		$sql = "
			SELECT
				l.id as location_id, l.name as location_name,
				s.name as space_name, s.id as space_id, s.calendar_link, s.capacity
			FROM
				space s inner join location l on s.location_id = l.id
			where
				s.space_type_luid = 1
				and s.is_inactive = 0
				and s.is_bookable = 1
				and 1=1
				and 2=2
			order by
				l.name, s.name
		";
		if($onlyForOpenLocations) $sql = str_replace("1=1", "l.is_closed = 0", $sql);
		if($specificLocation > 0) $sql = str_replace("2=2", "l.id = " . $specificLocation, $sql);;

		$query = $this->db->query($sql);

		$confRooms = $query->result();
		return $confRooms;
	}
	
	function getConferenceRoomsByLocation($onlyForOpenLocations = 0, $specificLocation = 0) {
			$sql = "
				SELECT
					l.id as location_id, l.name as location_name,
					s.name as space_name, s.id as space_id, s.calendar_link, s.capacity
				FROM
					space s inner join location l on s.location_id = l.id
				where
					s.space_type_luid = 1
					and s.is_inactive = 0
					and s.is_bookable = 1
					and 1=1
					and 2=2
				order by
					l.name, s.name DESC
			";
			
			if($onlyForOpenLocations) $sql = str_replace("1=1", "l.is_closed = 0", $sql);
			if($specificLocation > 0) $sql = str_replace("2=2", "l.id = " . $specificLocation, $sql);;
	
			$query = $this->db->query($sql);
			
			
	
			$confRooms = $query->result();
			return $confRooms;
		}

	function getSpaces($location, $parentSpace = 0) {
		$this->db->where("location_id", $location);
		$this->db->where("parent_space_id", $parentSpace);
		$query = $this->db->get("space");
		$spaces = $query->result();
		
		return $spaces;
	}

	function getSpace($id = 0) {
		$sql = "
			select
				space.*, 
				hourprice.id as pricing_hourly_id, dayprice.id as pricing_daily_id, monthprice.id as pricing_monthly_id,
				hourprice.stock_price as pricing_hourly, dayprice.stock_price as pricing_daily, monthprice.stock_price as pricing_monthly 
			from
				space
				left outer join pricing as hourprice on hourprice.product_id = space.id and hourprice.period_code = 1 and hourprice.product_type_code = 0 and (hourprice.end_date > now() or hourprice.end_date is null)
				left outer join pricing as dayprice on dayprice.product_id = space.id and dayprice.period_code = 2 and dayprice.product_type_code = 0 and (dayprice.end_date > now() or dayprice.end_date is null)
				left outer join pricing as monthprice on monthprice.product_id = space.id and monthprice.period_code = 3 and monthprice.product_type_code = 0 and (monthprice.end_date > now() or monthprice.end_date is null)
			where
				space.id = ?
		";
		$query = $this->db->query($sql, array($id));
		$spaces = $query->result();
		
		$retval = null;
		if (count($spaces) > 0) $retval = $spaces[0];
		
		return $retval;
	}
	

	function addSpace($data) {
		
		// separate out the the space data from the pricing data
		$spacedata["name"] = $data["name"];
		$spacedata["description"] = $data["description"];
		$spacedata["location_id"] = $data["location_id"];
		$spacedata["is_bookable"] = $data["is_bookable"];
		$spacedata["space_type_luid"] = $data["space_type_luid"];
		$spacedata["parent_space_id"] = $data["parent_space_id"];
		$spacedata["capacity"] = $data["capacity"];
		$spacedata["is_inactive"] = $data["is_inactive"];

		$this->db->insert("space", $spacedata);
		//echo $this->db->last_query() . "<br />";
		// now get the ID for the space just inserted
		$spaceId = $this->db->insert_id();
		
		if ($data["pricing_hourly"] != "" && $data["pricing_hourly"] > 0) {
			$pricingdata["product_id"] = $spaceId;
			$pricingdata["product_type_code"] = ProductType::SPACE;
			$pricingdata["period_code"] = PricingPeriod::HOURLY;
			$pricingdata["start_date"] = date("Y-m-d");
			$pricingdata["stock_price"] = $data["pricing_hourly"];
			
			$this->db->insert("pricing", $pricingdata);
		}
		
		if ($data["pricing_daily"] != "" && $data["pricing_daily"] > 0) {
			$pricingdata["product_id"] = $spaceId;
			$pricingdata["product_type_code"] = ProductType::SPACE;
			$pricingdata["period_code"] = PricingPeriod::DAILY;
			$pricingdata["start_date"] = date("Y-m-d");
			$pricingdata["stock_price"] = $data["pricing_daily"];
			
			$this->db->insert("pricing", $pricingdata);
		}
		
		if ($data["pricing_monthly"] != "" && $data["pricing_monthly"] > 0) {
			$pricingdata["product_id"] = $data["id"];
			$pricingdata["product_type_code"] = ProductType::SPACE;
			$pricingdata["period_code"] = PricingPeriod::MONTHLY;
			$pricingdata["start_date"] = date("Y-m-d");
			$pricingdata["stock_price"] = $data["pricing_monthly"];
			
			$this->db->insert("pricing", $pricingdata);
		}
	}

	function updateSpace($data) {

		// separate out the the space data from the pricing data
		$spacedata["id"] = $data["id"];
		$spacedata["name"] = $data["name"];
		$spacedata["description"] = $data["description"];
		$spacedata["location_id"] = $data["location_id"];
		$spacedata["is_bookable"] = $data["is_bookable"];
		$spacedata["space_type_luid"] = $data["space_type_luid"];
		$spacedata["parent_space_id"] = $data["parent_space_id"];
		$spacedata["capacity"] = $data["capacity"];
		$spacedata["is_inactive"] = $data["is_inactive"];
		$spacedata["calendar_link"] = $data["calendar_link"];

		$this->db->where("id", $spacedata["id"]);
		$this->db->update("space", $spacedata);
		//echo $this->db->last_query() . "<br />";
		
		if ($data["pricing_hourly"] == "" || $data["pricing_hourly"] <= 0) {
			$pricingdata["product_id"] = $data["id"];
			$pricingdata["product_type_code"] = ProductType::SPACE;
			$pricingdata["period_code"] = PricingPeriod::HOURLY;
			$pricingdata["start_date"] = date("Y-m-d");
			$pricingdata["stock_price"] = $data["pricing_hourly"];

			$this->db->where(array("product_id" => $data["id"], "product_type_code" => ProductType::SPACE, "period_code" => PricingPeriod::HOURLY));
			$this->db->where("end_date is null");
			$this->db->update("pricing", array("end_date" => date("Y-m-d")));

		} else {
			//echo "have hourly pricing<br />";
			$pricingdata["product_id"] = $data["id"];
			$pricingdata["product_type_code"] = ProductType::SPACE;
			$pricingdata["period_code"] = PricingPeriod::HOURLY;
			$pricingdata["start_date"] = date("Y-m-d");
			$pricingdata["stock_price"] = $data["pricing_hourly"];

			$this->db->where(array("product_id" => $data["id"], "product_type_code" => ProductType::SPACE, "period_code" => PricingPeriod::HOURLY));
			$this->db->where("stock_price <> ", $data["pricing_hourly"]);
			$this->db->where("end_date is null");
			$this->db->update("pricing", array("end_date" => date("Y-m-d")));
			//echo $this->db->last_query() . "<br />";
			
			// if there is not a price for the space, then insert one
			if(!$this->priceExistsForSpace($data["id"], PricingPeriod::HOURLY)) {
				$this->db->insert("pricing", $pricingdata);
				//echo $this->db->last_query() . "<br />";
			}
		}
		
		if ($data["pricing_daily"] == "" || $data["pricing_daily"] <= 0) {
			$pricingdata["product_id"] = $data["id"];
			$pricingdata["product_type_code"] = ProductType::SPACE;
			$pricingdata["period_code"] = PricingPeriod::DAILY;
			$pricingdata["start_date"] = date("Y-m-d");
			$pricingdata["stock_price"] = $data["pricing_daily"];

			$this->db->where(array("product_id" => $data["id"], "product_type_code" => ProductType::SPACE, "period_code" => PricingPeriod::DAILY));
			$this->db->where("end_date is null");
			$this->db->update("pricing", array("end_date" => date("Y-m-d")));

		} else {
			$pricingdata["product_id"] = $data["id"];
			$pricingdata["product_type_code"] = ProductType::SPACE;
			$pricingdata["period_code"] = PricingPeriod::DAILY;
			$pricingdata["start_date"] = date("Y-m-d");
			$pricingdata["stock_price"] = $data["pricing_daily"];

			$this->db->where(array("product_id" => $data["id"], "product_type_code" => ProductType::SPACE, "period_code" => PricingPeriod::DAILY));
			$this->db->where("stock_price <> ", $data["pricing_daily"]);
			$this->db->where("end_date is null");
			$this->db->update("pricing", array("end_date" => date("Y-m-d")));
			
			// if there is not a price for the space, then insert one
			if(!$this->priceExistsForSpace($data["id"], PricingPeriod::DAILY)) {
				$this->db->insert("pricing", $pricingdata);
			}
		}

		if ($data["pricing_monthly"] == "" || $data["pricing_monthly"] <= 0) {
			$pricingdata["product_id"] = $data["id"];
			$pricingdata["product_type_code"] = ProductType::SPACE;
			$pricingdata["period_code"] = PricingPeriod::MONTHLY;
			$pricingdata["start_date"] = date("Y-m-d");
			$pricingdata["stock_price"] = $data["pricing_monthly"];

			$this->db->where(array("product_id" => $data["id"], "product_type_code" => ProductType::SPACE, "period_code" => PricingPeriod::MONTHLY));
			$this->db->where("end_date is null");
			$this->db->update("pricing", array("end_date" => date("Y-m-d")));

		} else {
			$pricingdata["product_id"] = $data["id"];
			$pricingdata["product_type_code"] = ProductType::SPACE;
			$pricingdata["period_code"] = PricingPeriod::MONTHLY;
			$pricingdata["start_date"] = date("Y-m-d");
			$pricingdata["stock_price"] = $data["pricing_monthly"];

			$this->db->where(array("product_id" => $data["id"], "product_type_code" => ProductType::SPACE, "period_code" => PricingPeriod::MONTHLY));
			$this->db->where("stock_price <> ", $data["pricing_monthly"]);
			$this->db->where("end_date is null");
			$this->db->update("pricing", array("end_date" => date("Y-m-d")));
			
			// if there is not a price for the space, then insert one
			if(!$this->priceExistsForSpace($data["id"], PricingPeriod::MONTHLY)) {
				$this->db->insert("pricing", $pricingdata);
			}
		}

	}

	function deleteSpace($data) {
		$this->db->delete("space", array("id" => $data["id"]));
	}
	
	public function getAccessPricing() {
		/*
		 $sql = "
			select
				product.id, dayprice.stock_price as pricing_daily, monthprice.stock_price as pricing_monthly 
			from
				(select 0 as id) as product
				left outer join pricing as dayprice on dayprice.product_id = product.id and dayprice.period_code = 2 and dayprice.product_type_code = 2 and (dayprice.end_date > now() or dayprice.end_date is null)
				left outer join pricing as monthprice on monthprice.product_id = product.id and monthprice.period_code = 3 and monthprice.product_type_code = 2 and (monthprice.end_date > now() or monthprice.end_date is null)
		";
		*/
		$sql = "select daily_rate, default_monthly_rate_code, allow_monthly_memberships from configuration limit 1";
		$query = $this->db->query($sql);
		return $query->row();
	}

	public function updateAccessPricing($data) {
		
		$dailyRate = $data["daily_rate"];
		$defaultMonthlyRateCode = $data["default_monthly_rate_code"];
		$allowMonthlyMemberships = $data["allow_monthly_memberships"];
		$product = 0;
		$this->db->update("configuration", array("daily_rate" => $dailyRate, "default_monthly_rate_code" => $defaultMonthlyRateCode, "allow_monthly_memberships" => $allowMonthlyMemberships));

		/*		
		$this->db->where(array("product_id" => $product, "product_type_code" => ProductType::LOCATION, "period_code" => PricingPeriod::DAILY));
		$this->db->where("stock_price <> ", $data["pricing_daily"]);
		$this->db->where("end_date is null");
		$this->db->update("pricing", array("end_date" => date("Y-m-d")));

		if (!$this->priceExistsForAccess(PricingPeriod::DAILY)) {
			$info = array("product_id" => $product, "product_type_code" => ProductType::LOCATION, "period_code" => PricingPeriod::DAILY, "start_date" => date("Y-m-d"), "stock_price" => $pricingDaily);
			$this->db->insert("pricing", $info);
		}

		$this->db->where(array("product_id" => $product, "product_type_code" => ProductType::LOCATION, "period_code" => PricingPeriod::MONTHLY));
		$this->db->where("stock_price <> ", $data["pricing_monthly"]);
		$this->db->where("end_date is null");
		$this->db->update("pricing", array("end_date" => date("Y-m-d")));

		if (!$this->priceExistsForAccess(PricingPeriod::MONTHLY)) {
			$info = array("product_id" => $product, "product_type_code" => ProductType::LOCATION, "period_code" => PricingPeriod::MONTHLY, "start_date" => date("Y-m-d"), "stock_price" => $pricingMonthly);
			$this->db->insert("pricing", $info);
		}
		*/
		
	}
	
	private function priceExistsForSpace($spaceId, $periodType) {

		$this->db->where("period_code", $periodType);
		$this->db->where("product_type_code", ProductType::SPACE);
		$this->db->where("product_id", $spaceId);
		$this->db->where("(pricing.end_date > now() or pricing.end_date is null)");
		$query = $this->db->get("pricing");
		return ($query->num_rows() > 0);
		
	}
	
	private function priceExistsForAccess($periodType) {

		$product = 0;
		$this->db->where("period_code", $periodType);
		$this->db->where("product_type_code", ProductType::LOCATION);
		$this->db->where("product_id", $product);
		$this->db->where("(pricing.end_date > now() or pricing.end_date is null)");
		$query = $this->db->get("pricing");
		return ($query->num_rows() > 0);
		
	}

	function determine_membership($space_id, $memberships) {
		$daily_plan_name = 'Daily';
		$retValue = false;
		foreach ($memberships as $membership) {
			$membership = (array)$membership;
			if(strtolower($membership["plan_name"]) != strtolower($daily_plan_name)) {
				$retValue = true;
			}
		}
		return $retValue;
	}

	function spaces($user_id) {
		$space_data = array();
	    $query = $this->db->get("cobot_spaces");
	    $spaces = $query->result();
	    foreach ($spaces as $space_arr) {
	      $space = (array)$space_arr;
	      $space_id = $space['id'];
	      $space_img_src = '/grind-members/grind-code/images/spaces/'.$space['image'];
	      $capacity = $space['capacity'] - $space['checkins'];
	      $latitude = $space['lat'];
	      $longitude = $space['lon'];
	      $address = $space['address'];
	      $rate = $space['rate'];
	      $description = $space['description'];
	      $name = $space['name'];
	      $sql = "select membership.id, membership.plan_name from cobot_memberships membership where membership.space_id = '".$space_id."' and membership.user_id = '".$user_id."'";
		  error_log($sql);
		  $query = $this->db->query($sql);
		  $memberships = $query->result();
		  $plans_url = 'https://'.$space_id.'.cobot.me/api/plans';

	      $resourcedata = array(
	        'id' => $space_id,
	        'name' => $name,
            'img_src' => $space_img_src,
            'description' => $description,
            'capacity' => $capacity.' seats free',
            'rate' => '$'.$rate.'/day',
	        'is_member' => $this->determine_membership($space_id, $memberships),
	        'memberships' => $memberships,
	        'plans_url' => $plans_url
          );
	      $resources = array();
	      array_push($resources, $resourcedata);
	      $resources = array_merge($resources, $this->resources($space_id));
	      $spacedata = array(
	        'id' => $space_id,
	        'img_src' => $space_img_src,
	        'description' => $description,
	        'name' => $name,
	        'capacity' => $capacity.' seats free',
	        'lat' => $latitude,
	        'lon' => $longitude,
	        'address' => $address,
	        'rate' => '$'.$rate.'/day',
	        'resources' => $resources
	      );
	      array_push($space_data, $spacedata);
    	}
    	return $space_data;
	}

	function resources($space_id) {
	    $this->db->where("space_id", $space_id);
	    $query = $this->db->get('cobot_resources');
	    $resources = $query->result();
	    $resource_data = array();
	    foreach ($resources as $resource_arr) {
	      $resource = (array)$resource_arr;
	      $sql = "select booking.id, booking.from_datetime, booking.to_datetime from cobot_bookings booking left outer join cobot_memberships membership on booking.membership_id = membership.id and booking.space_id = membership.space_id where booking.resource_id = '".$resource['id']."' and membership.canceled_to is not null";
		  error_log($sql);
		  $query = $this->db->query($sql);
		  $bookings = $query->result();
	      $resourcedata = array(
	        'id' => $resource['id'],
	        'space_id' => $space_id,
	        'name' => $resource['name'],
            'img_src' => '/grind-members/grind-code/images/resources/'.$resource['image'],
            'description' => $resource['description'],
            'capacity' => $resource['capacity'].' people',
            'rate' => '$'.$resource['rate'].'/hr',
            'bookings' => $bookings
          );
          array_push($resource_data, $resourcedata);
	    }
	    return $resource_data;
  }

  function book_space($space_id, $user_id, $resource_id=null) {
  	error_log('booking space');
    global $spaceToMainArea, $environmentsToAccessToken;
    if(!$resource_id) {
	  	$resource_id = $spaceToMainArea[$space_id];
    }
  	$sql = "select * from cobot_memberships where user_id='".$user_id."' and space_id='".$space_id."'";
  	error_log($sql);
  	$query = $this->db->query($sql);
  	$result = $query->result();
  	error_log($result);
  	$membership = current($result);
  	error_log($membership);
  	$membership_id = $membership->id;
	$util = new utilities;
	$environment = $util->get_environment_for($space_id);
	error_log($environment);
  	$url = "https://".$space_id.".cobot.me/api/resources/".$resource_id."/bookings";
  	$from = date_create();
  	$to = date_add(date_create(), date_interval_create_from_date_string("5 hours"));
  	$data = array(
		"membership_id"=>$membership_id,
		"from"=> date_format($from, 'Y-m-d H:i O'), //date_create()->format('Y-m-d H:i:s')
		"to"=> date_format($to, 'Y-m-d H:i O'),
		"title"=> "test booking",
		"comments"=> "tea please"
  		);
    $options = array(
        'http' => array(
            'header'  => "Authorization: Bearer ".$environmentsToAccessToken[$environment]."\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ),
    );
    error_log(json_encode($options));
    error_log(json_encode($data));
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    error_log(json_encode($result));
    return json_decode($result);
  }

}

?>