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
		<table id='configTable'>
			<tr>
				<td class='helpHeader' colspan="2">For assistance, please refer to the SLAM [<a href='http://code.google.com/p/slam-project/wiki/Installation' target='_new'>installation wiki</a>].</td>
			</tr>
			<tr>
					<td class='inputCategory' colspan='2'>Nearly finished!</td>
			</tr>
			<tr>
					 <td class='categoryInfo' colspan="2">The installer is ready to complete your SLAM installation. If you would like to change any of the options, click on the appropriate step button below. You may return to this page by using your browser's "back" button.</td>
			</tr>
			<tr>
				<td class='confirmButtons' colspan='2'>
					<form name='license' action='index.php'  method='post'>
						<input type='submit' class='submitButton' value='Review License' />
					</form>
					<br />
				</td>
			</tr>
			<tr>
				<td class='confirmButtons' colspan='2'>
					<form name='step_1' action='step_1.php'  method='post'>
						<input type='submit' class='submitButton' value='Review Step 1 Options' />
					</form>
				</td>
			</tr>
			<tr>
				<td class='confirmButtons' colspan='2'>
					<form name='step_2' action='step_2.php'  method='post'>
						<input type='submit' class='submitButton' value='Review Step 2 Options' />
					</form>
				</td>
			</tr>
			<tr>
				<td class='confirmButtons' colspan='2'>
					<form name='step_3' action='step_3.php'  method='post'>
						<input type='submit' class='submitButton' value='Review Step 3 Options' />
					</form>
				</td>
			</tr>
			<tr>
				<td class='confirmButtons' colspan='2'>
					<form name='step_4' action='step_4.php'  method='post'>
						<input type='submit' class='submitButton' value='Review Step 4 Options' />
					</form>
					<br />
				</td>
			</tr>
			<tr>
				<td class='inputCategory' colspan='2'>All done?</td>
			</tr>
			<tr>
					 <td class='categoryInfo' colspan="2">If you are satisfied with your settings, click "Complete Installation" to set up your installation of SLAM:</td>
			</tr>
			<tr>
				<td class='confirmButtons' colspan='2'>
					<form name='complete' action='complete.php'  method='post'>
						<input type='submit' class='submitButton' value='Complete Installation' />
					</form>
				</td>
			</tr>
		</table>
		
	</div></body>
</html>