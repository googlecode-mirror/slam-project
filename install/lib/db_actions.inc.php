<?php

function checkDbOptions( $server, $dbname, $dbuser, $dbpass )
{
	$fail = array();

	if ($server == '')
		$fail[] = "Please specify a database server IP or 'localhost'";
	if( $dbname == '')
		$fail[] = "Please specify a database name.";
	if( $dbuser == '')
		$fail[] = "Please specify a database user.";
	if( $dbpass == '')
		$fail[] = "Please provide a password for the database user.";
	
	if( count($fail) == 0 )
	{
		$link = @mysql_connect( $server, $dbuser, $dbpass, true );
		
		if (!$link)
			$fail[] = "Could not connect to the server '$server' with the provided username '$dbuser'.";
		else
		{
			if (mysql_select_db( $dbname, $link ))
			{
				if( checkForSLAMTables($link, $dbname) > 0)
					$fail[] = "A SLAM installation already exists on this database.";
			}
			else /* try and temporarily create the database */
			{
				if( !mysql_query("CREATE DATABASE '$dbname'", $link) )
					 $fail[] = "Specified database '$dbname' doesn't exist and couldn't be created!";
				else
				{
					if (!mysql_select_db( $dbname, $link ))
						$fail[] = "Created database '$dbname', but couldn't select it!";
					
					mysql_query("DROP DATABASE '$dbname'", $link);
				}
			}
			
			mysql_close($link);
		}
	}
	
	if( count($fail) == 0 )
		return true;
	
	return $fail;
}

function checkForSLAMTables( $dblink, $dbname )
{
    /* returns a numeric value containing the suitability of the specified database for installing SLAM
    -1 - error
    0 - no existing required SLAM tables
    1 - SLAM_Category table exists
    2 - SLAM_Researchers table exists
	4 - SLAM_Permissions table exists
    7 - all tables exist
    */
    
    $sql = "SHOW TABLES FROM $dbname";
    $result = mysql_query($sql, $dblink);

    if (!$result)
	return -1;
    
    $tables = array();
    while ($row = mysql_fetch_row($result))
        $tables[] = $row[0];
    
    $ret = 0;
    if (in_array('SLAM_Researchers', $tables))
		$ret+=1;
    if (in_array('SLAM_Categories', $tables))
		$ret+=2;
	if (in_array('SLAM_Permissions', $tables))
		$ret+=4;
    
    return $ret;
}

function getDbUserPrivs($link, $dbuser)
{
	/* inoperative function for now */
	
	$q = "SHOW GRANTS";
	$result = mysql_query( $q, $link );
	
	if (!$result)
		return mysql_error();
	
	$privs = array();
	while( $row = mysql_fetch_row($result) )
		$privs[] = $row;
	
	/* have to do some complex parsing of MySQL's output */
	
	return true;
}

function SLAM_write_to_table( $link, $table, $data)
{
	$fields = array();
	$values = array();
	
	foreach( $data as $key=>$value )
	{
		$key = mysql_real_escape_string( $key, $link );
		$value = mysql_real_escape_string( $value, $link );
		
		$fields[] = "`$key`";
		$values[] = "'$value'";
	}
	
	$fields = '('.implode( ',', $fields ).')';
	$values = '('.implode( ',', $values ).')';

	$sql = "INSERT INTO `{$table}` $fields VALUES $values";
	return mysql_query( $sql, $link );
}

?>
