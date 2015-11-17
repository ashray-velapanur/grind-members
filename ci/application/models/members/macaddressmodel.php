<?

/**
 * MacAddress Model
 * 
 * Manages tasks associated with data for the macaddress address table
 * 
 * @joshcampbell
 * @model
 */
 
class MacAddressModel extends CI_Model {
    
    public $macaddress;
    public $macaddresses;
    
    
    function __construct()
       {
           parent::__construct();
          	$this->macaddress->id ="";
			$this->macaddress->user_id="";
			$this->macaddress->description="";
			$this->macaddress->address = "";
			$this->macaddresses = false; // by default the object represents a single address;
			$this->exists = false;
       }
       
	function init($field,$type=NULL)
	{
		if(!isset($type)){ // assume macaddress
			$type = "address";
		} 
		$sql = "select * from macaddress where ".$type."='".$field."';";

		$query = $this->db->query($sql);
		if($query->num_rows >0 )	{
			$this->exists = true; // the mac already exists in our system
			
			// if the db brings back more than one address
			if($query->num_rows > 1){
				foreach ($query->result() as $row){
					$this->macaddresses[]=$row;
				}
			} else {
				$this->macaddress = $query->row();
			}
		} else {
			$this->exists = false;
		
		}
	}
	
	public function validate($user_id){
		if($this->macaddress->user_id == $user_id){
			// the user_id passed matches this macaddress user
			return true;
		} else {
			// the user_id doesn't match what we have
			return false;
		}
	}
	
	public function update()
	{
		$this->db->set('user_id',$this->macaddress->user_id);
		$this->db->set('description',$this->macaddress->description);
		$this->db->set('address',$this->macaddress->address);
		$this->db->where('id',$this->macaddress->id);
		$result = $this->db->update('macaddress');
		
		if(!$result){
			error_log("Error updating macaddress",0);
			return false;
		} else {
			return true;
		}
	}
	
	public function create($macaddress,$user_id,$description=NULL)
	{
		error_log("Add mac address:".$macaddress,0);
		$retval = false;
		
		if ($macaddress == ""){
			error_log("MacAddress Creation Failed Empty Mac Address:".$macaddress,0);
		    throw new exception("mac address creation failed");
		    return $retval;
		}

		$data = array("user_id"=>$user_id,"address"=>$macaddress,"description"=>$description);
		$result = $this->db->insert('macaddress',$data);
		error_log($result,0);
		if (!$result) {
		    error_log("MacAddress Creation Failed".$macaddress,0);
		    throw new exception("mac address creation failed");
		    return $retval;
		}
		
		//set the return value to a newly minted macaddress object
		$this->macaddress->id = $this->db->insert_id();
		$this->macaddress->address = $macaddress;
		$this->macaddress->user_id = $user_id;
		$this->macaddress->description = $description;
		$retval = $this->macaddress;
		
		if(!$retval){
			$this->exists = false;
			error_log("MacAddress Creation Failed",0);
			throw new exception("mac address creation failed");
			return $retval;
		}
		$this->exists = true;
		return $retval;
	}
	
	public function delete()
	{
		log_message("debug","delete mac address");
		$this->db->delete('macaddress', array('id' => $this->macaddress->id));
	}
	
}
?>