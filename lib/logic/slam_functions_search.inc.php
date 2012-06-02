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
	
	/* collect all of the possible fields to search in from the current category(s)*/
	$result = new SLAMresult();
	
	/* retrieve the structure of the categories in the request */
	$result->getStructures($config,$db,$user,$request);
	
	/* retrieve all the searchable fields in the provided category(s) */
	$fields = array();
	foreach($categories as $category)
	{
		if (empty($fields))
			$fields = array_keys($result->fields[$category]);
		else
			$fields = array_intersect($fields,array_keys($result->fields[$category]));
	}
	
	/* if the user isn't a superuser, make sure that hidden fields aren't searched */
	$diff = array_intersect($fields,$config->values['hide_fields']);
	if(!$user->superuser)
		$fields = array_diff($fields,$diff);
	
	/* don't forget to remove the pseudofields! */
	$diff = array_intersect($fields,array('permissions','Files'));
	$fields = array_diff($fields,$diff);
		
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
		
		/* joins have to be in the approved list, or they default to AND */
		$joins[] = (in_array($request->search['join'][$i],$allowed_joins)) ? $request->search['join'][$i] : 'AND';

		if( in_array($field, $fields) )
		{
			$terms[] = '`'.mysql_real_escape($field,$db->link).'` '.$allowed_modes[$request->search['mode'][$i]].' \''.mysql_real_escape($value,$db->link).'\'';
		}
		elseif( $field == '(Search all)' )
		{
			/* build a special term that contains all of the available fields with OR joins */
			$sub_terms = array();
			foreach( $fields as $field )
				$sub_terms[] = '`'.mysql_real_escape($field,$db->link).'` '.$allowed_modes[$request->search['mode'][$i]].' \''.mysql_real_escape($value,$db->link).'\'';
			
			if( $request->search['mode'][$i] == 'LIKE' )
				$terms[] = '( '.implode( ' OR ', $sub_terms ).' )';
			else
				$terms[] = '( '.implode( ' AND ', $sub_terms ).' )';
		}
		else
		{
			/* if the field name isn't present in the fields of the current category(s), bail */
			$config->errors[] = "Error: User attempted to search field named '{$field}' which isn't in the current categories.";
			continue;
		}

	}
	
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