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

function SLAM_getAssetPerms($asset)
{
	list($o,$g,$u) = explode(';',$asset['Permissions']);
	
	$ret = array('owner'=>array(),'group'=>array(),'user'=>array());
	
	$a = explode(':',$o);
	$ret['owner']['name'] = $a[0];
	$ret['owner']['value'] = $a[1];
	if ($a[1] == 'R')
		$ret['owner']['R'] = true;
	elseif ($a[1] == 'RW'){
		$ret['owner']['R'] = true;
		$ret['owner']['W'] = true;
	}

	$groups = explode(':',$g);
	$status = array_pop($groups);
	foreach ($groups as $group)
	{
		$ret['group'][$group] = array();
		$ret['group'][$group]['value'] = $status;
		if ($status == 'R')
			$ret['group'][$group]['R'] = true;
		elseif ($status == 'RW'){
			$ret['group'][$group]['R'] = true;
			$ret['group'][$group]['W'] = true;
		}
	}
	
	$ret['user']['value'] = $u;
	if ($u == 'R')
		$ret['user']['R'] = true;
	elseif ($u == 'RW'){
		$ret['user']['R'] = true;
		$ret['user']['W'] = true;
	}
	
	return $ret;
}

function SLAM_getAssetRWStatus($user,$asset)
{
	$perms = SLAM_getAssetPerms($asset);
	
	$r_status = false;
	$w_status = false;
	
	if ($user->values['superuser'])
	{
		$r_status = true;
		$w_status = true;
	}
	
	/* check if we're the owner */
	if (($perms['user']['R']) && ($user->values['username'] == $perms['owner']['name']))
		$r_status = true;
	if (($perms['user']['W']) && ($user->values['username'] == $perms['owner']['name']))
		$w_status = true;
	
	/* check the availablility to all users */
	if ($perms['user']['R'])
		$r_status = true;
	if ($perms['user']['W'])
		$w_status = true;
	
	/* check our status as a group member */
	$user_groups = explode(',',$user->values['groups']);
	foreach($user_groups as $user_group)
	{
		foreach($perms['group'] as $group)
		{
			if($user_group == $group)
			{
				if ($perms['group'][$group]['R'])
					$r_status = true;
				if ($perms['group'][$group]['W'])
					$w_status = true;
				break;
			}
		}
	}
		
	$s = '';
	if ($r_status)
		$s.='R';
	if ($w_status)
		$s.='W';
	
	return $s;
}

?>