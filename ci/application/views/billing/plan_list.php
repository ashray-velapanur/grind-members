<?
/**
 * Plan List view template
 * 
 * returns a set of plans 
 *
 * @joshcampbell
 * @view template
 */
 ?>
 <ul class="plans">
 
<?php
 foreach($plans as $plan){
?>

<li class="plan"><?=$plan->name?> = <?=$plan->unit_amount_in_cents/100 ?>  - code <?=$plan->plan_code?></li>
<?php } ?>
 </ul>
 