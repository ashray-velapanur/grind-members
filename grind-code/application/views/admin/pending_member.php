<html>
<head>
	<title>Applicants</title>
	<link rel="stylesheet" type="text/css" href="<?=ROOTMEMBERPATH?>/grind-code/css/temp.css" />	
</head>
<body onload="init()">
	<header>
		<div class="breadcrumbs">
			<ul>
				<li><?=g_anchor("", "Admin Home") ?></a></li>
				<li><?=g_anchor("/locationmanagement/locations", "Locations") ?></a></li>
                                <li><?=g_anchor("/applicationmanagement/applications", "Applications") ?></a></li>
			</ul>
		</div>
		<h1></h1>
	</header>
	<content>

	<?
	$hidden = array("id" => $location->id);
	echo form_open("admin/locationmanagement/updatelocation", "", $hidden);
	$states = array("0" => "New York", "1" => "South Carolina");
	?>
	
	<ul>	
		<li><?=form_label("Address 1", "addr_1") . form_input(array("id" => "addr_1", "name" => "addr_1", "value" => $location->addr_1)) ?></li>
		<li><?=form_label("Address 2", "addr_2") . form_input(array("id" => "addr_2", "name" => "addr_2", "value" => $location->addr_2)) ?></li>
		<li><?=form_label("Cross street", "cross_street") . form_input(array("id" => "cross_street", "name" => "cross_street", "value" => $location->cross_street)) ?></li>
		<li><?=form_label("City", "city") . form_input(array("id" => "city", "name" => "city", "value" => $location->city)) ?></li>
		<li><?=form_label("State/Province", "state_prov_luid") . form_dropdown("state_prov_luid", $states, $location->state_prov_luid, 'id = "state_prov_luid" name = "state_prov_luid"') ?></li>
		<li><?=form_label("Post code", "post_code") . form_input(array("id" => "post_code", "name" => "post_code", "value" => $location->post_code)) ?></li>
		<li><?=form_label("Phone", "phone") . form_input(array("id" => "phone", "name" => "phone", "value" => $location->phone)) ?></li>
		<li><?=form_label("Manager", "manager") . form_input(array("id" => "manager", "name" => "manager", "value" => $location->manager)) ?></li>
		
		<div class="map"><?=g_anchor("http://maps.google.com/maps?q=" . urlencode($location->full_address), "<img id=\"locationImage\" src=\"http://maps.google.com/maps/api/staticmap?center=" . urlencode($location->full_address) . "&zoom=15&size=150x200&sensor=false&markers=|" . urlencode($location->full_address) . "\" />"); ?></div>
	</ul>

	<?=form_submit(array("id" => "update", "value" => "Update")); ?>	
	<?=form_close() ?>
	</content>
	<footer>
	</footer>
</body>
</html>