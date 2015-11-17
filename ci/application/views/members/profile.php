<?php

/**
 * View Template for Member Profile Screen
 *
 * @joshcampbell
 * @viewtemplate
 */

/**
 - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
  * @name:		profile stuff
  * @author:	@mattkosoy
  * @descrip:	forgive the mess here, but this is a quick fix....
  */

// the fields we're using.
$fields = array(
	'first_name'	=>	'Jerry',
	'last_name'		=>	'Grinder',
	'email'			=>	'Agora Email',
	'company_name'	=>	'Company Name',
	'company_desc'	=>	'Company Description',
//	'phone'			=>	'Phone ',
	'URL'			=>	'URL',
	'twitter'		=>  'Twitter URL...',
	'behance'		=>  'Behance URL...',
	'foursquare'	=>	'FourSquare URL...',
	'linkedin'		=>	'LinkedIn URL...',
	'facebook'		=>	'Facebook URL...',
	'dribbble'		=>	'Dribbble URL...',
	'i_need'		=>	'One walrus.'
);

$defaults = $fields;
// override the default w/ the data saved in WP
foreach($fields as $key=>$val){
	$tmp = get_user_meta($member->wp_users_id, $key, true);
	if($tmp != '' && $tmp != 'undefined' && $tmp != $val){
		// show wp usermeta data
		$fields[$key] = $tmp;
	}
}

// and our user's skillset
$skills = array();
$skills_classes =array(); // this will hold the html class attribute for skillset fields that have default values.  it's used to trigger the 'click and empty JS function'
for($i=1; $i<7; $i++){
	$skills[$i] = get_user_meta($member->wp_users_id, 'skill_'.$i, true);
	if($skills[$i] == ''){
		if($i == 1){
			$skills[$i] = 'I do this...';
		} else if($i == 6){
			$skills[$i] = 'And also this...';
		} else {
			$skills[$i] = 'And This...';
		}
	} 
	if($skills[$i] == 'I do this...' || $skills[$i] == 'And also this...' || $skills[$i] == 'And This...'){
		$skills_classes[$i] = ' default';
	} else {
		$skills_classes[$i] = ' ';
	}
}

?>

