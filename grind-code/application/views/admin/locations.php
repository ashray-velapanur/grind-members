<div id="globalactions">[<?=g_anchor("/admin/locationmanagement/location/", "Add Location")?>]</div>
<table>
	<thead>
		<tr>
			<th class="name">Name</th>
			<th class="address">Address</th>
			<th class="phone">Phone</th>
			<th class="manager">Manager</th>
			<th class="spaces">Spaces</th>
			<th class="signed-in">Signed-In</th>
		</tr>
	</thead>
	<tbody>
	<? foreach($locations as $location): ?>
		<tr>
			<td class="name">
				<?=g_anchor("/admin/locationmanagement/location/" . $location->id, $location->name) ?>
				<div class="map"><?=g_anchor("http://maps.google.com/maps?q=" . urlencode($location->full_address), "<img src=\"http://maps.google.com/maps/api/staticmap?center=" . urlencode($location->full_address) . "&zoom=15&size=200x150&sensor=false&markers=|" . urlencode($location->full_address) . "\" />"); ?></div>
			</td>
			<td class="address"><?=$location->full_address_w_linebreaks ?></td>
			<td class="phone"><?=$location->phone ?></td>
			<td class="manager"><?=$location->manager ?></td>
			<td class="spaces"><?=g_anchor("/admin/locationmanagement/locationspaces/" . $location->id, "Manage spaces for " . $location->name)?></td>
			<td class="whoshere"><?=g_anchor("/admin/locationmanagement/whoshere/" . $location->id, "Check sign-ins")?></td>
		</tr>
	<? endforeach; ?>
	</tbody>
</table>
