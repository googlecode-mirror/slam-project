<?php

function SLAM_loadSearchResults($config,$db,$user,$request)
{
	/*
		runs a search query on the requested tables and returns as SLAMresult containing the matching records
	*/

	/* return empty result on invalid attempt */
	if (empty($request->search))
		return new SLAMresult();
	
	$categories = array_keys($request->categories);
	if (empty($categories))
		return new SLAMresult();

	/* use an assoc array to sanitize search modes */
	$allowed_modes = array('LIKE'=>'LIKE','NOT LIKE'=>'NOT LIKE','>'=>'>','<'=>'<','='=>'=');
	$allowed_likes = array('LIKE','NOT LIKE');
	$allowed_joins = array('AND','OR');
	
	/* extract search terms */
	$terms = array();
	$joins = array();
	foreach($request->search['field'] as $i=>$field)
	{
		/* automatically bracket LIKE and NOTLIKE terms with % */
		$value = (in_array($request->search['mode'][$i],$allowed_likes)) ? "%{$request->search['value'][$i]}%" : $request->search['value'][$i];
		$terms[] = '`'.mysql_real_escape($field,$db->link).'` '.$allowed_modes[$request->search['mode'][$i]].' \''.mysql_real_escape($value,$db->link).'\'';
		
		$joins[] = (in_array($request->search['join'][$i],$allowed_joins)) ? $request->search['join'][$i] : 'AND';
	}

	$result = new SLAMresult();
	
	/* retrieve the structure of the categories in the request */
	$result->getStructures($config,$db,$user,$request);
	
	/* generate the limit based upon the previously provided limit */
	$limit = ($request->limit > 0) ? "{$request->limit},".($request->limit+$config->values['list_max']) : "0,{$config->values['list_max']}";

	/* run the query on each category */
	foreach($categories as $category)
	{
		/* check that the order-by field is appropriate for this category */
		if(!in_array($request->order['field'],array_keys($result->fields[$category])))				
			$request->order['field'] = 'Identifier';
		
		$order = mysql_real_escape($request->order['field'],$db->link)." ".mysql_real_escape($request->order['direction'],$db->link);
		
		/* construct the select statement by putting together the field names and joining conjunctions */
		$select = '';
		foreach($terms as $i=>$term)
			$select.= ((count($terms)>1) && ($i < (count($terms)-1))) ? "{$term} {$joins[$i]} " : $term;
				
		/* generate the query */
		$query = SLAM_makePermsQuery($config, $db, $user, '*', $category, $select, $order, $limit);

		/* execute the query */
		if (($result->assets[$category] = $db->getRecords($query)) === false)
		{
			$config->errors[] ='Database error: Error retrieving search:'.mysql_error().$query;
			return new SLAMresult();
		}
		
		/* count the number of assets in the category */
		$query = SLAM_makePermsQuery($config, $db, $user, 'COUNT(*)', $category, $select);
		
		if (($count=$db->getRecords($query)) === false)
			$config->errors[] = 'Database error: Error counting assets:'.mysql_error().$query;
		
		$result->counts[$category] = $count[0]['COUNT(*)'];
	}
	
	/* associate the retrieved records with their permissions*/
	$result->getPermissions($config, $db, $user, $request);
	
	return $result;
}

?>