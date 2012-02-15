<?php

require('../lib/db_actions.inc.php');

$server = base64_decode($_REQUEST['SLAM_DB_HOST']);
$dbname = base64_decode($_REQUEST['SLAM_DB_NAME']);
$dbuser = base64_decode($_REQUEST['SLAM_DB_USER']);
$dbpass = base64_decode($_REQUEST['SLAM_DB_PASS']);

if ( ($ret = checkDbOptions( $server, $dbname, $dbuser, $dbpass )) === true)
	print "<span style='color:green'>These settings are OK.</span>";
else
	print "<span style='color:red'>{$ret[0]}</span>";
?>