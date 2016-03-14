<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

date_default_timezone_set("EST");
require(APPPATH.'/controllers/admin/spaces_dict.php');

class utilities {

    public function redirect($url, $permanent = false) {
        if($permanent) {
            header('HTTP/1.1 301 Moved Permanently');
        }
        header('Location: '.$url);
        exit();
    }

    public function get_environment_for($subdomain) {
        global $environmentsToSpaces;
        foreach ($environmentsToSpaces as $environment => $spaces) {
            foreach ($spaces as $space) {
                if ($space == $subdomain) {
                    return $environment;
                }
            }
        }
        return "";
    }

    public function get_current_environment() {
        $env = null;
        switch (SITE) {
            case "STAGING":
                $env = "prod";
                break;
            case "PRODUCTION":
                $env = "prod";
                break;
            default:
                $env = "dev";
                break;
        };
        return $env;
    }  

    public function get_random_password($chars_min=8, $chars_max=10, $use_upper_case=true, $include_numbers=true, $include_special_chars=true)
    {
        $length = rand($chars_min, $chars_max);
        $selection = 'aeuoyibcdfghjklmnpqrstvwxz';
        if($include_numbers) {
            $selection .= "1234567890";
        }
        /*
		if($include_special_chars) {
            //$selection .= "!@\"#$%&[]{}?|";
            $selection .= "!@#$?|~^*()+";
        }
		*/
                                
        $password = "";
        for($i=0; $i<$length; $i++) {
            $current_letter = $use_upper_case ? (rand(0,1) ? strtoupper($selection[(rand() % strlen($selection))]) : $selection[(rand() % strlen($selection))]) : $selection[(rand() % strlen($selection))];            
            $password .=  $current_letter;
        }                
        
        return $password;
    }
    
    public static function grind_date_format($date) {
        return date_format(date_create($date), "m/d/Y h:i:s A");
    }
	
	public static function date_diff($date1, $date2) { 
		$current = $date1; 
		$datetime2 = date_create($date2); 
		$count = 0; 
		while(date_create($current) < $datetime2){ 
			$current = gmdate("Y-m-d", strtotime("+1 day", strtotime($current))); 
			$count++; 
		} 
		return $count; 
	} 

	public static function echo_array($a) {
		echo "<pre>";
		print_r($a);
		echo "</pre>";
	}
}

?>