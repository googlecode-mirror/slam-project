<?php


function default_JSON_request(&$config,$db,$user,&$request)
{
	/*
	in PHP 5.2 or higher we don't need to bring this in
	from http://flipsideservices.com/
	*/
	
	if (function_exists('json_encode'))
		define(default_JSON_OK, true);
	else
	{
		if (file_exists("{$config->values['path']}/mod/default_JSON/wrapper.inc.php") && file_exists("{$config->values['path']}/mod/default_JSON/encode.inc.php"))
		{
			require_once "{$config->values['path']}/mod/default_JSON/wrapper.inc.php";
			require_once "{$config->values['path']}/mod/default_JSON/encode.inc.php";
			define(default_JSON_OK, true);
		}
		else
			$config->errors[] = "default_JSON: Could not include library files. Please reinstall this module.";
	}
		
	/* nuke any standard output */
	if(default_JSON_OK && ($_REQUEST['json']))
	{
		/* save our module's output/status to the config object */
		$config->values['json'] = array();
		
		/* what requests does this module understand? */
		switch($request->action)
		{
			case 'list':
				$result = new SLAMresult($config,$db,$user,$request);
				break;
			case 'open':
				$result = new SLAMresult($config,$db,$user,$request);
				break;
			case 'search':
				$result = SLAM_loadSearchResults($config,$db,$user,$request);
				break;
			default:
				$config->values['json']['status'] = 'error';
				$config->values['json']['error'] = "Unrecognized request action: {$request->action}";
				break;
		}
		
		$request->action='none';
		$request->location='none';
		$config->html['abort']=true;
	}
	
	return;
}


function default_JSON_content(&$config,$db,$user,$request,$result,&$content)
{
	if(default_JSON_OK && ($_REQUEST['json']))
	{
		$content = json_encode($config->values['json']);
	}
}
	
?>