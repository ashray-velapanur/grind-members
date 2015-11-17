<?php
/**
 * Template Name: New Member Registration
 */
//include_once APPPATH . 'libraries/enumerations.php';
global $current_user; $wp_roles; $user_ID; $user_identity; get_currentuserinfo();


/*
// If user has admin_init in their meta, that means that the admin created
their account. They should be skipping this step (as it is filled out in the 
admin member registration screen
*/
if (get_user_meta($user_ID, 'admin_init',true)){
	error_log("redirecting admin created user to member success:".$wp_user,0);
	delete_user_meta($user_ID,'registerHash');
	delete_user_meta($user_ID,'admin_init');
	delete_user_meta($user_ID,'registerStep');
	
	$user_id_role = new WP_User($user_ID);
	$user_id_role->set_role('subscriber');
		
	wp_clear_auth_cookie();
	wp_set_auth_cookie($user_ID);
	$wp_user = wp_set_current_user($user_ID, $user_email);
	
	$_SESSION['membershipsuccess']=true;
	wp_redirect(site_url('/your-account'));
	exit;
	
} elseif(get_user_meta($user_ID, 'registerStep',true)==1) {
	wp_redirect(site_url('/new-member-set-password'));
	exit;
}
	// set a data point so user skips password setup if they click the setup link again
	// this functionality currently disabled in grind_login.php
	update_user_meta($user_ID,'registerStep',2,true);
	
// If user doesn't have a registerHash metadata field in the database, then
// they already already went through this process. Redirect to account screen.

if (!get_user_meta($user_ID, 'registerHash', true)) {
	wp_redirect(site_url('/your-account')) ;
	exit;
}



$monthlyPlan = GetMonthlyPlan();
$dailyRate = GetDailyRate();
$newregistration=true;
include("header.php"); 


?>



		<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
		
				<h1><?php the_title(); ?></h1>
				<p id="instruction"><?php the_content(); ?></p>
				<hr class="pagehead" />
				
				<!-- New member form-->
				<div id="newMemberWrapper">
					<form id="newMember" class="formContainer" method="post" action="<?=site_url('ci/members/profile/register')?>">
						<input type="hidden" id="wp_users_id" name="wp_users_id" value="<?php echo $user_ID; ?>"/>
						
					
						<div id="inputFieldBlock" class="clearfix">
							<div id="creditBlock">
								<h3>Billing Details</h3>
								<div id="errorMessage"></div>
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
                    <input id="credit_card_number" maxlength="20" name="credit_card[number]" type="text" />
                  </div>
                  <div class="form-col">
                    <label for="credit_card_verification_value">Sec. Code</label>
                    <input class="required" id="credit_card_verification_value" maxlength="4" name="credit_card[verification_value]" size="4" type="text" />
                  </div>
                </div>
                <div class="form-row expiration-date clearfix">
                  <label for="credit_card_month">Expiration Date</label>
                  <select id="credit_card_month" name="credit_card[month]">
                	<?php
                	  $months = array("January", "February", "March", "April", "May", "June", "July",
                		"August", "September", "October", "November", "December");
                	  for ($month = 1; $month <= 12; $month++) {
                		  print "<option value=\"$month\">" . $months[$month - 1] . "</option>\n";
                	  }
                	?>
                  </select>
                  <select id="credit_card_year" name="credit_card[year]">
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
                    <input id="billing_info_zip" maxlength="20" name="billing_info[zip]" type="text" />
                  </div>
                </div>
                <div class="form-row">
                  <label for="billing_info_country">Country</label>
                  <select id="billing_info_country" name="billing_info[country]">
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
                  	<option value="AX">�land Islands</option>
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
                  	<option value="BL">Saint Barth�lemy</option>
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
                  	<option value="�">Turkmenistan</option>
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
              </div>
            </div>
						<div>
						
						 <p class="terms"><input type="checkbox" id="terms" name="terms" class="required"><label for="terms" class="label-checkbox" tabindex="90"> I Agree to the <a href="#" class="pop-terms">Terms &amp; Conditions</a></label></p>
						<br />
						  <div class="loader" style="display:none"></div>
							<input class="submit btn" id="submit-billing" name="commit" type="submit" value="Become a Member" />
						</div>
					<input type="hidden" id="user_id" name="user_id" value="<?=$user_ID?>"/>
				    <input type="hidden" id="id_type" name="id_type" value="<?=UserIdType::WORDPRESSID?>"/>
	
					</form><!--end form-->
				</div><!--end newMemberWrapper-->
		
		<?php endwhile; ?>
