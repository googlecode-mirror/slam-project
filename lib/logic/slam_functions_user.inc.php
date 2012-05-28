<?php

function SLAM_doUserAction(&$config,$db,&$user)
{
	/*
		performs the requested user action
	*/
	
	switch($_REQUEST['user_action'])
	{
		case 'set_preferences':
			SLAM_setUserPreferences($config,$db,$user);
			/* pop up the user prefs panel to show the user that the changes have been applied */
			$config->html['onload'][] = 'showPopupDiv("pub/user_prefs.php","userActionPopup",{"noclose":true})';

			return;
		case 'change_password':
			if(SLAM_saveUserPassword($config,$db,$user) === true)
				return;
			else
				$config->html['onload'][] = 'showPopupDiv("pub/password_change.php?bad_password=true","userActionPopup",{})';
		
			return;
		case 'reset_send':
			SLAM_sendUserResetMail($config,$db);
			
			return;
		case 'reset_change':
			$config->html['onload'][] = "showPopupDiv(\"pub/password_choose.php?user_name={$_REQUEST['user_name']}&secret={$_REQUEST['secret']}\",\"userActionPopup\",{})";

			return;
		case 'reset_save':
			SLAM_saveUserResetPass($config,$db);
			
			return;
			
		case 'create_user':
			if( ($msg = SLAM_createNewUser($config,$db,$user)) !== true)
				$config->html['onload'][] = 'showPopupDiv("pub/user_create.php?error=true&error_text='.rawurlencode($msg).'","userActionPopup",{})';
			else
				return;
		default:
			return;
	}

	return;
}

function SLAM_setUserPreferences(&$config,$db,&$user)
{	
	$user->prefs['default_project'] = $_REQUEST['defaultProject'];
	
	/* interpret the permission menu selections */
	switch( $_REQUEST['defaultReadable'] )
	{
		case 1:
			$user->prefs['default_project_access'] = 1;
			$user->prefs['default_access'] = 0;
			break;
		case 2:
			$user->prefs['default_project_access'] = 1;
			$user->prefs['default_access'] = 1;
			break;
		default:
			$user->prefs['default_project_access'] = 0;
			$user->prefs['default_access'] = 0;
	}
	
	switch( $_REQUEST['defaultEditable'] )
	{
		case 1:
			$user->prefs['default_project_access'] = 2;
			break;
		case 2:
			$user->prefs['default_project_access'] = 2;
			$user->prefs['default_access'] = 2;
			break;
	}

	$user->savePrefs($config,$db);

	return;
}

function SLAM_saveAssetTags($config,$db,&$user,$request)
{
	$identifiers = array();

	/* append the tagged identifiers to the user's preferences' identifier array */
	foreach($request->categories as $category=>$assets)
	{
		if (!is_array($user->prefs['identifiers'][$category]))
			$user->prefs['identifiers'][$category] = array();
			
		$user->prefs['identifiers'][$category] = array_unique(array_merge($user->prefs['identifiers'][$category],$assets));
	}

	/* sort the identifiers */
	if(!ksort($user->prefs['identifiers']))
		$config->errors[] = 'Could not sort user tagged assets.';

	/* safety check to remove any reset secret still hanging around */
	if ($user->prefs['reset_secret'])
		unset($user->prefs['reset_secret']);
		
	/* save the modified list back to the user's record */
	$user->savePrefs($config,$db);
	
	return;
}

function SLAM_dropAssetTags($config,$db,&$user,$request)
{
	foreach($request->categories as $category=>$identifiers)
		if(is_array($user->prefs['identifiers'][$category]) && is_array($identifiers))
			$user->prefs['identifiers'][$category] = array_diff($user->prefs['identifiers'][$category],$identifiers);
			
	/* remove any empty categories */
	foreach($user->prefs['identifiers'] as $category=>$identifiers)
		if (empty($identifiers))
			unset($user->prefs['identifiers'][$category]);

	$user->savePrefs($config,$db);
	
	return;
}

function SLAM_changeUserPassword(&$config,$db,$username,$newpass)
{
	$username = mysql_real_escape($username,$db->link);
	$salt = makeRandomAlpha(8);
	$crypt = sha1($salt.$newpass);

	/* attempt to update the salt and crypt */
	$auth = $db->Query("UPDATE `{$config->values['user_table']}` SET `salt`='$salt',`crypt`='$crypt' WHERE `username`='$username' LIMIT 1");
	if ($auth === false)
	{
		$config->errors[] = 'Database error: Could not update password, could not access user table:'.mysql_error();
		return false;
	}
	elseif (count($auth) < 1)
	{
		$config->errors[] = 'Database error: Could not update password, invalid username provided.';
		return false;
	}
	
	return true;
}

function SLAM_saveUserPassword(&$config,$db,$user)
{
	if(!$user->authenticated)
		$config->errors[] = 'You must be logged in to change your password.';
	
	$old_password = $_REQUEST['old_password'];
	$new_password = $_REQUEST['new_password'];

	if ($user->checkPassword($config,$db,$old_password))
		return SLAM_changeUserPassword($config,$db,$new_password);

	return false;
}

