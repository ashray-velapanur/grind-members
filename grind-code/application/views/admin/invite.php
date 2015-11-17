<form action="<?=ROOTMEMBERPATH?>grind-code/index.php/admin/applicantmanagement/inviteuser" method="post" id="invite" class="clearfix">
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
        <input type="text" class="required" id="first_name" name="first_name" value="<?=(isset($user) ? $user->first_name : "")?>" />
      </li>
      <li>
        <label for="last_name">Last Name</label>
        <input type="text" class="required" id="last_name" name="last_name" value="<?=(isset($user) ? $user->last_name : "")?>" />
      </li>
      <li>
        <label for="primary_email">Email</label>
        <input type="text" class="required email" id="email_address" name="email_address" value="<?=(isset($user) ? $user->email : "")?>" />
      </li>
      <li>
        <label for="referrer">Referrer</label>
        <input type="text" class="" id="referrer" name="referrer" value="<?=(isset($user) ? $user->referrer : "")?>" />
      </li>
    </ul>
  </fieldset>
  
  <div class="clearfix" style="clear:both">
    <br><br>
    <input type="hidden" name="invite" value="true" />
    <input type="submit" value="Submit" class="btn" /> &nbsp;&nbsp; <div class="loader" style="display: none"></div>
  </div>
  
</form>
<script type="text/javascript">
    $(function() {
        
        $("#pwdchange").click(function() {
            alert("coming soon");
            return false;
        });
    
	$("#invite").validate({
	  submitHandler: function(form){
		  $('.loader').show();
		  $('#invite').fadeTo('slow',.5);
		  form.submit();
	  }
    });
	
    $("#invite #email_address").rules("add", {
		remote: '<?=ROOTMEMBERPATH?>grind-code/index.php/admin/utility/uniqueEmail',
		messages: {
	   	remote: jQuery.format("This email is already taken")
	 }
	 
	});
});
	

</script>

