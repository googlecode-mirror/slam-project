<?php

$path = base64_decode($_REQUEST['SLAM_CONF_PATH']);

if ($path != '')
{
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
	$message = "Please specify the absolute path to the SLAM directory.";
	
if ($message === true)
	print "<span style='color:green'>These settings are OK.</span>";
else
	print "<span style='color:red'>$message</span>";
?>