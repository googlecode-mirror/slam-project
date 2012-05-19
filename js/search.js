function addSearchGroup(num)
{
	var oDiv = document.getElementById('search_group_'+num);
	var nDiv = oDiv.cloneNode(true);
	nDiv.setAttribute('id','search_group_'+(num+1));
	
	/* update all of the child node ids to the new search group id */
	var i,id;
	for(i=0; i < nDiv.childNodes.length; i++)
	{
		id = nDiv.childNodes[i].id;
		if (id == 'search_field_'+num)
			nDiv.childNodes[i].setAttribute('id','search_field_'+(num+1));
		else if(id == 'search_mode_'+num)
			nDiv.childNodes[i].setAttribute('id','search_mode_'+(num+1));
		else if(id == 'search_value_'+num)
			nDiv.childNodes[i].setAttribute('id','search_value_'+(num+1));
		else if(id == 'search_join_'+num)
			nDiv.childNodes[i].setAttribute('id','search_join_'+(num+1));
		else if(id == 'search_toggle_'+num)
		{
			nDiv.childNodes[i].setAttribute('id','search_toggle_'+(num+1));
			nDiv.childNodes[i].setAttribute('onClick','addSearchGroup('+(num+1)+'); return false;');	
		}
	}
	
	/* update the old div if necessary */
	for(i=0; i < oDiv.childNodes.length; i++)
	{
		id = oDiv.childNodes[i].id;
		if(id == 'search_toggle_'+num)
		{
			oDiv.childNodes[i].value = '-';
			oDiv.childNodes[i].setAttribute('onClick','removeSearchGroup('+num+'); return false;');	
		}
		else if(id == 'search_join_'+num)
		{
			if (oDiv.childNodes[i].options[oDiv.childNodes[i].selectedIndex].value == '')
				oDiv.childNodes[i].selectedIndex = 1;
		}
	}
	
	/* append the new search group */
	oDiv.parentNode.appendChild(nDiv);
	return;
}

function removeSearchGroup(num)
{
	container = document.getElementById('searchTerms');
	group = document.getElementById('search_group_'+(num*1.0));
	container.removeChild(group);
	return;
}