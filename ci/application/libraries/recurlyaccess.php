<?
include_once(MEMBERINCLUDEPATH.'/lib/recurly.php');

Recurly_Client::$subdomain = RECURLY_SUBDOMAIN;
Recurly_Client::$apiKey = RECURLY_API_PASSWORD;
?>