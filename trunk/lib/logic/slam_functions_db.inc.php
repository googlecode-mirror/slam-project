<?php

function SLAM_makeInsertionStatement( $db, $f, $table, $array )
{
	$a = implode("`,`",sql_real_escape(array_keys($array),$db->link));
	$b = implode("','",sql_real_escape(array_values($array),$db->link));
	return "$f INTO `$table` (`$a`) VALUES ('$b')";
}

function SLAM_makeUpdateStatement( $db, $table, $array, $where, $limit=False )
{
	$a = array();
	foreach( $array as $k => $v )
		$a[]="`".sql_real_escape($k,$db->link)."`='".sql_real_escape($v,$db->link)."'";
	$a = implode( ',', $a );
	
	$b = ($limit === false) ? '' : "LIMIT $limit";

	return "UPDATE `$table` SET $a WHERE ($where) $b";
}

function SLAM_makePermsQuery($config, $db, $user, $return, $table, $match=false, $order=false, $limit=false)
{
	if (!$match)
		$match = '1=1';
	
	$project_match = '';
	foreach( $user->projects as $project )
		$project_match .= "OR ( MATCH (`Projects`) AGAINST ('$project' IN BOOLEAN MODE) AND `project_access` > 0)\n";
	
	$query=<<<EOL
SELECT $return FROM `$table`
WHERE(
	$match
	AND
	(
		(`Identifier` NOT IN (SELECT `Identifier` FROM `{$config->values['perms_table']}`))
		OR
		(`Identifier` IN (SELECT `Identifier` FROM `{$config->values['perms_table']}` WHERE(
			(`Default_access` > 0)
OR (`Owner` = "{$user->username}" AND `Owner_access` > 0)
$project_match
		)))
	)
	AND
	(
		`Removed` < 1
	)
)
EOL;

	if ($user->superuser)
		$query = "SELECT $return FROM `$table` WHERE $match ";

	if ($order)
		$query .= "ORDER BY $order ";
	
	if ($limit)
		$query .= "LIMIT $limit ";

	return $query;
}

?>