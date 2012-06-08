<?php

require('../lib/slam_files.inc.php');

$config	= new SLAMconfig();
$db		= new SLAMdb($config);
$user	= new SLAMuser($config,$db);

$slam_file_errors['zip_errors'] = array('No error','No error','Unexpected end of zip file','A generic error in the zipfile format was detected.','zip was unable to allocate itself memory','A severe error in the zipfile format was detected','Entry too large to be split with zipsplit','Invalid comment format','zip -T failed or out of memory','The user aborted zip prematurely','zip encountered an error while using a temp file','Read or seek error','zip has nothing to do','Missing or empty zip file','Error writing to a file','zip was unable to create a file to write to','bad command line parameters','zip could not open a specified file to read'); //exit status descriptions from the zip man page 
$slam_file_errors['unzip_errors'] = array('No error','One or more warning errors were encountered, but processing completed successfully anyway','A generic error in the zipfile format was detected','A severe error in the zipfile format was detected.','unzip was unable to allocate itself memory.','unzip was unable to allocate memory, or encountered an encryption error','unzip was unable to allocate memory during decompression to disk','unzip was unable allocate memory during in-memory decompression','unused','The specified zipfiles were not found','Bad command line parameters','No matching files were found','50'=>'The disk is (or was) full during extraction',51=>'The end of the ZIP archive was encountered prematurely.',80=>'The user aborted unzip prematurely.',81=>'Testing or extraction of one or more files failed due to unsupported compression methods or unsupported decryption.',82=>'No files were found due to bad decryption password(s)');

if( !$user->authenticated )
{
	echo "You are not logged in.\n";
	return;
}

$request	= new SLAMrequest($config,$db,$user);
$result		= new SLAMresult($config,$db,$user,$request);
$category	= array_shift(array_keys($request->categories));
$identifier	= array_shift($request->categories[ $category ]);
$path		= SLAM_getArchivePath($config,$category,$identifier);
$access		= 0;

/* get asset and set the accessibility appropriately */
if( count($result->assets[$category]) == 1 )
{
	$asset = array_shift($result->assets[ $category ]);	
	$access = SLAM_getAssetAccess($user, $asset);		
}
else // possibly a new asset
	$access = 2;

/* if we've encountered any errors at this point, bail */
if( (count($config->errors) == 0) && ($access > 1) )
{
	/* sanitize the path before going any further */
	$path = escapeshellarg($path);

	/* are there files ready to be uploaded? */
	if (isset($_FILES['asset_file']))
	{	
		$i = 0;
		foreach($_FILES['asset_file']['error'] as $error)
		{
			if ($error == UPLOAD_ERR_OK)
			{
				/* move the uploaded file into the temporary directory for incorporation into the archive */
				$name = urldecode($_FILES['asset_file']['name'][$i]);
				$temp = urldecode($_FILES['asset_file']['tmp_name'][$i]);
				$file = "{$config->values['file manager']['temp_dir']}/$name";

				/* note that this function cannot take escaped paths */
				if (@move_uploaded_file($temp,$file) !== true)
					$config->errors[] = "File '$name' could not be moved from temporary upload folder to {$config->values['file manager']['temp_dir']}. Perhaps access permissons are incorrect?";
				
				$file = escapeshellarg( $file );

				if (($status = SLAM_appendToAssetArchive($path,$file)) !== true)
					$config->errors[] = $status;
				
				/* remove the temporary file now that it's been added to the archive */
				exec("rm $file", $output,$status);
			}
			elseif($error != UPLOAD_ERR_NO_FILE)
			{
				/* give the user an informative reason for failure */
				/* see: http://www.php.net/manual/en/features.file-upload.errors.php */
				switch($error)
				{
					case UPLOAD_ERR_INI_SIZE:
						$config->errors[] = "File '{$_FILES['asset_file']['name'][$i]}' exceeded system-defined size.";
						break;
					case UPLOAD_ERR_FORM_SIZE:
						$config->errors[] = "File '{$_FILES['asset_file']['name'][$i]}' exceeded fl_mod-defined size.";
						break;
					case UPLOAD_ERR_PARTIAL:
						$config->errors[] = "File '{$_FILES['asset_file']['name'][$i]}' was only partially uploaded.";
						break;
					case UPLOAD_ERR_NO_TMP_DIR:
						$config->errors[] = "File '{$_FILES['asset_file']['name'][$i]}' was not uploaded due to an error (UPLOAD_ERR_NO_TMP_DIR).";
						break;
					case UPLOAD_ERR_CANT_WRITE:
						$config->errors[] = "File '{$_FILES['asset_file']['name'][$i]}' was not uploaded due to an error (UPLOAD_ERR_CANT_WRITE).";
						break;
					case UPLOAD_ERR_EXTENSION:
						$config->errors[] = "File '{$_FILES['asset_file']['name'][$i]}' was not uploaded due to an error (UPLOAD_ERR_EXTENSION).";
						break;
				}
			}
	
			$i++;
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
		print "<div id='actionSuccessDiv'>The specified files have been attached to the asset.</div>";
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
