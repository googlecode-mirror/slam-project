<?php

class SLAMdb
{
	public $link = null;
	private $required_fields = array('Serial','Identifier','Permissions','Removed');
	
	public function __construct(&$config)
	{
		if(($this->Connect($config->values['db_server'],$config->values['db_user'],$config->values['db_pass'],$config->values['db_name'])) === false)
			die('Error connecting to database: '.mysql_error());
		
		/* make sure that the category tables provided in the config are valid */
		$this->RemoveBadCategories($config);
	}
	
	public	function Connect($server,$user,$pass,$db)
	{
		/*
			attempts to connect to a server and database
			returns true on success, false otherwise
		*/
		
		if(!($link = @mysql_connect($server,$user,$pass)))
			return false;
			
		$this->link = $link;
			
		if (!@mysql_select_db($db,$this->link))
			return false;
			
		return true;
	}
	
	private	function RemoveBadCategories(&$config)
	{
		$tables = array_keys($config->values['categories']);
		foreach($tables as $table)
		{
			/* compare the fields in each table with the required set */
			$fields = array_keys($this->GetStructure($table));
			$diff = array_diff($this->required_fields,array_intersect($this->required_fields,$fields));
			
			if(count($diff)>0)
			{
				/* remove the malformed category */
				unset($config->values['categories'][$table]);
				
				foreach($diff as $error)
					$config->errors[] = "Table \"$table\" is missing required attribute \"$error\".";
			}
		}
		return;
	}
	
	public	function GetTables()
	{
		/*
			returns all tables in the current database
		*/
		
		if(!($result = mysql_query('SHOW TABLES',$this->link)))
			return false;
			
		$return = array();
		while($row = mysql_fetch_row($result))
			$return[] = $row[0];
		return $return;
	}
	
	public	function GetStructure($t)
	{
		/*
			returns information about the structure of the specified table, otherwise returns false on failure
		*/
		
		if(!($result = mysql_query("SHOW FULL COLUMNS FROM `$t`",$this->link)))
			return false;

		$fields = array();
		while($row = mysql_fetch_assoc($result))
		{
			$fields[$row['Field']]['name'] = $row['Field'];
			$fields[$row['Field']]['comment'] = $row['Comment'];
			$fields[$row['Field']]['default'] = $row['Default'];
			$fields[$row['Field']]['null'] = ($row['Null'] == 'YES') ? true : false;
			if (preg_match('/^(enum|set)\((.*)\)$/',$row['Type'],$m)>0)
			{
				$fields[$row['Field']]['type'] = $m[1];
				$fields[$row['Field']]['values'] = explode(',',str_replace('\'','',$m[2]));
			}
			elseif (preg_match('/^(\w+)\((\d+)\)$/',$row['Type'],$m)>0)
			{
				$fields[$row['Field']]['type'] = $m[1];
				$fields[$row['Field']]['size'] = $m[2];
			}
			else
				$fields[$row['Field']]['type'] = $row['Type'];
		}

		return $fields;
	}
		
	public	function GetRecords($q,$vert=true)
	{
		/*
			returns records specified in the query in one of two formats:
			vertical: numerical array of records => associative array of fields
			horizontal: associative array of fields => numerical array of records
		*/

		if(($result = mysql_query($q,$this->link)) === false)
			return false;
			
		$return = array();
		while ($row = mysql_fetch_assoc($result))
		{
			if ($vert)
				$return[] = $row;
			else
				foreach($row as $key => $value)
					$return[$key][] = $value;
		}
		return $return;
	}
	
	public	function Query($q){
		return mysql_query($q,$this->link);
	}
	
	public	function Disconnect(){
		mysql_close($this->link);
	}
}

?>