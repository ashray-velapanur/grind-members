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

	function determine_membership($user_id, $space_id, $memberships, $main_area_resource_id) {
		$default_plan_name = 'Virtual';
		$retValue = false;
		foreach ($memberships as $membership) {
			$membership = (array)$membership;
			error_log(json_encode($membership));
			if(strpos(strtolower($membership["plan_name"]), strtolower($default_plan_name)) == false) {
				$retValue = true;
			}
		}
		if (!$retValue) {
			$sql = "select id from cobot_memberships where space_id = '".$space_id."' and plan_name = '".$default_plan_name."' and user_id = '".$user_id."'";
			error_log($sql);
			$query = $this->db->query($sql);
			$cms = $query->result();
			if(count($cms) > 0) {				
				$cm = current($cms);
				$to_datetime = gmdate('Y-m-d H:i:s');
				$sql = "select id from cobot_bookings where space_id = '".$space_id."' and resource_id = '".$main_area_resource_id."' and membership_id = '".$cm->id."' and to_datetime > '".$to_datetime."'";
				error_log($sql);
				$query = $this->db->query($sql);
				$result = $query->result();
				if(count($result) > 0) {
					$retValue = true;
				}
			}
		}
		return $retValue;
	}

	function spaces($user_id) {
		$space_data = array();
		$default_plan_name = 'Virtual';
	    $query = $this->db->get("cobot_spaces");
	    $spaces = $query->result();
	    foreach ($spaces as $space_arr) {
	      $space = (array)$space_arr;
	      $space_id = $space['id'];
	      $space_img_src = '/grind-members/grind-code/index.php/image/get?id='.$space['image'];
	      $capacity = $space['capacity'] - $space['checkins'];
	      $latitude = $space['lat'];
	      $longitude = $space['lon'];
	      $address_1 = $space['address_1'];
	      $address_2 = $space['address_2'];
	      $rate = '$'.$space['rate'].'/day';
	      $description = $space['description'];
	      $name = $space['name'];
	      $sql = "select membership.id, membership.plan_name, membership.plan_id from cobot_memberships membership where membership.space_id = '".$space_id."' and membership.user_id = '".$user_id."'";
		  error_log($sql);
		  $query = $this->db->query($sql);
		  $memberships = $query->result();
		  $memberships_arr = array();
		  foreach ($memberships as $membership) {
		  	$id = $membership->id;
		  	$plan_name = $membership->plan_name;
		  	if($plan_name == $default_plan_name) {
		  		$plan_name = $rate;
		  	}
		  	$m = array(
		  			'id' => $id,
		  			'plan_name' => $plan_name
		  		);
		  	array_push($memberships_arr, $m);
		  }
		  $plans_url = 'https://'.$space_id.'.cobot.me/api/plans';

		  $booking_credits = array();
		  error_log('Memberships count: '.count($memberships));
		  if(count($memberships) > 0) {
			$current_membership = current($memberships);
			$booking_credits_url = 'https://'.$space_id.'.cobot.me/api/memberships/'.$current_membership->id.'/booking_credits';
			$util = new utilities;
			$booking_credits = $util->do_get($booking_credits_url, $params=array('access_token' => $util->get_current_environment_cobot_access_token()));
		  }
		  error_log(json_encode($booking_credits));

	      $resourcedata = array(
	        'id' => $space_id,
	        'name' => $name,
            'img_src' => $space_img_src,
            'description' => $description,
            'capacity' => $capacity.' seats free',
	        'address_1' => $address_1,
	        'address_2' => $address_2,
            'rate' => $rate,
	        'is_member' => $this->determine_membership($user_id, $space_id, $memberships, $space['main_area_id']),
	        'memberships' => $memberships_arr,
	        'plans_url' => $plans_url
          );
	      $resources = array();
	      array_push($resources, $resourcedata);
	      $resources = array_merge($resources, $this->resources($space_id, $name, $booking_credits));
	      $spacedata = array(
	        'id' => $space_id,
	        'img_src' => $space_img_src,
	        'description' => $description,
	        'name' => $name,
	        'capacity' => $capacity.' seats free',
	        'lat' => $latitude,
	        'lon' => $longitude,
	        'rate' => $rate,
	        'resources' => $resources
	      );
	      array_push($space_data, $spacedata);
    	}
    	return $space_data;
	}

	function resources($space_id, $space_name, $booking_credits) {
	    $this->db->where("space_id", $space_id);
	    $query = $this->db->get('cobot_resources');
	    $resources = $query->result();
	    $resource_data = array();
	    foreach ($resources as $resource_arr) {
	      $resource = (array)$resource_arr;
	      $sql = "select booking.id, booking.from_datetime, booking.to_datetime from cobot_bookings booking left outer join cobot_memberships membership on booking.membership_id = membership.id and booking.space_id = membership.space_id where booking.resource_id = '".$resource['id']."' and membership.canceled_to is null";
		  error_log($sql);
		  $query = $this->db->query($sql);
		  $bookings = $query->result();
		  $rate_and_hours = $this->get_resource_rate_and_hours($resource, $booking_credits);
		  $rate = $rate_and_hours['rate'];
		  $hours = $rate_and_hours['hours'];
	      $resourcedata = array(
	        'id' => $resource['id'],
	        'space_id' => $space_id,
	        'name' => $resource['name'],
	        'space_name' => $space_name,
            'img_src' => '/grind-members/grind-code/index.php/image/get?id='.$resource['image'],
            'description' => $resource['description'],
            'capacity' => $resource['capacity'].' people',
            'rate' => $rate,
            'hours' => $hours,
            'bookings' => $bookings
          );
          array_push($resource_data, $resourcedata);
	    }
	    return $resource_data;
  }

	function get_resource_rate_and_hours($resource, $booking_credits) {
		$rate = '$'.$resource['rate'].'/hr';
		$hours = '';
		$id = $resource['id'];
		$booking_credit = $booking_credits[$id];
		if($booking_credit) {
			$total_hours = $booking_credit->total_hours;
			$remaining_hours = $booking_credit->hours_remaining;
			$price_per_hour = $booking_credit->price_per_hour;
			if($remaining_hours || (intval($remaining_hours) == 0 && $total_hours && $total_hours > 0)) {
				if(!(is_numeric( $remaining_hours ) && floor( $remaining_hours ) != $remaining_hours)) {
					$remaining_hours = intval($remaining_hours);
				}
				$hours = $remaining_hours.' hrs remaining';
			} elseif ($total_hours) {
				$hours = $total_hours.' hrs remaining';
			}
			if($price_per_hour) {
				$rate = '$'.$price_per_hour.'/hr';
			}
		}
		return array('rate'=>$rate, 'hours'=>$hours);
	}

  function book_space($space_id, $user_id, $resource_id=null, $from=null, $to=null) {
  	error_log('booking space');
  	$response = array();
    if(!$resource_id) {
    	$sql = "SELECT main_area_id FROM cobot_spaces where id = '".$space_id."'";
		error_log($sql);
		$query = $this->db->query($sql);
		$results = (array)$query->result();
		error_log(json_encode($results));
		$main_area_id = NULL;
		if(count($results) > 0) {
			$main_area_id = current($results)->main_area_id;
		}
		error_log($main_area_id);
	  	$resource_id = $main_area_id;
    }
  	if($resource_id) {
  		$sql = "select m.*, u.first_name, u.last_name from cobot_memberships m join user u on m.user_id = u.id where u.id='".$user_id."' and m.space_id='".$space_id."'";
	  	error_log($sql);
	  	$query = $this->db->query($sql);
	  	$result = $query->result();
	  	error_log(json_encode($result));
	  	$membership = current($result);
	  	error_log(json_encode($membership));
	  	$membership_id = $membership->id;
	  	$title = $membership->first_name.' '.$membership->last_name;
		$util = new utilities;
	  	$url = "https://".$space_id.".cobot.me/api/resources/".$resource_id."/bookings";
	  	error_log('Booking url: '.$url);
	  	if(!$from){
	  		$from = date_create();
	  		$from = date_format($from, 'Y-m-d H:i O');
	  	}
	  	if(!$to){
	  		$to = date_add(date_create(), date_interval_create_from_date_string("5 hours"));
	  		$to = date_format($to, 'Y-m-d H:i O');
	  	}
	  	$data = array(
			"membership_id"=>$membership_id,
			"from"=> $from,
			"to"=> $to,
			"title"=> $title,
			"comments"=> "tea please"
	  		);
	  	$this->load->model("thirdpartyusermodel","tpum",true);
	  	$cobot_access_token = $this->tpum->get_cobot_access_token($user_id);
	    if($cobot_access_token) {
            $response = $util->do_post($url, $params=$data, $cobot_access_token);
	    } else {
	    	$response['error'] = "Could not find Cobot Access Token for the user";
	    }
  	} else {
  		$response['error'] = "No resource to book";
  	}
  	error_log(json_encode($response));
  	return $response;
  }

	public function add_space_and_resources($space_id, $space=NULL) {
		$util = new utilities;
		$main_area_resource_id = NULL;

		if($space) {
			$main_area_resource_id = $space['main_area_id'];
		}

		$this->add_update_space($space);

		$resource_data = array();
		$curl = curl_init();
		$url = 'https://'.$space_id.'.cobot.me/api/resources';
		$rdata = array(
		  'access_token' => $util->get_current_environment_cobot_access_token()
		);
		if ($rdata)
		      $url = sprintf("%s?%s", $url, http_build_query($rdata));
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
		    $resource_id = $resource['id'];
		    if($resource_id == $main_area_resource_id) {
		    	$sql = "UPDATE cobot_spaces SET capacity = ".$resource['capacity'].", rate = ".$resource['price_per_hour']." WHERE id = '".$space_id."'";
		    	error_log($sql);
				$this->db->query($sql);
		    }
		    else {
		    	$this->add_update_resource($resource, $space_id);
		    }
		  }
		}
	}

	public function add_update_space($space) {
		$util = new utilities;
		$plan = $util->get_cobot_plan('Virtual', $space['id']);
		$default_plan_id = "";
		if($plan) {
			$default_plan_id = $plan->id;
		}
		$sql = "SELECT * FROM cobot_spaces where id = '".$space['id']."'";
		error_log($sql);
		$query = $this->db->query($sql);
		$spaces = $query->result();
		error_log(count($spaces));
		try {
			if(count($spaces) <= 0) {
				$sql = "INSERT INTO cobot_spaces";
				$sql .= "(id, image, capacity, lat, lon, address_1, address_2, rate, name, description, main_area_id, default_plan_id) ".
						"VALUES ".
						"(\"".$space['id']."\", \"".$space['imgName']."\", ".$space['capacity'].", \"".$space['lat']."\", \"".$space['long']."\", \"".$space['address_1']."\", \"".$space['address_2']."\", ".$space['rate'].", \"".$space['name']."\", \"".$space['description']."\", \"".$space['main_area_id']."\", \"".$default_plan_id."\") ";
				error_log($sql);
				if ($this->db->query($sql) === TRUE) {
					error_log("Cobot space created successfully");
					$this->setup_webhook_subscriptions($space['id']);
				} else {
					error_log("Error: " . $sql . "<br>" . $this->db->error);
				}
			} else {
				$sql = "UPDATE cobot_spaces SET image = \"".$space['imgName']."\", capacity = ".$space['capacity'].", lat = \"".$space['lat']."\", lon = \"".$space['long']."\", address_1 = \"".$space['address_1']."\", address_2 = \"".$space['address_2']."\", rate = ".$space['rate'].", name = \"".$space['name']."\", description = \"".$space['description']."\", main_area_id = \"".$space['main_area_id']."\", default_plan_id = \"".$default_plan_id."\" WHERE id='".$space['id']."'";
				error_log($sql);
				$this->db->query($sql);
				$sql = "DELETE from cobot_resources where space_id = '".$space['id']."'";
				error_log($sql);
				$this->db->query($sql);
			}
		} catch (Exception $e) {
		    error_log('Caught exception: ',  $e->getMessage(), "\n");
		}
	}

	public function setup_webhook_subscriptions($space_id) {
		$host = $_SERVER['SERVER_NAME'];
		if($host != 'localhost' && $host != '127.0.0.1') {
			$is_https = $_SERVER['HTTPS'];
			$callback_url = 'http://';
			if($is_https) {
				$callback_url = 'https://';
			}
			$callbacks = array("created_booking" => $callback_url.$host.'/grind-members/grind-code/index.php/cobot/booking_created',
							   "updated_booking" => $callback_url.$host.'/grind-members/grind-code/index.php/cobot/booking_updated',
							   "deleted_booking" => $callback_url.$host.'/grind-members/grind-code/index.php/cobot/booking_deleted',
							   "created_membership" => $callback_url.$host.'/grind-members/grind-code/index.php/cobot/membership_created',
							   "confirmed_membership" => $callback_url.$host.'/grind-members/grind-code/index.php/cobot/membership_created',
							   "connected_user" => $callback_url.$host.'/grind-members/grind-code/index.php/cobot/membership_created',
							   "canceled_membership" => $callback_url.$host.'/grind-members/grind-code/index.php/cobot/membership_canceled',
							   "created_checkin" => $callback_url.$host.'/grind-members/grind-code/index.php/cobot/handle_checkin',
							   "created_checkout" => $callback_url.$host.'/grind-members/grind-code/index.php/cobot/handle_checkout',
							   "changed_membership_plan" => $callback_url.$host.'/grind-members/grind-code/index.php/cobot/membership_plan_changed'
							   );
			foreach ($callbacks as $event => $url) {
				$subdomain = $space_id;
				$this->load->model("subscriptionmodel","sm",true);
				$this->sm->create_webhook_subscription($event, $url, $subdomain);
			}
		}
	}

	public function add_update_resource($resource, $space_id) {
		$imgName = $resource['id'].'.png';
		$sql = "INSERT INTO cobot_resources";
		$rate = $resource['price_per_hour'] ? $resource['price_per_hour'] : 0.0;
		$sql .= "(id, space_id, image, name, capacity, rate, description) VALUES ('".$resource['id']."', '$space_id', '$imgName', '".$resource['name']."', 10, ".$rate.", \"".$resource['description']."\")";
		try {
			error_log($sql);
			if ($this->db->query($sql) === TRUE) {
				error_log("Cobot resource created successfully");
			} else {
				error_log("Error: " . $sql . "<br>" . $this->db->error);
			}
		} catch (Exception $e) {
		    error_log('Caught exception: ',  $e->getMessage(), "\n");
		}
	}

}

?>