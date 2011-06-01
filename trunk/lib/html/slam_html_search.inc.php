<?php

function SLAM_makeSearchBoxHTML($config,$db,$user,$request,$result)
{	
	/*
		generates the field search box
	*/

	/* register the necessary header files */
	$config->html['css'][] = 'css/search.css';
	$config->html['js'][] = 'js/search.js';
	
	$s="<form name='searchForm' action='{$config->html['url']}' method='GET'>\n";
	$s.=SLAM_makeHiddenInput($request->location,'loc');
	
	$categories = array_keys($request->categories);
	foreach($categories as $category)
		$s.=SLAM_makeHiddenInput($category,'cat[]');
		
	$s.="<div id='searchContainer'>\n";

	/* default search field should be identitifer */
	if (empty($request->search))
		$search = array(0=>array('field'=>'Identifier','value'=>'','mode'=>'LIKE','join'=>''));
	else /* get the terms used in the last search */
		foreach($request->search['field'] as $i=>$field)
			$search[$i] = array('field'=>$field,'value'=>$request->search['value'][$i],'mode'=>$request->search['mode'][$i],'join'=>$request->search['join'][$i]);

	/* all the possible fields to search in */
	$fields = array();
	foreach($result->fields as $category=>$structure)
		if (empty($fields))
			$fields = array_keys($structure);
		else
			$fields = array_intersect($fields,array_keys($structure));
	
	$diff = array_intersect($fields,$config->values['hide_fields']);
	if(!$user->values['superuser'])
		$fields = array_diff($fields,$diff);

	/* the possible search modes */
	$modes = array('~'=>'LIKE','!~'=>'NOT LIKE','>'=>'>','='=>'=','<'=>'<');
	
	/* the possible search joiners */
	$joins = array(''=>'','AND'=>'AND','OR'=>'OR');
	
	$s.="<div id='searchTerms'>\n";
	$i = 0;
	foreach($search as $terms)
	{
		$s.="<div id='search_group_$i' class='searchClass'>\n";
		$s.=SLAM_makeMenuHTML($terms['field'],@array_combine($fields,$fields),"name='s_field[]' id='search_field_$i'",false);
		$s.=SLAM_makeMenuHTML($terms['mode'],$modes,"name='s_mode[]' id='search_mode_$i'",false);
		$s.=SLAM_makeInputHTML($terms['value'],16,255,"name='s_value[]' id='search_value_$i'");
		$s.=SLAM_makeMenuHTML($terms['join'],$joins,"name='s_join[]' id='search_join_$i' onChange='doSearchGroup($i,this.options[this.selectedIndex].value); return false;'");
		
		/* the last field should have a plus sign so as to be able to add more terms */
		$c = ($i == (count($search) -1)) ? '+' : '-';
		$f = ($i == (count($search) -1)) ? "addSearchGroup($i)" : "removeSearchGroup($i)";
		$s.="<a href='#' id='search_toggle_$i' onClick=\"$f; return false;\">$c</a>\n";
		
		$s.="</div>\n";
		$i++;
	}
	$s.="</div>\n";
	
	$s.="<div id='searchConditions'>";
	$s.="&nbsp;&nbsp;<input type='submit' name='action' value='Search' />\n</div>\n";
	
	$s.="</div>\n</form>\n";
	return $s;
}

?>