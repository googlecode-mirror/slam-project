<?php
	require('lib/constants.inc.php');
	require('lib/db_schemas.inc.php');
	
	require('lib/db_actions.inc.php');
	require('lib/file_actions.inc.php');
	require('lib/actions.inc.php');

	$fail = write_SLAM_config();
?>
<html>
	<head>
		<title>SLAM installer - Finished</title>
		<link type='text/css' href='css/install.css' rel='stylesheet' />
	</head>
	<body><div id='container'>
		<div id='installerTitle'><span style='font-family:Impact'>SLAM</span> installer - Completed</div>
		<div id='installerVer'>Version: <?php print($slam_version) ?></div>
<?php
	if( is_array($fail) )
		foreach( $fail as $text )
			print "<div class='fatalFail'>$text</div>\n";
	else
		print "<div id='success'>Congratulations! SLAM has been successfully set up.<br />You can access the installation [<a href='../index.php'>here</a>].</div>\n";
?>		
	</div></body>
</html>