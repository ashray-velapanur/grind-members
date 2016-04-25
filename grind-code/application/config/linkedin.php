<?php

$linkedin_client_id = '75po4cen669lor';
$linkedin_client_secret = 'T0QszZUTI2shVmSR';
$linkedin_state = '987654321';
$linkedin_scope = 'r_basicprofile';
$linkedin_callback_url='http://percolate.grindspaces.com/grind-members/grind-code/index.php/auth/fetch_linkedin_access_token';
$linkedin_authorization_url='https://www.linkedin.com/uas/oauth2/authorization?response_type=code&client_id=%s&redirect_uri=%s&state=%s&scope=%s';
$linkedin_access_token_fetch_url = 'https://www.linkedin.com/uas/oauth2/accessToken';

?>