function SLAM_sendUserResetMail(&$config,$db)
{
	$email = mysql_real_escape($_REQUEST['user_email'],$db->link);

	$auth = $db->GetRecords("SELECT * FROM `{$config->values['user_table']}` WHERE `email`='$email'");
	
	//GetRecords returns false on error
	if ($auth === false)
	{
		$config->errors[] = 'Database error: Could not send reset email, could not access user table:'.mysql_error();
		return;
	}
	elseif (count($auth) < 1)
	{
		$config->errors[] = 'Could not send reset email, address is not valid.';
		return;
	}
	
	$reset_urls = '';
	foreach($auth as $user)
	{
		/* make the secret key the user will use to reset his/her password */
		$secret = makeRandomAlpha(10);
	
		/* save the secret to the user */
		$prefs = unserialize($user['prefs']);
		$prefs['reset_secret'] = $secret;
		$prefs = mysql_real_escape(serialize($prefs),$db->link);
	
		/* attempt to save the secret back to the user */
		$result = $db->Query("UPDATE `{$config->values['user_table']}` SET `prefs`='$prefs' WHERE `username`='{$user['username']}' LIMIT 1");
		if ($result === false){
			$config->errors[] = 'Database error:  Could not send reset email, could not update user table:'.mysql_error();
			return;
		}
	
		$referrer = explode('?',$_SERVER[HTTP_REFERER]);
		$reset_urls.= "For the account: \"{$user['username']}\":\n";
		$reset_urls.= $referrer[0]."?action=user&user_action=reset_change&user_name={$user['username']}&secret=$secret\n\n";
	}
	
	$message = <<<EOL
Someone from the IP address {$_SERVER[REMOTE_ADDR]} has requested that your account password be reset.
If you did not request this, you can safely ignore this message.

If you would like to reset your password, please click or copy/paste this address into your browser:

$reset_urls
EOL;

	if (mail($email,'SLAM Password reset',wordwrap($message,70).$url,$config->values['mail_header']) !== true)
		$config->errors[]='Could not send password reset email.';
		
	return;
}

function SLAM_saveUserResetPass(&$config,$db)
{	
	if (empty($_REQUEST['user_name']) || empty($_REQUEST['new_password']))
		return false;

	$username = mysql_real_escape($_REQUEST['user_name'],$db->link);		
	$password = mysql_real_escape($_REQUEST['new_password'],$db->link);
	$secret = $_REQUEST['secret'];
	
	$auth = $db->GetRecords("SELECT * FROM `{$config->values['user_table']}` WHERE `username`='$username' LIMIT 1");
	if ($auth === false){ //GetRecords returns false on error
		$config->errors[] = 'Database error: Could not save new password, could not access user table:'.mysql_error();
		return;
	}
	elseif (count($auth) < 1){
		$config->errors[] = 'Database error: Could not save new password, specified user was not found:';
		return;
	}
	
	$prefs = unserialize($auth[0]['prefs']);
	
	/* check the provided secret string against the one the user possesses */
	if ($prefs['reset_secret'] != $secret){
		$config->errors[] = 'User secrets did not match! New password was not saved.';
		return;
	}

	/* if we made it this far we're good */
	if(SLAM_changeUserPassword($config,$db,$username,$password) !== true)
		 return;
		
	/* remove the secret key from the user's prefs */
	unset($prefs['reset_secret']);
	$prefs = mysql_real_escape(serialize($prefs),$db->link);
	
	$result = $db->Query("UPDATE `{$config->values['user_table']}` SET `prefs`='$prefs' WHERE `username`='$username' LIMIT 1");
	if ($result === false)
		$config->errors[] = 'Database error:  Could not remove secret key from user record:'.mysql_error();
	
	return;
}

function SLAM_createNewUser( &$config, $db, $user )
{
	if( ! $user->superuser )
		return "Only superusers can add a new user.";
	
	$username	= mysql_real_escape($_REQUEST['new_user_name'],$db->link);		
	$email		= mysql_real_escape($_REQUEST['new_user_email'],$db->link);		
	$password	= mysql_real_escape($_REQUEST['new_user_password'],$db->link);
	$projects	= mysql_real_escape($_REQUEST['new_user_projects'],$db->link);		
	
	$auth = $db->GetRecords("SELECT * FROM `{$config->values['user_table']}` WHERE `username`='$username' LIMIT 1");
	if ($auth === false){ //GetRecords returns false on error
		$config->errors[] = 'Database error: Could not save new password, could not access user table:'.mysql_error();
		return;
	}
	elseif (count($auth) > 0){
		return "A user with that username already exists.";
	}
	
	$result = $db->Query("INSERT INTO `{$config->values['user_table']}` (`username`,`email`,`projects`) VALUES ('$username','$email','$projects')");
	if( $result === false)
	{
		$config->errors[] = 'Database error:  Could not create the new user:'.mysql_error();
		return "Could not create the user.";
	}
	
	if( ! SLAM_changeUserPassword($config,$db,$username,$password) ){
		return "Created user, but could not set password!";
	}
	
	return true;
}

?>