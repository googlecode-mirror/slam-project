<?php
	set_include_path(get_include_path().PATH_SEPARATOR.'../');
	require('../lib/slam_index.inc.php');
	
	$config	= new SLAMconfig();
	$db		= new SLAMdb($config);
	$user	= new SLAMuser($config,$db);
?>
<form name='userOptions' id='userOptions' action='' method='POST'>
	<div id='setUserOptions'>
		<input type='hidden' name='a' value='user' />
		<input type='hidden' name='user_action' value='set_options' />
		<table>
			<tr>
				<td style='text-align:right'>Username:</td>
				<td>
					<input readonly='readonly' type='text' size='25' value='<?php echo($user->values['username']) ?>' />
				</td>
			</tr>
			<tr>
				<td style='text-align:right;vertical-align:top'>Groups:</td>
				<td>
					<textarea readonly='readonly' cols='18' rows='4'>
<?php
	foreach ($user->values['groups'] as $group)
		echo $group."\n";
?>
					</textarea>
				</td>
			</tr>
			<tr>
				<td style='text-align:right'>Email:</td>
				<td>
					<input type='text' name='user_email' size='25' value='<?php echo($user->values['email']) ?>' id='user_email' />
				</td>
			</tr>
			<tr>
				<td colspan='2' style='text-align:left;padding-top:10px'>New assets are:</td>
			</tr>
			<tr>
				<td colspan='2' style='text-align:right'>Editable by
					<select name='foo1'>
						<option value='0'>Me and my groups</option>
						<option value='1'>Just me</option>
						<option value='2'>Anyone</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan='2' style='text-align:right'>Readable by
					<select name='foo2'>
						<option value='0'>Me and my groups</option>
						<option value='1'>Just me</option>
						<option value='2'>Anyone</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan='2' style='text-align:right;padding-top:10px'>
					<input type='submit' value='Save' />
					<input type='button' value='Cancel' onClick="removeBodyId('userDiv')" />
				</td>
			</tr>
		</table>
	</form>
</form>