	<script type="text/javascript">
		function $(x) {return document.getElementById(x);}
	</script>

	<?
	// general array of hidden input tags to be used
        $hidden["location_id"] = $location["id"];
        if (isset($space)) $hidden["id"] = $space->id;
	
	echo form_open("admin/locationmanagement/addupdatespace", "", $hidden);
	?>

	<div class="fieldgroup">
		<h3>Basic Information</h3>
		<ul>
			<li>
				<div class="label"><?=form_label("Name", "name")?></div>
				<div class="field"><?=form_input(array("id" => "name", "name" => "name", "value" => (isset($space) ? $space->name : ""))) ?></div>
			</li>
			<li>
				<div class="label"><?=form_label("Description", "description")?></div>
				<div class="field"><?=form_textarea(array("id" => "description", "name" => "description", "value" => (isset($space) ? $space->description : ""))) ?></div>
			</li>
			<li>
				<div class="label"><?=form_label("Parent space", "parent_space_id")?></div>
				<div class="field"><?=form_dropdown("parent_space_id", $existing_spaces, (isset($space) ? $space->parent_space_id : ""), 'id = "parent_space_id" name = "parent_space_id"') ?></div>
			</li>
			<li>
				<div class="label"><?=form_label("Space type", "space_type_luid")?></div>
				<div class="field"><?=form_dropdown("space_type_luid", $space_types, (isset($space) ? $space->space_type_luid : ""), 'id = "space_type_luid" name = "space_type_luid"') ?></div>
			</li>
			<li>
				<div class="label"><?=form_label("Capacity", "capacity")?></div>
				<div class="field"><?=form_input(array("id" => "capacity", "name" => "capacity", "value" => (isset($space) ? $space->capacity : ""))) ?></div>
			</li>
			<li>
				<div class="label"><?=form_label("Calendar link", "calendar_link")?></div>
				<div class="field"><?=form_input(array("id" => "calendar_link", "name" => "calendar_link", "value" => (isset($space) ? $space->calendar_link : ""))) ?></div>
			</li>
			<li>
				<div class="label"><?=form_label("Is inactive?", "is_inactive")?></div>
				<div class="field"><?=form_hidden("is_inactive", "0") . form_checkbox(array("id" => "is_inactive", "name" => "is_inactive", "value" => "1", "checked" => (isset($space) ? $space->is_inactive : 0))); ?></div>
			</li>
		</ul>
	</div>
	<div class="fieldgroup">
		<h3>Booking</h3>
		<ul>
			<li>
				<div class="label"><?=form_label("Is bookable?", "is_bookable")?></div>
				<div class="field"><?=form_hidden("is_bookable", "0") . form_checkbox(array("id" => "is_bookable", "name" => "is_bookable", "value" => "1", "checked" => (isset($space) ? $space->is_bookable : 0))); ?></div>
			</li>
			<li>
				<div class="label"><?=form_label("Hourly rate", "pricing_hourly")?></div>
				<div class="field"><?=form_input(array("type" => "number", "step" => ".05", "min" => "0.00", "id" => "pricing_hourly", "name" => "pricing_hourly", "value" => (isset($space) ? $space->pricing_hourly : ""))) . form_hidden("pricing_hourly_id", (isset($space) ? $space->pricing_hourly_id : "")); ?></div>
			</li>
			<li>
				<div class="label"><?=form_label("Daily rate", "pricing_daily")?></div>
				<div class="field"><?=form_input(array("type" => "number", "step" => ".05", "min" => "0.00", "id" => "pricing_daily", "name" => "pricing_daily", "value" => (isset($space) ? $space->pricing_daily : ""))) . form_hidden("pricing_daily_id", (isset($space) ? $space->pricing_daily_id : "")); ?></div>
			</li>
			<li>
				<div class="label"><?=form_label("Monthly rate", "pricing_monthly")?></div>
				<div class="field"><?=form_input(array("type" => "number", "step" => ".05", "min" => "0.00", "id" => "pricing_monthly", "name" => "pricing_monthly", "value" => (isset($space) ? $space->pricing_monthly : ""))) . form_hidden("pricing_monthly_id", (isset($space) ? $space->pricing_monthly_id : "")); ?></div>
			</li>
		</ul>
	</div>
	
	<?=form_submit(array("id" => "submit", "value" => (isset($space) ? "Update" : "Add"))); ?>	
	<?=form_close() ?>

	<?
	if (isset($space)) {
        $hidden["location_id"] = $location["id"];
        if (isset($space)) $hidden["id"] = $space->id;
		echo form_open("admin/locationmanagement/deletespace", "", $hidden);
		echo form_submit(array("id" => "submit", "value" => "Delete", "onclick" => "return confirm('Are you sure you want to delete this space?  This cannot be undone.');"));
		echo form_close();
	}
	?>
	