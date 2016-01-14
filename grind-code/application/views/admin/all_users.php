<div id="members">
  <h1>Members Listing</h1>
  <table class="list">
    	<thead>
    		<tr>
    		
    			<th class="name">Grindist</th>
    			<th>Contact</th>
    			<th>Member Type</th>
    			<th>RFID</th>
				<th>Last Check-In</th>
    			<th>&nbsp;</th>
    		</tr>
    	</thead>
    	<tbody id="items">
    	
    	<? foreach($users as $user): ?>
    	<?
    	if($user->last_sign_in == ""){
    		$lastcheckin = "Never";
    		
    	} else {
    		
    		$lastcheckin= format_date($user->last_sign_in,true);
    	
    	}	
    	
    	?>
    	
    		<tr id="item">
    			
    			<td class="name">
    				<?=g_anchor("/admin/usermanagement/viewprofile/" . $user->id,  $user->first_name.' '.$user->last_name ) ?>
    		        <? if($user->is_admin == 1) { echo "<sup>*</sup>"; } ?>
    				<? if($user->company != "") { echo "<br><span class=\"company\">" . $user->company . "</span>"; } 
    				
    				?>
    				<? if($user->designation != "") { echo "<br><span class=\"company\">" . $user->designation . "</span>"; } 
    				
    				?>
    				
    			</td>
    			<td class="contactinfo"><?=$user->email_address ?><br /><?=$user->phone_number ?></td>
    			<td class="type"><?=$user->plan_code?></td>
    			<td class="rfid"><?=$user->rfid?></td>
    			<td class="lastcheckin"><?=$lastcheckin?></td>
				<td class="alert">&nbsp;</td>
    			<!--<td><a href="<?=site_url('grind-code/members/profile/delete/'.$user->id)?>" target="_blank">delete?</a></td>-->
    		</tr>
    	<? endforeach; ?>
    	</tbody>
    </table>
    <?php if(isset($pagination)){ echo $pagination; } ?>
</div><!-- end #members -->