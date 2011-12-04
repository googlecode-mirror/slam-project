<?php

require('db_actions.php');

$server = base64_decode($_REQUEST['SLAM_DB_HOST']);
$dbname = base64_decode($_REQUEST['SLAM_DB_NAME']);
$dbuser = base64_decode($_REQUEST['SLAM_DB_USER']);
$dbpass = base64_decode($_REQUEST['SLAM_DB_PASS']);

if ($server == '')
	$message = "Please specify a database server IP or 'localhost'";
elseif( $dbname == '')
	$message = "Please specify a database name.";
elseif( $dbuser == '')
	$message = "Please specify a database user.";
elseif( $dbpass == '')
	$message = "Please provide a password for the database user.";
else
{
	$link = @mysql_connect( $server, $dbuser, $dbpass );
	if (!$link)
		$message = "Could not connect to the server '$server' with the provided username '$dbuser'.";
	else
	{
		if (mysql_select_db( $dbname, $link ))
			$message = true;
		else
			$message = "Could not open the '$dbname' database.";
		
		// check for the existence of preexisting SLAM tables
		$stat = checkForSLAMTables($link, $dbname);
		mysql_close($link);
	}
}

if ($message === true)
{
	if ($stat > 0)
	    print "<span style='color:red'>This database is already connected to a SLAM installation.</span>";
	else
	    print "<span style='color:green'>These settings are OK.</span>";

}
else
	print "<span style='color:red'>$message</span>";
?>