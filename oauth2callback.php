<?php

error_log('oauth2callback.php');

require_once __DIR__ . '/_siteconfig.php';
require_once __DIR__ . '/vendor/autoload.php';

define('CLIENT_SECRET_PATH', __DIR__ . '/client_secret.json');
define('SCOPES', implode(' ', array(
  Google_Service_Drive::DRIVE)
));

session_start();

$client = new Google_Client();
$client->setAuthConfigFile(CLIENT_SECRET_PATH);
$client->setRedirectUri(ROOTMEMBERPATH . 'oauth2callback.php');
$client->addScope(SCOPES);
$client->setAccessType('offline');

if (! isset($_GET['code'])) {
  $auth_url = $client->createAuthUrl();
  //$auth_url = str_replace('approval_prompt=auto','prompt=consent',$auth_url);
  error_log('Auth URL: '.$auth_url);
  header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
} else {
  $client->authenticate($_GET['code']);
  $_SESSION['access_token'] = $client->getAccessToken();
  $redirect_uri = ROOTMEMBERPATH . 'google_drive.php';
  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}