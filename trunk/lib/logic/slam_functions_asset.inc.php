<?php

function SLAM_getAssetFields($config,$db,$user,$assets)
{	
	/*
		composes the consensus values for each field from all the provided assets	
	*/
	
	/* compile a list of shared fields in all the assets */
	
	$fields = array_keys($assets[0]);
	foreach($assets as $asset)
		$fields = array_intersect( $fields, array_keys($asset) );
	
	$ret = array();
	foreach($fields as $field)
	{
		$all_values = array();
		foreach ($assets as $v)
			$all_values[] = $v[ $field ];
		
		$values = array_unique($all_values);
			
		if (count($values) == 1)
			$ret[ $field ] = $values[0];
		else
			$ret[ $field ] = '(multiple)';
	}
	return $ret;
}

function SLAM_getNewAssetFields($config,$db,$user,$category,$structure,$clone=null)
{
	/*
		returns fields set to default values, or from a clone if provided
	*/

	$fields = array();

	/* if we're not cloning an entry, save default values to all fields */
	foreach($structure as $name => $value)
	{
		if ($clone != null)
			$fields[ $name ]=$clone[ $name ];
		else
			$fields[ $name ]=$value['default'];
	}
	
	/* set the specified user fields, if needed */
	if (is_array($config->values['user_field']))
		foreach($config->values['user_field'] as $field)
			if ($fields[ $name ] == '')
				$fields[ $name ] = $user->username;

	/* set the specified date fields, if needed */
	if (is_array($config->values['date_field']))
		foreach($config->values['date_field'] as $field)
			if ($fields[ $name ] == '')
				$fields[ $name ] = date('Y-m-d');
	
	/* set permissions */
	$a = split(';',$clone['Permissions']); // do it this way in case the the clone entry has malformed permissions
	if (count($a) < 3)
	{
		$a = array();
		/* 0 - me
		 * 1 - groups
		 * 2 - everyone
		 */
		for ($i = 1; $i<=$user->prefs['default_entryReadable']; $i++)
			$a[ $i ] = 'R';
		for ($i = 1; $i<=$user->prefs['default_entryEditable']; $i++)
			$a[ $i ].= 'W';
		
		$groups = join(',',$user->groups);
		$fields['Permissions'] = "$user->username:RW;$groups:{$a[1]};{$a[2]}";
	}
	else
		$fields['Permissions'] = "$user->username:RW;{$a[1]};{$a[2]}";
	
	/* set user-default project */
	if ($clone != null)
	{
		if ((!empty($user->prefs['default_project'])) && (!empty($fields['Project'])))
			$fields[ 'Project'] = $user->prefs['default_project'];
	}
			
	/* generate the unique identifier */
	if(($results = $db->Query("SHOW TABLE STATUS WHERE `name`='$category'")) === false)
		die('Database error: Couldn\'t get table status: '.mysql_error());
	$row = mysql_fetch_assoc($results);
	
	$fields['Serial'] = $row['Auto_increment'];
	$fields['Identifier'] = "{$config->values['lab_prefix']}{$config->values['categories'][$category]['prefix']}_{$row['Auto_increment']}";
	$fields['Removed'] = '0';

	return $fields;
}

function SLAM_saveAssetEdits($config,$db,&$user,$request)
{	
	/*
		saves values present in edit_ fields to a new entry or to existing entries as necessary
		Returns a string, possibly containing a description of the error
	*/

	/* get asset values from fields that start with "edit_" */
	$fields = array();
	foreach($_REQUEST as $key => $value)
		if (($value != '(multiple)') && (preg_match('/^edit_(.+)$/',$key,$m) > 0))
			$fields[base64_decode($m[1])] = stripslashes($value);
	
	/* permissions field is special */
	if ($_REQUEST['Permissions'] && ($_REQUEST['Permissions'] != '(multiple'))
		$fields['Permissions'] = base64_decode($_REQUEST['Permissions']);
	
	/* go through all the tables we need to insert into */
	foreach($request->categories as $category=>$identifiers)
	{	
		/* identifiers is empty, we must be creating a new entry */
		if (!is_array($identifiers))
		{
			$asset = array('category'=>$category, 'fields'=>$fields);

			if ( ($s = insertNewAsset( $config, $db, $user, $asset)) !== True)
				return $s;
		}
		
		/* go through all the assets we have and update the changed fields */
		foreach($identifiers as $i=>$identifier)
		{		
			$fields['Identifier'] = $identifier;
			$asset = array('category'=>$category, 'fields'=>$fields);
			
			if (($s = replaceExistingAsset( $config, $db, $user, $asset)) !== True)
				return $s;
		}
	}
	
	$user->savePrefs($config,$db);
	
	return '';
}

