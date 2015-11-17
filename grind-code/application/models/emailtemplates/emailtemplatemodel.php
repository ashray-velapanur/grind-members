<?
include_once APPPATH . 'libraries/recurlyaccess.php';

/**
 * Email Template Model
 * 
 * Manages tasks associated with data for the email (Template) table
 * 
 * @joshcampbell
 * @model
 */
 
class EmailTemplateModel extends CI_Model {
    
    private $template; // email template
    
    function __construct()
       {
           parent::__construct();
           error_log("testing email template",0);
       }
	function init($id=NULL)
	{
		error_log("testing email template2",0);
		log_message("debug","Initalizing a email template");
		
		// initialize basic parameters
		$this->template->id = "";
		$this->template->name = "";
		$this->template->subject = "";
		$this->template->message = "";
		$this->template->from_name = "";
		$this->template->from_email = "";
		$this->template->reply_to_name = "";
		$this->template->reply_to_email = "";
		$this->template->bcc_address = "";
		
		$retval = false;
		
		if(isset($id))
		{
	        $this->db->where('id',$id);
	        $query = $this->db->get('email_template');  
	        
	        if ($query->num_rows() > 0)
	        {
	            $this->template = $query->row();
	            return $this->template; 
	                  
	        } else {
	        
	        	// no email templates were found with that ID
	        	return false;
	        	
	        }
	    }      
        return false;
	}
	
	public function local_init($data){
		$retval=false;
		//you need to init with a data array
		if (isset($data)) {
			foreach($data as $key=> $value){
				$this->template->$key = $value;
			}
			
			$retval = $this->template;
		} 
		return $retval;
	}
	
	public function update()
	{
		log_message("debug","Update an Email Template: ".$this->template->name);
		if ($this->template->submit){
			unset($this->template->submit);
		}
		$this->db->where('id',$this->template->id);
		$result = $this->db->update('email_template',$this->template);
		
		return $result;					
	}
	
	public function create()
	{
		log_message("debug","Create new email template: ".$this->template->name);
		if ($this->template->submit){
			unset($this->template->submit);
		}
		
		$result = $this->db->insert('email_template',$this->template);

		return $this->db->insert_id();
	}
	
	public function delete()
	{
		log_message("debug","Delete email template: ".$this->template->name);
		$this->db->where('id',$this->template->id);
		$result = $this->db->delete('email_template');
		return $result;
	}
	public function set_prop($prop,$value)
	{
		$this->{$prop} = $value;
	}
	
	
	public function get_all()
	{
		$retval = false;
		$query = $this->db->get('email_template');
	    if ($query->num_rows() > 0)
	    {
		   // do something
		   $retval = $query->result();
        }	        
        return $retval; // no companies
	}
	
	public function send($to_address)
	{
		log_message("debug","Sending an email using template: ".$this->template->name);
		$this->load->library('email');
		$this->load->library('postmark');
		$this->load->library('parser');
		$html = "/emailtemplates/htmlmessage";
		
	// SET THE REPLY AND FROM ADDRESSES AND NAMES
		// FROM
		if(isset($this->template->from_name)){
			$from_name = $this->template->from_name;
		}else{
			$from_name = $this->template->from_email;
		}
		$this->postmark->from($this->template->from_email, $this->template->from_name);
		$data['from'] = $this->template->from_email; // passed into the body of the html message
		
		// REPLY TO
		if(isset($this->template->reply_to_email)){
			if(isset($this->template->reply_to_name)){
				$reply_name = $this->template->reply_to_name;
			}else{
				$reply_name = $this->template->reply_to_email;
			}
			$this->postmark->reply_to($this->template->reply_to_email, $reply_name);
		} // IF REPLY TO IS NOT SET, THE EMAIL LIBRARY WILL USE THE FROM AS REPLY
	
	// SET THE TO & BCC ADDRESSES
		$this->postmark->to($to_address);  // TO
		
		if(isset($this->template->bcc_address)){  // BCC
			$this->postmark->bcc($this->template->bcc_email);	
		}
	// SET SUBJECT
		$this->template->subject = stripslashes($this->template->subject); 
		$this->postmark->subject($this->template->subject);
		$data["subject"] = $this->template->subject; // passed into the body of the html message
		
	// SET MESSAGE
		
		$data['message'] = str_replace("\n", "<br />", $this->template->message); // passed into the body of the html message
		$data['message'] = stripslashes($data['message']);
		$htmlMessage =  $this->parser->parse($html, $data, true);
		$this->postmark->message($htmlMessage);
		$this->postmark->set_alt_message($this->template->message);
		
	// SEND THE MESSAGE
		error_log("sending? to:".$to_address,0);
		$result = $this->postmark->send();
	//	$result=$this->email->print_debugger();
		return $result;
	}
	
}
?>
