<?
/**
 * listing of email templates view template
 * 
 * returns a list of email templates as data
 *
 * @joshcampbell
 * @view template
 */
 

 ?>
 <h1>Email Templates</h1>
<a href="<?= g_url('emailtemplate/edit')?>">Create new template</a>
<br />
 <br />
<ul>
 
<?php



 foreach($templates as $template){
?>
<li class="emailtemplate"><a href="<?=g_url('emailtemplate/get/'.$template->id)?>"><?=$template->name?></a></li>
<?php } ?>
 </ul>