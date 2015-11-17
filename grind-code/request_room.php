<?php

/* this is a quick script that will take a parameters for conference room booking and send an email */
/* it will return true or false based on the success */

//the front desk email address
//move to a config file
$destination = "frontdesk@joshc.com";
$mailer = "grind-web@joshc.com";

$headers = "Return-Path: grind-web@joshc.com\n"; 
$headers .= "From: GRIND <grindweb@joshc.com>\n"; 
$headers .= "Reply-to: grind-web@joshc.com.com\n"; 
$headers .= 'MIME-Version: 1.0' . "\n"; 
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n"; 

echo $headers . "<br />" . $destination;

if (isset($_REQUEST['user'],$_REQUEST['name'],$_REQUEST['email'],$_REQUEST['date'],$_REQUEST['time'],$_REQUEST['desc'],$_REQUEST['duration'])) {
//	constructEmail($_REQUEST['user'],$_REQUEST['name'],$_REQUEST['email'],$_REQUEST['date'],$_REQUEST['time'],$_REQUEST['desc'],$_REQUEST['duration']);

	$subject = "Conference Room Request";
	$message = $_REQUEST['name']." is requesting a conference room.\n\n<b>Request Details</b>:\nUserId: ".$_REQUEST['user']."\nEmail: ".$_REQUEST['email']."\nDate: ".$_REQUEST['date']."\nStart Time: ".$_REQUEST['time']."\nDuration: ".$_REQUEST['duration']."\n\nPlease check conference room availability and confirm the reservation with the Grind Member.";
	$foo = mail($destination,$subject,$message,$headers);
	echo "mail sent";
	return true;
} else {
	echo "broken";
	return false;
}

