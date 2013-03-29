<?php

$slam_file_errors['zip_errors'] = array('No error','No error','Unexpected end of zip file','A generic error in the zipfile format was detected.','zip was unable to allocate itself memory','A severe error in the zipfile format was detected','Entry too large to be split with zipsplit','Invalid comment format','zip -T failed or out of memory','The user aborted zip prematurely','zip encountered an error while using a temp file','Read or seek error','zip has nothing to do','Missing or empty zip file','Error writing to a file','zip was unable to create a file to write to','bad command line parameters','zip could not open a specified file to read'); //exit status descriptions from the zip man page 
$slam_file_errors['unzip_errors'] = array('No error','One or more warnings were encountered, but processing completed successfully anyway','A generic error in the zipfile format was detected','A severe error in the zipfile format was detected.','unzip was unable to allocate itself memory.','unzip was unable to allocate memory, or encountered an encryption error','unzip was unable to allocate memory during decompression to disk','unzip was unable allocate memory during in-memory decompression','unused','The specified zipfiles were not found','Bad command line parameters','No matching files were found','50'=>'The disk is (or was) full during extraction',51=>'The end of the ZIP archive was encountered prematurely.',80=>'The user aborted unzip prematurely.',81=>'Testing or extraction of one or more files failed due to unsupported compression methods or unsupported decryption.',82=>'No files were found due to bad decryption password(s)');

function SLAM_getArchivePath(&$config,$category,$identifier)
{	
	/*
		This function attempts to locate the archive file for the requested record, and returns a file path on success
	
		It returns the following status values:
			false - couldn't find an archive associated with that asset (for whatever reason)
			string (path) - asset exists
	*/
		
	$cats = array_flip($config->values['lettercodes']);
	$path = "{$config->values['file manager']['archive_dir']}/{$config->values['lab_prefix']}{$cats[$category]}/{$identifier}.zip";	
		
	if (file_exists($path) && (!is_readable($path)))
		$config->errors[]='File manager error: Asset file exists, but is not readable. (Permissions error?)';
	elseif (!file_exists($path))
	{
		if (is_writable(dirname($path)))
			return $path;
		elseif(mkdir(dirname($path)))
			return $path;
		else
			$config->errors[]="File manager error: Asset file path '$path' does not exist, and cannot be created.";
	}
	elseif (is_readable($path))
		return $path;
	
	return false;
}

function SLAM_getArchiveFiles(&$config,$path)
{
	global $slam_file_errors;
	
	if (!is_readable($path))
	{
		$config->errors[] = "File manager error: Cannot read the archive at ".escapeshellarg($path).".";
		return false;
	}

	/* get the list of files in the archive */
	$path = escapeshellarg($path);
		
	exec("unzip -l $path",$output,$status);

	if ($status == 9) /* there is/are file(s) attached, but they are not zip archives, or it is an empty zip archive */
	{
		$config->errors[] = 'File manager error: There is an archive associated with this asset, but it is not readable by SLAM.';
		return false;
	}

	if ($status > 0) /* see unzip man page for details */
	{
		if( key_exists($status, $slam_file_errors['unzip_errors']) )
			$config->errors[] = "File manager error: Unzip error: {$slam_file_errors['unzip_errors'][$status]}";
		else
			$config->errors[] = sprintf("File manager error: Attempting to call zip returned nonzero status %i : %s",$status,$output[0]);
		return false;
	}
	
	$files = array();
	$f_text	= array_slice($output,3,-2);
	foreach($f_text as $info)
	{
		if(preg_match("/^\s*(\d+)\s+(\S+)\s+(\S+)\s+(.+)/",$info,$a))
		{
			$files[$a[4]]['size']=$a[1];
			$files[$a[4]]['date']=$a[2];
			$files[$a[4]]['time']=$a[3];
			$files[$a[4]]['name']=$a[4];
		}
	}
	
	return $files;
}

function SLAM_updateArchiveFileList(&$config,$db,$category,$identifier)
{
	$path	= SLAM_getArchivePath($config,$category,$identifier);
	$files	= SLAM_getArchiveFiles($config,$path);
	
	if (empty($files))
		return;
		
	/* slam together all the files for the records, separated by newlines */
	$s = sql_real_escape(implode("\n",array_keys($files)),$db->link);

	$q = "UPDATE `$category` SET `Files`='$s' WHERE (`Identifier`='$identifier') LIMIT 1";
	if (($result = $db->Query($q)) === false)
		$config->errors[]='Database error: Could not update asset file list:'.$db->ErrorState();
		
	return;
}

function SLAM_appendToAssetArchive($path,$file)
{
	global $slam_file_errors;
		
	/*
	Appends a file to the current record archive.
	
	Upon success, return boolean true
	Upon error, returns a text string describing the error.
	*/
	
	/* attempt to append uploaded file to zip archive */
	exec("zip -j $path $file",$output,$status);

	if ($status > 0)
	{
		if( key_exists($status, $slam_file_errors['zip_errors']) )
			$config->errors[] = "File manager error: Zip error: {$slam_file_errors['zip_errors'][$status]}";
		else
			$config->errors[] = sprintf("File manager error: Attempting to call zip returned nonzero status %i : %s",$status,$output[0]);
		break;
	}
				
	return true;
}

function SLAM_deleteAssetFiles(&$config,$category,$identifier)
{
	/*
		deletes the file archive of an asset
	*/
	
	if(($path = SLAM_getArchivePath(&$config,$category,$identifier)) !== false)
		if(!unlink($path))
			$config->errors[]='File manager error: Could not delete asset archive.';
	
	return;
}

function SLAM_guessMimeType($filename)
{
	/* guesses a file mime type from the extension */
	$pathinfo = pathinfo($filename);
	
	switch($pathinfo['extension'])
	{
		case 'ai':
		case 'ps':
		case 'eps':
			return 'application/postscript';
		case 'avi':
			return 'application/x-msvideo';
		case 'bin':
			return 'application/binary';
		case 'bmp':
			return 'image/bmp';
		case 'doc':
		case 'docx':
			return 'application/msword';
		case 'pdf':
			return 'application/pdf';
		case 'pdb':
			return 'chemical/x-pdb';
		case 'pps':
		case 'ppt':
		case 'pptx':
			return 'application/mspowerpoint';
		case 'txt':
		case 'text':
			return 'text/plain';
		case 'rtf':
			return	'text/richtext';
		case 'jpeg':
		case 'jpg':
			return 'image/jpeg';
		case 'png':
			return 'image/png';
		case 'gif':
			return 'image/gif';
		case 'tif':
		case 'tiff':
			return 'image/tiff';
		case 'exe':
			return 'application/octet-stream';
		case 'htm':
		case 'html':
			return 'text/html';
		case 'zip':
			return 'application/zip';
		case 'tar':
		case 'gtar':
			return 'application/x-tar';
		case 'xls':
		case 'xlsx':
			return 'application/x-excel';
		case 'Z':
		case 'tZ':
		case 'tGZ':
		case 'gz':
		case 'gzip':
			return 'application/x-gzip';
	}
}

?>
