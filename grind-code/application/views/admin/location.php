	<script type="text/javascript">
		function $(x) {return document.getElementById(x);}

		function init() {
			var addrFields = ["addr_1", "city", "state_prov_luid", "post_code"];
			for(var field in addrFields) {
				$(addrFields[field]).onchange = updateMap;
				//if ($(addrFields[field]).captureEvents) $(addrFields[field]).captureEvents(Event.CHANGE);
			}  
		}
		
		function updateMap(e) {
			//alert("updateMap");
			var fullAddr = $("addr_1").value + ", " + $("city").value + ", " +
			  $("state_prov_luid").options[$("state_prov_luid").selectedIndex].text + 
			  " " + $("post_code").value;

			//alert(fullAddr);
			
			var imgsrc = "http://maps.google.com/maps/api/staticmap?center=" + 
			  encodeURIComponent(fullAddr) + "&zoom=15&size=150x200&sensor=false&markers=|" +
			  encodeURIComponent(fullAddr);
			  
			$("locationImage").src = imgsrc;
		}
	</script>

<div id="location" class="clearfix">

	<?
	// general array of hidden input tags to be used
	$hidden = (isset($location) ? array("id" => $location->id) : null);
	echo form_open("admin/locationmanagement/addupdatelocation", "", $hidden);
	?>
	<fieldset>
	 <h3>Basic Information</h3>
		<ul>
  		<li>
  		  <label for="name">Name</label>
  		  <?=form_input(array("id" => "name", "name" => "name", "value" => (isset($location) ? $location->name : ""))) ?>
  		</li>
  		<li>
  		  <label for="manager">Manager</label>
  		  <?=form_input(array("id" => "manager", "name" => "manager", "value" => (isset($location) ? $location->manager : ""))) ?>
  		</li>
  		<li>
  		  <label for="capacity">Capacity</label>
  		  <?=form_input(array("id" => "capacity", "name" => "capacity", "value" => (isset($location) ? $location->capacity : ""))) ?>
  		</li>
  	  <li>
  	   <?=form_hidden("is_closed","0") . form_checkbox(array("id" => "is_closed", "name" => "is_closed", "value" => "1", "checked" => (isset($location) ? $location->is_closed : 0))); ?> <?=form_label("Is closed?", "is_closed")?>
  	 </li>
	  </ul>
	  <div class="map"><?=g_anchor("http://maps.google.com/maps?q=" . urlencode((isset($location) ? $location->full_address : "")), "<img id=\"locationImage\" src=\"http://maps.google.com/maps/api/staticmap?center=" . urlencode((isset($location) ? $location->full_address : "")) . "&zoom=15&size=340x200&sensor=false&markers=|" . urlencode((isset($location) ? $location->full_address : "")) . "\" />"); ?></div>
	</fieldset>
  
	<fieldset>
	 <h3>Contact Information</h3>
    <ul>
  		<li>
  		  <label for="addr_1">Address 1</label>
  		  <?=form_input(array("id" => "addr_1", "name" => "addr_1", "value" => (isset($location) ? $location->addr_1 : ""))) ?>
  		</li>
  		<li>
  		  <label for="addr_2">Address 2</label>
  		  <?=form_input(array("id" => "addr_2", "name" => "addr_2", "value" => (isset($location) ? $location->addr_2 : ""))) ?>
  		</li>
  		<li>
  		  <label for="cross_street">Cross Street</label>
  		  <?=form_input(array("id" => "cross_street", "name" => "cross_street", "value" => (isset($location) ? $location->cross_street : ""))) ?>
  		</li>
  		<li>
  		  <label for="city">City</label>
  		  <?=form_input(array("id" => "city", "name" => "city", "value" => (isset($location) ? $location->city : ""))) ?>
  		</li>
  		<li>
  		  <label for="state_prov_luid">State</label>
  		  <?=form_dropdown("state_prov_luid", $state_provs, (isset($location) ? $location->state_prov_luid : ""), 'id = "state_prov_luid" name = "state_prov_luid"') ?>
  		</li>
  		<li>
  		  <label for="post_code">Zip Code</label>
  		  <?=form_input(array("id" => "post_code", "name" => "post_code", "value" => (isset($location) ? $location->post_code : ""))) ?>
  		</li>
  		<li>
  		  <label for="country_luid">Country</label>
  		  <?=form_dropdown("country_luid", $countries, (isset($location) ? $location->country_luid : ""), 'id = "country_luid" name = "country_luid"') ?>
  		</li>
  		<li>
  		  <label for="phone">Phone</label>
  		  <?=form_input(array("id" => "phone", "name" => "phone", "value" => (isset($location) ? $location->phone : ""))) ?>
  		</li>
		<li>
	  	  <label for="wlc_ip">Wireless LAN IP Address</label>
		  <?=form_input(array("id" => "wlc_ip", "name" => "wlc_ip", "value" => (isset($location) ? $location->wlc_ip : ""))) ?>
		</li>
		</ul>
  </fieldset>
	
	<ul class="buttons">
	  <li>
	   <?=form_submit(array("id" => "submit", "class" => "btn", "value" => (isset($location) ? "Update" : "Add"))); ?>	
  	 <?=form_close() ?>
  	</li>
  	<?
    	if (isset($location)) {?>
  	<li>
    	<?
    		$hidden = (isset($location) ? array("id" => $location->id) : null);
    		echo form_open("admin/locationmanagement/deletelocation", "", $hidden);
    		echo form_submit(array("id" => "submit", "class" => "btn btn-delete", "value" => "Delete", "onclick" => "return confirm('Are you sure you want to delete this location?  This cannot be undone.');"));
    		echo form_close();
    	?>
  	</li>
  	<? } ?>
	</ul>

</div>	