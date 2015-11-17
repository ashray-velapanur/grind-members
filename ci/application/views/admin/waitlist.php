<div id="waitlist" class="clearfix">

  <h1>Members on Waitlist</h1>
  <p class="instruction">Select a member&rsquo;s name below to view and edit additional information for each member.</p>

  <section id="member-list" class="module">
    <table class="list">
    	<thead>
    		<tr>
    			<th class="name">Grindist</th>
    			<th>Contact</th>
    			<th>Last Check-In</th>
    		</tr>
    	</thead>
    	<tbody>
    	<? foreach($users as $user): ?>
    		<tr>
    			<td class="name">
    				<?=g_anchor("/admin/usermanagement/viewprofile/" . $user->id,  $user->first_name.' '.$user->last_name ) ?>
    		        <? if($user->is_admin == 1) { echo "<sup>*</sup>"; } ?>
    				<? if($user->company != "") { echo "<br><span class=\"company\">" . $user->company . "</span>"; } ?>
    			</td>
    			<td class="contactinfo"><?=$user->email_address ?><br /><?=$user->phone_number ?></td>
    			<!-- TODO: Need password reset email trigger method -->
    			<td class="lastcheckin"><?=($user->last_sign_in == "" ? "Never" : utilities::grind_date_format($user->last_sign_in))?></td>
    		</tr>
    	<? endforeach; ?>
    	</tbody>
    </table>
    <p><sup>*</sup> Denotes an administrator</p>
  </section>
</div><!-- end #waitlist -->
