<?php

  require_once '../recurly/recurly.php';
  require_once '../wp-blog-header.php';
  require_once '../wp-config.php';
  require_once '../wp-includes/pluggable.php';
  require_once '../wp-includes/registration.php';

  // Replace with your Recurly API user credentials
  define('RECURLY_API_USERNAME', 'api-test@magicmight-test.com');
  define('RECURLY_API_PASSWORD', 'c32fa911fddb4720b02edd04dd3b6635');
  define('RECURLY_SUBDOMAIN', 'magicmight-test');
  define('RECURLY_ENVIRONMENT', 'sandbox');  //or "production"

  //RecurlyClient::SetAuth('api-test@magicmight-test.com', 'c32fa911fddb4720b02edd04dd3b6635', 'magicmight-test', 'sandbox');
  RecurlyClient::SetAuth(RECURLY_API_USERNAME, RECURLY_API_PASSWORD, RECURLY_SUBDOMAIN, RECURLY_ENVIRONMENT);
  
  // Setting timezone for time() function.
  date_default_timezone_set('America/New_York');
  
  // Replace with the user's unique ID in your system
  $account_id = '14';

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	//Recurly Account
    $account = new RecurlyAccount($account_id);
    $account->username = $_POST['account']['username'];
    $account->first_name = $_POST['account']['first_name'];
    $account->last_name = $_POST['account']['last_name'];
    $account->email = $_POST['account']['email'];
    
	//Wordpress Account
	$newWordpressUser = array(
		'user_login' => $_POST['account']['username'],
		'user_pass' => $_POST['account']['password'],
		'user_nicename' =>  $_POST['account']['first_name'] . ' ' . $_POST['account']['last_name'],
		'nickname' => $_POST['account']['first_name'] . ' ' . $_POST['account']['last_name'],
		'first_name' => $_POST['account']['first_name'],
		'last_name' => $_POST['account']['last_name'],
		'display_name' => $_POST['account']['first_name'] . ' ' . $_POST['account']['last_name'],
		'user_registered' => date("Y-m-d H:i:s"),
		'user_email' => $_POST['account']['email']
	);

	//Recurly Subscription
    $subscription = new RecurlySubscription();
    $subscription->plan_code = $_POST['plan_type'];
    $subscription->account = $account;
    $subscription->billing_info = new RecurlyBillingInfo($subscription->account->account_code);
    $billing_info = $subscription->billing_info;
    $billing_info->first_name = $account->first_name;
    $billing_info->last_name = $account->last_name;
    $billing_info->address1 = $_POST['billing_info']['address1'];
    $billing_info->address2 = $_POST['billing_info']['address2'];
    $billing_info->city = $_POST['billing_info']['city'];
    $billing_info->state = $_POST['billing_info']['state'];
    $billing_info->country = $_POST['billing_info']['country'];
    $billing_info->zip = $_POST['billing_info']['zip'];
    $billing_info->credit_card->number = $_POST['credit_card']['number'];
    $billing_info->credit_card->year = intval($_POST['credit_card']['year']);
    $billing_info->credit_card->month = intval($_POST['credit_card']['month']);
    $billing_info->credit_card->verification_value = $_POST['credit_card']['verification_value'];
    $billing_info->ip_address = $_SERVER['REMOTE_ADDR'];
		
    try {
	    //Create Recurly Subscription
	    $account_info = $subscription->create(); 
	    
	    //Create Wordpress Subscription
	    wp_insert_user($newWordpressUser);

	    $success_message = 'Your subscription was created successfully.';
    }
    catch (RecurlyValidationException $e) {
      $error_message = $e->getMessage();
    }
    catch (RecurlyException $e) {
      $error_message = "An error occurred while communicating with our payment gateway. Please try again or " +
        "contact support.";
    }
  }

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <title>Grind - Register Member</title>
  <link type="text/css" rel="stylesheet" href="style.css"/>
  <script type="text/javascript" src="js/jquery-1.5.2.min.js"></script>
  <script type="text/javascript" src="js/jquery.validate.min.js"></script>
  <script>
	$(document).ready(function(){
		$("#register").validate({
		  rules: {
			account_password_confirm: {
			  equalTo: "#account_password"
			}
		  }
		});
	});
  </script>

