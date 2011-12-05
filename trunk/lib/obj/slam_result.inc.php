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
		
		/* retrieve the user permissions filter string */
		$filter1 = SLAM_getPermissionsFilter($config,$db,$user,$request,'R');
		$filter2 = SLAM_getRemovedFilter($config,$user);

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
			
			$query = "SELECT * FROM `$category` WHERE ($select) AND ($filter1) AND ($filter2) ORDER BY $order LIMIT $limit";

			if (($this->assets[$category] = $db->getRecords($query)) === false)
				$config->errors[]='Database error: Error retrieving assets:'.mysql_error().$query;
			
			/* count the number of assets in the category */
			$query = "SELECT COUNT(*) FROM `$category` WHERE ($filter2)";
			
			if (($count=$db->getRecords($query)) === false)
				$config->errors[]='Database error: Error counting assets:'.mysql_error().$query;
			$this->counts[$category] = $count[0]['COUNT(*)'];
		}

		return true;
	}
}

?>