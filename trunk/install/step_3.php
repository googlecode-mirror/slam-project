<?php
	require('constants.inc.php');
	
	$fail = false;
	
	$pathinfo = pathinfo($_SERVER['SCRIPT_FILENAME']);
	
	# Read the default settings either from the previously-entered options, or from the default file
	if (file_exists('./step_1.ini'))
		$defaults = parse_ini_file('./step_3.ini',true);
	else
		$defaults = parse_ini_file('./defaults.ini',true);
	
	# save the previous page settings
	if ($_REQUEST['STEP'] == 2)
		if( ($ret = write_SLAM_options( './step_2.ini' )) != true )
			$fail = "Could not save your progress. Please contact your system administrator: $ret"
?>
<html>
	<head>
		<title>SLAM installer - Step 3</title>
		<link type='text/css' href='install.css' rel='stylesheet' />
		<script type='text/javascript' src='check.js'></script>
		<script type='text/javascript' src='../js/convert.js'></script>
	</head>
	<body>
		<div id='installerTitle'><span style='font-family:Impact'>SLAM</span> installer - Step 3</div>
		<div id='installerVer'>Version: <?php print($version) ?></div>
<?php

	if ($fail !== false)
		print "<div id='fatalFail'>$fail</div>\n";		
?>		
		<form name='config' action='complete.php'>
			<input type='hidden' name='STEP' value='3' />
			<table id='configTable'>
				<tr>
					<td class='inputCategory' colspan='2'>Administrator setup</td>
				</tr>
				<tr>
					 <td class='categoryInfo' colspan="2">A "superuser" account that can act as an administrator is required. This user can modify or remove any user's assets.</td>
				</tr>
				<tr>
					<td class='inputField'>Superuser name:</td>
					<td class='inputValue'><input type='text' value='<?php print $defaults['SLAM_ROOT_NAME'] ?>' size='20' id='SLAM_ROOT_NAME' name='SLAM_ROOT_NAME' /></td>
					
				</tr>
				<tr>
					<td class='inputField'>Superuser password:</td>
					<td class='inputValue'><input type='password' value='<?php print $defaults['SLAM_ROOT_PASS'] ?>' size='20' id='SLAM_ROOT_PASS' name='SLAM_ROOT_PASS' /></td>
				</tr>
				<tr>
					<td class='inputCategory' colspan='2'>User setup</td>
				</tr>
				<tr>
					 <td class='categoryInfo' colspan="2">You can now set up additional regular user accounts. (More can always be added later):</td>
				</tr>

			</table>
			<input type="submit" />
		</form>
	</body>
</html>