<?php
require('../lib/file_actions.inc.php');

$path = rtrim( base64_decode($_REQUEST['SLAM_CONF_PATH']),'/');
$message = true;

// check and make sure that the lab prefix is kosher
if (strlen($_REQUEST['SLAM_CONF_PREFIX']) != 2)
	$message = 'Please use a lab prefix that is exactly two (2) characters long';
elseif (ereg('[^A-Za-z0-9]', $_REQUEST['SLAM_CONF_PREFIX']))
	$message = 'Please only use alphanumeric (A-Z or 1-2) characters in the lab prefix.';
else
	$message = checkDirectoryIsRW( $path );
	
if ($message === true)
	print "<span style='color:green'>These settings are OK.</span>";
else
	print "<span style='color:red'>$message</span>";
?>