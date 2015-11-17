<?php
/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache

/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */
// assumes that _siteconfig.php is in same directory as wp_config.php  
if ( !defined('__DIR__') ) define('__DIR__', dirname(__FILE__));
include_once(__DIR__."/_siteconfig.php");

switch (SITE) {
		case "STAGING":
		define('WP_DEBUG', false);

		/* SSL CONFIG */
		define('FORCE_SSL_ADMIN', false);
        break;
		
  	    case "PRODUCTION":
		define('WP_DEBUG', false);

		/* SSL CONFIG */
		define('FORCE_SSL_ADMIN', true);
        break;  
}



// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
// set above in switch statement 
//define('DB_NAME', '583278_grindspaces');

/** MySQL database username */
// set above in switch statement 
// define('DB_USER', '583278_grind');

/** MySQL database password */
// set above in switch statement 
//define('DB_PASSWORD', '$0gR1nd5pac3s');

/** MySQL hostname */
// set above in switch statement 
//define('DB_HOST', 'mysql50-96.wc2.dfw1.stabletransit.com');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'V7?W^6nu*-zZ^-:G|&bNtp yXzlvk z2|&0IT47xz0WCY:0.;8Nnh82|}(O |tda');
define('SECURE_AUTH_KEY',  '_j=H,y%-!iHtG/v[:23zwgjLb=oEsmwTjUOcYa=[Vv[%VR>U}+^f+Y+u5{73vHP9');
define('LOGGED_IN_KEY',    'TRI0xLYcY*oW{OZ+-ax2{8MYr50VjuNLg0C_|/1#(R-y24/|r~qvMcH=+YCW[I(z');
define('NONCE_KEY',        '><s}UC%|h_p18XeL|3KgDl-`Lb$m=^+t*~i:-N{},.TtdK_AJB/Jv-->W,2R`1V@');
define('AUTH_SALT',        '8qBk^8$R/H~-9{$k-[Of-eXm%ft-51&_gO.~Awhp+H<p]g+XkJPW6UWP*X|>oo$H');
define('SECURE_AUTH_SALT', ':7}b/ktnlJl08@fqFQP+RIi~HQmwA7W|AD6>AL=!rGaVGB-&cM|q e,Lg ?NdY53');
define('LOGGED_IN_SALT',   'G7D-{T}lj|sr06=YI-{OP=lc8]&sO4D7L2XyPxS:LoR/I8e=JxUdsxs^(/E$-vB9');
define('NONCE_SALT',       ')0zP~2B$3?2cvNh^X1oB{0GBu#z>/@!G)ij~eqP7O{nY#c;:}`qgYNbMqSa-g+C-');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wpmember_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');




/** Sets up config for wp cache */



/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');


