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

	$b = ($user->superuser) ? '?superuser=true' : '';
	return "$s$a<a id='breadCrumbUser' href='#' onClick=\"togglePopupMenu('pub/user_actions.php$b','userActionsMenu',alignToBottomRight('breadCrumbUser')); return false\">$user->username</a></div>\n";
}

function SLAM_makeCategoryListHTML($config,$db,$user,$request)
{	
	/*
		generates a clickable list of available categories
	*/
	
	/* restrict displayed tables to only those that are specified in the config */
	$categories = array_intersect($db->tables,array_keys($config->categories));
	
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