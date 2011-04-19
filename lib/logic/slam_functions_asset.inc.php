<?php

function SLAM_saveAssetEdits($config,$db,&$user,$request)
{	
	/*
		saves values present in edit_ fields
	*/

	/* get asset values from edit_ fields */
	$record = array();
	foreach($_REQUEST as $key => $value)
		if (preg_match('/^edit_(.+)$/',$key,$m)>0)
			$record[base64_decode($m[1])] = stripslashes($value);

	/* if user isn't the owner or a root user, bail */
	if (($record[$config->values['user_field']] != $user->values['username']) && (!$user->values['superuser']))
		return SLAM_makeErrorHTML('Authentication error: You are not authorized to save edits to this asset.');
		
	/* get the table we're inserting into */
	$category = array_shift(array_keys($request->categories));
	$identifier = $request->categories[$category][0];
	
	/* if no identifier is available, it's a new entry */
	if (empty($identifier))
	{
		$f = 'INSERT';
		
		/* see if there's an entry with the specified identifier already */
		$r = $db->GetRecords("SELECT Identifier FROM `$category` WHERE identifier='$i' LIMIT 1");
		
		if ($r === false)
			return SLAM_makeErrorHTML('Database error: could not check for duplicate identifiers: '.mysql_error());
		elseif(count($r) > 0)
		{
			// pre-existing entry with that identifier!, get next highest asset number and regenerate identifier
			
			if(($results = $db->Query("SHOW TABLE STATUS WHERE `name`='$category'")) === false)
				return SLAM_makeErrorHTML('Database error: error retrieving table status:'.mysql_error());
				
			$row = mysql_fetch_assoc($results);
			if(empty($row))
				return SLAM_makeErrorHTML('Database error: could not get table\'s next available identifier.');
			else
			{
				$record['Serial'] = $row['Auto_increment'];
				$record['Identifier'] = "{$config->values['lab_prefix']}{$config->values['categories'][$category]['prefix']}_{$row['Auto_increment']}";
			}
		}
	}
	else
	{
		$f = 'REPLACE';
		
		/* make sure the asset we're editing belongs to the user */
		$r = $db->GetRecords("SELECT `{$config->values['user_field']}` FROM `$category` WHERE `Identifier`='$identifier' LIMIT 1");
		if (($r[0][$config->values['user_field']] != $user->values['username']) && (!$user->values['superuser']))
			return SLAM_makeErrorHTML('Authentication error: Unauthorized attempt to overwrite record.');
	}
	
	/* generate the SQL statement */
	$a = implode("`,`",array_keys($record));
	$b = implode("','",mysql_real_escape(array_values($record),$db->link));
	$q = "$f INTO `$category` (`$a`) VALUES ('$b')";

	if ($db->Query($q) === false)
		return SLAM_makeErrorHTML('Database error: could not save record: '.mysql_error());
		
	/* tag the asset if we've been asked to */
	if ($_REQUEST['tag'])
	{
		$user->prefs['identifiers'][$category][] = $record['Identifier'];
		$user->savePrefs($config,$db);
	}
	
	return '';
}

function updateNewEntryFields($config,$db,$user,$request,&$result)
{
	/*
		sets some fields of the current asset
	*/

	$category = array_shift(array_keys($request->categories));
	$structure = $result->fields[$category];
	$asset = &$result->assets[$category][0];

	/* if we're not cloning an entry, save default values to all fields */
	if(empty($request->categories[$category][0]))
		foreach($structure as $field => $value)
			$asset[$field]=$value['default'];
		
	/* generate the unique identifiers */
	if(($results = $db->Query("SHOW TABLE STATUS WHERE `name`='$category'")) === false)
		die('Database error: Couldn\'t get table status: '.mysql_error());
	$row = mysql_fetch_assoc($results);

	if (!$user->values['superuser'])
		$asset[$config->values['user_field']] = $user->values['username'];
	
	$asset['Serial'] = $row['Auto_increment'];
	$asset['Identifier'] = "{$config->values['lab_prefix']}{$config->values['categories'][$category]['prefix']}_{$row['Auto_increment']}";
	$asset['Removed'] = '0';

	return;
}

function SLAM_deleteAssets(&$config,$db,&$user,&$request)
{
	/*
		Drops the records specified in request
	*/
	
	/* drop them from the user's prefs first */
	SLAM_dropAssetTags($config,$db,&$user,$request);
	foreach($request->categories as $category=>$identifiers)
	{
		if (!is_array($identifiers))
			return;
			
		foreach($identifiers as $i=>$identifier)
		{
			/* non-super_users have to provide their name to match the entry */
			if ($user->values['superuser'])
				$q = "UPDATE `$category` SET `Removed`='1' WHERE `Identifier`='$identifier' LIMIT 1";
			else
				$q = "UPDATE `$category` SET `Removed`='1' WHERE (`{$config->values['user_field']}`='{$user->values['username']}' AND `Identifier`='$identifier') LIMIT 1";
			
			if (($result = $db->Query($q)) === false)
		 		return SLAM_makeErrorHTML('Database error: asset removal failure: '.mysql_error());
			
			/* remove from the request as well (mainly to remove from the breadcrumb trail) */
			unset($request->categories[$category][$i]);
			
			/* remove the file archive */
			if (($result == 1) && ($config->values['file manager']['delete_archives']))
				SLAM_deleteAssetFiles($config,$category,$identifier);
		}
	}
}

?>