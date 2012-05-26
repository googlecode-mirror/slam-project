<?php
	require('lib/constants.inc.php');
	require('lib/actions.inc.php');
	
	$fail = array();
	
	# save the previous page settings
	if ($_REQUEST['STEP'] == 2)
		if( ($ret = write_SLAM_options( './step_2.ini' )) != true )
			$fail[] = "Could not save your progress. Please contact your system administrator: $ret";
	
	# Read the default settings either from the previously-entered options, or from the default file
	if (file_exists('./step_3.ini'))
		$defaults = parse_ini_file('./step_3.ini');
	else
		$defaults = parse_ini_file('./defaults.ini');
?>
<html>
	<head>
		<title>SLAM installer - Step 3/4</title>
		<link type='text/css' href='css/install.css' rel='stylesheet' />
		<script type='text/javascript' src='js/check.js'></script>
		<script type='text/javascript' src='js/clone.js'></script>
		<script type='text/javascript' src='js/validate.js'></script>
	</head>
	<body><div id='container'>
		<script type='text/javascript'>
			document.cloneTRcounter=<?php echo count($defaults['SLAM_PROJECT_NAME']) ?>;
		</script>
		<div id='installerTitle'><span style='font-family:Impact'>SLAM</span> installer - Step 3/4</div>
		<div id='installerVer'>Version: <?php print($slam_version) ?></div>
<?php
	foreach( $fail as $text )
		print "<div class='fatalFail'>$text</div>\n";
?>		
		<form name='forward' action='step_4.php' method='post'>
			<input type='hidden' name='STEP' value='3' />
			<table id='configTable'>
				<tr>
					 <td class='helpHeader' colspan="2">For assistance, please refer to the SLAM documentation [<a href='http://steelsnowflake.com/SLAM' target='_new'>here</a>].</td>
				</tr>
				<tr>
					<td class='inputCategory' colspan='2'>Project Setup</td>
				</tr>
				<tr>
					 <td class='categoryInfo' colspan="2">Projects are a convenient way to segregate assets. Users can make their own projects, choose from a pre-set list, or both. Projects can also be used to share editing capabilities of assets with multiple users.</td>
				</tr>
				<tr>
					<td class='inputField'><input type='checkbox' id='SLAM_CUSTOM_PROJECT' name='SLAM_CUSTOM_PROJECT' value='true' <?php if( $defaults['SLAM_CUSTOM_PROJECT'] == 'true' ){ echo "checked='checked'"; } ?> /></td>
					<td class='inputValue'>Allow users to create their own projects.</td>
					
				</tr>
				<tr>
					 <td class='categoryInfo' colspan="2"><br />You can set up some default projects here. A one-word descriptor is best, like "DksA" or "Hsp_40".</td>
				</tr>
				<tr>
					<td class='inputField'></td>
					<td class='inputValue'><input type='button' value="add project" onClick="cloneLastTR('configTable',1)"><input type='button' value="remove project" onClick="removeLastTR('configTable',1)"></td>
				</tr>
<?php
	if( !is_array($defaults['SLAM_PROJECT_NAME']) )
		$defaults['SLAM_PROJECT_NAME'] = array('');
	
	foreach( $defaults['SLAM_PROJECT_NAME'] as $project )
	{
		print "<tr>\n";
		print "<td class='inputField'>Project name:</td>\n";
		print "<td class='inputValue'><input type='text' value='$project' size='10' name='SLAM_PROJECT_NAME[]' onkeyup=\"validateNeg( this, '[&#34\'\,\`]+')\" /></td>\n";
		print "</tr>\n";
	}
?>
			</table>
			<div class='actionButtons'>
				<input type='submit' class='submitButton' value='Save these settings and Continue' />
			</div>
		</form>
		<form name='back' action='step_2.php' method='post'>
			<div class='actionButtons'>
				<input type='submit' class="submitButton" value='Cancel these settings and Go Back' />
			</div>
		</form>
	</div></body>
</html>