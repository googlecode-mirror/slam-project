<?php

require('../lib/slam_json.inc.php');

/*
	in PHP 5.2 or higher we don't need to bring this in
	from http://flipsideservices.com/
*/
if (!function_exists('json_encode'))
	require_once 'json_encode/wrapper.php';

$config	= new SLAMconfig();
$db		= new SLAMdb($config);

if (!$_REQUEST['auth']){
	echo '{"error":{"0":"No auth string provided"}}';
	return;
}

list($username,$password) = explode(':',base64_decode($_REQUEST['auth']));

/* try and get the user up and running */
$user = new SLAMuser($config,$db,$username,$password);

if ($user->authenticated)
	return json_encode(array('status'=>'ok','error'=>''));
else
	return json_encode(array('status'=>'error','error'=>'Invalid auth'));
	
?>