function insertNewAsset( $config, $db, $user, $asset )
{
	/* see if there's an entry with the specified identifier already */
	$r = $db->GetRecords("SELECT * FROM `{$asset['category']}` WHERE identifier='{$asset['fields']['Identifier']}' LIMIT 1");
	
	if ($r === false)
		 return SLAM_makeErrorHTML('Database error: could not check for duplicate identifiers: '.mysql_error(),true);

	if(count($r) > 0)
	{
		// pre-existing entry with that identifier!, get next highest asset number and regenerate identifier
		
		if(($results = $db->Query("SHOW TABLE STATUS WHERE `name`='{$asset['category']}'")) === false)
			return SLAM_makeErrorHTML('Database error: error retrieving table status:'.mysql_error(),true);
			
		$row = mysql_fetch_assoc($results);
		if(empty($row))
			return SLAM_makeErrorHTML('Database error: could not get table\'s next available identifier.',true);
		else
		{
			$asset['fields']['Serial'] = $row['Auto_increment'];
			$asset['fields']['Identifier'] = "{$config->values['lab_prefix']}{$config->values['categories'][ $asset['category'] ]['prefix']}_{$row['Auto_increment']}";
		
			$q = SLAM_makeInsertionStatement( $db, 'INSERT', $asset['category'], $asset['fields'] );
			if ($db->Query($q) === false)
				return SLAM_makeErrorHTML('Database error: could not save record: '.mysql_error(),true);			
		}
	}
		
	return True;
}

function replaceExistingAsset( $config, $db, $user, $asset )
{
	$r = $db->GetRecords("SELECT * FROM `{$asset['category']}` WHERE `Identifier`='{$asset['fields']['Identifier']}' LIMIT 1");

	/* make sure we're not editing a removed asset */
	if (($r[0]['Removed'] == '1') && (!$config->values['edit_removed']) && (!$user->superuser))
		return SLAM_makeErrorHTML('Authentication error: Unauthorized attempt to edit removed record.',true);
	elseif (SLAM_getAssetPermission($user,$r[0]) < 2)
		return SLAM_makeErrorHTML('Authentication error: You are not authorized to save edits to this asset.',true);
	
	$q = SLAM_makeInsertionStatement( $db, 'REPLACE', $asset['category'], $asset['fields'] );
	if ($db->Query($q) === false)
		return SLAM_makeErrorHTML('Database error: could not save record: '.mysql_error(),true);
		
	return True;
}

function SLAM_deleteAssets(&$config, $db, &$user, &$request)
{
	/*
		Drops the records specified in request
	*/
	
	/* drop them from the user's prefs first */
	SLAM_dropAssetTags($config,$db,&$user,$request);
	
	/* iterate through the categories and assets to be dropped */
	foreach($request->categories as $category=>$identifiers)
	{
		if (!is_array($identifiers))
			return;
			
		foreach($identifiers as $i=>$identifier)
		{
			/* get the entry to check permissions */
			$r = $db->GetRecords("SELECT Permissions FROM `$category` WHERE identifier='$identifier' LIMIT 1");
			
			if ($r === false)
				return SLAM_makeErrorHTML('Database error: could not check for presence of specified identifier: '.mysql_error(),true);
			elseif(count($r) < 1)
				return SLAM_makeErrorHTML('Database error: specified asset was not found.',true);
		
			/* non-super_users have to provide their name to match the entry */
			if (SLAM_getAssetPermission($user,$r) > 2)
				$q = "UPDATE `$category` SET `Removed`='1' WHERE `Identifier`='$identifier' LIMIT 1";
			else
				return SLAM_makeErrorHTML('Authentication error: You are not authorized to remove this asset.',true);
			
			/* attempt to run the query */
			if (($result = $db->Query($q)) === false)
		 		return SLAM_makeErrorHTML('Database error: asset removal failure: '.mysql_error(),true);
			
			/* remove from the request as well (mainly to remove from the breadcrumb trail) */
			unset($request->categories[$category][$i]);
			
			/* remove the file archive */
			if (($result == 1) && ($config->values['file manager']['delete_archives']))
				SLAM_deleteAssetFiles($config,$category,$identifier);
		}
	}
	
	return True;
}

?>