<?php
	require('constants.inc.php');
	require('actions.inc.php');

	$fail = false;
	
	# save the previous page settings
	if ($_REQUEST['STEP'] == 3)
		if( write_SLAM_options( './step_3.ini' ) === false )
			$fail = "Could not save your progress. Please contact your system administrator: $ret";
		
	# create the installation config.ini
	if ($fail == false)
		if( ($ret = write_SLAM_options()) != true )
			$fail = "Could not save your progress. Please contact your system administrator: $ret";
?>
<html>
	<head>
		<title>SLAM installer - Finished</title>
		<link type='text/css' href='install.css' rel='stylesheet' />
	</head>
	<body>
		<div id='installerTitle'><span style='font-family:Impact'>SLAM</span> installer - Finished</div>
		<div id='installerVer'>Version: <?php print($version) ?></div>
<?php

	if ($fail !== false)
		print "<div id='fatalFail'>$fail</div>\n";		
?>		
	</body>
</html>