<?

/* Email Constants */
define("EMAIL_G_ADMIN", "admin@grindspaces.com");
define("EMAIL_G_PUBLIC","workliquid@grindspaces.com");
define("EMAIL_G_MEMBERREQ","workliquid@grindspaces.com"); // for member requests to the front desk
define("EMAIL_G_MEMBER","membership@grindspaces.com");
define("EMAIL_G_NAME","Grind");
define("WP_ADMIN_ROLE", "admin");
define("ENCRYPTKEY", "WYJUz786z1128na1Irh44626");
define("PRINT_CREDIT_AMOUNT", 35.00);

$http_host = $_SERVER['HTTP_HOST'];

switch($http_host){
	case 'local.grindspaces.com':
		define("SITE", "LOCAL");
	break;
	
	case 'percolate.grindspaces.com';
		define("SITE", "STAGING");
	break;
	
	default:
		define("SITE", "PRODUCTION");
	break;
}



        define("ROOTMEMBERPATH", "/");
		define("CI_PATH", "");
		define("CIPATH", "ci");  
        define("MEMBERINCLUDEPATH", $_SERVER["DOCUMENT_ROOT"]."/"); 
		define("GRINDPUBLICSITEROOT", "http://www.grindspaces.com");
		define("DB_USER","583278_grind");
		define("DB_PASSWORD","$0gR1nd5pac3s");
		define("DB_HOST","localhost");
		define("DB_NAME","583278_grindspaces");	
		// recurly
		define('RECURLY_API_USERNAME', 'api@grindspaces.com');
		define('RECURLY_API_PASSWORD', '52bda929c0114031af81e844d5c5d0e3');
		define('RECURLY_SUBDOMAIN', 'grindspaces');
		define('RECURLY_ENVIRONMENT', 'production');
		define('RECURLY_PRIVATEKEY', '6c86e4f0eaf1425c893fb3789356956c');

        

?>
