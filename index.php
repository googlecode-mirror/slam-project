<?php

require('lib/slam_index.inc.php');

/* obtain and initialize session objects */
$config	= new SLAMconfig();
$db		= new SLAMdb($config);
$user	= new SLAMuser($config,$db);

/* register the default css and javascript files */
$config->html['css'][] = 'css/global.css';
$config->html['css'][] = 'css/popup.css';
$config->html['js'][] = 'js/global.js';
$config->html['js'][] = 'js/popup.js';

/* load index modules */
$modules = SLAM_loadIndexModules($config,'mod','index.ini');

if ($user->authenticated)
{
	/* parse and obtain the requested action */
	$request = new SLAMrequest($config,$db,$user);

	/* run any module modifications to the request */
	SLAM_doModuleRequest($modules,$config,$db,$user,$request);

	/* perform built-in request actions if necessary */
	switch($request->action)
	{
		case 'none':
			break;
		case 'new':
		case 'clone':
			$result = new SLAMresult($config,$db,$user,$request);
			$content = SLAM_makeAssetEditHTML($config,$db,$user,$request,$result);
			break;
		case 'edit':
		case 'open':
			$result = new SLAMresult($config,$db,$user,$request);
			$content = SLAM_makeAssetEditHTML($config,$db,$user,$request,$result);
			break;
		case 'save changes':
			$content = SLAM_saveAssetEdits($config,$db,$user,$request);
			$result = new SLAMresult($config,$db,$user,$request);
			$content.= SLAM_makeAssetEditHTML($config,$db,$user,$request,$result);
			break;
		case 'save':
			$content = SLAM_saveAssetEdits($config,$db,$user,$request);
			break;
		case 'delete':
			$content = SLAM_deleteAssets($config,$db,$user,$request);
			break;
		case 'search':
			$result = SLAM_loadSearchResults($config,$db,$user,$request);
			break;
		case 'tag':
			$request->location = 'dash';
			$content = SLAM_saveAssetTags($config,$db,$user,$request);
			break;
		case 'untag':
			$request->location = 'dash';
			$content = SLAM_dropAssetTags($config,$db,$user,$request);
			break;
		default:
			break;
	}

	/* determine and/or set the current state (location) of the user */
	switch($request->location)
	{
		case 'none':
			break;
		case 'list':
			if (!$result)
				$result = new SLAMresult($config,$db,$user,$request);
				
			if (!$content)
			{
				$content.= SLAM_makeSearchBoxHTML($config,$db,$user,$request,$result);
				$content.= SLAM_makeAssetListHTML($config,$db,$user,$request,$result);
			}
			break;
		case 'dash':
		default:
			$request->location = 'dash';
			if (!$result)
				$result = SLAM_getDashboardResult($config,$db,$user,$request);
				
			if (!$content)
			{
				$content.= SLAM_makeDashboardHTML($config,$db,$user,$request,$result);
				$content.= SLAM_makeDashboardSearchHTML($config,$db,$user,$request);
				$content.= SLAM_makeDashboardListHTML($config,$db,$user,$request,$result);
			}
			break;
	}
	
	/* prepend the navigation and category listing HTML */
	$content = SLAM_makeCategoryListHTML($config,$db,$user,$request).$content;
	$content = SLAM_makeBreadcrumbHTML($config,$db,$user,$request,$result).$content;
	
	/* obtain module content */
	SLAM_doModuleContent($modules,$config,$db,$user,$request,$result,$content);
}
else
{
	/* provide the login form */
	$content = SLAM_makeUserAuthHTML($config,$db,$user);
}

/* if a module has pre-prepared HTML, we can exit now */
if ($config->html['abort'])
{
	echo $content;
	return;
}
elseif($_REQUEST['user_action']) /* has the user specified an action (e.g. logging in, requesting a password reset?) */
	$content.= SLAM_doUserAction($config,$db,$user);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0//EN">
<html>
	<head>
		<meta http-equiv="pragma" content="no-cache" />
		<title><?php echo $config->values['name']; ?></title>
<?php
	/* include any specified header files */
	foreach($config->html['css'] as $header)
		echo("<link type='text/css' href='$header' rel='stylesheet' />\n");
	foreach($config->html['js'] as $header)
		echo("<script type='text/javascript' src='$header'></script>\n");
	foreach($config->html['headers'] as $header)
		echo($header);
?>
	</head>
	<body onLoad='<?php echo(implode(';',$config->html['onload'])); ?>'>
		<div id='contentDiv'>
			<noscript>
				<div class='error'>Javascript is required for proper usage of SLAM.</div>
			</noscript>
			<?php echo $content ?>
<?php
/* display errors if in debug mode */
if(!empty($config->values['debug']))
{
	echo "<div class='error'>\n";
	for($i=0; $i<count($config->errors); $i++)
		echo "$i) {$config->errors[$i]}<br />\n";		
	echo "</div>\n";
}
?>
		<div id='contentFooter'>SLAM v. <?php echo($config->values['version']) ;?> &copy; <a href='#' onClick="showPopupDiv('pub/about.php','helpDiv',{}); return false">SteelSnowflake</a> LLC<br />[<a href="http://code.google.com/p/slam-project/issues/entry">Report a Bug</a>]</div>
		</div>
	</body>
</html>
