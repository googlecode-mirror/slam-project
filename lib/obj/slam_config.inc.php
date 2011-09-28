<?php

class SLAMconfig
{
	public $errors;
	public $values;
	public $db;
	public $html;
	
	function __construct($noini=false){
		$this->errors = array();
		
		$this->values['version'] = '1.0.7a';
		
		// do some basic initializing
		$http = ($_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
		$this->html['url'] = $http.dirname($_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']).'/';
		$this->html['headers'] = array();
		$this->html['onload'] = array();
				
		if ($noini)
			return;
			
		$this->values = array_merge($this->values,$this->parse_config());
		$this->values = array_merge($this->values,$this->parse_prefs());
		
		if(!is_dir($this->values['path']))
			exit("The installation path specified in your configuration file (\"{$this->values['path']}\") is not valid. Please check your \"configuration.ini\" file or contact your system administrator.");
		
		// define the regex
		$this->values['identifier_regex'] = "/([A-Za-z][A-Za-z])(".implode('|',array_keys($this->values['lettercodes'])).")[_]?(\d+)/";
		
		return;
	}

	private function parse_config()
	{
		/*
			reads the SLAM configuration file and returns the configuration associative array
		*/
				
		if (($r = @parse_ini_file('configuration.ini',true)) === false)
			die('Fatal error: Could not read your "configuration.ini" file. Please re-install SLAM or contact your system administrator.');
		
		return $r;
	}
	
	private function parse_prefs()
	{
		/*
			reads the SLAM preferences file and returns the configuration associative array
		*/
		
		if (($r = @parse_ini_file('preferences.ini',true)) === false)
			die('Fatal error: Could not read your "preferences.ini" file. Please re-install SLAM or contact your system administrator.');
	
		if (is_array($r['list_fields']))
		{
			$fields = $r['list_fields'];
			unset($r['list_fields']);
			$r['list_fields']['default'] = $fields;
		}
		else
			$r['list_fields']['default'] = array();
			
		/* fill out the appropriate arrays from the cateogory data */
		if (is_array($r['categories']))
		{
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
		}
		else
			die('Fatal error: Your "preferences.ini" file contains no categories. Please add a category or contact your system administrator.');
			
		return $r;
	}
}

?>