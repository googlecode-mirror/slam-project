<?php

class SLAMuser
{
	public $values;
	public $prefs;
	public $authenticated;
	
	function __construct(&$config=false,$db=false,$username=false,$password=false)
	{
		if ((!$config) || (!$db))
			return;
			
		if(($this->authenticated = $this->loaduser($config,$db,$username,$password)) === true)
		{
			$this->prefs = unserialize($this->values['prefs']);
			$this->values['groups'] = split(',',$this->values['groups']);
		}
				
		return;
	}

	private function loaduser(&$config,$db,$username,$password)
	{
		if ($_REQUEST['logout']){
			setcookie("{$config->values['name']}_slam",'',time()-3600,'/');
			return false;
		}
	
		/* is the user attempting to log in? */
		if (($_REQUEST['login_username']) && ($_REQUEST['login_password']))
		{
			$username = urldecode($_REQUEST['login_username']);
			$password = urldecode($_REQUEST['login_password']);
		}
		elseif($_REQUEST['auth']) /* is the user sending an auth variable? */
		{
			list($username,$password) = explode(':',base64_decode(rawurldecode($_REQUEST['auth'])));
		}
		elseif($_COOKIE["{$config->values['name']}_slam"]) /* does the user possess an auth cookie? */
		{
			$crypt = mysql_real_escape(urldecode($_COOKIE["{$config->values['name']}_slam"]),$db->link);
			$auth = $db->GetRecords("SELECT * FROM `{$config->values['user_table']}` WHERE `crypt`='$crypt' LIMIT 1");

			if ($auth === false) //GetRecords returns false on error
				die('Database error: could not check user crypt key: '.mysql_error());
			elseif (count($auth) == 1)
			{
				/* refresh the cookie */
				setcookie("{$config->values['name']}_slam",$auth[0]['crypt'],time()+$config->values['cookie_expire'],'/');
				$this->values = $auth[0];
				return true;
			}
			
			$config->errors[] = 'Auth error: Invalid crypt key.';
			return false;
		}
		
		/* attempt to check out the username and password */
		$auth = $this->checkPassword($config,$db,$username,$password);

		/* set the cookie to keep the user logged in and copy the user prefs, etc to the user */
		if ($auth !== false)
		{
			setcookie("{$config->values['name']}_slam",sha1($auth[0]['salt'].urldecode($_REQUEST['login_password'])),time()+$config->values['cookie_expire'],'/');
			$this->values = $auth[0];
			return true;
		}
		else
			$config->errors[] = 'Auth error: Incorrect password provided.';
		
		return false;
	}
	
	function checkPassword($config,$db,$username,$password)
	{
		$auth = $db->GetRecords("SELECT * FROM `{$config->values['user_table']}` WHERE `username`='".mysql_real_escape($username,$db->link)."' LIMIT 1");
		
		/* compare the salt+password hash with that stored in the db */
		if ($auth === false) //GetRecords returns false on error
			die('Database error: could not check user passphrase: '.mysql_error());
		if ((count($auth) == 1) && (sha1($auth[0]['salt'].$password) == $auth[0]['crypt']))
			return $auth;

		$config->errors[] = 'Auth error: Invalid username provided.';
		return false;
	}
	
	function savePrefs($config,$db)
	{
		$prefs = mysql_real_escape(serialize($this->prefs),$db->link);
		$q = "UPDATE `{$config->values['user_table']}` SET `prefs`='$prefs' WHERE `username`='{$this->values['username']}' LIMIT 1";
		$db->Query($q);
	}
}

?>