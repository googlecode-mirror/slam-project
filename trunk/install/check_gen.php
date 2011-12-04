<?php

$path = base64_decode($_REQUEST['SLAM_CONF_PATH']);

$message = true;

// check and make sure that the lab prefix is kosher
if (strlen($_REQUEST['SLAM_CONF_PREFIX']) != 2)
	$message = 'Please use a lab prefix that is exactly two (2) characters long';
elseif (ereg('[^A-Za-z0-9]', $_REQUEST['SLAM_CONF_PREFIX']))
	$message = 'Please only use alphanumeric (A-Z or 1-2) characters in the lab prefix.';
elseif ($path != '')
{
	// do a bunch of testing to make sure the SLAM path works
	if (file_exists($path))
	{
		if (!is_file($path))
		{
			if (is_readable($path))
				$message = true;
			else
				$message = "The directory '$path' is not readable.";
		}
		else
			$message = "'$path' is not a valid directory.";
	}
	else
		$message = "The path '$path' does not exist. Please create it.";
}
else
	$message = "Please provide the absolute (filesystem) path to the SLAM directory.";
	
if ($message === true)
	print "<span style='color:green'>These settings are OK.</span>";
else
	print "<span style='color:red'>$message</span>";
?>