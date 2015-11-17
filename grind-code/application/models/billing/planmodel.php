<?
include_once APPPATH . 'libraries/recurlyaccess.php';

/**
 * Plan Model
 * 
 * Manages tasks associated with data for a "plan"
 * 
 * @joshcampbell
 * @model
 */
 
class PlanModel extends CI_Model {
    
    private $plan;

    function __construct()
       {
           parent::__construct();
       }
       
    // gets live data from recurly about a given plan    
    function init($plan_code)
    {
        $retval=false;
        $this->plan = Recurly_Plan::get($plan_code);
        $this->plan->created_at = date(DATE_ATOM, $this->plan->created_at);
        $retval = $this->plan;
        return $retval;
    }
    
    // creates a plan out of data within our systems
    function local_init($plan_code,$data = NULL)
    {
        $retval=false;
        //you either need to init with a plan_code or data
        if (isset($data)) {
            foreach($this->recurlyPlanToArray($data) as $key=> $value){
                $this->plan->$key = $value;
            }
            //$this->plan->created_at = date(DATE_ATOM, $this->plan->created_at);
            $retval = $this->plan;
        } else {
            // get plan from our database
            $this->db->where('plan_code', $plan_code);
            $results = $this->db->get('plans');
            
            if (count($results) > 0) {
                $this->plan = $results->row();
               // $this->plan->created_at = date(DATE_ATOM, $this->plan->created_at);
                $retval = $this->plan;
            }   
        }
        return $retval;
    }
    
    public function recurlyPlanToArray($data){
        $ret = array();
        $ret['name'] = $data->name;
        $ret['plan_code'] = $data->plan_code;
        $ret['created_at'] = $data->created_at->format('Y-m-d H:i:s');
        $ret['unit_amount_in_cents'] = $data->unit_amount_in_cents->getCurrency('USD')->amount();
        $ret['setup_fee_in_cents'] = $data->setup_fee_in_cents->getCurrency('USD')->amount();
        $ret['plan_interval_length'] = $data->plan_interval_length;
        $ret['plan_interval_unit'] = $data->plan_interval_unit;
        $ret['plan_interval_length'] = $data->trial_interval_length;
        $ret['description'] = $data->description;
        return $ret;
    }   
    
    //upload plan details to the GRIND database for fast access
    //note the real plans are kept in recurly!
    function create(){  
        $this->db->where('plan_code', $this->plan->plan_code);
        $results = $this->db->get('plans');
    
        if ($results->num_rows > 0) {  // duplicate plan code found switching to update
            error_log("PLAN MODEL: duplicate plan code: ".$this->plan->plan_code." updating plan",0);
            $result = $this->update();
            return $result;

        } else {                    // no plan found with that code, so let's add it to our DB
            $result = $this->db->insert('plans',$this->plan);
            error_log("PLAN MODEL: duplicate plan code: ".$this->plan->plan_code." updating plan",0);
            if(!$result){
                log_message("error","error adding a plan to the database");
                return false;
            } else {
                $response = array("action" => "insert","status" => "success");
                return $response;
            }

        }
    }
    
    function get_application_plans(){
        $query = $this->db->get_where('plans', array('in_application'=>1));
        return $query->result();
    }
    
    //update plan details in the GRIND database for fast access
    //note the real plans are kept in recurly!
    function update(){      

        $this->db->where('plan_code',$this->plan->plan_code);
        $result = $this->db->update('plans',$this->plan);
        
        if(!$result){
            log_message("error updating a plan in the database");
            return false;
        } 
        $response = array("action" => "update","status" => "success");
        return $response;
        
    }
    
    function get_plans(){       
        $query = $this->db->get('plans');
        $this->load->model("billing/planmodel","",true);
        $plans = array();
        foreach ($query->result() as $plan)
        {
           $plans[]=$plan;
        }
        
        
    return $plans;
    }
    
    function get_membership_options(){      
        $this->load->helper('date');
        $this->load->model("locationmodel", "", true);
        
        setlocale(LC_MONETARY, 'en_US');
        
        $accesspricing = $this->locationmodel->getAccessPricing();
        foreach($accesspricing as $accessPrice){
            if($accessPrice->default_monthly_rate_code=='monthly'){
                $data['monthly_plan'] = $this->local_init($accessPrice->default_monthly_rate_code);
                $data['daily_rate'] = money_format('%(#1n', $accessPrice->daily_rate);
                $data["monthly_plan"]->unit_amount_in_cents=money_format('%(#1n', $data["monthly_plan"]->unit_amount_in_cents/100);
            }else{
                $data[$accessPrice->default_monthly_rate_code] = $this->local_init($accessPrice->default_monthly_rate_code);
                $data[$accessPrice->default_monthly_rate_code]->unit_amount_in_cents=money_format('%(#1n', $data[$accessPrice->default_monthly_rate_code]->unit_amount_in_cents/100);
            }
        }
        return $data;
    
    }
    
}
?>