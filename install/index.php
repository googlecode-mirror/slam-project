<?php
	require('constants.inc.php');
	require('file_actions.inc.php');
	
	$fail = false;
	$pathinfo = pathinfo($_SERVER['SCRIPT_FILENAME']);
	
	# make sure we're not installing into an existing install
	if (file_exists('../configurations.ini'))
		$fail = "A configuration.ini file already exists.";
	
	# make sure we have all the necessary files
	if(!checkFileList('./file_list.txt'))
		$fail = "Required installation files are missing. Please download the SLAM installer and try again.";
	
	# make sure the install directory is writeable
	if(!is_writable( $pathinfo['dirname'] ))
		$fail = "Installation directory is not writeable. Please contact your system administrator";
	
	if ($fail !== false)
		print "<div id='fatalFail'>$fail</div>\n";		
?>
<html>
	<head>
		<title>SLAM installer - Welcome</title>
		<link type='text/css' href='install.css' rel='stylesheet' />
		<script type='text/javascript' src='../js/detect.js'></script>
	</head>
	<body>
		<div id='installerTitle'><span style='font-family:Impact'>SLAM</span> installer - Welcome</div>
		<div id='installerVer'>Version: <?php print($version) ?></div>
<script>
	var stat = checkBrowser();
	if (stat < 1)
		document.write("<div class='error'>This browser is not supported for use with SLAM.</div>");
	else if (stat < 2)
		document.write("<div class='error'>SLAM requires that browser cookies be enabled.</div>");
</script>
	</body>
</html>