<?php
	require('lib/constants.inc.php');
	require('lib/actions.inc.php');

	$fail = array();
	
	# save the previous page settings
	if ($_REQUEST['STEP'] == 4)
		if( write_SLAM_options( './step_4.ini' ) === false )
			$fail[] = "Could not save your progress. Please contact your system administrator: $ret";
?>
<html>
	<head>
		<title>SLAM installer - Confirm</title>
		<link type='text/css' href='css/install.css' rel='stylesheet' />
	</head>
	<body><div id='container'>
		<div id='installerTitle'><span style='font-family:Impact'>SLAM</span> installer - Confirm</div>
		<div id='installerVer'>Version: <?php print($slam_version) ?></div>
<?php
	foreach( $fail as $text )
		print "<div class='fatalFail'>$text</div>\n";	
?>		
	</div></body>
</html>