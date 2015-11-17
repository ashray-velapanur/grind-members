<?



class ParseTest extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('/billing/accountmodel','am',true);
		
	}

	function parse(){
	    $file = $_SERVER['DOCUMENT_ROOT'].'/members/report_2.xml';
		
		
		$jobs_xml = new SimpleXMLElement($file, null, true);
		
		
		$users = $jobs_xml->xpath('//USERNAME');
		$users = array_unique($users);
		//echo print_r($users);
		foreach ($users as $user){
			$account_code = $user;
						
			$user_charges = $jobs_xml->xpath("JOB[USERNAME='".$account_code."']");
			if ($user_charges){
			$this->am->init($account_code);
			$data = array(); // remove to fix bulk
			foreach($user_charges as $job) // loop through our books
				{
				  
				  if ($job->AMOUNTPAID != 0) {// echo var_dump($job);
				  
				 
				    $charge = substr($job->AMOUNTPAID,0,-1);
				    $name = $job->JOBNAME;
				    $service = $job->SERVICE->NAME;
				    $quantity = $job->SERVICE->CARDINALITY;
				    $quantity = ($quantity>1) ? $quantity . " pages" : $quantity . " page";
					
					$data[] = array("charge"=>$charge,"description"=>$name.', '.$service.', '.$quantity);
					//$this->am->charge($charge,$name.', '.$service.', '.$quantity);  // uncomment to fix bulk
			
					echo $account_code. ": " . $name. "<br />";
				  /*
				    echo $job->USERNAME;
				    echo $job->JOBNAME."<br />";
				    echo $job->SERVICE->CARDINALITY. "…………………………".$job->SERVICE->NAME."……….COST:".$job->AMOUNTPAID;
				    
				    echo "<hr>";   
				   */
				    }
				    $this->am->bulkCharge($data); // remove to fix bulk
				}     
			} // endif		  
		
		
			
		}
		     
				       
	}
}
?>