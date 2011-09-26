<?php

function SLAM_getModules(&$config,$dir,$ini)
{
	// function returns names of modules in the specified ini file that are present

	if(($module_names = parse_ini_file("$dir/$ini",true)) === false){
		$config->errors[] = "Fatal module error: Could not find module ini file \"$ini\".";
		return;
	}

	$enabled = array();
	if(is_array($module_names['enabled']))
	{
		foreach($module_names['enabled'] as $name)
		{
			$path = escapeshellcmd("$dir/$name");
			if (is_dir($path))
				$enabled[] = $name;
			else
				$config->errors[] = "Fatal module error: Could not find module \"$name\".";
		}
	}

	return $enabled;
}

function SLAM_loadTranslatorModules(&$config,$dir,$ini)
{
	$modules = array();

	$names = SLAM_getModules($config,$dir,$ini);
	foreach($names as $name)
		$modules[] = new SLAMfileTranslator($config,"$dir/$name");
	
	return $modules;
}

function SLAM_loadIndexModules(&$config,$dir,$ini)
{
	$modules = array();
	
	$names = SLAM_getModules($config,$dir,$ini);
	foreach($names as $name)
		$modules[] = new SLAMindexPlugin($config,"$dir/$name");
	
	return $modules;
}

function SLAM_doModuleRequest($modules,&$config,&$db,&$user,&$request)
{
	foreach($modules as $module)
		$module->request_action($config,$db,$user,$request);

	return;
}

function SLAM_doModuleContent($modules,&$config,&$db,&$user,&$request,&$result,&$content)
{
	foreach($modules as $module)
		$module->content_action($config,$db,$user,$request,$result,$content);

	return;
}

//		header('Content-type: '.SLAM_guessMimeType($file));
//		header("Content-Disposition: inline; filename=$file");

?>