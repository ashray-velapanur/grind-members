<?
/**
 * get company view template
 * 
 * returns a list of companies as data
 *
 * @joshcampbell
 * @view template
 */
 ?>
<ul>
 
<?php
 foreach($companies as $company){
?>

<li class="company"><?=$company->name?> = <?=$company->description?></li>
<?php } ?>
 </ul>