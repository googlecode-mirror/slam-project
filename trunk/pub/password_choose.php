<form name='changepassForm' id='changepassForm' action='index.php' method='POST'>
	<div id='userChangePass'>
		<input type='hidden' name='a' value='user' />
		<input type='hidden' name='user_action' value='reset_save' />
		<input type='hidden' name='user_name' value='<?php echo($_REQUEST['user_name']) ?>' />
		<input type='hidden' name='secret' value='<?php echo($_REQUEST['secret']) ?>' />
		<table>
			<tr><td style='text-align:right'>New password:</td><td><input type='password' name='new_password' value='' id='new_password' /></td>&nbsp;</tr>
			<tr><td style='text-align:right'>New password: (confirm)</td><td><input type='password' name='new_password_2' value='' id='new_password_2' /></td>&nbsp;</tr>
		</table>
		<input type='button' value='Change Password' onClick="checkPasswordForm('changepassForm','new_password','new_password_2')" />
	</form>
</form>