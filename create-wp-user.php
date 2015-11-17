<?php
//ADDING A USER
@require_once('wp-config.php');
@require_once('wp-includes/pluggable.php');
@require_once('wp-includes/registration.php');

$id = wp_insert_user($_POST);

add_user_meta( $id, 'registerHash', $_POST['registerHash'] , true );

echo $id;

// VERIFYING A PASSWORD
/*
 if (user_pass_ok('admin', '')) {
echo 'correct';
} else
{
echo 'incorrect';
}
*/

//IS USER LOGGED IN
/*
if ( is_user_logged_in() ) {
 echo "logged in";
}  else
{
 echo "not logged in";
}
*/

?>
