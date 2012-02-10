<?php
	require('lib/constants.inc.php');
	require('lib/actions.inc.php');

	$fail = array();
	
	# save the previous page settings
	if ($_REQUEST['STEP'] == 3)
		if( write_SLAM_options( './step_3.ini' ) === false )
			$fail[] = "Could not save your progress. Please contact your system administrator: $ret";
		
	# create the installation config.ini
	if( count($fail) == 0 )
		if( ($ret = write_SLAM_config()) != true )
			$fail[] = array_merge( $fail, $ret );
?>
<html>
	<head>
		<title>SLAM installer - Finished</title>
		<link type='text/css' href='css/install.css' rel='stylesheet' />
	</head>
	<body><div id='container'>
		<div id='installerTitle'><span style='font-family:Impact'>SLAM</span> installer - Finished</div>
		<div id='installerVer'>Version: <?php print($slam_version) ?></div>
<?php
	foreach( $fail as $text )
		print "<div class='fatalFail'>$text</div>\n";	
?>		
	</div></body>
</html>