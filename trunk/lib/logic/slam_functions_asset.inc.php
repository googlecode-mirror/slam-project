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
			$ret[ $field ] = $config->values['multiple_value'];
	}
	return $ret;
}

function SLAM_setAssetFields($config,$db,$user,$category,$structure,$clone=false)
{
	/*
		returns fields set to default values, or from a clone if provided
	*/

	$fields = array();

	/* if we're not cloning an entry, save default values to all fields */
	foreach($structure as $name => $value)
	{
		if ($clone)
			$fields[ $name ]=$clone[ $name ];
		else
			$fields[ $name ]=$value['default'];
	}
					
	/* set permissions */
	SLAM_setDefaultPerms( $fields, $config, $user, $clone );
	if(array_key_exists($config->values['permissions']['owner_field'],$fields))
		$fields[$config->values['permissions']['owner_field']] = $user->username;
		
	if (!$clone)
	{
		/* set user-default project */
		if ( ($user->prefs['default_project'] != '') && ($fields['Project'] == ''))
			$fields[ 'Project'] = $user->prefs['default_project'];
		
		/* set the specified user fields, if needed */
		if (is_array($config->values['user_fields']))
			foreach($config->values['user_fields'] as $name)
				$fields[ $name ] = $user->username;

		/* set the specified date fields, if needed */
		if (is_array($config->values['date_fields']))
			foreach($config->values['date_fields'] as $name)
				$fields[ $name ] = date('Y-m-d');
	}
			
	/* generate the unique identifier */
	if(($results = $db->Query("SHOW TABLE STATUS WHERE `name`='$category'")) === false)
		die('Database error: Couldn\'t get table status: '.mysql_error());
	$row = mysql_fetch_assoc($results);
	
	$fields['Serial'] = $row['Auto_increment'];
	$fields['Identifier'] = "{$config->values['lab_prefix']}{$config->categories[$category]['prefix']}_{$row['Auto_increment']}";
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
	$asset = array();

	foreach($_REQUEST as $key => $value)
		if( $value != $config->values['multiple_value'] )
			if( preg_match('/^edit_(.+)$/',$key,$m) > 0)
				$asset[base64_decode($m[1])] = stripslashes($value);

	/* go through all the tables we need to insert into */
	foreach($request->categories as $category=>$identifiers)
	{
		/* if permissions field is the multiple field value, don't overwrite asset's existing permissions */
		if ($_REQUEST['permissions'] && ($_REQUEST['permissions'] == $config->values['multiple_value']))
			$asset['permissions'] = false;
		elseif( $_REQUEST['permissions'] )
			$asset['permissions'] = json_decode(base64_decode($_REQUEST['permissions']));
		
		/* identifiers is empty, we must be creating a new entry */
		if (!is_array($identifiers))
			if ( ($s = insertNewAsset( $config, $db, $user, $category, $asset)) !== True)
				return $s;
		
		/* go through all the assets we have and update the changed fields */
		foreach($identifiers as $identifier)
		{
			/* replace the insertion statement identifier with the correct one */
			$asset['Identifier'] = $identifier;
			
			if (($s = replaceExistingAsset( $config, $db, $user, $category, $asset)) !== True)
				return $s;
		}
	}
	
	$user->savePrefs($config,$db);
	
	# returns nothing on success (maybe a status message in the future?)
	return '';
}

function insertNewAsset( $config, $db, $user, $category, $asset )
{
	/* see if there's an entry with the specified identifier already */
	$r = $db->GetRecords("SELECT * FROM `$category` WHERE identifier='{$asset['Identifier']}' LIMIT 1");
	
	if ($r === false)
		 return SLAM_makeErrorHTML('Database error: could not check for duplicate identifiers: '.mysql_error(),true);

	if(count($r) > 0)
	{
		// pre-existing entry with that identifier!, get next highest asset number and regenerate identifier
		
		if(($results = $db->Query("SHOW TABLE STATUS WHERE `name`='$category'")) === false)
			return SLAM_makeErrorHTML('Database error: error retrieving table status:'.mysql_error(),true);
			
		$row = mysql_fetch_assoc($results);
		if(empty($row))
			return SLAM_makeErrorHTML('Database error: could not get table\'s next available identifier.',true);
		else
		{
			$asset['Serial'] = $row['Auto_increment'];
			$asset['Identifier'] = "{$config->values['lab_prefix']}{$config->categories[ $category ]['prefix']}_{$row['Auto_increment']}";
		}
	}
	
	/* separate the permissions from the asset attributes to be saved */
	$permissions = (array)$asset['permissions'];
	unset($asset['permissions']);
	
	/* insert the asset attributes into the database */
	$q = SLAM_makeInsertionStatement( $db, 'INSERT', $category, $asset );
	if ($db->Query($q) === false)
		return SLAM_makeErrorHTML('Database error: could not save record: '.mysql_error(),true);
	
	/* save the permissions as well */
	$asset['permissions'] = $permissions;
	if( ($ret = SLAM_saveAssetPerms($config, $db, $asset)) !== true)
		return $ret;
	
	return True;
}

