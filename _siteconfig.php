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

        define("ROOTMEMBERPATH", "/grind-members/");
                define("CI_PATH", "grind-members/ci");  //deprecate
                define("CIPATH", "grind-members/ci");

        define("MEMBERINCLUDEPATH", $_SERVER["DOCUMENT_ROOT"]."/grind-members/");
                define("GRINDPUBLICSITEROOT", "http://percolate.grindspaces.com/main/");
                define("DB_USER","583278_percolate");
                define("DB_PASSWORD","$0gR1nd5pac3s");
                define("DB_HOST","localhost");
                define("DB_NAME","test");
                // recurly
                define('RECURLY_API_USERNAME', 'api-test@grindspaces.com');
                define('RECURLY_API_PASSWORD', '79f928bcf89842f78d0d48771944f894');
                define('RECURLY_SUBDOMAIN', 'grindspaces-test');
                define('RECURLY_ENVIRONMENT', 'sandbox');
                define('RECURLY_PRIVATEKEY', '077dc41b75684b878ea1c22aad342003');
?>