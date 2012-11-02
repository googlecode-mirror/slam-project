<?php

require('../lib/slam_files.inc.php');

$config	= new SLAMconfig();
$db		= new SLAMdb($config);
$user	= new SLAMuser($config,$db);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0//EN">
<html>
	<head>
		<title>SLAM File Manager</title>
		<link type='text/css' href='../css/files.css' rel='stylesheet' />
		<script type='text/javascript' src='../js/files.js'></script>
	</head>
	<body>
		<div id='fileListContainer'>
<?php

if( !$user->authenticated )
{
	echo "<div id='fileEmpty'>You are not logged in</div>\n";
	echo "</div>\n</body>\n</html>";
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
else /* possibly a new asset */
{
	$config->errors[] = 'Invalid identifier provided.';
	$access = 2;
}

/* is the current user qualified to make changes to the archive? */
if( $access > 0)
{
	/* attempt to retrieve the asset's archive path*/
	if(($path = SLAM_getArchivePath($config,$category,$identifier)) !== false)
	{
		/* retrieve info on the files in the archive */
		if(($files = SLAM_getArchiveFiles($config,$path)) !== false)
		{
			echo "<div id='assetTitle'>$identifier</div>\n";
			echo "<div id='fileListScrollbox'>\n";
			echo "<form name='assetFileRemove' id='assetFileRemove' method='POST' action='delete.php'>\n";
			echo "<input type='hidden' name='i' value='$identifier' />\n";
			echo SLAM_makeArchiveFilesHTML($config,$db,$category,$identifier,$files,($access>1));
			echo "</div>\n";
			if ($access>1)
				echo "<input type='button' id='deleteButton' value='Delete' onClick=\"delete_submit('assetFileRemove')\" />\n";
			echo "</form>\n";
			echo "</div>\n";
		}
		else
			echo "<div id='fileEmpty'>No files to show</div>\n";
	}
	else
		echo "<div id='fileEmpty'>No files to show</div>\n";
}
else
	echo "<div id='fileEmpty'>You do not have access to this asset's files.</div>\n";

if(!empty($config->values['debug']))
{
	echo "<div class='error'>\n";
	for($i=0; $i<count($config->errors); $i++)
		echo "$i) {$config->errors[$i]}<br />\n";		
	echo "</div>\n";
}

if ($access > 1)
	SLAM_updateArchiveFileList($config,$db,$category,$identifier);
else
{
	echo "</div>\n</body>\n</html>\n";
	exit;
}

?>
</form>
		</div>
		<div id='fileUploadContainer'>
			<form name="assetFileUpload" id="assetFileUpload" method="POST" action="upload.php" enctype="multipart/form-data">
				<input type="hidden" name="i" value="<?php echo($identifier); ?>" />
				<table id='fileUploadTable'>
					<tr>
						<td style='width:250px;font-family:monospace;text-align: center'>
							<input name="asset_file[]" type="file">
							<input name="asset_file[]" type="file">
							<input name="asset_file[]" type="file">
							<input name="asset_file[]" type="file">
						</td>
						<td style='width:250px;text-align: center; border-left:1px solid gray'>
							<input type="button" name="action" value="Attach Files" class='actionButton' onClick="fileUploadSubmit('assetFileUpload','asset_file[]')" />
							<input type="Reset" value="Clear Form" class='actionButton' />
						</td>
					</tr>
				</table>
			</form>
		</div>
	</body>
</html>
