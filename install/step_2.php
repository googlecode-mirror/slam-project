<?php
	require('lib/constants.inc.php');
	require('lib/actions.inc.php');
	require('lib/db_schemas.inc.php');
	
	$fail = array();
	
	# save the previous page settings
	if ($_REQUEST['STEP'] == 1)
		if( ($ret = write_SLAM_options( './step_1.ini' )) != true )
			$fail[] = "Could not save your progress. Please contact your system administrator: $ret";
	
	# Read the default settings either from the previously-entered options, or from the default file
	if (file_exists('./step_2.ini'))
		$defaults = parse_ini_file('./step_2.ini');
	else
		$defaults = parse_ini_file('./defaults.ini');
?>
<html>
	<head>
		<title>SLAM installer - Step 2/4</title>
		<link type='text/css' href='css/install.css' rel='stylesheet' />
		<script type='text/javascript' src='js/check.js'></script>
		<script type='text/javascript' src='js/validate.js'></script>
	</head>
	<body><div id='container'>
		<div id='installerTitle'><span style='font-family:Impact'>SLAM</span> installer - Step 2/4</div>
		<div id='installerVer'>Version: <?php print($slam_version) ?></div>
<?php
	foreach( $fail as $text )
		print "<div class='fatalFail'>$text</div>\n";		
?>		
		<form name='foward' action='step_3.php' method='post'>
			<input type='hidden' name='STEP' value='2' />
			<table id='configTable'>
				<tr>
					 <td class='helpHeader' colspan="2">For assistance, please refer to the SLAM documentation [<a href='http://steelsnowflake.com/SLAM' target='_new'>here</a>].</td>
				</tr>
				<tr>
					<td class='inputCategory' colspan='2'>Default Categories</td>
				</tr>
				<tr>
					 <td class='categoryInfo' colspan="2">The installer can set up some asset categories for you automatically. Select which categories you would like to install at this time:</td>
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

	$i = 0;
	foreach( $table_names as $name )
	{	
		if( $defaults['SLAM_OPTIONAL_PREFIX'][ $i ] != '' )
			$prefix = $defaults['SLAM_OPTIONAL_PREFIX'][ $i ];
		else
			$prefix = $sql_create_optional[$name]['prefix'];
			
		print "<tr>\n";
		print "<td class='optionalCategoryBox'>\n";
		print "<input type='hidden' name='SLAM_OPTIONAL_TABLE[]' value='".base64_encode($name)."' />\n";
		print "<input type='checkbox' name='SLAM_OPTIONAL_INSTALL[]' value='$i' ";
		if ( in_array($i, $defaults['SLAM_OPTIONAL_INSTALL']) )
			print "checked='checked' ";
		print "/></td>\n";
		print "<td class='optionalCategoryName'>$name</td>\n";
		print "<td class='optionalCategoryPrefix'>";
		print "<input type='text' size='2' name='SLAM_OPTIONAL_PREFIX[]' id='SLAM_OPTIONAL_PREFIX_{$i}' value='$prefix' onkeyup=\"validatePos( this,'[a-zA-Z]')\" />";
		print "</td>\n";
		print "<td class='optionalCategoryDescription'>".$sql_create_optional[$name]['description']."</td>\n";
		print "</tr>\n";
		$i++;
	}
?>
			</table>
			<div class='actionButtons'>
				<input type='submit' class='submitButton' value='Save these settings and Continue' />
			</div>
		</form>
		<form name='back' action='step_1.php' method='post'>
			<div class='actionButtons'>
				<input type='submit' class="submitButton" value='Cancel these settings and Go Back' />
			</div>
		</form>
	</div></body>
</html>