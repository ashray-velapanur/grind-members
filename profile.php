<?php
require( dirname(__FILE__) . '/_siteconfig.php' );
?>
<?php if(isset($user)) {
	$formId="profile";
} else {
	$formId="register";
}

 ?>
<form action="<?=ROOTMEMBERPATH?>grind-code/index.php/admin/usermanagement/updateprofile" method="post" id="<?= $formId ?>" class="clearfix" enctype="multipart/form-data">
  <input type="hidden" name="id" id="id" value="<?=(isset($user) ? $user->id : "")?>" />

  <?php if(isset($user)): ?>
  <ul id="tabs" class="clearfix">
    <li class="current"><?=anchor("/admin/usermanagement/user/" . $user->id, "Edit")?></li>
    <li><?=anchor("/admin/usermanagement/usercheckins/" . $user->id, "Check-Ins")?></li>
    <li><?=anchor("/admin/usermanagement/usercharges/" . $user->id, "Charges and Credits")?></li>
  </ul>
  <?php endif ?>
  
  <fieldset>
    <h3>Membership Info</h3>
    <ul>
      <li>
        <label for="first_name">First Name</label>
        <input type="text" id="first_name" class="required" name="first_name" value="<?=(isset($user) ? $user->first_name : "")?>" />
      </li>
      <li>
        <label for="last_name">Last Name</label>
        <input type="text" id="last_name" class="required" name="last_name" value="<?=(isset($user) ? $user->last_name : "")?>" />
      </li>
      <li>
        <label for="company">Company</label>
        <input type="text" id="company" name="company" value="<?=(isset($user) ? $user->company_name : "")?>" />
      </li>
      <li>
        <label for="companylogo">Company</label>
        Select logo image for company: <input type="file" name="companylogo" id="companylogo">
      </li>
      <li>
        <label for="designation">Designation in Company</label>
        <input type="text" id="designation" name="designation" value="<?=(isset($user) ? $user->designation : "")?>" />
      </li>
      <li>
        <label for="rfid">RFID</label>
        <input type="text" id="rfid" name="rfid" value="<?=(isset($user) ? $user->rfid : "")?>" />
      </li>
      <li>
        <label for="website">Website</label>
        <input type="text" id="website" class="url" name="website" value="<?=(isset($user) ? $user->website : "")?>" />
      </li>
      <li>
        <label for="twitter">Twitter</label>
        <input type="text" id="twitter" name="twitter" value="<?=(isset($user) ? $user->twitter : "")?>" />
      </li>
      <li>
        <label for="twitter">Behance</label>
        <input type="text" id="behance" name="behance" value="<?=(isset($user) ? $user->behance : "")?>" />
      </li>
      <li>
        <label for="primary_email">Email</label>
        <input type="text" id="primary_email" class="required email" name="primary_email" value="<?=(isset($user) ? $user->email : "")?>" />
      </li>
      <li>
        <label for="primary_phone">Phone</label>
        <input type="text" id="primary_phone" name="primary_phone" value="<?=(isset($user) ? $user->phone : "")?>" />
      </li>
      <li>
        <label for="company_description">Company Description</label>
        <textarea id="company_description" name="company_description"><?=(isset($user) ? $user->company_description : "")?></textarea>
      </li>
      <li>
        <label for="membership_plan_code">Membership Type</label>
        <select id="membership_plan_code" name="membership_plan_code" title="Membership Type">
            <option value="">Daily</option>
            <!-- For now commented getting RecurlyPlans -->
            <?
            //$plans = RecurlyPlan::getPlans();
            //foreach ($plans as $plan) {
            ?>
            <!--<option value="<?//=$plan->plan_code?>" <?//=(isset($user) ? ($user->membership_plan_code == $plan->plan_code ? "selected" : "") : "")?>><?//=$plan->name?></option>-->
            <? //} ?>
        </select>
        <?
        if (isset($user)) {
          if ($user->membership_canceled) {
              echo "<br /><br /><span class=\"important\">Membership canceled.  Expires and will switch down to Daily on " . $user->membership_expires_on . ".</span>";
          }
        }
        ?>
      </li>
      <li>
        <label for="role">Role</label>
        <select name="role" id="role">
          <option value="subscriber">Subscriber</option>
          <option value="administrator">Administrator</option>
        </select>
      </li>
      <li style="border:0">
        <br><br><input type="submit" value="Submit" class="btn" />
      </li>
  <?php if(isset($user)): ?>
      <li>
            <a id="pwdchange" href="#">Change Password</a>
      </li>
  <?php endif ?>
    </ul>
  </fieldset>
  
  <fieldset>
    <h3>Billing Details</h3>
    <ul>
      <li>
        <label for="name_on_card">Billing First Name</label>
        <input type="text" id="billing_first_name" class="required" name="billing_first_name" value="<?=(isset($user) ? $user->billingInfo->first_name : "")?>" />
      </li>
      <li>
        <label for="name_on_card">Billing Last Name</label>
        <input type="text" id="billing_last_name" class="required" name="billing_last_name" value="<?=(isset($user) ? $user->billingInfo->last_name : "")?>" />
      </li>
      <li>
        <label for="card_number">Card Number</label>
        <input type="text" id="card_number" class="required creditcard" name="card_number" value="<?=(isset($user) ? ($user->billingInfo->credit_card->last_four != "" ? "*****" . $user->billingInfo->credit_card->last_four : "") : "")?>" />
      </li>
      <li>
        <label for="card_exp_month">Expiration Date</label>
        <select id="card_exp_month" name="card_exp_month">
          <option value="01" <?=(isset($user) ? ($user->billingInfo->credit_card->month == "01" ? "selected" : "") : "")?>>01 - Jan</option>
          <option value="02" <?=(isset($user) ? ($user->billingInfo->credit_card->month == "02" ? "selected" : "") : "")?>>02 - Feb</option>
          <option value="03" <?=(isset($user) ? ($user->billingInfo->credit_card->month == "03" ? "selected" : "") : "")?>>03 - Mar</option>
          <option value="04" <?=(isset($user) ? ($user->billingInfo->credit_card->month == "04" ? "selected" : "") : "")?>>04 - Apr</option>
          <option value="05" <?=(isset($user) ? ($user->billingInfo->credit_card->month == "05" ? "selected" : "") : "")?>>05 - May</option>
          <option value="06" <?=(isset($user) ? ($user->billingInfo->credit_card->month == "06" ? "selected" : "") : "")?>>06 - Jun</option>
          <option value="07" <?=(isset($user) ? ($user->billingInfo->credit_card->month == "07" ? "selected" : "") : "")?>>07 - Jul</option>
          <option value="08" <?=(isset($user) ? ($user->billingInfo->credit_card->month == "08" ? "selected" : "") : "")?>>08 - Aug</option>
          <option value="09" <?=(isset($user) ? ($user->billingInfo->credit_card->month == "09" ? "selected" : "") : "")?>>09 - Sep</option>
          <option value="10" <?=(isset($user) ? ($user->billingInfo->credit_card->month == "10" ? "selected" : "") : "")?>>10 - Oct</option>
          <option value="11" <?=(isset($user) ? ($user->billingInfo->credit_card->month == "11" ? "selected" : "") : "")?>>11 - Nov</option>
          <option value="12" <?=(isset($user) ? ($user->billingInfo->credit_card->month == "12" ? "selected" : "") : "")?>>12 - Dec</option>
        </select>
        <select id="card_exp_year" name="card_exp_year" title="Exp Year"> 
        <?
        $currentYear = date("Y");
        for($i=$currentYear; $i < $currentYear+10; ++$i) {
            echo "<option value=\"$i\" " . (isset($user) ? ($user->billingInfo->credit_card->year == $i ? "selected" : "") : "") . ">$i</option>\n";
        }
        ?>
        </select>
      </li>
      <li>
        <label for="security_code">Sec. Code</label>
        <input type="text" id="security_code" name="security_code" value="<?=(isset($user) ? $user->billingInfo->credit_card->verification_value : "")?>" />
      </li>
      <li>
        <label for="billing_address_1">Address 1</label>
        <input type="text" id="billing_address_1" class="required" name="billing_address_1" value="<?=(isset($user) ? $user->billingInfo->address1 : "")?>" />
      </li>
      <li>
        <label for="billing_address_2">Address 2</label>
        <input type="text" id="billing_address_2" name="billing_address_2" value="<?=(isset($user) ? $user->billingInfo->address2 : "")?>" />
      </li>
      <li>
        <label for="billing_city">City</label>
        <input type="text" id="billing_city" class="required" name="billing_city" value="<?=(isset($user) ? $user->billingInfo->city : "")?>" />
      </li>
      <li>
        <label for="billing_state">State</label>
        <select id="billing_state" name="billing_state" title="State">
            <option value="" selected="selected">Select a State</option> 
            <option value="AL" <?=(isset($user) ? ($user->billingInfo->state == "AL" ? "selected" : "") : "")?>>Alabama</option> 
            <option value="AK" <?=(isset($user) ? ($user->billingInfo->state == "AK" ? "selected" : "") : "")?>>Alaska</option> 
            <option value="AZ" <?=(isset($user) ? ($user->billingInfo->state == "AZ" ? "selected" : "") : "")?>>Arizona</option> 
            <option value="AR" <?=(isset($user) ? ($user->billingInfo->state == "AR" ? "selected" : "") : "")?>>Arkansas</option> 
            <option value="CA" <?=(isset($user) ? ($user->billingInfo->state == "CA" ? "selected" : "") : "")?>>California</option> 
            <option value="CO" <?=(isset($user) ? ($user->billingInfo->state == "CO" ? "selected" : "") : "")?>>Colorado</option> 
            <option value="CT" <?=(isset($user) ? ($user->billingInfo->state == "CT" ? "selected" : "") : "")?>>Connecticut</option> 
            <option value="DE" <?=(isset($user) ? ($user->billingInfo->state == "DE" ? "selected" : "") : "")?>>Delaware</option> 
            <option value="DC" <?=(isset($user) ? ($user->billingInfo->state == "DC" ? "selected" : "") : "")?>>District Of Columbia</option> 
            <option value="FL" <?=(isset($user) ? ($user->billingInfo->state == "FL" ? "selected" : "") : "")?>>Florida</option> 
            <option value="GA" <?=(isset($user) ? ($user->billingInfo->state == "GA" ? "selected" : "") : "")?>>Georgia</option> 
            <option value="HI" <?=(isset($user) ? ($user->billingInfo->state == "HI" ? "selected" : "") : "")?>>Hawaii</option> 
            <option value="ID" <?=(isset($user) ? ($user->billingInfo->state == "ID" ? "selected" : "") : "")?>>Idaho</option> 
            <option value="IL" <?=(isset($user) ? ($user->billingInfo->state == "IL" ? "selected" : "") : "")?>>Illinois</option> 
            <option value="IN" <?=(isset($user) ? ($user->billingInfo->state == "IN" ? "selected" : "") : "")?>>Indiana</option> 
            <option value="IA" <?=(isset($user) ? ($user->billingInfo->state == "IA" ? "selected" : "") : "")?>>Iowa</option> 
            <option value="KS" <?=(isset($user) ? ($user->billingInfo->state == "KS" ? "selected" : "") : "")?>>Kansas</option> 
            <option value="KY" <?=(isset($user) ? ($user->billingInfo->state == "KY" ? "selected" : "") : "")?>>Kentucky</option> 
            <option value="LA" <?=(isset($user) ? ($user->billingInfo->state == "LA" ? "selected" : "") : "")?>>Louisiana</option> 
            <option value="ME" <?=(isset($user) ? ($user->billingInfo->state == "ME" ? "selected" : "") : "")?>>Maine</option> 
            <option value="MD" <?=(isset($user) ? ($user->billingInfo->state == "MD" ? "selected" : "") : "")?>>Maryland</option> 
            <option value="MA" <?=(isset($user) ? ($user->billingInfo->state == "MA" ? "selected" : "") : "")?>>Massachusetts</option> 
            <option value="MI" <?=(isset($user) ? ($user->billingInfo->state == "MI" ? "selected" : "") : "")?>>Michigan</option> 
            <option value="MN" <?=(isset($user) ? ($user->billingInfo->state == "MN" ? "selected" : "") : "")?>>Minnesota</option> 
            <option value="MS" <?=(isset($user) ? ($user->billingInfo->state == "MS" ? "selected" : "") : "")?>>Mississippi</option> 
            <option value="MO" <?=(isset($user) ? ($user->billingInfo->state == "MO" ? "selected" : "") : "")?>>Missouri</option> 
            <option value="MT" <?=(isset($user) ? ($user->billingInfo->state == "MT" ? "selected" : "") : "")?>>Montana</option> 
            <option value="NE" <?=(isset($user) ? ($user->billingInfo->state == "NE" ? "selected" : "") : "")?>>Nebraska</option> 
            <option value="NV" <?=(isset($user) ? ($user->billingInfo->state == "NV" ? "selected" : "") : "")?>>Nevada</option> 
            <option value="NH" <?=(isset($user) ? ($user->billingInfo->state == "NH" ? "selected" : "") : "")?>>New Hampshire</option> 
            <option value="NJ" <?=(isset($user) ? ($user->billingInfo->state == "NJ" ? "selected" : "") : "")?>>New Jersey</option> 
            <option value="NM" <?=(isset($user) ? ($user->billingInfo->state == "NM" ? "selected" : "") : "")?>>New Mexico</option> 
            <option value="NY" <?=(isset($user) ? ($user->billingInfo->state == "NY" ? "selected" : "") : "")?>>New York</option> 
            <option value="NC" <?=(isset($user) ? ($user->billingInfo->state == "NC" ? "selected" : "") : "")?>>North Carolina</option> 
            <option value="ND" <?=(isset($user) ? ($user->billingInfo->state == "ND" ? "selected" : "") : "")?>>North Dakota</option> 
            <option value="OH" <?=(isset($user) ? ($user->billingInfo->state == "OH" ? "selected" : "") : "")?>>Ohio</option> 
            <option value="OK" <?=(isset($user) ? ($user->billingInfo->state == "OK" ? "selected" : "") : "")?>>Oklahoma</option> 
            <option value="OR" <?=(isset($user) ? ($user->billingInfo->state == "OR" ? "selected" : "") : "")?>>Oregon</option> 
            <option value="PA" <?=(isset($user) ? ($user->billingInfo->state == "PA" ? "selected" : "") : "")?>>Pennsylvania</option> 
            <option value="RI" <?=(isset($user) ? ($user->billingInfo->state == "RI" ? "selected" : "") : "")?>>Rhode Island</option> 
            <option value="SC" <?=(isset($user) ? ($user->billingInfo->state == "SC" ? "selected" : "") : "")?>>South Carolina</option> 
            <option value="SD" <?=(isset($user) ? ($user->billingInfo->state == "SD" ? "selected" : "") : "")?>>South Dakota</option> 
            <option value="TN" <?=(isset($user) ? ($user->billingInfo->state == "TN" ? "selected" : "") : "")?>>Tennessee</option> 
            <option value="TX" <?=(isset($user) ? ($user->billingInfo->state == "TX" ? "selected" : "") : "")?>>Texas</option> 
            <option value="UT" <?=(isset($user) ? ($user->billingInfo->state == "UT" ? "selected" : "") : "")?>>Utah</option> 
            <option value="VT" <?=(isset($user) ? ($user->billingInfo->state == "VT" ? "selected" : "") : "")?>>Vermont</option> 
            <option value="VA" <?=(isset($user) ? ($user->billingInfo->state == "VA" ? "selected" : "") : "")?>>Virginia</option> 
            <option value="WA" <?=(isset($user) ? ($user->billingInfo->state == "WA" ? "selected" : "") : "")?>>Washington</option> 
            <option value="WV" <?=(isset($user) ? ($user->billingInfo->state == "WV" ? "selected" : "") : "")?>>West Virginia</option> 
            <option value="WI" <?=(isset($user) ? ($user->billingInfo->state == "WI" ? "selected" : "") : "")?>>Wisconsin</option> 
            <option value="WY" <?=(isset($user) ? ($user->billingInfo->state == "WY" ? "selected" : "") : "")?>>Wyoming</option>
        </select>        

      </li>
      <li>
        <label for="billing_zip_code">Zip Code</label>
        <input type="text" id="billing_zip_code" class="required" name="billing_zip_code" value="<?=(isset($user) ? $user->billingInfo->zip : "")?>" />
      </li>
    </ul>
  </fieldset>
  
</form>

<script type="text/javascript">
    $(document).ready(function() {
        
        $("#pwdchange").click(function() {
            alert("coming soon");
            return false;
        });
        
    });
</script>