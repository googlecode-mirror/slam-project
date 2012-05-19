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
	
	$array = false;
	foreach($result->assets as $category => $assets)
		if (count($assets)>0)
			break;
		
	if ( count($assets) > 0)
	{
		# retrieve the array from the provided results array
		$array = $assets[0];
		
		$identifier = $array['Identifier'];
		
		# remove all hidden fields
		foreach( $config->values['hide_fields'] as $v)
			unset( $array[$v] );
		
		$content = makeTableHTML( $array );
	
		# header array of values
		$array = array(
				'Date'=>date('D, j M Y H:i:s O'),
				'SLAM version'=>$config->values['version'],
				'Lab name'=>$config->values['name'],
				'User'=>$user->username,
				'Email'=>$user->email);

		$header = makeTableHTML( $array );
								
		# footer array of values
		$array = array();
				
		$footer = makeTableHTML( $array );
	}
	else
	{
		$content = 'No results to export';
		$title = 'Error';
	}
}
else
	die("User is not authorized. Please <a href='{$config->html['url']}'>log in</a>");
	
	
function makeTableHTML( $array )
{
	$keys = array_keys($array);
	
	$o = "<table>\n";
	for( $i=0; $i<count($keys); $i++)
	{
		$o.= ($i % 2 == 0) ? "<tr class='even'>" : "<tr class='odd'>";
		$o.="<td class='printFieldTitle'>{$keys[$i]}</td><td class='printFieldValue'>{$array[$keys[$i]]}</td></tr>\n";
	}
	$o.="</table>\n";
	return $o;
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0//EN">
<html>
	<head>
		<title><?php echo "{$config->values['name']}:$identifier"; ?></title>
		<link type='text/css' href='../css/print.css' rel='stylesheet' />
	</head>
	<body onload="window.print()">
		<div id='printHeader'>
			<?php echo $header; ?>
		</div>
		<div id='printIdentifier'>
			Identifier: <?php echo $identifier ?>
		</div>
		<div id='printCategory'>
			Category: <?php echo $category ?>
		</div>
		<div id='printContent'>
			<?php echo $content; ?>
		</div>
		<div id='printFooter'>
			<?php echo $footer; ?>
		</div>
	</body>
</html>