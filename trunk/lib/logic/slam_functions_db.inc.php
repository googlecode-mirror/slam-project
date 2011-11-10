<?php

function SLAM_makeInsertionStatement( $db, $f, $table, $array )
{
	$a = implode("`,`",mysql_real_escape(array_keys($array),$db->link));
	$b = implode("','",mysql_real_escape(array_values($array),$db->link));
	return "$f INTO `$table` (`$a`) VALUES ('$b')";
}

?>