<?
/**
 * Delete User Screen
 * 
 * Admin only
 * Controller will process a user id to delete and display this page.
 *
 * @joshcampbell
 * @view template
 */
 
 $this->load->helper('form');
 $this->load->helper('html');

 $form =  form_open('members/profile/delete/',''); 
	$submit = array(
	    'name' => 'submit',
	    'id' => 'submit',
	    'value' => 'submit',
	    'type' => 'submit',
	    'content' => 'Delete Member',
	    'class'=>'btn'
	);

 ?>
 
<h1>Delete a member</h1>
 
 
<? if ($allowed) { 
	if (isset($error)){
		?><div class="error">Could not delete because: <?= $error ?></div><?
		}
	if($member) {
	?> <?= $member->first_name?> has been deleted 
	
	<? } ?>
	<?= $form ?>
		Enter the member id you would like to remove. Caution this is permanent.
		<br />
		<div id="user" class="form-row">
		<?
				$attributes = array('name'=>'user_id','id'=>'user_id','value'=>"",'class'=>'required');
			echo form_input($attributes);
		?>
		</div>
		<br><br>
		<?= form_button($submit);?>
	<?= form_close();?>
		
<? } else { ?>
	<aside>Sorry but you are not allowed to access this function.</aside>
<? } ?>

