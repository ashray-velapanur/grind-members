<?php
/**
 * Front to the WordPress application. This file doesn't do anything, but loads
* wp-blog-header.php which does and tells WordPress to load the theme.
*
* @package WordPress
*/

/**
* Tells WordPress to load the WordPress theme and output it.
*
* @var bool
*/
//define('WP_USE_THEMES', true);

/***************************************************************************
* CODEIGNITER INTEGRATION 
* creates a global $CI object
* by default it loads the homepage and captures it in the variable
* $GLOBALS['wp_ci_output']
****************************************************************************/
define('STDIN', TRUE);
$_SERVER['argv'] = array();
ob_start();
require('./ci/index.php'); 
$GLOBALS['wp_ci_output'] = ob_get_contents();
ob_end_clean();
$GLOBALS['CI'] = get_instance();
require_once(APPPATH.'helpers/wordpress_helper.php');

/***************************************************************************/

/** Loads the WordPress Environment and Template */
require(dirname(__FILE__).'/wp-blog-header.php');