<?php
	require('lib/constants.inc.php');
	require('lib/actions.inc.php');
	
	$fail = array();
	
	# save the previous page settings
	if ($_REQUEST['STEP'] == 3)
		if( ($ret = write_SLAM_options( './step_3.ini' )) != true )
			$fail[] = "Could not save your progress. Please contact your system administrator: $ret";

	# Read the default settings either from the previously-entered options, or from the default file
	if (file_exists('./step_4.ini'))
		$defaults = parse_ini_file('./step_4.ini');
	else
		$defaults = parse_ini_file('./defaults.ini');
	
	# read the project options too
	if (file_exists('./step_3.ini'))
		$projects = parse_ini_file('./step_3.ini');
	else
		$projects = parse_ini_file('./defaults.ini');	
?>
<html>
	<head>
		<title>SLAM installer - Step 4/4</title>
		<link type='text/css' href='css/install.css' rel='stylesheet' />
		<script type='text/javascript' src='js/check.js'></script>
		<script type='text/javascript' src='js/clone.js'></script>
		<script type='text/javascript' src='js/validate.js'></script>
	</head>
	<body><div id='container'>
		<script type='text/javascript'>
			document.cloneTRcounter=<?php echo count($defaults['SLAM_USERS'])*3 ?>;
		</script>
		<div id='installerTitle'><span style='font-family:Impact'>SLAM</span> installer - Step 4/4</div>
		<div id='installerVer'>Version: <?php print($slam_version) ?></div>
<?php

	foreach( $fail as $text )
		print "<div class='fatalFail'>$text</div>\n";		
?>		
		<form name='forward' action='confirm.php' method='post'>
			<input type='hidden' name='STEP' value='4' />
			<table id='configTable'>
				<tr>
					 <td class='helpHeader' colspan="2">For assistance, please refer to the SLAM documentation [<a href='http://steelsnowflake.com/SLAM' target='_new'>here</a>].</td>
				</tr>
				<tr>
					<td class='inputCategory' colspan='2'>Administrator setup</td>
				</tr>
				<tr>
					 <td class='categoryInfo' colspan="2">A "superuser" account that can act as an administrator is required. This user can modify or remove any user's assets.</td>
				</tr>
				<tr>
					<td class='inputField'>Superuser name:</td>
					<td class='inputValue'><input type='text' value='<?php print $defaults['SLAM_ROOT_NAME'] ?>' size='20' name='SLAM_ROOT_NAME' onkeyup="validateNeg( this, '[&#34\'\,\`]+')" /></td>
				</tr>
				<tr>
					<td class='inputField'>Superuser password:</td>
					<td class='inputValue'><input type='password' value='<?php print $defaults['SLAM_ROOT_PASS_1'] ?>' size='20' name='SLAM_ROOT_PASS_1' onkeyup="validateNeg( this, '[&#34\']+')" /></td>
				</tr>
				<tr>
					<td class='inputField'>Confirm password:</td>
					<td class='inputValue'><input type='password' value='<?php print $defaults['SLAM_ROOT_PASS_2'] ?>' size='20' name='SLAM_ROOT_PASS_2' onkeyup="validateNeg( this, '[&#34\']+')" /></td>
				</tr>
				<tr>
					<td class='inputField'>Superuser email:</td>
					<td class='inputValue'><input type='text' value='<?php print $defaults['SLAM_ROOT_EMAIL'] ?>' size='20' name='SLAM_ROOT_EMAIL' onkeyup="validateNeg( this, '[&#34\']+')" /></td>
				</tr>
				<tr>
					<td class='inputCategory' colspan='2'><br />User setup</td>
				</tr>
				<tr>
					 <td class='categoryInfo' colspan="2">You can now set up any number of regular user accounts. Additional users can always be added later by a superuser, if necessary.
						 <br /><br />If you specified any projects on step 3, you can associate users with them now. Multiple projects may be selected by cntrl/command clicking.</td>
				</tr>
				<tr>
					<td class='inputField'></td>
					<td class='inputValue'><input type='button' value="add user" onClick="cloneLastTR('configTable',4)"><input type='button' value="remove user" onClick="removeLastTR('configTable',3)"></td>
				</tr>
<?php
	if( !is_array($defaults['SLAM_USERS']) )
	{
		$defaults['SLAM_USERS'] = array('');
		$defaults['SLAM_PASSWORDS'] = array('');
	}
	
	$i = 0;
	foreach( $defaults['SLAM_USERS'] as $user )
	{
		print "<tr>\n";
		print "<td class='inputField'>Username:</td>\n";
		print "<td class='inputValue'><input type='text' value='$user' size='10' name='SLAM_USERS[]' onkeyup=\"validateNeg( this, '[&#34\'\,\`]+')\" /></td>\n";
		print "</tr>\n";
		print "<tr>\n";
		print "<td class='inputField'>Password:</td>\n";
		print "<td class='inputValue'><input type='password' value='{$defaults['SLAM_PASSWORDS'][ $i ]}' size='20' name='SLAM_PASSWORDS[]' onkeyup=\"validateNeg( this, '[&#34\']+')\" /></td>\n";
		print "</tr>\n";
		print "<tr>\n";
		print "<td class='inputField'>Email:</td>\n";
		print "<td class='inputValue'><input type='text' value='{$defaults['SLAM_EMAILS'][ $i ]}' size='20' name='SLAM_EMAILS[]' onkeyup=\"validateNeg( this, '[&#34\'\,\`]+')\" /></td>\n";
		print "</tr>\n";
		print "<tr>\n";
		print "<td class='inputField'>Projects:</td>\n";
		print "<td class='inputValue'>\n";
		print "<select style='width:126px' name='SLAM_USER_PROJECTS_{$i}[]' multiple='multiple'>\n";

		if( is_array($defaults['SLAM_USER_PROJECTS_'.$i]) )
			$selected = $defaults['SLAM_USER_PROJECTS_'.$i];
		else
			$selected = array();
		
		foreach( $projects['SLAM_PROJECT_NAME'] as $project )
		{
			$s = in_array($project,$selected) ? "selected='selected'" : '';
			print "<option value='$project' $s>$project</option>\n";
		}
		
		print "</select>\n";
		print "</td>\n";
		print "</tr>\n";
		$i++;
	}
?>
			</table>
			<div class='actionButtons'>
				<input type='submit' class='submitButton' value='Save these settings and Continue' />
			</div>
		</form>
		<form name='back' action='step_3.php' method='post'>
			<div class='actionButtons'>
				<input type='submit' class="submitButton" value='Cancel these settings and Go Back' />
			</div>
		</form>
	</div></body>
</html>