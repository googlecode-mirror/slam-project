<?php

function SLAM_makeArchiveFilesHTML($config,$db,$category,$identifier,$files,$editable)
{
	$s = "<table id='fileListTable'>\n";
	$s.= "<tr id='fileListHeader'>\n";
	if ($editable)
		$s.="<td>Delete?</td>\n";
	$s.= <<<EOL
<td>Download</td>
<td>Size</td>
<td>Date</td>
<td>Name</td>
</tr>\n
EOL;
	
	$i=0;
	foreach($files as $name=>$info)
	{
		/* use the appropriate tr class */
		$s.= (($i+1)%2 == 0) ? "<tr class='fileListRowEven'>\n" : "<tr class='fileListRowOdd'>\n";
		
		$size = round(($info['size']/$config->values['file manager']['size_divisor'])).$config->values['file manager']['size_unit'];
		$pathinfo = pathinfo($name);
		$date = str_replace('-','/',$info['date']);
		
		if ($editable)
			$s.="<td style='text-align:center'><input type='checkbox' name='FL_del-".base64_encode($name)."' /></td>";
			
		$save_url = "download.php?i={$identifier}&asset_file=".base64_encode($name);
		$view_url = "download.php?i={$identifier}&asset_file=".base64_encode($name)."&inline=true";
		
		if (in_array(strtolower($pathinfo['extension']),$config->values['file manager']['inline_formats']))
			$open_url = "<a href='$save_url'>save</a>/<a href='$view_url' target='_new'>view</a>";
		else
			$open_url = "<a href='$save_url'>save</a>";
			
		/* smash together the row */
		$s.= <<<EOL
<td style='text-align:center'>$open_url</td>
<td style='font-size: 75%'>$size</td>
<td style='font-size: 75%'>$date {$info['time']}</td>
<td id='asset_file_{$i}'>$name</td>
</tr>\n
EOL;
		$i++;
	}
	
	return $s."</table>\n";
}

function SLAM_makeFileSplashHTML($identifier,$title,$desc,$errors)
{
	$s = <<<EOL
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0//EN">
<html>
	<head>
		<title>$title</title>
	</head>
	<body>
		<div style="width:400;margin-left:auto;margin-right:auto;text-align:center">
		<b><i>$desc</i></b>
		<br />
		<img src="../img/grey_loader_dots.gif" width="43" height="11" alt="[loading dots]" vertical-align="middle" />
EOL;
	
	foreach($errors as $error)
		$s.="<p style='color:red'>$error</p>\n";
	
	$s.= <<<EOL
			<br />
			<br />
			<br />
			<b><i>Please click <a href='../ext/?i=$identifier'>here</a> to continue.</b></i>
		</div>
	</body>
</html>
EOL;

	return $s;
}

?>