<?php get_footer(); ?>
<div class="overlay">
<div class="overlay-bg">
<?php // TERMS AND CONDITIONS POP UP
$page_id = "399"; // 123 should be replaced with a specific Page's id from your site, which you can find by mousing over the link to edit that Page on the Manage Pages admin page. The id will be embedded in the query string of the URL, e.g. page.php?action=edit&post=123.
$page_data = get_page( $page_id ); // You must pass in a variable to the get_page function. If you pass in a value (e.g. get_page ( 123 ); ), WordPress will generate an error. 

$content = apply_filters('the_content', $page_data->post_content); // Get Content and retain Wordpress filters such as paragraph tags. Origin from: http://wordpress.org/support/topic/get_pagepost-and-no-paragraphs-problem
echo $content;
?>
</div>
</div>
<script type="text/javascript">
  $(function(){
    function resetForm() {
      $(".loader").hide();
      $("#submit-billing").removeAttr("disabled")
      $("#inputFieldBlock, #submit-billing").fadeTo('slow',1);
    }
    
    $(".pop-terms").click(function(e) {
      e.preventDefault();
      $(".overlay, .overlay-bg, #pop-terms").fadeIn();
    });
    
    $("#pop-terms .close a, .overlay-bg").click(function(e) {
      e.preventDefault();
      $(".overlay, .overlay-bg, #pop-terms").fadeOut();
    });
    
    $.validator.addMethod('CCExp', function(value, element, params) {
      var minMonth = new Date().getMonth() + 1;
      var minYear = new Date().getFullYear();
      var month = parseInt($(params.month).val(), 10);
      var year = parseInt($(params.year).val(), 10);
      return (year > minYear || (year === minYear && month >= minMonth));
    }, 'Your Credit Card Expiration date is invalid.');
    $("#newMember").validate({
    	submitHandler: function(form) {
    		$("#submit-billing").attr("disabled", "true"); 
    		var userID = $("#user_id").val();
   			var idType = $("#id_type").val();
    		var formAction = $("#newMember").attr("action")+"/" + userID + "/" + idType     
        	var str = $("#newMember").serialize();
        $.ajax({
            type: "POST",
            url: formAction,
            data: str,
            dataType: "json",
            beforeSend: function() {
              $("#inputFieldBlock, #submit-billing").fadeTo('slow', 0.5);
              $(".loader").show();
            },
            success: function(response) {
              if (response['error']=='ALREADY_HAS_SUB') {
                window.location = "/your-account";
              } else if (response['error']=='INVALID_CC') {
                $("#errorMessage").text(response['message']);
                resetForm();
              } else if (response['error']=='GATEWAY_ERROR') {
                $("#errorMessage").text("We were unable to create your membership. Please try again." );
                resetForm();
              } else if (response['error']=='GRIND_EXCEPTION') {
                $("#errorMessage").text("We were unable to create your membership. Please try again.");
                resetForm();
              } else if (response['success'] == 1) {
                window.location = "<?=site_url('/your-account')?>";
              } else {
                $("#errorMessage").text("We were unable to create your membership. Please try again.");
                resetForm();
              }
            },
            error: function(jqXHR, textStatus, errorThrown) {
              alert("We were unable to create your membership. Please try again.");
              console.log(errorThrown);
              resetForm();
            }
        });
        return false;            
    	}
    });
    $("#credit_card_number").rules("add", { required: true, creditcard: true, 
      messages: { creditcard: "Enter a valid credit card #." }
    });
    $("#credit_card_verification_value").rules("add", { required: true, number: true, minlength: 3 });
    $("#billing_info_zip").rules("add", { required: true, number: true, minlength: 5 });
    $("#credit_card_year").rules("add", { CCExp: { month: '#credit_card_month', year: '#credit_card_year' } });
  });
</script>