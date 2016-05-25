<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

date_default_timezone_set("EST");
require(APPPATH.'/controllers/admin/spaces_dict.php');

class utilities {

    public function do_get($url, $params=array()) {
        $get_result = array();
        $curl = curl_init();
        $url = $url.'?';
        foreach ($params as $key => $value) {
            $url.="&".$key."=".$value;
        }
        error_log("GET: ".$url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        $error_message = curl_error($curl);
        $result_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if($result_code == 200) {
            $get_result = (array)json_decode($result);
        } else {
            error_log('Error: '.$error_message.', HTTP Code: '.$result_code);
        }
        error_log(json_encode($get_result));
        return $get_result;
    }

    public function do_post($url, $params=array(), $oauth_authorization_token) {
        error_log(json_encode($params));
        $params_string = json_encode($params);
        error_log($params_string);
        $post_result = array();
        $curl = curl_init();
        error_log("POST: ".$url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST"); 
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params_string);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);                                                                      
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(                                                                          
            'Content-Type: application/json',
            'Content-Length: ' . strlen($params_string),
            'Authorization: OAuth '.$oauth_authorization_token
            )
        );
        $result = curl_exec($curl);
        $error_message = curl_error($curl);
        $result_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if($result_code >= 200 && $result_code < 300) {
            $post_result = (array)json_decode($result);
        } else {
            error_log('Error: '.$error_message.', HTTP Code: '.$result_code);
            $post_result = array('error' => $error_message);
        }
        error_log(json_encode($post_result));
        return $post_result;
    }

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