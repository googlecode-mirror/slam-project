<?php

function SLAM_makeDashboardHTML(&$config,$db,$user,$request,$result)
{
	/*
		generates the dashboard HTML
	*/
	
	/* register the necessary header files */
	$config->html['css'][] = 'css/dash.css';
	$config->html['css'][] = 'css/list.css';
	$config->html['js'][] = 'js/dash.js';
	$config->html['js'][] = 'js/list.js';
	
	/* register the javascript plugin stub */
	$config->html['onload'][] = 'doDashJS()';
	
	$s = "<div id='dashboardTitle'>{$user->username}'s Dashboard</div>\n";
	
	$categories = array_keys($request->categories);
	if($_REQUEST['d_status'])
		$s.="<div id='dashboardStatus'>Searching ".count($categories)." categories</div>\n";
	elseif(!empty($request->search))
		$s.="<div id='dashboardStatus'>Viewing search results</div>\n";
	else
		$s.="<div id='dashboardStatus'>Viewing tagged assets</div>\n";
		
	return $s;	
}

function SLAM_makeDashboardSearchHTML($config,$db,$user,$request)
{
	/*
		Generates the different stages of a multi-category search
	*/

	$categories = array_keys($request->categories);
		
	/* restrict tables to only those that are specified in the config */
	$tables = array_intersect($db->tables,array_keys($config->categories));

	$s ="<form name='selectSearchCategories' action='".$request->makeRequestURL($config,array('category'=>array()),true)."&d_status=select' method='POST'>\n";
	$s.="<div id='dashboardSearchContainer'>\n";
	if (! $_REQUEST['d_status']=='select')
		$s.=SLAM_makeButtonHTML('Multi-category search',"onClick=\"toggleHideBodyId('dashboardSearchReveal')\"",false);
//	else
//		$s.=SLAM_makeButtonHTML('Cancel',"onClick=\"hideBodyId('searchContainer')\"",false);
	$s.="<div id='dashboardSearchReveal' style='display:none'>\n";
	$s.="<select name='cat[]' multiple='true'>\n";
	foreach($tables as $category)
		$s.= (in_array($category,$categories)) ? "<option value='$category' selected='true'>$category</option>\n" : "<option value='$category'>$category</option>\n";
	$s.="</select>";
	$s.="<br /><input type='submit' name='action' value='Select' />\n";
	$s.="</div>\n";
	$s.="</div>\n";
	$s.="</form>\n";
	
	if (($_REQUEST['d_status']=='select') || (!empty($request->search)))
	{
		/* make a temporary result containing just the structures of the requested tables */
		$result = new SLAMresult();
		$result->getStructures($config,$db,$user,$request);
		
		$s.=SLAM_makeSearchBoxHTML($config,$db,$user,$request,$result);
	}

	return $s;
}

function SLAM_makeDashboardListHTML(&$config,$db,$user,$request,$result)
{
	if (empty($result->assets))
		return "<div id='dashboardNoEntries'><span>No assets to show</span></div>\n";	
		
	$s.="<form name='dashboardListForm' action='".$request->makeRequestURL($config,array('location'=>'dash','order'=>$request->order),false)."' method='POST'>\n";
	$s.= "<div id='assetListContainer'>\n";
	
	/* are we to automatically tag entries from these categories? */
	$tag = (empty($request->search)) ? '&tag=true' : '';
	
	/* display the different tables */
	foreach($result->assets as $category => $assets)
	{
		/* append the category name */
		$s.="<div class='assetListName'><a href='#' onClick=\"toggleHideBodyId('assetListTable_{$category}'); return false;\" >$category</a> <input type='button' value='New' onClick=\"location.href='index.php?a=new&cat={$category}&loc=dash$tag'\"/></div>\n";
		$s.=SLAM_makeAssetTableHTML($config,$db,$user,$request,$category,$assets);
		
		if ($assets != $result->assets[count($result->assets) -1])
			$s.="</form>\n";
	}

	$s.=SLAM_makeDashTableActions($category,!empty($request->search));

	$s.= "</div>\n";
	$s.="</form>\n";
	
	return $s;
}

function SLAM_makeDashTableActions($id,$search)
{
	$export=http_build_query(array_merge($_GET,$_POST));

	$s=<<<EOL
<div class='assetListActions' id='assetListActions_{$id}'>
	<input type='submit' name='action' value='Edit' disabled='true' class='assetListActionButton'/>\n
EOL;

	if ($search)
		$s.="<input type='submit' name='action' value='Tag' disabled='true' class='assetListActionButton'/>\n";
	else
		$s.="<input type='submit' name='action' value='Untag' disabled='true' class='assetListActionButton'/>\n";
		
	$s.=<<<EOL
	<input type='submit' name='action' value='Clone' disabled='true' class='assetListActionButton'/>
	<input type='submit' name='action' value='Delete' onClick=\"return confirm('Are you sure you want to delete the selected record?')\" disabled='true' class='assetListActionButton'/>
	<div class='assetListFunctions'>
		<a href='ext/export.php?$export'>export</a> | 
		<a href='#' onClick="showPopupDiv('pub/help_dash.html','helpDiv',{}); return false">help</a>
	</div>
</div>
EOL;

	return $s;
}

?>