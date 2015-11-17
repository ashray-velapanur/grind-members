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
		define("SITE", "LOCAL");
	break;
	
	default:
		define("SITE", "LOCAL");
	break;
}



        define("ROOTMEMBERPATH", "/Library/WebServer/Documents/grind-members/");
		define("CI_PATH", "");
		define("CIPATH", "ci");  
        define("MEMBERINCLUDEPATH", "/Library/WebServer/Documents/grind-members/"); 
		define("GRINDPUBLICSITEROOT", "http://localhost/grind-members");
		define("DB_USER","root");
		define("DB_PASSWORD","beagles666");
		define("DB_HOST","127.0.0.1");
		define("DB_NAME","grind_2");	
		// recurly
		define('RECURLY_API_USERNAME', 'api@grindspaces.com');
		define('RECURLY_API_PASSWORD', '52bda929c0114031af81e844d5c5d0e3');
		define('RECURLY_SUBDOMAIN', 'grindspaces');
		define('RECURLY_ENVIRONMENT', 'production');
		define('RECURLY_PRIVATEKEY', '6c86e4f0eaf1425c893fb3789356956c');

        

?>
