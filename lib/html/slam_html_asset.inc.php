<?php

function SLAM_makeAssetEditHTML(&$config,$db,$user,$request,&$result)
{
	/*
		Displays the edit page for the records corresponding to the first category in $result
	*/
	
	/* register the necessary header files */
	$config->html['css'][] = 'css/asset.css';
	$config->html['js'][] = 'js/asset.js';
	$config->html['js'][] = 'js/convert.js';
	
	/* register the javascript plugin stub */
	$config->html['onload'][] = 'doEditJS()';
	
	$s="<form id='editRecord' action='{$config->html['url']}' method='POST'>\n";
	
	$category	= array_shift(array_keys($request->categories)); //the first category
	$assets		= $result->assets[$category];
	$structure	= $result->fields[$category];
	$editable	= array();
	
	/* retrieve the field values, either for a new entry or for editing existing ones */
	if ($request->action == 'new')
	{
		$editable[] = true;
		$fields	= SLAM_setAssetFields($config,$db,$user,$category,$structure,null);

		/* save the identifier to the form so we can come back to it if the user saves changes */
		$s.=SLAM_makeHiddenInput($fields['Identifier'],'Identifier');
		$s.=SLAM_makeHiddenInput('true','new');
	}
	elseif ($request->action == 'clone')
	{
		$editable[] = true;
		$fields	= SLAM_setAssetFields($config,$db,$user,$category,$structure,$assets[0]);
		
		/* save the identifier to the form so we can come back to it if the user saves changes */
		$s.=SLAM_makeHiddenInput($fields['Identifier'],'Identifier');
		$s.=SLAM_makeHiddenInput('true','new');
	}
	elseif( count($assets) > 0)
	{
		/* retrieve the consensus field values */
		$fields	= SLAM_getAssetFields($config,$db,$user,$assets);
		
		foreach($assets as $asset)
		{
			/* save the editable status for every asset */
			$editable[] = (SLAM_getAssetAccess($user,$asset) > 1);
			
			/* save all of the identifiers we're to update into the form */		
			$s.=SLAM_makeHiddenInput($asset['Identifier'],'Identifier[]');
		}
		$editable = array_unique($editable);
		
		/* fields that cannot be edited for more than one asset at a time shouldn't be shown */
		if (count($assets) > 1)
		{
			unset($structure['Files']);
		}
	}
	else{
		return SLAM_makeNoteHTML("This asset has been removed, or does not exist.",true);
	}
	
	/* if there are a mix of editable and uneditable assets, provide the user a warning */
	if( count($editable) > 1 )
	{
		$config->html['onload'][] = 'doNonEditableWarning()';
		$editable = true;
	}
	else
		$editable = $editable[0];
	
	/* set our location */
	$s.=SLAM_makeHiddenInput($request->location,'loc');
	$s.=SLAM_makeHiddenInput($category,'cat');
		
	$s.="<div id='assetEditContainer'>\n";

	$export=http_build_query(array_merge($_GET,$_POST));
	$f=<<<EOL
<div id='assetEditFunctions'>
jump to <a href='#End'>bottom</a> | 
<a href='#' onClick="setSwitchableTR('none'); return false">hide</a>/<a href='' onClick="setSwitchableTR(''); return false">show</a> unused 
| <a href='ext/export.php?$export'>export</a>/<a href='ext/print.php?$export'>print</a>
| <a href='#' onClick="showPopupDiv('pub/help_edit.html','helpDiv',{}); return false">help</a>
</div>\n
EOL;

	$b="$f<table id='assetEdit'>\n";
	
	/* go through the structure and put together each fields's html */
	foreach($structure as $name=>$scheme)
	{		
		/* when we run across the title field, save it to the $t variable for later use */
		if ($name == $config->categories[$category]['title_field'])
			$t="<div id='assetEditTitle'>$category : $fields[$name]</div>\n";
		
		/* collapse fields that are empty */
		$collapsed = false;
		if( in_array($fields[$name], $config->values['hide_empty']) )
			$collapsed = true;
		if ($request->action == 'new')
			$collapsed = false;

		switch($name)
		{
			case 'Identifier': /* identifier should not be editable if the user is not a superuser */
				$b.=SLAM_makeFieldHTML($config,$request,$fields[$name],$scheme,$user->superuser,$collapsed);				
				break;
			
			case 'permissions': /* insert the permissions control panel */
				$b.=SLAM_makePermissionsHTML($config,$user,$assets);
				$b.=SLAM_makeHiddenInput( base64_encode(json_encode($fields[$name])) ,'permissions');
				break;
			
			case 'Project': /* save the default projects array to the structure of the projects field */
				$scheme['values'] = $config->projects;
				$b.=SLAM_makeFieldHTML($config,$request,$fields[$name],$scheme,$editable,False);
				break;
				
			case 'Files': /* if there's a "Files" field, show a link to the file browser instead */
				$b.="<tr>\n<td class='assetEditField'>Files :</td><td class='assetEditValue'><input type='button' class='assetFileButton' onClick=\"showFileManager('ext/files.php?i={$fields['Identifier']}'); return false\" value='Open Browser' /></td><td class='assetEditFunction'>&nbsp;</td>\n</tr>\n";
				break;
		
			default:
				if ($scheme['hidden'] && !$user->superuser)
					$b.=SLAM_makeHiddenInput($fields[$name],'edit_'.base64_encode($scheme['name']));
				else
					$b.=SLAM_makeFieldHTML($config,$request,$fields[$name],$scheme,$editable,$collapsed);
		}
	}
	$s.="$t$b</table>\n";
	
	$s.="<div id='assetEditActions'>\n";
	if ($editable)
		$s.="<input type='button' name='action' value='Cancel' onClick='javascript: history.go(-1)'/><input type='submit' name='action' value='Delete' onClick=\"javascript:return confirm('Are you sure you want to delete the selected record?')\"/><input type='submit' name='action' value='Save' /><input type='submit' name='action' value='Save Changes' />\n";
	else
		$s.="<input type='button' name='action' value='Cancel' onClick='javascript: history.go(-1)'/>\n";
	$s.="<a name='End'>&nbsp;</a>\n";
	$s.="</div>\n</div></form>\n";

	return $s;
}

