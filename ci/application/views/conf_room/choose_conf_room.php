<?
/**
 * drop down of conference room template
 * 
 * returns a list of conference rooms as a select box
 *
 * @joshcampbell
 * @view template
 */
 $this->load->helper('html');
 
 
 $loc_id = ""; 
 foreach ($locations as $row)
 // this routine will work so long as the locations results are ordered by location_id
 {
 	$space = array(
 		'name'=>$row->space_name,
 		'loc_id'=>$row->location_id,
 		'id'=>$row->space_id,
 		'cal'=>$row->calendar_link,
 		'capacity'=>$row->capacity
 	);
 	$loc = array("id"=>$row->location_id,"name"=>$row->location_name);
 	$spaces[]=$space;
 	if ($row->location_id != $loc_id){ // did we see this location_id before?
 		$locs[]=$loc;
 		$loc_id = $row->location_id;	    	
    } // other wise we have
 }
 $multipleLocations = (count($locs)>=2) ? true : false;
 $display = $multipleLocations ? "block" : "none"	; 
 $loc_opt = $multipleLocations ? '<option value="-1" selected="selected">Choose a Location</option>' : "";
?>
<script type="text/javascript">
	var location_data = <?=$jsDataSet?>;
</script>

<div id="form-location-wrapper" style="display:<?=$display?>;">
	<select name="form-location" id="form-location">
	<?=$loc_opt?>
	</select>
</div>
<div id="space-wrapper">
	<select name="space" id="space">
	<?
	// CO: Asks to remove this so a space is always chosen by default
	//	<option value="-1" selected="selected">Choose a conference room</option> 
	?>
		<?
		$i=0;
		foreach($spaces as $space){
		if ($i < 1){
		$firstCal = $space['cal'];
		$i++;
		}
		?>
		
		<option value="<?=$space["id"]?>"><?=$space["name"]?></option>
	<? } ?>

	</select><a id="calendarLink" style="margin-left:5px;display:inline;" href="<?=$firstCal?>" target="_blank">[view room calendar]</a>
	</div>
