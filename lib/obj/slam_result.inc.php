<?php

class SLAMresult
{
	public $fields;
	public $assets;
	
	function __construct(&$config=false,$db=false,$user=false,$request=false)
	{
		$this->fields = array();
		$this->assets = array();
		$this->counts = array();
	
		if ($config)
		{
			$this->getStructures($config,$db,$user,$request);
			$this->getRecords($config,$db,$user,$request);
		}
	}
	
	public function getStructures(&$config,$db,$user,$request)
	{
		if (!is_array($request->categories))
			return true;
			
		foreach($request->categories as $category => $identifiers)
		{
			$this->fields[$category] = array();
			if(($this->fields[$category] = $db->getStructure($category)) === false)
				die('Error retrieving table info:'.mysql_error());
				
			/* set hidden status for fields */
			foreach($this->fields[$category] as &$field)
				if (in_array($field['name'],$config->values['hide_fields']))
					$field['hidden'] = true;
		}

		return true;
	}
	
	public function getRecords(&$config,$db,$user,$request)
	{
		if (!is_array($request->categories))
			return true;

		foreach($request->categories as $category => $identifiers)
		{
			$this->assets[$category] = array();

			/* if the order-by field isn't in this category, default to Identifier */
			if(!in_array($request->order['field'],array_keys($this->fields[$category])))				
				$request->order['field'] = 'Identifier';
			
			/* retrieve assets from the table */
			if(empty($identifiers) || ($request->action == 'save'))
			{
				$select = "1=1";
				$order = "`".mysql_real_escape($request->order['field'],$db->link)."` ".mysql_real_escape($request->order['direction'],$db->link);
				$limit = ($request->limit > 0) ? "{$request->limit},".($request->limit+$config->values['list_max']) : "0,{$config->values['list_max']}";
			}
			else
			{
				$select = "`Identifier`='".implode("' OR `Identifier`='",$identifiers)."'";
				$order = "`".mysql_real_escape($request->order['field'],$db->link)."` ".mysql_real_escape($request->order['direction'],$db->link);
				$limit = count($identifiers);
			}
			
			$query = SLAM_makePermsQuery($config, $db, $user, '*', $category, $select, $order, $limit);

			if (($this->assets[$category] = $db->getRecords($query)) === false)
				$config->errors[]='Database error: Error retrieving assets:'.mysql_error().$query;
			
			/* count the number of visible assets in the category */
			$query = SLAM_makePermsQuery($config, $db, $user, 'COUNT(*)', $category, $select);
			
			if (($count=$db->getRecords($query)) === false)
				$config->errors[]='Database error: Error counting assets:'.mysql_error().$query;
			
			$this->counts[$category] = $count[0]['COUNT(*)'];
		}

		/* associate the retrieved records with their permissions*/
		$this->getPermissions($config, $db, $user, $request);
		
		return true;
	}
	
	public function getPermissions( &$config, $db, $user, $request )
	{
		/* this function creates a list of all the identifiers in the result and associates their permissions */
		
		# compile the list of identifiers we're to retrieve
		$list = array();
		foreach( $this->assets as $name=>$category)
			foreach( $category as $asset )
				$list[] = "'{$asset['Identifier']}'";
		
		# run a single query to get all available info
		$query = "SELECT * FROM `{$config->values['perms_table']}` WHERE `Identifier` IN (".join(',',$list).')';
		if( ($rows = $db->GetRecords( $query )) === false)
		{
			$config->errors[] = "Error: Could not retrieve permissions for requested assets.".mysql_error();
			return false;
		}
		
		# reconfigure the perms so that the identifier is the key
		$permissions = array();
		foreach( $rows as $row )
			$permissions[ $row['Identifier'] ] = $row;

		$identifiers = @array_keys( $permissions );
		
		# save the permissions to the assets, or outfit them with default perms
		foreach( $this->assets as $category=>&$list)
		{
			foreach( $list as &$asset)
			{
				if( in_array($asset['Identifier'], $identifiers) )
					$asset['Permissions'] = $permissions[ $asset['Identifier'] ];
				else
					SLAM_setDefaultPerms( $config, $asset, null );
			}
		}

		return true;
	}
}

?>