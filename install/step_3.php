<?php
	require('lib/constants.inc.php');
	require('lib/actions.inc.php');
	
	$fail = array();
		
	# Read the default settings either from the previously-entered options, or from the default file
	if (file_exists('./step_3.ini'))
		$defaults = parse_ini_file('./step_3.ini');
	else
		$defaults = parse_ini_file('./defaults.ini');
	
	# save the previous page settings
	if ($_REQUEST['STEP'] == 2)
		if( ($ret = write_SLAM_options( './step_2.ini' )) != true )
			$fail[] = "Could not save your progress. Please contact your system administrator: $ret"
?>
<html>
	<head>
		<title>SLAM installer - Step 3</title>
		<link type='text/css' href='css/install.css' rel='stylesheet' />
		<script type='text/javascript' src='js/check.js'></script>
		<script type='text/javascript' src='../js/convert.js'></script>
	</head>
	<body><div id='container'>
		<div id='installerTitle'><span style='font-family:Impact'>SLAM</span> installer - Step 3</div>
		<div id='installerVer'>Version: <?php print($slam_version) ?></div>
<?php
	foreach( $fail as $text )
		print "<div class='fatalFail'>$text</div>\n";		
?>		
		<form name='forward' action='complete.php' method='post'>
			<input type='hidden' name='STEP' value='3' />
			<table id='configTable'>
				<tr>
					 <td class='helpHeader' colspan="2">For assistance, please refer to the SLAM [<a href='http://code.google.com/p/slam-project/wiki/Installation' target='_new'>installation wiki</a>].</td>
				</tr>
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