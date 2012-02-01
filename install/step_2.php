<?php
	require('constants.inc.php');
	require('actions.inc.php');
	require('db_schemas.inc.php');
	
	$fail = false;
	
	$pathinfo = pathinfo($_SERVER['SCRIPT_FILENAME']);
	
	# Read the default settings either from the previously-entered options, or from the default file
	if (file_exists('./step_1.ini'))
		$defaults = parse_ini_file('./step_2.ini',true);
	else
		$defaults = parse_ini_file('./defaults.ini',true);
	
	# save the previous page settings
	if ($_REQUEST['STEP'] == 1)
		if( ($ret = write_SLAM_options( './step_1.ini' )) != true )
			$fail = "Could not save your progress. Please contact your system administrator: $ret";
?>
<html>
	<head>
		<title>SLAM installer - Step 2</title>
		<link type='text/css' href='install.css' rel='stylesheet' />
		<script type='text/javascript' src='check.js'></script>
		<script type='text/javascript' src='../js/base64.js'></script>
	</head>
	<body>
		<div id='installerTitle'><span style='font-family:Impact'>SLAM</span> installer - Step 2</div>
		<div id='installerVer'>Version: <?php print($version) ?></div>
<?php

	if ($fail !== false)
		print "<div id='fatalFail'>$fail</div>\n";		
?>		
		<form name='config' action='step_3.php">
			<input type='hidden' name='STEP' value='2' />
			<table id='configTable'>
				<tr>
					<td class='inputCategory' colspan='2'>Default Categories</td>
				</tr>
				<tr>
					 <td class='categoryInfo' colspan="2">The installer can set up some asset categories for you automatically. Select which categories you would like to set up at this time:</td>
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
		if (in_array($name,$defaults['tables']['install']))
			print "checked='checked' ";
		print "/></td>\n";
		print "<td class='optionalCategoryName'>$name</td>\n";
		print "<td class='optionalCategoryPrefix'><input type='text' size='2' name='OPTIONAL_TABLE_PREFIX[]' value='".$sql_create_optional[$name]['prefix']."'/></td>\n";
		print "<td class='optionalCategoryDescription'>".$sql_create_optional[$name]['description']."</td>\n";
		print "</tr>\n";
	}
?>
			</table>
			<input type="submit" />
		</form>
	</body>
</html>