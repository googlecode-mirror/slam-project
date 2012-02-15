<?php

function mysql_real_escape($a,$link)
{
	/*
		recursively mysql_real_escape_string's an array or a string
	*/
	
	if (!is_array($a))
		return mysql_real_escape_string($a,$link);
	
	foreach($a as $k => $v)
		$a[$k] = (is_array($v)) ? mysql_real_escape($v,$link) : mysql_real_escape_string($v,$link);
	
	return $a;
}

function makeRandomAlpha($length=8)
{
	/*
		generates a random alphanumeric string
	*/
	
	$out = '';
	$chrs = array_merge(range(48,57),array_merge(range(65,90),range(97,122)));
	
	for($i=0;$i<$length;$i++)
		$out.=chr($chrs[rand(0,count($chrs))]);
	return $out;
}

?>