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

	/* make the result object */
	$result = new SLAMresult();
	$result->getStructures($config,$db,$user,$request);
	
	/* retrieve the user permissions filter string */
	$filter1 = SLAM_getPermissionsFilter($config,$db,$user,$request,'R');
	$filter2 = SLAM_getRemovedFilter($config,$user);
	
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
		$query = "SELECT * FROM `$category` WHERE ($select) AND ($filter1) AND ($filter2) ORDER BY $order LIMIT $limit";

		/* execute the query */
		if (($result->assets[$category] = $db->getRecords($query)) === false)
		{
			$config->errors[] ='Database error: Error retrieving search:'.mysql_error().$query;
			return new SLAMresult();
		}
		
		/* count the number of assets in the category */
		$query = "SELECT COUNT(*) FROM `$category` WHERE ($filter1)";
		
		if (($count=$db->getRecords($query)) === false)
			$config->errors[] = 'Database error: Error counting assets:'.mysql_error().$query;
		$result->counts[$category] = $count[0]['COUNT(*)'];
	}
	
	return $result;
}

?>