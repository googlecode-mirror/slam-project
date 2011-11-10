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
	
	public function makePermissionsFilter(&$config,$db,$user,$request,$state='R')
	{
		/*
			this function returns a SQL query string that without any additional constraints would return all the entries that user is able to read
			$state = "R", all readable entries
			$state = "RW", all readble and writable entries
		*/
		
		$a = array();
		$groups = explode(',',$user->values['groups']);
		
		$a[]="(`Permissions` like '{$user->values['username']}:{$state}%;')";
		foreach($user->values['groups'] as $group)
			$a[]="(`Permissions` like '%;%{$group}%:{$state}%;%')";
		if ($state == 'RW')
			$a[]="(`Permissions` like '%;RW')";
		elseif($state == 'R')
			$a[]="(`Permissions` like '%;R')";
		
		return implode(' OR ',$a);
	}
	
	public function getRecords(&$config,$db,$user,$request)
	{
		if (!is_array($request->categories))
			return true;
		
		/* retrieve the user permissions filter string */
		$perms = $this->makePermissionsFilter($config,$db,$user,$request,'R');

		foreach($request->categories as $category => $identifiers)
		{
			$this->assets[$category] = array();

			/* check that the order-by field is appropriate for this category */
			if(!in_array($request->order['field'],array_keys($this->fields[$category])))				
				$request->order['field'] = 'Identifier';
			
			
			/* retrieve assets from the table */
			if(empty($identifiers) || ($request->action == 'save'))
			{
				$order = "ORDER BY `".mysql_real_escape($request->order['field'],$db->link)."` ".mysql_real_escape($request->order['direction'],$db->link);
				$filter = ($user->values['superuser'] || $config->values['show_removed']) ? '' : "WHERE (($perms) AND `Removed`='0')";
				$limit = ($request->limit > 0) ? "LIMIT {$request->limit},".($request->limit+$config->values['list_max']) : "LIMIT 0,{$config->values['list_max']}";
			}
			else
			{
				$selector = "`Identifier`='".implode("' OR `Identifier`='",$identifiers)."'";
				$order = "ORDER BY `".mysql_real_escape($request->order['field'],$db->link)."` ".mysql_real_escape($request->order['direction'],$db->link);
				$filter = ($user->values['superuser'] || $config->values['show_removed']) ? "WHERE($selector)" : "WHERE (($selector) AND ($perms) AND `Removed`='0')";
				$limit = "LIMIT ".count($identifiers);
			}
			
			$query = "SELECT * FROM `$category` $filter $order $limit";

			if (($this->assets[$category] = $db->getRecords($query)) === false)
					die('Database error: Error retrieving assets:'.mysql_error().$query);
			
			/* count the number of assets in the category */
			if (($count=$db->getRecords("SELECT COUNT(*) FROM `$category` $filter")) === false)
					die('Database error: Error counting assets:'.mysql_error().$query);
			$this->counts[$category] = $count[0]['COUNT(*)'];
		}

		return true;
	}
}

?>