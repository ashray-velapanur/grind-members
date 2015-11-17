<?
include_once($_SERVER["DOCUMENT_ROOT"]."/grind-code/application/libraries/recurlyaccess.php");
include_once $_SERVER['DOCUMENT_ROOT'] . '/_siteconfig.php';

class InvoiceManagement extends CI_Controller {

	function __construct() {
		parent::__construct();
	}
	
	public function ProcessDailyInvoices($id) {
		
		$verificationGuid = 'da68e4ab-dadd-4b15-94ef-a90be9fd7095';
		
		if ($id === $verificationGuid) {
		
			log_message('debug', 'Daily Invoices: Start');
			$screenOutput = "<h3>Daily Invoicing</h3>";
			try {
				$this->load->model("usermodel");
				$userList = $this->usermodel->getUsers();
				log_message('debug', 'Daily Invoices: Found ' . count($userList) . ' users');
				$screenOutput .= 'Daily Invoices: Found ' . count($userList) . ' users<br/><br/>';
				foreach ($userList as $user) {
					$account = RecurlyAccount::getAccount($user->id);
					//if ($user->email_address === "dcummo@gmail.com") {
					//	$account->chargeAccount(12.34, 'Charging $12.34 to account from unittest');
					//}
	
					if (isset($account)) {
						$invoice = RecurlyInvoice::createInvoice($account->account_code);
						
						if (isset($invoice)) {
							$screenOutput .= 'Invoiced account: Name = ' . $user->first_name . ' ' . $user->last_name . ', Account Code = ' . $user->id . '<br/>';
							log_message('debug', 'Daily Invoices: Invoiced user. Name = ' . $user->first_name . ' ' . $user->last_name . ', Account Code = ' . $user->id);
						} else {
							$screenOutput .= 'No charges for account: Name = ' . $user->first_name . ' ' . $user->last_name . ', Account Code = ' . $user->id  . '<br/>';
						}
					} else {
						$screenOutput .= 'No Recurly account found for ' . $user->first_name . ' ' . $user->last_name . ', Account Code = ' . $user->id  . '<br/>';
					}
				}
			} catch (Exception $e) {
				$screenOutput .= 'Error occurred: Reason: ' + $e->getMessage() . '<br/>';
				log_message('error', 'Daily Invoices: Failed. Reason: ' + $e->getMessage());
			}
			
			
			$screenOutput .= '<br/>Invoicing complete';
			log_message('debug', 'Daily Invo ices: Finished');
			
			$to = EMAIL_G_ADMIN;
			$subject = "[GRIND INVOICING REPORT]";
			$body = $screenOutput; 
			$headers = "From: ".EMAIL_G_ADMIN."\r\n" .
				"Reply-To: ".EMAIL_G_ADMIN."\r\n" .
				"X-Mailer: PHP/" . phpversion() . "\r\n" .
				"MIME-Version: 1.0\r\n" .
				"Content-Type: text/html; charset=ISO-8859-1\r\n";
			mail($to, $subject, $body, $headers);
			
			echo $screenOutput;
		} else {
			echo 'Invalid calling entity';
			log_message('error', 'Daily Invoices: Cron job is passing the wrong id parameter value. It should be ' . $verificationGuid);
		}
	}
	
}

?>