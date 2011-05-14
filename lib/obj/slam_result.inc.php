<?php

class SLAMresult
{
	public $fields;
	public $assets;
	
	function __construct(&$config=false,$db=false,$user=false,$request=false)
	{
		$this->fields = array();
		$this->assets = array();
	
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

			/* check that the order-by field is appropriate for this category */
			if(!in_array($request->order['field'],array_keys($this->fields[$category])))				
				$request->order['field'] = 'Identifier';
	
			/* retrieve assets from the table */
			if(empty($identifiers) || ($request->action == 'save'))
			{
				$order = "ORDER BY `".mysql_real_escape($request->order['field'],$db->link)."` ".mysql_real_escape($request->order['direction'],$db->link)." LIMIT ".mysql_real_escape($config->values['list_max'],$db->link);
				$filter = ($user->values['superuser'] || $config->values['show_removed']) ? '' : "WHERE (`Removed`='0')";
			}
			else
			{
				$selector = "`Identifier`='".implode("' OR `Identifier`='",$identifiers)."'";
				$order = "ORDER BY `".mysql_real_escape($request->order['field'],$db->link)."` ".mysql_real_escape($request->order['direction'],$db->link)." LIMIT ".count($identifiers);
				$filter = ($user->values['superuser'] || $config->values['show_removed']) ? "WHERE($selector)" : "WHERE (($selector) AND `Removed`='0')";

			}
			
			$query = "SELECT * FROM `$category` $filter $order";
			
			if (($this->assets[$category] = $db->getRecords($query)) === false)
					die('Database error: Error retrieving assets:'.mysql_error().$query);
		}

		return true;
	}
}

?>