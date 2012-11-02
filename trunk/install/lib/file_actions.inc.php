<?php

function checkFileOptions($arch_path, $temp_path)
{
	$fail = array();
	$fail[] = checkDirectoryIsRW($arch_path);
	$fail[] = checkDirectoryIsRW($temp_path);

	// ok to try and delete these, because rmdir will fail if there are not empty
	@rmdir($temp_path);
	@rmdir($arch_path);
	
	if( ($fail[0] === true) && ($fail[1] === true) )
		return true;
	
	return $fail;
}

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

function checkExecCommand($cmd)
{
	if(function_exists('exec'))
	{
		$cmd = escapeshellcmd($cmd);
	    exec("command -v $string >& /dev/null && echo 'Found' || echo 'Not Found'", $output);

	    if( $output[0] == "Found" )
	        return true;
	    else
	        return false;
	}
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
