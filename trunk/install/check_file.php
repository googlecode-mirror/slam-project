<?php

$arch_path = urldecode($_REQUEST['SLAM_FILE_ARCH_DIR']);
$temp_path = urldecode($_REQUEST['SLAM_FILE_TEMP_DIR']);

$msg1 = checkPathSuitability($arch_path);
$msg2 = checkPathSuitability($temp_path);

// ok to try and delete these, because rmdir will fail if there are not empty
@rmdir($temp_path);
@rmdir($arch_path);

if (($msg1 === true) && ($msg2 === true))
	print "<span style='color:green'>These settings are OK.</span>";
else
	print "<span style='color:red'>$msg1<br />$msg2</span>";

function checkPathSuitability($path)
{
	if ($path != '')
	{
		if (file_exists($path))
		{
			if (is_dir($path))
			{
				if (is_readable($path))
				{
					if (!is_writeable($path))
						return "The directory '$path' is not writeable.";
				}
				else
					return "The directory '$path' is not readable.";
			}
			else
				return "'$path' is not a valid directory.";
		}
		elseif (!mkdir($path))
			return "The path '$path' does not exist and cannot be created.";
	}
	else
		return "Please specify a path in which to save attached files.";
	
	return true;
}

?>