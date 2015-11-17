<?
/**
 * listing of Transactions view template
 * 
 * returns a list of transactions for a given user
 * this is meant as a test harness
 *
 * @joshcampbell 
 * @view template
 */
 include_once APPPATH . 'libraries/Date_Difference.phps';

 ?>
<h1>Account Information</h1>

<h4>Pending Activity:</h4>
<table>
<?
//echo var_dump($activity);
 $total = 0;
 foreach($activity as $details){
?>
	 <tr>
        <td class="date"><?=Date_Difference::getString(new DateTime(date("m/d/Y H:i:s", ($details->created_at)))) ?></td>
        <td class="description"><?=$details->description ?></td>
        <td class="amount"><?=number_format($details->amount_in_cents, 2) ?></td>
        <td class="invoice"><?=($details->invoice_number == "" ? "Not yet invoiced" : $details->invoice_number) ?></td>
      </tr>
<?php
} ?>
</table>


<h4>Previous Invoices</h4>
<table>
<?
foreach($invoices as $invoice){
?>
<tr>
        <td class="date"><?=Date_Difference::getString(new DateTime(date("m/d/Y H:i:s", ($invoice->date)))) ?></td>
        <td class="status">&nbsp;</td>
        <td class="amount"><?=number_format($invoice->total_in_cents, 2) ?></td>
        <td class="invoice"><a href="<?=g_url('billing/account/getinvoice/'.$invoice->account_code.'/'.$invoice->id)?>"><?=($invoice->invoice_number == "" ? "Not yet invoiced" : $invoice->invoice_number) ?></a></td>
      </tr>
<?


}
?>
</table>

 </ul>

