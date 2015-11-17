<?
/**
 * get email view template
 * 
 * returns a company as data
 *
 * @joshcampbell
 * @view template
 */
 ?>
 
 <h1>Email Template</h1>
 <a href="<?=g_url('emailtemplate/')?>">< back</a>
 <br />
 <ul class="email">
 <br />
<?php

 foreach($attrs as $key => $value){
?>
<li class="attribute"><?= $key.": " .$value ?></li>
<?php
} ?>
 </ul>
 <br /> <br />
 <a href="<?=g_url('emailtemplate/edit/'.$attrs->id)?>">Edit</a>
 &nbsp;&nbsp;
 <a href="<?=g_url('emailtemplate/delete/'.$attrs->id)?>">Delete</a>
 
 