<?
/**
 * listing of Transactions view template
 * 
 * returns a list of transactions for a given user
 *
 *
 * @joshcampbell 
 * @view template
 */
 include_once APPPATH . 'libraries/Date_Difference.phps';
 $this->load->helper("money_helper.php");
 ?>
<h3>Account Information</h3>

<h4>Pending Activity:</h4>
<table>
<?

if ($activity){
 $total = 0;
 foreach($activity as $details){
?>
	 <tr>
        <td class="date"><?=Date_Difference::getString(new DateTime(date("m/d/Y H:i:s", ($details->created_at)))) ?></td>
        <td class="description"><?=$details->description ?></td>
        <td class="amount"><?=format_money($details->total_in_cents) ?></td>
        <td class="invoice"><?=($details->invoice_number == "" ? "Not yet invoiced" : $details->invoice_number) ?></td>
      </tr>
<?php
	}// end foreach 
} else {
	echo "No recent transactions";
	}
?>
</table>


<h4>Previous Invoices</h4>
<table>
<?
if ($invoices) {
foreach($invoices as $invoice){
?>
<tr>
        <td class="date"><?=Date_Difference::getString(new DateTime(date("m/d/Y H:i:s", ($invoice->created_at)))) ?></td>
        <td class="status">&nbsp;</td>
        <td class="amount"><?=format_money($invoice->total_in_cents) ?></td>
        <td class="invoice"><a href="<?=g_url('billing/account/getinvoice/'.$invoice->account_code.'/'.$invoice->id)?>"><?=($invoice->invoice_number == "" ? "Not yet invoiced" : $invoice->invoice_number) ?></a></td>
      </tr>
<?
} //end foreach
} else{
	echo "No invoices yet!";
}
?>
</table>

