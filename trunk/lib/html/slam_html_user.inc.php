<?php

function SLAM_makeUserAuthHTML($config,$db,$user)
{	
	/*
		Displays the user login form, as well as any provided message
	*/
	
	$s=<<<EOL
<form name='loginForm' action='{$config->html['url']}' method='POST'>
<div id='authContainer'>
<table id='authFields'>
<tr><td class='authTitle'>Username:</td><td class='authField'><input name='login_username' value='{$_REQUEST['login_username']}' /></td>\n</tr>
<tr><td class='authTitle'>Password:</td><td class='authField'><input name='login_password' type='password' /></td>\n</tr>
</table>
<input type='submit' value='Log In' />
EOL;

	if (!empty($config->errors))
		$s.="<div id='authMessage'><a href='#' onClick=\"showPopupDiv('pub/password_reset.html','userDiv',{}); return false\">forgot password?</a></div>\n";
		
	$s.="</div>\n</form>\n";
	
	return "$s<img id='authImage' src='img/barn_swallow.gif' width='200' height='200' alt='[barn_swallow'] />";
}

?>