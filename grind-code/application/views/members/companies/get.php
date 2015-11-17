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

 foreach($attrs as $key => $value){
?>
<li class="attribute"><?= $key.": " .$value ?></li>
<?php
} ?>
 </ul>
 
 