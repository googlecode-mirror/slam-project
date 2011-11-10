<?php

function mysql_real_escape($a,$link)
{
	/*
		recursively mysql_real_escape_string's an array or a string
	*/
	
	if (!is_array($a))
		return mysql_real_escape_string($a,$link);
	
	foreach($a as $k => $v)
		$a[$k] = (is_array($v)) ? mysql_real_escape($v,$link) : mysql_real_escape_string($v,$link);
	
	return $a;
}

function SLAM_findAssetDiffs($assets)
{
	if(count($assets) < 2)
		return false;
	
	$ret = array();
	$fields = array_keys($assets[0]);
	foreach($fields as $field)
	{
		$temp = array();
		foreach($assets as $asset)
			$temp[] = $asset[$field];
		
		$temp = array_unique($temp);
		
		if(count($temp) > 1)
			$ret[$field] = $temp;
	}

	return $ret;	
}


function SLAM_getAssetPermissions($user,$asset)
{
	/*
	returns the permissions the provided user has upon the provided asset:
	
	0 = no access
	1 = read access
	3 = read + write access
	
	*/

	if ($user->values['superuser'])
		return 3;

	$ret = 0;
	list($o,$g,$u) = explode(';',$asset['Permissions']);
	
	$a = explode(':',$o);
	if ($user->values['username'] == $a[0])
	{
		if ($a[1] == 'R')
			$ret = 1;
		if ($a[1] == 'RW')
			$ret = 3;
	}

	$a = explode(':',$g);
	$groups = explode(',',$a[0]);
	if ( count(array_intersect($groups,$user->values['groups'])) > 0 )
	{
		if (($a[1] == 'R') && ($ret < 3))
			$ret = 1;
		if ($a[1] == 'RW')
			$ret = 3;
	
	}
	
	if (($u == 'R') && ($ret < 3))
		$ret = 1;
	elseif ($u == 'RW')
		$ret = 3;
		
	return $ret;
}

?>