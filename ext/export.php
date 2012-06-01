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
			break;
	}

	switch($request->location)
	{
		case 'list':
		if (!$result)
				$result = new SLAMresult($config,$db,$user,$request);
			break;
		case 'dash':
		default:
			if (!$result)
				$result = SLAM_getDashboardResult($config,$db,$user,$request);
			break;
	}
	
	$empty = true;
	$output = '';
	
	foreach($result->assets as $category => $assets)
	{
		if (count($assets)>0)
		{
			/* table identity */
			if (count(array_keys($result->assets)) > 1)
				$output.= makeCSVEntry(array($category));
	
			/* table column types */
			$output.= makeCSVEntry(array_keys($assets[0]));
	
			/* table data */
			foreach($assets as $asset)
				$output.= makeCSVEntry($asset);
				
			if (count($assets)>0)
				$empty = false;
		}
	}
	
	if ($empty)
		die('No results to export');
		
	header("Content-type: text/csv");
	header("Content-Disposition: attachment; filename=export.csv");
	echo $output;
	exit;
}
else
	die("User is not authorized. Please <a href='{$config->html['url']}'>log in</a>");

function makeCSVEntry($a)
{
	foreach($a as $k=>$v)
	{
		if( is_array($v) ) /* permissions value */
			$v = '';
		
		$a[$k]=str_replace('"','""',stripslashes($v));
		if (preg_match('/[\r\n\,\"]/',$v)>0)
			$a[$k]="\"{$a[$k]}\"";
	}
	return implode(',',$a)."\n";
}

?>