<?php

/**
 * View Template for Member Profile Screen
 *
 * @joshcampbell
 * @viewtemplate
 */
 
 include_once APPPATH . 'libraries/enumerations.php';
?>


			<div class="formContainer" id="your-account-container">
			  <input type="hidden" id="editaction" name="editaction" value = "<?=site_url('grind-code/index.php/members/profile/edit/')?>">
			  <input type="hidden" id="user_id" name="user_id" value="<?=$member->id?>"/>
			  <input type="hidden" id="id_type" name="id_type" value=""/>
			  <input type="hidden" id="billingdata" name="billingdata" value="<?=$billingData?>"/>
			<div class="col membership">	
				<div class="profileSection" id="memberInfoSection">
					<h3>Member Information</h3>
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
							<p class="formNote">This is also the members username</p>
						</div>
						<div class="editItemContainer clearfix">
						  <p class="formNote">This is also the members username</p>
							<form id="email-form" rel="emailBlock" class="clearfix">
								<input type="hidden" id="pwhash" name="pwhash" value=""/>
								<input type="hidden" id="emailcheck" value="<?=site_url("/grind-code/index.php/admin/utility/uniqueEmail")?>" />

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
					<div class="itemContainer" id="rfidBlock">
						<div class="roItemContainer">
							<div class="roItem">
								<span id="rfid_ro"><?= $member->rfid == '' ? '<span class="placeholder">Member RFID/PIN</span>' : "RFID Card #: " .$member->rfid ?></span><br />
								<span id=""><?= $member->grind_uid == '' ? '<span class="placeholder">None</span>' : "Printing Username: " .$member->grind_uid ?></span>

							</div>
						</div>
						<div class="editItemContainer clearfix">
							<form id="rfid-form" rel="rfidBlock" class="clearfix">
								<div class="floatingFormItem">
									<input class="text-input" name="rfid" type="text" id="rfid" value="<?=$member->rfid?>" />
									<label for="rfid" class="inputLabel">RFID Card/Printing PIN</label>
								</div>
								<div class="update">
									<input type="submit" value="Update" class="updateItem btn" rel="rfid"/>
								</div>
							</form>
							<div class="cancelUpdate">
							  <a class="toggleit" rel="rfidBlock">Cancel</a>
							</div>
						</div>
					</div>

					<div class="itemContainer" id="phoneBlock">
						<div class="roItemContainer">
							<div class="roItem">
								<span id="phone_ro"><?= $member->phone == '' ? '<span class="placeholder">Member phone number</span>' : $member->phone ?></span>
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
					
					
					<div class="itemContainer" id="websiteBlock">
						<div class="roItemContainer">
							<div class="roItem">
								<span id="website_ro"><?= $member->website == '' ? '<span class="placeholder">Member Website</span>' : $member->website ?></span>
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
								<span id="company_description_ro"><?= $member->company_description == '' ? '<span class="placeholder">Member company description</span>' : $member->company_description ?></span>
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
								<span id="twitter_ro"><?= $member->twitter == '' ? '<span class="placeholder">Member Twitter account</span>' : $member->twitter ?></span>
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
								<span id="behance_ro"><?= $member->behance == '' ? '<span class="placeholder">Member Behance URL</span>' : $member->behance ?></span>
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
					<br />
					<div id="post-pw-reset" class="pw-reset" style="display:none">
					      		      We sent an email them to update their password.
					      		</div>
					            <div id="pre-pw-reset" style="display:block">
					            	<a id="pwdchange" href="#">Reset Password</a>
					            </div>
					            <div id="during-pw-reset" class="pw-reset" style="display:none">
					            Are you sure you want to reset this members password?<br /><br />
									<input type="hidden" id="resetaction" value="<?=site_url('/grind-code/index.php/members/profile/reset_pw/')?>"/>
					            	<input type="submit" name="pw" id="btn-pw-cancel" class="btn" value="No, just kidding" /> 
									&nbsp;
					            	<input type="submt" name="pw" id="btn-pw-reset" class="btn" value="Yes, Please" /> 
					            </div>
				</div>

				
				<div class="profileSection" id="membershipSection">
					<h3>Membership</h3>
					
					<div class="itemContainer" id="membershipTypeBlock">
						<div class="roItemContainer">
							<div class="roItem">
								<span id="membership_ro"><?=$plan_name?> <?=$member->subscription_state?></span><br />
								
							</div>
						</div>
						<div class="editItemContainer clearfix">
							<? if ($member->membership_status_luid == MembershipStatus::INACTIVE_MEMBER ) {
								// before this can really work, people would need to reassociate billing data with the account, we need to investigate if recurly billing info exists for the account. (however we don't know if it is valid).
								// we might need to try and create a new subscription or daily membership using the billing info on file. If no billing info on file then we should error (and open billing info fields) for them to add those first.
								// if the account was closed in recurly…we need to determine if we can still query the status through the API.  Closed accounts will never have a subscription, or a billing info.
								// open accounts assume they are at least a daily.  an inactive account could have an active subscription (expiring at a certain date)
								echo "This membership has been deactivated.  <a href='".site_url('/grind-code/member/profile/activate/'.$member->id)."'>Reactivate?</a>";
							} else {
						
								if($member->subscription_state != "active"){
									echo "There are pending changes on this account, manage membership in Recurly";
								} else {?>
							<form id="membership-form" action="<?=site_url("/grind-code/billing/account/changeMembership/")?>" rel="membershipTypeBlock" class="clearfix">
								<div class="floatingFormItem">
									<?=$membershipChooser?>
									<input type="hidden" value=<?=$member->plan_code?> id="current_plan" name="current_plan"/>
									<?// put the select there //?>
									<label for="membership" class="inputLabel">Choose Membership Type</label>
								</div>
								<div class="timeframe clear" display="block" style="line-height:20px;">
									<label class="inputLabel">Membership plan changes are processed at the end of the members billing period. For additional management options, make these changed directly in Recurly.</label>

									<? /*
									<!-- not complete -->
									<br />
									<label class="inputLabel">When should the change take effect?</label>
									<input type="radio" name="now" value="true" /> Change now<br />
									<input type="radio" name="now" value="false" /> Change at renewal<br />
									<!-- not complete -->
									*/ ?>
								</div>
								<div class="update">
									<input type="submit" value="Update" class="updateItem btn" rel="membership"/>
								</div>
								
							</form>
							<? }
							}// end membership_status_luid
							?>
							<div class="cancelUpdate">
							  <a class="toggleit" rel="membershipTypeBlock">Cancel</a>
							</div>
						</div>
					</div>
					<?php if (!$allow_monthly && ($member->plan_code == '' || $member->plan_code == 'daily') ) { ?>
						<div class="itemContainer" id="waitlistBlock">
							<div class="roItemContainer">
								<div class="roItem">
									<span id="waitlist_ro"><?= $waitlist == '1' ? '<span>Member is on waitlist</span>' : '<span class="placeholder">Memberships on hold, add member to waitlist?</span>' ?></span>
								</div>
							</div>
							<div class="editItemContainer clearfix">
								<form id="waitlist-form" rel="waitlistBlock" class="clearfix">
									<div class="floatingFormItem">
										<input type="checkbox" id="waitlist" name="waitlist" value="1" <?=$waitlist=='1' ? 'checked' : ''; ?> /> Waitlist?
										<label for="waitlist" class="inputLabel">Add/remove from waitlist</label>
									</div>
									<div class="update">
										<input type="submit" value="Update" class="updateItem btn" rel="waitlist"/>
									</div>
								</form>
								<div class="cancelUpdate">
								  <a class="toggleit" rel="waitlistBlock">Cancel</a>
								</div>
							</div>
						</div>
					<?php } ?>
					</div>
				</div> <!-- end col membership -->
	<div class="col account">
		<?=$acct_activity?><br />
		<div class="profileSection" id="billingInfoSection">
		<h3>Billing Details</h3>
			<div class="itemContainer" id="billingInfoBlock">
				<div class="roItemContainer">
					<div class="roItem">
						<?php 
						
							$setup_billing = "Setup a Credit Card for you account";
							
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
	</div>
	</div> <!-- end col account -->
	<div class="col checkins">
		<?=$checkins?>
	</div>
</div>


<script src="<?=site_url('/grind-code/js/admin_profile_temp.js')?>"></script>
