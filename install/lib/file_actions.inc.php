<?php

function checkFileList( $file_list_path )
{
	$lines = file($file_list_path);
	
	if ($lines === false)
	{
		print "<!-- file '$file_list_path' not found -->\n";
		return false;
	}
	
	$ret = true;
	foreach ($lines as $line)
	{
		if ((substr($line, 0, 1) == '.') && (!is_readable( trim($line) )))
		{
			print "<!-- file '".trim($line)."' not found -->\n";
			$ret = false;
		}
	}
	
	return $ret;
}

function checkDirectoryIsRW($path)
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
		return "User provided an empty path.";
	
	return true;
}

?>
