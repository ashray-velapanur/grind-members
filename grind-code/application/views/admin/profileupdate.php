<link rel="stylesheet" href="<?=ROOTMEMBERPATH?>wp-content/themes/css/style.css">

<?php
/**
 * Template Name: Profile Page
 # DEPRECATED
 */

global $current_user, $user_ID, $wp_roles;
//get_currentuserinfo();
//get_header();

//$monthlyPlan = GetMonthlyPlan();
//$dailyRate = GetDailyRate();
?>

	<!--?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?-->
			
			<?php if(isset($_REQUEST['membershipsuccess'])) {?>
				<div class="membershipSuccess">
					Congratulations, You&rsquo;re in! Feel free to change your profile information below.....blah blah.
					This can be changed in theme-dir/profile-template.php line 432.
				</div>
			<?php }?>
			
			<p class="instruction"><!--?php the_content(); ?--></p>
			
			<hr class="pagehead">
			
			<div class="formContainer" id="your-account-container">
			  
			
				<div class="profileSection" id="memberInfoSection">
					<h3>Member Information</h3>
					<div class="itemContainer" id="fullNameBlock">
						<div class="roItemContainer">
							<div class="roItem">
								<span id="full_name_ro"></span>
							</div>
						</div>
						<div class="editItemContainer clearfix">
							<form id="fullName-form" rel="fullNameBlock" class="clearfix">
								<input type="hidden" id="wp_users_id" name="wp_users_id" value="<?php echo $user_ID; ?>"/>
								<div class="floatingFormItem">
									<input class="text-input" name="first_name" type="text" id="first_name" value="" />
								</div>
								<div class="floatingFormItem">
									<input class="text-input" name="last_name" type="text" id="last_name" value="" />
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
								<span id="company_name_ro"></span>
							</div>
						</div>
						<div class="editItemContainer clearfix"> 
							<form id="companyName-form" rel="companyNameBlock" class="clearfix">
								<input type="hidden" id="wp_users_id" name="wp_users_id" value="<?php echo $user_ID; ?>"/>
								<div class="floatingFormItem">
									<input class="text-input" name="company_name" type="text" id="company_name" value="" />
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
								<span id="email_ro"></span>
							</div>
							<p class="formNote">this is also your username</p>
						</div>
						<div class="editItemContainer clearfix">
						  <p class="formNote">Please remember this is also your username</p>
							<form id="email-form" rel="emailBlock" class="clearfix">
								<input type="hidden" id="pwhash" name="pwhash" value="<?php echo $current_user->user_pass;?>"/>
								<input type="hidden" id="wp_users_id" name="wp_users_id" value="<?php echo $user_ID; ?>"/>
								<div class="floatingFormItem">
									<input class="text-input" name="email" type="text" id="email" value="" />
									<label for="email" class="inputLabel">New Email</label>
								</div>
								<div class="floatingFormItem">
									<input class="text-input" name="confirm_email" type="text" id="confirm_email" value="" />
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
					
					<div class="itemContainer" id="passwordBlock">
						<div class="roItemContainer">
							<div class="roItem">
								<span>Password: ********</span>
							</div>
						</div>
						<div class="editItemContainer clearfix">
							<form id="password-form" rel="passwordBlock" class="clearfix">
						    <div class="floatingFormItem current-pass">
  								<input class="text-input" name="current_password" type="password" id="current_password" value="" />
  								<label for="email" class="inputLabel">Current Password</label>
								</div>
								<div class="floatingFormItem">
									<input class="text-input" name="password" type="password" id="password" value="" />
									<label for="password" class="inputLabel">New Password</label>
								</div>
								<div class="floatingFormItem">
									<input class="text-input" name="password_confirm" type="password" id="password_confirm" value="" />
									<label for="password_confirm" class="inputLabel">New Password</label>
								</div>
								<div class="update">
									<input type="submit" value="Update" class="updateItem btn" rel="password"/>
								</div>
								<!-- needed?
								<div style="float:right;font-size:smaller;margin-right:20px;margin-top:25px;">
									<a href="wp-login.php?action=lostpassword" title="Reset Password">Lost your password?</a>
								</div>
								-->
							</form>
							<div class="cancelUpdate">
								<a class="toggleit" rel="passwordBlock">Cancel</a>
							</div>
						</div>
					</div>
					
					<div class="itemContainer" id="websiteBlock">
						<div class="roItemContainer">
							<div class="roItem">
								<span id="website_ro">Website URL</span>
							</div>
						</div>
						<div class="editItemContainer clearfix">
							<form id="website-form" rel="websiteBlock" class="clearfix">
								<input type="hidden" id="wp_users_id" name="wp_users_id" value="<?php echo $user_ID; ?>"/>
								<div class="floatingFormItem">
									<input class="text-input" name="website" type="text" id="website" value="" />
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
								<span id="company_description_ro"></span>
							</div>
						</div>
						<div class="editItemContainer clearfix">
							<form id="companyDescription-form" rel="companyDescriptionBlock" class="clearfix">
								<input type="hidden" id="wp_users_id" name="wp_users_id" value="<?php echo $user_ID; ?>"/>
								<div class="floatingFormItem">
									<label for="company_description" class="inputLabel above">Company Description</label>
									<textarea cols="50" rows="4" name="company_description" id="company_description" value="" ></textarea>
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
								<span id="twitter_ro"></span>
							</div>
						</div>
						<div class="editItemContainer clearfix">
							<form id="twitter-form" rel="twitterBlock" class="clearfix">
								<input type="hidden" id="wp_users_id" name="wp_users_id" value="<?php echo $user_ID; ?>"/>
								<div class="floatingFormItem">
									<input class="text-input" name="twitter" type="text" id="twitter" value="" />
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
								<span id="behance_ro"></span>
							</div>
						</div>
						<div class="editItemContainer clearfix">
							<form id="behance-form" rel="behanceBlock" class="clearfix">
								<input type="hidden" id="wp_users_id" name="wp_users_id" value="<?php echo $user_ID; ?>"/>
								<div class="floatingFormItem">
									<input class="text-input" name="behance" type="text" id="behance" value="" />
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
								<span id="membership_type_ro">
								</span>
							</div>
						</div>
					</div>
					<div class="itemContainer" id="becomeAMemberBlock" style="display:none;">
						<div class="roItemNoEdit">
							<div>
								<a href="javascript:void(0);" id="becomeAMemberToggle">Become a monthly member</a>
							</div>
							<div id="becomeAMemberOptions" style="display:none;">
								<?php if (AllowMonthlyMemberships() === "1") { ?>
									<strong><?php echo $monthlyPlan->name; ?> - $<?php echo $monthlyPlan->unit_amount_in_cents/100; ?> per month</strong><br/>
									By signing up, you will have unlimited access to Grind facilities for a rate of 
									$<?php echo $monthlyPlan->unit_amount_in_cents/100; ?> per month.
									<a href="javascript:void(0);" id="becomeAMember">Sign me up!</a>
									<form id="becomeAMemberForm" style="display:none;">
										<input type="hidden" name="plan_code" value="<?php echo $monthlyPlan->plan_code; ?>"/>
										<input type="hidden" name="hasBillingInfo" value="true"/>
									</form>
								<?php } else { ?>
									We're sorry, but no more monthly memberships are currently available. Would you like
									to be added to our waiting list?
									<a href="javascript:void(0);" id="addToWaitList">Yes, add me!</a>
								<?php } ?>
							</div>
						</div>
					</div>
					<div class="itemContainer" id="cancelMemberBlock" style="display:none;">
						<div class="roItemNoEdit">
							<div id="membership_type_renewal"></div>
							<div>
								<a href="javascript:void(0);" id="cancelSubscription">Cancel Subscription</a>
							</div>
							<div id="cancelSubscriptionOptions" style="display:none;">
								<a href="javascript:void(0);" id="cancelSubNow">Cancel now</a>, or
								<a href="javascript:void(0);" id="cancelSubLater">cancel on <span id="subEndDate"></span></a>
							</div>
						</div>
					</div>
				</div>
				
				<div class="profileSection" id="billingInfoSection">
					<h3>Billing Details</h3>
					<div class="itemContainer" id="billingInfoBlock">
						<div class="roItemContainer">
							<div class="roItem">
								<span id="cc_type_last_four_ro"></span>
							</div>
						</div>
						<div class="editItemContainer clearfix"> 
              <form id="billingInfo-form" rel="billingInfoBlock">
                <div class="form-row clearfix">
                  <div class="form-col">
                    <label for="first_name">First Name</label>
                    <input class="required" id="first_name" maxlength="20" name="billing_info[first_name]" type="text" />
                  </div>
                  <div class="form-col">
                    <label for="last_name">Last Name</label>
                    <input class="required" id="last_name" maxlength="20" name="billing_info[last_name]" type="text" />
                  </div>
                </div>
                <div class="form-row clearfix">
                  <div class="form-col with-spacer">
                    <label for="credit_card_number">Credit Card Number</label>
                    <input class="required" id="credit_card_number" maxlength="20" name="credit_card[number]" type="text" />
                  </div>
                  <div class="form-col">
                    <label for="credit_card_verification_value">Sec. Code</label>
                    <input class="required" id="credit_card_verification_value" maxlength="4" name="credit_card[verification_value]" size="4" type="text" />
                  </div>
                </div>
                <div class="form-row expiration-date clearfix">
                  <label for="credit_card_month">Expiration Date</label>
                  <select id="credit_card_month" name="credit_card[month]" class="required">
                	<?php
                	  $months = array("January", "February", "March", "April", "May", "June", "July",
                		"August", "September", "October", "November", "December");
                	  for ($month = 1; $month <= 12; $month++) {
                		  print "<option value=\"$month\">" . $months[$month - 1] . "</option>\n";
                	  }
                	?>
                  </select>
                  <select id="credit_card_year" name="credit_card[year]" class="required">
                  	<?php 
                  	  $date = getdate();
                  	  $current_year = $date['year'];
                  	  for ($year = $current_year; $year <= $current_year + 10; $year++) {
                  		  print "<option value=\"$year\">$year</option>\n";
                  	  }
                  	?>
                  </select>
                </div>
                <div class="form-row">
                  <label for="billing_info_address1">Address</label>
                  <input class="required" id="billing_info_address1" maxlength="50" name="billing_info[address1]" type="text" />
                </div>
                <div class="form-row">
                  <label for="billing_info_address2">Address 2</label>
                  <input id="billing_info_address2" maxlength="50" name="billing_info[address2]" type="text" />
                </div>
                <div class="form-row clearfix">
                  <div class="form-col">
                    <label for="billing_info_city">City</label>
                    <input class="required" id="billing_info_city" maxlength="50" name="billing_info[city]" type="text" />
                  </div>
                  <div class="form-col">
                    <label for="billing_info_state">State</label>
                    <input class="required" id="billing_info_state" maxlength="50" name="billing_info[state]" type="text" />
                  </div>
                  <div class="form-col">
                    <label for="billing_info_zip">Zip</label>
                    <input class="required" id="billing_info_zip" maxlength="20" name="billing_info[zip]" type="text" />
                  </div>
                </div>
                <div class="form-row">
                  <label for="billing_info_country">Country</label>
                  <select id="billing_info_country" name="billing_info[country]" class="required">
                  	<option value="AF">Afghanistan</option>
                  	<option value="AL">Albania</option>
                  	<option value="DZ">Algeria</option>
                  	<option value="AS">American Samoa</option>
                  	<option value="AD">Andorra</option>
                  	<option value="AO">Angola</option>
                  	<option value="AI">Anguilla</option>
                  	<option value="AG">Antigua and Barbuda</option>
                  	<option value="AR">Argentina</option>
                  	<option value="AM">Armenia</option>
                  	<option value="AW">Aruba</option>
                  	<option value="AU">Australia</option>
                  	<option value="AT">Austria</option>
                  	<option value="AX">Åland Islands</option>
                  	<option value="AZ">Azerbaijan</option>
                  	<option value="BS">Bahamas</option>
                  	<option value="BH">Bahrain</option>
                  	<option value="BD">Bangladesh</option>
                  	<option value="BB">Barbados</option>
                  	<option value="BY">Belarus</option>
                  	<option value="BE">Belgium</option>
                  	<option value="BZ">Belize</option>
                  	<option value="BJ">Benin</option>
                  	<option value="BM">Bermuda</option>
                  	<option value="BT">Bhutan</option>
                  	<option value="BO">Bolivia</option>
                  	<option value="BA">Bosnia and Herzegovina</option>
                  	<option value="BW">Botswana</option>
                  	<option value="BV">Bouvet Island</option>
                  	<option value="BR">Brazil</option>
                  	<option value="BN">Brunei Darussalam</option>
                  	<option value="IO">British Indian Ocean Territory</option>
                  	<option value="BG">Bulgaria</option>
                  	<option value="BF">Burkina Faso</option>
                  	<option value="BI">Burundi</option>
                  	<option value="KH">Cambodia</option>
                  	<option value="CM">Cameroon</option>
                  	<option value="CA">Canada</option>
                  	<option value="CV">Cape Verde</option>
                  	<option value="KY">Cayman Islands</option>
                  	<option value="CF">Central African Republic</option>
                  	<option value="TD">Chad</option>
                  	<option value="CL">Chile</option>
                  	<option value="CN">China</option>
                  	<option value="CX">Christmas Island</option>
                  	<option value="CC">Cocos (Keeling) Islands</option>
                  	<option value="CO">Colombia</option>
                  	<option value="KM">Comoros</option>
                  	<option value="CG">Congo</option>
                  	<option value="CD">Congo, the Democratic Republic of the</option>
                  	<option value="CK">Cook Islands</option>
                  	<option value="CR">Costa Rica</option>
                  	<option value="CI">Cote D'Ivoire</option>
                  	<option value="HR">Croatia</option>
                  	<option value="CU">Cuba</option>
                  	<option value="CY">Cyprus</option>
                  	<option value="CZ">Czech Republic</option>
                  	<option value="DK">Denmark</option>
                  	<option value="DJ">Djibouti</option>
                  	<option value="DM">Dominica</option>
                  	<option value="DO">Dominican Republic</option>
                  	<option value="EC">Ecuador</option>
                  	<option value="EG">Egypt</option>
                  	<option value="SV">El Salvador</option>
                  	<option value="GQ">Equatorial Guinea</option>
                  	<option value="ER">Eritrea</option>
                  	<option value="EE">Estonia</option>
                  	<option value="ET">Ethiopia</option>
                  	<option value="FK">Falkland Islands (Malvinas)</option>
                  	<option value="FO">Faroe Islands</option>
                  	<option value="FJ">Fiji</option>
                  	<option value="FI">Finland</option>
                  	<option value="FR">France</option>
                  	<option value="GF">French Guiana</option>
                  	<option value="PF">French Polynesia</option>
                  	<option value="TF">French Southern Territories</option>
                  	<option value="GA">Gabon</option>
                  	<option value="GM">Gambia</option>
                  	<option value="GE">Georgia</option>
                  	<option value="DE">Germany</option>
                  	<option value="GH">Ghana</option>
                  	<option value="GI">Gibraltar</option>
                  	<option value="GR">Greece</option>
                  	<option value="GL">Greenland</option>
                  	<option value="GD">Grenada</option>
                  	<option value="GP">Guadeloupe</option>
                  	<option value="GU">Guam</option>
                  	<option value="GT">Guatemala</option>
                  	<option value="GN">Guinea</option>
                  	<option value="GW">Guinea-Bissau</option>
                  	<option value="GY">Guyana</option>
                  	<option value="GG">Guernsey</option>
                  	<option value="HT">Haiti</option>
                  	<option value="VA">Holy See (Vatican City State)</option>
                  	<option value="HN">Honduras</option>
                  	<option value="HK">Hong Kong</option>
                  	<option value="HM">Heard Island And Mcdonald Islands</option>
                  	<option value="HU">Hungary</option>
                  	<option value="IS">Iceland</option>
                  	<option value="IN">India</option>
                  	<option value="ID">Indonesia</option>
                  	<option value="IR">Iran, Islamic Republic of</option>
                  	<option value="IQ">Iraq</option>
                  	<option value="IE">Ireland</option>
                  	<option value="IM">Isle Of Man</option>
                  	<option value="IL">Israel</option>
                  	<option value="IT">Italy</option>
                  	<option value="JM">Jamaica</option>
                  	<option value="JP">Japan</option>
                  	<option value="JE">Jersey</option>
                  	<option value="JO">Jordan</option>
                  	<option value="KZ">Kazakhstan</option>
                  	<option value="KE">Kenya</option>
                  	<option value="KI">Kiribati</option>
                  	<option value="KP">Korea, Democratic People's Republic of</option>
                  	<option value="KR">Korea, Republic of</option>
                  	<option value="KW">Kuwait</option>
                  	<option value="KG">Kyrgyzstan</option>
                  	<option value="LA">Lao People's Democratic Republic</option>
                  	<option value="LV">Latvia</option>
                  	<option value="LB">Lebanon</option>
                  	<option value="LS">Lesotho</option>
                  	<option value="LR">Liberia</option>
                  	<option value="LY">Libyan Arab Jamahiriya</option>
                  	<option value="LI">Liechtenstein</option>
                  	<option value="LT">Lithuania</option>
                  	<option value="LU">Luxembourg</option>
                  	<option value="MO">Macao</option>
                  	<option value="MK">Macedonia, the Former Yugoslav Republic of</option>
                  	<option value="MG">Madagascar</option>
                  	<option value="MW">Malawi</option>
                  	<option value="MY">Malaysia</option>
                  	<option value="MV">Maldives</option>
                  	<option value="ML">Mali</option>
                  	<option value="MT">Malta</option>
                  	<option value="MH">Marshall Islands</option>
                  	<option value="MQ">Martinique</option>
                  	<option value="MR">Mauritania</option>
                  	<option value="MU">Mauritius</option>
                  	<option value="YT">Mayotte</option>
                  	<option value="MX">Mexico</option>
                  	<option value="FM">Micronesia, Federated States of</option>
                  	<option value="MD">Moldova, Republic of</option>
                  	<option value="MC">Monaco</option>
                  	<option value="MN">Mongolia</option>
                  	<option value="ME">Montenegro</option>
                  	<option value="MS">Montserrat</option>
                  	<option value="MA">Morocco</option>
                  	<option value="MZ">Mozambique</option>
                  	<option value="MM">Myanmar</option>
                  	<option value="NA">Namibia</option>
                  	<option value="NR">Nauru</option>
                  	<option value="NP">Nepal</option>
                  	<option value="NL">Netherlands</option>
                  	<option value="AN">Netherlands Antilles</option>
                  	<option value="NC">New Caledonia</option>
                  	<option value="NZ">New Zealand</option>
                  	<option value="NI">Nicaragua</option>
                  	<option value="NE">Niger</option>
                  	<option value="NG">Nigeria</option>
                  	<option value="NU">Niue</option>
                  	<option value="NF">Norfolk Island</option>
                  	<option value="MP">Northern Mariana Islands</option>
                  	<option value="NO">Norway</option>
                  	<option value="OM">Oman</option>
                  	<option value="PK">Pakistan</option>
                  	<option value="PW">Palau</option>
                  	<option value="PS">Palestinian Territory, Occupied</option>
                  	<option value="PA">Panama</option>
                  	<option value="PG">Papua New Guinea</option>
                  	<option value="PY">Paraguay</option>
                  	<option value="PE">Peru</option>
                  	<option value="PH">Philippines</option>
                  	<option value="PN">Pitcairn</option>
                  	<option value="PL">Poland</option>
                  	<option value="PT">Portugal</option>
                  	<option value="PR">Puerto Rico</option>
                  	<option value="QA">Qatar</option>
                  	<option value="RE">Reunion</option>
                  	<option value="RO">Romania</option>
                  	<option value="RU">Russian Federation</option>
                  	<option value="RW">Rwanda</option>
                  	<option value="BL">Saint Barthélemy</option>
                  	<option value="SH">Saint Helena</option>
                  	<option value="KN">Saint Kitts and Nevis</option>
                  	<option value="LC">Saint Lucia</option>
                  	<option value="MF">Saint Martin (French part)</option>
                  	<option value="PM">Saint Pierre and Miquelon</option>
                  	<option value="VC">Saint Vincent and the Grenadines</option>
                  	<option value="WS">Samoa</option>
                  	<option value="SM">San Marino</option>
                  	<option value="ST">Sao Tome and Principe</option>
                  	<option value="SA">Saudi Arabia</option>
                  	<option value="SN">Senegal</option>
                  	<option value="RS">Serbia</option>
                  	<option value="SC">Seychelles</option>
                  	<option value="SL">Sierra Leone</option>
                  	<option value="SG">Singapore</option>
                  	<option value="SK">Slovakia</option>
                  	<option value="SI">Slovenia</option>
                  	<option value="SB">Solomon Islands</option>
                  	<option value="SO">Somalia</option>
                  	<option value="ZA">South Africa</option>
                  	<option value="GS">South Georgia and the South Sandwich Islands</option>
                  	<option value="ES">Spain</option>
                  	<option value="LK">Sri Lanka</option>
                  	<option value="SD">Sudan</option>
                  	<option value="SR">Suriname</option>
                  	<option value="SJ">Svalbard and Jan Mayen</option>
                  	<option value="SZ">Swaziland</option>
                  	<option value="SE">Sweden</option>
                  	<option value="CH">Switzerland</option>
                  	<option value="SY">Syrian Arab Republic</option>
                  	<option value="TW">Taiwan, Province of China</option>
                  	<option value="TJ">Tajikistan</option>
                  	<option value="TZ">Tanzania, United Republic of</option>
                  	<option value="TH">Thailand</option>
                  	<option value="TL">Timor Leste</option>
                  	<option value="TG">Togo</option>
                  	<option value="TK">Tokelau</option>
                  	<option value="TO">Tonga</option>
                  	<option value="TT">Trinidad and Tobago</option>
                  	<option value="TN">Tunisia</option>
                  	<option value="TR">Turkey</option>
                  	<option value="TM">Turkmenistan</option>
                  	<option value="TC">Turks and Caicos Islands</option>
                  	<option value="TV">Tuvalu</option>
                  	<option value="UG">Uganda</option>
                  	<option value="UA">Ukraine</option>
                  	<option value="AE">United Arab Emirates</option>
                  	<option value="GB">United Kingdom</option>
                  	<option value="US" selected="selected">United States</option>
                  	<option value="UM">United States Minor Outlying Islands</option>
                  	<option value="UY">Uruguay</option>
                  	<option value="UZ">Uzbekistan</option>
                  	<option value="VU">Vanuatu</option>
                  	<option value="VE">Venezuela</option>
                  	<option value="VN">Viet Nam</option>
                  	<option value="VG">Virgin Islands, British</option>
                  	<option value="VI">Virgin Islands, U.S.</option>
                  	<option value="WF">Wallis and Futuna</option>
                  	<option value="EH">Western Sahara</option>
                  	<option value="YE">Yemen</option>
                  	<option value="ZM">Zambia</option>
                  	<option value="ZW">Zimbabwe</option>
                  </select>
                </div>
                <input type="submit" value="Update" class="updateItem btn" rel="billingInfo" />
              </form>
							<div class="cancelUpdate">
								<a class="toggleit" rel="billingInfoBlock">Cancel</a>
							</div>
						</div>
					</div>
					<div class="itemContainer" name="invoiceBlock">
						<div class="roItemContainer">
							<div class="roItemNoEdit" id="invoices">
								
							</div>
						</div>
					</div>
				</div>
			</div>
	
	
	<!--?php endwhile; ?-->

<!--?php get_footer(); ?-->
