<?php

function SLAM_makeInsertionStatement( $db, $f, $table, $array )
{
	$a = implode("`,`",mysql_real_escape(array_keys($array),$db->link));
	$b = implode("','",mysql_real_escape(array_values($array),$db->link));
	return "$f INTO `$table` (`$a`) VALUES ('$b')";
}

function SLAM_getPermissionsFilter($config,$db,$user,$request,$state='R')
{
	/*
		this function returns a SQL query string that without any additional constraints would return all the entries that user is able to read
		$state = "R", all readable entries
		$state = "RW", all readble and writable entries
	*/

	$a = array();
	$groups = explode(',',$user->groups);

	$a[]="`Permissions` like '{$user->username}:{$state}%;'";
	foreach($user->groups as $group)
		$a[]="`Permissions` like '%;%{$group}%:{$state}%;%'";
	if ($state == 'RW')
		$a[]="`Permissions` like '%;RW'";
	elseif($state == 'R')
		$a[]="`Permissions` like '%;R'";

	return implode(' OR ',$a);
}

function SLAM_getRemovedFilter($config, $user)
{
	return ($user->superuser || $config->values['show_removed']) ? '' : "`Removed`='0'";
}

?>