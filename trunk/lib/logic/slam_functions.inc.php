<?php

function sql_real_escape($a,$link)
{
	/*
		recursively mysql_real_escape_string's an array or a string
	*/
	
	if (!is_array($a))
		return substr($link->quote($a), 1, -1);
	
	foreach($a as $k => $v)
		$a[$k] = (is_array($v)) ? sql_real_escape($v,$link) : substr($link->quote($v), 1, -1);
	
	return $a;
}

function makeRandomAlpha($length=8)
{
	/*
		generates a random alphanumeric string
	*/
	
	$chrs = 'abcdefghijklmnopqrstuvwxyz0123456789';
	$out = '';
	for ($i=0; $i < $length; $i++)
		$out.= $chrs[rand(0, strlen($chrs) - 1)];

	return $out;
}

?>