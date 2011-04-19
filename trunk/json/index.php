<?php

require('lib/slam_index.inc.php');

$config	= new SLAMconfig();
$db		= new SLAMdb($config);

if (!$_REQUEST['auth']){
	echo '{"error":{"0":"No auth string provided"}}';
	return;
}

list($username,$password) = explode(':',base64_decode($_REQUEST['auth']));

/* initialize an empty user object */
$user = new SLAMuser($config,$db,$username,$password);

if ($user->authenticated)
	echo '{"status":"good"}';

if ($user->checkPassword($config,$db,$username,$password))
	echo "user good";
else
	echo "user bad";


/* wrote my own json_encode, as only newer versions of PHP have it */
function makeJSONArray($a)
{
	$s='';
	foreach($a as $k=>$v)
	{
		if (is_array($v))
			$v = explode("\n",makeJSONArray($v))
		else
		{
			if(!is_numeric($k)
				$k = '"'.encodeJSONString($k).'"';
			if(!is_numeric($v))
				$v = '"'.encodeJSONString($v).'"';

			$s.=
		}
	}

}

function encodeJSONString($s)
{

}

function addLeadingTab($text)
{
	$a = preg_split("/((\r(?!\n))|((?<!\r)\n)|(\r\n))/", $text);
	array_walk($a,create_function('$a','return "\t".$a'));
}

?>