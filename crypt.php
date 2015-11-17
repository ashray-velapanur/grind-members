<?php

    function fnEncrypt($sValue, $sSecretKey)
    {
       return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $sSecretKey, $sValue, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
		
    }

	function fnDecrypt($sValue, $sSecretKey)
	   {
	        return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $sSecretKey, base64_decode($sValue), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
	   }





	$key = "WYJUz786z1128na1Irh44626";
    $cook = $_COOKIE["gauth"];
    
	echo "gauth cookie:   ".$cook;
	 echo "<br />";
//	echo "gauth cookie after base64decode:   ".base64_decode($_COOKIE["gauth"]);
//	echo "<br />";
	//$cook = base64_decode($_COOKIE["gauth"]);
	$cook = fnDecrypt($cook ,$key);
	
	echo($cook);
	echo("<hr />");
	echo "is it an array".(is_array($cook) ? "true" : "false");
	echo "<hr />";
	$cook = json_decode($cook);	
	var_dump($cook);
	echo("<hr />");
	echo "is it an array".(is_array($cook) ? "true" : "false");
	echo "<hr />";
	
    $msg = "my little secret";

    $new_value = fnEncrypt($msg, $key);
    echo $new_value;
    echo "<br />";

    echo fnDecrypt($new_value, $key);
    echo "<br />";



    /*
     * Outputs:
     *
     * $ php crypt.php 
     * ?8@??DX<;]I:"??0???@FԦc??m?ܠ??B'?ȫĵ?7}?????cJC?7???
     * vi4XRU7Y93ogVMXuunUtmlYIqxlUHpLFa44Nuah8RJc=
     * my little secret
     *
     */


?>