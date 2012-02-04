<?php
	require('constants.inc.php');
	require('actions.inc.php');
	
	$fail = false;
	
	$pathinfo = pathinfo($_SERVER['SCRIPT_FILENAME']);
	
	# Read the default settings either from the previously-entered options, or from the default file
	if (file_exists('./step_1.ini'))
		$defaults = parse_ini_file('./step_1.ini',true);
	else
		$defaults = parse_ini_file('./defaults.ini',true);
	
	if ($defaults['SLAM_CONF_PATH'] == 'auto')
		$defaults['SLAM_CONF_PATH'] = str_replace('/install','',dirname(realpath($_SERVER['SCRIPT_FILENAME'])));
		
	if ($defaults['SLAM_CONF_HEADER'] == 'auto')
		$defaults['SLAM_CONF_HEADER'] = 'From: SLAM <'.$_SERVER['SERVER_ADMIN'].'>';
	
	if ($defaults['SLAM_FILE_ARCH_DIR'] == 'auto')
		$defaults['SLAM_FILE_ARCH_DIR'] = str_replace('/slam/install','/slam_files',dirname(realpath($_SERVER['SCRIPT_FILENAME'])));
	if ($defaults['SLAM_FILE_TEMP_DIR'] == 'auto')
		$defaults['SLAM_FILE_TEMP_DIR'] = str_replace('/slam/install','/slam_files/temp',dirname(realpath($_SERVER['SCRIPT_FILENAME'])));
?>
<html>
	<head>
		<title>SLAM installer - Step 1</title>
		<link type='text/css' href='install.css' rel='stylesheet' />
		<script type='text/javascript' src='check.js'></script>
		<script type='text/javascript' src='../js/convert.js'></script>
	</head>
	<body>
		<div id='installerTitle'><span style='font-family:Impact'>SLAM</span> installer - Step 1</div>
		<div id='installerVer'>Version: <?php print($version) ?></div>
<?php

	if ($fail !== false)
		print "<div id='fatalFail'>$fail</div>\n";		
?>		
		<form name='config' action='step_2.php'>
			<input type='hidden' name='STEP' value='1' />
			<table id='configTable'>
				<tr>
					<td class='inputCategory' colspan='2'>General Settings</td>
				</tr>
				<tr>
					<td class='inputField'>Installation path:</td>
					<td class='inputValue'><input type='text' value='<?php print $defaults['SLAM_CONF_PATH'] ?>' size='50' id='SLAM_CONF_PATH' name='SLAM_CONF_PATH' /></td>
					
				</tr>
				<tr>
					<td class='inputField'>Lab name:</td>
					<td class='inputValue'><input type='text' value='<?php print $defaults['SLAM_CONF_NAME'] ?>' size='20' id='SLAM_CONF_NAME' name='SLAM_CONF_NAME' /></td>
				</tr>
				<tr>
					<td class='inputField'>Lab prefix:</td>
					<td class='inputValue'><input type='text' value='<?php print $defaults['SLAM_CONF_PREFIX'] ?>' size='2' maxlength='2' id='SLAM_CONF_PREFIX' name='SLAM_CONF_PREFIX' /></td>
				</tr>
				<tr>
					<td class='inputField'>Mail header:</td>
					<td class='inputValue'><input type='text' value='<?php print $defaults['SLAM_CONF_HEADER'] ?>' size='50' id='SLAM_CONF_HEADER' name='SLAM_CONF_HEADER' /></td>
				</tr>
				<tr>
					<td class='checkCategory' colspan='2'><input type='button' value='Check these values' onClick='checkGeneralForm()'/></td>
				</tr>
				<tr>
					<td class='inputCategory' colspan='2'>Database Settings</td>
				</tr>
				<tr>
					<td class='inputField'>Server:</td>
					<td class='inputValue'><input type='text' value='<?php print $defaults['SLAM_DB_HOST'] ?>' size='20' id='SLAM_DB_HOST' name='SLAM_DB_HOST' /></td>
				</tr>
				<tr>
					<td class='inputField'>Database name:</td>
					<td class='inputValue'><input type='text' value='<?php print $defaults['SLAM_DB_NAME'] ?>' size='20' id='SLAM_DB_NAME' name='SLAM_DB_NAME' /></td>
				</tr>
				<tr>
					<td class='inputField'>Login name:</td>
					<td class='inputValue'><input type='text' value='<?php print $defaults['SLAM_DB_USER'] ?>' size='20' id='SLAM_DB_USER' name='SLAM_DB_USER' /></td>
				<tr>
				</tr>
					<td class='inputField'>Login password:</td>
					<td class='inputValue'><input type='password' value='<?php print $defaults['SLAM_DB_PASS'] ?>' size='20' id='SLAM_DB_PASS' name='SLAM_DB_PASS' /></td>
				</tr>
				<tr>
					<td class='checkCategory' colspan='2'><input type='button' value='Check these values' onClick='checkDatabaseForm()'/></td>
				</tr>
				<tr>
					<td class='inputCategory' colspan='2'>Attached File Settings</td>
				</tr>
				</tr>
					<td class='inputField'>Attachment directory:</td>
					<td class='inputValue'><input type='text' value='<?php print $defaults['SLAM_FILE_ARCH_DIR'] ?>' size='50' id='SLAM_FILE_ARCH_DIR' name='SLAM_FILE_ARCH_DIR' /></td>
				</tr>
				</tr>
					<td class='inputField'>Temporary directory:</td>
					<td class='inputValue'><input type='text' value='<?php print $defaults['SLAM_FILE_TEMP_DIR'] ?>' size='50' id='SLAM_FILE_TEMP_DIR' name='SLAM_FILE_TEMP_DIR' /></td>
				</tr>
				<tr>
					<td class='checkCategory' colspan='2'><input type='button' value='Check these values' onClick='checkFilesForm()' /></td>
				</tr>
			</table>
			<input type="submit" />
		</form>
	</body>
</html>