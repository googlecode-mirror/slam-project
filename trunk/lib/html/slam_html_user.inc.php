<?php

function SLAM_makeUserAuthHTML(&$config,$db,$user)
{	
	/*
		Displays the user login form, as well as any provided message
	*/
	
	$config->html['js'][] = 'js/detect.js';
	
	$s.=<<<EOL

<script>
	var stat = checkBrowser();
if (stat < 1)
	document.write("<div class='error'>This browser is not supported for use with SLAM.</div>");
else if (stat < 2)
	document.write("<div class='error'>SLAM requires that browser cookies be enabled.</div>");
</script>
<form name='loginForm' action='{$config->html['url']}' method='POST'>
<div id='authContainer'>
<table id='authFields'>
<tr><td class='authTitle'>Username:</td><td class='authField'><input name='login_username' value='{$_REQUEST['login_username']}' /></td>\n</tr>
<tr><td class='authTitle'>Password:</td><td class='authField'><input name='login_password' type='password' /></td>\n</tr>
</table>
<input type='submit' value='Log In' />
EOL;

	if (!empty($config->errors))
		$s.="<div id='authMessage'><a href='#' onClick=\"showPopupDiv('pub/password_reset.html','userActionPopup',{'noclose':1}); return false\">forgot password?</a></div>\n";
		
	$s.="</div>\n</form>\n";
	
	return "$s<img id='authImage' src='img/SLAM_splash_300x178.png' width='300' height='178' alt='[SLAM_logo'] />";
}

?>