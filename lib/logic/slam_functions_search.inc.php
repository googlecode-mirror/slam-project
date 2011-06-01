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

	/* get the number of assets we're to limit ourselves to */
	$limit = (empty($request->search['limit'])) ? '10' : $request->search['limit'];

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
	
	/* run the query on each category */
	foreach($categories as $category)
	{
		/* check that the order-by field is appropriate for this category */
		if(!in_array($request->order['field'],array_keys($result->fields[$category])))				
			$request->order['field'] = 'Identifier';
		
		/* only put the joins between terms */
		$selector = '';
		foreach($terms as $i=>$term)
			$selector.= ((count($terms)>1) && ($i < (count($terms)-1))) ? "{$term} {$joins[$i]} " : $term;

		/* filter out the removed assets */
		$filter = ($user->values['superuser']) ? "($selector)" : "(($selector) AND `Removed`='0')";
		
		/* generate the query */
		$query = "SELECT * FROM `{$category}` WHERE $filter ORDER BY ".mysql_real_escape($request->order['field'],$db->link)." ".mysql_real_escape($request->order['direction'],$db->link)." LIMIT {$limit}";

		/* execute the query */
		if (($result->assets[$category] = $db->getRecords($query)) === false)
		{
			$config->errors[] ='Database error: Error retrieving search:'.mysql_error().$query;
			return new SLAMresult();
		}
	}
	
	return $result;
}

?>