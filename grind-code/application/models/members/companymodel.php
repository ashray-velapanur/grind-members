<?
include_once APPPATH . 'libraries/recurlyaccess.php';

/**
 * Company Model
 * 
 * Manages tasks associated with data for the company address table
 * 
 * @joshcampbell
 * @model
 */
 
class CompanyModel extends CI_Model {
    
    private $company;
    
    function __construct()
       {
           parent::__construct();
       }
	function init($id=NULL)
	{
		
		// initialize basic parameters
		$this->company->id = "";
		$this->company->name = "";
		$this->company->description = "";
		$this->company->website = "";
		$this->company->twitter = "";
		$retval = false;
		
		if(isset($id))
		{
	        $this->db->where('id',$id);
	        $query = $this->db->get('company');  
	        
	        if ($query->num_rows() > 0)
	        {
	            $this->company = $query->row();
	            return $this->company; 
	                  
	        } else {
	        
	        	// no companies were found with that ID
	        	return false;
	        	
	        }
	    }      
        return false;
	}
	
	public function update()
	{
		log_message("debug","Create new Company: ".$this->company->name);
		
		$this->db->where('id',$this->company->id);
		$result = $this->db->update();
		return $result;					
	}
	
	public function create($data)
	{
		log_message("debug","Create new Company: ".$this->company->name);
		
		return $retval;
	}
	public function delete()
	{
		log_message("debug","Delete Company: ".$this->company->name);
		
		
	}
	
	function get_all()
	{
		$retval = false;
		$query = $this->db->get('company');
	    if ($query->num_rows() > 0)
	    {
		   // do something
		   $retval = $query->result();
        }	        
        return $retval; // no companies
	}

	public function get_members($id) {
		if(isset($id))
		{
	        $this->db->where('id',$id);
	        $query = $this->db->get('company');  
	        
	        if ($query->num_rows() > 0)
	        {
	            $this->company = $query->row();
	            $sql = "select u.id as id, u.first_name as fname, u.last_name as lname, u.wp_users_id as wpid
	            		from
	            			user u
	            			left outer join company c on c.id = u.company_id
	            		where ";
	            $sql = $sql.' c.id = '.$id;
				$query = $this->db->query($sql);
				$results = $query->result();
				$members = [];
				foreach ($results as $result) {
					$result = (array)$result;
					$member = [
						'name' => $result['fname'].' '.$result['lname'],
					];
					array_push($members, $member);
				}
				$data = [
					'members' => $members
				];
				return $data;
	        } else {
	        
	        	// no companies were found with that ID
	        	return false;
	        	
	        }
	    }
	}
	
}
?>