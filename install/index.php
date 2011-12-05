<?php
	require('file_actions.inc.php');
	require('db_actions.inc.php');

	#
	# SLAM installer
	#
	$version = '1.x.x';
	
	$fail = false;
	
	$pathinfo = pathinfo($_SERVER['SCRIPT_FILENAME']);
	$defaults = parse_ini_file('./defaults.ini');
	
	if ($defaults['SLAM_CONF_PATH'] == 'auto')
		$defaults['SLAM_CONF_PATH'] = str_replace('/install','',dirname(realpath($_SERVER['SCRIPT_FILENAME'])));
		
	if ($defaults['SLAM_CONF_HEADER'] == 'auto')
		$defaults['SLAM_CONF_HEADER'] = 'From: SLAM <'.$_SERVER['SERVER_ADMIN'].'>';
	
	if ($defaults['SLAM_FILE_ARCH_DIR'] == 'auto')
		$defaults['SLAM_FILE_ARCH_DIR'] = str_replace('/slam/install','/slam_files',dirname(realpath($_SERVER['SCRIPT_FILENAME'])));
	if ($defaults['SLAM_FILE_TEMP_DIR'] == 'auto')
		$defaults['SLAM_FILE_TEMP_DIR'] = str_replace('/slam/install','/slam_files/temp',dirname(realpath($_SERVER['SCRIPT_FILENAME'])));
	

	if (file_exists('../configurations.ini'))
		$fail = "A configuration.ini file already exists.";
	if(!checkFileList('./file_list.txt'))
		$fail = "Required installation files are missing. Please download the SLAM installer and try again.";
?>
<html>
	<head>
		<title>SLAM installer</title>
		<link type='text/css' href='install.css' rel='stylesheet' />
		<script type='text/javascript' src='check.js'></script>
		<script type='text/javascript' src='base64.js'></script>
	</head>
	<body>
		<div id='installerTitle'><span style='font-family:Impact'>SLAM</span> installer</div>
		<div id='installerVer'>Version: <?php print($version) ?></div>
<?php

	if ($fail !== false)
		print "<div id='fatalFail'>$fail</div>\n";		
?>		
		<form name='config'>

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
				<tr>
					<td class='inputCategory' colspan='2'>Default Categories</td>
				</tr>
				<tr>
					 <td class='categoryInfo' colspan="2">This installer can set up some asset categories for you automatically. Select which categories you would like to set up at this time:</td>
				</tr>
			</table>
			<table id='optionalCategoriesTable'>
				<tr>
					<td class='optionalCategoryBox' style="color: #045FB4;font-weight:bold">&nbsp;</td>
					<td class='optionalCategoryName' style="color: #045FB4;font-weight:bold">Name</td>
					<td class='optionalCategoryPrefix' style="color: #045FB4;font-weight:bold">Prefix</td>
					<td class='optionalCategoryDescription' style="color: #045FB4;font-weight:bold">Description</td>
				</tr>
			
<?php
	$table_names = array_keys($sql_create_optional);

	foreach( $table_names as $name )
	{
		print "<tr>\n";
		print "<td class='optionalCategoryBox'><input type='checkbox' name='OPTIONAL_TABLES[]' value='".base64_encode($name)."' ";
		if ($sql_create_optional[$name]['checked'])
			print "checked='checked' ";
		print "/></td>\n";
		print "<td class='optionalCategoryName'>$name</td>\n";
		print "<td class='optionalCategoryPrefix'><input type='text' size='2' name='OPTIONAL_TABLE_PREFIX[]' value='".$sql_create_optional[$name]['prefix']."'/></td>\n";
		print "<td class='optionalCategoryDescription'>".$sql_create_optional[$name]['description']."</td>\n";
		print "</tr>\n";
	}
?>
			</table>
		</form>
	</body>
</html>