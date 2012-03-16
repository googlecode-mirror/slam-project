<?php
require('../lib/file_actions.inc.php');

$arch_path = rtrim( ($_REQUEST['SLAM_FILE_ARCH_DIR']),'/');
$temp_path = rtrim( ($_REQUEST['SLAM_FILE_TEMP_DIR']),'/');

$ret = checkFileOptions( $arch_path, $temp_path );

if ($ret === true)
	print "<span style='color:green'>These settings are OK.</span>";
else
	print "<span style='color:red'>{$ret[0]}<br />{$ret[1]}</span>";

?>