function SLAM_makeFieldHTML($config,$request,$v,$s,$e,$h)
{	
	/* generates an appropriate editable (or non-editable) field
		$v = value of field
		$s = structure of field
		$e = is field editable by the user?
		$h = is the field hidden?
	*/

	$n64 = base64_encode($s['name']);
	$n = "edit_$n64"; // field names will get mangled if we don't encode them
	
	/* make value html-form friendly */
	$v = htmlspecialchars($v,ENT_QUOTES);
	
	/* if it's a set or an enum with multiple choices, do the same */
	if (!empty($s['values']))
		foreach($s['values'] as $key=>$value)
			$s['values'][$key] = htmlspecialchars($value,ENT_QUOTES);
		
	/* generate the appropriate form input based on field type */
	switch($s['type'])
	{
		case 'char':
		case 'varchar':
			$b=SLAM_makeInputHTML($v,40,$s['size'],"name='$n' id='$n'",!$e);
			break;
		case 'set':
			$b=SLAM_makeCheckBoxes($v,$s['values'],"name='$n' id='$n'",!$e);
			$na = false;
			break;
		case 'enum':
			$b=SLAM_makeMenuHTML($v,array_combine($s['values'],$s['values']),"name='$n' id='$n'",true,!$e);
			break;
		case 'tinyint':
		case 'smallint':
		case 'mediumint':
		case 'int':
		case 'bigint':
		case 'float':
		case 'double':
		case 'real':
		case 'decimal':
		case 'numeric':
		case 'datetime':
		case 'timestamp':
		case 'year':
			$b=SLAM_makeInputHTML($v,13,10,"name='$n' id='$n'",!$e);
			break;
		case 'date':
			$b=SLAM_makeInputHTML($v,13,10,"name='$n' id='$n'",!$e);
			break;
		case 'tinyblob':
		case 'tinytext':
		case 'blob':
		case 'text':
		case 'mediumblob':
		case 'mediumtext':
		case 'longblob':
		case 'longtext':
			$b=SLAM_makeTxtareaHTML($v,50,12,"name='$n' id='$n'",!$e);
			break;
	}
	
	/* append the project menu */
	if ($s['name'] == 'Project')
		$b = SLAM_makeProjectMenuHTML($config,$v,$s,$n,$e);
	
	/* should the file be a linked identifier field? */
	if ( preg_match('/^(\#\S+\s+)/',$s['comment'],$m) > 0 )
	{
		$f = SLAM_makeIdentifierMenuHTML($config,$request,$v,$s,"name='$n' id='$n'");
		$s['comment'] = str_replace($m[1],'',$s['comment']); // remove the link field specifier from the title
	}
	
	/* if set, the field signifying ownership should be marked read-only */
	if( $config->values['permissions']['owner_field'] == $s['name'] )
		$b=SLAM_makeInputHTML($v,40,$s['size'],"name='$n' id='$n'",true);
	
	/* make linked identifier fields bold */
	$g = (empty($f)) ? "{$s['name']}" : "<b>{$s['name']}</b>";
	
	/* date entries should have a date entry button */
	$d = ($s['type'] == 'date') ? "<span>(yyyy-mm-dd)</span> <input class='assetDateButton' type='button' onClick=\"document.getElementById('$n').value=get10Date()\" value='Today' />" : "";
	
	/* if the value is empty and we're not on a new entry, make the table row hidden/hidable */
	$h = ($h) ? "class='TRhidable' style='display:none'" : "class='TRstatic'";

	/* if there's help for this entry, show it (strlen() is to prevent empty titles from linked identifier fields with no help) */ 
	$t = (strlen($s['comment'])>1) ? "<span class='assetFieldHelp' title='".htmlspecialchars($s['comment'],ENT_QUOTES)."'>$g</span>" : $g;
	
	/* slam together all the pieces */
	return "<tr $h >\n<td class='assetEditField'>$t :</td><td class='assetEditValue'>$b$d</td><td class='assetEditFunction'>$f</td>\n</tr>\n";
}

