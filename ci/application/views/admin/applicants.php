<input type="hidden" id="changeaction" value="<?=site_url('/ci/admin/applicantmanagement/applicants/')?>"/>
<select id="applicantStatuses" style="margin-bottom:10px;">
    <? foreach($statuses as $key => $value): ?>
        <?php if($key<=MembershipStatus::APPLICANT_APPROVED) {
        
        ?>
            <option value="<?php echo $key; ?>" <?php echo $status_chosen == $key ? 'selected' : '';?>><?php echo $value; ?></option>
        <?php } ?>
    <? endforeach; ?>
</select>
<table class="list">
	<thead>
		<tr>
			<th>Name</th>
			<th>Company</th>
			<th>Location</th>
			<th>Desired Plan</th>
			<th>Referred By</th>
			<th>Status</th>
			<th>Date Added</th>
			<th>Why Them?</th>
			<th>Action</th>
		</tr>
	</thead>
	<?  if ($applicants){ ?>
	<? foreach($applicants as $applicant): ?>
		<tr id="<?=$applicant->id?>">
			<td><?=$applicant->first_name ?> <?=$applicant->last_name ?><br />
			<a href="mailto:<?=$applicant->email_address ?>"><?=$applicant->email_address ?><br />
			<?php if (!$applicant->HasUniqueEmail){?>
					<div style="color:red;font-weight:bold;">Email is not unique!</div>
				<?php } ?>
			</a>
			</td>
			<td><?=$applicant->company_name ?></td>
			<td><?=$applicant->location; ?></td>
			<td><?=$applicant->plan; ?></td>
			<td><?=$applicant->referrer ? $applicant->referrer : "&nbsp;" ?></td>
			<td><?=$applicant->status_name ?></td>
			<td><?php echo $applicant->date_added; ?></td>
			<td <?
			 if ($applicant->why_me) { // they have why_me details
				echo "class='showAnswer'>";
			 } else {  // they do not have why_me details
			 	echo ">No answer";
			 }
			 ?>
			</td>
			<td style="min-width:200px" class="action">
			
				<?php if (!$applicant->HasUniqueEmail){?>
					Email is not unique. Cannot approve.
				<?php } else { ?>
					<?php if ($applicant->status_id==MembershipStatus::APPLICANT_AWAITING_APPROVAL
						  || $applicant->status_id==MembershipStatus::APPLICANT_DENIED) { ?>
						<?=g_anchor("admin/applicantmanagement/approveApplicant/" . $applicant->id, "Approve", array('class'=>'applicantConfirm')) ?>&nbsp;
						
					<?php }?>
				<?php }?>
				<?php if ($applicant->status_id==MembershipStatus::APPLICANT_AWAITING_APPROVAL
					  || $applicant->status_id==MembershipStatus::APPLICANT_APPROVED) { ?>
					  
					
					<?=g_anchor("admin/applicantmanagement/denyApplicant/" . $applicant->id, "Deny", array('class'=>'applicantConfirm')) ?>	
				<?php }?>
				<?
				
				$deleteaction = ($status_chosen == MembershipStatus::APPLICANT_APPROVED) ? "members/profile/delete/" : "admin/applicantmanagement/delete/";
				echo g_anchor($deleteaction . $applicant->id, "Delete", array('class'=>'applicantConfirm')) ?>
			</td>
		</tr>
		 <? if ($applicant->why_me) { // they have why_me details ?>
		 
		<tr id="<?="detail".$applicant->id?>" style="display:none">
		<td colspan="7" class="expandedrow" id="details"><div id="why_me"><?=stripslashes($applicant->why_me) ?></div></td>
		
		</tr>
		<? } //end why me ?>
	<? endforeach; ?>
<?

} else {

?>	<tr>
		<td colspan="7">No Recent Applicants</td>
	</tr>
<?
}
?>
</table>