<div class="formContainer col" id="your-account-container">
<form id="user_profile_information" name="user_profile_information" enctype="multipart/form-data" action="<?=site_url('update-usermeta') ?>" method="POST">
  <input type="hidden" id="editaction" name="editaction" value = "<?=ROOTMEMBERPATH?>ci/members/profile/edit/">
  <input type="hidden" id="user_id" name="user_id" value="<?=$member->id?>"/>
  <input type="hidden" id="wp_id" name="wp_id" value="<?=$member->wp_users_id?>"/>
  <input type="hidden" id="id_type" name="id_type" value=""/>
  <input type="hidden" id="billingdata" name="billingdata" value="<?=$billingData?>"/>
  <input type="hidden" id="gs_action" name="gs_action" value="update-usermeta"/>

	<div class="profileSection" id="memberInfoSection">
		<h3>About Me:</h3>
		<div class="row">
			<div class="myAvatar col_1 left">
				<?php echo get_avatar($member->wp_users_id, 120); ?>
			</div> <!-- /avatar display -->
			<div class="chooseImage left">
				<a href="#" class="btn">Choose Image</a>
				<div style="position:absolute; top:-9999px; left:-9999px;">
					    <input type="hidden" id="MAX_FILE_SIZE" name="MAX_FILE_SIZE" value="3000000" />
					<input type="file" name="simple-local-avatar" id="simple-local-avatar" />
					<?php
						#$options = get_option('simple_local_avatars_caps');
						#if ( empty($options['simple_local_avatars_caps']) || current_user_can('upload_files') ) {
						#	do_action( 'simple_local_avatar_notices' ); 
							wp_nonce_field( 'simple_local_avatar_nonce', '_simple_local_avatar_nonce', false ); 
					?>
				</div>
			</div> <!-- /new avatar upload -->
			<small style="position:absolute; left: 230px; bottom:0; color:#ccc;">Max file size:  500K</small>
			<div class="col_1 left" id="new_image_filename" style="display:none;"></div>

		</div><!-- /row -->
		<div class="row">
			<!-- name/email info -->
			<? if($fields['first_name'] == $defaults['first_name']){  $class= 'class="default"';  } else { $class= ''; }?><div class="col_1 left"><input type="text" name="first_name" id="first_name" value="<?= $fields['first_name']; ?>"  <?= $class; ?>/></div>
			<? if($fields['last_name'] == $defaults['last_name']){  $class= 'class="default"';  } else { $class= ''; }?><div class="col_1 left"><input type="text" name="last_name" id="last_name" value="<?= $fields['last_name']; ?>"  <?= $class; ?>/></div>
			<? if($fields['email'] == $defaults['email']){ $class= 'class="default"';  } else { $class= ''; }?><div class="col_1 left"><input type="text" name="email" id="email" value="<?= $fields['email']; ?>"  <?= $class; ?>/></div>
		</div> <!-- /name&email -->
		<div class="row">
			<!-- company name ect -->
			<? if($fields['company_name'] == $defaults['company_name']){  $class= 'class="default"';  } else { $class= ''; }?><div class="col_1 left"><input type="text" name="company_name" id="company_name" value="<?= $fields['company_name']; ?>"   <?= $class; ?>/></div>
	<? /*		<? if($fields['phone'] == $defaults['first_name']){  $class= 'class="default"'; } else { $class= ''; }?><div class="col_1 left"><input type="text" name="phone" id="phone" value="<?= $fields['phone']; ?>"  <?= $class; ?>/></div> */ ?>
			<? if($fields['URL'] == $defaults['URL']){  $class= 'class="default"';  } else { $class= ''; }?><div class="col_1 left"><input type="text" name="URL" id="URL" value="<?= $fields['URL']; ?>" <?= $class; ?>/></div>	
		</div> <!-- /companyname, mobile, url -->
		<div class="row">
			<!-- descrip -->
			<div class="col_3 left">
				<? if($fields['company_desc'] == $defaults['company_desc']){  $class= 'class="default"';  } else { $class= ''; } ?>
				<textarea id="company_desc" name="company_desc" value="<?= $fields['company_desc']; ?>" <?= $class; ?> maxlength="282"><?= $fields['company_desc']; ?></textarea>
				<small style="position:absolute; left: 400px; bottom:0; color:#ccc;">Max 280 characters</small>
			</div>
		</div> <!-- /description -->
				
		<div class="row update">
			<input type="submit" class="btn" value="Update" id="about_me_btn" name="about_me_btn"/>
		</div>
		
	</div><!-- /about me -->
	
	
	<div class="profileSection" id="memberSkillSetSection">
		<h3>I know...</h3>

		<div class="row">
			<!-- my skills #1 -->
			<div class="col_1 left skillset"><span class="icon">&nbsp;</span><input type="text" name="skill_1" id="skill_1" value="<?= $skills[1]; ?>"  class="skillset <?= $skills_classes[1]; ?>" maxlength="15" /></div>
			<div class="col_1 left skillset"><span class="icon">&nbsp;</span><input type="text" name="skill_2" id="skill_2" value="<?= $skills[2]; ?>"  class="skillset <?= $skills_classes[2]; ?>" maxlength="15"/></div>
			<div class="col_1 left skillset"><span class="icon">&nbsp;</span><input type="text" name="skill_3" id="skill_3" value="<?= $skills[3]; ?>"  class="skillset <?= $skills_classes[3]; ?>" maxlength="15"/></div>
		</div> <!-- /my skills #1 -->
		<div class="row">
			<!-- my skills #2 -->
			<div class="col_1 left skillset"><span class="icon">&nbsp;</span><input type="text" name="skill_4" id="skill_4" value="<?= $skills[4]; ?>"  class="skillset <?= $skills_classes[4]; ?>" maxlength="15"/></div>
			<div class="col_1 left skillset"><span class="icon">&nbsp;</span><input type="text" name="skill_5" id="skill_5" value="<?= $skills[5]; ?>"  class="skillset <?= $skills_classes[5]; ?>" maxlength="15"/></div>
			<div class="col_1 left skillset"><span class="icon">&nbsp;</span><input type="text" name="skill_6" id="skill_6" value="<?= $skills[6]; ?>"  class="skillset <?= $skills_classes[6]; ?>" maxlength="15"/></div>	
		</div> <!-- /my skills #2 -->
		
		
		<h3>I need...</h3>
		<div class="row">
			<!-- i'm looking for -->
			<div class="col_3 left">
				<textarea id="i_need" name="i_need" value="<?= $fields['i_need']; ?>" maxlength="135"><?= $fields['i_need']; ?></textarea>
				<small style="position:absolute; left: 400px; color:#ccc; bottom:0;">Max 130 characters</small>
			</div>
		</div> <!-- /i'm looking for -->

		<div class="row update">
			<input type="submit" class="btn" value="Update" id="skillset_btn" name="skillset_btn"/>
		</div>

	</div><!-- /give & take -->

	<div class="profileSection" id="memberSocialNetsSection">
		<h3>Find me here:</h3>
		<p style="width: 66%;margin-bottom: 20px;color: #666;">Copy and paste the URL of your Behance, Twitter, FourSquare, LinkedIn, Facebook, or Dribble profile into the forms below.  Social and collaboration go hand in hand.</p>
		<div class="row">
			<!-- my skills #1 -->
			<? if($fields['behance'] == $defaults['behance']){  $class= 'class="default"';  } else { $class= ''; } ?><div class="col_1 left social behance"><span class="icon">&nbsp;</span><input type="text" name="behance" id="behance" value="<?= $fields['behance']; ?>" /></div>
			<? if($fields['twitter'] == $defaults['twitter']){  $class= 'class="default"';  } else { $class= ''; } ?><div class="col_1 left social twitter"><span class="icon">&nbsp;</span><input type="text" name="twitter" id="twitter" value="<?= $fields['twitter']; ?>" maxlength="20"/></div>
			<? if($fields['foursquare'] == $defaults['foursquare']){  $class= 'class="default"';  } else { $class= ''; } ?><div class="col_1 left social foursquare"><span class="icon">&nbsp;</span><input type="text" name="foursquare" id="foursquare" value="<?= $fields['foursquare']; ?>" /></div>
		</div> <!-- /my skills #1 -->
		<div class="row">
			<!-- my skills #2 -->
			<? if($fields['linkedin'] == $defaults['linkedin']){  $class= 'class="default"';  } else { $class= ''; } ?><div class="col_1 left social linkedin"><span class="icon">&nbsp;</span><input type="text" name="linkedin" id="linkedin" value="<?= $fields['linkedin']; ?>"  /></div>
			<? if($fields['facebook'] == $defaults['facebook']){  $class= 'class="default"';  } else { $class= ''; } ?><div class="col_1 left social facebook"><span class="icon">&nbsp;</span><input type="text" name="facebook" id="facebook" value="<?= $fields['facebook']; ?>" /></div>
			<? if($fields['dribbble'] == $defaults['dribbble']){  $class= 'class="default"';  } else { $class= ''; } ?><div class="col_1 left social dribbble"><span class="icon">&nbsp;</span><input type="text" name="dribbble" id="dribbble" value="<?= $fields['dribbble']; ?>"   /></div>	
		</div> <!-- /my skills #2 -->
		
		<div class="row update">
			<input type="submit" class="btn" value="Update" id="social_btn" name="social_btn"/>
		</div>
		
	</div> <!-- /find me here -->
