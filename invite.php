<form action="<?=ROOTMEMBERPATH?>grind-code/index.php/admin/usermanagement/inviteUser" method="post" id="invite" class="clearfix">
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
        <input type="text" class="required email" id="primary_email" name="primary_email" value="<?=(isset($user) ? $user->email : "")?>" />
      </li>
      <li style="border:0">
        <input type="hidden" name="invite" value="true" />
        <br><br><input type="submit" value="Submit" class="btn" />
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
        
    $("#invite #primary_email").rules("add", {
		remote: '/grind-code/index.php/admin/utility/uniqueEmail',
		messages: {
	   	remote: jQuery.format("This email is already taken")
	 }
	});

  $("#invite").validate();
});
	

</script>