function replaceExistingAsset( $config, $db, $user, $category, $asset )
{
	/* save the asset perms for now */
	$permissions = (array)$asset['permissions'];
	unset($asset['permissions']);
	
	/* don't trust the user-provided asset, check permissions separately */
	$old_perms = $db->GetRecords("SELECT * FROM `{$config->values['perms_table']}` WHERE `Identifier`='{$asset['Identifier']}' LIMIT 1");
	if( count($old_perms) == 1 )
		$asset['permissions'] = $old_perms[0];
	else
		SLAM_setDefaultPerms( $asset, $config );
	
	/* verify that the current user is qualified */
	if (SLAM_getAssetAccess($user,$asset) < 2)
		return SLAM_makeErrorHTML('Authentication error: You are not authorized to save edits to this asset.',true);

	/* don't try and save the permissions field into the asset table */
	unset($asset['permissions']);
	
	$q = SLAM_makeUpdateStatement( $db, $category, $asset, "`Identifier`='".mysql_real_escape($asset['Identifier'],$db->link)."'", 1 );

	if ($db->Query($q) === false)
		return SLAM_makeErrorHTML('Database error: could not save record: '.mysql_error(),true);
	
	$asset['permissions'] = $permissions;
	if( ($ret = SLAM_saveAssetPerms($config, $db, $asset)) !== true)
		return $ret;
	
	return True;
}

function SLAM_saveAssetPerms(&$config, $db, $asset)
{
	if( !$asset['permissions'] )
		return true;
	
	/* permissions are sometimes provided as a JSON object */
	$permissions = (array)$asset['permissions'];
	$permissions['Identifier'] = $asset['Identifier'];
	$permissions['projects'] = join(',',$permissions['projects']);

	$q = SLAM_makeInsertionStatement( $db, 'REPLACE', $config->values['perms_table'], $permissions );	

	if ($db->Query($q) === false)
		return SLAM_makeErrorHTML('Database error: could not save record permissions: '.mysql_error(),true);

	return true;
}

function SLAM_deleteAssets(&$config, $db, &$user, &$request)
{
	/*
		Drops the records specified in request
	*/
	
	$result = new SLAMresult($config, $db, $user, $request);
	
	/* drop them from the user's prefs first */
	SLAM_dropAssetTags($config,$db,&$user,$request);
	
	/* iterate through the categories and assets to be dropped */
	foreach($result->assets as $category=>$assets)
	{
		foreach($assets as $i=>$asset)
		{
			if (SLAM_getAssetAccess($user,$asset) > 1)
				$q ="UPDATE `$category` SET `Removed`='1' WHERE `Identifier`='{$asset['Identifier']}' LIMIT 1";
			else
				return SLAM_makeErrorHTML('Authentication error: You are not authorized to remove this asset.',true);
			
			/* attempt to run the query */
			if (($result = $db->Query($q)) === false)
		 		return SLAM_makeErrorHTML('Database error: asset removal failure: '.mysql_error(),true);
			
			/* remove from the request as well (mainly to remove from the breadcrumb trail) */
			unset($request->categories[$category][$i]);
		}
	}
	
	# returns nothing on success (maybe a status message in the future?)
	return '';
}

function SLAM_setDefaultPerms( &$asset, $config, $user=false, $clone=false )
{
	/* sets an asset's permissions array to stand in for output from the SLAM_perms table */

	$asset['permissions'] = array();

	if ($user)
		$asset['permissions']['owner'] = $user->username;
	elseif( ($config->values['permissions']['default_owner'] == '') && ($config->values['permissions']['owner_field'] != '') )
		$asset['permissions']['owner'] = $asset[ $config->values['permissions']['owner_field'] ];
	elseif( $config->values['permissions']['default_owner'] != '' )
		$asset['permissions']['owner'] = $config->values['permissions']['default_owner'];
	else
		$asset['permissions']['owner'] = null;
	
	/* a shortcut if we're just cloning an existing asset */
	if( ($clone != false) && ($user != false) )
	{
		$asset['permissions'] = $clone['permissions'];
		$asset['permissions']['owner'] = $user->username;
		$asset['permissions']['owner_access'] = 2;
		return;
	}
	
	$asset['permissions']['owner_access'] = (int)$config->values['permissions']['default_owner_access'];

	/* if first project of config file defaults is empty, autopopulate with specified project field */
	if( ($config->values['permissions']['default_projects'][0] == '') && ($config->values['permissions']['project_field'] != '') )
		$default_project = array($asset[ $config->values['permissions']['project_field'] ]);

	if ($user !== false)
		$asset['permissions']['projects'] = $user->projects;
	elseif( is_array( $default_project ) )
		$asset['permissions']['projects'] = $default_project;
	else
		$asset['permissions']['projects'] = array();
	
	if( is_numeric($user->prefs['default_project_access']) )
		$asset['permissions']['project_access'] = $user->prefs['default_project_access'];
	else
		$asset['permissions']['project_access'] = (int)$config->values['permissions']['default_project_access'];
	
	if( is_numeric($user->prefs['default_access']) )
		$asset['permissions']['default_access'] = $user->prefs['default_access'];
	else
		$asset['permissions']['default_access'] = (int)$config->values['permissions']['default_access'];

	return true;
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

function SLAM_getAssetAccess($user,$asset)
{
	/*
	returns the access the provided user has for the provided asset:
	
	0 = no access
	1 = read access
	2 = read + write access
	
	*/
	if ($user->superuser)
		return 2;
	
	/* start with default access afforded to all users */
	$access = $asset['permissions']['default_access'];

	/* is the user the owner? */
	if( $asset['permissions']['owner'] == $user->username )
		if( $asset['permissions']['owner_access'] > $access )
			$access = $asset['permissions']['owner_access'];
	
	if( ! is_array($asset['permissions']['projects']) )
		$asset['permissions']['projects'] = array();
		
	/* check user projects against asset projects */
	foreach($user->projects as $project)
		if( in_array( $project, $asset['permissions']['projects'] ) )
			if( $asset['permissions']['project_access'] > $access )
				$access = $asset['permissions']['project_access'];
	
	return $access;
}

?>