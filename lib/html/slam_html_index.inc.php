<?php

function SLAM_makeBreadcrumbHTML($config,$db,$user,$request,$results)
{
	/*
		generates the "breadcrumb" trail for navigation
	*/
	
	$s="<div id='breadCrumb'>&nbsp;&Sigma;&nbsp;&nbsp;<a href='{$config->html['url']}'>{$config->values['name']}</a>";
	$categories = array_keys($request->categories);

	/* set the current categor(ies) */
	if (count($categories) == 1)
		$s.=" &raquo; <a href='".$request->makeRequest($config,array('category'=>$categories,'location'=>'list'),false)."'>{$categories[0]}</a>";
	elseif(count($categories) > 1)
		$s.=" &raquo; (Multiple Categories)";

	/* set the current result */
	if($request->search)
		$s.=" &raquo; <a href='".$request->makeRequest($config,array(),true)."'>Search</a>";
	else
	{
		if((count($categories) == 1) && (count($request->categories[$categories[0]])==1))
			$s.=" &raquo; <a href='".$request->makeRequest($config,array(),true)."'>{$results->assets[$categories[0]][0]['Identifier']}</a>";
		elseif(count($request->assets)>1)
			$s.=" &raquo; (Multiple Assets)";
	}
	
//	if ($user->values['superuser'] && $config->values['premium'])
//		$a = "<a style='float:right;width:5%' href='{$config->values['url']}?admin=true'>admin</a> <span style='float:right;width:1%'>&nbsp;|</span>";

	return "$s$a<a id='breadCrumbUser' href='#' onClick=\"showPopupDiv('pub/user_actions.html','userDiv'); return false\">{$user->values['username']}</a></div>\n";
}

function SLAM_makeCategoryListHTML($config,$db,$user,$request)
{	
	/*
		generates a clickable list of available categories
	*/
	
	if(!($categories = $db->GetTables()))
		die ('Database error: Could not get list of categories'.mysql_error());
	
	/* restrict displayed tables to only those that are specified in the config */
	$categories = array_intersect($categories,array_keys($config->values['categories']));
	
	$s=<<<EOL
<div id='categoryListContainer'>
<table id='categoryList'>
EOL;
	/* link to dashboard */
	if ($request->location == 'dash')
		$s.="<tr><td class='categoryListElementSelected' style='padding-bottom:5px'><a href='".$request->makeRequest($config,array('location'=>'dash'),false)."'>Dashboard</a> &raquo;</td></tr>\n";
	else
		$s.="<tr><td class='categoryListElement' style='padding-bottom:5px'><a href='".$request->makeRequest($config,array('location'=>'dash'),false)."'>Dashboard</a> &raquo;</td></tr>\n";
	
	$request_categories = array_keys($request->categories);
	foreach($categories as $category)
	{
		if(($request->location == 'list') && ($request_categories[0] == $category))
			$s.="<tr><td class='categoryListElementSelected'><a href='".$request->makeRequest($config,array('location'=>'list','category'=>array($category)),false)."'>$category</a> &raquo;</td></tr>\n";
		else
			$s.="<tr><td class='categoryListElement'><a href='".$request->makeRequest($config,array('location'=>'list','category'=>array($category)),false)."'>$category</a> &raquo;</td></tr>\n";
	}
	
	return "$s</table>\n</div>\n";
}

?>