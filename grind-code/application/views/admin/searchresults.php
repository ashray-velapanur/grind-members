<div id="members" class="clearfix">

  <h1>Search Results</h1>
  <p class="instruction">Select a member&rsquo;s name below to view and edit additional information for each member.</p>
  
  <aside id="sidebar">
    <section id="member-search" class="clearfix">
      <?
        echo form_open("admin/usermanagement/search");
        echo form_input(array("id" => "q", "type" => "search", "name" => "q", "value" => "Search for a Member"));
        echo form_close();
      ?>
      <!--<p id="register-new"><b><?=g_anchor("/admin/usermanagement/user", "Register a New Member") ?></b></p>-->
      <!--<p id="invite-new"><b><?=g_anchor("/admin/usermanagement/invite", "Send a Membership Invitation") ?></b></p>-->
    </section>
  </aside>
  
  <section id="member-list" class="module">
  	
    <table class="list">
    	<thead>
    		<tr>
    		
    			<th class="name">Grindist</th>
    			<th>Contact</th>
    			<th>Member Type</th>
    			<th>RFID</th>
				<th>Last Check-In</th>
    			<th>&nbsp;</th>
    		</tr>
    	</thead>
    	<tbody id="items">
		<? if ($users) { ?>
    	<? foreach($users as $user): ?>
    	<?
    	if($user->last_sign_in == ""){
    		$lastcheckin = "Never";
    		
    	} else {
    		
    		$lastcheckin= format_date($user->last_sign_in,true);
    	
    	}	
    	
    	?>
    	
    		<tr id="item">
    			
    			<td class="name">
    				<?=g_anchor("/admin/usermanagement/viewprofile/" . $user->user_id,  $user->name ) ?>
    		        <? if($user->is_admin == 1) { echo "<sup>*</sup>"; } ?>
    				<? if($user->company != "") { echo "<br><span class=\"company\">" . $user->company . "</span>"; } 
    				
    				?>
    				
    			</td>
    			<td class="contactinfo"><?=$user->email_address ?><br /><?=$user->phone_number ?></td>
    			<td class="type"><?=$user->plan_code?></td>
    			<td class="rfid"><?=$user->rfid?></td>
    			<td class="lastcheckin"><?=$lastcheckin?></td>
				<td class="alert">&nbsp;</td>
    			<td class="checkcharge">
    				<?
    				echo img(array("src" => "/img/icon-checked-in.png", "class" => "popupButton", "alt" => "Check-In", "id" => "Checkin_" . $user->id));
	  				echo nbs(2);
    				echo img(array("src" => "/img/icon-charge.png", "class" => "popupButton", "alt" => "Charge", "id" => "Charge_" . $user->id));
    				?>
    			</td>
    			<!--<td><a href="<?=site_url('grind-code/members/profile/delete/'.$user->id)?>" target="_blank">delete?</a></td>-->
    		</tr>
    	<? endforeach; ?>
		<?php } ?>
    	</tbody>
    </table>
    <p><sup>*</sup> Denotes an administrator</p>
    <?=$pagination?>
  </section>