</head>
<body>
  <div class="container">

    <h1>Register for Grind</h1>

	<?php
	  // Print success or error message
	  if (isset($success_message))
		print "    <div class=\"success\">$success_message</div>\n";
	  if (isset($error_message))
		print "    <div class=\"error\">$error_message</div>\n"; 
	?>

    <form method="post" name="register" id="register">
		<h2>Membership Type</h2>
		<input type="radio" name="plan_type" value="monthly-member" /> Monthly<br />
		<input type="radio" name="plan_type" value="multi" /> Daily
	  <h2>Personal Information</h2>
      <table class="editor">
        <!-- Account Details -->
        <tr>
          <td class="field"><label for="account_first_name">First Name</label></td>
          <td><input class="required" id="account_first_name" maxlength="50" name="account[first_name]" size="50" type="text"
            value="<?php if (isset($_POST['account'])) print htmlentities($_POST['account']['first_name']); ?>" /></td>
        </tr>
        <tr>
          <td class="field"><label for="account_last_name">Last Name</label></td>
          <td><input class="required" id="account_last_name" maxlength="50" name="account[last_name]" size="50" type="text"
            value="<?php if (isset($_POST['account'])) print htmlentities($_POST['account']['last_name']); ?>" /></td>
        </tr>
        <tr>
          <td class="field"><label for="account_email">Email Address</label></td>
          <td><input class="required" id="account_email" maxlength="50" name="account[email]" size="50" type="text"
            value="<?php if (isset($_POST['account'])) print htmlentities($_POST['account']['email']); ?>" /></td>
        </tr>
        <tr>
          <td class="field"><label for="account_username" class="optional">Username</label></td>
          <td><input class="required" id="account_username" maxlength="50" name="account[username]" size="50" type="text"
            value="<?php if (isset($_POST['account'])) print htmlentities($_POST['account']['username']); ?>" /></td>
        </tr>
		<tr>
          <td class="field"><label for="account_password" class="optional">Password</label></td>
          <td><input class="required" id="account_password" maxlength="50" name="account[password]" size="50" type="password"
            value="" /></td>
        </tr>
		<tr>
          <td class="field"><label for="account_password_confirm" class="optional">Confirm Password</label></td>
          <td><input class="required" id="account_password_confirm" maxlength="50" name="account_password_confirm" size="50" type="password"
            value="" /></td>
        </tr>
    </table>
	<h2>Company Information</h2>
	<table class="editor">
		<tr>
          <td class="field"><label for="account_company_name">Company Name</label></td>
          <td><input class="required" id="account_company_name" maxlength="50" name="account[company_name]" size="50" type="text"
            value="<?php if (isset($_POST['account'])) print htmlentities($_POST['account']['company_name']); ?>" /></td>
        </tr>
	</table>
	<h2>Billing Information</h2>
	<table class="editor">
        <tr>
          <td class="field"></td>
          <td><img alt="Visa" height="32" src="images/visa.png" width="32" />
            <img alt="MasterCard" height="32" src="images/mastercard.png" width="32" />
            <img alt="AmEx" height="32" src="images/amex.png" width="32" />
            <img alt="Discover" height="32" src="images/discover.png" width="32" />
          </td>
        </tr>
        <tr>
          <td class="field"><label for="credit_card_number">Credit Card Number</label></td>
          <td><input class="required" id="credit_card_number" maxlength="20" name="credit_card[number]" size="20" type="text"
            value="<?php if (isset($_POST['account'])) print htmlentities($_POST['credit_card']['number']); ?>" /></td>
        </tr>
        <tr>
          <td class="field"><label for="credit_card_verification_value">Verification Code</label></td>
          <td><input class="required" id="credit_card_verification_value" maxlength="4" name="credit_card[verification_value]" size="4" type="text"
            value="<?php if (isset($_POST['account'])) print htmlentities($_POST['credit_card']['verification_value']); ?>" />
            <img alt="CVV" src="images/cvv-glyph.png" />
          </td>
        </tr>
        <tr>
          <td class="field"><label for="credit_card_month">Exp. Date</label></td>
          <td>
            <select id="credit_card_month" name="credit_card[month]" class="required">
				<?php
				  $months = array("January", "February", "March", "April", "May", "June", "July",
				    "August", "September", "October", "November", "December");
    
				  for ($month = 1; $month <= 12; $month++) {
				    print "<option value=\"$month\"";
				    if (isset($_POST) && $_POST['credit_card']['month'] == $month)
				      print " selected=\"true\"";
				    print ">" . $months[$month - 1] . "</option>\n";
				  }
				?>
            </select> 
            <select id="credit_card_year" name="credit_card[year]" class="required">
				<?php 
				  $date = getdate();
				  $current_year = $date['year'];
				  for ($year = $current_year; $year <= $current_year + 10; $year++) {
				    print "<option value=\"$year\"";
				    if (isset($_POST) && $_POST['credit_card']['year'] == $year)
				      print " selected=\"true\"";
				    print ">$year</option>\n";
				  }
				?>
            </select>
          </td>
        </tr>
        <tr><td class="section">&nbsp;</td></tr>
        
        <!-- Billing Info Details -->
        <tr>
          <td class="field"><label for="billing_info_address1">Address 1</label></td>
          <td><input class="required" id="billing_info_address1" maxlength="50" name="billing_info[address1]" size="50" type="text"
            value="<?php if (isset($_POST['account'])) print htmlentities($_POST['billing_info']['address1']); ?>" /></td>
        </tr>
        <tr>
          <td class="field"><label for="billing_info_address2" class="optional">Address 2</label></td>
      	  <td><input class="text" id="billing_info_address2" maxlength="50" name="billing_info[address2]" size="50" type="text"
            value="<?php if (isset($_POST['account'])) print htmlentities($_POST['billing_info']['address2']); ?>" /></td>
        </tr>
        <tr>
          <td class="field"><label for="billing_info_city">City</label></td>
          <td><input class="required" id="billing_info_city" maxlength="50" name="billing_info[city]" size="50" type="text"
            value="<?php if (isset($_POST['account'])) print htmlentities($_POST['billing_info']['city']); ?>" /></td>
        </tr>
        <tr>
          <td class="field"><label for="billing_info_state">State/Province</label></td>
          <td><input class="required" id="billing_info_state" maxlength="50" name="billing_info[state]" size="50" type="text"
            value="<?php if (isset($_POST['account'])) print htmlentities($_POST['billing_info']['state']); ?>" /></td>
        </tr>
        <tr>
          <td class="field"><label for="billing_info_zip">Zip/Postal Code</label></td>
          <td><input class="required" id="billing_info_zip" maxlength="20" name="billing_info[zip]" size="20" type="text"
            value="<?php if (isset($_POST['account'])) print htmlentities($_POST['billing_info']['zip']); ?>" /></td>
        </tr>
        <tr>
          <td class="field"><label for="billing_info_country">Country</label></td>
          <td>
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
		  </td>
        </tr>
        <tr><td class="section">&nbsp;</td></tr>
        <tr>
          <td></td>
          <td>
            <input class="button" id="subscribe" name="commit" type="submit" value="Subscribe" />
          </td>
        </tr>
      </table>
    </form>
  </div>
</body>
</html>