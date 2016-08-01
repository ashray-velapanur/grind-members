<?php

error_log('google_drive.php');
require_once __DIR__ . '/_siteconfig.php';
require_once __DIR__ . '/vendor/autoload.php';

define('CLIENT_SECRET_PATH', __DIR__ . '/client_secret.json');
define('SCOPES', implode(' ', array(
  Google_Service_Drive::DRIVE)
));

session_start();

$client = new Google_Client();
$client->setAuthConfigFile(CLIENT_SECRET_PATH);
$client->addScope(SCOPES);
$client->setAccessType('offline');

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
	$client->setAccessToken($_SESSION['access_token']);
	if($client->isAccessTokenExpired()) {
		error_log("Access Token has expired!");
		$refresh_token = $client->getRefreshToken();
		error_log('Refresh Token: ');
    	error_log($refresh_token);
		$client->refreshToken($refresh_token);
		$_SESSION['access_token'] = $client->getAccessToken();
		$client->setAccessToken($_SESSION['access_token']);
	} else {
		$drive_service = new Google_Service_Drive($client);
		if(isset($_SESSION['not_authorized'])) {
			error_log($_SESSION['not_authorized']);
		}
		if(isset($_SESSION['app_request_uri']) && isset($_SESSION['not_authorized']) && $_SESSION['not_authorized']) {
			$request_uri =  $_SESSION['app_request_uri'];
			error_log("request_uri: ".$request_uri);
			$_SESSION['app_request_uri'] = NULL;
			$_SESSION['not_authorized'] = 0;
			header('Location: ' . filter_var($request_uri, FILTER_SANITIZE_URL));
		}
	}
} else {
	$redirect_uri = ROOTMEMBERPATH. 'oauth2callback.php';
	error_log($redirect_uri);
	header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}