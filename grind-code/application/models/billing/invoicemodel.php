<?php

include_once APPPATH . 'libraries/recurlyaccess.php';

/**
 * Invoice Model
 * 
 * Manages tasks associated with data for an invoice
 * 
 * @joshcampbell
 * @model
 */
 
 class InvoiceModel extends CI_Model {
    
    var $id;
    var $account_code;
    var $invoice; 
    
    function __construct()
       {
           parent::__construct();
       }
	
	function init($account_code,$id=NULL)
	{

		// initialize basic parameters
		$this->id = (isset($id) ? $id : ""); // id refers to 
		$this->account_code = $account_code;  // account codes are same as user_id
		$this->invoice = new RecurlyInvoice($account_code);

		return $this;
	}
	function getInvoice(){
		$invoice = $this->invoice->getInvoice($id);
		return $invoice;
	}
}
?>