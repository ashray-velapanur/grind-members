<?php

/**
 * View Template for Membership Chooser
 *
 * @joshcampbell
 * @viewtemplate
 */
 
 // by default we assume the simple plan chooser
 $selectName = isset($selectName) ? $selectName : "plan_code";
 
?>
<? if (isset($allplans)){ ?>
<select id="<?=$selectName?>" name="<?=$selectName?>">
<? foreach ($plans as $plan) {
			if(isset($accesspricing) && $accesspricing->allow_monthly_memberships < 1 ){  ?>
				<?php if($plan->plan_code != 'monthly'){ ?>
						          <option value="<?=$plan->plan_code?>"><?=$plan->name?></option>
				<?php } ?>
		<?	} else { ?>
		          <option value="<?=$plan->plan_code?>"><?=$plan->name?></option>
			<? }  ?>
        <? } ?>
</select>

<?
} else {
?>
<select id="<?=$selectName?>" name="<?=$selectName?>">
	<option value="<?= $monthly_plan->plan_code; ?>"><?= $monthly_plan->name; ?> <?= $monthly_plan->unit_amount_in_cents; ?> per month</option>
	<option value="daily">Daily Member <?= $daily_rate; ?> per use</option>
</select>

<?  }  // end ?>