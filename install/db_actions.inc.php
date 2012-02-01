<?php

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
/*
 * UPDATE `Cell Strains` SET Permissions=CONCAT(`Researcher`,':RW',`Researcher`,':RW;R')
 */

?>