</form>

<script type="text/javascript">var form = document.getElementById('user_profile_information');form.encoding = 'multipart/form-data';form.setAttribute('enctype', 'multipart/form-data');</script>

	<div class="profileSection" id="membershipSection">
		<h3>Membership Information:</h3>
		<div class="itemContainer" id="emailBlock">
			<div class="roItemContainer">
				<div class="roItem">
					<span id="email_ro"><?=$member->email?></span>
				</div>
				<p class="formNote">this is also your username</p>
			</div>
			<div class="editItemContainer clearfix">
			  <p class="formNote">Please remember this is also your username</p>
				<form id="email-form" rel="emailBlock" class="clearfix">
					<input type="hidden" id="pwhash" name="pwhash" value=""/>
					<input type="hidden" id="emailcheck" value="<?=site_url("ci/admin/utility/uniqueEmail")?>" />
					<div class="floatingFormItem">
						<input class="text-input email required" name="email" type="text" id="email" value="" />
						<label for="email" class="inputLabel">New Email</label>
					</div>
					<div class="floatingFormItem">
						<input class="text-input required" name="confirm_email" type="text" id="confirm_email" value="" />
						<label for="confirm_email" class="inputLabel">Confirm Email</label>
					</div>
					<div class="update">
						<input type="submit" value="Update" class="updateItem btn" rel="email"/>
					</div>
				</form>
				<div class="cancelUpdate">
					<a class="toggleit" rel="emailBlock">Cancel</a>
				</div>
			</div>
		</div>
		
	
	
	
		<h3>Your Plan</h3>
		<div class="itemContainer" id="membershipTypeBlock">
			<div class="roItemContainer">
				<div class="roItemNoEditBlack">
					<span id="membership_type_ro"><?=$plan_name?></span>
					 
					<div id="daily-membership_description" style="display:<?=($member->plan_code == 'daily') ? "block":"none";?>">You have a daily membership, each day you come to Grind, we charge the credit card on file below the daily membership fee: <?=$daily_price?></div>
					<div id="monthly-membership_description" style="display:<?=($member->plan_code == 'daily') ? "none":"block";?>">
					<div id="monthly-canceled" style="display:<?=($member->subscription_state == 'canceled') ? "block":"none";?>">								
						Your current membership is valid through <?=$end_date;?>,
						then your pending changes will take effect.
						</div>
					
					<div id="monthly-pending" style="display:<?=($membershipsuccess) ? "block":"none";?>"><div class="loader">&nbsp;</div>Gathering your membership details</div>
					<div id="monthly-active" style="display:<?=(($member->subscription_state == 'canceled')||($membershipsuccess)) ? "none":"block";?>">You have a recurring monthly membership.
					The credit card you have on file below will be charged <span id="monthly-membership_cost"><?= $member->total_amount_in_cents ?></span> on <span id="monthly_sub_end_date"><?=$end_date?></span>.
					</div>
					</div>
				</div>
			</div>
		</div>
		<? $display = ($member->plan_code == 'daily' || $member->plan_code == '') ? "block":"none";?>
		<div class="itemContainer" id="becomeAMemberBlock" style="display:<?=$display?>">
			<div class="roItemNoEdit">
				<?php if ($allow_monthly) { ?>
				<div>
					<a href="javascript:void(0);" id="becomeAMemberToggle"><strong>Become a monthly member</strong></a>
				</div>
				<?php } ?>
				<div id="becomeAMemberOptions" style="display:none">
					<?php if ($allow_monthly) { ?>
						By signing up for a <?= $monthly_plan->name; ?> membership, you will have unlimited access to Grind facilities for a rate of 
						<?= $monthly_plan->unit_amount_in_cents; ?> per month. At the end of each month we will charge you automatically for the next month.<br /><br />
						<input type="button" class="btn" id="addSubscription" value="Sign me up!" />
						<div class="loader" style="display:none"></div>
						<form id="becomeAMemberForm" style="display:none;" action="<?=site_url('ci/billing/account/update/'.$member->id.'/1/')?>">
							<input type="hidden" name="plan_code" value="<?php echo $monthly_plan->plan_code; ?>"/>
							<input type="hidden" name="hasBillingInfo" value="true"/>
						</form>
					<?php } else { ?>
						<form id="waitlist-form" rel="waitlistBlock" style="display:<?=$waitlist?'none':'block'?>;">
							We're sorry, but no more monthly memberships are currently available. Would you like to be added to our waiting list?
							<br /><br />
							<input type="hidden" id="waitlist" name="waitlist" value="1"/>
							<div class="updateWaitlist">
								<input type="submit" class="updateItem btn" id="addToWaitList" value="Yes, add me!" />
							</div>
						</form>
						<div id="alreadyOnWaitlist" style="display:<?=$waitlist?'block':'none'?>;">
							You are currently on the waitlist to become a monthly member.
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
		<? 
			if( $member->plan_code == 'daily') {
				$display = "none";
			} elseif ($member->subscription_state == 'canceled'){
				$display = "none";
			} else {
				$display = "block";
			}
			//$display = ($member->plan_code != 'daily' || ($member->subscription_state == 'canceled')) ? "none":"block";
			$cost=$member->total_amount_in_cents/100;
		
		?>
		<div class="itemContainer" id="cancelMemberBlock" style="display:<?=$display?>">
			<div class="roItemNoEdit">
				<div id="membership_type_renewal"></div>
				Interested in changing your plan? Send an email to <a href="mailto:membership@grindspaces.com">membership@grindspaces.com</a>. Please note that for all monthly subscriptions, we require at least 2 weeks notice prior to your renewal date in order to make adjustments.
			</div>
		</div>
	</div>



	<div class="profileSection" id="billingInfoSection">
		<h3>Billing Details</h3>
		<div class="itemContainer" id="billingInfoBlock">
			<div class="roItemContainer">
				<div class="roItem">
				<?php 
							$setup_billing = "Setup a Credit Card for your account";
							
							$cc_placeholder = $billingData ? $member->billing_info->card_type." ending in ".$member->billing_info->last_four : $setup_billing;
							
							// for the future
							$company_billing = "Your account is billed to a corporate account";
							//$cc_placeholder = $company_billing;
							// for now
							$company_billing = false;
							
							
						?>
						<span id="cc_type_last_four_ro"><?=$cc_placeholder?></span>
				</div>
			</div>
				<? if($company_billing == false) { 	
					echo $billing_form;
				 } // not company billing single user or THE corporate account 
				 ?>	
		</div>
		<div class="itemContainer" name="invoiceBlock">
			<div class="roItemContainer">
				<div class="roItemNoEdit" id="invoices">
					
				</div>
			</div>
		</div>
	</div>

		<h3>Terms &amp; Conditions</h3>
		<div class="itemContainer" id="emailBlock">
			<a href="<?php bloginfo('url'); ?>/terms-and-conditions">Read em</a>
		</div>
	



</div> <!-- / your account container -->

<? 

/*

* Old HTML for reference 
* or a "just in case" scenario. 
* :paid
<div class="itemContainer" id="fullNameBlock">
	<div class="roItemContainer">
		<div class="roItem">
			<span id="first_name_ro"><?=$member->first_name?></span> <span id="last_name_ro"><?=$member->last_name?></span>
		</div>
	</div>
	<div class="editItemContainer clearfix">
		<form id="fullName-form" rel="fullNameBlock" class="clearfix">
			<div class="floatingFormItem">
				<input class="text-input required" name="first_name" type="text" id="firstName" value="<?=$member->first_name?>" />
			</div>
			<div class="floatingFormItem">
				<input class="text-input" name="last_name" type="text" id="lastName" value="<?=$member->last_name?>" />
			</div>
			<div class="update">
				<input type="submit" value="Update" class="updateItem btn" rel="fullName"/>
			</div>
		</form>
		<div class="cancelUpdate">
			<a class="toggleit" rel="fullNameBlock">Cancel</a>
		</div>
	</div>
</div>

<div class="itemContainer" id="companyNameBlock">
	<div class="roItemContainer">
		<div class="roItem">
			<span id="company_name_ro"><?= $member->company_name == '' ? '<span class="placeholder">Your company name</span>' : $member->company_name ?></span>
		</div>
	</div>
	<div class="editItemContainer clearfix"> 
		<form id="companyName-form" rel="companyNameBlock" class="clearfix">
			<div class="floatingFormItem">
				<input class="text-input" name="company_name" type="text" id="company_name" value="<?=$member->company_name?>" />
				<label for="company_name" class="inputLabel">Company Name</label>
			</div>
			<div class="update">
				<input type="submit" value="Update" class="updateItem btn" rel="companyName"/>
			</div>
		</form>
		<div class="cancelUpdate">
			<a class="toggleit" rel="companyNameBlock">Cancel</a>
		</div>
	</div>
</div>
<div class="itemContainer" id="emailBlock">
	<div class="roItemContainer">
		<div class="roItem">
			<span id="email_ro"><?=$member->email?></span>
		</div>
		<p class="formNote">this is also your username</p>
	</div>
	<div class="editItemContainer clearfix">
	  <p class="formNote">Please remember this is also your username</p>
		<form id="email-form" rel="emailBlock" class="clearfix">
			<input type="hidden" id="pwhash" name="pwhash" value=""/>
			<input type="hidden" id="emailcheck" value="<?=site_url("ci/admin/utility/uniqueEmail")?>" />
			<div class="floatingFormItem">
				<input class="text-input email required" name="email" type="text" id="email" value="" />
				<label for="email" class="inputLabel">New Email</label>
			</div>
			<div class="floatingFormItem">
				<input class="text-input required" name="confirm_email" type="text" id="confirm_email" value="" />
				<label for="confirm_email" class="inputLabel">Confirm Email</label>
			</div>
			<div class="update">
				<input type="submit" value="Update" class="updateItem btn" rel="email"/>
			</div>
		</form>
		<div class="cancelUpdate">
			<a class="toggleit" rel="emailBlock">Cancel</a>
		</div>
	</div>
</div>
<div class="itemContainer" id="phoneBlock">
	<div class="roItemContainer">
		<div class="roItem">
			<span id="phone_ro"><?= $member->phone == '' ? '<span class="placeholder">Your phone number</span>' : $member->phone ?></span>
		</div>
	</div>
	<div class="editItemContainer clearfix">
		<form id="phone-form" rel="phoneBlock" class="clearfix">
			<div class="floatingFormItem">
				<input class="text-input" name="phone" type="text" id="phone" value="<?=$member->phone?>" />
				<label for="phone" class="inputLabel">Phone Number</label>
			</div>
			<div class="update">
				<input type="submit" value="Update" class="updateItem btn" rel="phone"/>
			</div>
		</form>
		<div class="cancelUpdate">
		  <a class="toggleit" rel="phoneBlock">Cancel</a>
		</div>
	</div>
</div>
<div class="itemContainer" id="passwordBlock">
	<div class="roItemContainer">
		<div class="roItem">
			<span>Password: ********</span>
		</div>
	</div>
	<div class="editItemContainer clearfix">
		<form id="password-form" rel="passwordBlock" class="clearfix">
		<div class="floatingFormItem current-pass">
			<input class="text-input required" name="current_password" type="password" id="current_password" value="" />
			<label for="email" class="inputLabel">Current Password</label>
			</div>
			<div class="floatingFormItem">
				<input class="text-input required" name="password" type="password" id="password" value="" />
				<label for="password" class="inputLabel">New Password</label>
			</div>
			<div class="floatingFormItem">
				<input class="text-input required" name="password_confirm" type="password" id="password_confirm" value="" />
				<label for="password_confirm" class="inputLabel">New Password</label>
			</div>
			<div class="update">
				<input type="submit" value="Update" class="updateItem btn" rel="password"/>
			</div>
		</form>
		<div class="cancelUpdate">
			<a class="toggleit" rel="passwordBlock">Cancel</a>
		</div>
	</div>
</div>

<div class="itemContainer" id="websiteBlock">
	<div class="roItemContainer">
		<div class="roItem">
			<span id="website_ro"><?= $member->website == '' ? '<span class="placeholder">Your Website</span>' : $member->website ?></span>
		</div>
	</div>
	<div class="editItemContainer clearfix">
		<form id="website-form" rel="websiteBlock" class="clearfix">
			<div class="floatingFormItem">
				<input class="text-input" name="website" type="text" id="website" value="<?=$member->website?>" />
				<label for="website" class="inputLabel">Company Website</label>
			</div>
			<div class="update">
				<input type="submit" value="Update" class="updateItem btn" rel="website"/>
			</div>
		</form>
		<div class="cancelUpdate">
		  <a class="toggleit" rel="websiteBlock">Cancel</a>
		</div>
	</div>
</div>

<div class="itemContainer" id="companyDescriptionBlock">
	<div class="roItemContainer">
		<div class="roItem">
			<span id="company_description_ro"><?= $member->company_description == '' ? '<span class="placeholder">Your company description</span>' : $member->company_description ?></span>
		</div>
	</div>
	<div class="editItemContainer clearfix">
		<form id="companyDescription-form" rel="companyDescriptionBlock" class="clearfix">
			<div class="floatingFormItem">
				<label for="company_description" class="inputLabel above">Company Description</label>
				<textarea cols="50" rows="4" name="company_description" id="company_description"><?=$member->company_description?></textarea>
			</div>
			<div class="update">
				<input type="submit" value="Update" class="updateItem btn" rel="companyDescription"/>
			</div>
		</form>
		<div class="cancelUpdate">
		<a class="toggleit" rel="companyDescriptionBlock">Cancel</a>
		</div>
	</div>
</div>

<div class="itemContainer" id="twitterBlock">
	<div class="roItemContainer">
		<div class="roItem">
			<span id="twitter_ro"><?= $member->twitter == '' ? '<span class="placeholder">Your Twitter account</span>' : $member->twitter ?></span>
		</div>
	</div>
	<div class="editItemContainer clearfix">
		<form id="twitter-form" rel="twitterBlock" class="clearfix">
			<div class="floatingFormItem">
				<input class="text-input" name="twitter" type="text" id="twitter" value="<?=$member->twitter?>" />
				<label for="twitter" class="inputLabel">Twitter</label>
			</div>
			<div class="update">
				<input type="submit" value="Update" class="updateItem btn" rel="twitter"/>
			</div>
		</form>
		<div class="cancelUpdate">
		  <a class="toggleit" rel="twitterBlock">Cancel</a>
		</div>
	</div>
</div>

<div class="itemContainer" id="behanceBlock">
	<div class="roItemContainer">
		<div class="roItem">
			<span id="behance_ro"><?= $member->behance == '' ? '<span class="placeholder">Your Behance URL</span>' : $member->behance ?></span>
		</div>
	</div>
	<div class="editItemContainer clearfix">
		<form id="behance-form" rel="behanceBlock" class="clearfix">
			<div class="floatingFormItem">
				<input class="text-input" name="behance" type="text" id="behance" value="<?=$member->behance?>" />
				<label for="behance" class="inputLabel">Behance URL</label>
			</div>
			<div class="update">
				<input type="submit" value="Update" class="updateItem btn" rel="behance"/>
			</div>
		</form>
		<div class="cancelUpdate">
		  <a class="toggleit" rel="behanceBlock">Cancel</a>
		</div>
	</div>
</div>
</div>

<div class="profileSection" id="membershipSection">
<h3>Membership</h3>
<div class="itemContainer" id="membershipTypeBlock">
	<div class="roItemContainer">
		<div class="roItemNoEditBlack">
			<span id="membership_type_ro"><?=$plan_name?></span>
			 
			<div id="daily-membership_description" style="display:<?=($member->plan_code == 'daily') ? "block":"none";?>">You have a daily membership, each day you come to Grind, we charge the credit card on file below the daily membership fee: <?=$daily_price?></div>
			<div id="monthly-membership_description" style="display:<?=($member->plan_code == 'daily') ? "none":"block";?>">
			<div id="monthly-canceled" style="display:<?=($member->subscription_state == 'canceled') ? "block":"none";?>">								
				Your current membership is valid through <?=$end_date;?>,
				then your pending changes will take effect.
				</div>
			
			<div id="monthly-pending" style="display:<?=($membershipsuccess) ? "block":"none";?>"><div class="loader">&nbsp;</div>Gathering your membership details</div>
			<div id="monthly-active" style="display:<?=(($member->subscription_state == 'canceled')||($membershipsuccess)) ? "none":"block";?>">You have a recurring monthly membership.
			The credit card you have on file below will be charged <span id="monthly-membership_cost"><?= $member->total_amount_in_cents ?></span> on <span id="monthly_sub_end_date"><?=$end_date?></span>.
			</div>
			</div>
		</div>
	</div>
</div>
<? $display = ($member->plan_code == 'daily' || $member->plan_code == '') ? "block":"none";?>
<div class="itemContainer" id="becomeAMemberBlock" style="display:<?=$display?>">
	<div class="roItemNoEdit">
		<?php if ($allow_monthly) { ?>
		<div>
			<a href="javascript:void(0);" id="becomeAMemberToggle"><strong>Become a monthly member</strong></a>
		</div>
		<?php } ?>
		<div id="becomeAMemberOptions" style="display:none">
			<?php if ($allow_monthly) { ?>
				By signing up for a <?= $monthly_plan->name; ?> membership, you will have unlimited access to Grind facilities for a rate of 
				<?= $monthly_plan->unit_amount_in_cents; ?> per month. At the end of each month we will charge you automatically for the next month.<br /><br />
				<input type="button" class="btn" id="addSubscription" value="Sign me up!" />
				<div class="loader" style="display:none"></div>
				<form id="becomeAMemberForm" style="display:none;" action="<?=site_url('ci/billing/account/update/'.$member->id.'/1/')?>">
					<input type="hidden" name="plan_code" value="<?php echo $monthly_plan->plan_code; ?>"/>
					<input type="hidden" name="hasBillingInfo" value="true"/>
				</form>
			<?php } else { ?>
				<form id="waitlist-form" rel="waitlistBlock" style="display:<?=$waitlist?'none':'block'?>;">
					We're sorry, but no more monthly memberships are currently available. Would you like to be added to our waiting list?
					<br /><br />
					<input type="hidden" id="waitlist" name="waitlist" value="1"/>
					<div class="updateWaitlist">
						<input type="submit" class="updateItem btn" id="addToWaitList" value="Yes, add me!" />
					</div>
				</form>
				<div id="alreadyOnWaitlist" style="display:<?=$waitlist?'block':'none'?>;">
					You are currently on the waitlist to become a monthly member.
				</div>
			<?php } ?>
		</div>
	</div>
</div>
<? 
	if( $member->plan_code == 'daily') {
		$display = "none";
	} elseif ($member->subscription_state == 'canceled'){
		$display = "none";
	} else {
		$display = "block";
	}
	//$display = ($member->plan_code != 'daily' || ($member->subscription_state == 'canceled')) ? "none":"block";
	$cost=$member->total_amount_in_cents/100;

?>
<div class="itemContainer" id="cancelMemberBlock" style="display:<?=$display?>">
	<div class="roItemNoEdit">
		<div id="membership_type_renewal"></div>
		<div>
			<div><a href="javascript:void(0);" id="cancelSubscription"><strong>Switch to Daily Membership</strong></a></div>Daily members are charged <?=$daily_price?> on a per use basis. Each day you check in at Grind, we charge your account for the day. Easy as that.<br /><br />
			</div>
		<div id="cancelSubscriptionOptions" style="display:none;">
			You've already paid up for a monthly membership till <?=$end_date?>. We can switch you to daily automatically at that time.<br />Just swing by the Grind front desk, <a href="mailto:admin@grindspaces.com">send us an email</a>, or give us a call and we'll get you switched over.
			<br />
			<!-- TEMPORARY HIDING
			
			<input type="button" class="btn" id="cancelSubLater" value="Make the Switch" />
			<br />
			<input type="hidden" id ="cancelaction" value="<?=site_url('ci/billing/account/update/'.$member->id.'/0/')?>"/>
			-->
		</div>
	</div>
</div>
</div>

<div class="profileSection" id="billingInfoSection">
<h3>Billing Details</h3>
<div class="itemContainer" id="billingInfoBlock">
<div class="roItemContainer">
<div class="roItem">
<?php 

	$setup_billing = "Setup a Credit Card for your account";
	
	$cc_name = false;
	if ($billingData){
		switch ($member->billing_info->credit_card->type) {
			case "visa":
				$cc_name = CreditCardNames::CC_VISA;
			break;
			case "discover":
				$cc_name = CreditCardNames::CC_DISCOVER;
			break;
			case "american_express":
				$cc_name = CreditCardNames::CC_AMEX;
			break;
			case "mastercard":
				$cc_name = CreditCardNames::CC_MC;
			break;
		}
	}
	
	$cc_placeholder = $billingData ? $cc_name." ending in ".$member->billing_info->credit_card->last_four : $setup_billing;
	
	// for the future
	$company_billing = "Your account is billed to a corporate account";
	//$cc_placeholder = $company_billing;
	// for now
	$company_billing = false;
	
	
?>
<span id="cc_type_last_four_ro"><?=$cc_placeholder?></span>
</div>
</div>
<? if($company_billing == false) { 	
echo $billing_form;
} // not company billing single user or THE corporate account ?>	
</div>
<div class="itemContainer" name="invoiceBlock">
<div class="roItemContainer">
<div class="roItemNoEdit" id="invoices">

</div>
</div>
</div>
</div>

*/ ?>