<script type="text/javascript">
	$(document).ready(function(){	
	
		
		function setPopupVars(buttonId) {
			var idParts = buttonId.split("_");
			var action = idParts[0];
			var userId = idParts[1];

			$("#" + action + "_user_id").val(userId);
			$("#" + action + "_action").val(action);
			
			return "#popup" + action;
		}

		//LOADING POPUP
		//Click the button event!
		$(".popupButton").click(function(){
			
			var popupDiv = setPopupVars(this.id);
		  
			//centering with css
			centerPopup(popupDiv);
			//load popup
			loadPopup(popupDiv);
			
			$(popupDiv).find('.form-success').hide().end()
			           .find('form').show().find('input').removeClass('error').end()
			           .find('.form-intro').show();
			           		
		});		
		
		$(".popupClose, .close-pop").live('click', function(e) {
		  e.preventDefault();
      disablePopup(".popup");
    });

		//Click out event!
		$("#backgroundPopup").click(function(){
			disablePopup(".popup");
		});

		//Press Escape event!
		$(document).keypress(function(e){
			if(e.keyCode==27 && popupStatus==1){
				disablePopup(".popup");
			}
		});

		$("#popupCheckinSubmit").click(function(){
			dataString = "user_id=" + $("#Checkin_user_id").val() + "&location_id=" + $("#location_id").val() + "&sign_in_method=" + <?=SignInMethod::ADMIN ?>;
			$.ajax({
			  type: "POST",
			  url: "<?=site_url('/grind-code/index.php/admin/usermanagement/checkin')?>",
			  data: dataString,
			  beforeSend: function() {
			    $('#checkin-form, #popupCheckin .form-intro').fadeTo('slow',.5);
          $('#popupCheckin .loader').show();
			  },
			  success: function() {
          $('#popupCheckin .form-success').html('Checkin successful!<br><br><a href="#" class="close-pop">Close</a>').slideDown();
          		 
			    $('#checkin-form, #popupCheckin .form-intro').slideUp().fadeTo(0,1);
          $('#popupCheckin .loader').hide();
			  }
			});
			return false;
		});

		$("#popupChargeSubmit").click(function(){

		  var valid = true;
			if ($('#details').val() === '') {
        $("#details").addClass('error');
        valid = false;
      }
      if (valid) {
        var dc = $("input[name=dc]:checked").val();
        dataString = "user_id=" + $("#Charge_user_id").val() + "&amount=" + $("#amount").val() + "&details=" + $("#details").val() + "&dc=" + dc;
  			$.ajax({
  			  type: "POST",
  			  url: "<?=site_url('/grind-code/index.php/admin/usermanagement/charge')?>",
  			  data: dataString,
  			  beforeSend: function() {
  			    $('#charge-credit, #popupCharge .form-intro').fadeTo('slow',.5);
  			    $('#charge-credit .loader').show();
  			  },
  			  success: function() {
  			    $('#popupCharge .form-success').html((dc == "C" ? "Credit" : "Charge") + ' was successful!<br><br><a href="#" class="close-pop">Close</a>').slideDown();
  			    $('#charge-credit')[0].reset();
  			    $('#charge-credit, #popupCharge .form-intro').slideUp().fadeTo(0,1);
  			    $('#charge-credit .loader').hide();
  			  }
  			});
  		}
			return false;
		});

		$("input[name=dc]").change(function(){
			var dc = $("input[name=dc]:checked").val();
			$("#popupChargeSubmit").val((dc == "C" ? "Credit" : "Charge"));
		});
		
	});
</script>

</div><!-- end #members -->


<div id="backgroundPopup"></div>

<div class="popup" id="popupCheckin">
	<a id="popupCheckinClose" class="popupClose">x</a>
	<h1>Check-In</h1>
	<p class="form-intro">Where are you checking the member into?</p>
	<p class="form-success" style="dislay:none"></p>
	<form action="" method="post" id="checkin-form">
		<fieldset>
			<ul>
				<li>
		      <label for="location_id">Location</label>
					<select id="location_id" name="location_id">
						<? foreach($navlocations as $id => $location) { ?>
							<option value="<?=$id ?>"><?=$location ?></option>
						<? } ?>
					</select>
				</li>
				<li>
					<input id="Checkin_user_id" name="Checkin_user_id" type="hidden" />
					<input id="Checkin_action" name="Checkin_action" type="hidden" />
					<input id="popupCheckinSubmit" class="btn" name="popupCheckinSubmit" type="submit" value="Check-In" />
					<div class="loader" style="display:none"></div>
				</li>
			</ul>
		</fieldset>
	</form>
</div>

<div class="popup" id="popupCharge">
	<a id="popupChargeClose" class="popupClose">x</a>
	<h1>Charge/Credit</h1>
	<p class="form-intro">Please confirm the charge/credit details</p>
	<p class="form-success" style="dislay:none"></p>
	<form action="" method="post" id="charge-credit">
		<fieldset>
			<ul>
				<li>
					<label for="amount">Amount</label>
					<?=form_input(array("type" => "number", "step" => ".05", "min" => "0.00", "id" => "amount", "name" => "amount", "value" => (isset($accesspricing) ? $accesspricing->daily_rate : "0")));?>
				</li>
				<li>
					<label for="details">Details</label>
					<?=form_input(array("type" => "text", "id" => "details", "name" => "details", "value" => ""));?>
				</li>
				<li class="charge-credit">
					<?
					$data = array(
						'type'        => 'radio',
						'name'        => 'dc',
						'id'          => 'dcD',
						'value'       => 'D',
						'checked'     => 'checked'
						);
					echo form_checkbox($data);
					?>
					<label for="dcD">Charge</label>
					<?
					echo nbs(5);
					$data = array(
						'type'        => 'radio',
						'name'        => 'dc',
						'id'          => 'dcC',
						'value'       => 'C'
						);
					echo form_checkbox($data);
					?>
					<label for="dcC">Credit</label>
				</li>
				<li>
					<input id="Charge_user_id" name="Charge_user_id" type="hidden" />
					<input id="Charge_action" name="Charge_action" type="hidden" />
					<input id="popupChargeSubmit" class="btn" name="popupChargeSubmit" type="submit" value="Charge" />
					<div class="loader" style="display:none"></div>
				</li>
			</ul>
		</fieldset>
	</form>
</div>