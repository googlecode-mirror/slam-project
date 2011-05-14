<?php

require('lib/slam_index.inc.php');

$config	= new SLAMconfig();
$db		= new SLAMdb($config);
$user	= new SLAMuser($config,$db);

$config->html['css'][] = 'css/global.css';
$config->html['css'][] = 'css/popup.css';
$config->html['js'][] = 'js/index.js';
$config->html['js'][] = 'js/popup.js';

if ($user->authenticated)
{
	$request = new SLAMrequest($config,$db,$user);

	switch($request->action)
	{
		case 'new':
		case 'clone':
			$result = new SLAMresult($config,$db,$user,$request);
			$content = SLAM_makeAssetEditHTML($config,$db,$user,$request,$result,true);
			break;
		case 'edit':
		case 'open':
			$result = new SLAMresult($config,$db,$user,$request);
			$content = SLAM_makeAssetEditHTML($config,$db,$user,$request,$result,false);
			break;
		case 'save changes':
			$content = SLAM_saveAssetEdits($config,$db,$user,$request);
			$result = new SLAMresult($config,$db,$user,$request);
			$content.= SLAM_makeAssetEditHTML($config,$db,$user,$request,$result,false);
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

	switch($request->location)
	{
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
}

if ($_REQUEST['user_action'])
	$content.= SLAM_doUserActionHTML($config,$db,$user);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0//EN">
<html>
	<head>
		<title><?php echo $config->values['name']; ?></title>
<?php
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
				<div class='error'>Javascript is required for proper usage of SLAM!</div>
			</noscript>
<?php

if ($user->authenticated)
{
	echo SLAM_makeBreadcrumbHTML($config,$db,$user,$request,$result);
	echo SLAM_makeCategoryListHTML($config,$db,$user,$request);
	echo $content;
}
else
	echo SLAM_makeUserAuthHTML($config,$db,$user);
	
if(!empty($config->values['debug']))
{
	echo "<div class='error'>\n";
	for($i=0; $i<count($config->errors); $i++)
		echo "$i) {$config->errors[$i]}<br />\n";		
	echo "</div>\n";
}

?>
		<div id='contentFooter'>SLAM v. <?php echo($config->values['version']) ;?> &copy; 2011 <a href='#' onClick="showPopupDiv('pub/about.php','helpDiv',{}); return false">About</a><br />[<a href="http://code.google.com/p/slam-project/issues/entry">Report a Bug</a>]</div>
		</div>
	</body>
</html>
