<?php
require('../lib/file_actions.inc.php');

$path = rtrim( ($_REQUEST['SLAM_CONF_PATH']),'/');
$message = true;

// check and make sure that the lab prefix is kosher
if (strlen($_REQUEST['SLAM_CONF_PREFIX']) != 2)
	$message = 'Please use a lab prefix that is exactly two (2) characters long';
else
	$message = checkDirectoryIsRW( $path );
	
if ($message === true)
	print "<span style='color:green'>These settings are OK.</span>";
else
	print "<span style='color:red'>$message</span>";
?>