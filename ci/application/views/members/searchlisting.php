<?php

/**
 * View Template for Search Listing (test harness)
 *
 * @joshcampbell
 * @viewtemplate
 */
 
 $this->load->helper('form');
 $this->load->helper('html');
 
 
 $submit = array(
     'name' => 'submit',
     'id' => 'submit',
     'value' => 'submit',
     'type' => 'submit',
     'content' => 'Search',
     'class'=>'btn'
 );
?>

<html>
<head><title>Search Results Test Harness</title></head>
<body>

<h1>Search Results Test Harness</h1>
<?
$form_attributes = array('id' => 'foo');
$form = form_open('members/search/go/',$form_attributes); 
echo $form;

$attributes = array('name'=>'term','id'=>'term','value'=>'','class'=>'field');
echo form_input($attributes);
?>
<?= form_button($submit)?>
</form>
<ul>
	<?
	if(isset($error)){
		echo "sorry an error occurred…we got nothing back";
	} else{
	?>
	
	<?php foreach($members as $key=>$member){ ?>
	<li><strong><?=$member->user_id?></strong> <?=$member->primary . " " . $member->secondary ?>
		<br />
	<?php } // end foreach 
	} // end else
	?>
</ul>
</body>
<!--[if lt IE 9]><script src="https://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
	<script src="<?=ROOTMEMBERPATH?>ci/js/libs/jquery-1.6.2.min.js"></script>

<script type="text/javascript">
	$(document).ready(function(){
		
	});
</script>

</html>