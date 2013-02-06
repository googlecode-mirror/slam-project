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
			$this->getPermissions($config, $db, $user, $request);
		}
	}
	
	public function getStructures(&$config,$db,$user,$request)
	{
		if (!is_array($request->categories))
			return true;
			
		foreach($request->categories as $category=>$identifiers)
		{
			if(($structure = $db->getStructure($category)) === false)
				die('Error retrieving table info:'.$db->ErrorState());
				
			/* set hidden status for fields */
			foreach($structure as &$field)
				if (in_array($field['name'],$config->values['hide_fields']))
					$field['hidden'] = true;
				
			/* set the correct field ordering */
			$fields = $config->categories[ $category ]['field_order'];
			$fields = array_merge( $fields, array_keys($structure) );
			array_unshift( $fields, 'permissions' );
			array_unshift( $fields, 'Identifier' );
			array_push( $fields, 'Files');
			$fields = array_unique( $fields );
			
			/* now build the actual structure */
			$this->fields[ $category ] = array();
			foreach( $fields as $name  )
			{
				if( $name == '' )
					pass;
				elseif( is_array($structure[ $name ]) )
					$this->fields[ $category ][ $name ] = $structure[ $name ];
				else
					$this->fields[ $category ][ $name ] = array();
			}
		}
		return true;
	}
	
	public function getRecords(&$config,$db,$user,$request)
	{
		if (!is_array($request->categories))
			return true;
		if($request->action == 'new')
			return true;

		foreach($request->categories as $category => $identifiers)
		{
			$this->assets[$category] = array();

			/* if the order-by field isn't in this category, default to Identifier */
			if(!in_array($request->order['field'],array_keys($this->fields[$category])))				
				$request->order['field'] = 'Identifier';
			
			/* convert identifiers to numeric sort */
			if($request->order['field'] == 'Identifier')
				$order = 'CAST(SUBSTR(`Identifier`,6) AS SIGNED) '.sql_real_escape($request->order['direction'],$db->link);
			else
				$order = "`".sql_real_escape($request->order['field'],$db->link)."` ".sql_real_escape($request->order['direction'],$db->link);
			
			/* retrieve assets from the table */
			if(empty($identifiers) || ($request->action == 'save'))
			{
				$select = "1=1";				
				$limit = ($request->limit > 0) ? "{$request->limit},".($request->limit+$config->values['list_max']) : "0,{$config->values['list_max']}";
			}
			else
			{
				$select = "`Identifier`='".implode("' OR `Identifier`='",$identifiers)."'";
				$limit = count($identifiers);
			}
			
			$query = SLAM_makePermsQuery($config, $db, $user, '*', $category, $select, $order, $limit);

			if (($this->assets[$category] = $db->getRecords($query)) === false)
				$config->errors[]='Database error: Error retrieving assets:'.$db->ErrorState().$query;
			
			/* count the number of visible assets in the category */
			$query = SLAM_makePermsQuery($config, $db, $user, 'COUNT(*)', $category, $select);
			
			if (($count=$db->getRecords($query)) === false)
				$config->errors[]='Database error: Error counting assets:'.$db->ErrorState().$query;
			
			$this->counts[$category] = $count[0]['COUNT(*)'];
		}
		
		return true;
	}
	
	public function getPermissions( &$config, $db, $user, $request )
	{
		/*
		 * this function creates a list of all the identifiers in the result and associates their permissions
		 */
				
		# compile the list of identifiers we're to retrieve
		$list = array();
		foreach( $this->assets as $category)
			foreach( $category as $asset )
				$list[] = "'{$asset['Identifier']}'";
		
		if( count($list) < 1 )
			return true;
				
		# run a single query to get all available info
		$query = "SELECT * FROM `{$config->values['perms_table']}` WHERE `Identifier` IN (".join(',',$list).')';
		if( ($rows = $db->GetRecords( $query )) === false)
		{
			$config->errors[] = "Error: Could not retrieve permissions for requested assets.".$db->ErrorState();
			return false;
		}
		
		# reconfigure the perms so that the identifier is the key
		$permissions = array();
		foreach( $rows as $row )
		{
			$permissions[ $row['Identifier'] ] = $row;
			$permissions[ $row['Identifier'] ]['projects'] = explode(',',$permissions[ $row['Identifier'] ]['projects']);
		}

		$identifiers = @array_keys( $permissions );
		
		# save the permissions to the assets, or outfit them with default perms
		foreach( $this->assets as $category=>&$list)
		{
			foreach( $list as &$asset)
			{
				if( in_array($asset['Identifier'], $identifiers) )
					$asset['permissions'] = $permissions[ $asset['Identifier'] ];
				else
					SLAM_setDefaultPerms( $asset, $config );
			}
		}

		return true;
	}
}

?>