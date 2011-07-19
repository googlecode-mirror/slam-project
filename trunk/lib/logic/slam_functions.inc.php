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

?>