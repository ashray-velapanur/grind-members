<?php
/*
Plugin Name:    Authenticate by XML-RPC
Plugin URI:     http://magicandmight.com/
Description:    Simple boolean authentication by XMLRPC.
Version:        0.1
Author:         Magic+Might
Author URI:     http://magicandmight.com/
 */
 
add_filter( 'xmlrpc_methods', 'add_new_xmlrpc_methods' );
function add_new_xmlrpc_methods( $methods ) {
	$methods['grind.loginCheck'] = 'loginCheck';
	return $methods;
}
 

function loginCheck($args){
		$username=$args[0];
		$password=$args[1];
		if (!user_pass_ok($username, $password)) {
			//an error occurred, the username and password supplied were not valid
			return false;
		}
		// no errors occurred, the U&P are good, return true
		return true;
}