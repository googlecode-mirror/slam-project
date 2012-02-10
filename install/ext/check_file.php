<?php
require('../lib/file_actions.inc.php');


$arch_path = rtrim( base64_decode($_REQUEST['SLAM_FILE_ARCH_DIR']),'/');
$temp_path = rtrim( base64_decode($_REQUEST['SLAM_FILE_TEMP_DIR']),'/');

$msg1 = checkDirectoryIsRW($arch_path);
$msg2 = checkDirectoryIsRW($temp_path);

// ok to try and delete these, because rmdir will fail if there are not empty
@rmdir($temp_path);
@rmdir($arch_path);

if (($msg1 === true) && ($msg2 === true))
	print "<span style='color:green'>These settings are OK.</span>";
else
	print "<span style='color:red'>$msg1<br />$msg2</span>";

?>