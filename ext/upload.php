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
		$config->errors[] = 'Invalid identifier provided.';
	elseif(SLAM_checkAssetOwner($config,$db,$user,$category,$identifier) === false)
		$config->errors[] = 'You cannot modify this asset.';
}
else
	$config->errors[] = "Please <a href='{$config->html['url']}'>log in</a>";

if (count($config->errors) == 0)
{
	$cats = array_flip($config->values['lettercodes']);
	$path = "{$config->values['file manager']['archive_dir']}/{$config->values['lab_prefix']}{$cats[$category]}/{$identifier}.zip";

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
				
				if (@move_uploaded_file($temp,$file) !== true)
					$config->errors[] = "File '$name' could not be moved from temporary upload folder to {$config->values['file manager']['temp_dir']}. Perhaps access permissons are incorrect?";
				
				if (($status = SLAM_appendToAssetArchive($path,$file)) !== true)
					$config->errors[] = $status;
				
				/* remove the temporary file now that it's been added to the archive */
				exec("rm ".escapeshellarg($file),$output,$status);
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
	header("refresh:0;url=../ext/?i=$identifier");
else
	header("refresh:{$config->values['file manager']['action_timeout']};url=../ext/?i=$identifier");

echo SLAM_makeFileSplashHTML($identifier,'Attach to asset','Your files are being attached to the asset.',$config->errors);
?>