function SLAM_makeProjectMenuHTML($config,$v,$s,$n,$e)
{
	/* generates the project field menu and potentially an "other" input field
		$v = value of field (the project)
		$s = structure of the field
		$n = name of the field
		$e = is field editable?
	*/

	/* set the input visibility */
	$vis = (in_array($v,$s['values']) || ($v == '')) ? "style='display:none'" : ''; // hide the other input text box if the option is in the menu
	
	/* set the menu selection */
	$menu_v = (in_array($v,$s['values']) || ($v == '')) ? $v : 'Other'; // if the project isn't in the menu, set the menu to other

	/* make the "other" project input field */	
	if($config->values['novel_projects'])
	{
		$s['values'][] = 'Other'; //Append "Other" option to menu
		$o = SLAM_makeInputHTML($v,10,$s['size'],"name='$n' id='$n' $vis",!$e);
	}
	else
		$o = SLAM_makeInputHTML($v,10,$s['size'],"name='$n' id='$n' $vis",true);

	/* make the menu */
	$m = SLAM_makeMenuHTML($menu_v,array_combine($s['values'],$s['values']),"name='projectMenu' onChange=\"doProjectMenu(this.options[this.selectedIndex].value, '$n')\"",true,!$e);
	return $m.$o;
}

function SLAM_makeIdentifierMenuHTML($config,$request,$v,$s,$n)
{	
	/* generates a smart identifier menu (or button) for jumping to linked assets
		$v = value of the field
		$s = format of the field
		$n = name of the field
	*/
	
	// get list of linkable identifier elements
	preg_match_all($config->values['identifier_regex'],$v,$m);
	$url = "{$config->html['url']}?action=open&rloc={$request->location['return']}&identifier=";
	
	$a=array();
	foreach($m[0] as $k=>$match)
		$a["{$m[1][$k]}{$m[2][$k]}_{$m[3][$k]}"] = $request->makeRequest($config,array('identifier'=>array("{$m[1][$k]}{$m[2][$k]}_{$m[3][$k]}")),true);
	$id = "{$m[1][$k]}{$m[2][$k]}_{$m[3][$k]}";
	
	/* make a smart menu with each linkable identifier we found */
	if (count($a) == 1)
		return SLAM_makeButtonHTML("{$m[0][0]} &rarr;","name='{$s['name']}_linklist' class='assetIdentifierLinkButton' onClick=\"jumpToIdentifier('".$request->makeRequest($config,array('identifier'=>array($id)),true)."')\" class='linkButton'",false);
	elseif(count($a) > 1)
		return SLAM_makeMenuHTML('',$a,"name='{$s['name']}_linklist' onChange=\"jumpToIdentifier(this.options[this.selectedIndex].value)\"",true,false);
	else
		return '<!-- empty -->';
}

function SLAM_makePermissionsHTML($config,$user,$assets)
{
	/* generates a panel that the user can modify the permissions with */

	$s = "<tr>\n<td class='assetEditField'>Permissions:</td><td class='assetEditValue'>";

	if( count($assets) > 1 )
		$s.= "<input type='button' value='Open Editor' onClick=\"showPopupDiv('pub/permissions_multiple.html','permissionsDiv',{noclose:'true'});populatePermsPanel('permissions')\"/ class='assetPermsButton'>";
	else
		$s.= "<input type='button' value='Open Editor' onClick=\"showPopupDiv('pub/permissions_single.html','permissionsDiv',{noclose:'true'});populatePermsPanel('permissions')\"/ class='assetPermsButton'>";
	
	$s.= "</td><td class='assetEditFunction'></td>\n</tr>\n";
	
	return $s;
}

?>