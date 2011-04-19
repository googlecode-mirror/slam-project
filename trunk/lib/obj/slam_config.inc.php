<?php

class SLAMconfig
{
	public $errors;
	public $values;
	public $db;
	public $html;
	
	function __construct($noini=false){
		$this->errors = array();
		
		$this->values['version'] = '1.0.3a';
		$this->values['debug'] = true;
		
		// do some basic initializing
		$this->html['url'] = 'http://'.dirname($_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']).'/';
		$this->html['headers'] = array();
		$this->html['onload'] = array();
		
		if ($noini)
			return;
			
		if(($this->values = array_merge($this->values,$this->parseini())) === false)
			die ('Could not load configuration.ini!');
		
		// define the regex
		$this->values['identifier_regex'] = "/([A-Za-z][A-Za-z])(".implode('|',array_keys($this->values['lettercodes'])).")[_]?(\d+)/";
		
		return;
	}

	private function parseini()
	{
		/*
			reads the SLAM configuration file and returns the configuration associative array
		*/
		
		$path = (file_exists('configuration.ini')) ? 'configuration.ini' : '../configuration.ini'; // may be called from files/ directory
		if (($r = @parse_ini_file($path,true)) === false)
			return false;
		
		$fields = $r['list_fields'];
		unset($r['list_fields']);
		$r['list_fields']['default'] = $fields;
		
		/* fill out the appropriate arrays from the cateogory data */
		$categories = $r['categories'];
		unset($r['categories']);
		foreach($categories as $category)
		{
			$r['categories'][$category]['prefix'] = $r[$category]['prefix'];
			$r['categories'][$category]['title_field'] = $r[$category]['title_field'];
			$r['categories'][$category]['list_fields'] = $r[$category]['list_fields'];
			$r['lettercodes'][$r[$category]['prefix']] = $category;
			unset($r[$category]);
		}
		
		return $r;
	}
}

?>