<?php

require('../lib/slam_files.inc.php');

$config	= new SLAMconfig();
$db		= new SLAMdb($config);
$user	= new SLAMuser($config,$db);

$slam_file_errors['zip_errors'] = array('No error','No error','Unexpected end of zip file','A generic error in the zipfile format was detected.','zip was unable to allocate itself memory','A severe error in the zipfile format was detected','Entry too large to be split with zipsplit','Invalid comment format','zip -T failed or out of memory','The user aborted zip prematurely','zip encountered an error while using a temp file','Read or seek error','zip has nothing to do','Missing or empty zip file','Error writing to a file','zip was unable to create a file to write to','bad command line parameters','zip could not open a specified file to read'); //exit status descriptions from the zip man page 
$slam_file_errors['unzip_errors'] = array('No error','One or more warning errors were encountered, but processing completed successfully anyway','A generic error in the zipfile format was detected','A severe error in the zipfile format was detected.','unzip was unable to allocate itself memory.','unzip was unable to allocate memory, or encountered an encryption error','unzip was unable to allocate memory during decompression to disk','unzip was unable allocate memory during in-memory decompression','unused','The specified zipfiles were not found','Bad command line parameters','No matching files were found','50'=>'The disk is (or was) full during extraction',51=>'The end of the ZIP archive was encountered prematurely.',80=>'The user aborted unzip prematurely.',81=>'Testing or extraction of one or more files failed due to unsupported compression methods or unsupported decryption.',82=>'No files were found due to bad decryption password(s)');

if ($user->authenticated)
{
	$request = new SLAMrequest($config,$db,$user);
	$category = @array_shift(@array_keys($request->categories));
	$identifier = @array_shift($request->categories[$category]);

	if (empty($identifier))
		$config->errors[] = 'Invalid asset identifier provided.';
	elseif(SLAM_checkAssetOwner($config,$db,$user,$category,$identifier) === false)
		$config->errors[] = 'You cannot modify this asset.';
	elseif(($path = SLAM_getArchivePath(&$config,$category,$identifier)) === false)
		$config->errors[] = 'There are no files attached to this asset.';
}
else
	$config->errors[] = "Please <a href='{$config->html['url']}'>log in</a>";

if (count($config->errors) == 0)
{
	/* remove the specified file from the archive */
	$path = escapeshellarg($path);
			
	/* go through the arguments and look for files for deletion */
	foreach($_REQUEST as $name => $value)
	{
		if (substr($name,0,7) == 'FL_del-')
		{
			/* extract the name of the file to be deleted */
			$file = base64_decode(substr($name,7));

			$file = escapeshellarg($file);
			exec("zip $path -d $file",$output,$status);
			
			if ($status > 0)
			{
				$config->errors[] = "There was a zip error: {$slam_file_errors['zip_errors'][$status]}";
				break;
			}
		}
	}
}

/* immediately redirect on success, or give the user a chance to see any errors we've run into */
if (count($config->errors) == 0)
	header("refresh:0;url=../ext/files.php?i=$identifier");
else
	header("refresh:{$config->values['file manager']['action_timeout']};url=../ext/files.php?i=$identifier");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0//EN">
<html>
	<head>
		<link type='text/css' href='../css/files.css' rel='stylesheet' />
	</head>
	<body>
<?php
	if (count($config->errors) == 0)
		print "<div id='actionSuccessDiv'>The specified files have been deleted from the asset.</div>";
	elseif(!empty($config->values['debug']))
	{
		print "<div id='actionErrorDiv'>";
		foreach($config->errors as $error)
			print "$error<br />\n";
		print "</div>";
	}

	print "<div id='actionContinueDiv'>Please <a href='../ext/files.php?i=$identifier'>click here</a> to continue.</div>";
?>
	</body>
</html>
