<?php
include_once APPPATH . 'libraries/constants.php';
include_once APPPATH . 'libraries/enumerations.php';

/**
 * View Template for Billing Info Form
 *
 * Requires abilling_info array to pre pop field data
 *
 * @joshcampbell
 * @viewtemplate
 */

 // SETUP prepopulated variables
 $first_name = isset($billing_info->first_name) ? $billing_info->first_name : ""; 
 $last_name = isset($billing_info->last_name) ? $billing_info->last_name : ""; 
 $address1 = isset($billing_info->address1) ? $billing_info->address1 : "";
 $address2 = isset($billing_info->address2) ? $billing_info->address2 : "";  
 $city = isset($billing_info->city) ? $billing_info->city : "";
 $state = isset($billing_info->state) ? $billing_info->state : "";  
 $zip = isset($billing_info->zip) ? $billing_info->zip : "";
 $country = isset($billing_info->country) ? $billing_info->country : "";  
 $date = getdate();
 $exp_year = isset($billing_info->credit_card->year) ? $billing_info->credit_card->year : ""; 
 $exp_month = isset($billing_info->credit_card->month) ? $billing_info->credit_card->month: ($date["mon"] - 1); 

?>

<div class="editItemContainer clearfix"> 	
      <form id="billingInfo-form" rel="billingInfoBlock" action="<?=site_url('/grind-code/billing/account/updateBilling/')?>">
        <div id="errorMessage"></div>
        <div class="form-row clearfix">
          <div class="form-col">
            <label for="first_name">First Name</label>
            <input class="required" id="first_name" maxlength="20" name="billing_info[first_name]" value="<?= $first_name ?>" type="text" />
          </div>
          <div class="form-col">
            <label for="last_name">Last Name</label>
            <input class="required" id="last_name" maxlength="20" name="billing_info[last_name]" value="<?= $last_name ?>" type="text" />
          </div>
        </div>
        <div class="form-row clearfix">
          <div class="form-col with-spacer">
            <label for="credit_card_number">Credit Card Number</label>
            <input class="required" id="credit_card_number" maxlength="20" name="credit_card[number]" value="" type="text" />
          </div>
          <div class="form-col">
            <label for="credit_card_verification_value">Security. Code</label>
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
          	  	  $selected_month = ($month == $exp_month) ? "selected = 'selected'" : "";
        		  print "<option value='".$month."' ".$selected_month.">" . $months[$month - 1] . "</option>\n";
        		  
        	  }
        	?>
          </select>
          <select id="credit_card_year" name="credit_card[year]" class="required">
          	<?php 
          	  
          	  $current_year = $date['year'];
          	  for ($year = $current_year; $year <= $current_year + 10; $year++) {
          	  	  $selected_year = ($year == $exp_year) ? "selected = 'selected'" : "";
          		  print "<option value='".$year."' ".$selected_year.">$year</option>\n";
          	  }
          	?>
          </select>
        </div>
        <div class="form-row">
          <label for="billing_info_address1">Address</label>
          <input class="required" id="billing_info_address1" maxlength="50" name="billing_info[address1]" value="<?= $address1 ?>" type="text" />
        </div>
        <div class="form-row">
          <label for="billing_info_address2">Address 2</label>
          <input id="billing_info_address2" maxlength="50" name="billing_info[address2]" value="<?= $address2 ?>" type="text" />
        </div>
        <div class="form-row clearfix">
          <div class="form-col">
            <label for="billing_info_city">City</label>
            <input class="required" id="billing_info_city" maxlength="50" name="billing_info[city]" value="<?= $city ?>" type="text" />
          </div>
          <div class="form-col">
            <label for="billing_info_state">State</label>
            <input class="required" id="billing_info_state" maxlength="50" name="billing_info[state]" value="<?= $state ?>" type="text" />
          </div>
          <div class="form-col">
            <label for="billing_info_zip">Zip</label>
            <input class="required" id="billing_info_zip" maxlength="20" name="billing_info[zip]" value="<?= $zip ?>" type="text" />
          </div>
        </div>
        <div class="form-row">
        	<label for="billing_info_country">Country</label>
			<? echo $countries_dropdown; ?>
        </div>
        <div class="row update">
        <input type="submit" value="Update" class="updateItem btn" rel="billingInfo" />
		</div>
        <div class="loader" style="display: none;"></div>
      </form>
	<div class="cancelUpdate">
		<a class="toggleit" rel="billingInfoBlock">Cancel</a>
	</div>
</div>
