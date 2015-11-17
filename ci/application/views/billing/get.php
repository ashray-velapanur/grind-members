<?
/**
 * get view template
 * 
 * returns a set of plans as data 
 *
 * @joshcampbell
 * @view template
 */
 ?>
 <ul class="plans">
 
<?php
 foreach($plans as $plan){
?>

<li class="plan"><?=$plan->name?> = <?=$plan->unit_amount_in_cents/100 ?></li>
<?php } ?>
 </ul>
 