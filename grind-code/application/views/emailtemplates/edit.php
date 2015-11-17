<?
/**
 * simple edit email template view
 * 
 * edits an email template
 * @joshcampbell
 * @view template
 */
 
 $this->load->helper('form');
 $this->load->helper('html');
 
 ?>
 

 <?php

if(isset($template->id)){
// edit scenario
$hidden = array('id' => $template->id);
$form_attributes = array('id' => 'emailtemplate');
$form = form_open('emailtemplate/update/',$form_attributes, $hidden); 
	$submit = array(
	    'name' => 'submit',
	    'id' => 'submit',
	    'value' => 'submit',
	    'type' => 'submit',
	    'content' => 'Update Template',
	    'class'=>'btn'
	);


} else {
// create scenario
$form =  form_open('emailtemplate/create/',''); 
	$submit = array(
	    'name' => 'submit',
	    'id' => 'submit',
	    'value' => 'submit',
	    'type' => 'submit',
	    'content' => 'Create Template',
	    'class'=>'btn'
	);
	$template->name ="";
	$template->subject ="";
	$template->message ="";
	$template->from_name ="";
	$template->from_email ="";
	$template->reply_to_name ="";
	$template->reply_to_email ="";
	$template->bcc_email ="";
	
	
};

 ?>
<h1>Email Template Editor</h1>
<div id = "section">

<?= $form ?>
	<div id="misc_editor">
		<div id="name" class="form-row">
		<?
			echo form_label('Template Name', 'name' );
			$attributes = array('name'=>'name','id'=>'name','value'=>$template->name,'class'=>'email_template_input required');
			echo form_input($attributes);
		?>
		</div>
		<div id="subject" class="form-row">
		<?
			$template->subject = isset($template->subject) ? $template->subject : "";
			echo form_label('Subject line', 'subject');
			$attributes = array('name'=>'subject','id'=>'subject','value'=>$template->subject,'class'=>'email_template_input required');
			echo form_input($attributes);
		?>
		</div>
		<div id="message" class="form-row">
		<?
			echo form_label('Message Body', 'message');
			$attributes = array('name'=>'message','id'=>'subject','value'=>$template->message,'class'=>'email_template_input required');
			echo form_textarea($attributes);
		?>
		</div>
		<div id="from_name" class="form-row">
		<?
			echo form_label('From Name', 'from_name');
			$attributes = array('name'=>'from_name','id'=>'from_name','value'=>$template->from_name,'class'=>'email_template_input');
			echo form_input($attributes);
		?>
		</div>
		<div id="from_email" class="form-row">
		<?
			echo form_label('From Email', 'from_email');
			$attributes = array('name'=>'from_email','id'=>'from_email','value'=>$template->from_email,'class'=>'email_template_input email required');
			echo form_input($attributes);
		?>
		</div>
		<div id="reply_to_name" class="form-row">
		<?
			echo form_label('Reply To Name', 'reply_to_name');
			$attributes = array('name'=>'reply_to_name','id'=>'reply_to_name','value'=>$template->reply_to_name,'class'=>'email_template_input');
			echo form_input($attributes);
		?>
		</div>
		<div id="reply_to_email" class="form-row">
		<?
			echo form_label('Reply To Email', 'reply_to_email');
			$attributes = array('name'=>'reply_to_email','id'=>'reply_to_email','value'=>$template->reply_to_email,'class'=>'email_template_input email');
			echo form_input($attributes);
		?>
		</div>
		<div id="bcc_email" class="form-row">
		<?
			echo form_label('BCC Email', 'bcc_email');
			$attributes = array('name'=>'bcc_email','id'=>'bcc_email','value'=>$template->bcc_email,'class'=>'email_template_input email');
			echo form_input($attributes);
		?>
		</div>
		<br><br>
		<?
		
			$reset = array(
			    'name' => 'reset',
			    'id' => 'reset',
			    'value' => 'reset',
			    'type' => 'reset',
			    'content' => 'Reset Template',
			    'class'=>'btn'
			);
		
			echo form_button($reset);
			echo nbs(2);
			echo form_button($submit);
			
	?>
	</div>
<?= form_close();?>
</div>
<script type="text/javascript">
 $(document).ready(function(){
    $("#emailtemplate").validate();
  });
</script>
