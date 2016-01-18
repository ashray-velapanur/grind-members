<?
/**
 * get company view template
 * 
 * returns a company as data
 *
 * @joshcampbell
 * @view template
 */
 ?>
 <ul class="company">
 
<?php

 foreach($members as $member){
?>
<li class="attribute"><?= $member['name'] ?></li>
<?php
} ?>
 </ul>
 
 