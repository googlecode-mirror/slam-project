<?php

class SLAMuser
{
	public $username;
	public $email;
	public $prefs;
	public $projects;
	public $authenticated;
	public $superuser;
	
	function __construct(&$config=false,$db=false,$username=false,$password=false)
	{
		if ((!$config) || (!$db))
			return;

		/* loaduser will return false if username/password are bad */
		if(($ret = $this->loaduser($config,$db,$username,$password)) !== false)
		{
			$this->authenticated = true;
			$this->superuser = ($ret['superuser'] > 0) ? True : False;
			$this->email = $ret['email'];
			
			/* extract user project groups */
			if( $ret['projects'] != '')
				$this->projects = split(',',$ret['projects']);
			else
				$this->projects = array();
//			if(count($this->projects) == 0)
//				$this->projects = array( $this->values['username'] );
			
			/* prefs already loaded by loaduser() */
			if(!is_numeric($this->prefs['default_project_access']))
				$this->prefs['default_project_access'] = (int)$config->values['permissions']['default_project_access'];
			
			if(!is_numeric($this->prefs['default_access']))
				$this->prefs['default_access'] = (int)$config->values['permissions']['default_access'];

			$this->prefs['failed_logins'] = 0;
		}
			
		return;
	}

	private function loaduser(&$config,$db,$username,$password)
	{
		if ($_REQUEST['logout']){
			setcookie("slam_{$config->values['lab_prefix']}",'',time()-3600,'/');
			return false;
		}

		$this->username = $username;
		
		/* is the user attempting to log in? */
		if (($_REQUEST['login_username']) && ($_REQUEST['login_password']))
		{
			$this->username = $_REQUEST['login_username'];
			$password = $_REQUEST['login_password'];
		}
		elseif($_REQUEST['auth']) /* is the user sending an auth variable? */
		{
			list($this->username,$password) = explode(':',base64_decode($_REQUEST['auth']));
		}
		elseif($_COOKIE["slam_{$config->values['lab_prefix']}"]) /* does the user possess an auth cookie? */
		{
			$crypt = mysql_real_escape(urldecode($_COOKIE["slam_{$config->values['lab_prefix']}"]),$db->link);
			$auth = $db->GetRecords("SELECT * FROM `{$config->values['user_table']}` WHERE `crypt`='$crypt' LIMIT 1");
			
			if ($auth === false) //GetRecords returns false on error
				die('Database error: could not check user crypt key: '.mysql_error());
			elseif (count($auth) == 1)
			{
				/* refresh the cookie */
				setcookie("slam_{$config->values['lab_prefix']}",$auth[0]['crypt'],time()+$config->values['cookie_expire'],'/');
				
				$this->username = $auth[0]['username'];
				$this->prefs = unserialize($auth[0]['prefs']);
				return $auth[0];
			}
			
			$config->errors[] = 'Auth error: Invalid crypt key.';
			
			# erase the bad cookie
			setcookie("slam_{$config->values['lab_prefix']}",'',time()-3600,'/');
			
			return false;
		}
		
		if( $this->username == false)
			return false;
		
		/* attempt to check out the username and password */
		$auth = $this->checkPassword($config,$db,$password);
		
		/* set the cookie to keep the user logged in and copy the user prefs, etc to the user */
		if ($auth !== false)
		{
			setcookie("slam_{$config->values['lab_prefix']}",sha1($auth[0]['salt'].$_REQUEST['login_password']),time()+$config->values['cookie_expire'],'/');
			return $auth[0];
		}
		else
			$config->errors[] = 'Auth error: Incorrect password provided.';
		
		return false;
	}
	
	function checkPassword(&$config,$db,$password)
	{
		$auth = $db->GetRecords("SELECT * FROM `{$config->values['user_table']}` WHERE `username`='".mysql_real_escape($this->username,$db->link)."' LIMIT 1");
		
		/* compare the salt+password hash with that stored in the db */
		if ($auth === false) //GetRecords returns false on error
			die('Database error: could not check user passphrase: '.mysql_error());
		
		/* make sure we haven't exceeded our number of failed logins */
		if( count($auth) == 1)
		{
			$this->prefs = unserialize($auth[0]['prefs']);
			
			if( $this->prefs['failed_logins'] > 20 )
				$config->errors[] = 'Auth error: Maximum number of failed attempts reached!';
			elseif( sha1($auth[0]['salt'].$password) == $auth[0]['crypt'] )
				return $auth;
			
			/* if we weren't successful, increment the failed login counter */		
			$this->prefs['failed_logins']++;
			$this->savePrefs($config, $db);
		}
		$config->errors[] = 'Auth error: Invalid username provided.';
		
		return false;
	}
	
	function savePrefs(&$config,$db)
	{
		$prefs = mysql_real_escape(serialize($this->prefs),$db->link);
		$q = "UPDATE `{$config->values['user_table']}` SET `prefs`='$prefs' WHERE `username`='$this->username' LIMIT 1";		
		if (!$db->Query($q))
		{
			$config->errors[] = 'Error updating user preferences: '.mysql_error();
			return false;
		}
		return true;
	}
}

?>