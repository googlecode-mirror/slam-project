<?php

class SLAMmodule
{
	public $path;
	public $class;
	public $name;
	public $author;
	public $version;
	public $compatible;
		
	function readIni(&$config,$path)
	{
		$ret = array();
		$this->path = $path;
		
		# try and read the ini file
		if(($ret = @parse_ini_file("{$path}/module.ini")) === false)
		{
			$config->errors[] = "Fatal module error: Module ini \"{$path}/module.ini\" is not readable.";
			return false;
		}
		
		# extract the required info
		if(!isset($ret['name'])){
			$config->errors[] = "Fatal module error: Module ini does not contain a name field.";
			return false;
		}
		
		if(!isset($ret['compatible'])){
			$config->errors[] = "Fatal module error: Module \"{$ret['name']} does not contain a compatible SLAM version field.";
			return false;
		}
		
		if(!isset($ret['version'])){
			$config->errors[] = "Fatal module error: Module \"{$ret['name']} does not contain a module version field.";
			return false;
		}
		
		# extract useful fields
		if(!isset($ret['author']))
			$config->errors[] = "Module error: Module \"{$ret['name']}\" does not have an author property.";
		
		if(!isset($ret['author']))
			$config->errors[] = "Module error: Module \"{$ret['name']}\" does not have an author property.";
		
		$this->name = $ret['name'];
		$this->compatible = $ret['compatible'];
		$this->version = $ret['version'];
		$this->author = $ret['author'];
		
		return $ret;
	}
	
	function checkCompatibility(&$config)
	{
		if(version_compare($config->values['version'],$this->compatible) > -1)
			return true;
		
		$config->errors[] = "The \"{$this->name}\" module requires SLAM version {$this->compatible}.";
		return false;
	}
	
	function loadIncludes(&$config,$values,$path)
	{
		# Make sure that the components of the module to include are in good order, and include them			
		if (is_array($values['include']))
		{
			foreach($values['include'] as $file)
			{
				if(is_readable("$path/$file"))
					include("$path/$file");
				else
				{
					$config->errors[] = "Fatal module error: Module \"{$this->name}\" inclusion \"$path/$file\" is not readable.";
					return false;
				}
			}
			
			return true;
		}
		
		$config->errors[] = "Fatal module error: Module \"{$this->name}\" has no file inclusions.";
		return false;
	}
}

class SLAMindexPlugin extends SLAMmodule
{
	public $request_actions;
	public $content_actions;
	
	function __construct(&$config,$path)
	{
		$this->class = 'index_plugin';
		$this->request_actions = array();
		$this->content_actions = array();
		
		if(($values = $this->readIni($config,$path)) === false)
			return;
		
		if(($this->checkCompatibility($config)) === false)
			return;
			
		if($this->loadIncludes($config,$values,$path) === false)
			return;
		
		if(is_array($values['request_action']))
		{
			foreach($values['request_action'] as $action)
			{
				if(is_callable($action))
					$this->request_actions[] = $action;
				else
					$config->errors[] = "Fatal module error: Module \"{$this->name}\" request action \"{$this->action}\" is not callable.";
			}
		}
		
		if(is_array($values['content_action']))
		{
			foreach($values['content_action'] as $action)
			{
				if(is_callable($action))
					$this->content_actions[] = $action;
				else
					$config->errors[] = "Fatal module error: Module \"{$this->name}\" content action \"{$this->action}\" is not callable.";
			}
		}
		
		return;
	}
	
	function request_action(&$config,$db,&$user,&$request)
	{
		foreach($this->request_actions as $action)
			call_user_func_array($action,array($config,$db,$user,$request));

		return true;
	}
	
	function content_action(&$config,$db,&$user,&$request,&$result,&$content)
	{
		foreach($this->content_actions as $action)
			call_user_func_array($action,array($config,$db,$user,$request,$result,$content));

		return true;
	}
}

class SLAMfileTranslator extends SLAMmodule
{
	public $type;
	public $input_ext;
	public $action;
	
	function __construct(&$config,$path)
	{
		$this->class = 'file_translator';
		
		if(($values = $this->readIni($config,$path)) === false)
			return;
		
		if(($this->checkCompatibility($config)) === false)
			return;
		
		if(is_array($values['in_extension']))
			$this->input_ext = $values['in_extension'];
		else{
			$config->errors[] = "Fatal module error: File convertor module \"{$this->name}\" has no associated file input types.";
			return;
		}
			
		if($this->loadIncludes($config,$values,$path) === false)
			return;

		if (!isset($values['translate_action'])){
			$config->errors[] = "Fatal module error: File convertor \"{$this->name}\" has no translate action function.";
			return;
		}
		
		if(!is_callable($this->action = $values['translate_action'])){
			$config->errors[] = "Fatal module error: File convertor \"{$this->name}\" function \"{$this->action}\" is not callable.";
			return;
		}

		return;
	}
	
	function translate_action(&$config,&$db,&$user,&$request,&$result,$path)
	{
		$input_path = escapeshellcmd($input_path);
		
		# everything should already be set up, just call it
		return call_user_func_array($this->action,array($config,$db,$user,$request,$requst,$path));
	}
}