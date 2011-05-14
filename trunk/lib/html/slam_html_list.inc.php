<?php

function SLAM_makeAssetListHTML(&$config,$db,$user,$request,$result)
{
	/* register the necessary header files */
	$config->html['css'][] = 'css/list.css';
	$config->html['js'][] = 'js/list.js';
	
	/* register the javascript plugin stub */
	$config->html['onload'][] = 'doListJS()';

	$s = "<div id='assetListContainer'>\n";
	
	$categories = array_keys($result->assets);
	foreach($result->assets as $category => $assets)
	{
		/* append the category name */
		if (count($result->assets)>1)
			$s.="<div class='assetListName' onClick=\"toggleHideBodyId('assetListTable_{$category}')\">$category</div>\n";
			
		/* generate the category list html */
		if (count($assets)>0)
		{
			$s.="<form name='assetListForm_$category' action='".$request->makeRequest($config,array('location'=>'list','category'=>array($category)),false)."' method='POST'>\n";
			$s.=SLAM_makeAssetTableActions($category);
		}
	
		$s.=SLAM_makeAssetTableHTML($config,$db,$user,$request,$category,$assets);
		
		if ($category != $categories[count($result->assets) -1])
			$s.="</form>\n";
	}
	
	/* if the last category had visible assets, put an extra bunch of action buttons at the end */
	if (count($assets)>0)
	{
		$s.=SLAM_makeAssetTableActions($category);
		$s.="</form>\n";
	}
	
	$s.= "</div>\n";
	
	return $s;
}

function SLAM_makeAssetTableActions($id)
{
	$export=explode('?',$_SERVER['REQUEST_URI']);
	
	$s=<<<EOL
<div class='assetListActions' id='assetListActions_{$id}'>
	<input type='submit' name='action' value='New' class='assetListActionButton'/>
	<input type='submit' name='action' value='Edit' disabled='true' class='assetListActionButton'/>
	<input type='submit' name='action' value='Tag' disabled='true' class='assetListActionButton'/>
	<input type='submit' name='action' value='Clone' disabled='true' class='assetListActionButton'/>
	<input type='submit' name='action' value='Delete' onClick=\"return confirm('Are you sure you want to delete the selected record?')\" disabled='true' class='assetListActionButton'/>
	<div class='assetListFunctions'>
		<a href='ext/export.php?{$export[1]}'>export</a> | 
		<a href='#' onClick="showPopupDiv('pub/help_list.html','helpDiv',{}); return false">help</a>
	</div>
</div>
EOL;

	return $s;
}

function SLAM_makeAssetTableHTML($config,$db,$user,$request,$category,$assets)
{
	$s = '';
	
	/* loop through each category */
	
	if (count($assets)<1)
		return "<div class='assetListEmpty'>( No entries found )</div>\n";
	
	/* start the table output */
	$s.="<table class='assetList' id='assetListTable_{$category}'>\n";
	
	/* register onloads */
	$config->html['onload'][] = "checkAssetListBoxes(\"{$category}\")";

	/* go through the default fields and extract all asset fields that match */
	$a1 = array();
	foreach($config->values['list_fields']['default'] as $pattern)
		$a1 = array_merge($a1,preg_grep("/$pattern/",array_keys($assets[0])));
	
	/* append any special fields if necessary */
	$a2 = array();
	if (is_array($config->values['categories'][$category]['list_fields']))
		$a2 = $config->values['categories'][$category]['list_fields'];
	
	/* combine the two field arrays, except remove those that are present in both */
	$fields = array_diff(array_merge($a1,$a2),array_intersect($a1,$a2));

	/* the "files" field should always be at the end */
	if (in_array('Files',$fields)){
		$fields = array_diff($fields,array('Files'));;
		$fields[] = 'Files';
	}
	
	/* build the header bar showing the fields for each table */
	$s.="<tr class='assetListHeader'>\n";
	$s.="<td><a href='#' onClick='toggleCategoryCheckboxes(\"$category\"); return false'>Select</a></td>\n"; // cell for the edit link and radio buttons
	foreach ($fields as $field)
	{
		$field = (strlen($field) > $config->values['title_truncate']) ? (substr($field,0,$config->value['title_truncate']).'...') : ($field);
		$direction = ($request->order['direction'] == 'DESC') ? "ASC" : "DESC";
		$s.="<td><a href='".$request->makeRequest($config,array('order'=>array('field'=>$field,'direction'=>$direction)),true)."'>$field</a></td>\n";
	}
	$s.="</tr>\n";
	
	/* build the asset rows */
	$i = 0; // the record counter, used for even/odd TR classes
	foreach ($assets as $asset)
	{
		/* use the appropriate tr class */
		$s.= (($i+1)%2 == 0) ? "<tr class='assetListRowEven'>\n" : "<tr class='assetListRowOdd'>\n";
		
		/* is it selected? */
	//	$c = (@in_array($asset['Identifier'],$request->categories[$category])) ? 'checked=\'true\'' : '';
		
		/* generate the checkbox and link */
		$s.="<td class='assetListLink'>\n";
		$s.="<input type='checkbox' name='i[]' value='{$asset['Identifier']}' id='{$category}_checkbox_{$i}' onClick='checkAssetListBoxes(\"{$category}\")' /> ";

		/* the url for the entry */
		$url = $request->makeRequest($config,array('identifier'=>array($asset['Identifier']),'action'=>'open'),true);

		/* is the current user qualified to edit this record ? */
		if (($asset[$config->values['user_field']] == $user->values['username']) || $user->values['superuser'])
			$s.="<a href='$url'>open</a>\n";
		else
			$s.="<a href='$url'>view</a>\n";
		
		$s.="</td>\n";
		
		foreach($fields as $field)
		{		
			/* use the reduced-size class if it's too long */
			$class = (strlen($asset[$field]) > $config->values['field_resize']) ? 'assetListFieldLong' : 'assetListField';

			/* truncate the value if it's too long */	
			$value = (strlen($asset[$field]) > $config->values['field_truncate']) ? substr($asset[$field],0,$config->values['field_truncate']).'...' : $asset[$field];
				
			if ($field == 'Files') // special treatment for files field
				$s.="<td class='assetListField'><input type='button' class='listFileButton' onClick=\"showPopupIframe('ext/?i={$asset['Identifier']}','fileManagerDiv',510,310); return false\" value='View' />\n";
			else
				$s.= ($f_value == $value) ? "<td class='$class'>$value</td>\n" : "<td class='$class' title='{$asset[$field]}'>$value</td>\n";
		}
		
		$s.="</tr>\n";
		
		$i++;
	}
	$s.="</table>\n";
	
	return $s;
}

?>