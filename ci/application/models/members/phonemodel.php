<?
/**
 * Phone Model
 * 
 * Manages tasks associated with data for the phone number table
 * 
 * @joshcampbell
 * @model
 */
 
class PhoneModel extends CI_Model {
    
    private $phone;
    
    function __construct()
       {
           parent::__construct();
       }
	function init($id)
	{
		$retval = false;
        $sql = "
            select 
                    phone.id as id, user_id, is_primary, phone_type_luid, number, phone_type_lu.short_description
                    from 
                    phone 
                    left outer join phone_type_lu on phone_type_lu.id = phone_type_luid
            where
            user_id = ".$id;    


        $query = $this->db->query($sql);
        
        $results = $query->result();
        
        if (count($results) > 0) {
            $this->phone = $results[0];
            $retval = $this->phone;
            
        }
				        
        return $retval;
	}
	public function update($id=null)
	{
			log_message("debug","PHONEMODEL: update phone number");
			$this->db->set("user_id",$this->phone->user_id);
			$this->db->set("is_primary",$this->phone->is_primary);
			$this->db->set("phone_type_luid",$this->phone->phone_type_luid);
			$this->db->set("number",$this->phone->number);
			$this->db->where("id", $this->phone->id);
			$this->db->update('phone');
			
	}
	
	public function create($data)
	{
		log_message("debug","PHONEMODEL: create phone number");
		$retval = false;
		$this->db->trans_start();
			foreach($data as $key => $value){
				$this->db->set($key,$value);
			}
			$result = $this->db->insert('phone');
			if (!$result){
				throw new exception("phone number creation failed");
			}
		$this->db->trans_complete();
		//set the return value to a newly minted phone object
		$retval = $this->init($this->db->insert_id());
		if(!$retval){
			throw new exception("phone number creation failed");
		}
		return $retval;
	}
	public function delete()
	{
		log_message("debug","PHONEMODEL: delete phone number");
		$this->db->delete('phone', array('id' => $this->phone->id));
	}
	
}
?>