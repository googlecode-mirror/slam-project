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


?>
