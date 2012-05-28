<form name='changepassForm' id='changepassForm' action='' method='POST'>
	<div id='userChangePass'>
		<input type='hidden' name='a' value='user' />
		<input type='hidden' name='user_action' value='change_password' />
		<table>
			<tr><td style='text-align:right'>Old password:</td><td><input type='password' name='old_password' id='old_password' value='' size='16'/></td>&nbsp;</tr>
			<tr><td style='text-align:right'>New password:</td><td><input type='password' name='new_password' value='' id='new_password' size='16'/></td>&nbsp;</tr>
			<tr><td style='text-align:right'>New password: (confirm)</td><td><input type='password' name='new_password_2' value='' id='new_password_2' size='16'/></td>&nbsp;</tr>
		</table>
		<?php if($_REQUEST['bad_password']){ echo "<span style='color:red'>Bad Password</span><br />\n"; } ?>
		<input type='button' value='Change Password' onClick="checkPasswordForm('changepassForm','new_password','new_password_2')" /><input type='button' value='Cancel' onClick="removeBodyId('userActionPopup')"/>
	</div>
</form>