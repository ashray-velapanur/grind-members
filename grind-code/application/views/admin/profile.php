<form action="<?=ROOTMEMBERPATH?>grind-code/index.php/admin/usermanagement/updateprofile" method="post" id="register" class="clearfix">
  <input type="hidden" name="id" id="id" value="<?=(isset($user) ? $user->id : "")?>" />
  
  <fieldset>
    <h3>Membership Info</h3>
    <ul>
      <li>
        <label for="first_name">First Name</label>
        <input type="text" id="first_name" class="required" name="first_name" value="" />
      </li>
      <li>
        <label for="last_name">Last Name</label>
        <input type="text" id="last_name" class="required" name="last_name" value="" />
      </li>
      <li>
        <label for="first_name">Company</label>
        <input type="text" id="company" name="company" value="" />
      </li>
      <li>
        <label for="rfid">RFID</label>
        <input type="text" id="rfid" name="rfid" value="" />
      </li>
      <li>
        <label for="website">Website</label>
        <input type="text" id="website" class="url" name="website" value="" />
      </li>
      <li>
        <label for="twitter">Twitter</label>
        <input type="text" id="twitter" name="twitter" value="" />
      </li>
      <li>
        <label for="twitter">Behance</label>
        <input type="text" id="behance" name="behance" value="" />
      </li>
      <li>
        <label for="primary_email">Email</label>
        <input type="text" id="primary_email" class="required email" name="primary_email" value="" />
      </li>
      <li>
        <label for="primary_phone">Phone</label>
        <input type="text" id="primary_phone" name="primary_phone" value="" />
      </li>
      <li>
        <label for="company_description">Company Description</label>
        <textarea id="company_description" name="company_description"></textarea>
      </li>
      <li>  
        <label for="membership_plan_code">Membership Type</label>
        <select id="membership_plan_code" name="membership_plan_code" title="Membership Type">
            <option value="daily">Daily</option>
        <?
        
        
        
        $plans = RecurlyPlan::getPlans();
        foreach ($plans as $plan) {        
			if(isset($accesspricing) && $accesspricing->allow_monthly_memberships < 1 ){  ?>
				<?php if($plan->plan_code != 'monthly'){ ?>
						          <option value="<?=$plan->plan_code?>"><?=$plan->name?></option>
				<?php } ?>
		<?	} else { ?>
		          <option value="<?=$plan->plan_code?>"><?=$plan->name?></option>
			<? }  ?>
        <? } ?>
        </select>
        
      </li>
  
    </ul>
  </fieldset>
  
  <fieldset>
    <h3>Billing Details</h3>
    <ul>
      <li>
        <label for="name_on_card">Billing First Name</label>
        <input type="text" id="billing_first_name" class="required" name="billing_first_name" value="" />
      </li>
      <li>
        <label for="name_on_card">Billing Last Name</label>
        <input type="text" id="billing_last_name" class="required" name="billing_last_name" value="" />
      </li>
      <li>
        <label for="card_number">Card Number</label>
        <input type="text" id="card_number" class="required creditcard" name="card_number" value="" />
      </li>
      <li>
        <label for="card_exp_month">Expiration Date</label>
        <select id="card_exp_month" name="card_exp_month">
          <option value="01">01 - Jan</option>
          <option value="02">02 - Feb</option>
          <option value="03">03 - Mar</option>
          <option value="04">04 - Apr</option>
          <option value="05">05 - May</option>
          <option value="06">06 - Jun</option>
          <option value="07">07 - Jul</option>
          <option value="08">08 - Aug</option>
          <option value="09">09 - Sep</option>
          <option value="10">10 - Oct</option>
          <option value="11">11 - Nov</option>
          <option value="12">12 - Dec</option>
        </select>
        <select id="card_exp_year" name="card_exp_year" title="Exp Year"> 
        <?
        $currentYear = date("Y");
        for($i=$currentYear; $i < $currentYear+10; ++$i) {
            echo "<option value=\"$i\">$i</option>\n";
        }
        ?>
        </select>
      </li>
      <li>
        <label for="security_code">Security. Code</label>
        <input type="text" id="security_code" name="security_code" value="" />
      </li>
      <li>
        <label for="billing_address_1">Address 1</label>
        <input type="text" id="billing_address_1" class="required" name="billing_address_1" value="" />
      </li>
      <li>
        <label for="billing_address_2">Address 2</label>
        <input type="text" id="billing_address_2" name="billing_address_2" value="" />
      </li>
      <li>
        <label for="billing_city">City</label>
        <input type="text" id="billing_city" class="required" name="billing_city" value="" />
      </li>
      <li>
        <label for="billing_state">State</label>
        <select id="billing_state" name="billing_state" title="State">
            <option value="" selected="selected">Select a State</option> 
            <option value="AL">Alabama</option> 
            <option value="AK">Alaska</option> 
            <option value="AZ">Arizona</option> 
            <option value="AR">Arkansas</option> 
            <option value="CA">California</option> 
            <option value="CO">Colorado</option> 
            <option value="CT">Connecticut</option> 
            <option value="DE">Delaware</option> 
            <option value="DC">District Of Columbia</option> 
            <option value="FL">Florida</option> 
            <option value="GA">Georgia</option> 
            <option value="HI">Hawaii</option> 
            <option value="ID">Idaho</option> 
            <option value="IL">Illinois</option> 
            <option value="IN">Indiana</option> 
            <option value="IA">Iowa</option> 
            <option value="KS">Kansas</option> 
            <option value="KY">Kentucky</option> 
            <option value="LA">Louisiana</option> 
            <option value="ME">Maine</option> 
            <option value="MD">Maryland</option> 
            <option value="MA">Massachusetts</option> 
            <option value="MI">Michigan</option> 
            <option value="MN">Minnesota</option> 
            <option value="MS">Mississippi</option> 
            <option value="MO">Missouri</option> 
            <option value="MT">Montana</option> 
            <option value="NE">Nebraska</option> 
            <option value="NV">Nevada</option> 
            <option value="NH">New Hampshire</option> 
            <option value="NJ">New Jersey</option> 
            <option value="NM">New Mexico</option> 
            <option value="NY">New York</option> 
            <option value="NC">North Carolina</option> 
            <option value="ND">North Dakota</option> 
            <option value="OH">Ohio</option> 
            <option value="OK">Oklahoma</option> 
            <option value="OR">Oregon</option> 
            <option value="PA">Pennsylvania</option> 
            <option value="RI">Rhode Island</option> 
            <option value="SC">South Carolina</option> 
            <option value="SD">South Dakota</option> 
            <option value="TN">Tennessee</option> 
            <option value="TX">Texas</option> 
            <option value="UT">Utah</option> 
            <option value="VT">Vermont</option> 
            <option value="VA">Virginia</option> 
            <option value="WA">Washington</option> 
            <option value="WV">West Virginia</option> 
            <option value="WI">Wisconsin</option> 
            <option value="WY">Wyoming</option>
        </select>        

      </li>
      <li>
        <label for="billing_zip_code">Zip Code</label>
        <input type="text" id="billing_zip_code" class="required" name="billing_zip_code" value="" />
      </li>
      <li>
        <label for="billing_info_country">Country</label><br />
       <?=form_countries( "billing_info_country",'US',array("name"=>"billing_info[country]","id"=>"billing_country"));?>
        
      </li>
    </ul>
  </fieldset>
  
  <div class="clearfix" style="clear:both">
    <br><br>
    <input type="submit" value="Submit" class="btn" /> &nbsp;&nbsp; <div class="loader" style="display: none"></div>
  </div>
  
</form>

<script type="text/javascript">
    $(function() {
		
        $("#register").validate({
	      submitHandler: function(form){
		    $('.loader').show();
		    $('#register').fadeTo('slow',.5);
		    form.submit();
	      }
        });
 	
		$("#register #primary_email").rules("add", {
		  remote: '<?=ROOTMEMBERPATH?>grind-code/index.php/admin/utility/uniqueEmail',
		  messages: {
	        remote: jQuery.format("This email is already taken")
		  }
		});
    });
</script>