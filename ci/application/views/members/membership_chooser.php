<?php

/**
 * View Template for Membership Chooser
 *
 * @joshcampbell
 * @viewtemplate
 */
?>
<select id="plan_code" name="plan_code">
	<option value="<?= $monthly_plan->plan_code; ?>"><?= $monthly_plan->name; ?> - $<?= $monthly_plan->unit_amount_in_cents/100; ?></option>
	<option value="daily">Daily Member - $<?= $daily_rate; ?> per use</option>
</select>