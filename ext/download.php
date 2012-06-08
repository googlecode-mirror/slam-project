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
if( (count($config->errors) == 0) && ($access > 0) )
{
	if (empty($_REQUEST['asset_file']))
		die('No file specified.');
	elseif(($path = SLAM_getArchivePath(&$config,$category,$identifier)) === false)
		die('There are no files attached to this asset.');
	
	$file = base64_decode($_REQUEST['asset_file']);
	$a = pathinfo($file);

	/* attempt to guess the file's mime type to send */
	if ($config->values['file manager']['send_mime_type'])
		header('Content-type: '.SLAM_guessMimeType($file));

	/* send the appropriate headers */		
	if ((in_array($a['extension'],$config->values['file manager']['inline_formats']))&&($_REQUEST['inline']))
		header("Content-Disposition: inline; filename=$file");
	else
		header("Content-Disposition: attachment; filename=$file");
	
	/* extract the requested file from the zip archive */
	$path = addslashes($path);
	$status = passthru("unzip -p '$path' '$file'");
	
	if ($status > 1) /* see unzip manual for details */
		die("An unzip error was encountered: {$slam_file_errors['unzip_errors'][$status]}");
}

?>