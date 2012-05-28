<form name='changepassForm' id='createNewUserForm' action='' method='POST'>
	<div id='createNewUser'>
		<input type='hidden' name='a' value='user' />
		<input type='hidden' name='user_action' value='create_user' />
		<table>
			<tr>
				<td style='text-align:right'>Username:</td>
				<td><input type='text' name='new_user_name' id='new_user_name' value='<?php echo($_REQUEST['new_user_name'])?>' size='18'/></td>
			</tr>
			<tr>
				<td style='text-align:right'>Email:</td>
				<td><input type='text' name='new_user_email' id='new_user_email' value='<?php echo($_REQUEST['new_user_email'])?>' size='18'/></td>
			</tr>
			<tr>
				<td style='text-align:right'>Password:</td>
				<td><input type='password' name='new_user_password' id='new_user_password' value='' size='18'/></td>
			</tr>
			<tr>
				<td style='text-align:right'>Password: (confirm)</td>
				<td><input type='password' name='new_user_password2' id='new_user_password2' value='' size='18'/></td>
			</tr>
			<tr>
				<td style='text-align:right'>Projects:</td>
				<td><input type='text' name='new_user_projects' id='new_user_projects' value='<?php echo($_REQUEST['new_user_projects'])?>' size='18'/></td>
			</tr>
		</table>
		<br />
		<?php
			if($_REQUEST['error'] == 'true'){
				echo "<span style='color:red'>".rawurldecode($_REQUEST['error_text'])."</span><br />\n";
			}
		?>
		<input type='button' value='Create User' onClick="checkPasswordForm('createNewUserForm','new_user_password','new_user_password2')" /><input type='button' value='Cancel' onClick="removeBodyId('userActionPopup')"/>
	</div>
</form>