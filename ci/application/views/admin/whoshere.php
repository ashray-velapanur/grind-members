

<table class="list">
	<thead>
        <tr>
            <th>Name</th>
            <th>Signed-In</th>
            <th>Method</th>
        </tr>
	</thead>
    <tfoot>
        <tr>
            <td colspan="3">Total signed-in: <?=count($signedInMembers)?></td>
        </tr>
    </tfoot>
	<tbody>
	<? foreach($signedInMembers as $user): 
	
	?>
		<tr>
			<td class="name">
				<?=g_anchor("/admin/usermanagement/user/" . $user->id, $user->last_name . ", " . $user->first_name) ?>
				<? if($user->company != "") { echo "<div class=\"company\">" . $user->company . "</div>"; } ?>
			</td>
			
            <td class="date"><?=format_date($user->sign_in,true) ?></td>
            <td class=""><?=$user->sign_in_method ?></td>
        </tr>
    <? endforeach; ?>
    </tbody>
</table>
