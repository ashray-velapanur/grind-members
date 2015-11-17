<?php

class CheckinApi{
    private static $_main_endpoint = 'http://apiv1.grindspaces.com/';
    
    public static function checkin($rfid, $location_id){
        $url = self::$_main_endpoint.'checkin/'.urlencode($rfid);
        $data = "location_id=".urlencode($location_id);
        
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $data
        ));
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return $response;
    }
}
