<?php

require('../lib/slam_export.inc.php');

$config	= new SLAMconfig();
$db		= new SLAMdb($config);
$user	= new SLAMuser($config,$db);


if ($user->authenticated)
{
	$request = new SLAMrequest($config,$db,$user);
	
	switch($request->action)
	{
		case 'search':
			$result = SLAM_loadSearchResults($config,$db,$user,$request);
		default:
			$result = new SLAMresult($config,$db,$user,$request);
	}
	
	$empty = true;
	$output = '';

	foreach($result->assets as $category => $assets)
	{
		if (count($assets)>0)
		{
			foreach( $assets[0] as $key=>$field)
				if( ! in_array($key, $config->values['hide_fields']))
					$output.="$key : $field<br />\n";
			
			$title = $assets[0]['Identifier'];
			$empty = false;
			break;
		}
	}
	
	if ($empty)
	{
		$output = 'No results to export';
		$title = 'Error';
	}
}
else
	die("User is not authorized. Please <a href='{$config->html['url']}'>log in</a>");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0//EN">
<html>
	<head>
		<title><?php echo "{$config->values['name']}:$title"; ?></title>
		<!-- <link type='text/css' media='all' href='../css/asset.css' rel='stylesheet' />
		<link type='text/css' media='print' href='../css/print.css' rel='stylesheet' /> -->
	</head>
	<body>
		<tt><?php echo $output; ?></tt>
	</body>
</html>