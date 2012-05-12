<form name='userActions' action='' method='POST'>
	<input type='button' value='Preferences' onClick="showPopupDiv('pub/user_prefs.php','userDiv',{'noclose':true})"/>
	<input type='button' value='Change Password' onClick="showPopupDiv('pub/password_change.php','userDiv',{'noclose':true})"/>
<?php
if( $_REQUEST['superuser'] == 'true'){
	echo("	<input type='button' value='Add User' onClick=\"showPopupDiv('pub/user_create.php','userDiv',{'noclose':true})\"/>\n");
}
?>	
</form>
<br />
<form name='logoutForm' action='index.php' method='POST'>
	<input type='hidden' name='logout' value='true' />
	<input type='submit' value='Log Out' />
</form>