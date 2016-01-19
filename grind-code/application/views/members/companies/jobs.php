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

 foreach($jobs as $job){
?>
<li class="attribute"><?= $job['title'] ?></li>
<li class="attribute"><?= $job['type'] ?></li>
<li class="attribute"><?= $job['url'] ?></li>
<?php
} ?>
 </ul>
 
 